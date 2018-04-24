<?php
/**
 * 网宿cdn管理
 * User: weiliang
 * Date: 2016/4/16
 * Time: 18:46
 */

namespace Sdk\Txcos;
use Core\Object;
//use \Auth;
//use \Cosapi;
require_once __DIR__ . '/include.php';
require_once(__DIR__  .'/auth.php');
require_once(__DIR__  .'/conf.php');
require_once(__DIR__  .'/cosapi.php');
require_once(__DIR__  .'/http_client.php');
require_once(__DIR__  .'/slice_uploading.php');

class Txcos extends Object {

    protected $_cfg = [];

    /**
     * @param array $option
     * @return Txcos
     */
    public static function i($option=[]) {
        return parent::i($option);
    }

    /**
     * 上传文件
     * @param string $bunket 资源桶
     * @param string $file_remote cdn文件路径
     * @param string $file_local 本地文件路径
     * @return boolean 是否上传成功
     */
    public function file_upload( $bunket, $file_remote, $file_local ) {
        error_reporting(E_ALL);

        Cosapi::setTimeout(180);

// 设置COS所在的区域，对应关系如下：
//     华南  -> gz
//     华中  -> sh
//     华北  -> tj
        Cosapi::setRegion('sh');

// Create folder in bucket.
        //$ret = Cosapi::createFolder($bunket, $folder);
        //var_dump($ret);

// Upload file into bucket.
        $ret = Cosapi::upload($bunket, $file_local, $file_remote);

        if ( $ret['code'] != 0 ) {
            \Util\Debug::log([$bunket, $file_remote, $file_local], "上传至cdn");
            \Util\Debug::log($ret, "上传至cdn出错");
        }else {
            \Util\Debug::log([$bunket, $file_remote, $file_local, $ret], "上传至cdn成功");
        }

        return $ret['code'] == 0?1:0;
    }

    /**
     * 删除文件
     * @param string $bunket 资源桶
     * @param string $file_remote cdn文件路径
     * @return boolean
     */
    public function delete( $bunket, $file_remote ) {
//        $returnBody = 'bucket=$(bucket)&key=$(key)&fname=$(fname)&url=$(url)&hash=$(hash)';
        $client = new WcsFileManager();
        $ret = $client->delete( $bunket, $file_remote );
        if ( $ret->code != 200 ) {
            \Util\Debug::log([$bunket, $file_remote], "删除cdn文件");
            \Util\Debug::log($ret, "上传至cdn出错");
        }

        return $ret->code == 200;
    }
}