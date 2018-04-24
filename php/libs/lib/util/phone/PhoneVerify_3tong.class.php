<?php
namespace Util\Phone;
class PhoneVerify_3tong extends Phone_Verify {

	public function __construct($cfg = array()) {
		$cfg = array_merge($cfg, array(
			'account' => 'dh21023',
			'password' => 'dh21023',
			//'userid' => 706,
			'url_send' => 'http://3tong.net/http/sms/Submit'
		));
		parent::__construct($cfg);
	}
	
	public function _send($phone_number, $msg) {
		
		$cfg = $this->_cfg;
		$password = md5($cfg['password']);
		$message = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>"
			. "<message>"
			. "<account>{$cfg['account']}</account>"
			. "<password>{$password}</password>"
			. "<msgid></msgid>"
			. "<phones>{$phone_number}</phones>"
			. "<content>{$msg}</content>"
			. "<sign></sign><subcode></subcode><sendtime></sendtime>"
			. "</message>";	
		
		if (!empty($cfg['url_send'])) {
			$url = $cfg['url_send'];
			//$url = $url . '?message=' . $message;
			//$ret = file_get_contents( $url ); 
			//如果用http_build_query,对方会解析错误，原因未知
			$ret = file_get_contents ( $url, false, stream_context_create(array('http' => array('method' => 'POST', 'content' => "message={$message}"))) );
			LOG::record($url);
			LOG::record($ret);
			$arr = xml_array($ret);
			return '0' == $arr['RESULT'];
		}
		
		return false;
	}
	

	

}