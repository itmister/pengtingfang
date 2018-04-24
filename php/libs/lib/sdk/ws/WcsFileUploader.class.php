<?php
namespace Sdk\Ws;
class WcsFileUploader
{
    /**
     * 普通上传
     * @param $bucketName
     * @param $fileName
     * @param $localFile
     * @param $returnBody
     * @return string
     */
    function upload_return($bucketName, $fileName, $localFile, $returnBody) {
        $pp = new WcsPutPolicy();
        $pp->returnBody = $returnBody;
        $pp->overwrite = 1;
        $resp = $this->_upload($bucketName, $fileName, $localFile, $pp);
//        $respBody = url_safe_base64_decode($resp->respBody);
//        $respBody = json_decode($resp->respBody, true );
        return $resp;

    }

    /**
     * 回调上传
     * @param $bucketName
     * @param $fileName
     * @param $localFile
     * @param $callbackUrl
     * @param $callbackBody
     * @return array
     */
    function upload_callback($bucketName, $fileName, $localFile, $callbackUrl, $callbackBody) {
        $pp = new WcsPutPolicy();
        $pp->callbackUrl = $callbackUrl;
        $pp->callbackBody = $callbackBody;
        $pp->overwrite = 1;
        $resp = $this->_upload($bucketName, $fileName, $localFile, $pp);

        return $this->build_result($resp);
    }


    /**
     * 通知上传
     * @param $bucketName
     * @param $fileName
     * @param $localFile
     * @param $cmd
     * @param $notifyUrl
     * @return array
     */
    function upload_notify($bucketName, $fileName, $localFile, $cmd, $notifyUrl) {
        $pp = new WcsPutPolicy();
        $pp->persistentOps = $cmd;
        $pp->persistentNotifyUrl = $notifyUrl;
        $pp->overwrite = 1;
        $resp = $this->_upload($bucketName, $fileName, $localFile, $pp);

        return $this->build_result($resp);
    }

    /**
     * @param $bucketName
     * @param $fileName
     * @param $localFile
     * @param WcsPutPolicy $putPolicy
     * @return WcsHttpReturn
     */
    function _upload($bucketName, $fileName, $localFile, $putPolicy) {
        global $WCS_PUT_URL;

        $url = $WCS_PUT_URL . '/file/upload';

        if ($fileName == null || $fileName === '') {
            $putPolicy->scope = $bucketName;
        } else {
            $putPolicy->scope = $bucketName . ':' . $fileName;
        }

        $token = $putPolicy->get_token(null);

        $mimeType = null;
        $fields = array('token' => $token, 'file' => $this->create_file($localFile, $mimeType));
        $resp = http_post($url, null, $fields);

        return $resp;
    }


    private function create_file($filename, $mime)
    {
        // PHP 5.5 introduced a CurlFile object that deprecates the old @filename syntax
        // See: https://wiki.php.net/rfc/curl-file-upload
        if (function_exists('curl_file_create')) {
            return curl_file_create($filename, $mime);
        }

        // Use the old style if using an older version of PHP
        $value = "@{$filename}";
        if (!empty($mime)) {
            $value .= ';type=' . $mime;
        }

        return $value;
    }

    private function build_result($resp) {
        if ($resp->code == 200) {
            $ret = Array(
                'code' => 200,
                'message' => 'OK'
            );
            return json_encode($ret);
        } else {
            return $resp->respBody;
        }
    }
}