<?php
namespace Dao\Wallpaper;
use \Dao;
class Tag extends Wallpaper {


    /**
     * @return Tag
     */
    public static function get_instance(){ return parent::get_instance(); }

    /**
     * 查找分类
     * @param $start
     * @param $num
     * @param null $category_id
     * @param null $tag_name
     * @param bool|false $with_category_info
     * @return array
     */
    public function get_list( $start, $num, $category_id = null,$tag_name= null, $with_category_info = false ) {
        $arr_where = [];
        $where = '';
        if (!empty($tag_name)) $arr_where[] = " tag_name like '{$tag_name}%' ";
        if (!empty($arr_where)) $where = " WHERE " . implode( ' and ', $arr_where );
        $sql_total = "
            select count(*) from tag {$where}
        ";
        $sql = "
            select * from tag {$where} ORDER by id desc
        ";
        $data           = $this->page_get($sql,  $sql_total, $start, $num );

        //查找分类
        $tag_ids = [];
        foreach ($data['list'] as $row ) $tag_ids[] = $row['id'];
        if ( !empty($with_category_info) and !empty($tag_ids)) {
            $ids = implode(',', $tag_ids );
            $sql_get_category = "
            select ct.id_tag,c.category_name from category_tag ct LEFT JOIN category c on c.id=ct.id_category where id_tag in ($ids)
            ";

            $tag_category_list = [];

            foreach ( $this->query( $sql_get_category) as $row ) $tag_category_list[$row['id_tag']] .= $row['category_name'] . ' ';
            foreach ($data['list'] as &$item ) $item['category'] = !empty($tag_category_list[$item['id']]) ? $tag_category_list[$item['id']] : '';
        }

        return $data;
    }

    /**
     * 根据标签名返回标签id
     * @param array $tag_names
     * @return array
     */
    public function get_tag_id_by_tag_names( $tag_names = [] ) {

        $result = [];
        $now = time();
        foreach ($tag_names as $tag_name ) {
            $id = $this->get_one('id', "tag_name = '{$tag_name}'");
            $result[] = !empty($id) ? $id : $this->add(['tag_name' => $tag_name, 'timestamp_update' => $now]);
        }

        return $result;

    }
}
