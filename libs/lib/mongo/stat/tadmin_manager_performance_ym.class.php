<?php
namespace Mongo\Stat;

class Tadmin_manager_performance_ym extends Stat {

    /**
     * @return Tadmin_manager_performance_ym
     */
    public static function i(){ return parent::i();}


    public function add_all($data) {
        foreach ($data as &$item ) $item['_id'] = "{$item['manager_uid']}_{$item['ym']}";
        parent::add_all( $data );
    }

    public function get_list( $ym, $director_uid, $manager_uid) {

        $condition = ['ym' => intval($ym)];
        //adminµÄÇþµÀidÎª1
        if ( !empty($director_uid) && $director_uid != 1) $condition[ 'director_uid' ] = intval($director_uid);
        if ( !empty($manager_uid) ) $condition['manager_uid'] = intval( $manager_uid );
//        $fields = array("_id"=>false,"director_uid"=>false,"ym"=>false,"manager_uid"=>false);
        $result = iterator_to_array( $this->find( $condition ) );
        return $result;

    }
}