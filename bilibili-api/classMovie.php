<?php
/**
 * 追剧数据类
 *
 * 相比原实现：
 *  1. 原构造函数每次请求都遍历 B 站全部分页拉取数据；现改为 getData() 静态方法，
 *     仅当本地缓存过期时才抓取一次，其余请求直接命中缓存。
 *  2. 抓取失败时回退旧缓存，保证服务可用；并发请求由锁机制保护，避免缓存风暴。
 */
require_once __DIR__ . '/common.php';

class bilibiliMovie
{
    /**
     * 获取追剧数据（带缓存）。
     *
     * @param string $uid     B站 UID
     * @param int    $ttl     缓存有效期（秒）
     * @param bool   $refresh 是否强制刷新（跳过缓存直接抓取）
     * @return array|null  array('total' => int, 'list' => array)，失败且无兜底缓存时返回 null
     */
    public static function getData($uid, $ttl = BILI_CACHE_TTL, $refresh = false)
    {
        $key = 'movie_' . $uid;

        if ($refresh) {
            bili_cache_clear($key);
        }

        $lock = null;
        $cached = bili_cache_get($key, $ttl, $lock);
        if ($cached !== false && $cached !== null) {
            return $cached; // 命中缓存（含等待其他进程更新后命中）
        }

        $data = null;
        if ($cached === false) {
            // 持有更新锁：负责抓取并写入缓存
            $data = self::fetchAll($uid);
            if ($data !== null) {
                bili_cache_set($key, $data);
            }
            if ($lock) {
                @flock($lock, LOCK_UN);
                @fclose($lock);
            }
        } else {
            // 未取得锁（其他进程正在更新）：直接回退旧缓存
        }

        if ($data !== null) {
            return $data;
        }

        // 抓取失败：回退旧缓存，尽量保证接口可用
        $stale = bili_cache_get_stale($key);
        return $stale !== null ? $stale : null;
    }

    /**
     * 分页抓取全部追剧（type=2）。
     * 第一页同时读取总数，之后仅拉取剩余页，避免重复请求第一页。
     *
     * @return array|null array('total' => int, 'list' => array)，失败返回 null
     */
    public static function fetchAll($uid)
    {
        $list = array();
        $ps = BILI_MOVIE_PAGE_SIZE;

        $info = self::fetchPage($uid, 1, $ps);
        if ($info === null) {
            return null;
        }
        $total = isset($info['total']) ? (int)$info['total'] : 0;
        if ($total <= 0) {
            return array('total' => 0, 'list' => array());
        }

        $pages = (int)ceil($total / $ps);
        for ($pn = 1; $pn <= $pages; $pn++) {
            if ($pn > 1) {
                $info = self::fetchPage($uid, $pn, $ps);
                if ($info === null) {
                    break; // 后续页失败则使用已拉取部分
                }
            }
            if (!isset($info['list']) || !is_array($info['list'])) {
                break;
            }
            foreach ($info['list'] as $data) {
                $list[] = self::normalize($data);
            }
            if (count($list) >= $total) {
                break;
            }
        }

        return array('total' => $total, 'list' => $list);
    }

    /** 抓取单页追剧列表 */
    private static function fetchPage($uid, $pn, $ps)
    {
        $url = "https://api.bilibili.com/x/space/bangumi/follow/list?type=2&follow_status=0&pn=$pn&ps=$ps&vmid=$uid";
        $body = bili_http_get($url);
        if ($body === false) {
            return null;
        }
        $json = json_decode($body, true);
        if (!is_array($json) || !isset($json['code']) || $json['code'] !== 0 || !isset($json['data'])) {
            return null;
        }
        return $json['data'];
    }

    /** 规整单条数据（与原实现的字段映射保持一致） */
    private static function normalize($data)
    {
        return array(
            'title' => isset($data['title']) ? $data['title'] : '',
            'image_url' => str_replace('http://', '//', isset($data['cover']) ? $data['cover'] : ''), // 协议跟随
            'evaluate' => isset($data['summary']) ? $data['summary'] : '',
            'season_id' => isset($data['season_id']) ? $data['season_id'] : '',
            'type' => isset($data['season_type_name']) ? $data['season_type_name'] : '',
            'finish' => isset($data['is_finish']) ? $data['is_finish'] : null,
            'started' => isset($data['is_started']) ? $data['is_started'] : null,
            'index_show' => isset($data['new_ep']['index_show']) ? $data['new_ep']['index_show'] : '',
            'follow_status' => isset($data['follow_status']) ? $data['follow_status'] : null,
            'rating_score' => isset($data['rating']['score']) ? $data['rating']['score'] : null,
            'rating_count' => isset($data['rating']['count']) ? $data['rating']['count'] : null,
            'stat_view' => isset($data['stat']['view']) ? $data['stat']['view'] : null,
        );
    }
}
