<?php
namespace Dao\Union;
use Dao;

/**
 *  微信红包
 * Class Act_weixin_hongbao
 * @package Dao\Union
 */
class Act_weixin_hongbao extends Union {
    use Dao\Orm;

    /**
     * @return Dao\Union\Act_weixin_hongbao
     */
    public static function get_instance(){
        return parent::get_instance();
    }


    public function info( $uid ) {
        if (empty($uid)) return [];
        $info = $this->get_row(['uid' => $uid]);
        if (empty($info)) {
            $this->add([
                'uid'           => $uid,
            ]);
            $info = $this->get_row(['uid' => $uid]);

        }
        return $info;

    }

    /**
     * @param $uid
     * @param $money_get
     * @param int $get_type
     * @return bool
     * @throws \Exception
     */
    public function get( $uid, $money_get, $get_type = 1 ) {

        $arr_up = [
            'money' => $money_get,
            'is_get' => 1,
            'datetime_get' => date('Y-m-d H:i:s'),
            'get_type' => $get_type
        ];
        return $this->update( ['uid' => $uid, 'is_get' => 0 ], $arr_up  );

    }
}