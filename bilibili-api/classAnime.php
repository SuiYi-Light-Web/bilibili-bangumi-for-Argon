<?php

require_once __DIR__ . '/common.php';

class bilibiliAnime
{

    public static function getData($uid, $ttl = BILI_CACHE_TTL, $refresh = false)
    {
        $key = 'anime_' . $uid;

        if ($refresh) {
            bili_cache_clear($key);
        }

        $lock = null;
        $cached = bili_cache_get($key, $ttl, $lock);
        if ($cached !== false && $cached !== null) {
            return $cached;
        }

        $data = null;
        if ($cached === false) {
            $data = self::fetchAll($uid);
            if ($data !== null) {
                bili_cache_set($key, $data);
            }
            if ($lock) {
                @flock($lock, LOCK_UN);
                @fclose($lock);
            }
        } else {
        }

        if ($data !== null) {
            return $data;
        }

        $stale = bili_cache_get_stale($key);
        return $stale !== null ? $stale : null;
    }

    public static function fetchAll($uid)
    {
        $list = array();
        $ps = BILI_PAGE_SIZE;

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
                    break;
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

    private static function fetchPage($uid, $pn, $ps)
    {
        $url = "https://api.bilibili.com/x/space/bangumi/follow/list?type=1&follow_status=0&pn=$pn&ps=$ps&vmid=$uid&playform=web";
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

    private static function normalize($data)
    {
        return array(
            'title' => isset($data['title']) ? $data['title'] : '',
            'image_url' => str_replace('http://', '//', isset($data['cover']) ? $data['cover'] : ''),
            'evaluate' => isset($data['summary']) ? $data['summary'] : '',
            'type' => isset($data['season_type_name']) ? $data['season_type_name'] : '',
            'season_id' => isset($data['season_id']) ? $data['season_id'] : '',
            'finish' => isset($data['is_finish']) ? $data['is_finish'] : null,
            'started' => isset($data['is_started']) ? $data['is_started'] : null,
            'index_show' => isset($data['new_ep']['index_show']) ? $data['new_ep']['index_show'] : '',
            'follow_status' => isset($data['follow_status']) ? $data['follow_status'] : null,
            'rating_score' => isset($data['rating']['score']) ? $data['rating']['score'] : null,
            'rating_count' => isset($data['rating']['count']) ? $data['rating']['count'] : null,
            'stat_view' => isset($data['stat']['view']) ? $data['stat']['view'] : null,
            'area' => self::area($data['title']),
        );
    }

    private static function area($title)
    {
        if (stripos($title, "僅限港澳台") !== false) {
            return 2;
        } elseif (stripos($title, "僅限港澳") !== false) {
            return 3;
        } elseif (stripos($title, "僅限台灣") !== false) {
            return 4;
        } else {
            return 1;
        }
    }
}
