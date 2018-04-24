<?php
namespace Dao\Log_7654;
use Dao\Dao;

class Log_7654 extends Dao {
    protected $_connection_key = 'db_log_7654';
    protected $_prefix = '';
    protected $_realTableName ;
    

    
    public function create_table(){
        $table = "biyibi_".date("Ymd");
        $sql="
Create Table If Not Exists {$table}(
  `His` int DEFAULT NULL,
  `Times` int DEFAULT NULL,
  `Site` varchar(255) DEFAULT NULL,
  `Httpcode` varchar(255) DEFAULT NULL,
  `Key` varchar(255) DEFAULT NULL,
  `PriceMin` smallint(6) DEFAULT NULL,
  `PriceMax` smallint(6) DEFAULT NULL,
  `Orderby` varchar(255) DEFAULT NULL,
  `Class` varchar(255) DEFAULT NULL,
  `Brand` varchar(255) DEFAULT NULL,
  `PageNum` smallint(255) DEFAULT NULL,
  `PageSize` tinyint(255) DEFAULT NULL,
  `ZiYing` char(6) DEFAULT NULL,
  `ExtraParameter` varchar(255) DEFAULT NULL,
  `Chno` varchar(255) DEFAULT NULL,
  `State` smallint(6) DEFAULT NULL,
  `Ip` varchar(255) DEFAULT NULL,
  `Sendtime` varchar(6) DEFAULT NULL,
   KEY `His` (`His`) USING BTREE
)Engine MyISAM DEFAULT CHARSET=utf8;
    ";
     $this->query($sql);
        
     $this->_realTableName = $table;
     
     
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    
}