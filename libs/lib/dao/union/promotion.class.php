<?php
namespace Dao\Union;
use \Dao;
class Promotion extends Union {

    /**
     * @return Dao\Union\Promotion
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    public function search( $name = '') {
        $where = '';
        if ( !empty($name) ) $where = " WHERE `name` like '%{$name}%' OR `short_name` like '%{$name}%' ";
        $sql = "
            select
                *
            from
              {$this->_get_table_name()}
            {$where}
        ";
        return $this->query( $sql );
    }


    /**
     * 取推广列表
     * @return array
     */
    public function get_list( $state = 1, $fields = '`id`, `name`, `short_name`' ) {
        $state       = intval( $state );
        $where_state = !empty($state) ? $where_state = " AND state={$state}" : '';
        $table_promotion = $this->_get_table_name();
        $sql = "SELECT
                    {$fields}
        FROM {$table_promotion}
        WHERE
          short_name <> 'yqjsy' #邀请技术员排除
          {$where_state}
        ";

        $result = array();
        $list   = $this->query( $sql );

        foreach ($list as $item ) {
            $result[ $item['id'] ] = $item;
        }
        return $result;
    }



    public function software_list($state = 1, $fields = '`id`, `name`, `short_name`' ) {
        $state       = intval( $state );
        $where_state = !empty($state) ? $where_state = " AND state={$state}" : '';
        $table_promotion = $this->_get_table_name();
        $sql = "SELECT
          {$fields}
        FROM {$table_promotion}
        WHERE
          short_name <> 'yqjsy' and short_name <> 'ktwjf' and short_name <> 'winhomeqd' #邀请技术员排除
          and type < 6
          and app_type != 1
          {$where_state}
        ORDER BY
            sort desc,id desc
        ";

        $result = array();
        $list   = $this->query( $sql );
        foreach ($list as $item ) {
            $item['tag'] = !empty($item['tag']) ? explode(',', $item['tag']) : [];
            $result[ $item['id'] ] = $item;
        }
        return $result;
    }
    
    /**
     * @desc 
     * @return array
     */
    public function get_promotion_id($soft_id) {
        $table_promotion = $this->_get_table_name();
        $sql = "SELECT id,`name`,short_name FROM {$table_promotion}
        WHERE short_name='{$soft_id}'";
        $result   = $this->query( $sql );
        return $result;
    }

    public function get_promotion_name($where = null){
        $sql = "SELECT `name`,short_name FROM {$this->_get_table_name()}";
        if($where){
            $sql .= " where ".$where;
        }
        $result   = $this->query( $sql );
        $data = array();
        foreach($result as $v){
            $data[$v['short_name']] = $v['name'];
        }
        return $data;
    }




    /**
     * 用户推广app列表，附加申请与包分配信息
     * @param $uid
     * @return mixed
     */
    public function user_app_list( $uid ) {
        $uid = intval( $uid );
        $sql = "
SELECT
    p.*,
	apply.uid as applying,
	apply.dateline as apply_dateline,
	ao.org_id as assign_org_id,
	ao.org_app as assign_org_app
FROM
	promotion p
	left JOIN log_soft_apply apply on p.short_name=apply.`name` and apply.uid='{$uid}'
	left JOIN assign_orgid ao on p.short_name=ao.softID and ao.`status`=0 and ao.uid='{$uid}'
WHERE
	p.state=1
	and p.app_type=1
ORDER BY
  sort desc,id desc
";

        $list = $this->query( $sql );
        $dao_soft_apply = \Dao\Union\Log_Soft_Apply::get_instance();
        foreach ( $list as &$row ) {
            //前面排队人数
            $row['apply_order'] = !empty( $row['applying'] ) ? $dao_soft_apply->get_num_before( $row['soft_name'], $row['apply_dateline'] ) : 0;
        }
        return $list;
    }

    /**
     * 用户推广软件情况
     * @param $uid
     * @param $app_id
     * @return array
     */
    public function user_app_info( $uid, $app_id ) {
        $uid = intval( $uid );
        $sql = "
SELECT
    p.*,
	apply.uid as applying,
	apply.dateline as apply_dateline,
	ao.org_id as assign_org_id,
	ao.org_app as assign_org_app
FROM
	promotion p
	left JOIN log_soft_apply apply on p.short_name=apply.`name` and apply.uid='{$uid}'
	left JOIN assign_orgid ao on p.short_name=ao.softID and ao.`status`=0 and ao.uid='{$uid}'
WHERE
	p.state=1
	and p.app_type=1
	and p.id= {$app_id}
        ";

        $list = $this->query( $sql );
        $dao_soft_apply = \Dao\Union\Log_Soft_Apply::get_instance();
        foreach ( $list as &$row ) {
            //前面排队人数
            $row['apply_order'] = !empty( $row['applying'] ) ? $dao_soft_apply->get_num_before( $row['soft_name'], $row['apply_dateline'] ) : 0;
        }
        return !empty($list) ? $list[0] : [];
    }

    /**
     * 取app列表
     * @return mixed
     */
    public function app_list( ) {
        $sql = "
SELECT
    p.*
FROM
	promotion p
WHERE
	p.state=1
	and p.app_type=1
ORDER BY
    sort desc,id desc
";
        $list = $this->query( $sql );
        return $list;
    }

}
