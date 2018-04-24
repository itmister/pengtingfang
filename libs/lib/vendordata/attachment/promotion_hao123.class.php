<?php
namespace VendorData\Attachment;
use Util\Tool;
/**
 * hao123导航
 * Class Promotion_hao123
 * @package VendorData\Attachment
 */
class Promotion_hao123 extends Base {

    private $vendor_data;
    public static function get_instance(){
        return parent::get_instance(__CLASS__);
    }

    public function __construct(){
        $this->_attachment_path = "/app/www/jf7654/emailAttach/attachments/hao123@cp01-hao123-union1.cp01.baidu.com/";
        $this->_save_path = "/app/www/jf7654/emailAttach/attachments/hao123/".date('Ymd')."/";
        if(!file_exists($this->_save_path))
        {
            Tool::mk_dir($this->_save_path);
        }
    }

    public function get_data($date = ''){
        if (!file_exists($this->_attachment_path)){
           return false;
        }
        $date  = $date ? $date : date("Y-m-d",strtotime("-1 days"));
        $this->vendor_data = [];
        
        //将下载的邮件附件拷贝到360导航业绩目录
        $handler = opendir($this->_attachment_path);
        while( ($filename = readdir($handler)) !== false )  
        {
            if($filename != "." && $filename != "..") 
            {
                preg_match("/.*?(\d{8,}_hao_pg).*/", $filename,$savename);
                $org_id = $savename[1];
                if(!$org_id)
                {
                    continue;
                }
                //文件保存目录
                $savefile = $this->_save_path.$org_id.".html";
                //文件后缀
                $ext = substr($filename,strrpos($filename, '.'));
                if($ext == ".html")
                {
                    //获取文件数据
                    $data = file_get_contents($this->_attachment_path.$filename);
                    $result = $this->fiter_data($data,$org_id,$date);
                    if($result && !file_exists($savefile))
                    {
                        //移动文件到新的目录
                        copy($this->_attachment_path.$filename,$savefile);
                    }
                    
                }
            }
        }
        
        return array_values($this->vendor_data);
    }
    
    /**
     * 拿到表格数据
     * @param $data
     * @return array [日期,安装量  ]
     */
    public function fiter_data($data,$org_id,$date)
    {
        $dom = new \DomDocument();
        $dom->loadHTML($data);
        $dom->preserveWhiteSpace = false;
        $tr_list = $dom->getElementsByTagName('tr');
        $temp = [];
        for($i = 0; $i < $tr_list->length; $i++)
        {
            $tr = $tr_list->item($i);
            $td_list = $tr->getElementsByTagName("td");
            $th_list = $tr->getElementsByTagName("th");
            if ($td_list->length > 0)
            {
                $ymd = date('Y-m-d',strtotime(trim($th_list->item(0)->nodeValue)));
                if($ymd == $date)
                {
                    $temp['org_id'] = $org_id;
                    $temp['count']  = intval(trim($td_list->item(0)->nodeValue));//安装量
                    $this->vendor_data[$org_id] = $temp;
                }
                
            }
        }
        
        return $temp;
     }
}