<?php
namespace Mongo\Stat;

class Tadmin_directory_performance_ranking extends Stat {


    /**
     * @return Tadmin_directory_performance_ranking
     */
    public static function i(){ return parent::i();}

    public function add_all($data) {
        $int_field = ['director_uid', 'ym'];
        foreach ($data as &$item ) {
            $item['_id'] = "{$item['director_uid']}_{$item['ym']}";
            foreach ($int_field as $filed ) $item[$filed] = intval( $item[$filed]);
        }
        parent::add_all( $data );
    }
    
    /**
     * 根据年月取值
     * @param unknown $ym
     */
    public function get_list($ym,$sort = 'technician_credit30'){
    	$condition = ['ym' => intval($ym)];
    	$condition['director_user_name'] = array('$ne'=>'admin'); 
    	$fields = array("_id"=>false,"director_uid"=>false,"ym"=>false);
    	$result = iterator_to_array( $this->find( $condition )->fields($fields)->sort([$sort => -1]) );
    	return $result;
    }
}