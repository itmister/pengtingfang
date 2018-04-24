<?php
namespace Util\Phone;
class PhoneVerify_dh8801 extends Phone_Verify {

	public function __construct($cfg = array()) {
		$cfg = array_merge($cfg, array(
			'account' => 'dh8801',
			'password' => '8801.com',
			'userid' => 706,
			'url_send' => 'http://115.29.170.211:8085/sms.aspx'
		));
		parent::__construct($cfg);
	}
	
	protected function _send($phone_number, $msg) {
		$cfg = $this->_cfg;
		if (!empty($cfg['url_send'])) {
			$params = array(
							'userid' => $cfg['userid'],
							'account' => $cfg['account'],
							'password' => $cfg['password'],
							'mobile' => $phone_number,
							'content' => $msg,
 							'sendTime' => '',
							'action' => 'send',
							'extno' => ''
			);
			
			$url = $cfg['url_send'] . '?' . http_build_query($params);
		
			//$ret = file_get_contents ( $url, false, stream_context_create(array('http' => array('method' => 'POST', 'content' => http_build_query($params, '&')))) );
			$url = auto_charset($url);
			$ret = file_get_contents($url);
			LOG::record($url);
			LOG::record($ret);
			$arr = xml_array($ret);
			return 'Success' == $arr['RETURNSTATUS'];
		}

		return false;
	}
	

	

}