<?php
class user{
  function __construct(){
      $this->users = new users_model();
      $this->helper = new helper();
  }
  function index(){
     echo 'Please Call Required method';
  }

  function allusers(){
      $user_id=$this->helper->getUriSegment(2);
      if(intval($user_id) == 0){
 	   $json[0]['result']=0;
	   $json[0]['msg']='Please login';
	   echo json_encode($json);
	   die;       
      }
      $allusers = $this->users->getAllUsers($user_id);
      if($allusers->num_rows > 0){
         $i=0;
         while($data=$allusers->fetch_assoc()){
           $json[$i]=$this->users->getUserById($data['ID']);
           $i++;
         }
         echo json_encode($json);
	 die;
      }else{
        $json[0]['result']=0;
	$json[0]['msg']='No user found';
	echo json_encode($json);
	die;
      }
  }

  function allonlineusers(){
      $user_id=$this->helper->getUriSegment(2);
      if(intval($user_id) == 0){
 	   $json[0]['result']=0;
	   $json[0]['msg']='Please login';
	   echo json_encode($json);
	   die;       
      }
      $allusers = $this->users->getAllOnlineUsers($user_id);
      if($allusers->num_rows > 0){
         $i=0;
         while($data=$allusers->fetch_assoc()){
           $json[$i]=$this->users->getUserById($data['user_id']);
           $i++;
         }
         echo json_encode($json);
	 die;
      }else{
        $json[0]['result']=0;
	$json[0]['msg']='No user found';
	echo json_encode($json);
	die;
      }
  }

 /*
  * device token dummy
  * url:  http://merrona.com/authscript/user/devicetoken
  */  
  function devicetoken(){
        $json[0]['result']=1;
	$json[0]['msg']='Dummy Url';
	echo json_encode($json);
	die;      
  }

  function login(){
       if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $useremail=$_POST['useremail'];
        $password = $_POST['password'];
        $device_id = (isset($_POST['device_id']) ? $_POST['device_id'] : '');
        if(strlen($useremail) == 0 || strlen($password) == 0){
	   $json[0]['result']=0;
	   $json[0]['msg']='Please fill all required data';
	   echo json_encode($json);
	   die;
        }
        if(!($this->users->check_useremail_exist($useremail))){
	   $json[0]['result']=2;
	   $json[0]['msg']='User email not registered';
	   echo json_encode($json);
	   die;
        }
        $data=$this->users->logincheck($useremail,$password);
       //var_dump($data);
        if(!$data){
           $json[0]['result']=0;
           $json[0]['msg']='invalid username or password';
	   echo json_encode($json);
	   die;
        }
        if(strlen($device_id) != 0 ){
          $this->users->RegisterDeviceToken($device_id,'ANDROID',$data['user_id']);
        }
        $json[0]=$data;
        $json[0]['result']=1;
	echo json_encode($json);
	die;
     }else{
	   $json[0]['result']=0;
	   $json[0]['msg']='Please fill all required data';
	   echo json_encode($json);
	   die;       
     }
  }
function signup(){
      if($_SERVER['REQUEST_METHOD'] === 'POST'){
     $age=$_POST['age'];
     $useremail=$_POST['useremail'];
     $password=$_POST['password'];
     if(strlen($age) == 0 || strlen($useremail) == 0 || strlen($password) == 0){
	$json[0]['result']=0;
	$json[0]['msg']='Please fill all required data';
	echo json_encode($json);
	die;
     }
     if($this->users->check_useremail_exist($useremail)){
	$json[0]['result']=2;
	$json[0]['msg']='Email already registered';
	echo json_encode($json);
	die;	
     }
        $args['display_name']=$_POST['display_name'];
	$args['age']=$age;
	$args['useremail']=$useremail;
	$args['password']=$this->users->password_hash($password);
        $user_id=$this->users->insertUser($args);
        $json[0]['result']=1;
	$json[0]['msg']='User registered';
	$json[0]=$this->users->getUserById($user_id);
        echo json_encode($json);
  
  $code=mt_rand(1000,9999);
  $args['reset_key']=$code;
  $update=$this->users->updateUser($args,$user_id);
  $to = $useremail;
  $subject = 'Merrona app registration ';
  $body = '<p>Greetings,</p><br/>';			
  $body .= '<p>Welcome come to Merrona!</p>';
  $body .= '<p>Thanks for creating a Merrona account. Your are one step away from being a gentleman.</p><br/>';
  $body .= '<p>Your login information:</p><br/>';
  $body .="<table><tr><th>Username</th><td>".$useremail."</td></tr><tr><th>Password</th><td>****</td></tr><tr><th>Authentication code</th><td>".$code."</td></tr></table>";	
  $body.="<br /> <br /><h4>Good Luck!!! </h4>";

  $this->users->mail($to,$subject,$body);
	die;	
  }else{
     $json[0]['result']=0;
     $json[0]['msg']='Please fill all required data';
     echo json_encode($json);
     die;   
  }
}

  /*
  * update user
  * url:  http://merrona.com/authscript/user/update/40
  */  
  function update(){
    $user_id = $this->helper->getUriSegment(2);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $age=$_POST['age'];
      $useremail=$_POST['useremail'];
      $contact=($_POST['contact'] ?: '');
      $description=($_POST['description'] ?: '');
      $display_name=($_POST['display_name'] ?: '');
      if(strlen($age) == 0 || strlen($useremail) == 0 || intval($user_id) == 0){
	$json[0]['result']=0;
	$json[0]['msg']='Please fill all required data';
	$json[0]['user']=intval($user_id);
	echo json_encode($json);
	die();
      }
      	if($this->users->check_useremail_exist($useremail)){
            $userdata=$this->users->check_useremail_exist($useremail);
            if($userdata['ID'] != $user_id){
		$json[0]['result']=2;
		$json[0]['msg']='Email already registered with another user';
		echo json_encode($json);
		die();	
            }
	}
        
        $validextensions = array("jpeg", "jpg", "png");
     	$temporary = explode(".", $_FILES["profile_pic"]["name"]);			
     	$file_extension = end($temporary);
     	$newfilename='';
     	if (($_FILES["profile_pic"]["type"] == "image/png") || ($_FILES["profile_pic"]["type"] == "image/jpg") || ($_FILES["profile_pic"]["type"] == "image/jpeg")) {
        	$sourcePath = $_FILES['profile_pic']['tmp_name']; 
        	$t=time();
        	$path = dirname($_SERVER["SCRIPT_FILENAME"]).'/images/profile_pic/';
        	$newfilename = $t.".".$file_extension;;
		$targetPath = $path.$newfilename; 
        	move_uploaded_file($sourcePath,$targetPath);
                
	     $args['profile_pic']=$GLOBALS['PROFILE_IMAGE_URL'].$newfilename;
      	}

	$args['age']=$age;
	$args['useremail']=$useremail;
	$args['contact']=$contact;
	$args['description']=$description;
	$args['display_name']=$display_name;

        $user_updated=$this->users->updateUser($args,$user_id);
        if($user_updated){
           $json[0]['result']=1;
	   $json[0]['msg']='User updated';
	   $json[0]['userdata']=$this->users->getUserById($user_id);
        }else{
           $json[0]['result']=0;
	   $json[0]['msg']='Not updated';
        }
         echo json_encode($json);
	 die;	
    }else{
      $json[0]['result']=0;
      $json[0]['msg']='Please fill all required data';
      echo json_encode($json);
      die; 
    }    
  }

  /*
  * Add Wallet Balance
  * url:  http://merrona.com/authscript/user/add_wallet/40
  */ 
  function add_wallet(){
    $user_id = $this->helper->getUriSegment(2);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $amount=$_POST['amount'];
        $transaction_id=$_POST['transaction_id'];
        if(intval($user_id) == 0 || intval($amount) == 0 || strlen($transaction_id) == 0){
	  $json[0]['result']=0;
	  $json[0]['msg']='Please fill all required data';
	  echo json_encode($json);
	  die();
        }
        $userdata=$this->users->getUserById($user_id);
        if($userdata){
            $balance=$userdata['wallet_balance'];
            //$total=number_format((floatval($balance)+floatval($amount)), 2);
            $total=floatval($balance)+floatval($amount);
            $args['wallet_balance']=$total;
            $this->users->updateUser($args,$user_id);

            $args_w['user_id']=$user_id;
            $args_w['transaction_type']='paypal_add';
            $args_w['amount']=$amount;
            $args_w['transfer_id']=$transaction_id;
            $args_w['status']='success';

            $this->users->add_wallet_history($args_w);
            $json[0]['result']=1;
           $json[0]['msg']='wallet update succesfully '.$total.' '.$balance;
           echo json_encode($json);

  $to = $userdata['useremail'];
  $subject = 'Merrona wallet';
  $body = '<h3>Hello    ' . $userdata["display_name"] . ',</h3>';
	
  $body .= '<p>This email is to notify you that you just added $'.$amount.' to your Merrona wallet.</p>';
  $body .= '<p>Your available wallet balance $'.$total.'</p>';
  $body.="<br /><p>Stay Classy!!</p><br/><h4>Good luck!! </h4>";

  $this->users->mail($to,$subject,$body);

           die;
        }else{
           $json[0]['result']=0;
           $json[0]['msg']='Please check user data';
           echo json_encode($json);
           die; 
        }
    }else{
      $json[0]['result']=0;
      $json[0]['msg']='Please fill all required data';
      echo json_encode($json);
      die; 
    }
  }
  
  /*
  * Add Wallet Balance
  * url:  http://merrona.com/authscript/user/add_coins/40
  */
  function add_coins(){
    $user_id = $this->helper->getUriSegment(2);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $amount=$_POST['amount'];
        $coins=$_POST['coins'];
        $transaction_id=$_POST['transaction_id'];
        if(intval($user_id) == 0 || intval($coins) == 0 || strlen($transaction_id) == 0){
	  $json[0]['result']=0;
	  $json[0]['msg']='Please fill all required data';
	  echo json_encode($json);
	  die();
        }
        $userdata=$this->users->getUserById($user_id);
        if($userdata){
            $balance=$userdata['wallet_balance'];
            //$total=number_format((floatval($balance)+floatval($amount)), 2);
            $total=floatval($balance)+floatval($coins);
            $args['wallet_balance']=$total;
            $this->users->updateUser($args,$user_id);

            $args_w['user_id']=$user_id;
            $args_w['transaction_type']='paypal_add';
            $args_w['amount']=$amount;
            $args_w['transfer_id']=$transaction_id;
            $args_w['status']='success';

            $this->users->add_wallet_history($args_w);
            $json[0]['result']=1;
            $json[0]['total_coins']=$total;
           echo json_encode($json);

  $to = $userdata['useremail'];
  $subject = 'Merrona balance';
  $body = '<h3>Hello    ' . $userdata["display_name"] . ',</h3>';
  $body .= '<p>This email is to notify you that you just added '.$amount.' coins to your Merrona account.</p>';
  $body .= '<p>Your available coins '.$total.'</p>';
  $body.="<br /><p>Stay Classy!!</p><br/><h4>Good luck!! </h4>";

  $this->users->mail($to,$subject,$body);

           die;
        }else{
           $json[0]['result']=0;
           $json[0]['msg']='Please check user data';
           echo json_encode($json);
           die; 
        }
    }else{
      $json[0]['result']=0;
      $json[0]['msg']='Please fill all required data';
      echo json_encode($json);
      die; 
    }
  }

  /*
  * Wallet history
  * url:  http://merrona.com/authscript/user/wallet_history/40/2
  */ 
  function wallet_history(){
    $user_id = $this->helper->getUriSegment(2);
   
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
       if(intval($user_id) == 0){
	  $json[0]['result']=0;
	  $json[0]['msg']='Please fill all required data';
	  echo json_encode($json);
	  die();
       }
       $page = ($this->helper->getUriSegment(3) ?: 1 );
       $page_id=intval($page)-1;
       $wallet_history=$this->users->wallet_history($user_id,$page_id);
      // print_r($wallet_history);
       if($wallet_history){
          $i=0;
          while($row = $wallet_history->fetch_assoc()){
             $json[$i]=$row;
             $i++;
          }
          echo json_encode($json);
          die;
       }else{
          $json[0]['result']=0;
          $json[0]['msg']='No record found';
          echo json_encode($json);
          die;
       }
    }else{
    
      $json[0]['result']=0;
      $json[0]['msg']='Please call required data';
      echo json_encode($json);
      die; 
    }
  }


  /*
  * Withdraw request
  * url:  http://merrona.com/authscript/user/wallet_withdraw/40
  */ 
  function wallet_withdraw(){
    $user_id = $this->helper->getUriSegment(2);
    if($_SERVER['REQUEST_METHOD'] === 'POST'){
       $amount=$_POST['amount'];
       $paypal_id=$_POST['paypal_id'];
       if(intval($user_id) == 0 || intval($amount) == 0 || strlen($paypal_id) == 0){
	  $json[0]['result']=0;
	  $json[0]['msg']='Please fill all required data';
	  echo json_encode($json);
	  die();
       }
       $userdata=$this->users->getUserById($user_id);
       if($userdata['wallet_balance'] < $amount ){
	  $json[0]['result']=2;
	  $json[0]['msg']='Insuficient coins';
	  echo json_encode($json);
	  die();          
       }
       $total=floatval($userdata['wallet_balance'])-floatval($amount);
       $args['wallet_balance']=$total;
       $this->users->updateUser($args,$user_id);
       
            $args_w['user_id']=$user_id;
            $args_w['transaction_type']='paypal_withdraw';
            $args_w['amount']=$amount;
            $args_w['transfer_id']='';
            $args_w['wallet_balance']=$total;
            $args_w['status']='pending';

         $wallethistory_id=  $this->users->add_wallet_history($args_w);
         /*** Add paypal ID (14-8-2017) ****/
         $args_w1=array("wallethistory_id"=>$wallethistory_id,"	meta_key"=>"user_paypal_id","meta_value"=>$paypal_id);
         $this->users->add_wallet_paypal_id($args_w1);
         /*** Add paypal ID (14-8-2017) ****/
      $json[0]['result']=1;
      $json[0]['msg']='Request Recieved';
      echo json_encode($json);

  $to = $userdata['useremail'];
  $subject = 'Merrona wallet';
  $body = '<h3>Hello    ' . $userdata["display_name"] . ',</h3>';			
  $body .= '<p>You request $'.$amount.' to withdraw from wallet. Your available wallet balance $'.$total.'.</p>';
  $body.="<br /> <br /><h4>Good luck! </h4>";

  $this->users->mail($to,$subject,$body);


  $to = $GLOBALS['Admin_email'];
  $subject = 'Merrona withdraw request';
  $body = '<h3>Hello,</h3>';			
  $body .= '<p>'.$userdata["display_name"].' request $'.$amount.' to withdraw from wallet. Please approve request to process withdraw.</p>';
  $body.="<br /> <br /><h4>Thanks! </h4>";

  $this->users->mail($to,$subject,$body);

      die; 

    }else{
      $json[0]['result']=0;
      $json[0]['msg']='Please call required data';
      echo json_encode($json);
      die; 
    }
  }

function verifyuser(){
   if(!empty($_REQUEST)){
       $useremail = $_REQUEST['useremail'];
       $code = $_REQUEST['code'];
       $user_id = $_REQUEST['user_id'];
     if(strlen($useremail) == 0){
	$json[0]['result']=0;
	$json[0]['msg']='Please fill all required data';
	echo json_encode($json);
	die;
     }

     if($_REQUEST['action'] && $_REQUEST['action']=='codeauthenticate'){
       if($this->users->resetcodeauthenticate($useremail,$code,$user_id)){
        $args['status']='verify';
        $update=$this->users->updateUser($args,$user_id);
      	$json[0]['user_id']=$_REQUEST['user_id'];
      	$json[0]['result']=1;
	$json[0]['msg']='code authenticated';
	echo json_encode($json);
	die;
       }else{
      	$json[0]['result']=0;
	$json[0]['msg']='invalid code';
	echo json_encode($json);
	die;
       }
     }
  }else{
     $json[0]['result']=0;
     $json[0]['msg']='Please fill all required data';
     echo json_encode($json);
     die;   
  }

}

  /*
  * change user status
  * url:  http://merrona.com/authscript/user/useraction
  */ 

function useraction(){
   if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $user_id=$_POST['user_id'];
      $token=$_POST['device_id'];
      $action=$_POST['action'];
      if(intval($user_id) == 0 || strlen($token) == 0 || strlen($action) == 0 ){
        $json[0]['result']=0;
        $json[0]['msg']='Please fill required data';
        echo json_encode($json);
        die;
      }

      if($_POST['action'] == 'logout'){
         $allusers=$this->users->userLogOut($user_id,$token);
      }else if($_POST['action'] == 'offline'){
         $allusers=$this->users->userOffline($user_id,$token);
      }else if($_POST['action'] == 'online'){
         $allusers=$this->users->userOnline($user_id,$token);
      }
      if(!$allusers){
	$json[0]['result']=0;
	$json[0]['msg']='No user found';
	$json[0]['userdata']=$this->users->getUserById($user_id);
	echo json_encode($json);
	die;
      }else{
	$json[0]['result']=1;
	$json[0]['msg']='User Updated';
        echo json_encode($json);
	die;
      }

   }else{
      $json[0]['result']=0;
      $json[0]['msg']='Please call required method';
      echo json_encode($json);
      die; 
   }

}

  /*
  * forget  password
  * url:  http://merrona.com/authscript/user/forgetpassword
  */ 
  function forgetpassword(){
   if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $useremail=$_POST['useremail'];
      if(strlen($useremail) == 0){
	$json[0]['result']=0;
	$json[0]['msg']='Please fill all required data';
	echo json_encode($json);
	die;
      }

      if(!$this->users->check_useremail_exist($useremail)){
           $json[0]['result']=2;
	   $json[0]['msg']='Email not registered';
           echo json_encode($json);
	   die;	        
      }
            $userdata=$this->users->check_useremail_exist($useremail);
            if($userdata['reset_key']){
                $code=$userdata['reset_key'];
            }else{
                $code=mt_rand(1000,9999);
                $args['key']=$code;
                $update=$this->users->updateUser($args,$userdata['ID']);
            }
            $to = $useremail;
            $subject = 'Merrona app password reset ';
            $body = '<h3>Hello    ' . $userdata["display_name"] . ',</h3>';			
            $body .= '<p>Someone requested that the password be reset for the following account:</p>';
            $body .="<table><tr><th>Username</th><td>".$useremail."</td></tr><tr><th>Reset Code</th><td>".$code."</td></tr></table>";
            $body.="<p>Please put this code: ".$code." to reset password.</p>";		
            $body.="<br /> <br /><h4>Good luck! </h4>";

            $this->users->mail($to,$subject,$body);

		$json[0]['user_id']=$userdata['ID'];    
		$json[0]['result']=1;
		$json[0]['msg']='Code sent on registered email';
		echo json_encode($json);
		die;
    
    }else{
       $json[0]['result']=0;
       $json[0]['msg']='Please fill all required data';
       echo json_encode($json);
       die;   
    }
  }

  /*
  * forget  password codeauthenticate
  * url:  http://merrona.com/authscript/user/codeauthenticate
  */ 
  function codeauthenticate(){
   if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $useremail=$_POST['useremail'];
      $code=$_POST['code'];
      if(strlen($useremail) == 0 || strlen($code) == 0){
	$json[0]['result']=0;
	$json[0]['msg']='Please fill all required data';
	echo json_encode($json);
	die;
      }
      if($this->users->codeauthenticate($useremail,$code)){
        $userdata=$this->users->check_useremail_exist($useremail);
      	$json[0]['user_id']=$userdata['ID'];
      	$json[0]['result']=1;
	$json[0]['msg']='code authenticated';
	echo json_encode($json);
	die;
      }else{
      	$json[0]['result']=0;
	$json[0]['msg']='invalid code';
	echo json_encode($json);
	die;
      }
    }else{
       $json[0]['result']=0;
       $json[0]['msg']='Please call required method';
       echo json_encode($json);
       die;   
    }
  }

  function mailtest(){
     $this->users->mail('bhuneshsatpada.oss@gmail.com','test','tetsing');
  }

  /*
  * reset  password
  * url:  http://merrona.com/authscript/user/resetpassword
  */ 
  function resetpassword(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $user_id=$_POST['user_id'];
        $newpass = $_POST['newpassword'];
        if(intval($user_id) == 0 || strlen($newpass) == 0){
	   $json[0]['result']=0;
	   $json[0]['msg']='Please fill all required data';
	   echo json_encode($json);
	   die;
        }
        $check=true;
       if(strlen($_REQUEST['oldpassword']) != 0){
            $oldpass=$_REQUEST['oldpassword'];
            $check=$this->users->authenticatepassword($user_id,$this->users->password_hash($oldpass));
       }
       if($check){
            $args=array('password'=>$this->users->password_hash($newpass));
            $userdata=$this->users->updateUser($args,$user_id);
            if($userdata){
               $useremail=$userdata['useremail'];
               $to = $useremail;
  $subject = 'Merrona app password update ';
  $body = '<h3>Hello    ' . $userdata["display_name"] . ',</h3>';			
  $body .= '<p>Someone requested that the password be reset for the following account:</p>';
  $body .="<table><tr><th>Username</th><td>".$useremail."</td></tr><tr><th>New Password</th><td>".$newpass."</td></tr></table>";
  $body.="<br /> <br /><h4>Good luck! </h4>";

     $this->users->mail($to,$subject,$body);
		$json[0]['result']=1;
		$json[0]['msg']='password updated';
		echo json_encode($json);
		die;
            }else{
		$json[0]['result']=0;
		$json[0]['msg']='error. password not updated';
		echo json_encode($json);
		die;
            }
       }else{
		$json[0]['result']=0;
		$json[0]['msg']='invalid old password';
		echo json_encode($json);
		die;
       }
     }else{
       $json[0]['result']=0;
       $json[0]['msg']='Please fill all required data';
       echo json_encode($json);
       die;         
     }
  }

  function random_num($size) {
	$alpha_key = '';
	$keys = range('A', 'Z');

	for ($i = 0; $i < 5; $i++) {
		$alpha_key .= $keys[array_rand($keys)];
	}

	$length = $size - 5;

	$key = '';
	$keys = range(0, 9);

	for ($i = 0; $i < $length; $i++) {
		$key .= $keys[array_rand($keys)];
	}

	return $alpha_key . $key;
    }

  /*
  * email an agent
  * url:  http://merrona.com/authscript/user/emailagent
  */ 
  function emailagent(){
     if($_SERVER['REQUEST_METHOD'] === 'POST'){
        $fname=$_POST['first_name'];
        $lname=$_POST['last_name'];
        $email=$_POST['email'];
        $phone=$_POST['phone'];
        $message=$_POST['message'];
        if(strlen($fname) == 0 || strlen($lname) == 0 || strlen($email) == 0 || strlen($phone) == 0 || strlen($message) == 0){
           $json[0]['result']=0;
           $json[0]['msg']='Please fill all required data';
           echo json_encode($json);
           die;
        }

        $args['first_name']=$fname;
        $args['last_name']=$lname;
        $args['email']=$email;
        $args['phone']=$phone;
        $args['message']=$message;
        $ticket=$this->random_num(14);
        $args['ticket_code']=$ticket;

        $this->helper->db_insert($args,'tb_agentcontact');
 
        $to = $email;
        $subject = 'Contact request recieved #'.$ticket;
        $body = '<h3>Hello '.$fname. ',</h3>';			
  $body .= '<p>Your contact request recived. Your ticket toekn number is '.$ticket.' We will check and response you soon.</p>';
  $body.="<br /> <br /><h4>Thanks! </h4>";
     $this->users->mail($to,$subject,$body);

        $to = $GLOBALS['Admin_email'];
        $subject = 'Contact request recieved #'.$ticket;
        $body = '<h3>Hello,</h3>';			
        $body .= '<p>One new contact request recived. Ticket token number '.$ticket.'. Details as follows:</p>';
        $body .="<table><tr><th>First Name</th><td>".$fname."</td></tr><tr><th>Last Name</th><td>".$lname."</td></tr><tr><th>Email</th><td>".$email."</td></tr><tr><th>Phone</th><td>".$phone."</td></tr><tr><th>Message</th><td>".$message."</td></tr></table>";
  $body.="<br /> <br /><h4>Thanks! </h4>";
     $this->users->mail($to,$subject,$body,$email);
     
		$json[0]['result']=1;
		$json[0]['msg']='Contact request recieved';
		echo json_encode($json);
		die;
     }else{
       $json[0]['result']=0;
       $json[0]['msg']='Please call required method';
       echo json_encode($json);
     }
  }

} 
?>