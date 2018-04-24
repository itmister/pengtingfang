<?php
namespace Sdk\Ws;
function wcs_set_key($accessKey, $secretKey)
{
    global $WCS_ACCESS_KEY;
    global $WCS_SECRET_KEY;

    $WCS_ACCESS_KEY = $accessKey;
    $WCS_SECRET_KEY = $secretKey;
}

function wcs_require_mac($mac)
{
    if (isset($mac)) {
        return $mac;
    }

    global $WCS_ACCESS_KEY;
    global $WCS_SECRET_KEY;

    return new WcsMac($WCS_ACCESS_KEY, $WCS_SECRET_KEY);
}

function get_token($mac, $data)
{
    return wcs_require_mac($mac)->get_token($data);
}

function get_token_with_data($mac, $data)
{
    return wcs_require_mac($mac)->get_token_with_data($data);
}


function get_file_stat_token($bucketName, $fileName) {
    $encodedEntry = url_safe_base64_encode($bucketName . ':' . $fileName);
    $encodedPath = '/stat/' . $encodedEntry . "\n";
    return wcs_require_mac(null)->get_token($encodedPath);
}

function get_file_delete_token($bucketName, $fileName) {
    $encodedEntry = url_safe_base64_encode($bucketName . ':' . $fileName);
    $encodedPath = '/delete/' . $encodedEntry . "\n";
    return wcs_require_mac(null)->get_token($encodedPath);
}