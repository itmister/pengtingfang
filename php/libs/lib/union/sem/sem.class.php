<?php
namespace Union\Sem;
use Dao\Udashi_admin\Stat\Template;
use Dao\Udashi_admin\Stat\Website;

/**
 *
 * Class Sem
 * @package Union\Sem
 */
class Sem {
    public function get_template($url,$query_str = ''){
        if(!$url){
            return false;
        }
        $params = [
            'where' => "url = '{$url}'",
        ];
        if($query_str){
            $params['where'] .= " AND params = '{$query_str}'";
        }
        $website_info = Website::get_instance()->find($params);
        if($website_info){
            $template_info = Template::get_instance()->find(['where' => "id = {$website_info['tid']}"]);
        }else{
            $template_info = Template::get_instance()->find(['where' => "id = 1"]);
        }
        return $template_info['directory'];
    }
}