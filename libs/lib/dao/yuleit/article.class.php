<?php
namespace Dao\Yuleit;
use \Dao;

class Article extends Yuleit{

    /**
     * @return article
     */
    public static function get_instance(){
        return parent::get_instance();
    }

    
    public function addData($newdata){
        $sql = "INSERT INTO {$this->_realTableName}(
        `title`,
        `title_info`,
        `content_href`,
        `content_time`,
        `type_id`,
        `type_index`,
        `index`,
        `source`,
        `addtime`,
        `title_md5`,
        `title_img_list`,
        `title_img_num`,
        `content_img_list`,
        `content_img_num`,
        `content`,
        `img_down_mark`) 
        VALUE('{$newdata['title']}',
              '{$newdata['title_info']}',
              '{$newdata['content_href']}',
              '{$newdata['content_time']}',
              '{$newdata['type_id']}',
              '{$newdata['type_index']}',
              '{$newdata['index']}',
              '{$newdata['source']}',
              '{$newdata['addtime'] }',
              '{$newdata['title_md5']}',
              '{$newdata['title_img_list']}',
              '{$newdata['title_img_num']}',
              '{$newdata['content_img_list']}',
              '{$newdata['content_img_num']}',
              '{$newdata['content']}',
              '{$newdata['img_down_mark']}')";
        $this->exec($sql);
        if ($this->get_error()) return false;
        $oDb = $this->db();
        $last_insert_id =  $oDb->lastInsertId();
        if (!empty($last_insert_id)) return $last_insert_id;
    }

    public function updateData($newdata){
        $sql = "UPDATE {$this->_realTableName} SET `title_img_list` = '{$newdata['title_img_list']}', `title_img_num` = 
        {$newdata["title_img_num"]}, `content_img_list` = '{$newdata["content_img_list"]}', `content_img_num` = 
        {$newdata["content_img_num"]},`content` = '{$newdata['content']}', `img_down_mark` = {$newdata['img_down_mark']}
        where `id` = {$newdata['id']}";
        $this->exec($sql);
        if ($this->get_error()) {
          return false;
        }
        return true;
    }
    
    public function update_index_locked_record($type_index,$b_num){
        return $this->db()->query("UPDATE {$this->_realTableName} set `b_num`={$b_num},`is_show`=1,`index`=`index_lock` where `type_index`='{$type_index}' and `index_lock` <> 0");
    }
    
   public function get_exist_by_title($title){
       $title = md5(addslashes($title));
       $article = $this->query("select id from {$this->_realTableName}  where `title_md5`='{$title}'");
       if($article)
           return true;
       return false;
       
   }

   public function get_img_info($limit){
	  $sql = "select `id`, `title_img_list`, `content_img_list`, `title_img_num`, `content_img_num`, `content` from {$this->_realTableName} where `img_down_mark` = 0 order by content_time desc limit {$limit} ";
      $img_info = $this->query($sql);
      return $img_info;
   }

   public function get_exist_by_url($url){
       $sql = "select count(*) as num from {$this->_realTableName}  where content_href='{$url}'";
	   //echo $sql;
	   $article = $this->query($sql);
	   return $article;
   }
   public function get_all_word($type){
	   $sql = "select id,type_id,title,search_word from {$this->_realTableName} where 1";
	   if($type==1){
			$sql.=" and search_word=''";
	   }else{
			$sql.=" and search_word!='' and associate_article is null";
	   }
	   $sql.=" order by id desc";
	   //echo $sql;
	   $article = $this->query($sql);
	   return $article;
   }


   public function add_search_word($id,$search_word){
	   $sql = "update {$this->_realTableName} set search_word = '$search_word' where id='$id'";
	   //echo $sql."\r\n";
	   $this->exec($sql);
   }

   public function get_associate_article($id,$type_id,$search_word){
	   $array = explode(",",$search_word);
	   $sq = "";
	   foreach($array as $key=>$value){
		   $sq.= "title like '%".$value."%' or ";
	   }
	   $sq = substr($sq,0,strrpos($sq,"or"));
	   $sql = "select id,title from {$this->_realTableName} where id!='$id' and type_id='$type_id' and (";
	   $sql.= $sq;
	   $sql.=") order by id desc limit 12";
	   //echo $sql."\r\n";
	   $article = $this->query($sql);
	   return $article;
   }

   public function get_top($type_id,$num,$id_list){
	   $sql = "select id,title from {$this->_realTableName} where type_id='$type_id' and is_show=1 and id not in(".$id_list.") order by content_time desc limit ".$num;
	   //echo $sql."\r\n";
	   $article = $this->query($sql);
	   return $article;
   }

   public function save_associate_article($id,$search_word){
	   $sql = "update {$this->_realTableName} set associate_article = '$search_word' where id='$id'";
	   //echo $sql."\r\n";
	   $this->exec($sql);
   }



}
