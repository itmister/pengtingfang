<?php

$file = new file();
$file->set_content('pengtingfang\n2222');



class file{

    public function set_content($str){
        $file_pash = 'data/text.txt';
        if(!file_exists($file_pash)){ //判断是否存在
            echo "文件不存在";
            $myfile = fopen($file_pash ,"w"); //创建文件
            echo "<br>创建了文件";
        }
        
       // $myfile = fopen($file_pash, "w+") ;

       //  fwrite($myfile,$str);//写入内容
       // fwrite($myfile,$str);//写入内容
       //file_put_contents($file_pash, '33333',FILE_APPEND);  //追加内容      
       // fclose($myfile); }        );
        
    }
}



?>