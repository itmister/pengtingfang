<?php
namespace Mongo\Stat;

use Org\Util\String;
class Tadmin_manager_performance extends Stat {

    /**
     * @return Tadmin_manager_performance
     */
    public static function i(){ return parent::i();}


    public function add_all($data) {
        foreach ($data as &$item ) $item['_id'] = "{$item['manager_uid']}_{$item['ym']}";
        parent::add_all( $data );
    }

    public function get_list( $director_uid, $ym, $manager_uid = '-1',$sort = 'technician_credit30' ) {
        $condition = ['ym' => intval($ym)];
       	$condition[ 'director_uid' ] = intval($director_uid);
       	if($manager_uid != -1) $condition[ 'manager_uid' ] = intval($manager_uid);
       	$fields = array("_id"=>false,"director_uid"=>false,"ym"=>false,"manager_uid"=>false);
        $result = iterator_to_array( $this->find( $condition, $fields )->sort([$sort => -1]) );
        return $result;
    }
}