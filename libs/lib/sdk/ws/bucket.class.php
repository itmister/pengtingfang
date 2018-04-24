<?php
/**
 * Created by PhpStorm.
 * User: vling
 * Date: 2016/7/5
 * Time: 0:33
 */
namespace Sdk\Ws;
class Bucket {
    protected $_bucket_name = '';

    public function __construct( $bucket ) {
        $this->_bucket_name = $bucket;
    }

    /**
     * cdn上傳文件
     * @param $file_remote
     * @param $file_local
     * @return bool|void
     */
    public function upload($file_remote, $file_local) {
        if (empty($this->_bucket_name)) return;
        if ( substr( $file_remote, 0, 1 ) == '/' )  $file_remote = substr( $file_remote, 1 );

        return \Sdk\Ws\Ws::i()->file_upload( $this->_bucket_name, $file_remote, $file_local  );
    }
}