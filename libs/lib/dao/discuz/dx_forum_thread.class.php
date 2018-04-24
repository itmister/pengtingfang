<?php
namespace Dao\Discuz;
class Dx_forum_thread extends  Discuz {

    protected static $_instance = null;

    /**
     * @return Dx_forum_thread
     */
    public static function get_instance(){
        if (empty(self::$_instance)) {
            self::$_instance = new self(__CLASS__);
        }
        return self::$_instance;
    }
    public function dx_list(){
        $dx_arr = array(
            'field' => "tid,author,`subject`,FROM_UNIXTIME(dateline) as datetime",
            'where' => "digest>=1 ORDER BY dateline DESC LIMIT 6"
        );
        $data = $this->select($dx_arr);
        return $data;
    }
}
