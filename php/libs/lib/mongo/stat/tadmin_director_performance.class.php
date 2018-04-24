<?php
namespace Mongo\Stat;

class Tadmin_director_performance extends Stat {

    /**
     * @return Tadmin_director_performance
     */
    public static function i(){ return parent::i(); }

    public function add_all($data) {
        $int_field = ['director_uid', 'ymd'];
        foreach ($data as &$item ) {
            $item['_id'] = "{$item['director_uid']}_{$item['ymd']}";
            foreach ($int_field as $filed ) $item[$filed] = intval( $item[$filed]);
        }
        parent::add_all( $data );
    }
    
    public function get_list( $director_uid, $ymd_start, $ymd_end ){
    	$condition = ['ymd' => ['$gte'=>intval($ymd_start),'$lte'=>intval($ymd_end)]];
    	if ( !empty($director_uid) ) $condition["director_uid"] = intval($director_uid);
    	$fields = array("_id"=>false,"director_uid"=>false,"director_user_name"=>false);
    	$result = iterator_to_array( $this->find( $condition )->fields($fields)->sort(['ymd' => -1]) );
    	return $result;
    }
}