<?php
namespace Dao;
class Dao {

    /**
     * 数据库连接配置key
     * @var string
     */
    protected $_connection_key = '';

    /**
     * 数据表前缀
     * @var string
     */
    protected $_prefix = '';

    /**
     * 实际表名
     * @var string
     */
    protected $_realTableName = '';


    protected static $_instance_list = [];

    public static function get_instance(){
        $class_now = get_called_class();
        return empty(self::$_instance_list[$class_now])
            ? ( self::$_instance_list[$class_now] = new $class_now($class_now) ) : self::$_instance_list[$class_now];
    }

    /**
     * class_name 命名规范\dao\库名\表名
     * @param $class_name
     */
    public function __construct($class_name) {
        $arr = explode('\\', strtolower( $class_name) );
        $arr = array_slice($arr, 2);//\dao\库名\表名
        $tableName = implode('_', $arr);
        $this->_realTableName = $this->_prefix . $tableName;
    }

//    public function __construct( $className ) {
//        $arr = explode('\\', $className);
//        $tableName = strtolower( array_pop($arr) );
//        $this->_realTableName = $this->_prefix . $tableName;
//    }

    /**
     * 清空所有数据
     */
    public function delete_all($table_name = '') {
        $sql = "truncate table " . $this->_get_table_name($table_name);
        return $this->query( $sql );
    }

    /**
     * 删除表 danger
     * @param string $table_name
     * @return bool|int|string
     */
    public function drop( $table_name = '' ) {
        $table_name = empty($table_name) ? $this->_get_table_name() : $table_name;
        $sql = "drop table if exists `{$table_name}`";
        return $this->exec( $sql );
    }

    /**
     * 增加一条记录
     */
    public function add( $data, $replace = false, $table_name = '', $update_on_duplicate = false) {
        if (empty($data)) return false;
        $values = $this->_field_to_str( array_values($data) );
        $fields = $this->_field_to_str( array_keys($data), '`' );
        $tableName = empty($table_name) ?  $this->_get_table_name() : $table_name;
        $insert_type = empty($replace) ? 'INSERT' : 'REPLACE';
        $sql = "{$insert_type} INTO {$tableName} ({$fields}) values ({$values})";
        if (!$replace && $update_on_duplicate) {
            //INSERT INTO TABLE (a,c) VALUES (1,3),(1,7) ON DUPLICATE KEY UPDATE c=c+1;
            $arr_update = [];
            foreach ($data as $field => $value ) $arr_update[] = "`{$field}`='{$value}'";
            $str_update = " ON DUPLICATE KEY UPDATE " . implode(',', $arr_update);
            $sql .= $str_update;
        }
        //echo $sql.'<br>';
        $oDb = $this->db();
        $result = $oDb->query( $sql );
        if ($this->get_error()) return false;
        $last_insert_id =  $oDb->lastInsertId();
        if (!empty($last_insert_id)) return $last_insert_id;

        if (!$replace && $update_on_duplicate) return $oDb->affected_rows();

        return true;
    }


    public function affected_rows() {
        return $this->db()->affected_rows();
    }

    /**
     * 关闭索引
     * @param string $table_name
     * @return mixed
     */
    public function disable_keys( $table_name = '' ) {
        $table_name = $this->_get_table_name( $table_name );
        $sql = "ALTER TABLE {$table_name} DISABLE KEYS";
        return $this->query( $sql );
    }

    /**
     * 开启索引
     * @param string $table_name
     * @return mixed
     */
    public function enable_keys ( $table_name = '' ) {
        $table_name = $this->_get_table_name( $table_name );
        $sql = "ALTER TABLE {$table_name} ENABLE KEYS";
        return $this->query( $sql );
    }


    /**
     * 更新记录
     * @param $where
     * @param $arr_data
     * @param string $table_name
     * @return boolean
     */
    public function update( $where, $arr_data, $table_name = '') {
        if (empty($where) || empty($arr_data) || !is_array( $arr_data)) return false;
        if ( empty($table_name ) ) $table_name = $this->_get_table_name();
        $arr_set = array();
        foreach ( $arr_data as $field => $value ) {
            if($value !== NULL){
                $arr_set[] = "`$field`='{$value}'";
            }else{
                $arr_set[] = "`$field`=NULL";
            }
        }
        $str_set = implode(',', $arr_set);
        $where = $this->_parse_where( $where );
        $sql = "UPDATE {$table_name} SET {$str_set} {$where}";
        ////INSERT INTO TABLE (a,c) VALUES (1,3),(1,7) ON DUPLICATE KEY UPDATE c=c+1;
        $ret = $this->db()->query($sql);
        if ($ret){
            return $this->db()->affected_rows();
        }else{
            return false;
        }
    }

    public function exec($sql){
        $ret = $this->db()->query($sql);
        if ($ret){
            return $this->db()->lastInsertId() ?$this->db()->lastInsertId():$this->db()->affected_rows();
        }else{
            return false;
        }
    }

    /**
     * 批量增加
     * @param $data
     * @param boolean $replace 是否替换
     * @return bool|\mysqli_result
     */
    public function add_all( $data, $replace = false ) {
        if (empty($data)) return false;
        $first = current($data);
        $arrFields = array_keys($first);
        $fields = $this->_field_to_str( $arrFields , '`');
        $arrValues = array();
        foreach ( $data as $row ) {
            $arrValues[] = $this->_field_to_str( $row );
        }
        $values = implode('),(', $arrValues);
        $tableName = $this->_realTableName;
        $insert_type = empty($replace) ? 'INSERT' : 'REPLACE';
        $sql = "{$insert_type} INTO {$tableName} ({$fields}) values ({$values});";
        return $this->db()->query( $sql );
    }

    /**
     * 批量增加，主键冲突时更新
     * @param $data
     * @return bool
     */
    public function add_all_duplicate_update( $data, $update_field ) {
        if ( empty( $data) ) return false;
        $first = reset( $data );
        $arr_fields = array_keys( $first );
        $fields = $this->_field_to_str( $arr_fields , '`');
        $arr_values = array();
        foreach ( $data as $row ) {
            $arr_values[] = $this->_field_to_str( $row );
        }
        $values = implode('),(', $arr_values);
        $table_name = $this->_get_table_name();

        $update_duplicate = '';
        if ( !empty($update_field) && is_array( $update_field) ) {
            $arr_update = [];
            foreach ( $update_field as $item ) $arr_update[] = " `{$item}`=values(`{$item}`) ";
            $update_duplicate = " on duplicate key update " . implode(',', $arr_update);
        }
        $sql = "INSERT INTO {$table_name} ({$fields}) values ({$values}) {$update_duplicate} ;";
        $this->db()->query( $sql );
        return $this->db()->lastInsertId() ? $this->db()->lastInsertId() : $this->db()->affected_rows();
    }


    /**
     * 检查主键记录是否存在，如果不存在添加一条主键为$value的空记录
     * @param $id_field
     * @param $value
     * @return boolean
     */
    public function add_if_not_exist( $id_field, $value ) {
        $id_field = trim($id_field);
        if (empty($value) || empty($id_field)) return false;
        $table_name = $this->_get_table_name();
        $sql        = "SELECT {$id_field} FROM {$table_name} WHERE {$id_field}='{$value}' LIMIT 1";
        $arr_data   = $this->query( $sql );
        $arr        = current( $arr_data );
        if (empty($arr) || empty($arr[$id_field]) ) {
            $this->add(array(
                $id_field => $value
            ));
        }
        return true;
    }

    /**
     * 删除记录
     * @param string $where
     * @param boolean $check_where 是否检查$where
     * @return false
     */
    public function delete( $where = '', $check_where = true) {
        if ( empty($where) && $check_where ) return false;
        $table_name = $this->_get_table_name();
        $where = $this->_parse_where( $where );
        $sql = "DELETE FROM {$table_name} {$where} ";
//        return $this->db()->query($sql);
        return $this->exec( $sql );
    }

    /**
     * 替换增加
     * @param array $arr_data
     * @return boolean
     */
    public function replace_all( $arr_data ) {
        return $this->add_all( $arr_data, true );
    }

    /**
     * 取数据库连接类
     * @return Db
     */
    public function db() {
        return Db::instance( $this->_connection_key );
    }



    /**
     * 取错误信息
     * @return string
     */
    public function get_error() {
        return $this->db()->get_error();
    }

    /**
     * 取最后执行的sql
     * @return mixed
     */
    public function get_last_sql() {
        return $this->db()->get_last_sql();
    }

    /**
     * 判断记录是否存在
     * @param string $where 条件如: id=83
     * @return boolean
     */
    public function is_exist( $where ) {
        $table_name = $this->_get_table_name();
        $where      = $this->_parse_where( $where );
        $sql        = "SELECT * FROM {$table_name} {$where} LIMIT 1";
        $arr_data   = $this->query( $sql );
        return !empty($arr_data) ? true : false;
    }

    /**
     * 取一行记录
     * @param $where
     * @return array
     */
    public function get_row( $where, $fields = "*" ) {
        $where = $this->_parse_where( $where );
        $table_name = $this->_get_table_name();
        $sql        = "SELECT
                        {$fields}
                      FROM
                        {$table_name}
                        {$where}
                    LIMIT 1";

        $arr_data   = $this->query( $sql );
        return !empty($arr_data) ? current( $arr_data ) : array();

    }

    /**
     * 取一个字段值
     * @param string $field 字段名
     * @param string $where
     * @return mix
     */
    public function get_one( $field, $where ) {

        $table_name = $this->_get_table_name();
        $sql        = "SELECT
                        {$field}
                      FROM
                        {$table_name}
                      WHERE
                        {$where}
                    LIMIT 1";

        $arr_data   = $this->query( $sql );
        return ( !empty($arr_data) && is_array( $arr_data ) ) ? $arr_data[0][$field] : null;

    }

    /**
     * 执行查询 返回数据集
     * @access public
     * @param string $str  sql指令
     * @return mixed
     */
    public function query($str) {
//        $this->db()->query( $str );
        return $this->db()->getAll($str);
    }

    /**
     * yield迭代返回结果集
     * @param $sql
     */
    public function yield_result( $sql ) {
        foreach ( $this->db()->yield_result( $sql) as $item ) yield $item;
    }


    public function begin_transaction(){
         $this->db()->begin_transaction();
    }

    public function commit(){
         $this->db()->commit();
    }

    public function rollback(){
         $this->db()->rollback();
    }

    /**
     * 按页取
     * @param $sql
     * @param $sql_total
     * @param int $page_now
     * @param $num
     * @return array
     */
    public function page_get($sql, $sql_total, $start = 0, $num = 10) {

        $start   = intval($start);
        $num        = intval($num);
//        preg_replace('/LIMIT.+d+/', " LIMIT {$start},{$num} ");
        $sql        .= " LIMIT {$start}, {$num}";
        $sql_query  = "{$sql_total};{$sql}";
        $result = $this->db()->query_multi("{$sql_total};{$sql}");
        return [
            'total' => !empty( $result[0][0] ) ? array_pop( $result[0][0] ) : 0,
            'list'  => !empty( $result[1] ) ? $result[1] : []
        ];
    }

    /*
     * 取表名，如果有前缀配置，自动加前缀,如果配置了$_realTableName属性则取此属性值,否则取类名小写
     * @param string $table_name 表名
     * @param array $arr_cfg 数据库配置,如果设置了此项将添加库名，如channel_7654.`user`
     */
    protected function _get_table_name( $table_name = '' , $arr_cfg = array() ) {
        if ( empty($table_name) ) {
            if (!empty($this->_realTableName)) {
                $table_name = $this->_realTableName;
                return $table_name;
            }
        }
        if (empty($table_name)) return '';
        if (empty($arr_cfg)) return $this->_prefix . $table_name;
        if ( !is_array( $arr_cfg ) || empty($arr_cfg['DB_NAME'])) return $table_name;
        return "`{$arr_cfg['DB_NAME']}`.`{$arr_cfg['DB_PREFIX']}{$table_name}`";
    }

    /**
     * 取出表所有数据
     * @param string $key_field
     */
    public function all( $key_field = null) {
        $sql = "
            select
                *
            from
              {$this->_get_table_name()}
        ";
        $data = $this->query( $sql );

        $result = [];
        if (!empty($id_field)) {
            foreach ($data as $row ) $result[$row[$key_field]] = $row;
            return $result;
        }
        else {
            $result = $data;
        }

        return $result;
    }

    /**
     * 取升降序,默认降序
     * @param $asc
     * @return string
     */
    protected function _get_asc_flag( $asc ) {
        if ( is_string($asc) ) {
            $asc = strtoupper(trim($asc));
            if (!in_array($asc, array('ASC', 'DESC'))) return 'DESC';
            return $asc;
        }
        $asc = intval($asc);
        return 0 == $asc ? 'DESC' : 'ASC';
    }

    /**
     * 填充序号
     * @param $arr_data
     * @param $row_start 开始行数
     */
    protected function _fill_idx( &$arr_data, $row_start ) {
        if (empty($arr_data)) return false;
        $i=0;
        foreach (array_keys($arr_data) as $key) {
            $i++;
            $arr_data[$key]['idx'] = $row_start + $i;
        }
    }

    /**
     * 字段数组转换成字符串
     * @param $arr
     * @param string $quote
     * @return string
     *
     */
    protected function _field_to_str($arr, $quote = '\'') {
        return $quote . implode($quote . ',' . $quote, $arr) . $quote;
    }
    
    /**
     * 查询列表
     * @param array $params
     * @return \Dao\mixed
     */
    public function select($params){
        extract($params);
        if(!$field){
            $field = "*";
        }
        
        $sql ="SELECT {$field} FROM {$this->_get_table_name()}";
        if($where){
            $sql .= " WHERE {$where}";
        }
        
        if($groupby){
            $sql .= " GROUP BY {$groupby}";
        }
        
        if($having){
            $sql .= " HAVING {$having}";
        }
        
        if($orderby){
            $sql .= " ORDER BY {$orderby}";
        }
        if($limit){
            $sql .=" LIMIT {$limit}";
        }
        $result = $this->query($sql);
        return $result;
    }
    
    /**
     * 查询单条记录
     * @param array $params
     * @return \Dao\mixed
     */
    public function find($params){
        extract($params);
        if(!$field){
            $field = "*";
        }
    
        $sql ="SELECT {$field} FROM {$this->_get_table_name()}";
        if($where){
            $sql .= " WHERE {$where}";
        }
    
        if($groupby){
            $sql .= " GROUP BY {$groupby}";
        }
    
        if($having){
            $sql .= " HAVING {$having}";
        }
    
        if($orderby){
            $sql .= " ORDER BY {$orderby}";
        }
        $sql .=" LIMIT 0,1";
        
        $result = $this->query($sql);
        return !empty($result) ? current( $result ) : array();
    }

    /**
     * 统计
     * @param array $params
     * @return Ambigous <number, mixed>
     */
    public function count($params){
        extract($params);
        $field = $field ? $field : "*";
        
        $sql ="SELECT COUNT({$field}) AS num FROM {$this->_get_table_name()}";
        if($where){
            $sql .=" WHERE ".$where;
        }
        $result = current($this->query($sql));
        return $result['num'] ? $result['num'] : 0;
    }

    /**
     * 取数量总计
     * @param string $where
     */
    public function total( $where = '' ) {
        $where = $this->_parse_where( $where );
        $sql ="SELECT COUNT(*) AS num FROM {$this->_get_table_name()} {$where}";
        return $this->query( $sql )[0]['num'];
    }
    
    /**
     * 批量增加
     * @param array $data    数据
     * @param string replace 是否替换
     * @param string $table  自定义表
     * @return boolean
     */
    public function addAll( $data, $replace = false ,$table = false,$update = false) {
        if (empty($data)) return false;
        $first = current($data);
        $arrFields = array_keys($first);
        $fields = $this->_field_to_str( $arrFields , '`');
        $arrValues = array();
        foreach ( $data as $row ) {
            $arrValues[] = $this->_field_to_str( $row );
        }
        $values = implode('),(', $arrValues);
        $tableName = $table ? $table : $this->_realTableName;
    
        $replace= (is_numeric($replace) && $replace>0)?true:$replace;
        $sql = (true === $replace ? 'REPLACE' : 'INSERT')." INTO {$tableName} ({$fields}) values ({$values})".$this->parse_duplicate($replace,$update);
        return $this->db()->query( $sql );
    }

    /**
     * 显示建表ddl
     */
    public function show_create() {
        $sql = "show create table ". $this->_get_table_name();
        return $this->query( $sql )[0]['Create Table'];
    }

    /**
     * 增加字段值
     * @param $where
     * @param $field
     * @param $value
     * @return bool|int|string
     */
    public function set_inc($where, $field, $value ) {
        $where = $this->_parse_where($where);
        $table = $this->_get_table_name();
        $value = intval( $value );
        $sql = " update {$table} set `{$field}`=`{$field}`+{$value} {$where}";
        return $this->exec( $sql );
    }
    
    /**
     * 减少字段值
     * @param $where
     * @param $field
     * @param $value
     * @return bool|int|string
     */
    public function set_dec($where, $field, $value ) {
        $where = $this->_parse_where($where);
        $table = $this->_get_table_name();
        $value = intval( $value );
        $sql = " update {$table} set `{$field}`=`{$field}`-{$value} {$where}";
        return $this->exec( $sql );
    }

    /**
     * 主键冲突丢弃批量增加
     * @param $data
     * @param boolean $replace 是否替换
     * @return bool|\mysqli_result
     */
    public function ignore_add_all( $data, $replace = false ) {
        if (empty($data)) return false;
        $first = current($data);
        $arrFields = array_keys($first);
        $fields = $this->_field_to_str( $arrFields , '`');
        $arrValues = array();
        foreach ( $data as $row ) {
            $arrValues[] = $this->_field_to_str( $row );
        }
        $values = implode('),(', $arrValues);
        $tableName = $this->_realTableName;
        $insert_type = empty($replace) ? 'INSERT' : 'REPLACE';
        $sql = "{$insert_type} ignore INTO {$tableName} ({$fields}) values ({$values});";
        return $this->db()->query($sql);
    }

    /**
     * 取下一条记录
     * @param $where
     * @param string $field_compare
     * @param integer $value
     * @param string $fields
     * @return array
     */
    public function row_next( $where, $field_compare, $value, $fields = '*' ) {
        $where = $this->_parse_where( $where );
        $_where = " {$field_compare} > {$value} ";
        $where = !empty($where) ? ( $where . ' AND ' . $_where) : " WHERE {$_where}";
        $sql = "
            SELECT
                {$fields}
            FROM
               {$this->_get_table_name()}
            {$where}
            ORDER BY
               {$field_compare}
            LIMIT 1
        ";
        $list  = $this->query( $sql );
        return !empty($list) ? $list[0] : [];
    }

    /**
     * 取前一条记录
     * @param $where
     * @param string $field_compare
     * @param integer $value
     * @param string $fields
     * @return array
     */
    public function row_prev( $where, $field_compare, $value, $fields = '*' ) {
        $where = $this->_parse_where( $where );
        $_where = " {$field_compare} < {$value} ";
        $where = !empty($where) ? ( $where . ' AND ' . $_where) : " WHERE {$_where}";
        $sql = "
            SELECT
                {$fields}
            FROM
               {$this->_get_table_name()}
            {$where}
            ORDER BY
               {$field_compare} desc
            LIMIT 1
        ";
        $list  = $this->query( $sql );
        return !empty($list) ? $list[0] : [];
    }


    /**
     * ON DUPLICATE KEY UPDATE 分析
     * @access protected
     * @param mixed $duplicate
     * @return string
     */
    protected function parse_duplicate($duplicate,$update){
        // 布尔值或空则返回空字符串
        if(is_bool($duplicate) || empty($duplicate)) return '';
    
        if(is_string($duplicate)){
            // field1,field2 转数组
            $duplicate = explode(',', $duplicate);
        }elseif(is_object($duplicate)){
            // 对象转数组
            $duplicate = get_class_vars($duplicate);
        }
        $updates                    = array();
        foreach((array) $duplicate as $key=>$val){
            if(is_numeric($key)){ // array('field1', 'field2', 'field3') 解析为 ON DUPLICATE KEY UPDATE field1=VALUES(field1), field2=VALUES(field2), field3=VALUES(field3)
                if($update){
                    $updates[]          = $this->parse_key($val, '`')."=".$this->parse_key($val, '`')."+VALUES(".$this->parse_key($val, '`').")";
                }else{
                    $updates[]          = $this->parse_key($val, '`')."=VALUES(".$this->parse_key($val, '`').")";
                }
            }else{
                if(is_scalar($val)) // 兼容标量传值方式
                    $val            = array('value', $val);
                if(!isset($val[1])) continue;
                switch($val[0]){
                    case 'exp': // 表达式
                        $updates[]  = $this->parse_key($key, '`')."=($val[1])";
                        break;
                    case 'value': // 值
                    default:
                        $name       = count($this->bind);
                        $updates[]  = $this->parse_key($key, '`')."=:".$name;
                        break;
                }
            }
        }
        if(empty($updates)) return '';
        return " ON DUPLICATE KEY UPDATE ".join(', ', $updates);
    }
    
    /**
     * 字段和表名处理添加`
     * @access protected
     * @param string $key
     * @return string
     */
    protected function parse_key(&$key) {
        $key   =  trim($key);
        if(!preg_match('/[,\'\"\*\(\)`.\s]/',$key)) {
            $key = '`'.$key.'`';
        }
        return $key;
    }

    /**
     * where字段处理
     * @param $where
     * @return string
     */
    protected function _parse_where( $where = '' ) {
        if ( is_array( $where) && !empty($where) ){
            $arr = [];
            foreach ($where as $field => $value ) $arr[] = "{$field}='{$value}'";
            return "WHERE " . implode(' AND ', $arr );
        }
        $where = trim($where);
        if ( empty($where) ) return '';
        $where = strtolower(substr($where,0, strlen('where'))) != 'where' ? " WHERE {$where} " : $where;
        return $where;
    }

    protected function _parse_order ( $order = '' ) {
        $order = trim($order);
        if ( empty($order) ) return '';
        $order = strtolower(substr($order,0, strlen('order by'))) != 'order by' ? " ORDER BY {$order} " : $order;
        return $order;
    }
}