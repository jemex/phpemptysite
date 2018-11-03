<?php
class chatting{
   function __construct(){
      $this->users = new users_model();
      $this->helper = new helper();
      $this->chatting = new chatting_model();
   }

  function index(){
     echo 'Please Call Required method';
  }


  /*
  * To fetch user recent messages list
  * url:  http://merrona.com/authscript/chatting/recentmessages/1
  */  
  function recentmessages(){
     $user_id=$this->helper->getUriSegment(2);
     $users = $this->chatting->RecentUserMessageList($user_id);

     if($users){
        $arr=$users;
     }else{
        $arr[0]['Result'] = '0';
        $arr[0]['MSG'] = 'No message found';
     }
     echo json_encode($arr);
  }
  
  /*
  * To send message
  * url:  http://merrona.com/authscript/chatting/postmessage
  */  
  function postmessage(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $sender=$_POST['sender_id'];
        $receiver=$_POST['reciever_id'];
        $message=$_POST['message'];
        if(intval($sender) == 0 || intval($receiver) == 0 || strlen($message) == 0){
            $arr[0]['Result'] = 0;
            $arr[0]['MSG'] = 'fill send all required data';
            echo json_encode($arr); 
            die;           
        }
        $args['sender_id']=$sender;
        $args['receiver_id']=$receiver;
        $args['message']=$message;
        $insert=$this->chatting->PostMessage($args);
        if($insert){

                $sender_detail=$this->users->getUserById($sender);
                $act_msg = 'message';
        	$message=$sender_detail['display_name']."@".$message;
        	$this->users->SendPushNotification($receiver,$message,$act_msg);

          	$arr[0]['Result'] = '1';
          	$arr[0]['insert_id'] = $insert;
	  	$arr[0]['MSG'] = 'Applied Successfully';
        }else{
		$arr[0]['Result'] = 0;
		$arr[0]['MSG'] = 'something went wrong';
        }
        echo json_encode($arr);
     }else{
       	$arr[0]['Result'] = 0;
	$arr[0]['MSG'] = 'something went wrong';
        echo json_encode($arr);
     }
  }

  /*
  * To get detail messages between users
  * url:  http://merrona.com/authscript/chatting/getmessages
  */  
  function getmessages(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $sender_id   =  $_POST["sender_id"];
	$reciever_id   =  $_POST["reciever_id"];
        if(intval($sender_id) == 0 || intval($reciever_id) == 0){
          $arr[0]['Result']=0;
          $arr[0]['MSG']='please send all required data';           
          echo json_encode($arr);
          die;
        }
        $results=$this->chatting->DetailMessage($sender_id,$reciever_id);
        if($results->num_rows > 0){
	   $i=0;
	  while($result = $results->fetch_assoc()){
	    $arr[$i]['msg_id']=(string)$result['ID'];
	    $arr[$i]['message']=$result['message'];
	    $arr[$i]['date']=date("d/m/y H:i", strtotime($result['sent_time']));
	    $arr[$i]['sender']=(string)$result['sender_id'];
	    $arr[$i]['receiver']=(string)$result['receiver_id'];
            $getStatus=$this->chatting->checkstatus($reciever_id,$sender_id);
            $getStatus1 = $getStatus->fetch_assoc();
            $arr[$i]['status']=$getStatus1['typing_status'];
	    $i++;
	  }		
	}else{
        	$arr[0]['Result'] = '0';
                $getStatus=$this->chatting->checkstatus($reciever_id,$sender_id);
                $getStatus1 = $getStatus->fetch_assoc();
        	$arr[0]['Result'] = '0';
                $arr[0]['status']=$getStatus1['typing_status'];
	  	$arr[0]['MSG'] = 'No new message found';
	}
     }else{
        $arr[0]['Result']=0;
        $arr[0]['MSG']='invalid request';
     }
    echo json_encode($arr);
  }

  /*
  * To get new messages
  * url:  http://merrona.com/authscript/chatting/getnewmessages
  */  
  function getnewmessages(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $sender_id   =  $_POST["sender_id"];
	$reciever_id   =  $_POST["reciever_id"];
        $id=$_POST['msg_id'];
        if(intval($sender_id) == 0 || intval($reciever_id) == 0 || intval($id) == 0){
          $arr[0]['Result']=0;
          $arr[0]['MSG']='please send all required data';           
          echo json_encode($arr);
          die;
        }
        $results=$this->chatting->NewMessage($sender_id,$reciever_id,$id);
        if($results->num_rows > 0){
	   $i=0;
	  while($result = $results->fetch_assoc()){
	    $arr[$i]['msg_id']=(string)$result['ID'];
	    $arr[$i]['message']=$result['message'];
	    $arr[$i]['date']=date("d/m/y H:i", strtotime($result['sent_time']));
	    $arr[$i]['sender']=(string)$result['sender_id'];
	    $arr[$i]['receiver']=(string)$result['receiver_id'];
            $getStatus=$this->chatting->checkstatus($reciever_id,$sender_id);
            $getStatus1 = $getStatus->fetch_assoc();
            $arr[$i]['status']=$getStatus1['typing_status'];
	    $i++;
	  }	
	}else{
                $getStatus=$this->chatting->checkstatus($reciever_id,$sender_id);
                $getStatus1 = $getStatus->fetch_assoc();
        	$arr[0]['Result'] = '0';
                $arr[0]['status']=$getStatus1['typing_status'];
                $arr[0]['receiver']=$reciever_id;
                //$arr[0]['status'] = "false";
	  	$arr[0]['MSG'] = 'No new message found';
	}
     }else{
        $arr[0]['Result']=0;
        $arr[0]['MSG']='invalid request';
     }
    echo json_encode($arr);
  }

  /*
  * To get old messages
  * url:  http://merrona.com/authscript/chatting/getoldmessages
  */  
  function getoldmessages(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $sender_id   =  $_POST["sender_id"];
	$reciever_id   =  $_POST["reciever_id"];
        $id=$_POST['msg_id'];
        if(intval($sender_id) == 0 || intval($reciever_id) == 0 || intval($id) == 0){
          $arr[0]['Result']=0;
          $arr[0]['MSG']='please send all required data';           
          echo json_encode($arr);
          die;
        }
        $results=$this->chatting->OldMessage($sender_id,$reciever_id,$id);
        if($results->num_rows > 0){
	   $i=0;
	  while($result = $results->fetch_assoc()){
	    $arr[$i]['msg_id']=(string)$result['ID'];
	    $arr[$i]['message']=$result['message'];
	    $arr[$i]['date']=date("d/m/y H:i", strtotime($result['sent_time']));
	    $arr[$i]['sender']=(string)$result['sender_id'];
	    $arr[$i]['receiver']=(string)$result['receiver_id'];
	    $i++;
	  }		
	}else{
        	$arr[0]['Result'] = '0';
	  	$arr[0]['MSG'] = 'No new message found';
	}
     }else{
        $arr[0]['Result']=0;
        $arr[0]['MSG']='invalid request';
     }
    echo json_encode($arr);
  }

  /*
     * To get old messages
     * url:  http://merrona.com/authscript/chatting/getUsertyping
     */

    function getUsertyping() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sender = $_POST['sender_id'];
            $receiver = $_POST['reciever_id'];
            $status = $_POST['status'];
          //  echo $status;
            if (intval($sender) == 0 || intval($receiver) == 0) {
                $arr[0]['Result'] = 0;
                $arr[0]['MSG'] = 'fill send all required data';
                echo json_encode($arr);
                die;
            }

            $checkexist = $this->chatting->checkstatus($sender, $receiver);

            if ($status) {
                // if($checkexist->num_rows > 0){
                $result = $checkexist->fetch_assoc();
               // print_r($result);
                if (empty($result)) {
                    $args['sender_id'] = $sender;
                    $args['receiver_id'] = $receiver;
                    $args['typing_status'] = "user_typing";
                    $insert = $this->chatting->insertStatus($args);
                    $arr[0]['Result'] = '1';
                    $arr[0]['status'] = "typing";
                    $arr[0]['MSG'] = 'User Typing';
                } else {
                    $typing_status="user_typing";
                    $updateStatus = $this->chatting->updateStatusMessage($sender, $receiver,$typing_status);
                    $arr[0]['Result'] = '1';
                    $arr[0]['status'] = "typing";
                    $arr[0]['MSG'] = 'User Typing';
                }
                // }
            } else {
                $typing_status="stopped";
                $updateStatus = $this->chatting->updateStatusMessage($sender, $receiver,$typing_status);
                $arr[0]['Result'] = '1';
                $arr[0]['status'] = "stopped";
                $arr[0]['MSG'] = 'message successfully sent.';
            }
            echo json_encode($arr);
        } else {
            $arr[0]['Result'] = 0;
            $arr[0]['MSG'] = 'something went wrong';
            echo json_encode($arr);
        }
    }
}