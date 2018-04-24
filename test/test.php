<?php

echo fopen("http://admin.wenhu.7654.com/index/api/getData/id/2" ,'rb');
die;


$dt = "2017-7-30";

echo date('w',strtotime($dt));die;

$url ='http://mini.eastday.com/apidata/top20jiankang.json';

$data = json_decode( file_get_contents($url),1);


// 过滤掉emoji表情
function filterEmoji($str)
{
    $str = preg_replace_callback(
        '/./u',
        function (array $match) {
            return strlen($match[0]) >= 4 ? '' : $match[0];
        },
        $str);

    return $str;
}


foreach ($data['data'] as &$v){
    $v = filterEmoji($v['title']);
}

echo "<pre>";
print_r($data);
die;



$a = -1;
$b = 888;
echo min($a,$b,6,9,6,5,4,1,2,3,6,7,8,9,9,9,9);
die;


$str = '222222222<div class="detail_page">
<a href="javascript:void(0)" style="cursor: default;color: #CCC;">上一页</a>
<a id="href_1" class="cur" href="./258645.html">1</a>
<a id="href_2" class="" href="./258645_2.html">2</a>
<a href="./258645_2.html">下一页</a>
</div>sdfsdfsdf';

echo preg_replace("/<div class=\"deta[\s\S]*?<\/div>/i", '', $str);

die;


$channels = array(
  array('id'=>1,'name'=>"衣服",'parId'=>0),
  array('id'=>2,'name'=>"书籍",'parId'=>0),
  array('id'=>3,'name'=>"T恤",'parId'=>1),
  array('id'=>4,'name'=>"裤子",'parId'=>1),
  array('id'=>5,'name'=>"鞋子",'parId'=>1),
  array('id'=>6,'name'=>"皮鞋",'parId'=>5),
  array('id'=>7,'name'=>"运动鞋",'parId'=>5),
  array('id'=>8,'name'=>"耐克",'parId'=>7),
  array('id'=>9,'name'=>"耐克",'parId'=>3),
  array('id'=>10,'name'=>"鸿星尔克",'parId'=>7),
  array('id'=>11,'name'=>"小说",'parId'=>2),
  array('id'=>12,'name'=>"科幻小说",'parId'=>11),
  array('id'=>13,'name'=>"古典名著",'parId'=>11),
  array('id'=>14,'name'=>"文学",'parId'=>2),
  array('id'=>15,'name'=>"四书五经",'parId'=>14)
);



$html = array();
/**
 * 递归查找父id为$parid的结点
 * @param array $html  按照父-》子的结构存放查找出来的结点
 * @param int $parid  指定的父id
 * @param array $channels  数据数组
 * @param int $dep  遍历的深度，初始化为1
 */
function getChild(&$html,$parid,$channels,$dep){
  /*
   * 遍历数据，查找parId为参数$parid指定的id
   */
  echo "<ul>";
  for($i = 0;$i<count($channels);$i++){
     
    if($channels[$i]['parId'] == $parid){
         
      $html[] = array('id'=>$channels[$i]['id'],'name'=>$channels[$i]['name'],'dep'=>$dep);
      getChild($html,$channels[$i]['id'],$channels,$dep+1);
    }
  }
  echo "</ul>";
}

getChild($html,0,$channels,1);


/*
 
 
 
 
 */


?>