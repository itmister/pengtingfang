<?php
namespace Online_7654;
use Dao\Online_7654\Ad_product_record_fafang;
use Dao\Online_7654\Ad_product_record_fafang_log;
use Dao\Online_7654\Auto_fafang_log;
/**
 * 技术员分配推广软件包
 * @author huxiaowei1238
 *
 */
class Soft_num
{
    /**
     * 技术员分配推广软件包表模型
     */
    protected $softNumModel;
    
    /**
     * 业绩发放模型
     */
    protected $fafangModel;
    
    /**
     * 业绩发放log模型
     */
    protected $fafangLogModel;
    
    /**
     * 自动发放日志模型
     */
    protected $autoFafangLog;
    
    public function __construct(){
        //初始化数据库模型
        $this->softNumModel   = \Dao\Online_7654\Soft_num::get_instance();
        $this->fafangModel    = Ad_product_record_fafang::get_instance();
        $this->fafangLogModel = Ad_product_record_fafang_log::get_instance();
        $this->autoFafangLog  = Auto_fafang_log::get_instance();
    }
    
    /**
     * @param unknown $promote_data 厂商返回原始数据
     * @param unknown $fafang_data  发放日志
     * @param unknown $soft_name    软件名
     * @return boolean
     */
    public function promote_performance($promote_data,$fafang_data,$soft_name)
    {
        if(empty($promote_data) || empty($fafang_data) || !$soft_name)
        {
            return false;
        }
        //渠道列表
        $channel_list = [];
        $params = [
            'field' => "id,username,phone,email,contact",
            'where' => "status = 1",
        ];
        $channel_list_temp = \Dao\Online_7654\Channel::get_instance()->select($params);
        foreach ($channel_list_temp as $value){
            $channel_list[$value['id']] = $value;
        }
        
        //软件信息
        $ymd          = $promote_data[0]['ymd']; 
        $soft_id      = $promote_data[0]['soft_id'];
        $promotion_id = $promote_data[0]['id'];
        $fafang_statu = $promote_data[0]['fafang'];
        
        //查询条件
        $where = "soft_id='{$soft_id}' and ymd={$ymd}";
        
        //是否已发放，存在删除当日该软件发放记录重新发放
        $has_log = $this->fafangLogModel->get_row($where);
        if($has_log)
        {
            //删除业绩发放日志
            $this->fafangLogModel->delete($where);
            
            //删除有效量记录表
            $this->softNumModel->delete($where);
        }
        
        //开启事务
        $this->fafangModel->begin_transaction();
        
        //添加
        $fafang = $this->fafangModel->add($fafang_data,false,'',true);
        if(!$fafang)
        {
            //回滚事务
            $this->fafangModel->rollback();
            return false;
        }
        
        $fafang_log = $this->fafangLogModel->add_all($promote_data);
        if(!$fafang_log)
        {
            //回滚事务
            $this->fafangModel->rollback();
            return false;
        }
        
        
        #进业绩有效量表
        $data_list = $this->fafangLogModel->get_list($soft_id,$ymd);
        if(!$data_list)
        {
            //回滚事务
            $this->fafangModel->rollback();
            return false;
        }
        //更新状态
        $this->fafangLogModel->update($where,array('stat'=>10));
       
        //短信数据
        $sms_data = [];
        
        //邮件数据
        $email_data = [];
        
        //发放业绩
        $soft_num = array();$i = 0;
        foreach($data_list as $value)
        {
            $soft_num[] = array(
                'uid'       => $value['uid'],
                'channel_id'=> $value['channel_id'],
                'org_id'    => $value['org_id'],
                'soft_id'   => $value['soft_id'],
                'soft_name' => $soft_name,
                'num'       => $value['num'],
                'ymd'       => $value['ymd'],
                'dateline'  => time(),
            );

            #蓝光联盟、蟾蜍联盟、51联盟不发短信
            //给渠道商发送短信通知
            $channel_id = $value['channel_id'];
            $channel_name = $channel_list[$channel_id]['username'];
            $contact      = $channel_list[$channel_id]['contact'];
            $phone = $channel_list[$channel_id]['phone'];
            $email = $channel_list[$channel_id]['email'];
            if($value['num'] > 0 && !in_array($channel_name,['蓝光联盟','蟾蜍联盟','51联盟']) && !in_array($value['soft_id'],['hao123','360dh','sgdh']))
            {
                //短信内容
                $sms_template = "尊敬的%s，您好，TN号/软件包名为%s的%s产品在%s的实际发放量为%s，如有问题，请联系QQ:2880626024";
                if($has_log){
                    $sms_template = "[数据更正]尊敬的%s，您好，TN号/软件包名为%s的%s产品在%s的实际发放量更正为%s，如有问题，请联系QQ:2880626024";
                }
                if($phone){
                    $sms_data[$channel_id][] = [
                        'phone' => $phone,
                        'msg'   => sprintf(
                                    $sms_template,$contact,
                                    $value['org_id'],$soft_name,
                                    date('m日d日',strtotime($value['ymd'])),$value['num']
                                ),
                    ];
                }
            }
                
            //邮件内容
            /* if($email){
                if(array_key_exists($channel_id,$email_data)){
                    $email_data[$channel_id]['list'][] = ['date' => $value['ymd'],'tn' => $value['org_id'],'num' => $value['num']];
                }else{
                    if($has_log){
                        $up = "【数据更正】";
                    }
                    $email_data[$channel_id] = [
                        'to'           => $email,
                        'subject'      => $up.'7654联盟线上收量平台实际推广数据报告'.date('m.d',strtotime($value['ymd'])),
                        'list'         => [['date' => $value['ymd'],'tn' => $value['org_id'],'num' => $value['num']]],
                        'channel_name' => $contact,
                        'soft_name'    => $soft_name,
                    ];
                }
            } */

        }
        
        $soft_num = $this->softNumModel->add_all($soft_num);
        if(!$soft_num)
        {
            //回滚事务
            $this->fafangModel->rollback();
            return false;
        }
        
        //更新状态
        $this->fafangLogModel->update($where,array('stat'=>1));
        
        //发放日志
        $auto_log = [
            'soft_id' => $soft_id,
            'ymd'     => $ymd
        ];
        $this->autoFafangLog->add($auto_log,true);
        
        //提交事务
        $this->fafangModel->commit();
        
        //发送短信
        $this->_send_sms($sms_data);
        
        //发送邮件
        $this->_send_email($email_data);
        
        return true;
    }
    
    /**
     * 发送短信
     * @param array $sms_data
     * @return boolean
     */
    protected function _send_sms($sms_data){
        
        if(!$sms_data) return true;
        
        $obj = new \Util\Phone\PhoneVerify_160();
        foreach ($sms_data as $sms){
            foreach ($sms as $_sms){
                $obj->_send( $_sms['phone'], $_sms['msg']);
            }
        }
        return true;
    }
    
    /**
     * 发送邮件
     * @param array $email_data
     * @return boolean
     */
    protected function _send_email($email_data){
        if(!$email_data) return true;
        
        $obj = \Util\Email\Main::instance();
        foreach ($email_data as $key => $email){
            //获取邮件内容
            $body = $this->_mail_body($email['channel_name'],$email['soft_name'],$email['list']);
            $obj->send_with_cc($email['to'], $email['subject'], $body);
        }
        return true;
    }
    
    protected function _mail_body($name,$product,$data){
      //邮件内容
       $day = date('Y年m月d日',strtotime($data[0]['date']));
       $body  = "<div style='width:800px;height:auto;'>
                 <p>尊敬的{$name}：</p>
                 <p style='text-align:center;'>{$day}的{$product}产品实际发放量</p>";
       $body .= "<table style='border-collapse: collapse;border-spacing:0;text-align:center;width:800px;border: 1px solid #ccc;'>
                    <thead>
                        <tr>
                            <th style='border: 1px solid #ccc;'>日期</th>
                            <th style='border: 1px solid #ccc;'>TN号/软件包名</th>
                            <th style='border: 1px solid #ccc;'>实际推广量</th>
                        </tr>
                    </thead>
                    <tbody>";
        foreach ($data as $value){
            $body .= "
                <tr>
                   <td style='border: 1px solid #ccc;'>{$value['date']}</td>
                   <td style='border: 1px solid #ccc;'>{$value['tn']}</td>
                   <td style='border: 1px solid #ccc;'>{$value['num']}</td>
                </tr>";
        }
      
        $body .= "</tbody>
                  </table>
                    <p>7654联盟全体敬上！</p>
                    <p>该邮件由7654联盟系统自动发出，请勿回复此邮件</p>
                 </div>";
      return $body;
    }
}