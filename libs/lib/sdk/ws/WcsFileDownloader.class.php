<?php
namespace Sdk\Ws;
class WcsFileDownloader
{
    /**
     * 获取公开文件下载地址
     * @param $bucketName
     * @param $fileName
     * @return string
     */
    public function build_public_url($bucketName, $fileName) {
        global $WCS_GET_URL;
        $HTTP_PREFIX = 'http://';

        if (str_start_with($WCS_GET_URL, $HTTP_PREFIX)) {
            $baseUrl = $HTTP_PREFIX . $bucketName . '.' . substr($WCS_GET_URL, strlen($HTTP_PREFIX));
        } else {
            $baseUrl = $bucketName . '.' . $WCS_GET_URL;
        }

        $baseUrl .= '/' . $fileName;

        return $baseUrl;
    }

    /**
     * 获取私有文件下载地址
     * @param $bucketName
     * @param $fileName
     * @return string
     */
    public function build_private_url($bucketName, $fileName) {
        global $WCS_GET_URL;
        $HTTP_PREFIX = 'http://';

        if (str_start_with($WCS_GET_URL, $HTTP_PREFIX)) {
            $baseUrl = $HTTP_PREFIX . $bucketName . '.' . substr($WCS_GET_URL, strlen($HTTP_PREFIX));
        } else {
            $baseUrl = $bucketName . '.' . $WCS_GET_URL;
        }

        $baseUrl .= '/' . $fileName;

        $gp = new WcsGetPolicy();
        $url = $gp->build_url($baseUrl, null);

        return $url;
    }
}