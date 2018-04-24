<?php 

$redis = new Redis();
$redis->connect('127.0.0.1', 6379);


/*设置用户*/
//echo "<pre>";
// $arr =['A'=>'B','V'=>'D'];
$user = array(
    'dsf'=>'阿斯顿发达',
    ''=>100,
    ''=>888,    
);

$redis->hMset('user:pengtingfang',$user);

//print_r( $redis->hGetAll('pengtingfang11'));

?>