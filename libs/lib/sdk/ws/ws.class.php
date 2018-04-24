<?php
/**
 * 网宿cdn管理
 * User: weiliang
 * Date: 2016/4/16
 * Time: 18:46
 */
namespace Sdk\Ws;
use Core\Object;


require_once __DIR__ . '/config.inc.php';
require_once __DIR__ . '/WcsFileDownloader.class.php';
require_once __DIR__ . '/WcsFileManager.class.php';
require_once __DIR__ . '/WcsFileUploader.class.php';
require_once __DIR__ . '/WcsGetPolicy.class.php';
require_once __DIR__ . '/WcsHttpReturn.class.php';
require_once __DIR__ . '/WcsImageManager.class.php';
require_once __DIR__ . '/WcsImageMogr.class.php';
require_once __DIR__ . '/WcsImageView.class.php';
require_once __DIR__ . '/WcsImageWatermark.class.php';
require_once __DIR__ . '/WcsMac.class.php';
require_once __DIR__ . '/WcsPutPolicy.class.php';
require_once __DIR__ . '/utils.php';
require_once __DIR__ . '/auth.php';

class Ws extends Object {

    protected $_cfg = [];

    /**
     * @param array $option
     * @return Ws
     */
    public static function i($option=[]) {
        return parent::i($option);
    }

    public function __construct() {
        $cfg = \Config::get('ws_config');
        $this->_cfg = $cfg;
        wcs_set_key($cfg['ak'], $cfg['sk']);
    }

    /**
     * 上传文件
     * @param string $bunket 资源桶
     * @param string $file_remote cdn文件路径
     * @param string $file_local 本地文件路径
     * @return boolean 是否上传成功
     */
    public function file_upload( $bunket, $file_remote, $file_local ) {
        $host_name = gethostname();
        if (in_array($host_name, ['web_test']) ) {
            return false;
        }
        $bucket = new \Sdk\Txcos\Bucket(str_replace("-","",$bunket));
        $ret = $bucket->upload( $file_remote, $file_local);
        return $ret;
        $returnBody = 'bucket=$(bucket)&key=$(key)&fname=$(fname)&url=$(url)&hash=$(hash)';
        $client = new WcsFileUploader();
        $ret = $client->upload_return($bunket, $file_remote, $file_local, $returnBody);
//        \Io::fb( $ret );

        if ( $ret->code != 200 ) {
            \Util\Debug::log([$bunket, $file_remote, $file_local], "上传至cdn");
            \Util\Debug::log($ret, "上传至cdn出错");
        }
        else {
            \Util\Debug::log([$bunket, $file_remote, $file_local, $ret], "上传至cdn成功");
        }

        return $ret->code == 200;
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