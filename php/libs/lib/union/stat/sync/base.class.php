<?php
namespace Union\Stat\Sync;

/**
 * 统计- 同步基类
 * Class Base
 * @package Union\Stat\Sync
 */

class Base {

    /**
     * 指定年月日范围更新
     * @param $ymd_start
     * @param $ymd_end
     */
    public function ymd( $ymd_start, $ymd_end ) {
        $this->_base_sync();
        \Dao\Stat\User_base::get_instance()->sync_all();//@todo user暂时无法做到增量同步，暂全同步
        \Dao\Stat\Base\Performance::get_instance()->sync_ymd( $ymd_start, $ymd_end );
    }

    public function ym( $ym ) {
        $this->_base_sync();
    }

    public function uid( $uid ) {
        $this->_base_sync();

    }

    public function all() {

        $this->_base_sync();
        \Dao\Stat\User_base::get_instance()->sync_all();
        \Dao\Stat\Base\Performance::get_instance()->sync_all();

    }

    /**
     * 基础表信息同步更新
     */
    private function _base_sync() {
        \Dao\Stat\Base\Promotion::get_instance()->sync_all();
        \Dao\Stat\Manager\Working::get_instance()->sync_all();//@todo manager_woking 同步节点待调整
        \Dao\Stat\Manager\Director::get_instance()->sync_all();
    }
}