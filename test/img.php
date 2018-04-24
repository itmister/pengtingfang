<?php 

function getImage($url,$save_dir='',$filename='',$type=0){
    if(trim($url)==''){
        return array('file_name'=>'','save_path'=>'','error'=>1);
    }
    if(trim($save_dir)==''){ //默认保存路径
        $save_dir='./';
    }
    if(trim($filename)==''){//默认保存文件名
        $ext=strrchr($url,'.');
        if($ext!='.gif' && $ext!='.jpg' && $ext!='.png'){
            //return array('file_name'=>'','save_path'=>'','error'=>3);
        }
        $filename = md5($url).$ext;
    }
    
    if(0!==strrpos($save_dir,'/')){
        $save_dir.='/';
    }
    //创建保存目录
    if(!file_exists($save_dir)&&!mkdir($save_dir,0777,true)){
        return array('file_name'=>'','save_path'=>'','error'=>5);
    }
    //获取远程文件所采用的方法
    if($type){
        $ch=curl_init();
        $timeout=5;
        curl_setopt($ch,CURLOPT_URL,$url);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);
        curl_setopt($ch,CURLOPT_CONNECTTIMEOUT,$timeout);
        $img=curl_exec($ch);
        curl_close($ch);
    }else{
        ob_start();
        readfile($url);
        $img=ob_get_contents();
        ob_end_clean();
    }
    //$size=strlen($img);
    //文件大小
    $fp2=@fopen($save_dir.$filename,'a');
    fwrite($fp2,$img);
    fclose($fp2);
    unset($img,$url);
    return array('file_name'=>$filename,'save_path'=>$save_dir.$filename,'error'=>0);
}




$url = "http://img.mianfeicha.com/huabian/2016/1124/d2fc2129cc2ad79_size41_w640_h360.jpg";
//getImage($url,'');
my_image_resize('65c641379376ca7bf5aa8a0b808c1b3a.jpg','222222.jpeg','300px','200px');

function my_image_resize($src_file, $dst_file , $new_width , $new_height) {
    $new_width= intval($new_width);
    $new_height=intval($new_height);
    if($new_width <1 || $new_height <1) {
        echo "params width or height error !";
        exit();
    }
    if(!file_exists($src_file)) {
        echo $src_file . " is not exists !";
        exit();
    }
    // 图像类型
    $type=exif_imagetype($src_file);
    $support_type=array(IMAGETYPE_JPEG , IMAGETYPE_PNG , IMAGETYPE_GIF);
    if(!in_array($type, $support_type,true)) {
        echo "this type of image does not support! only support jpg , gif or png";
        exit();
    }
    //Load image
    switch($type) {
        case IMAGETYPE_JPEG :
            $src_img=imagecreatefromjpeg($src_file);
            break;
        case IMAGETYPE_PNG :
            $src_img=imagecreatefrompng($src_file);
            break;
        case IMAGETYPE_GIF :
            $src_img=imagecreatefromgif($src_file);
            break;
        default:
            echo "Load image error!";
            exit();
    }
    $w=imagesx($src_img);
    $h=imagesy($src_img);
    $ratio_w=1.0 * $new_width / $w;
    $ratio_h=1.0 * $new_height / $h;
    $ratio=1.0;
    // 生成的图像的高宽比原来的都小，或都大 ，原则是 取大比例放大，取大比例缩小（缩小的比例就比较小了）
    if( ($ratio_w < 1 && $ratio_h < 1) || ($ratio_w > 1 && $ratio_h > 1)) {
        if($ratio_w < $ratio_h) {
            $ratio = $ratio_h ; // 情况一，宽度的比例比高度方向的小，按照高度的比例标准来裁剪或放大
        }else {
            $ratio = $ratio_w ;
        }
        // 定义一个中间的临时图像，该图像的宽高比 正好满足目标要求
        $inter_w=(int)($new_width / $ratio);
        $inter_h=(int) ($new_height / $ratio);
        $inter_img=imagecreatetruecolor($inter_w , $inter_h);
        //var_dump($inter_w,$inter_h);exit();
        imagecopy($inter_img, $src_img, 0,0,0,100,$inter_w,$inter_h);
        // 生成一个以最大边长度为大小的是目标图像$ratio比例的临时图像
        // 定义一个新的图像
        $new_img=imagecreatetruecolor($new_width,$new_height);
        //var_dump($new_img);exit();
        imagecopyresampled($new_img,$inter_img,0,0,0,0,$new_width,$new_height,$inter_w,$inter_h);
        switch($type) {
            case IMAGETYPE_JPEG :
                imagejpeg($new_img, $dst_file,100); // 存储图像
                break;
            case IMAGETYPE_PNG :
                imagepng($new_img,$dst_file,100);
                break;
            case IMAGETYPE_GIF :
                imagegif($new_img,$dst_file,100);
                break;
            default:
                break;
        }
    } // end if 1
    // 2 目标图像 的一个边大于原图，一个边小于原图 ，先放大平普图像，然后裁剪
    // =if( ($ratio_w < 1 && $ratio_h > 1) || ($ratio_w >1 && $ratio_h <1) )
    else{
        $ratio=$ratio_h>$ratio_w? $ratio_h : $ratio_w; //取比例大的那个值
        // 定义一个中间的大图像，该图像的高或宽和目标图像相等，然后对原图放大
        $inter_w=(int)($w * $ratio);
        $inter_h=(int) ($h * $ratio);
        $inter_img=imagecreatetruecolor($inter_w , $inter_h);
        //将原图缩放比例后裁剪
        imagecopyresampled($inter_img,$src_img,0,0,0,0,$inter_w,$inter_h,$w,$h);
        // 定义一个新的图像
        $new_img=imagecreatetruecolor($new_width,$new_height);
        imagecopy($new_img, $inter_img, 0,0,0,0,$new_width,$new_height);
        switch($type) {
            case IMAGETYPE_JPEG :
                imagejpeg($new_img, $dst_file,100); // 存储图像
                break;
            case IMAGETYPE_PNG :
                imagepng($new_img,$dst_file,100);
                break;
            case IMAGETYPE_GIF :
                imagegif($new_img,$dst_file,100);
                break;
            default:
                break;
        }
    }// if3
}// end function


?>