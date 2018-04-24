<?php
namespace Util\Phone;

class Phone_Verify {
	
	protected $_cfg = array(
		'time_limit' => 60,//发送时间间隔限制,秒
		'expire'	 => 1800,//有效期,秒
		'day_limit'	 => 60,//每天发送次数限制

        'save_type'  => 'db',//验证码保存类型, db:数据库, session:session,默认db
	);
	/**
	 * @var Phone_Verify
	 */
	protected static $_instance = null;
	
	/**
	 * @param array $cfg
	 * @return Phone_Verify|null
	 */
	public static function instance( $cfg = array() ) {
		
		if (empty(self::$_instance ) ) {
			$pf = C('SMS_PLATFORM');//短信平台
			
			if (!empty($pf)) {
				$class_name = 'PhoneVerify_' . $pf;
//				import( '@.ORG.Phone.' . $class_name );//手机验证
                $path_base = dirname(__FILE__) . '/';
                require_once ($path_base . $class_name . '.class.php');
                
				self::$_instance = new $class_name( $cfg );
			}
			else {
				self::$_instance = new self( $cfg );
			}

			
			
		}
		return self::$_instance;
	}
		
	public function PhoneVerify($cfg = array()) {
		if (!empty($cfg)) $this->_cfg = array_merge($this->_cfg, $cfg);
//        if ( 'session' == $this->_cfg['save_type'] ) session('phone_verify', array());
	}

	/**
	 * 取用户短信发送cd
	 * @param integer $uid 用户id
     * @return integer
	 */
	public function cd_get($uid) {
        $time_limit = $this->_cfg['time_limit'];
        $dateline = time();
        if ($this->_is_session()){
            $diff = $dateline - intval(session('phone_verify_dateline'));
            if (session('phone_verify_used')) return 0;
        }
        else {
            if (empty($uid)) return 0;
            $dateline_begin = strtotime(date('Y-m-d'));
            $where = array('uid' => $uid, 'dateline' => array('gt', $dateline_begin) , 'used' => 0 );
            $last = M('phone_code')->where($where)->order('id DESC')->find();
            if (empty($last)) return 0;
            $diff = $dateline - intval($last['dateline']);
        }
		return  $diff < $time_limit ? ($time_limit - $diff ) : 0;
	}
	
	/**
	 * 获取今天已经发送的次数
	 * @param integer $uid 用户id
	 * @return integer
	 */
	public function day_count($uid) {
        if ($this->_is_session()) return intval(session('phone_verify_day_count'));

		$dateline_begin = strtotime(date('Y-m-d'));
		$where = array('uid' => $uid, 'dateline' => array('gt', $dateline_begin) );
		return  M('phone_code')->where( $where )->count();
	}
	
	/**
	 * 获取上一次发送验证码的时间戳
	 * @param integer $uid 用户id
	 * @return integer
	 */
	public function last_send_dateline($uid) {
        if ($this->_is_session()) return session('phone_verify_dateline');

		$where = array('uid' => $uid, 'dateline' => array('gt', time() - $this->_cfg['expire']));
		$data = M('phone_code')->where( $where  )->order('id DESC')->find();
		return !empty($data) ? $data['dateline'] : 0;
	}
	
	/**
	 * 发送手机验证码
	 * @param integer $uid 用户id
	 * @param integer $phone_number 手机号码
	 * @param string $msg 验证码的短信内容，code做占位符,如:你的验证码：code,则实际发送短信内容为:你的验证码:115376
	 * @param integer $type 类型
     * @return boolean
	 */
	public function send($uid, $phone_number, $msg = '你的验证码：code', $type = 0) {
        $code = $this->_verify_code_make();
        $dateline = time();
        
        $code = $this->_verify_code_make();
        $msg = str_replace('_code', $code, $msg);
        $_SESSION['mcode'] = md5($phone_number.$code);
        if ($this->_is_session()) {
            if (empty($phone_number)) return false;
            session('phone_verify_code', $code);
            session('phone_verify_dateline', $dateline);
            session('phone_verify_used', false);
            session('phone_verify_number', $phone_number);
            $count = intval(session('phone_verify_day_count') );
            session('phone_verify_day_count', ++$count);
        }
        else {
            if (empty($uid) || empty($phone_number)) return false;
            M('phone_code')->add(array(
                'uid' => $uid,
                'type'=>$type,
                'code' => $code,
                'phone_number' => $phone_number,
                'dateline' => $dateline,
                'use' => 0
            ));
        }
        
        M('test_sms_log')->add(array(
        'uid' => $uid,
        'phone_number' => $phone_number,
        'dateline' => $dateline,
        'msg' => $msg,
        ));

        return $this->_send($phone_number, $msg);
	}
	
	/**
	 * 验证码是否正确
	 * @param integer $uid 用户id
	 * @param string $code 验证码
	 * @param string $phone_number 验证手机号，默认空，不检查手机号
     * @return boolean
	 */
	public function verify($uid, $code, $phone_number = '') {

        if ($this->_is_session()) {
            if ( !session('phone_verify_code') || $code != session('phone_verify_code') ) return false;
            if ( !empty($phone_number) && $phone_number !=  session('phone_verify_number') ) return false;
            if (time() - session('phone_verify_dateline') > $this->_cfg['expire']) return false;
            return true;
        }
        else {
            if (empty($uid) || empty($code)) return false;
            $where = array('uid' => $uid, 'dateline' => array('gt', time() - 1800));
            $data = M('phone_code')->where( $where  )->order('id DESC')->find();
            if ($data['code'] != $code ) return false;
            return !empty($phone_number) ? (!empty($data) && $data['phone_number'] == $phone_number) : !empty($data);
        }

	}

    /**
     * 将验证码置为已使用状态
     * @param $uid
     * @param $code
     */
    public function used($uid, $code) {
        if ($this->_is_session()) {
            session('phone_verify_is_use', 1);
        }
        else {
            M('phone_code')->where(array('uid' => $uid, 'code' => $code))->save(array('used' => 1));
        }
    }

	/**
	 * 清空验证码
	 * @param integer $uid
     * @return boolean
	 */
	public function verify_code_clear($uid ) {
		return M('phone_code')->where(array('uid' => $uid))->delete();
	}
	
	protected function _send($phone_number, $msg) {
		return true;
		$cfg_sms = C('SMS');
		if (!empty($cfg_sms['URL_SEND'])) {
			$arr = array(
							'_account' => $cfg_sms['ACCOUNT'],
							'_password' => strtolower( md5($cfg_sms['PASSWORD']) ),
							'_phone' => $phone_number,
							'_content' => $msg,
 							'_sendtime' => date('YmdHi')
			);
			$content = <<<eot
<?xml version="1.0" encoding="UTF-8"?>
<message>
			<account>_account</account>
			<password>_password</password>
			<msgid></msgid>
			<phones>_phone</phones>
			<content>_content</content>
			<sign></sign>
			<sendtime>_sendtime</sendtime>
</message>							
eot;
			
			$url = $cfg_sms['URL_SEND'];
			$params = array('message' => str_replace(array_keys($arr), array_values($arr), $content));
			$ret = file_get_contents ( $url, false, stream_context_create(array('http' => array('method' => 'POST', 'content' => http_build_query($params, '&')))) );
			LOG::record($ret);
			$arr = xml_array($ret);
			return '0' == $arr['RESULT'];
		}

		return false;
	}

    /**
     * 判断当前是否使用session来存储
     */
    protected function _is_session() {
        return $this->_cfg['save_type'] == 'session';
    }

	/**
	 * 生成验证码
	 * @return string
	 */
	protected  function _verify_code_make() {
        $num = 6;
		$ret = '';
		while ($num--) $ret .= mt_rand($num > 5 ? 1 :0, 9);
		return $ret;		
	}
	

}