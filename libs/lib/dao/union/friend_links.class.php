<?php
namespace Dao\Union;
class Friend_links extends Union {

    use \Dao\Crud;

    /**
     * @return Friend_links
     */
    public static function get_instance() { return parent::get_instance(); }

    /**
     * 列表排序
     */
    public function sort_list(){
        $sql = "SELECT * FROM friend_links ORDER BY sort ASC;";
        $data = $this->query($sql);
        return $data;
    }
}
