<?php
namespace Mongo;
class Mongo{

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

    public function __construct( $className ) {
        $arr = explode('\\', $className);
        $tableName = strtolower( array_pop($arr));
        $this->_realTableName = $this->_prefix . $tableName;
    }



    /**
     * 取数据库连接类
     * @return Db
     */
    public function mongodb() {
        return Mongodb::instance( $this->_connection_key );
    }

    /**插入数据**/
    public function add($array){
        return $this->mongodb()->insert($array,$this->_realTableName);
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
            }
        }
        if (empty($table_name)) return '';
        if (empty($arr_cfg)) return $this->_prefix . $table_name;
        if ( !is_array( $arr_cfg ) || empty($arr_cfg['MONGODB_NAME'])) return $table_name;
        return "`{$arr_cfg['MONGODB_NAME']}`.`{$arr_cfg['MONGODBDB_PREFIX']}{$table_name}`";
    }
}