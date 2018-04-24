<?php
namespace Mongo\Stat;

class Tadmin_manager_performance_ymd extends Stat {

    /**
     * @return Tadmin_manager_performance_ymd
     */
    public static function i(){ return parent::i();}


    public function add_all($data) {
        foreach ($data as &$item ) $item['_id'] = "{$item['manager_uid']}_{$item['ymd']}";
        parent::add_all( $data );
    }

    public function get_list( $director_uid, $ym, $manager_uid) {
        $condition = ['ym' => intval($ym)];
        if ( !empty($director_uid) && $director_uid != 1) $condition[ 'director_uid' ] = intval($director_uid);
        $condition['manager_uid'] = intval($manager_uid);
        $fields = array("_id"=>false,"director_uid"=>false,"ym"=>false,"manager_uid"=>false);
        $result = iterator_to_array( $this->find( $condition, $fields )->sort(['ymd' => -1]) );
        return $result;
    }
}