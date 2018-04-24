<?php
/**
 * Created by vl
 * Description : rc4加解密
 * Date: 2016/1/5
 * Time: 17:45
 */
namespace Util\Security;
class Rc4 {
    /**
     * rc4加解密
     * @param string $pwd 密钥
     * @param string $data 加密的字符串
     * @return string
     */
    public static function exe( $pwd, $data ){
        $key[] ="";
        $box[] ="";

        $pwd_length = strlen($pwd);
        $data_length = strlen($data);

        for ($i = 0; $i < 256; $i++){
            $key[$i] = ord($pwd[$i % $pwd_length]);
            $box[$i] = $i;
        }

        for ( $j = $i = 0; $i < 256; $i++ ){
            $j = ($j + $box[$i] + $key[$i]) % 256;
            $tmp = $box[$i];
            $box[$i] = $box[$j];
            $box[$j] = $tmp;
        }
        $cipher = '';
        for ($a = $j = $i = 0; $i < $data_length; $i++){
            $a = ($a + 1) % 256;
            $j = ($j + $box[$a]) % 256;

            $tmp = $box[$a];
            $box[$a] = $box[$j];
            $box[$j] = $tmp;

            $k = $box[(($box[$a] + $box[$j]) % 256)];
            $cipher .= chr(ord($data[$i]) ^ $k);
        }
        return $cipher;
    }
}