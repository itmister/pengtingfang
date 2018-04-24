<?php
namespace Sdk\Ws;

class WcsMac
{

    public $AccessKey;
    public $SecretKey;

    public function __construct($accessKey, $secretKey)
    {
        $this->AccessKey = $accessKey;
        $this->SecretKey = $secretKey;
    }

    public function get_token($data)
    {
        $sign = hash_hmac('sha1', $data, $this->SecretKey, false);
        return $this->AccessKey . ':' . url_safe_base64_encode($sign);
    }

    public function get_token_with_data($data)
    {
        $data = url_safe_base64_encode($data);
        return $this->get_token($data) . ':' . $data;
    }

    public function SignRequest($req, $incbody) // => ($token, $error)
    {
        $url = $req->URL;
        $url = parse_url($url['path']);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        if ($incbody) {
            $data .= $req->Body;
        }
        return $this->get_token($data);
    }

    public function VerifyCallback($auth, $url, $body) // ==> bool
    {
        $url = parse_url($url);
        $data = '';
        if (isset($url['path'])) {
            $data = $url['path'];
        }
        if (isset($url['query'])) {
            $data .= '?' . $url['query'];
        }
        $data .= "\n";

        $data .= $body;
        $token = 'QBox ' . $this->get_token($data);
        return $auth === $token;
    }
} 