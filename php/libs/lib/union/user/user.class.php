<?php
/**
 * Created by vl
 * Description :
 * Date: 2015/12/17
 * Time: 15:59
 */
namespace union\user;
use Core\Object;

class user extends Object {
    /**
     * @param array $option
     * @return user
     */
    public static  function i($option=[]) { return parent::i($option); }

    /**
     * ���ӻ��ּ�¼
     * @param integer $user_id �û�id
     * @param integer $credit �õ��Ļ���
     * @param integer $type ����
     * @param integer $sub_type ������
     * @param integer $ip_count ��Ч����
     * @param $need_confirm �Ƿ���Ҫ���
     */
    public function credit_add( $user_id, $credit, $type, $sub_type, $name = '', $ip_count = 0, $from_id = 0 , $dateline = 0, $xishu=1 ,$fafang_type=1 ) {
        $time_now = empty($dateline) ? time() : $dateline;
        $dao_user = \Dao\Union\User::get_instance();
        $dao_credit_wait_confirm = \Dao\Union\Credit_wait_confirm::get_instance();

        //��Ӵ����Ż��ּ�¼
        $data = array(
            'uid' 		=> $user_id,
            'credit'	=> $credit,
            'type'		=> $type,
            'sub_type'	=> $sub_type,
            'name'		=> $name,
            'ip_count'	=> $ip_count,
            'is_get' 	=>  0,
            'ym'		=> date('ym', $time_now ),
            'ymd'		=> date('Ymd', $time_now ),
            'dateline'  => time(),
            'from_id'	=> $from_id,
            'xishu'     => $xishu,
            'fafang_type'=> $fafang_type
        );
        $dao_user->update(['id' => $user_id], [ 'credit_wait_confirm' => 0 ] );//���ô����Ż���
        $credit_wait_confirm_id = $dao_credit_wait_confirm->add($data);
        if( !empty($credit_wait_confirm_id) ){
            $info = \Dao\Union\Credit_Name_Decs_Map::get_instance()->get_info_by_name($name);
            $user_change_log = array(
                'uid' => $user_id,
                'ip_count' => ($data['type']==2) ?$ip_count:0,
                'credit' =>$credit,
                'name' => $data['name'],
                'rule' => ($data['type']==2) ? "��װ":"---",
                'user_type' =>$info['with_attr'] ? 2:1,
                'dateline' => $data['dateline'],
                'ymd'=>$data['ymd'],
            );
            \Dao\Union\User_change_log::get_instance()->add_log( $user_change_log );
        }

        return $credit_wait_confirm_id;
    }
}