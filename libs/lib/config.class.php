<?php
class Config {

    /**
     * 配置取与设置
     * @param string|array $name 配置变量
     * @param mixed $value 配置值
     * @param mixed $default 默认值
     * @param string $file 指定配置文件，不带路径，将在配置文件目录搜索
     * @param boolean $force_reload 强制重新加载$file配置文件
     * @return mixed
     */
    public static function get( $name = null, $value = null, $default = null, $file = '', $force_reload = false) {

        static $_config = array();
        static $_log_file_load = array();

        //初始化配置
        if ( empty($_config) ) {

            $file_cfg = PATH_LIB . 'conf/' . gethostname() . '.php';
            if (!is_file( $file_cfg)) $file_cfg = PATH_LIB . '/conf/default.php';
            if (is_file( $file_cfg )) {
                $cfg = include $file_cfg;
                $_config = array_merge( $_config, array_change_key_case( $cfg ) );
            }

            //加载项目配置文件
            if ( defined('PATH_APP_CONF') && is_file( PATH_APP_CONF . 'config.php' ) )
                $_config = array_merge( $_config, array_change_key_case( include  PATH_APP_CONF . 'config.php' ) );
        }

        if ( !empty($file)
            &&  ( !isset($_log_file_load[$file]) || $force_reload ) ) {
            if ( is_file( $file_cfg = PATH_LIB . '/conf/' . $file . '.php') ) {
//                echo $file_cfg,"\n";
                $ext = array_change_key_case( include($file_cfg) );
                if (!empty($ext) && is_array( $ext)) $_config = array_merge( $_config, $ext);
            }
            if ( defined('PATH_APP_CONF') && is_file( $file_cfg = PATH_APP_CONF . $file . '.php' ) ) {
                $ext = array_change_key_case( include($file_cfg) );
                if (!empty($ext) && is_array( $ext)) $_config = array_merge( $_config, $ext);
            }


            $_log_file_load[$file] = 1;
        }


        if ( empty($name) ) return null;
        if ( is_string($name) ) {
            $name = strtolower($name);
            if ( is_null($value) ){
                //取值

                if ( isset($_config[$name]) ) return $_config[$name];
                if (function_exists('C')) return C( $name, $value, $default );
                return $default;
            }
            //设置值

            $_config[$name] = $value;

            echo "<pre>";print_r($_config[$name] );die;
        }

        if ( is_array( $name ) ) $_config = array_merge( $_config, array_change_key_case( $name ) );
        return true;

    }
}