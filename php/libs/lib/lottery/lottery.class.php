<?php
namespace Lottery;
class lottery {
	private static $_instance = null;
    public $_errno;
    public $_error;
    const LOTTERY_VIOLENCE_BORDER = 1000000;
    private $_lottery_packages = [];
    private $_lottery_id2info_map =[];
    private $_default_package_info =[];//默认礼包

	public static function get_instance() {
		if (!isset(self::$_instance)) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}

    public function getErrorInfo(){
        return array('errno'=>$this->_errno, 'error'=>$this->_error);
    }

	public function getLottery($rid){
        if(!$rid){
            return false;
        }
        if($this->_lottery_packages) return $this->_lottery_packages;
        $this->_lottery_packages = \Dao\Union\Act_Mod_Lottery_Package::get_instance()->getLotteryPackage($rid);
        if ($this->_lottery_packages){
            foreach ($this->_lottery_packages as $v){
               $this->_lottery_id2info_map[$v['pid']] = $v;
               if($v['default'] == 1) $this->_default_package_info = $v;
            }
        }
		return $this->_lottery_packages;
	}

    /**
     *  获取获取礼包
     * @param $pid
     * @return mixed
     */
    public function getPackage($pid){
        if ($this->_lottery_id2info_map[$pid]) return $this->_lottery_id2info_map[$pid];
    }
	
	/**
	* 用户执行一次抽奖
	* @param  int $rid 方案ID
	* @param  int $uid
	*/
	public function lotteryDraw($rid, $uid){
		if(!$rid || !$uid){
			$this->_errno = -1;
			$this->_error = '用户执行一次抽奖，错误的参数出传入！';
			return false;
		}
				
		//检查抽奖方案的配置条件
		$ret = $this->_checkLottery($rid);
		if (!$ret) return $ret;

		//判断用户是否中奖
		$pid = $this->determineWinning($rid);
		if(empty($pid)){
            if ($this->_default_package_info){
                return $this->_default_package_info;
            }
			$this->_errno = -3;
			$this->_error = '用户'. $uid . '未抽中奖品！';
			return [];
		}
		$package_info = $this->_lottery_id2info_map[$pid];
        if($package_info['quantity'] < 1){
            if ($this->_default_package_info){
                return $this->_default_package_info;
            }
            $this->_errno = -3;
            $this->_error = '奖励 '.$pid.' 没有库存！';
            return [];
        }
		return $package_info;
	}

	/**
	 * 检查抽奖方案的配置条件
	 * @param  string $rid 方案ID
	 * @return boolean
	 */
	private function _checkLottery($rid) {
		return true;
	}


	/**
	* 判断是否中奖
	*/
	public function determineWinning($rid){
		if(!$rid){
			return false;
		}
		//依据中奖边际产生一个随机数
		$rand = $this->getWinningRand();
		//获取边际列表
		$scope = $this->getScope($rid);
		if(empty($scope) || !is_array($scope)){
			return false;
		}
		foreach($scope as $val){
			if($rand >= $val['min'] && $rand <= $val['max']){
				return $val['pid'];
				break;
			}
		}
		return false;		
	}
	
	//依据中奖边际产生一个随机数 
	private function getWinningRand(){
		$rand = mt_rand(1,self::LOTTERY_VIOLENCE_BORDER);
		return $rand;
	}
	
	//将奖品依据当前爆率生成一个范围边际
	private function getScope($rid){
		if(!$rid){
			return false;
		}
		$PrizeList = $this->getLottery($rid);
		if(empty($PrizeList) || !is_array($PrizeList)){
			return false;
		}
		$retList = array();
		foreach($PrizeList as $ks=>$vs){
			if(empty($vs)){
				continue;
			}
			$retList[] = array('pid'=>$vs['pid'],'rate'=>$vs['rate']);
		}
		if(empty($retList)){
			return false;
		}
		//将数组排序
		usort($retList, array($this,"sortWinningList"));
		$ret = array();
		foreach($retList as $ks=>$vs){
			if(empty($vs)){
				continue;
			}
			$pid = $vs['pid'];
			$win_rate = intval($vs['rate']);	
			if($win_rate == 0){
				continue;
			}
			if(empty($ret)){
				$ret[] = array('pid'=>$pid,'min'=>1,'max'=>$win_rate);
			}else{
				$wincount = count($ret);
				$tmpData = $ret[$wincount-1];
				$min = $tmpData['max']+1;
				$max = $min+$win_rate-1;
				$ret[] = array('pid'=>$pid,'min'=>$min,'max'=>$max);
			}
		}
		return $ret;
	}
	
	//读取一个奖品的当前爆率
    private function getPackageRate($pid){
		if(!$pid) return false;
		return $this->_lottery_id2info_map[$pid]['rate'] ? $this->_lottery_id2info_map[$pid]['rate'] : false;
	}
	
	//依据中奖爆率对边际做排序
	private function sortWinningList($ar1,$ar2){
		if ($ar1['rate'] == $ar2['rate']) return 0;
		return ($ar1['rate'] > $ar2['rate']) ? -1 : +1;
	}
}