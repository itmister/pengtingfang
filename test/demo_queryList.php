<?php
/***
 * 
 * 文本选择器，和jquery语法意义
 * 
 */
require 'plug_in/queryList/autoload.php';
use QL\QueryList;
header( 'Content-Type:text/html;charset=utf-8 ');
//采集某页面所有的图片
//$data = QueryList::Query('http://tv.youku.com/?spm=0.0.topNav.5~1~3!2~A.qFkI79',array(

// //使用插件
// $urls = QueryList::run('Request',array(
//     'target' => 'http://cms.querylist.cc/news/list_2.html',
//     'referrer'=>'http://cms.querylist.cc',
//     'method' => 'GET',
//     'params' => ['var1' => 'testvalue', 'var2' => 'somevalue'],
//     'user_agent'=>'Mozilla/5.0 (Macintosh; Intel Mac OS X 10.8; rv:21.0) Gecko/20100101 Firefox/21.0',
//     'cookiePath' => './cookie.txt',
//     'timeout' =>'30'
// ))->setQuery(array('link' => array('h2>a','href','',function($content){
//     //利用回调函数补全相对链接
//     $baseUrl = 'http://cms.querylist.cc';
//     return $baseUrl.$content;
// })),'.cate_list li')->getData(function($item){
//     return $item['link'];
// });

//     print_r($urls);

/******* 优酷首页视频  **********/

$html = file_get_contents('http://www.youku.com/');

//全部ID
$arrid = QueryList::Query($html,array(
    'id' => array('.yk-content>div[name=m_pos]','id'),

))->data;
$dataall ='';
foreach ($arrid as $k=>$v){
    //   'name' => array('.yk-content>div[name=m_pos].mod-new h3:first a,.yk-content .mod-new h2> a','text'),
    $title = QueryList::Query($html,array(
        'title' => array("#{$v['id']} .mod-new .h img","title"),
    ))->data;
    //数据
    $data = QueryList::Query($html,array(
        'name' => array("#{$v['id']} .p-thumb>a","title"),
        'href' => array("#{$v['id']} .p-thumb>a","href"),
        'img'=> array("#{$v['id']} .p-thumb>img","alt"),
    ))->data;
    if(empty($data)){
        continue;
    }
 
    @$dataall[$k]['title'] =$title[0]['title'];
    $dataall[$k]['data'] =$data;
}

//打印结果
echo "<pre>";
print_r($dataall);
die;
/*************************************************/

?>