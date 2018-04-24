<?php
namespace wallpaper;
use Core\Object;
use Dao\Wallpaper\Category as dao_category;
class category extends Object {

    /**
     * @return category
     */
    public static function i($option = []) { return parent::i($option); }

    public function get_list() {

        $redis = \Io\Redis::i('redis_wallpaper');
        $redis_key = 'wallpaper:cache:category';
        $list = $redis->hGetAll($redis_key);
        if (empty($list)) {
            $table_rows = dao_category::get_instance()->where(['status' => 1])->fields('id,category_name')->find();
            foreach ( $table_rows as $row ) $list[$row['id']] = $row;
            $redis->hMset($redis_key, $list);
        }
        return $list;
    }

    /**
     * »º´æÖØÖÃ
     */
    public function reset() {
        $redis_key = 'wallpaper:cache:category';
        $redis = \Io\Redis::i('redis_wallpaper');
        $redis->del($redis_key);
    }
}