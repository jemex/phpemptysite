<?php 
/**
 * Post Model
 */
 
class post_model extends DBconfig{
	
   public function __construct(){
	$connection = new DBconfig();  // database connection
	$this->connection = $connection->connectToDatabase();
	$this->helper = new helper(); // calling helper class
        $this->users = new users_model();
   }
   public function getposts($arr = array()){
      if(array_key_exists('post_type',$arr)){
          $post_type=$arr['post_type'];
      }else{
          $post_type='post';
      }
      $status=(array_key_exists('status',$arr) ? $arr['status'] : 'publish');
      $post_data=$this->helper->db_select("*", "tb_posts", "WHERE post_type='$post_type' && status='$status'");
      if($post_data->num_rows != 0 ){
       $i=0;
       while($row = $post_data->fetch_assoc()){
          $data[$i]=$this->getpost($row['ID']);
          $i++;
       }
       return $data;  
      }else{
       return false;
      }
   }
   public function getpost($id = 0){
      $post_data=$this->helper->db_select("*","tb_posts","where ID='$id'");
      if($post_data->num_rows != 0){
         $data=$post_data->fetch_assoc();
         if($data['author'] == 1){
            $data['userdata']=$this->users->getUserById($data['author']);             
         }else{
            $data['userdata']=$this->users->getUserById($data['author']);
         }
         return $data;
      }else{
         return false;
      }
   }
   public function get_image_meta($post_id = 0, $meta_key = ""){
   $post_data=$this->helper->db_select("*", "tb_postdata", "where `post_id`=$post_id && `meta_key`='$meta_key' order by `ID` DESC");
      if($post_data->num_rows != 0){
         $fetch_data=$post_data->fetch_assoc();
         return $GLOBALS['POST_IMAGE_URL'].$fetch_data['meta_value'];
      }else{
         return false;
      }
   }
   public function checkLike($user_id,$post_id){

     $check=$this->helper->db_select("*", "tb_likes", "where `user_id`=$user_id && `post_id`=$post_id");
     if($check->num_rows != 0){
       return true;
     }else{
       return false;
     }
    }

   public function totalLikes($post_id){
  
    $check=$this->helper->db_select("*", "tb_likes", "where `post_id`=$post_id");
    if($check->num_rows != 0){
       return $check->num_rows;  
    }else{
       return false;
    }
   }
   public function LikePost($user_id,$post_id){
    $data['post_id']=$post_id;
    $data['user_id']=$user_id;
    $insert = $this->helper->db_insert($data,"tb_likes");
    return $insert;
   }
   public function UnLikePost($user_id,$post_id){
     $delete=$this->helper->db_delete("tb_likes", "where `user_id`=$user_id && `post_id`=$post_id");
     return $delete;
   }
   public function PostComment($user_id,$post_id,$comment){
      $data['post_id']=$post_id;
      $data['user_id']=$user_id;
      $data['message']=$comment;
      $insert = $this->helper->db_insert($data,"tb_comments");
      return $insert;
    }

   /*public function EditComment($user_id,$post_id,$comment){

      if(!empty($where)){
       		$keys = array_keys($where);
		$set = array();
		foreach($keys as $key) {
			$set[] = "$key = '$where[$key]' ";
		}
		$set = implode(" && ", $set);
         $update = $this->helper->db_update($args, 'tb_posts', "WHERE ".$set);
         return $update;

      }else{
        return false;
      }

      $data['post_id']=$post_id;
      $data['user_id']=$user_id;
      $data['message']=$comment;
      $insert = $this->helper->db_update($data,"tb_comments","WHERE ");
      return $insert;
    } */   

    public function allcomments($post_id){
 
      $alldata=$this->helper->db_select("*", "tb_comments", "where `post_id`=$post_id");
      if($alldata->num_rows != 0){
        $i=0;
        while($row = $alldata->fetch_assoc()){
          $data[$i]=$row;
          $i++;
       }
       return $data;  
     }else{
       return false;
     }
   }

   public function insert_post($args = array()){
       if(array_key_exists('post_type',$args)){
          $arr['post_type']=$args['post_type'];
       }else{
          $arr['post_type']='post';
       }
       $arr['content']=(array_key_exists('content',$args) ? $args['content'] : '' );
       $arr['post_name']=(array_key_exists('post_name',$args) ? $args['post_name'] : '' );
       $arr['author']=(array_key_exists('author',$args) ? $args['author'] : 1 );
       $arr['status']=(array_key_exists('status',$args) ? $args['status'] : 'publish' );

       $insert = $this->helper->db_insert($arr,"tb_posts");
       return $insert;
   }

   public function update_post($args = array(),$where = array()){
       if(array_key_exists('post_type',$args)){
          $arr['post_type']=$args['post_type'];
       }else{
          $arr['post_type']='post';
       }
      if(!empty($where)){
       		$keys = array_keys($where);
		$set = array();
		foreach($keys as $key) {
			$set[] = "$key = '$where[$key]' ";
		}
		$set = implode(" && ", $set);
         $update = $this->helper->db_update($args, 'tb_posts', "WHERE ".$set);
         return $update;

      }else{
        return false;
      }
   }

   public function delete_post($args = array()){

        if(!empty($args)){
       		$keys = array_keys($args);
		$set = array();
		foreach($keys as $key) {
			$set[] = "$key = '$args[$key]' ";
		}
		$set = implode(" && ", $set);
         $delete=$this->helper->db_delete("tb_posts", "where ".$set);
         return $delete;

      }else{
        return false;
      }     
   }

   public function add_post_meta($post_id = 0, $meta_key = null, $meta_value = null){
       if($this->get_post_meta($post_id,$meta_key)){
            $this->update_post_meta($post_id,$meta_key,$meta_value);
       }else{
            $arr['post_id']=$post_id;
            $arr['meta_key']=$meta_key;
            $arr['meta_value']=$meta_value;
            $insert = $this->helper->db_insert($arr,"tb_postdata");
         return $insert;            
       }
   }
   public function get_post_meta($post_id = 0, $meta_key = null){
      $post_data=$this->helper->db_select("meta_value","tb_postdata","where `post_id`='$post_id' && `meta_key`='$meta_key'");

      if($post_data->num_rows != 0){
         $data=$post_data->fetch_assoc();
         return $data;
      }else{
         return false;
      }
   }
   public function update_post_meta($post_id = 0, $meta_key = null, $meta_value = null){
       if($this->get_post_meta($post_id,$meta_key)){
            $data['meta_value']=$meta_value;
	    $result = $this->helper->db_update($data, 'tb_postdata', "WHERE post_id='$post_id' && `meta_key`='$meta_key'");
       }else{
            $this->add_post_meta($post_id,$meta_key,$meta_value);            
       }       
   }
}
?>