<?php
/**
 * bilibili-api 公共函数库
 *
 * 职责：
 *  1. 参数校验（bili_param）
 *  2. HTTP 抓取（带超时 / UA / 重试，bili_http_get）
 *  3. 本地 JSON 文件缓存（带锁防并发风暴 / 原子写入 / 过期兜底，bili_cache_*）
 *  4. JSON 统一输出（Content-Type / Cache-Control / ETag 304，bili_json）
 *  5. 字段格式化函数集中管理（finish / follow_status / play / url / area / title）
 *
 * 相比原实现：GetAnimeData.php 与 GetMovieData.php 不再重复定义格式化函数，
 * 不再每次请求都去 B 站拉全量数据，仅缓存过期时才抓取一次。
 */

error_reporting(0);

/** 缓存有效期（秒）：B 站追番/追剧列表变化不频繁，默认 6 小时刷新一次 */
if (!defined('BILI_CACHE_TTL')) define('BILI_CACHE_TTL', 6 * 3600);

/** 追番接口单页拉取条数（与原实现一致，避免超出 B 站接口限制） */
if (!defined('BILI_PAGE_SIZE')) define('BILI_PAGE_SIZE', 24);

/** 追剧接口单页拉取条数（与原实现一致） */
if (!defined('BILI_MOVIE_PAGE_SIZE')) define('BILI_MOVIE_PAGE_SIZE', 15);

/** 输出到浏览器的缓存时长（秒）：数据在缓存有效期内基本不变，允许浏览器/代理缓存 5 分钟 */
if (!defined('BILI_HTTP_CACHE_TTL')) define('BILI_HTTP_CACHE_TTL', 300);

/** 抓取 B 站接口失败时的重试次数 */
if (!defined('BILI_FETCH_RETRY')) define('BILI_FETCH_RETRY', 3);

/**
 * 获取并校验 GET 参数为整数，超出范围时收敛到边界。
 * 相比原实现：不再直接使用 $_GET 原始值，避免非法输入导致异常。
 */
function bili_param($name, $default, $min = null, $max = null)
{
    if (!isset($_GET[$name]) || $_GET[$name] === '') {
        return $default;
    }
    $v = (int)$_GET[$name];
    if ($min !== null && $v < $min) $v = $min;
    if ($max !== null && $v > $max) $v = $max;
    return $v;
}

/**
 * HTTP GET 抓取：带超时、UA、重试。
 * 相比原实现的裸 file_get_contents：避免超时卡死，降低临时失败率。
 */
function bili_http_get($url, $timeout = 15)
{
    $context = stream_context_create(array(
        'http' => array(
            'method' => 'GET',
            'timeout' => $timeout,
            'header' =>
                "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/124.0.0.0 Safari/537.36\r\n" .
                "Accept: application/json, text/plain, */*\r\n" .
                "Referer: https://www.bilibili.com/\r\n" .
                "Accept-Language: zh-CN,zh;q=0.9\r\n"
        )
    ));
    $retry = BILI_FETCH_RETRY;
    while ($retry-- > 0) {
        $result = @file_get_contents($url, false, $context);
        if ($result !== false) {
            return $result;
        }
        usleep(200000);
    }
    return false;
}

/**
 * 读取本地 JSON 缓存。
 *
 * 返回值约定：
 *   array  - 命中有效缓存（含等待其他进程更新后命中）
 *   false  - 缓存缺失 / 过期，且已成功取得更新锁（$lock 为文件句柄，调用方负责抓取、写入、释放）
 *   null   - 未取得锁（其他进程正在更新），等待超时后仍未就绪
 */
function bili_cache_get($key, $ttl, &$lock = null)
{
    $dir = __DIR__ . '/cache';
    $file = $dir . '/' . $key . '.json';

    // 命中有效缓存
    if (is_file($file) && (time() - @filemtime($file)) <= $ttl) {
        $data = @json_decode(@file_get_contents($file), true);
        if (is_array($data)) {
            return $data;
        }
    }

    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    // 尝试获取更新锁（非阻塞），防止缓存过期瞬间多请求并发抓取 B 站
    $lock = @fopen($dir . '/' . $key . '.lock', 'c');
    if ($lock && @flock($lock, LOCK_EX | LOCK_NB)) {
        // 二次检查：等待期间可能已被其他进程更新
        if (is_file($file) && (time() - @filemtime($file)) <= $ttl) {
            $data = @json_decode(@file_get_contents($file), true);
            @flock($lock, LOCK_UN);
            @fclose($lock);
            $lock = null;
            return is_array($data) ? $data : false;
        }
        return false; // 确实需要更新，由调用方抓取并写入
    }

    if ($lock) {
        @fclose($lock);
        $lock = null;
    }

    // 未拿到锁：等待更新进程完成（最多约 10 秒）
    for ($i = 0; $i < 50; $i++) {
        usleep(200000);
        if (is_file($file) && (time() - @filemtime($file)) <= $ttl) {
            $data = @json_decode(@file_get_contents($file), true);
            if (is_array($data)) {
                return $data;
            }
        }
    }
    return null;
}

/**
 * 读取已过期缓存（抓取失败时的兜底，保证服务可用）。
 */
function bili_cache_get_stale($key)
{
    $file = __DIR__ . '/cache/' . $key . '.json';
    if (!is_file($file)) {
        return null;
    }
    $data = @json_decode(@file_get_contents($file), true);
    return is_array($data) ? $data : null;
}

/**
 * 写入本地 JSON 缓存（临时文件 + rename 原子替换，避免写一半导致损坏）。
 */
function bili_cache_set($key, $data)
{
    $dir = __DIR__ . '/cache';
    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }
    $file = $dir . '/' . $key . '.json';
    $tmp = $file . '.' . getmypid() . '.tmp';
    $json = json_encode($data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    if ($json !== false && @file_put_contents($tmp, $json, LOCK_EX) !== false) {
        @rename($tmp, $file);
        @chmod($file, 0644);
        return true;
    }
    @unlink($tmp);
    return false;
}

/**
 * 清空指定缓存（用于强制刷新，配合 ?refresh=1）。
 */
function bili_cache_clear($key)
{
    @unlink(__DIR__ . '/cache/' . $key . '.json');
}

/**
 * 输出 JSON 并结束请求。
 * 相比原实现：补充正确 Content-Type，并支持 ETag / If-None-Match（命中返回 304），
 * 大幅减少重复请求的流量与服务器处理量。
 */
function bili_json($payload, $status = 200)
{
    $body = json_encode($payload, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    $etag = '"' . md5($body) . '"';

    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    header('Cache-Control: public, max-age=' . BILI_HTTP_CACHE_TTL);
    header('ETag: ' . $etag);

    if (isset($_SERVER['HTTP_IF_NONE_MATCH']) && trim($_SERVER['HTTP_IF_NONE_MATCH']) === $etag) {
        http_response_code(304);
        exit;
    }
    http_response_code($status);
    echo $body;
    exit;
}

/**
 * 输出错误 JSON。
 */
function bili_json_error($message, $status = 400)
{
    bili_json(array('error' => $message), $status);
}

/* ==================== 字段格式化函数（原分散在两个 Get 文件，现集中管理） ==================== */

/** 追番完结状态 */
function bili_finish_anime($str1, $str2)
{
    if (is_numeric($str1) && $str1 == 1) {
        return "已完结";
    } elseif (is_numeric($str2) && $str2 == 0) {
        return "敬请期待";
    } elseif (is_numeric($str1) && $str1 == 0) {
        return "连载中";
    } else {
        return "状态未知";
    }
}

/** 追剧完结状态（与原 GetMovieData 逻辑保持一致） */
function bili_finish_movie($str1, $str2)
{
    if (is_numeric($str1) && $str1 == 1 && $str2 == 1) {
        return "已完结";
    } elseif (is_numeric($str2) && $str2 == 0) {
        return "敬请期待";
    } elseif (is_numeric($str1) && $str1 == 0) {
        return "更新中";
    } else {
        return "状态未知";
    }
}

/** 追番/追剧状态 */
function bili_follow_status($str1)
{
    if (is_numeric($str1) && $str1 == 1) {
        return "想看";
    } elseif (is_numeric($str1) && $str1 == 2) {
        return "在看";
    } elseif (is_numeric($str1) && $str1 == 3) {
        return "看过";
    } else {
        return "状态未知";
    }
}

/** 播放量格式化为 k / w / E（兼容原实现，并对空值更健壮） */
function bili_play($num)
{
    if ($num == null || !is_numeric($num)) {
        return "暂无播放量";
    }
    if ($num >= 1000 && $num < 10000) {
        return round(($num / 1000), 2) . 'k';
    }
    if ($num >= 10000 && $num < 100000000) {
        return round(($num / 10000), 2) . 'w';
    }
    if ($num >= 100000000) {
        return round(($num / 100000000), 2) . 'E';
    }
    return $num;
}

/** 番剧播放链接（GitHub 版：所有区域统一跳转 B 站播放页，不提供区域跳转提示） */
function bili_url($id, $area)
{
    return "https://www.bilibili.com/bangumi/play/ss" . $id . "/";
}

/** 版权区域名称 */
function bili_area($num)
{
    if ($num == 1) {
        return "中国大陆";
    } elseif ($num == 2) {
        return "中国香港、中国澳门、中国台湾";
    } elseif ($num == 3) {
        return "中国香港、中国澳门";
    } elseif ($num == 4) {
        return "中国台湾";
    } else {
        return "区域未知";
    }
}

/** 清理标题中的区域后缀 */
function bili_title($title)
{
    return str_replace('（僅限台灣地區）', '', str_replace('（僅限港澳地區）', '', str_replace('（僅限港澳台地區）', '', $title)));
}
