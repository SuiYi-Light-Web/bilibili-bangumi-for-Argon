<?php

if (!defined('BILI_CACHE_TTL')) define('BILI_CACHE_TTL', 6 * 3600);

if (!defined('BILI_PAGE_SIZE')) define('BILI_PAGE_SIZE', 24);

if (!defined('BILI_MOVIE_PAGE_SIZE')) define('BILI_MOVIE_PAGE_SIZE', 15);

if (!defined('BILI_HTTP_CACHE_TTL')) define('BILI_HTTP_CACHE_TTL', 300);

if (!defined('BILI_FETCH_RETRY')) define('BILI_FETCH_RETRY', 3);

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

function bili_cache_get($key, $ttl, &$lock = null)
{
    $dir = __DIR__ . '/cache';
    $file = $dir . '/' . $key . '.json';

    if (is_file($file) && (time() - @filemtime($file)) <= $ttl) {
        $data = @json_decode(@file_get_contents($file), true);
        if (is_array($data)) {
            return $data;
        }
    }

    if (!is_dir($dir)) {
        @mkdir($dir, 0755, true);
    }

    $lock = @fopen($dir . '/' . $key . '.lock', 'c');
    if ($lock && @flock($lock, LOCK_EX | LOCK_NB)) {
        if (is_file($file) && (time() - @filemtime($file)) <= $ttl) {
            $data = @json_decode(@file_get_contents($file), true);
            @flock($lock, LOCK_UN);
            @fclose($lock);
            $lock = null;
            return is_array($data) ? $data : false;
        }
        return false;
    }

    if ($lock) {
        @fclose($lock);
        $lock = null;
    }

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

function bili_cache_get_stale($key)
{
    $file = __DIR__ . '/cache/' . $key . '.json';
    if (!is_file($file)) {
        return null;
    }
    $data = @json_decode(@file_get_contents($file), true);
    return is_array($data) ? $data : null;
}

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

function bili_cache_clear($key)
{
    @unlink(__DIR__ . '/cache/' . $key . '.json');
}

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

function bili_json_error($message, $status = 400)
{
    bili_json(array('error' => $message), $status);
}

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

function bili_play($num)
{
    if ($num == null || !is_numeric($num)) {
        return "暂无播放量";
    }
    if ($num >= 1000 && $num < 10000) {
        return round(($num / 1000), 2) . '千';
    }
    if ($num >= 10000 && $num < 100000000) {
        return round(($num / 10000), 2) . '万';
    }
    if ($num >= 100000000) {
        return round(($num / 100000000), 2) . '亿';
    }
    return $num;
}

function bili_url($id, $area)
{
    return "https://www.bilibili.com/bangumi/play/ss" . $id . "/";
}

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

function bili_title($title)
{
    return str_replace('（僅限台灣地區）', '', str_replace('（僅限港澳地區）', '', str_replace('（僅限港澳台地區）', '', $title)));
}
