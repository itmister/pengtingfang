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
    <title>新闻站</title>
    <link style="text/css" rel="stylesheet" href="<?=$resources_url?>/public/css/main.css" />
    <script type="text/javascript">
        var global_qid = '<?=$qid;?>';
    </script>
	<script type="text/javascript" src="<?=$resources_url?>/public/js/jquery.js"></script>
    <script type="text/javascript" src="<?=$resources_url?>/public/js/jquery.cookie.js"></script>
    <script type="text/javascript" src="<?=$resources_url?>/public/js/miniglobal.js"></script>
	<script type="text/javascript" src="<?=$resources_url?>/public/js/jquery.mousewheel.js"></script>
	<script type="text/javascript" src="//afpmm.alicdn.com/g/mm/afp-cdn/JS/k.js"></script>
</head>
<body onselectstart="return false" oncontextmenu="return false">

<div id="nav">
    <div class="nav_ul">
        <?php 
            if(!empty($category_list)){
                foreach ($category_list as $_key => $_category){
                       /* $url = str_replace(['http:','https:'], '', $_category['url']);
                       if(in_array($_category['ename'],['lieqi','bagua'])){
                           $url = $_category['url'];
                       } */
                       $url = $_category['url'];
                       $pdata = $url."|1|nav|".$_category['id']."|text|left";
                       
                       $class = 'nav_li';
                       if($_key == 0){
                            $class = 'nav_li now';
                       }
                       //广告、外链栏目不加载右侧广告
                       if(in_array($_category['type'],[2,3])){
                           $class = 'nav_li noad';
                       }
        ?>
        <div class="<?=$class?>">
            <?php if($_category['type'] != 3){?>
            <a href="<?=$url?>?qid=<?=$qid;?>" pdata="<?=$pdata?>" target="_blank" onmouseover="_hmt.push(['_trackEvent', 'navigator', 'hover', '<?=$_category['cname']?>']);"><?=$_category['cname']?></a>
            <?php }else{
                echo $_category['cname'];
            }?>
        </div>
        <?php 
                }
            }
        ?>  
    </div>
    <?php 
        if(count($category_list) > 11){
    ?>
    <div class="more_btn st1">更多&nbsp;&nbsp;<span></span></div>
    <?php }?>
</div>
<div class="over_now"></div>
<div id="frame">
    <div class="frame_ul">
        <?php
            
            if(!empty($category_list)){
                foreach ($category_list as $category){
                    $left_news  = $category['left_news'];   //左边图片新闻
                    $right_news = $category['right_news'];  //右边文字 
                    $more_news  = $category['more_news'];   //更多新闻
                    $pic_news   = $category['pic_news'];    //图片新闻
                    
                    //广告
                    $left_down_ad_code = $category['left_down_ad_code'];
                    $page_ad_code      = $category['page_ad_code'];
                    
                    if($category['type'] != 3){
                        include $template_path.$category['template'];
                    }else{
                        //外链页面
                        if($page_ad_code){
                            echo '<div class="iframe" style="width:757px;height: 519px; padding: 0 0 0 0;">';
                            foreach ($page_ad_code as $ad_code){
                                echo base64_decode($ad_code);
                            }
                            echo '</div>';
                        }
                    }
                }
            }
        ?>
        
    </div>
</div>
<script type="text/javascript">
var right_ad_pos = <?php 
                        if($right_ad_pos){
                            echo json_encode($right_ad_pos);
                        }else{
                            echo 0;
                        }
                    ?>;
</script>
<script>
var _hmt = _hmt || [];
(function() {
   var hm = document.createElement("script");
     hm.src = "https://hm.baidu.com/hm.js?1151ad7baafa9a5763b5df4aad4ec541";
	   var s = document.getElementsByTagName("script")[0]; 
	     s.parentNode.insertBefore(hm, s);
		 })();
</script>
<script type="text/javascript" src="<?=$resources_url?>/public/js/main.js"></script>
</body>
</html>
