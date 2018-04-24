<?php
/**
 * @desc 错误码
 */
namespace Util;

class Errcode_msg{

    public static function codeByMsg($errcode, $type='public'){
        $msg = array(
            'public' => array(
                90000 => '请勿重负提交',
            ),
        );
        return $msg[$type][$errcode];
    }
}