<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta content="width=device-width,initial-scale=1.0,maximum-scale=1.0,user-scalable=no" name="viewport">
    <title>Document</title>
    <link href="http://dev.mini.resources.com/mini/public/css/base.css" type="text/css" rel=stylesheet />
    <link href="http://dev.mini.resources.com/mini/public/css/default.css" type="text/css" rel="stylesheet" />
    <style type="text/css">
      body{
        background: #15547c;
      }
    </style>

</head>
<body>
  <div class="gl_mini">
  <!-- 第一屏 -->
      <div class="gl_section">
          <div class="gl_sec_fl">
		  <?php
			$news_list = $left_news[0];
            $url = $news_list['url'].'?qid='.$qid.'&_1_'.$category['cname'];
		 ?>
             <div class="gl_sec_top"><a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $news_list["pic"];?>" class="gl_sec_img" />
                <span class="gl_top_bj"><?php echo $news_list["title"];?></span>             
             </a></div>
             <ul class="gl_sec_ls">
			 <?php 
			 $left_3 = array_slice($left_news,1,3);
		  	 //echo "<pre>";
		  	 //print_r($left_3);
		  	 //echo "</pre>";
			 foreach($left_3 as $key=>$value){
				 $key=$key+1;
                 $url = $value['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];
				 //echo "url=>".$url;
			 ?>
               <li>
                 <a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $value["pic"];?>" target="_blank" class="gl_sec_ls_img" /></a>
                 <div class="gl_sec_div">
                    <a href="<?php echo $url;?>" target="_blank" class="gl_link">
                    <img src="http://dev.mini.resources.com/mini/public/img/images/branch-img1.gif" class="gl_sec_gif"/><?php echo $value["title"];?></a>
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
                    <a href="<?php echo $url;?>" target="_blank" class="gl_link">
                    <img src="http://dev.mini.resources.com/mini/public/img/images/branch-img1.gif" class="gl_sec_gif"/><?php echo $value["title"];?></a>
                 </div>
               </li>
			 <?php 
			  }
			 ?>
             </ul>
           </div>

           <div class="gl_mini_more"><span>点击查看更多资讯</span><em></em></div>
      </div>
      <!-- 第一屏/ -->
      <!-- 第二屏 -->
       <div class="gl_section">
          <div class="gl_sec_fl">
            
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
               	      <a href="<?php echo $url;?>" target="_blank" class="gl_link">
               	      <img src="http://dev.mini.resources.com/mini/public/img/images/branch-img1.gif" class="gl_sec_gif"/><?php echo $value["title"];?></a>
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
			     $left_3 = array_slice($left_news,14,5);
			     foreach($left_3 as $key=>$value){
			     $key=$key+14;
                 $url = $value['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];
			     ?>
               <li>
                 <a href="<?php echo $url;?>" target="_blank"><img src="<?php echo $value["pic"];?>" target="_blank" class="gl_sec_ls_img" /></a>
                 <div class="gl_sec_div">
                    <a href="<?php echo $url;?>" target="_blank" class="gl_link">
                    <img src="http://dev.mini.resources.com/mini/public/img/images/branch-img1.gif" class="gl_sec_gif"/><?php echo $value["title"];?></a>
                 </div>
               </li>
			   <?php } ?>
             </ul>
           </div>

           <div class="gl_mini_more"><a href="#" target="_blank">点击查看更多资讯</a><em></em></div>
      </div>
      <!-- 第二屏/-->
  </div>


</body>

</html>

