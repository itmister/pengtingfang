<?php
namespace VendorData\Attachment;
/**
 * 360导航
 * Class Promotion_360dh
 * @package VendorData\Attachment
 */
class Promotion_360dh extends Base {

    private $vendor_data;
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_attachment_path = "/app/www/jf7654/emailAttach/attachments/yangqingrong/sunchunfeng_%s-%s.csv";
    }

    public function get_data($date = ''){
        $date  = $date ? date('Ymd',strtotime($date)) : date("Ymd",strtotime("-1 days"));
        $file_path = sprintf($this->_attachment_path,$date,$date);
        
        if (!is_file($file_path)){
            return false;
        }
        $data = $this->parseCsv($file_path);
        $data = $this->fiter_data($data);
        return $data;
    }
    
    /**
     * 拿到表格数据
     * @param $data
     * @return array [子渠道号,安装量  ]
     */
    public function fiter_data($data)
    {
        $org_id_list = ['zhanmeng7654'];
        $arr = [];
        foreach($data as $val){
            $org_id = trim($val[1]);
            if(in_array($org_id, $org_id_list))
            {
                $temp = [];
                $temp['org_id'] = $org_id;//子渠道号
                $temp['count']  = intval($val[3]);//有效IP
                $arr [] = $temp;
            }
            
        }
        return $arr;
     }
}