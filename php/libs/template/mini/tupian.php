<div class="iframe iframe_imgf">
    <div class="img_box clearfix">
			<?php 
				if($pic_news){
					$j = 0;
					foreach ($pic_news as $key => $news){
						$mod = $j % 4;
						$j ++;
						
						$newstype = 0;//0 自动 ；1 手动
						if($news['expire_time'] > 0){
							$newstype = 1;
						}
						$class = "w168 clearfix";
						if($key <= 3){
							$subtype = "left";
							$class = "w168 clearfix ul_w168";
						}else if($key <= 7){
							$subtype = "center";
						}else{
							$subtype = "right";
						}

						$pdata = $news['url'].'|'.$newstype.'|'.$category['ename'].'|'.($key + 1).'|'.$btype.'|'.$subtype;
						$url = $news['url'].'?qid='.$qid.'&_'.($key + 1).'_'.$category['cname'];
						if($mod == 0){
							echo '<ul class="'.$class.'">';
						}
				?>
						<li class="img_p10">
							<a pdata="<?=$pdata?>" class="pic" href="<?=$url?>" target="_blank" title="<?=$news['title']?>">
								<img src="<?=$news['pic']?>"/>
							</a>
							<div class="opa"></div>
							<a pdata="<?=$pdata?>" class="opa_txt"  href="<?=$url?>" target="_blank"   title="">
								<?=$news['title']?>
							</a>
						</li>
				<?php 
							if($mod == 3){
								echo '</ul>';
							}
						}
					}
				?>
    </div><!-- end 图片 -->
</div>