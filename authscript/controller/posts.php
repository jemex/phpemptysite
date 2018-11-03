<?php
class posts{
  function __construct(){
      $this->users = new users_model();
      $this->post = new post_model();
      $this->helper = new helper();
  }

  /*
  * To fetch all news feeds
  * url:  http://merrona.com/authscript/posts/allnews
  */  
  function allnews(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $user_id=$_POST['user_id'];
        if(intval($user_id) == 0){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;
        }
        $args['post_type']='newsfeed';
        $allposts=$this->post->getposts($args);
        if(!$allposts){
	  $json[0]['result']=0;
	  $json[0]['msg']='No user found';
	  echo json_encode($json);
	  die;
        }
        $i=0;
        foreach($allposts as $allpost){
          $json[$i]=$allpost;
          $json[$i]['image']=($this->post->get_image_meta($allpost['ID'],'feature_image') ?: '');
          $json[$i]['location']='';
          $json[$i]['liked']=($this->post->checkLike($user_id,$allpost['ID']) ? 'true' : 'false' );
          $json[$i]['total_liked']=($this->post->totalLikes($allpost['ID']) ?: 0 );
          $json[$i]['total_comment']=0;
          $i++;
        }
        echo json_encode($json);
	die;
     }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'something went wrong';
        echo json_encode($arr);
     }
  }
  

  /*
  * To post detail
  * url:  http://merrona.com/authscript/posts/singlepost/2
  */  
  function singlepost(){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
       $user_id=$_POST['user_id'];
       $post_id=$this->helper->getUriSegment(2);
       if(intval($post_id) == 0 || intval($user_id) == 0){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;        
       }
       $details=$this->post->getpost($post_id);
       if(!$details){
	  $json[0]['result']=0;
	  $json[0]['msg']='No data found';
	  echo json_encode($json);
	  die;
       }
       $json[0]=$details;
       $json[0]['image']=($this->post->get_image_meta($details['ID'],'feature_image') ?: '');
       $json[0]['location']='';
       $json[0]['liked']=($this->post->checkLike($user_id,$details['ID']) ? 'true' : 'false' );
       $json[0]['total_liked']=($this->post->totalLikes($details['ID']) ?: 0 );
       $comments=$this->post->allcomments($post_id);
       if($comments){
         $json[0]['total_comment']=count($comments);
         $c=0;
         foreach($comments as $comment){
           if($this->users->getUserById($comment['user_id'])){
            $json[0]['comments'][$c]['comment_id']=$comment['ID'];
            $json[0]['comments'][$c]['message']=$comment['message'];
            $json[0]['comments'][$c]['dated']=$comment['dated'];
            $json[0]['comments'][$c]['comment_user']=$this->users->getUserById($comment['user_id']);
            $c++;
           }
         }  
           if($c==0){
              $json[0]['total_comment']=0;
              $json[0]['comments']['result']=0;
              $json[0]['comments']['msg']='no comment found';
           }      
      }else{
         $json[0]['total_comment']=0;
         $json[0]['comments']['result']=0;
         $json[0]['comments']['msg']='no comment found';
      }
 
	echo json_encode($json);
	die;
    }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'please call required method';
        echo json_encode($arr);
    }
  }
  /*
  * post like
  * url:  http://merrona.com/authscript/posts/postlike
  */  
  function postlike(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        if(intval($user_id) == 0 || intval($post_id) == 0){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;
        }
        if($this->post->checkLike($user_id,$post_id)){
 	   $json[0]['result']=0;
	   $json[0]['msg']='Already liked';
	   echo json_encode($json);
	   die;      
        }
        $liked=$this->post->LikePost($user_id,$post_id);
       if($liked){
 	$json[0]['result']=1;
	$json[0]['msg']='liked';
	echo json_encode($json);
	die;  
       }else{
 	$json[0]['result']=0;
	$json[0]['msg']='error';
	echo json_encode($json);
	die;  
       }
     }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'please call required method';
        echo json_encode($arr);
     }
  }
  /*
  * post dislike
  * url:  http://merrona.com/authscript/posts/postunlike
  */  
  function postunlike(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        if(intval($user_id) == 0 || intval($post_id) == 0){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;
        }
        if(!$this->post->checkLike($user_id,$post_id)){
 	   $json[0]['result']=0;
	   $json[0]['msg']='not liked';
	   echo json_encode($json);
	   die;      
        }
        $liked=$this->post->UnLikePost($user_id,$post_id);
       if($liked){
 	$json[0]['result']=1;
	$json[0]['msg']='unliked';
	echo json_encode($json);
	die;  
       }else{
 	$json[0]['result']=0;
	$json[0]['msg']='error';
	echo json_encode($json);
	die;  
       }
     }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'please call required method';
        echo json_encode($arr);
     }
  }
/*
  * post comment
  * url:  http://merrona.com/authscript/posts/postcomment
  */  
  function postcomment(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $user_id = $_POST['user_id'];
        $post_id = $_POST['post_id'];
        $message = $_POST['message'];
        if(intval($user_id) == 0 || intval($post_id) == 0 || strlen($message) == 0){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;
        }

        $comments=$this->post->PostComment($user_id,$post_id,$message);
        if($comments){
 	   $json[0]['result']=1;
	   $json[0]['msg']='Comment posted';
	   echo json_encode($json);
	   die; 
        }else{
 	   $json[0]['result']=0;
	   $json[0]['msg']='error';
	   echo json_encode($json);
	   die; 
        }
     }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'please call required method';
        echo json_encode($arr);
     }
  }

  /*
  * add newsfeed
  * url:  http://merrona.com/authscript/posts/addnewsfeed
  */  
  function addnewsfeed(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $user_id=$_POST['user_id'];
        $content=$_POST['content'];
        if(intval($user_id) == 0 || strlen($content) == 0){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;
        }
        $args=array(
                  'post_type'=>'newsfeed',
                  'content'=>$content,
                  'author'=>$user_id
                );
        $post_id=$this->post->insert_post($args);
        if($post_id){

     $validextensions = array("jpeg", "jpg", "png");
     $temporary = explode(".", $_FILES["image"]["name"]);			
     $file_extension = end($temporary);
     $newfilename='';
     if(isset($_FILES["image"])){
         if (($_FILES["image"]["type"] == "image/png") || ($_FILES["image"]["type"] == "image/jpg") || ($_FILES["image"]["type"] == "image/jpeg")) {
            $sourcePath = $_FILES['image']['tmp_name']; 
            $t=time();
            $path = dirname($_SERVER["SCRIPT_FILENAME"]).'/images/posts/';
            $newfilename = $t.".".($file_extension == 'jpeg' ? 'jpg' : $file_extension);
	    $targetPath = $path.$newfilename; 
            move_uploaded_file($sourcePath,$targetPath);
            $this->post->add_post_meta($post_id,'feature_image',$newfilename);
          } 
      }

 	   $json[0]['result']=1;
	   $json[0]['msg']='News added successfully';
	   echo json_encode($json);
	   die; 
        }else{
 	   $json[0]['result']=0;
	   $json[0]['msg']='error';
	   echo json_encode($json);
	   die; 
        }
     }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'please call required method';
        echo json_encode($arr);
     }
  }

  /*
  * To update post detail
  * url:  http://merrona.com/authscript/posts/editnewsfeed/2
  */  
   function editnewsfeed(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $post_id=$this->helper->getUriSegment(2);
        $user_id=$_POST['user_id'];
        $content=$_POST['content'];
        if(intval($user_id) == 0 || strlen($content) == 0 || intval($post_id) == 0 ){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;
        }
        $details = $this->post->getpost($post_id);
        if(!$details){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'invalid post id';
          echo json_encode($arr);
          die;            
        }
        if($details['author'] != $user_id){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'You are not authorized to update this  post.';
          echo json_encode($arr);
          die; 
        }
        $args=array(
                  'post_type'=>'newsfeed',
                  'content'=>$content,
                  'author'=>$user_id
                );
        $where = array( 'ID' => $post_id );
        $update=$this->post->update_post($args,$where);

     if(isset($_FILES["image"])){

     $validextensions = array("jpeg", "jpg", "png");
     $temporary = explode(".", $_FILES["image"]["name"]);			
     $file_extension = end($temporary);
     $newfilename='';

         if (($_FILES["image"]["type"] == "image/png") || ($_FILES["image"]["type"] == "image/jpg") || ($_FILES["image"]["type"] == "image/jpeg")) {
            $sourcePath = $_FILES['image']['tmp_name']; 
            $t=time();
            $path = dirname($_SERVER["SCRIPT_FILENAME"]).'/images/posts/';
            $newfilename = $t.".".($file_extension == 'jpeg' ? 'jpg' : $file_extension);
	    $targetPath = $path.$newfilename; 
            move_uploaded_file($sourcePath,$targetPath);
            $this->post->add_post_meta($post_id,'feature_image',$newfilename);
          } 
      }

 	   $json[0]['result']=1;
	   $json[0]['msg']='News updated successfully';
	   echo json_encode($json);
	   die; 
     }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'please call required method';
        echo json_encode($arr);
     }
  }
 /*
  * delete post
  * url:  http://merrona.com/authscript/posts/deletefeed/2
  */
 function deletefeed(){
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $post_id=$this->helper->getUriSegment(2);
        $user_id=$_POST['user_id'];
        if(intval($user_id) == 0 || intval($post_id) == 0 ){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'please send all required data';
          echo json_encode($arr);
          die;
        }
        $details = $this->post->getpost($post_id);
        if(!$details){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'invalid post id';
          echo json_encode($arr);
          die;            
        }
        if($details['author'] != $user_id){
          $arr[0]['Result'] = 0;
	  $arr[0]['MSG'] = 'You are not authorized to delete this  post.';
          echo json_encode($arr);
          die; 
        }
        $where = array( 'ID' => $post_id );
        $update=$this->post->delete_post($where);

 	   $json[0]['result']=1;
	   $json[0]['msg']='News deleted successfully';
	   echo json_encode($json);
	   die; 
     }else{
        $arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'please call required method';
        echo json_encode($arr);
     }
 }


 /*
  * get_page_data
  * url:  http://merrona.com/authscript/posts/get_page_data
  */  
  function get_page_data(){
   $post_data=$this->post->getpost(1);
   $json[0]['result']=1;
   $json[0]['post_data']=$post_data['content'];
   echo json_encode($json); 
  }

  function testmeta(){
            var_dump($this->post->get_post_meta(2,'feature_image'));
  }
}
?>