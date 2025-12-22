<?php
$array = array();
$limit = $_GET["limit"];
$page = $_GET["page"];
error_reporting(0);
// 判断获取的参数
if (empty($limit)) {
    die('limit 为必须参数');
} elseif (empty($page)) {
    $page = 0;
}

//完结状态
function finish($str1, $str2)
{
    if (is_numeric($str1) && $str1 == 1 && $str2 == 1){
        return "已完结";
    } elseif (is_numeric($str2) && $str2 == 0 ){
        return "敬请期待";
    } elseif (is_numeric($str1) && $str1 == 0){
        return "更新中";
    } else {
        return "状态未知";
    }
}

//追番状态
function follow_status($str1)
{
    if (is_numeric($str1) && $str1 == 1) {
        return "想看";
    } elseif (is_numeric($str1) && $str1 == 2)
 {
        return "在看";
    } elseif (is_numeric($str1) && $str1 == 3)
 {
        return "看过";
    } else {
        return "状态未知";
    }
}
//暂无评分
function rating_score($score)
{
    if ($score == null){
        return "暂无评分";
    } else {
        return $score;
    }
}
function rating_count($count)
{
    if ($count == null){
        return " 暂无 ";
    } else {
        return $count;
    }
}
//播放量k，w
function play($num)
{
    if($num >= 1000 && $num < 10000){
        return round(($num / 1000),2).'k';
    }
    if($num >= 10000 ){
        return round(($num / 10000),2).'w';
    }
    return $num;
}

require_once("bilibiliAcconut.php");
require_once("classMovie.php");
$biliM = new bilibiliMovie($UID);
$total = $biliM->total;  // 追剧总数
$total_page = intdiv($total, $limit);  // 分页总数
$pagenum = $page * $limit;  // 第一页为 page = 0

// 构造请求接口
for ($i = 0; $i < $total; $i++) {
    // limit
    if ($i == $limit || $biliM->season_id[$pagenum] == NULL) {
        break;
    }
    $array[$i]['num'] = $i;
    $array[$i]['title'] = $biliM->title[$pagenum];
    $array[$i]['image_url'] = $biliM->image_url[$pagenum];
    $array[$i]['evaluate'] = $biliM->evaluate[$pagenum];
    $array[$i]['id'] = $biliM->season_id[$pagenum];
    $array[$i]['view'] = $biliM->stat_view[$pagenum];
    $array[$i]['rating_score'] = rating_score($biliM->rating_score[$pagenum]);
    $array[$i]['rating_count'] = rating_count($biliM->rating_count[$pagenum]);
    $array[$i]['finish'] = finish($biliM->finish[$pagenum], $biliM->started[$pagenum]);
    $array[$i]['follow_status'] = follow_status($biliM->follow_status[$pagenum]);
    $array[$i]['type'] = $biliM->type[$pagenum];
    $array[$i]['index_show'] = $biliM->index_show[$pagenum];
    $pagenum++;
}
echo '{"total": ' . $total . ',"total_page": ' . $total_page . ', "limit": ' . $limit . ', "page": ' . $page . ', "data":' . json_encode($array, true) . '}';
