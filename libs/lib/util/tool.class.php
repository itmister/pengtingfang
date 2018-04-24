<?php
/**
 * @desc Tool 杂功能
 */
namespace Util;

class Tool {

    /**
     * 正则匹配迭代返回数组
     * @param string $pattern 正则表达式
     * @param string $subject
     * @param array $arr_fields
     * @return array
     */
    public static function preg_to_array($pattern, $subject, $arr_fields = []) {
        $ret        = preg_match_all( $pattern, $subject, $arr );
//        $result     = [];
        $fields     = [];
        if ( $ret > 0 ) {
            foreach (array_keys($arr) as $key) {
                if ($key == 0) continue;
                $fields[$key] = isset($arr_fields[$key - 1]) ? $arr_fields[$key - 1] : $key;
            }

            foreach ( array_keys( $arr[0] ) as $row_key ) {
                $_item = [];
                foreach ($fields as $col_key => $field) if ( !empty($field) ) $_item[$field] = $arr[$col_key][$row_key] ;
                yield $_item;
            }
        }
//        echo $ret; die();
//        return $result;
    }

    /**
     * @param array $data
     * @param array $title
     * @param string $filename
     */
    function another_export_csv($data=array(),$title=array(),$filename='report'){
        header("Content-type:application/octet-stream");
        header("Accept-Ranges:bytes");
        header("Content-type:application/vnd.ms-excel");
        header("Content-Disposition:attachment;filename=".$filename.".csv");
        header("Pragma: no-cache");
        header("Expires: 0");
        //导出xls 开始
        if (!empty($title)){
            foreach ($title as $k => $v) {
                $title[$k]=iconv("UTF-8", "GB2312",$v);
            }
            $title= implode(",", $title);
            echo "$title\n";
        }
        if (!empty($data)){
            foreach($data as $key=>$val){
                foreach ($val as $ck => $cv) {
                    $data[$key][$ck]=trim(iconv("UTF-8", "GB2312", $cv));
                }
                $data[$key]=implode(",", $data[$key]);

            }
            echo implode("\n",$data);
        }
    }


    /**
     * 导出数据为csv文件
     *@param $data     导出数据
     *@param $title    excel的第一行标题
     *@param $parmas   特殊处理字段
     *@param $filename 下载的文件名,
     */
    public static function export_csv($data=array(),$title=array(),$parmas = array(),$filename='report')
    {
    
        header("Content-type:text/csv");
        header("Content-Disposition:attachment;filename=".$filename . '.csv');
        header('Cache-Control:must-revalidate,post-check=0,pre-check=0');
        header('Expires:0');
        header('Pragma:public');
    
        //导出csv 开始
        //输出标题
        if (!empty($title))
        {
            $csv_title = $title;
            foreach ($csv_title as $key => $val)
            {
                $csv_title[$key]=iconv("UTF-8", "GB2312",$val['name']);
            }
            $csv_title= implode(",", $csv_title);
            echo "$csv_title\n";
        }
     
        //输出内容
        $new_data = array();
        if (!empty($data))
        {
            foreach($data as $_key=>$_val)
            {
                foreach ($title as $_v => $_t)
                {
                    $default = "-";
                    if(in_array($_t['field'],$parmas))
                    {
                        $default = 0;
                    }else if(is_numeric($_val[$_t['field']])){
                        $default = 0;
                    }
                    if(isset($_t['type']))
                    {
                        if($_t['type'] == 'date')
                        {
                            $value = $_val[$_t['field']] ? date('Y/m/d H:i:s',$_val[$_t['field']]) : $default;
                        }
                        if($_t['type'] == 'rate')
                        {
                            $value = round( $_val[$_t['num1']]/$_val[$_t['num2']] * 100 , 2).'%';
                        }
                        if($_t['type'] == '-'){
                            $value = (int)($_val[$_t['num1']] - $_val[$_t['num2']]);
                        }
                    }
                    else
                    {
                        $value = $_val[$_t['field']] ? $_val[$_t['field']] : $default;
                    }
                    $new_data[$_key][] = iconv("UTF-8", "GB2312", str_replace(',','，',$value));
                }
                $new_data[$_key]=implode(",", $new_data[$_key]);
            }
            echo implode("\n",$new_data);
        }
    }
    
    /**
     * 保存csv文件
     * @param array $title_field
     * @param array $data
     * @param string $savepath
     * @return string
     */
    public static function savecsv($title,$data,$savepath,$filename = '')
    {
        if(!$data || !$savepath)
            return false;
        if(!$filename) 
            $filename = date('Y-m-d').'.csv';

        //打开文件
        $fp = fopen($savepath.$filename,'w');
        
        //保存csv开始
        //输出标题
        if (!empty($title))
        {
            $csv_title = $title;
            foreach ($csv_title as $key => $val)
            {
                $csv_title[$key]=iconv("UTF-8", "GB2312",$val['name']);
            }
            fputcsv($fp,$csv_title);
        }
        
        //输出内容
        $new_data = array();
        if (!empty($data))
        {
            foreach($data as $_key=>$_val)
            {
                foreach ($title as $_v => $_t)
                {
                    $new_data[$_key][] = iconv("UTF-8", "GB2312", $_val[$_t['field']]);
                }
                fputcsv($fp, $new_data[$_key]);
            }
        }
        fclose($fp);
        
        return $savepath.$filename;
    }
    
    
    /**
     * 解析xml
     * @param string $str_xml
     * @return multitype:|array
     */
    public static function get_xml_data($str_xml) {
        $pos = strpos($str_xml, 'xml');
        if ($pos) {
            $xml_obj = simplexml_load_string($str_xml,'SimpleXMLElement', LIBXML_NOCDATA);
            $array_data= Tool::get_object_vars_final($xml_obj);
            return $array_data ;
        } else {
            return '';
        }
    }
    
    private static function get_object_vars_final($obj){
        if(is_object($obj)){
            $obj=get_object_vars($obj);
        }
        if(is_array($obj)){
            foreach ($obj as $key=>$value){
                $obj[$key]= Tool::get_object_vars_final($value);
            }
        }
        return $obj;
    }
    
    /**
     * 写日志
     * @param string $file_path
     * @param string $content
     * @param string $filename
     */
    public static function write_log($file_path,$content,$filename = 'log.txt'){
        //创建目录
        if(!is_dir($file_path)){
            Tool::mk_dir($file_path);
        }
        $filename = $file_path.'/'.$filename;
        
        $log = date("Y-m-d H:i:s")." ".$content."\r\n";;
        $handle = fopen($filename,"a+");
        fwrite($handle, $log);
        fclose($handle);
    }
    
    /**
     * 创建目录
     * @param string $dir
     * @param number $mode
     * @return boolean
     */
    public static function mk_dir($dir, $mode = 0755)
    {
        if (is_dir($dir) || @mkdir($dir,$mode)) return true;
        if (!Tool::mk_dir(dirname($dir),$mode)) return false;
        return @mkdir($dir,$mode);
    }
    
    /**
     * 发邮件
     * @param string $config 配置
     * @param string $to 收信地址
     * @param string $subject 邮件主题
     * @param string $body 邮件内容
     * @param string $cc 抄送地址
     * @param string $attachment 附件
     */
    public static function send_mail($config,$to,$subject, $body, $cc = null,$attachment = null){
        if(!is_array($config) || !$to || !$subject || !$body){
            return false;
        }
        //默认配置
        $default = [
            'SMTP_HOST'   => 'smtp.qq.com', //SMTP服务器
            'SMTP_PORT'   => '465', //SMTP服务器端口
            'FROM_EMAIL'  => $config['SMTP_USER'], //发件人EMAIL
            'FROM_NAME'   => '7654', //发件人名称
            'REPLY_EMAIL' => '', //回复EMAIL（留空则为发件人EMAIL）
            'REPLY_NAME'  => '', //回复名称（留空则为发件人名称）
        ];
        $config = array_merge($default,$config);
    
        $mail             = new \Util\Email\PHPMailer(true); //PHPMailer对象
        $mail->CharSet    = 'UTF-8'; //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $mail->IsSMTP();  // 设定使用SMTP服务
        $mail->SMTPDebug  = 0;                     // 关闭SMTP调试功能   0 close 、1 errors and messages 、2 messages only
        $mail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
    
        if ($config['SMTP_PORT'] == 465)
            $mail->SMTPSecure = 'ssl';                 // 使用安全协议
    
        $mail->Host       = $config['SMTP_HOST'];  // SMTP 服务器
        $mail->Port       = $config['SMTP_PORT'];  // SMTP服务器的端口号
        $mail->Username   = $config['SMTP_USER'];  // SMTP服务器用户名
        $mail->Password   = $config['SMTP_PASS'];  // SMTP服务器密码
    
        $mail->SetFrom($config['FROM_EMAIL'], $config['FROM_NAME']);
    
        $replyEmail       = $config['REPLY_EMAIL']?$config['REPLY_EMAIL']:$config['FROM_EMAIL'];
        $replyName        = $config['REPLY_NAME']?$config['REPLY_NAME']:$config['FROM_NAME'];
    
        $mail->AddReplyTo($replyEmail, $replyName);
        $mail->FromName   = $replyName;
        $mail->Subject    = $subject;
        $mail->WordWrap   = 80;
        $mail->MsgHTML($body);
        
        //收件人
        if(is_array($to)){
            foreach ($to as $_to){
                $mail->AddAddress($_to);
            }
        }else{
            $mail->AddAddress($to);
        }
        
    
        //添加抄送
        if($cc){
            if(is_array($cc)){
                foreach ($cc as $_cc){
                    $mail->AddCC($_cc);
                }
            }
        }
    
        if(is_array($attachment)){ // 添加附件
            foreach ($attachment as $file){
                is_file($file) && $mail->AddAttachment($file);
            }
        }else{
            if(file_exists($attachment)){
                $mail->AddAttachment($attachment);
            }
        }
        try{
            $mail->Send();
        }catch (\Exception $e){
            return $e->getMessage();
        }
    }

    /**
     * uuid生成
     * @return string
     */
    public static function uuid(){
        mt_srand((double)microtime() * 10000);
        $uuid = md5(uniqid(rand(), true));
        return $uuid;
    }

    /**
     * 获取用户id
     * @return string
     */
    public static function get_client_ip() {
        if (isset($_SERVER)) {
            if (isset($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                return $_SERVER['HTTP_X_FORWARDED_FOR'];
            } elseif (isset($_SERVER['HTTP_CLIENT_IP'])) {
                return $_SERVER['HTTP_CLIENT_IP'];
            } elseif(isset($_SERVER['REMOTE_ADDR'])) {
                return $_SERVER['REMOTE_ADDR'];
            }
        } else {
            if (getenv("HTTP_X_FORWARDED_FOR")) {
                return getenv( "HTTP_X_FORWARDED_FOR");
            } elseif(getenv("HTTP_CLIENT_IP")) {
                return getenv("HTTP_CLIENT_IP");
            } else {
                return getenv("REMOTE_ADDR");
            }
        }
        return '';
    }


    /**
     * @param $str_org
     * @param int $len
     */
    public static function fill_string($str_org, $fill_str = '0', $len = 5, $type = 1) {
        $str  = str_repeat($fill_str, $len - strlen($str_org)) ;
        return $type == 1 ? ( $str . $str_org ) : ( $str_org . $str );
    }

    /**
     * ip转换成地址
     * @param string $ip
     * @return array
     */
    public static function get_area_by_ip($ip = '',$filename = ''){
        if(!$ip) $ip = Tool::get_client_ip();
        if(!$filename) $filename = 'UTFWry.dat';

        $Ip = new \Util\Net\Ip($filename);      // 实例化类 参数表示IP地址库文件
        $area = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
        
        //获取字符串编码
        $encode = mb_detect_encoding($area['country'], array("ASCII","UTF-8","GB2312","GBK","BIG5"));
        if($encode != 'UTF-8'){
            $area['country'] = iconv("GBK", "UTF-8//IGNORE", $area['country']);
            $area['area'] = iconv("GBK", "UTF-8//IGNORE", $area['area']);
        }

        return $area;
    }
    
    public static function get_country_by_ip($ip = ''){
        if(!$ip) $ip = Tool::get_client_ip();
    
        $Ip = new \Util\Net\Ip('UTFWry_new.dat');   // 实例化类 参数表示IP地址库文件
        $area = $Ip->getlocation($ip); // 获取某个IP地址所在的位置
        return $area;
    }
    
    /**
     * 获取省市
     */
    public static function get_province_city_by_ip($ip = ''){
        if(!$ip) $ip = Tool::get_client_ip();
        
        $obj  = new \Util\Net\Ipsearch();
        $area = $obj->get($ip);
        $area = explode('|', $area);
        
        //省、市
        @$data = [
            'province' => $area['2'] ? $area['2'] : '未知',
            'city'     => $area['3'] ? $area['3'] : '未知'
        ];
        return $data;
    }
    /*
     * 判断登录ip是否是公司ip登录 可以登录返回 true 禁止登陆返回 false;
     * */
    public static function check_login_ip(){
        $ip = Tool::get_client_ip();
        $ip = trim($ip);
        $loginIp = array();

        $model=\Dao\Union\Ipwhite_login::get_instance();
        $data=$model->select(array(
            'field'=>"ip",
        ));
        foreach($data as $v){
            $loginIp[]=trim($v['ip']);
        }

        if(!in_array($ip,$loginIp)&&$ip){
            //caolei 这里是安全限制 请不要注释代码！如发现不能访问 请将ip添加到相应的配置文件中
            return false;
        }
        return true;
    }

    public static function check_area($ip = '',$has_use = false){
        #TODO 由于3.15 暂时停止语音验证码
        if($has_use == false){
            return false;
        }
        //语音短信适用地区
        $key = "Speech_focus_area";
        $obj_redis = \Union\Service\Redis\Main::get_instance();
        $list = $obj_redis->get($key);
        if(empty($list)){
            $model=\Dao\Union\point_area::get_instance();
            $data=$model->select(array(
                'field'=>"province,city",
            ));
            foreach($data as $k=>$v){
                $area_list[$k][]=$v['province'];
                $area_list[$k][]=$v['city'];
            }
            $obj_redis->set($key,json_encode($area_list));
        }else{
            $area_list = json_decode($list,1);
        }


//        $area_list = [
//            ['吉林','通化'],['江苏','宿迁'],['福建','厦门'],['福建','莆田'],['广西','南宁'],['陕西','西安'],['福建','福州'],['福建','长乐'],['浙江','湖州'],
//            ['浙江','绍兴'],['广东','梅州'],['江苏','徐州'],['湖南','郴州'],['江苏','镇江'],['浙江','温州'],['四川','资阳'],['重庆','重庆']
//        ];
        //获取地址
        $area = Tool::get_area_by_ip($ip);
        if(!$area) return false;

        //是否存在
        foreach ($area_list as $value){
            if(strstr($area['country'],$value[0]) && strstr($area['country'],$value[1])){
                return true;
            }
        }
        return false;
    }

    public static function check_reg($ip = ''){
        //注册屏蔽地区
        $area_list = [
            ['江苏','镇江']
        ];
        //获取地址
        $area = Tool::get_area_by_ip($ip);
        if(!$area) return false;
        //是否存在
        foreach ($area_list as $value){
            if(strstr($area['country'],$value[0]) && strstr($area['country'],$value[1])){
                return true;
            }
        }
        return false;
    }
   
    /**
     * 生成二维码
     * @param string $url
     * @param string $outfile
     * @param number $level
     * @param number $size
     * @param number $margin
     * @param real $back_color
     */
   public static function generate_qrcode($url,$outfile = false,$level = 2,$size = 4,$margin = 4,$back_color = 0xF4F4F8){
       $path = dirname(__FILE__) . '/';
       require_once( $path . 'phpqrcode.class.php' );
       
       return QRcode::png($url,$outfile,$level,$size,$margin,false,$back_color);
   }


    /**
     * 用*号替换帐号部分字符实现隐藏帐号
     * @param string $account
     * @return string
     */
    public static function hide_account( $account, $char_replace = '*' ) {
        if (empty($account)) return $char_replace;

        $len = strlen($account);
        if ( $len <= 3 ) return substr($account,0, $len -1 ) . $char_replace;

        $start = floor($len / 4 );
        $replace_len = floor( $len / 2 );
        return substr($account, 0, $start ) . str_repeat( $char_replace, $replace_len) . substr($account, $start + $replace_len );
    }

    /**
     * 生成随机字符串
     * @param int $length
     * @return string
     */
    public function create_nonce_str($length = 16) {
        $chars = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }
    
    /**
     * 腾讯CND，刷新url
     * @param array $data   数据
     * @param array $method 方法
     * @return Ambigous <string, boolean, mixed>
     */
    public static function tencent_push_refresh_url($data,$method)
    {
        $url = 'https://cdn.api.qcloud.com/v2/index.php';
        $postData = [
            'Action'   => 'RefreshCdnUrl',
            'Timestamp'=> time(),
            'Nonce'    => rand(),
            'SecretId' => 'AKIDvlSxKYHVD0d4LQJ30oQs9IYihjdLLBLN',
        ];
        $postData = array_merge($postData,$data);
    
        //生成签名
        ksort($postData);
    
        $sigstr = $method."cdn.api.qcloud.com/v2/index.php?";
        $is_first = true;
        foreach ($postData as $key => $value)
        {
            if (!$is_first)
            {
                $sigstr = $sigstr."&";
            }
            $is_first = false;
            //拼接签名原文时，如果参数名称中携带_，需要替换成
            if(strpos($key, '_'))
            {
                $key = str_replace('_', '.', $key);
            }
            $sigstr = $sigstr.$key."=".$value;
        }
    
        //根据签名原文字符串 $SigTxt，生成签名 Signature
        $secretKey = 'jJivMfqqeFd5OVQsCJEyWs6FF5jBWjkZ';
        $Signature = base64_encode(hash_hmac('sha1', $sigstr, $secretKey, true));
    
        //拼接请求串,对于请求参数及签名，需要进行urlencode编码
        $req = "Signature=".urlencode($Signature);
        foreach ($postData as $key => $value)
        {
            $req=$req."&".$key."=".urlencode($value);
        }
    
        //发送请求
        $reponse= Tool::curl_post($url,$req);
        return json_decode($reponse,true);
    }

    /**
     * 腾讯CND，刷新目录
     * @param array $data   数据
     * @param array $method 方法
     * @return Ambigous <string, boolean, mixed>
     */
    public static function tencent_push_refresh_dir($data,$method)
    {
        $url = 'https://cdn.api.qcloud.com/v2/index.php';
        $postData = [
            'Action'   => 'RefreshCdnDir',
            'Timestamp'=> time(),
            'Nonce'    => rand(),
            'SecretId' => 'AKIDvlSxKYHVD0d4LQJ30oQs9IYihjdLLBLN',
        ];
        $postData = array_merge($postData,$data);

        //生成签名
        ksort($postData);

        $sigstr = $method."cdn.api.qcloud.com/v2/index.php?";
        $is_first = true;
        foreach ($postData as $key => $value)
        {
            if (!$is_first)
            {
                $sigstr = $sigstr."&";
            }
            $is_first = false;
            //拼接签名原文时，如果参数名称中携带_，需要替换成
            if(strpos($key, '_'))
            {
                $key = str_replace('_', '.', $key);
            }
            $sigstr = $sigstr.$key."=".$value;
        }

        //根据签名原文字符串 $SigTxt，生成签名 Signature
        $secretKey = 'jJivMfqqeFd5OVQsCJEyWs6FF5jBWjkZ';
        $Signature = base64_encode(hash_hmac('sha1', $sigstr, $secretKey, true));

        //拼接请求串,对于请求参数及签名，需要进行urlencode编码
        $req = "Signature=".urlencode($Signature);
        foreach ($postData as $key => $value)
        {
            $req=$req."&".$key."=".urlencode($value);
        }

        //发送请求
        $reponse= Tool::curl_post($url,$req);
        return json_decode($reponse,true);
    }



    public static function curl_post($url, $data)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if (parse_url($url, PHP_URL_SCHEME) == 'https') {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,  false);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  false);
        }
        $result = curl_exec($ch);
        return $result;
    }
    
    public static function save_image($url,$save_path = '') {
        //是否是图片
        if(!preg_match('/\/([^\/]+\.[a-z]{3,4})$/i',$url,$matches)) 
            return false;
        
        $image_name = strtolower($matches[1]);
        
        //获取图片资源
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
        $img = curl_exec($ch);
        curl_close($ch);
        
        //目录是否存在
        if (!file_exists($save_path)){
            \Util\Tool::mk_dir($save_path);
        }
        
        //保存图片
        $fp = fopen($save_path.$image_name,'w');
        fwrite($fp, $img);
        fclose($fp);
    }

    public static function string_encoding($string){
        $encode = mb_detect_encoding($string, array("ASCII","UTF-8","GB2312","GBK"));
        if($encode != "UTF-8"){
            $code_string = iconv($encode,"UTF-8",$string);
            return $code_string;
        }else{
            return $string;
        }
    }
    
    /*
     * @pamarm
     * $dir 本地文件夹路径
     * $f_dir cdn上保存路径
     */
    public  static function listDir($dir,$f_dir){
        if(is_dir($dir)){
            if ($dh = opendir($dir)){
                while (($file = readdir($dh)) !== false){
                    if((is_dir($dir."/".$file)) && $file!="." && $file!=".." && $file!='.svn'){
                        self::listDir($dir.$file."/",$f_dir.$file."/");
                    }else{
                        if($file!="." && $file!=".." && !in_array($file,array('.svn','.php'))){
   
                            $bucket = 'mini2eastdaycom';
                            $bucket = new \Sdk\Ws\Bucket($bucket);
                            $i = 0;
                            check:
                            $ret = $bucket->upload( $f_dir . $file, $dir . $file);
                            if(!$ret && $i<3){
                                $i++;
                                goto check;
                            }
                        }
                    }
                }
                closedir($dh);
            }
        }
    }
}