<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="zh-CN">
<head>
    <meta charset="utf-8" />
    <META name="filetype" content="1">
    <META name="publishedtype" content="1">
    <META name="pagetype" content="2">
    <META name="catalogs" content="toutiao_PC">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Cache-Control" content="no-cache">
    <meta http-equiv="Expires" content="0">
	<meta name="viewport" content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <title>新闻站</title>
	<link style="text/css" rel="stylesheet" href="<?=$resources_url?>/public/css/index.css" />
    <script type="text/javascript" src="//dup.baidustatic.com/js/ds.js"></script>
    <script type="text/javascript" src="<?=$resources_url?>/public/js/jquery.js"></script>
	<script type="text/javascript" src="//afpmm.alicdn.com/g/mm/afp-cdn/JS/k.js"></script>
</head>
<body onselectstart="return false" oncontextmenu="return false" data-time="<?php echo date('Y-m-d H:i:s');?>">
<script type="text/javascript">BAIDU_CLB_fillSlot("2973323");</script>
<!-- 广告位：mini右侧广告位新 -->
<?php 
    if($right_ad_code){
        foreach ($right_ad_code as $_key => $_ad_code){
            $index = ($_key + 1);
            echo '<div class="w200 g'.$index.'">';
            echo '<div class="us">';
            echo base64_decode($_ad_code);
            echo '</div>';
            echo '</div>';
        }
    }
    
    if($preview == true){
        include_once $template_path.'index-in.php';
    }else{
        echo '<iframe src="index-in.html" frameborder="0" width="757" height="550" scrolling="no"></iframe>';
    }
?>
<script type="text/javascript">
    function showgg(num){
        $('.w200').hide();
        $('.g'+num).show();
    }
    function hidegg(){
        $('.w200').hide();
    }

</script>
<?php 
    //统计代码
    if($tj_code){
        echo base64_decode($tj_code['ad_code']);
    }
?>
<script type="text/javascript" src="//tajs.qq.com/stats?sId=56309991" charset="UTF-8"></script>
</body>
</html>
