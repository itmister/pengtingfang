<?php
namespace Sdk\Ws;

class WcsGetPolicy
{
    public $deadline;

    public function build_url($baseUrl, $mac) {
        $deadline = $this->deadline;
        if ($deadline == 0) {
            $deadline = round(1000 * (microtime(true) + 3600));
        }

        $pos = strpos($baseUrl, '?');
        if ($pos !== false) {
            $baseUrl .= '&e=';
        } else {
            $baseUrl .= '?e=';
        }
        $baseUrl .= $deadline;

        $token = get_token($mac, $baseUrl);
        return $baseUrl . '&token=' . $token;
    }
} 