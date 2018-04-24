<?php
namespace Sdk\Ws;

/**
 * @param $str
 * @return mixed
 */
function url_safe_base64_encode($str)
{
	$find = array('+', '/');
	$replace = array('-', '_');
	return str_replace($find, $replace, base64_encode($str));
}


/**
 * @param $str
 * @return string
 */
function url_safe_base64_decode($str)
{
	$find = array('-', '_');
	$replace = array('+', '/');
	return base64_decode(str_replace($find, $replace, $str));
}

/**
 * @param $str
 * @param $token
 * @return bool
 */
function str_start_with($str, $token) {
    return stripos($str, $token) == 0;
}

function get_user_agent()
{
    global $WCS_SDK_VER;
    $sdkInfo = "WCS PHP SDK /$WCS_SDK_VER (http://wcs.chinanetcenter.com/)";

    $systemInfo = php_uname("s");
    $machineInfo = php_uname("m");

    $envInfo = "($systemInfo/$machineInfo)";

    $phpVer = phpversion();

    $ua = "$sdkInfo $envInfo PHP/$phpVer";
    return $ua;
}

function http_get($url)
{
    $ch = curl_init();
    $options = array(
        CURLOPT_USERAGENT => get_user_agent(),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => false,
        CURLOPT_URL => $url
    );

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);

    $ret = new WcsHttpReturn();
    $errno = curl_errno($ch);
    if ($errno !== 0) {
        $ret->code = 0;
        $ret->message = curl_error($ch);
        curl_close($ch);
        return json_encode($ret);
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseArray = explode("\r\n\r\n", $result);
    $responseArraySize = sizeof($responseArray);
    $respHeader = $responseArray[$responseArraySize - 2];
    $respBody = $responseArray[$responseArraySize - 1];

    $ret->code = $code;
    $ret->respBody = $respBody;

    return $respBody;
}

function http_post($url, $headers, $fields)
{
    $ch = curl_init();
    $options = array(
        CURLOPT_USERAGENT => get_user_agent(),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
        CURLOPT_HEADER => true,
        CURLOPT_NOBODY => false,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_URL => $url
    );

    if (!empty($headers)) {
        $options[CURLOPT_HTTPHEADER] = $headers;
    }

    if (!empty($fields)) {
        $options[CURLOPT_POSTFIELDS] = $fields;
    }

    curl_setopt_array($ch, $options);

    $result = curl_exec($ch);

    $ret = new WcsHttpReturn();
    $errno = curl_errno($ch);
    if ($errno !== 0) {
        $ret->code = 0;
        $ret->message = curl_error($ch);
        curl_close($ch);
        return $ret;
    }

    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    $responseArray = explode("\r\n\r\n", $result);
    $responseArraySize = sizeof($responseArray);
    $respHeader = $responseArray[$responseArraySize - 2];
    $respBody = $responseArray[$responseArraySize - 1];

    $ret->code = $code;
    $ret->respBody = $respBody;


//        list($reqId, $xLog) = get_request_info($respHeader);

//        $resp->header["X-Reqid"] = $reqId;
//        $resp->header["X-Log"] = $xLog;

    return $ret;
}


function build_public_url($bucketName, $fileName) {
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

function build_private_url($bucketName, $fileName) {
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