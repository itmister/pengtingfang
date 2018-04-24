<?php
namespace Dao\Wallpaper;
use \Dao;
class Picture extends Wallpaper {
    use \Dao\Orm;

    /**
     * @return Picture
     */
    public static function get_instance(){ return parent::get_instance(); }

    /**
     * 按分类取图片
     * @param $id_category
     * @param int $start
     * @param int $num
     * @return mixed
     */
    public function by_category( $id_category, $start = 0, $num = 10) {
        $id_category = intval( $id_category );
        $where = '';
        if ( !empty($id_category )) $where = " WHERE ct.id_category = '{$id_category}'";
        $sql = <<<eot
select
	id,file
FROM
	category_tag ct
	INNER  JOIN tag_picture tp on ct.id_tag= tp.tag_id
	INNER JOIN picture p on tp.picture_id = p.id and p.info_complete=1 and p.`status`=1
{$where}
limit
{$start},{$num}
eot;
        $data  = $this->query( $sql );
        return $data;
    }


    /**
     * 查找分类
     * @param $start
     * @param $num
     * @param null $tag_name
     * @return array
     */
    public function search( $start, $num, $tag_name= null) {
        $arr_where = [];
        $where = '';
        $tag_name = trim( $tag_name );

        if ( !empty($tag_name) ) {
            $where = " WHERE tag_name like '{$tag_name}%' ";
            $sql_total = "
            select count(*) from tag t inner JOIN tag_picture tp on t.id=tp.tag_id {$where}
            ";
            $sql = "
            select
              p.*
            from
              tag t
              inner JOIN tag_picture tp on t.id=tp.tag_id
              inner JOIN picture p on tp.picture_id=p.id
            {$where}
            ";
        }
        else {
            $sql_total = "
            select count(*) from picture {$where}
            ";
            $sql = "
            select * from picture {$where} ORDER by id desc
            ";
        }
        $data           = $this->page_get($sql,  $sql_total, $start, $num );
        return $data;
    }

    /**
     * 根据图片id取路径
     * @param $id_picture
     * @return string
     */
    public function file_by_id( $id_picture, $prefix = '.jpg' ) {
        $file =   '/' . floor ( $id_picture / 100 ) . '/' . $id_picture . $prefix;
        return $file;
    }
}
