<link style="text/css" rel="stylesheet" href="<?=$resources_url?>/css/jsad.css" />     
<div class="right_w160 fr">
<?php 
  foreach ($ad_list as $key => $ad){
      $class = 'img marb5';
      if($key == 3){
          $class = 'img';
      }
?>
<div class="<?=$class?>">
<a class="txt_link" href="javascript:void(0);" data-href="<?=$ad['url']?>" title="<?=$ad['title']?>" data-filekey="<?=$filekey;?>" data-pos="<?=($key + 1);?>"><img src="<?=$ad['pic']?>" class="pic"  /></a>
</div>
<?php 
 	  }
?>
</div>
<script type="text/javascript" src="<?=$resources_url?>/js/ad.js"></script>