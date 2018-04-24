<?php
namespace Clt_7654;
use Dao\Clt_7654\Product_record_fafang;
use Dao\Clt_7654\Product_record_fafang_log;
use Dao\Clt_7654\Auto_fafang_log;
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
        $this->softNumModel   = \Dao\Clt_7654\Soft_num::get_instance();
        $this->fafangModel    = Product_record_fafang::get_instance();
        $this->fafangLogModel = Product_record_fafang_log::get_instance();
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
        
        //发放业绩
        $soft_num = array();$i = 0;
        foreach($data_list as $value)
        {
            $soft_num[] = array(
                'uid'       => $value['uid'],
                'qid'       => $value['qid'],
                'soft_id'   => $value['soft_id'],
                'soft_name' => $soft_name,
                'num'       => $value['num'],
                'ymd'       => $value['ymd'],
                'dateline'  => time(),
            );
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
        
        return true;
    }
    
}