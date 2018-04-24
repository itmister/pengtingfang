<?php
namespace Dao\Daohang_admin;
use \Dao;
class Modules_money extends Daohang_admin {

    protected static $_instance = null;


    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        
        return self::$_instance;
    }

    /**
     * 获取信息
     * @return array
     */
    public function get_all($where=true,$field='*'){
    	$sql = "SELECT {$field} FROM `{$this->_realTableName}` WHERE {$where}";
    	return $this->query($sql);
    }
    
    /**
     * 获取总数
     * @param string $where
     * @return array
     */
    public function get_count($where){
    	$sql = "SELECT count(*) as count FROM `{$this->_realTableName}` WHERE {$where}";
    	$result = $this->query($sql);
    	return $result[0]['count'];
    }
    
    public function yingshou_mokuai_count($select_params){
        $sql="SELECT count(a.dt) count from (select dt from modules_money where {$select_params['where']} GROUP BY dt) a";
        $result = $this->query($sql);
    	return $result[0]['count'];
    }
    
    public function yingshou_mokuai_list($select_params){
        
        //查询最大的时间 和 最小的时间  
        $sql = "select dt from modules_money where  {$select_params['where']} GROUP BY dt desc  order by {$select_params['orderby']} limit {$select_params['limit']}";

        $date_list = $this->query($sql);
        $edata = $date_list[0]['dt'];
        $sdata = $date_list[(count($date_list)-1)]['dt'];
        
        if($select_params['type']==2) //主模块
        $sql = "
select e.* from (select c.dt,d.names,d.pid,sum(money)money from  modules_money c 
	left JOIN 
		(select a.id,a.name names , b.pid,b.pname from modules a 
				INNER JOIN  (select id pid,`name` pname from modules where id in(select id from modules where pid = -1)) b on a.pid = b.pid ) d on c.m_id = d.id  
 
GROUP BY dt,pid  ) e  where dt>={$sdata} and  dt<={$edata} order by dt desc ";
        
        if($select_params['type']==1) //子模块
           $sql = "select e.* from (select c.dt,d.name,d.id pid,sum(money)money from  modules_money c 
	left JOIN  (select id,name from modules where type=1 and is_show=1) d on c.m_id = d.id  
GROUP BY dt,d.id  ) e  where dt>={$sdata} and  dt<={$edata} order by dt desc ";
       
        
        $result = $this->query($sql);
        $data = array();
        
        $cnzzDao = \Dao\Daohang_admin\Cnzz::get_instance(); //cnzz
       
        
        
        foreach ($date_list as $k=>$v){ //时间范围
           
            $cnzz = $cnzzDao->find(['where'=>"dt={$v['dt']}"]);
            $data[$k]['cnzzIP'] = $cnzz['cnzzIP'] ?: '' ; //cnzz访问IP
            $data[$k]['summoney'] = 0; //总收入
            foreach ($result as $kk=>&$vv){
                if ($vv['dt'] == $v['dt']){
                    $data[$k]['dt'] = $v['dt'];
                    $data[$k]['title_'.$vv['pid']] = $vv['money']*1;
                    $data[$k]['title_qian_'.$vv['pid']] = $cnzz['cnzzIP']>0 ? round($vv['money']/$cnzz['cnzzIP']*1000,2) : '' ;
                    $data[$k]['summoney']+=$vv['money'];
                    
                    
                    unset($result[$kk]);
                }else{
                    break;
                }
            }
         
            $data[$k]['summoney_qian'] = $cnzz['cnzzIP']>0 ? round($data[$k]['summoney']/$cnzz['cnzzIP']*1000,2) : '' ; 
            
        }
      
        return $data;
        
    }
}
