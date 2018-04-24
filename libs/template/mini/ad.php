<?php 
    //精选
    if($page_ad_code||$category['ename']=="newad"){
        if($category['ename'] == 'jingxuan'){
            $class_list = [
                'select_l','select_m',
                'select_r','select_tui','select_b'
            ];
            echo '<div class="iframe iframe_select">';
            foreach ($page_ad_code as $key => $code){
                $class = $class_list[$key];
                echo '<div class="'.$class.' clearfix">';
                echo base64_decode($code);
                echo '</div>';
            }
            echo "</div>";
        }
        //推荐
        else if($category['ename'] == 'tuijian'){
            echo '<div class="iframe iframe_recom">';
            foreach ($page_ad_code as $key => $code){
                echo base64_decode($code);
            }
            echo '</div>';
        }
        //特价
        else if($category['ename'] == 'tejia'){
            echo '<div class="iframe iframe_1111">';
            foreach ($page_ad_code as $key => $code){
                echo base64_decode($code);
            }
            echo '</div>';
        }
		//新增
        else if($category['ename'] == 'newad'){
            echo '<div class="iframe iframe_1111">';
		?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <title>Document</title>
	<?php 
	if($category["id"]==35){
	?>
    <!--<link href="http://dev.mini.resources.com/mini/public/css/base.css" type="text/css" rel=stylesheet />-->
    <link href="http://dev.mini.resources.com/mini/public/css/default.css" type="text/css" rel="stylesheet" />
	<link style="text/css" rel="stylesheet" href="http://dev.mini.resources.com/mini/public/css/toutiao.css" />
	<?php
	}
	?>
</head>
<body>
<div style="width:757px;height: auto;overflow: hidden;position: relative;">
<div id="dowebok">
    <div class="tt-cont">
  <div class="gl_mini">
  <!-- 第一屏 -->
      <div class="gl_section">
          <div class="gl_sec_fl">
		  <?php
		  	$left_news = $category['news_list'];
			if($category['id']==35 && $left_news==""){
		  		$left_news = $category['left_news'];
			}
			$news_list = $left_news[0];
            $url = $news_list['url'].'?qid='.$qid.'&_1_'.$category['cname'];
		 ?>
             <div class="gl_sec_top"><a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $news_list["pic"];?>" class="gl_sec_img" />
                <span class="gl_top_bj"><?php echo $news_list["title"];?></span>             
             </a></div>
             <ul class="gl_sec_ls">
			 <?php 
			 $left_3 = array_slice($left_news,1,3);
			 foreach($left_3 as $key=>$value){
				 $key=$key+1;
                 $url = $value['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];
			 ?>
               <li>
                 <a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $value["pic"];?>" target="_blank" class="gl_sec_ls_img" /></a>
                 <div class="gl_sec_div">
                    <a href="<?php echo $url;?>" target="_blank" class="gl_link"><?php echo $value["title"];?></a>
                 </div>
               </li>
			   <?php 
			 	}
			   ?>
             </ul>
          </div>
           <div class="gl_sec_fr">
               <ul class="gl_sec_ls">
			   <?php 
			   $left_3 = array_slice($left_news,4,5);
			   foreach($left_3 as $key=>$value){
				   $key=$key+4;
                 $url = $value['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];
			   ?>
               <li>
                 <a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $value["pic"];?>" target="_blank" class="gl_sec_ls_img" /></a>
                 <div class="gl_sec_div">
                    <a href="<?php echo $url;?>" target="_blank" class="gl_link"><?php echo $value["title"];?></a>
                 </div>
               </li>
			 <?php 
			  }
			 ?>
             </ul>
           </div>
           <div class="mini-more">
             <div>点击查看更多资讯<span class="mini6-icon1"></span></div>
           </div>
      </div>
      <!-- 第一屏/ -->
      <!-- 第二屏 -->
       <div class="gl_section" >
          <div class="gl_sec_fl" style="margin-top:13px">
             <ul class="gl_sec_ls">
			     <?php 
			     $left_3 = array_slice($left_news,9,5);
			     foreach($left_3 as $key=>$value){
				 $key=$key+9;
                 $url = $value['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];
			     ?>
               	 <li>
               	   <a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $value["pic"];?>" target="_blank" class="gl_sec_ls_img" /></a>
               	   <div class="gl_sec_div">
               	      <a href="<?php echo $url;?>" target="_blank" class="gl_link"><?php echo $value["title"];?></a>
               	   </div>
               	 </li>
			   	 <?php
			   	 }
			   	 ?>
             </ul>
          </div>
           <div class="gl_sec_fr" style="margin-top:13px">
               <ul class="gl_sec_ls">
			     <?php 
			     $left_3 = array_slice($left_news,14,5);
			     foreach($left_3 as $key=>$value){
			     $key=$key+14;
                 $url = $value['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];
			     ?>
               <li>
                 <a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $value["pic"];?>" target="_blank" class="gl_sec_ls_img" /></a>
                 <div class="gl_sec_div">
                    <a href="<?php echo $url;?>" target="_blank" class="gl_link"><?php echo $value["title"];?></a>
                 </div>
               </li>
			   <?php } ?>
             </ul>
           </div>
           <div class="gl_mini_more">
		   <a href="<?php echo $category['url'];?>?qid=<?php echo $qid;?>&_100_<?php echo $category['cname'];?>" target="_blank">点击查看更多资讯</a><em></em>
		   </div>
      </div>
      <!-- 第二屏/-->
  </div>
    </div>
</div>
    <div class="page_scroll" style="right:9px;margin-top:-11px">
        <a href="javascript:void(0)"></a>
        <a href="javascript:void(0)"></a>
        <span class="cur" style="top: 0px;"></span>
    </div>
</div>
</body>
</html>
<?php
            echo '</div>';
        }
    }
?>
