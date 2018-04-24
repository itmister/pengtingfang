<?php
$time =  time();

$begin_id = 1707;
$num = 2;
$news_user_id = [];

$yiji =  "T8474797" ; //一级邀请码
//一级用户
for ($i = $begin_id  ; $i < $begin_id+$num  ; $i++ ){
    echo "INSERT INTO `new_head`.`user` (`id`, `account`, `password`, `imei`, `name`, `sign`, `credit`, `total_credit`, `total_money`, `withdraw_money`, `no_withdraw_money`, `code`, `invitecode`, `phone`, `channel`, `source`, `reg_dateline`, `reg_ip`, `last_login_time`, `channel_code`, `bind_dateline`, `bind_active_dateline`, `bind_status`, `status`, `head_img`) VALUES ('$i', '".(18000000000+$i)."', '', 'B343617F-8FAF-4FF2-8868-C6ADA67B96DD', 'news_$i', '', '0', '0', '0', '0.00', '0', 'F$i', '', '".(18000000000+$i)."', 'APPStore', 'app', '$time', '', '1515747196', '$yiji', '$time', '$time', '1', '1', 'http://zmimg.guangsuss.com/newhead/20171218/a05c7a200abcbeed475503e55c8d1251.png');"."<br>";
}

$erji = "F319";
//二级用户
for ($i = $begin_id  ; $i < $begin_id+$num  ; $i++ ){
    //  echo "INSERT INTO `new_head`.`user` (`id`, `account`, `password`, `imei`, `name`, `sign`, `credit`, `total_credit`, `total_money`, `withdraw_money`, `no_withdraw_money`, `code`, `invitecode`, `phone`, `channel`, `source`, `reg_dateline`, `reg_ip`, `last_login_time`, `channel_code`, `bind_dateline`, `bind_active_dateline`, `bind_status`, `status`, `head_img`) VALUES ('$i', '".(18000000000+$i)."', '', 'B343617F-8FAF-4FF2-8868-C6ADA67B96DD', 'news_$i', '', '0', '0', '0', '0.00', '0', 'F$i', '$erji', '".(18000000000+$i)."', 'APPStore', 'app', '$time', '', '1515747196', '', '$time', '$time', '1', '1', 'http://zmimg.guangsuss.com/newhead/20171218/a05c7a200abcbeed475503e55c8d1251.png');"."<br>";
}


//阅读行为
for ($i = $begin_id  ; $i <  $begin_id+$num  ; $i++ ){
    echo "INSERT INTO `new_head`.`credit` (`uid`, `credit`, `name`, `is_get`, `dateline`, `ymd`, `update_dateline`) VALUES ($i, '10', 'day_read', '1', $time, '".date("Ymd",$time)."', '$time');
"."<br>";
}

