<?php
/**
 * 追番列表 API（返回 JSON）
 *
 * 相比原实现：
 *  1. 数据改为本地缓存复用：仅缓存过期时才请求 B 站，其他请求零出站、零全量解析。
 *  2. 从缓存数组直接切片返回当前页，不再每次遍历全量数据。
 *  3. limit / page 参数经过整数校验与收敛，避免非法输入。
 *  4. total_page 修正为 ceil(total/limit) - 1（最后一页的页码），避免多余请求。
 *  5. 输出 Content-Type / Cache-Control / ETag，浏览器可缓存，命中返回 304。
 *  6. 支持 ?refresh=1 强制刷新 B 站数据。
 */

require_once __DIR__ . '/common.php';
require_once __DIR__ . '/bilibiliAcconut.php';
require_once __DIR__ . '/classAnime.php';

$limit = bili_param('limit', BILI_PAGE_SIZE, 1, 100);
$page = bili_param('page', 0, 0, 100000);
$refresh = isset($_GET['refresh']) && $_GET['refresh'] == '1';

$data = bilibiliAnime::getData($UID, BILI_CACHE_TTL, $refresh);
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
        'title' => bili_title($item['title']),
        'image_url' => $item['image_url'],
        'evaluate' => $item['evaluate'],
        'id' => $item['season_id'],
        'view' => bili_play($item['stat_view']),
        'rating_score' => $item['rating_score'],
        'rating_count' => $item['rating_count'],
        'finish' => bili_finish_anime($item['finish'], $item['started']),
        'follow_status' => bili_follow_status($item['follow_status']),
        'url' => bili_url($item['season_id'], $item['area']),
        'right_area' => bili_area($item['area']),
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
