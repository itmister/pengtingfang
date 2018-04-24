<?php

$title= '<script src="http://www.huabian.com/conn/js/show_zw_qiantao.js"></script><center>
<p><img src="http://img.mianfeicha.com/huabian/2016/1124/d2fc2129cc2ad79_size41_w640_h360.jpg"></p>
<center><script src="http://www.huabian.com/conn/js/show_zw_qiantao.js"></script></center>
</center><script src="http://www.huabian.com/conn/js/show_zw_qiantao.js"></script>';
echo time();
//$title =  str_replace("#<script.*</script>#is", '', $title);
$title =  preg_replace("/<script[\s\S]*?<\/script>/i", '', $title);

ECHO $title;DIE;

$sql ="select * from rt_7654_active_flow_by_qid where logtype='7654dh' AND subqid='None' ORDER BY dt DESC ; ";


$mysql_server_name='10.200.100.169:3306'; 
$mysql_username='7654read'; 
$mysql_password='7654^UYJH!';
$mysql_database='single_station'; 
$conn = mysql_connect($mysql_server_name,$mysql_username,$mysql_password) or die("error connecting") ; //连接数据库
mysql_query("set names 'utf8'");
mysql_select_db($mysql_database);
$result = mysql_query($sql,$conn);
$list = array();
while($row = mysql_fetch_array($result)){
    $list[]=$row;
}
mysql_close(); 
var_dump($list);


?>

