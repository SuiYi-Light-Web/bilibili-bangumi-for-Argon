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

require_once("bilibiliAcconut.php");
require_once("classAnime.php");
$biliA = new bilibiliAnime($UID);
$total = $biliA->total;  // 追番总数
$total_page = intdiv($total, $limit);  // 分页总数
$pagenum = $page * $limit;  // 第一页为 page = 0


//完结状态
function finish($str1,$str2)
{
    if (is_numeric($str1) && $str1 == 1) 
    {
        return "已完结";
    }elseif (is_numeric($str2) && $str2 == 0 )
    {
        return "敬请期待";
    } elseif (is_numeric($str1) && $str1 == 0)
    {
        return "连载中";
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
    if($num == null || !is_numeric($num)){
        return " 暂无播放量 " ;
    }
    if($num >= 1000 && $num < 10000){
        return round(($num / 1000),2).'k';
    }
    if($num >= 10000 && $num < 100000000 ){
        return round(($num / 10000),2).'w';
    }
    if($num >= 100000000 ){ 
        return round(($num / 100000000),2).'E';
    }
    return $num;
}

function area ($num)
{
    if( $num == 1 ){
        return "中国大陆";
    } elseif ($num == 2 ){ 
        return "中国香港、中国澳门、中国台湾";
    } elseif ($num == 3 ){ 
        return "中国香港、中国澳门";
    } elseif ($num == 4 ){ 
        return "中国台湾";
    } else { 
        return "区域未知";
    };
}
function title ($title){
    return str_replace('（僅限台灣地區）','',str_replace('（僅限港澳地區）','',str_replace('（僅限港澳台地區）','', $title)));
}
// 构造请求接口
for ($i = 0; $i < $total; $i++) {
    // limit
    if ($i == $limit || $biliA->season_id[$pagenum] == NULL) {
        break;
    }
    $array[$i]['num'] = $i;
    $array[$i]['title'] = title($biliA->title[$pagenum]);
    $array[$i]['image_url'] = $biliA->image_url[$pagenum];
    $array[$i]['evaluate'] = $biliA->evaluate[$pagenum];
    $array[$i]['id'] = $biliA->season_id[$pagenum];
    $array[$i]['view'] = play($biliA->stat_view[$pagenum]);
    $array[$i]['rating_score'] = rating_score($biliA->rating_score[$pagenum]);
    $array[$i]['rating_count'] = rating_count($biliA->rating_count[$pagenum]);
    $array[$i]['finish'] = finish($biliA->finish[$pagenum],$biliA->started[$pagenum]);
    $array[$i]['follow_status'] = follow_status($biliA->follow_status[$pagenum]);
    $array[$i]['right_area'] = area($biliA-> area[$pagenum]);
    $array[$i]['type'] = $biliA->type[$pagenum];
    $array[$i]['index_show'] = $biliA->index_show[$pagenum];
    $pagenum++;
}
echo '{"total": ' . $total . ',"total_page": ' . $total_page . ', "limit": ' . $limit . ', "page": ' . $page . ', "data":' . json_encode($array, true) . '}';
