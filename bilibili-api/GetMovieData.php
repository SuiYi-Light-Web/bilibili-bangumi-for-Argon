<?php

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/bilibiliAcconut.php';
require_once __DIR__ . '/classMovie.php';

$limit = bili_param('limit', 12, 1, 100);
$page = bili_param('page', 0, 0, 100000);
$refresh = isset($_GET['refresh']) && $_GET['refresh'] == '1';

$data = bilibiliMovie::getData($UID, BILI_CACHE_TTL, $refresh);
if ($data === null) {
    bili_json_error('数据获取失败，请稍后重试', 500);
}

$total = (int)$data['total'];
$list = $data['list'];
$total_page = $total > 0 ? (int)ceil($total / $limit) - 1 : 0;
$offset = $page * $limit;

$items = array();
foreach (array_slice($list, $offset, $limit) as $i => $item) {
    $items[] = array(
        'num' => $i,
        'title' => $item['title'],
        'image_url' => $item['image_url'],
        'evaluate' => $item['evaluate'],
        'id' => $item['season_id'],
        'view' => bili_play($item['stat_view']),
        'rating_score' => $item['rating_score'],
        'rating_count' => $item['rating_count'],
        'finish' => bili_finish_movie($item['finish'], $item['started']),
        'follow_status' => bili_follow_status($item['follow_status']),
        'type' => $item['type'],
        'index_show' => $item['index_show'],
    );
}

bili_json(array(
    'total' => $total,
    'total_page' => $total_page,
    'limit' => $limit,
    'page' => $page,
    'data' => $items,
));
