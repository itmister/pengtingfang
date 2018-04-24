<?php
namespace Dao;
class Db {

    protected static $_instances = array();

    protected $_link = null;

    protected $_cfg = array();

    protected $_last_sql = '';

    /**
     * 取数据库连接实例
     * @param $key
     * @return Db
     */
    public static function instance($key ) {
        if (empty($key)) return false;
        if ( empty(self::$_instances[$key]) ) {
            $cfg = \Lib\Core::config($key);
//            $cfg = \C($key);
            $obj = new self( $cfg );
            self::$_instances[$key] = $obj;

        }
        return self::$_instances[$key];
    }

    public function __construct( $cfg ) {
        $this->_cfg = $cfg;
    }



    public function connect() {

        $cfg = $this->_cfg;
        $this->_link = @mysqli_connect($cfg['DB_HOST'], $cfg['DB_USER'], $cfg['DB_PWD'], $cfg['DB_NAME'],
            !empty($cfg['DB_PORT']) ? $cfg['DB_PORT'] : 3306 );
        $db_charset = !empty($cfg['DB_CHARSET']) ? trim( $cfg['DB_CHARSET'] ) : 'utf8';

        if (empty($this->_link)) {
            throw new \Exception('db connect error', 4);
        }
        mysqli_query( $this->_link, "SET NAMES {$db_charset}");

    }

    public function close() {
        return mysqli_close( $this->_link );
    }

    /**
     * 取记录的条数
     * @param $table_name
     * @param $where
     * @return int
     */
    public function count( $table_name, $where = '' ) {
        $where = !empty( $where ) ? " WHERE {$where} " : '';
        $sql = "
            SELECT count(*) as num FROM {$table_name} {$where}
        ";
        $arr_data = $this->getAll($sql);
        return !empty($arr_data) ? intval( $arr_data[0]['num'] ) : 0;
    }

    /**
     * 导出表
     * @param $table_name
     * @param string $fields
     * @param string $where
     * @param string $file_output
     * @param int $step
     */
    public function dump( $table_name, $fields = '*', $where = '', $file_output = '', $step = 1000 ) {

        $total      = $this->count( $table_name, $where);
        $path       = dirname( $file_output );
        if (!is_dir($path)) mkdir( $path, 0777, true);
        if ( $total > $step ) {
            for ($i = 0; $i < $total; $i+= $step ) {
                $this->_dump( $table_name, $fields, $where, $file_output, "{$i},$step" );
            }
        }
        else {
            $this->_dump( $table_name, $fields, $where, $file_output );
        }
    }

    protected function _dump(  $table_name, $fields = '*', $where = '', $file_output = '', $limit= '' ) {
        $where      = !empty($where) ? " WHERE {$where} " : '';
        $limit      = !empty($limit) ? " LIMIT {$limit} " : '';
        $sql = "
        SELECT
            {$fields}
        FROM
            {$table_name}
        {$where}
        {$limit}
        ";
        $arr_data       = $this->getAll( $sql );
        if ( !empty($arr_data) ) {
            $field_export   = '(' . $this->_field_to_str( array_keys( current( $arr_data ) ), '`') . ')';
            $arr_values     = '';
            foreach ( $arr_data as $row ) {
                $arr_values[] = '(' . $this->_field_to_str( $row, '\'') . ')';
            }
            $str            = "INSERT INTO {$table_name} {$field_export} VALUES " . implode(',', $arr_values) . "\n";
            file_put_contents( $file_output, $str, FILE_APPEND);
        }
    }

    /**
     * 取插入id
     * @return int|string
     */
    public function lastInsertId() {
        return mysqli_insert_id( $this->_link );
    }

    /**
     * 取错误信息
     */
    public function get_error() {
        return mysqli_error( $this->_link );
    }

    /**
     * 取最后一次执行的sql
     * @return mixed
     */
    public function get_last_sql() {
        return $this->_last_sql;
    }

    /**
     * 执行查询
     * @param $sql
     * @return bool|\mysqli_result
     */
    public function query( $sql ) {
        $this->_last_sql = $sql;
        $link   = $this->_linkGet();
        $ret    = mysqli_query( $link, $sql);

//        echo mysqli_error( $link );//@todo
        if ( defined('APP_DEBUG') && APP_DEBUG ) {
            if ( defined('MYSQL_HALT_ERROR') && MYSQL_HALT_ERROR ) {
                $error = mysqli_error( $link );
                if ( !empty($error) ) throw new \Exception( $error, 101 );
            }
        }

        return $ret;
    }

    /**
     * 多结果集查询
     * @param $sql
     * @return array
     */
    public function query_multi( $sql ) {
        $link       = $this->_linkGet();
        $arr_result = array();
        $this->_last_sql = $sql;
        if (mysqli_multi_query($link, $sql)) {
            do {
                /* store first result set */
                if ($result = mysqli_store_result( $link ) ) {
                    $rows = array();
                    while ($row = mysqli_fetch_assoc($result)) {
                        $rows[] = $row;
                    }
                    $arr_result[] = $rows;
                    mysqli_free_result($result);
                }
            } while ( mysqli_more_results($link) && mysqli_next_result($link));
        }
        return $arr_result;
    }

    /**
     * 获得所有的查询数据
     * @access public
     * @param string $sql  sql语句
     * @return array
     */
    public  function getAll( $sql = '') {
        $link = $this->query($sql);
        $result = array();
        if (empty($link) || !is_object( $link)) return $result;
        while ($data = mysqli_fetch_assoc($link)) {
            $result[] = $data;
        }
        return $result;

        /*
        //返回数据集
        $result = array();
        if($this->numRows>0) {
            //返回数据集
            for($i=0;$i<$this->numRows ;$i++ ){
                $result[$i] = $this->queryID->fetch_assoc();
            }
            $this->queryID->data_seek(0);
        }
        return $result;
        */
    }

    /**
     * yield迭代返回结果结果
     */
    public function yield_result( $sql = '' ) {
        $link = $this->query( $sql );
        if ( !empty($link) && is_object( $link) ) while ($data = mysqli_fetch_assoc($link)) yield $data;
    }

    public function begin_transaction(){
        if(!$this->_link){
            $this->_link = $this->_linkGet();
        }
        mysqli_begin_transaction($this->_link);
        mysqli_autocommit ( $this->_link ,  FALSE );
    }

    public function commit(){
        mysqli_commit ( $this->_link );
        mysqli_autocommit ( $this->_link ,  true );
    }

    public function rollback (){
        mysqli_rollback ( $this->_link );
        mysqli_autocommit ( $this->_link ,  true );
    }

    public function affected_rows(){
        return mysqli_affected_rows ( $this->_link );
    }

    /**
     * 批量增加
     * @param $data
     * @param boolean $replace 是否替换
     * @param string $table_name
     * @return bool|\mysqli_result
     */
    public function add_all( $data, $table_name, $replace = false ) {
        if (empty($data)) return false;
        $first = current($data);
        $arrFields = array_keys($first);
        $fields = $this->_field_to_str( $arrFields , '`');
        $arrValues = array();
        foreach ( $data as $row ) {
            $arrValues[] = $this->_field_to_str( $row );
        }
        $values = implode('),(', $arrValues);
        $insert_type = empty($replace) ? 'INSERT' : 'REPLACE';
        $sql = "{$insert_type} INTO {$table_name} ({$fields}) values ({$values});";
        return $this->query( $sql );
    }

    /**
     * 取当前数据库连接
     * @return null
     */
    protected function _linkGet() {
        if (empty($this->_link)) $this->connect();
        return $this->_link;
    }

    /**
     * 字段数组转换成字符串
     * @param $arr
     * @param string $quote
     * @return string
     */
    protected function _field_to_str($arr, $quote = '\'') {
        return $quote . implode($quote . ',' . $quote, $arr) . $quote;
    }

}