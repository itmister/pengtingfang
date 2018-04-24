<?php
namespace Union;

/**
 * 推广软件管理
 * Class promotion
 * @package Union
 */

class Promotion {
    //申请状态
    const APPLY_STATUS_DEFAULT  = 0;//没有申请
    const APPLY_STATUS_APPLYING = 1;//申请中
    const APPLY_ASSIGNED        = 2;//已分配

    protected static $_instance = null;

    /**
     * @return \Union\Promotion
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self();
        }
        return self::$_instance;
    }

    /**
     * 取推广列表
     * @return array
     */
    public function get_list( $state = 1, $fields = '`id`, `name`, `short_name`', $key_field = 'id' ) {
        //$list = M('promotion')->select();
        $list = \Dao\Union\Promotion::get_instance()->get_list($state, $fields, $key_field );
        foreach ($list as $item ) if (isset($item[$key_field])) $result[$item[$key_field]] = $item;
        return $result;
    }

    /**
     * 取推广软件列表,不包括导航
     * @param int $state
     */
    public function software_list($state = 1, $fields = '`id`, `name`, `short_name`', $key_field = 'id'){
        $list = \Dao\Union\Promotion::get_instance()->software_list($state, $fields);
        $result = [];
        foreach ($list as $item ) if (isset($item[$key_field])) $result[$item[$key_field]] = $item;
        return $result;
    }

    /**
     * 取某一个推广软件信息
     * @param string $short_name 标识名,空则使用软件id检索，
     * @param int $id 软件id 使用软件id检索，不建议
     * @return array
        id : 软件id
        name : 名称
        template_type :
        short_name : 标识名
     */
    public function get_info( $short_name = '', $id = 0 ) {
        if ( empty($short_name) && empty($id) ) return false;
        $where  = !empty( $short_name ) ? " short_name = '{$short_name}' " : " id='{$id}' ";
        $data   = \Dao\Union\Promotion::get_instance()->get_row( $where );
        return $data;

    }

    /**
     * 取推广软件列表
     */
    public function get_software_list() {
        static $software_list;
        if (empty($software_list)) $software_list = $this->get_list();
        return $software_list;
    }

    /**
     * 取app列表
     */
    public function user_app_list( $uid = 0 ) {

        if (!empty(($uid))) {
            $row_list =  \Dao\Union\Promotion::get_instance()->user_app_list( $uid );
            foreach ( $row_list as &$row ) {
//            $row['assign_org_app'] = 'http://www.baidu.com/';//@todo debug
                if ( !empty($row['assign_org_app']) ) {
                    //已分配
                    $row['apply_status'] = self::APPLY_ASSIGNED;
                }
                else if ( !empty($row['applying'])) {
                    //申请中
                    $row['apply_status'] = self::APPLY_STATUS_APPLYING;
                }
                else {
                    //没申请
                    $row['apply_status'] = self::APPLY_STATUS_DEFAULT;
                }

            }
        }
        else {
            $row_list = \Dao\Union\Promotion::get_instance()->app_list();
        }

        return $row_list;
    }

    /**
     * 根据软件id取软件标识
     */
    public function get_software_by_id( $id ) {
        static $list = null;
        if ( empty($list) ) foreach ( \Dao\Union\Promotion::get_instance()->all() as $row ) $list[$row['id']] = $row['short_name'];
        return isset($list[$id]) ? $list[$id] : '';
    }
}