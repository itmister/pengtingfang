<?php
namespace Util\Email;

class Main {
    /**
     * @var Main
     */
    protected static $_instance = null;


    protected $_cfg = null;

    /**
     * @param array $cfg
     * @return Main|null
     */
    public static function instance( $cfg = array() ) {

        if (empty(self::$_instance ) ) {
            $base = dirname(__FILE__) . '/';
            require_once( $base . 'PHPMailer.class.php' );
            require_once( $base . 'POP3.class.php' );
            require_once( $base . 'SMTP.class.php' );
            if (empty($cfg)) $cfg = \Config::get('email');

            if (empty($cfg)) {//兼容旧写法
                $cfg = array(
                    'host' =>  \Config::get('Email_Host'),             // SMTP 服务器
                    'port' => \Config::get('Email_Port'),              // SMTP 服务器,
                    'account' => \Config::get('Email_Account'),       // SMTP服务器用户名,
                    'password' => \Config::get('Email_Password'),     // SMTP服务器密码,
                    'username' => \Config::get('Email_Username'),
                );
            }
//            'Email_Host' 	 => 'smtp.exmail.qq.com',
//				'Email_Username' => 'service@7654.com',
//				'Email_Port'	=> '465',
//				'Email_Password' => 'lUQU35DCHisE',
//            \Lib\Core::dead( $cfg );
            self::$_instance = new self( $cfg );
        }
        return self::$_instance;
    }

    public function __construct( $cfg ) {
        $this->_cfg = $cfg;
    }

    /**
 * @param $email_address 收件人地址
 * @param $title 邮件标题
 * @param $content 邮件正文
 * @param $to_username 收件人名称
 * @param [] $attachment 邮件附件 [[file:文件路径,name:附件名称] ...]
 * @return bool|string
 */
    public function send($email_address, $title, $content, $to_username = '', $attachment_list = [] ) {
        $cfg = $this->_cfg;
        $phpmail              = new PHPMailer();        //PHPMailer对象
        $phpmail->CharSet    = 'UTF-8';                //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $phpmail->IsSMTP();                             // 设定使用SMTP服务
        $phpmail->SMTPDebug  = 0;                      // 关闭SMTP调试功能
        $phpmail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $phpmail->SMTPSecure = 'ssl';                 // 使用安全协议


        $phpmail->Host        = $cfg['host'];//C('Email_Host');       // SMTP 服务器
        $phpmail->Port        = $cfg['port'];//C('Email_Port');       // SMTP服务器的端口号
        $phpmail->Username   = $cfg['account'];//C('Email_Account');   // SMTP服务器用户名
        $phpmail->Password   = $cfg['password'];//C('Email_Password');  // SMTP服务器密码

        $phpmail->SetFrom( $cfg['account'], $cfg['username'] );
        $phpmail->Subject    = $title;
        $phpmail->MsgHTML($content);
        $phpmail->AddAddress($email_address, $to_username);

        //发邮件增加发附件 vl@20150626
        if ( !empty($attachment_list) && is_array($attachment_list) ) foreach($attachment_list as $attachment ){
            if (!empty($attachment['file']) && is_file($attachment['file'])) {
                $name = !empty($attachment['name']) ? $attachment['name'] : $attachment['file'];
                $phpmail->addAttachment( $attachment['file'], $name);
            }
        }

        return  $phpmail->Send() ? true : $phpmail->ErrorInfo;

    }


    /**
     * @param $email_address
     * @param $title
     * @param $content
     * @param array $cc_email_address
     * @param string $to_username
     * @param array $attachment_list
     * @return bool|string
     * @throws \Exception
     * @throws phpmailerException
     */
    public function send_with_cc($email_address, $title, $content,$cc_email_address =[], $to_username = '', $attachment_list = [] ) {
        $cfg = $this->_cfg;
        $phpmail              = new PHPMailer();        //PHPMailer对象
        $phpmail->CharSet    = 'UTF-8';                //设定邮件编码，默认ISO-8859-1，如果发中文此项必须设置，否则乱码
        $phpmail->IsSMTP();                             // 设定使用SMTP服务
        $phpmail->SMTPDebug  = 0;                      // 关闭SMTP调试功能
        $phpmail->SMTPAuth   = true;                  // 启用 SMTP 验证功能
        $phpmail->SMTPSecure = 'ssl';                 // 使用安全协议

        $phpmail->Host        = $cfg['host'];//C('Email_Host');       // SMTP 服务器
        $phpmail->Port        = $cfg['port'];//C('Email_Port');       // SMTP服务器的端口号
        $phpmail->Username   = $cfg['account'];//C('Email_Account');   // SMTP服务器用户名
        $phpmail->Password   = $cfg['password'];//C('Email_Password');  // SMTP服务器密码

        $phpmail->SetFrom( $cfg['account'], $cfg['username'] );
        $phpmail->Subject    = $title;
        $phpmail->MsgHTML($content);
        if (is_array($email_address)){
            for( $i =0;$i<count($email_address);$i++){
                $phpmail->AddAddress($email_address[$i], $to_username[$i]);
            }
        }else{
            $phpmail->AddAddress($email_address, $to_username);
        }
        if ($cc_email_address){
            if (is_array($cc_email_address)){
                foreach($cc_email_address as $val){
                    $phpmail->addCC($val);
                }
            }else{
                $phpmail->addCC($cc_email_address);
            }
        }
        //发邮件增加发附件 vl@20150626
        if ( !empty($attachment_list) && is_array($attachment_list) ) foreach($attachment_list as $attachment ){
            if (!empty($attachment['file']) && is_file($attachment['file'])) {
                $name = !empty($attachment['name']) ? $attachment['name'] : $attachment['file'];
                $phpmail->addAttachment( $attachment['file'], $name);
            }
        }
        return  $phpmail->Send() ? true : $phpmail->ErrorInfo;
    }
}