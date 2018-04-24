<?php
namespace Dao\Daohang_admin;
use \Dao;
class Data extends Daohang_admin {

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
    
    
    /**
     * 获得流量数据-总数
     */
    public function get_liuliang_count($select_params){
        if($select_params['type']=='nw') //按照内外渠道
           return $this->query("select COUNT(a.count) count from (SELECT count(*) count from `data` where  {$select_params['where']} group by dt)a")[0]['count'];
        if($select_params['type']=='p')   //按主渠道
            return $this->query("select count(channel_id) count from `data` where channel_pid = 0 and {$select_params['where']}")[0]['count'];
        if($select_params['type']=='s')   //按子渠道
            return $this->query("select count(channel_id) count from `data` where channel_pid !=0 and {$select_params['where']} ")[0]['count'];
       
    }
    /**
     * 获得流量数据
     */
   public function get_liuliang($select_params){

      $data = array();
      if($select_params['type']=='nw'){ //按照内外渠道
          $getlist = function($typeid,$select_params){
              //内
                  $sql="sELECT dt,{$typeid} as outside , SUM(rec_ip) rec_ip,SUM(rec_pv) rec_pv,SUM(rec_uv) rec_uv,SUM(click_ip)
 click_ip, SUM(click_uv) click_uv, SUM(click_pv) click_pv from  `data` a  , channel b where a.channel_id = b.id and b.outside={$typeid} and {$select_params['where']} group by a.dt ORDER BY {$select_params['orderby']} LIMIT {$select_params['limit']}";
                  $list = $this->query($sql);
               
                  if($list)
                     return $list;
                  else 
                     return array();
          };
          
      
          if($select_params['id']==1){
              return $getlist(1,$select_params);
          }else if($select_params['id']==2){
              return $getlist(2,$select_params);
          }else{
              return array_merge($getlist(1,$select_params),$getlist(2,$select_params));
          }
         
          
      }
      
      if($select_params['type']=='p'){ //按主渠道    channel_id!=4522  特殊主渠道
            $sql="SELECT dt,quality,channel_id,channelname,rec_ip, rec_pv, rec_uv, click_ip, click_uv, click_pv ,firstctg,secondctg from
                  `data` where channel_pid=0 and channel_id!=4522 and {$select_params['where']} order by {$select_params['orderby']} LIMIT {$select_params['limit']}";
              return  $this->query($sql);
      }
       
      if($select_params['type']=='s'){ //按子渠道
          $sql="SELECT * from
          `data` where channel_pid!=0 and {$select_params['where']} order by {$select_params['orderby']} LIMIT {$select_params['limit']}";
          return  $this->query($sql);
      }
          
   }
    
   /*
    * 时间区间count
    * */
   public function yingshou_date_count($select_params){
       
       $sql="SELECT count(a.dt) count from (select dt from data where {$select_params['where']} GROUP BY dt) a";
       
       if($select_params['type']=='s'){
           $sql="SELECT count(a.dt) count from (select dt from data where {$select_params['where']} and  channel_id !=0 GROUP BY dt,channel_id) a";
       }
       
       
       $result = $this->query($sql);
       return $result[0]['count'];
       
   }
   /**
    * 营收渠道数据 - 内外
    * @param unknown data
    */
   public function yingshou_qudao($select_params){
       
       if($select_params['type']=='nw'){
               $sql="select aa.*,bb.* from ( SELECT  b.dt,sum(CASE WHEN xiuzheng_cb !=0  THEN xiuzheng_cb ELSE channel_cb END) xiuzheng_cb_n , 
        sum(CASE WHEN xiuzheng_lr !=0  THEN xiuzheng_lr ELSE channel_lr END) xiuzheng_lr_n,
        sum(channel_sr) channel_sr_n
          FROM channel a INNER JOIN `data` b  on a.id=b.channel_id   WHERE  {$select_params['where']} and  a.pid!=0 and a.outside= 1  group BY b.dt  ORDER BY  b.dt desc
         ) aa
         left JOIN
         (SELECT  b.dt dt2,sum(CASE WHEN xiuzheng_cb >0  THEN xiuzheng_cb ELSE channel_cb END) xiuzheng_cb_w , 
        sum(CASE WHEN xiuzheng_lr >0  THEN xiuzheng_lr ELSE channel_lr END) xiuzheng_lr_w,
        sum(channel_sr) channel_sr_w
          FROM channel a INNER JOIN `data` b  on a.id=b.channel_id   WHERE  {$select_params['where']} and a.pid!=0 and a.outside= 2 group BY b.dt  ORDER BY  b.dt desc
        )bb  on aa.dt = bb.dt2 ";
       
       }
       
       if($select_params['type']=='p'){
           $sql="select t1.channelname,t2.* from channel t1 , 
                (SELECT  
                dt,channel_pid,
                sum(CASE WHEN determine_ip !=0  THEN determine_ip ELSE channel_cb END) determine_ip , 
                sum(CASE WHEN xiuzheng_cb !=0  THEN xiuzheng_cb ELSE channel_cb END) xiuzheng_cb , 
                sum(CASE WHEN xiuzheng_lr !=0  THEN xiuzheng_lr ELSE channel_lr END) xiuzheng_lr,
                sum(CASE WHEN rec_dist_uv < rec_dist_ip  THEN rec_dist_uv ELSE rec_dist_ip END) rec_dist_iporuv,
                sum(channel_sr) channel_sr
                  FROM data  WHERE {$select_params['where']} and channel_pid!=0 GROUP BY dt ,channel_pid  order BY dt desc
                )t2 
                where {$select_params['where']} and t1.id = t2.channel_pid ";
       }
       
       
       if($select_params['type']=='s'){
            $sql=" select t1.channelname,t2.* from channel t1 , 
                (SELECT  
                id,dt,channel_id ,returnnum_xishu,quality,rec_uv,rec_ip,rec_dist_pv,rec_dist_uv,rec_dist_ip,price,determine_ip,xiuzheng_determine_ip,channel_lr,channel_cb,
                (CASE WHEN xiuzheng_cb !=0  THEN xiuzheng_cb ELSE channel_cb END) xiuzheng_cb , 
                (CASE WHEN xiuzheng_lr !=0  THEN xiuzheng_lr ELSE channel_lr END) xiuzheng_lr,
                (channel_sr) channel_sr
                  FROM data  WHERE {$select_params['where']} and channel_pid!=0 GROUP BY dt ,channel_id  order BY dt desc
                )t2 
                where t1.id = t2.channel_id ORDER BY {$select_params['orderby']}  LIMIT {$select_params['limit']}";
       }
       
       
       $result = $this->query($sql);
       return $result;
   }
   
   /**
    * 
    */
   public function yingshou_zhengti($select_params){
       $list = $this->yingshou_qudao($select_params);
       
       foreach ($list as $k=>&$v){
           
           
           $dt = date('Ymd',strtotime($v['dt']));
           $sql = "select dt,SUM(money) money from modules_money where dt=$dt";
           $result = $this->query($sql);
           $v['money'] = $result[0]['money'];
           $v['all_cb'] = $v['xiuzheng_cb_n']+$v['xiuzheng_cb_w'];
           $v['lirun'] = round( ($v['money'] -$v['all_cb']),2);
       }
     
       return $list;
   }
}
