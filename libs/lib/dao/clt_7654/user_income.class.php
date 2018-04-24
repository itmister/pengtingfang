<?php
/**
 * @desc 用户收入表;
 */
namespace Dao\Clt_7654;
use \Dao;
class User_income extends Clt_7654 {
    
    
    protected static $_instance = null;

    /**
     * @return User_income
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function fetch_user_income($uid){
        $month = date('Ym');
        $last_month = date('Ym',strtotime('-1 month'));
        
        $sql = "
          SELECT
        	SUM(income) AS sum_income,
        	(SUM(CASE WHEN FROM_UNIXTIME(fafang_dateline, '%Y%m') = {$month} THEN income END)) AS current_month_income,
        	(SUM(CASE WHEN FROM_UNIXTIME(fafang_dateline, '%Y%m') = {$last_month} THEN income END)) AS last_month_income
          FROM
        	{$this->_get_table_name()}
          WHERE
        	type = 1
          AND `status` = 2
        ";
        return current($this->query($sql));
    }
    
    public function fetch_list($where,$limit){
        $sql = "
            SELECT
            	i.*,u.username,u.phone,b.bank_account,b.customer_name,b.bank_account,b.bank_account_name
            FROM
            	`{$this->_get_table_name()}` AS i
            INNER JOIN `user` AS u ON i.uid = u.uid
            INNER JOIN `bank_log` AS b ON i.bank_account = b.bank_account
        ";
        if($where){
            $sql .=" WHERE {$where}";
        }
        if($limit){
            $sql .=" LIMIT {$limit}";
        }
        return $this->query($sql);
    }
}
?>
