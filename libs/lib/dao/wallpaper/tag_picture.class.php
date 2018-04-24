<?php
namespace Dao\Wallpaper;
use \Dao;
class Tag_picture extends Wallpaper {


    /**
     * @return Tag_picture
     */
    public static function get_instance(){ return parent::get_instance(); }

    /**
     * 处理图片标签,关联标签，新标签入库
     * @param integer $id_picture 图片id
     * @param string $tags 图片标签 空格分隔
     * @return array
     */
    public function handle_tag($id_picture, $tags ) {
        $now = time();
        $id_picture = intval( $id_picture );
        $tags = trim( $tags );
        $arr = explode(' ', $tags);


        $dao_tag = Tag::get_instance();
        foreach ( $arr as &$item )  $item = trim($item);
        $sql = "SELECT id, tag_name from tag where `tag_name` in ('" . implode("','", $arr) . "')";
        $rows = $dao_tag->query( $sql );
        $tag_name_list = [];
        foreach ( $rows as $item )  $tag_name_list[ $item['id']] = $item['tag_name'];

        //处理未入库的标签
        foreach ( $arr as $tag_name ) if (!in_array($tag_name, $tag_name_list) ) $tag_name_list[ $dao_tag->add(['tag_name' => $tag_name, 'timestamp_update' => $now])] = $tag_name;

        $this->delete(['picture_id' => $id_picture]);
        $picture_tag_list = [];
        foreach ( $tag_name_list as $tag_id => $tag_name ) $picture_tag_list[] = [ 'picture_id' => $id_picture, 'tag_id' => $tag_id, 'timestamp_update' => $now ];
        if (!empty($picture_tag_list)) $this->add_all( $picture_tag_list );
        return true;
    }
}
