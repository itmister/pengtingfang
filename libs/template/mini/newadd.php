========================
<!--toutiao123456-->
<!--带大图的iframe-->
<link style="text/css" rel="stylesheet" href="<?=$resources_url?>/public/css/toutiao.css" />
<div class="iframe" style="position: relative;">
<div  id="dowebok">
    <div class="tt-cont">
       <!-- 第一屏 start-->
        <div  class="section">
            <ul class="w170 clearfix">
                 <?php
                    $btype   = "pic";
                    $subtype = "left";
                    foreach($left_news as $key => $news){
            
                        $newstype = 0;//0 自动 ；1 手动
                        if($news['expire_time'] > 0){             
                            $newstype = 1;             
                        }
						$pdata = $news['url'].'|'.$newstype.'|'.$category['ename'].'|'.$news['index'].'|'.$btype.'|'.$subtype;
                        $url = $news['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];

						$class = "img";
                        if($key == 0){
                            $class = "img img-big";
                        }
                  ?>
                    <li class="<?=$class?>" <?php if($category['cname']=='头条'){?> onclick="_hmt.push(['_trackEvent', 'toutiao_left', 'click', 'position_<?php echo $key+1;?>']);" <?php } ?>>
                        <a class="pic" pdata="<?=$pdata?>"  target="_blank" href="<?=$url?>" title="<?=$news['title']?>"><img src="<?=$news['pic']?>" /></a>
                        <span class="txt"></span>
                        <a pdata="<?=$pdata?>" target="_blank" class="txt_a" href="<?=$url?>" title="<?=$news['title']?>"><?=$news['title']?></a>
                    </li>
                 <?php
                    }
                  ?>
                  <?php 
                    if($left_down_ad_code){
                  ?>
                  <li class="img img-ad">
                    <!-- 广告位 -->
                    <?php echo base64_decode($left_down_ad_code);?>
                  </li>
                  <?php }?>
            </ul>
        
            <div class="w355 clearfix">
                <ul class="title_content" style="overflow: hidden;">
                 <?php
                    $temp_index = 0;
                    $btype   = "text";
                    foreach($right_news as $key => $news){
        
                        $newstype = 0;//0 自动 ；1 手动
                        if($news['expire_time'] > 0){
                            $newstype = 1;
                        }
                        //位置
                        $index = $temp_index = $news['index'];
                        if($index == 21){
                            break;
                        }
                        $subtype = ($news['pos'] == 0) ? 'left' : ($news['pos'] == 1 ? 'center' : 'right');
                        if($news['pos'] >= 0){
                            $index = $news['index'].'.'.$news['pos'];
                            if($news['pos'] == 0 && $right_news[$key + 1]['pos'] <= 0){
                                $index   = $news['index'];
                                $subtype = 'center';
                            }
                        }
                        $pdata = $news['url'].'|'.$newstype.'|'.$category['ename'].'|'.$news['index'].'|'.$btype.'|'.$subtype;
                        $url = $news['url'].'?qid='.$qid.'&_'.$index.'_'.$category['cname'];
                        
                        //标题样式
                        $li_class= "title_h2";
                        if($news['pos'] < 0){
                            $li_class= "title_h1";
                        }
                        
                        //小图标样式
                        $a_class   = "branch-a";
                        $span_html = '<span class="point_gray"></span>';
                        if($news['ico']){
                            $span_html = '';
                            $a_class   .= " branch-a-img{$news['ico']}";
                        }
                        //新闻样式
                        if(isset($news['style'])){
        					if(count($news['style']) == 2){
        						if($news['style'][0] == 0){
        							$a_class .=" a_red";
        						}
        						if($news['style'][1] == 1){
        							$a_class .=" a_underline";
        						}
        					}else{
        						if($news['style'][0] == 0){
        							$a_class .=" a_red";
        						}
        						if($news['style'][0] == 1){
        							$a_class .=" a_underline";
        						}
        					}
                            $a_class = trim($a_class);
                        }
        				//算定义颜色
        				$style = '';
        				if($news['color']){
        					$style = 'style="color:'.$news['color'].'"';
        				}
						//算定义文字广告样式
						$ad_pdata = $ad_style = $ad_class = $ad_pos = '';
						if($news['ad_code']){
							$ad_pdata = 'pdata="'.$pdata.'"';
							$ad_style = $style;
							$ad_class = 'adclass="'.$a_class.'"';

							//文字新闻位置
							$pos = '?qid='.$qid.'&_'.$index.'_'.$category['cname'];
							$ad_pos  = 'adpos="'.$pos.'"'; 
						}
                    ?>
                    <?php if($news['pos'] < 1){?>
                        <li class="<?=$li_class?>" <?=$ad_pdata?> <?=$ad_style?> <?=$ad_class?> <?=$ad_pos?>>
                    <?php }?>
                    
                        <?=$span_html?>
                        <!-- 文字广告 -->
                        <?php 
                            if($news['ad_code']){
								$urlarr[] = substr($url,strpos($url,"?"));
                                echo base64_decode($news['ad_code']);
                            }else{
                        ?>
                        <a class="<?=$a_class?>" pdata="<?=$pdata?>" target="_blank" href="<?=$url?>" <?=$style?> title="<?=$news['title']?>"><i></i><?=$news['title']?></a>
                    <?php 
                           }
                        if(in_array($right_news[$key + 1]['pos'],[0,-1])){?>
                        </li>
                    <?php }?>
                <?php
                    }
                ?>
                </ul>
				<script>
					var arrayurl=new Array()
					<?php
					foreach($urlarr as $k=>$v){
					?>
					arrayurl[<?php echo $k;?>]="<?php echo $v;?>"
					<?php
					}
					?>
				</script>
                <?php if($more_news){?>
                  <div class="mini-more">
                    <div>点击查看更多资讯<span class="mini6-icon1"></span></div>
                  </div>
               <?php }?>
            </div>
        </div>
       <!-- 第一屏 end-->
       <?php if($more_news){?>
        <!-- 第二屏 start  都是自动新闻-->
        <div class="section">
            <div class="addbox">
                <ul>
                    <?php 
                        foreach ($more_news as $key => $more){
                            $mod = $i % 3;
                            $i ++;
                            
                            $newstype = 0;//0 自动 ；1 手动
                            if($more['expire_time'] > 0){
                                $newstype = 1;
                            }
                            if($key <= 2){
                                $subtype = "left";
                            }else if($key <= 5){
                                $subtype = "center";
                            }else{
                                $subtype = "right";
                            }

                            $pdata = $news['url'].'|'.$newstype.'|'.$category['ename'].'2|'.($key + 4).'|'.$btype.'|'.$subtype;
                            $url = $more['url'].'?qid='.$qid.'&_'.($temp_index + 1 + $key).'_'.$category['cname'];
                            
                            if($mod == 0){
                                echo '<li>';
                            }
                    ?>
                        <a href="<?=$url?>" pdata="<?=$pdata?>" title="<?=$more['title']?>" target="_blank">
                            <img src="<?=$more['pic']?>">
                            <p><?=$more['title']?></p>
                        </a>
                    <?php 
                            if($mod == 2){
                                echo '</li>';
                            }
                        }
                    ?>
                 </ul>
                 <div class="mini-more2">
                   <div class="link"><a href="<?php echo $category['url'];?>?qid=<?php echo $qid;?>&_100_<?php echo $category['cname'];?>" target="_blank">点击查看更多资讯</a><span class="mini6-icon1"></span></div>
                 </div>
            </div>
        </div>
        <?php }?>
       <!-- 第二屏 end-->
    </div>
</div>
    <?php if($more_news){?>
        <div class="page_scroll">
        	<a href="javascript:void(0)"></a>
        	<a href="javascript:void(0)"></a>
        	<span class="cur"></span>
        </div>
    <?php }?>
</div>
<script>
	$("a.link").each(function(i){
		oldurl = $(this).attr("href");
		oldurl = oldurl.split("?");
		$(this).attr("href",oldurl[0]+""+arrayurl[i]);
	});
</script>
