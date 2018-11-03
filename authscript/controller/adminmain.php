<?php

class adminmain {

    function __construct() {
        $this->users = new users_model();
        $this->betting = new betting_model();
        $this->helper = new helper();
        $this->model = new auth_model();

        $protocol = ((!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] != 'off') || $_SERVER['SERVER_PORT'] == 443) ? "https://" : "http://";
        $currentUrl = $protocol . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

        if (trim($currentUrl) != trim($GLOBALS['ep_dynamic_url'] . "adminmain")) {
            if (trim($currentUrl) == trim($GLOBALS['ep_dynamic_url'] . "adminmain/forgot")) {
                
            } else {
                if (!isset($_SESSION['easyphp_sessionid']) && $_SESSION['easyphp_sessionid'] == '')
                    header("Location: " . $GLOBALS['ep_dynamic_url'] . "adminmain");
            }
        }
    }

    function index() {
        if (isset($_SESSION['easyphp_sessionid']) && $_SESSION['easyphp_sessionid'] != '') {
            $data['ep_title'] = "Dashboard"; //setting title name
            $data['view_page'] = "dashboard.php"; //controller view page
            $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
            $data['ep_footer'] = 'footer.php';
            return $data;
        }
        if (!empty($_POST)) {
            $data['post'] = $_POST;
            $email = $_POST['username'];
            $password = $_POST['password'];
            $remember = $_POST['remember'];
            $email = strip_tags($email);
            $password = strip_tags($password);
            $remember = strip_tags($remember);
            $password = md5($password);
            $result = $this->model->adminlogin($email, $password, $remember);
            if ($result) {
                $data['ep_title'] = "Dashboard"; //setting title name
                $data['view_page'] = "dashboard.php"; //controller view page
                $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
                $data['ep_footer'] = 'footer.php';
                return $data;
                die();
            } else {
                $data['errors'] = array(array("Username and Password do not match, Please try again"));
            }
        }
        $data['ep_title'] = "Login"; //setting title name
        $data['view_page'] = "admin/login.php"; //controller view page
        return $data;
    }

    function adminlogout() {
        unset($_SESSION['easyphp_sessionid']);
        header("Location: " . $GLOBALS['ep_dynamic_url'] . "adminmain");
    }

    function allusers() {

        $allusers = $this->users->getAllUsers(52);
        if ($allusers->num_rows > 0) {
            $i = 0;
            while ($data = $allusers->fetch_assoc()) {
                $json[$i] = $this->users->getUserById($data['ID']);
                $i++;
            }
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No user found';
        }

        $data['ep_title'] = "All Users"; //setting title name
        $data['view_page'] = "admin/userlist.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $json;
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }
    
    function userprofile() {

        $user_id = $this->helper->getUriSegment(2);
        $userdetails = $this->users->getUserById($user_id);

        $data['view_page'] = "admin/user_profile.php"; //controller view page 
        $data['ep_title'] = "User Profile"; //setting title name
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $userdetails;
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }

    function useraccountHistory() {

        $user_id = $this->helper->getUriSegment(2);

        $page = ($this->helper->getUriSegment(3) ?: 1 );
        $page_id = intval($page) - 1;
        $wallet_history = $this->users->wallet_history($user_id, $page_id);

        if ($wallet_history) {
            $i = 0;
            while ($row = $wallet_history->fetch_assoc()) {
                $json[$i] = $row;
                $i++;
            }
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No record found';
        }

        $max = 20;
        $limit = ($page - 1) * $max;
        $prev = $page - 1;
        $next = $page + 1;
        $limits = (int) ($page - 1) * $max;
        $pageSlug = $GLOBALS['ep_dynamic_url'] . 'adminmain/useraccountHistory/' . $user_id;

        $query = "SELECT * FROM tb_wallethistory where user_id='$user_id'";

        $data['ep_title'] = "User Account History"; //setting title name
        $data['view_page'] = "admin/user_account_history.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $json;
        $data['pagination'] = $this->helper->pagination($query, $page, $prev, $next, $pageSlug, $max);
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }

    function accountHistory() {

        $page = ($this->helper->getUriSegment(2) ?: 1 );
        $page_id = intval($page) - 1;
        $wallet_history = $this->users->complete_wallet_history($page_id);
        if ($wallet_history) {
            $i = 0;
            while ($row = $wallet_history->fetch_assoc()) {
                $username = $this->users->getUserById($row['user_id']);
                $row["username"] = $username['display_name'];
                $json[$i] = $row;
                $i++;
            }
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No record found';
        }

        $max = 20;
        $limit = ($page - 1) * $max;
        $prev = $page - 1;
        $next = $page + 1;
        $limits = (int) ($page - 1) * $max;
        $pageSlug = $GLOBALS['ep_dynamic_url'] . 'adminmain/accountHistory';
        $query = "select tb_wallethistory.*,tb_gamerequest.amount as bet_amount,tb_gamerequest.commission from tb_wallethistory INNER JOIN tb_gamerequest on tb_wallethistory.transfer_id=tb_gamerequest.ID where transaction_type !='paypal_withdraw' order by `ID`";

        $data['ep_title'] = "User Account History"; //setting title name
        $data['view_page'] = "admin/account_history.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $json;
        $data['pagination'] = $this->helper->pagination($query, $page, $prev, $next, $pageSlug, $max);
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }

    function userBetHistory() {

        $page = ($this->helper->getUriSegment(3) ?: 1 );
        $page_id = intval($page) - 1;

        $user_id = $this->helper->getUriSegment(2);
        if (intval($user_id) == 0) {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'Please fill required data';
            json_encode($json);
        } else {
            $all = $this->betting->UserBetWithpaginationHistory($user_id, $page_id);
            if (!$all) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'No bet available';
                json_encode($json);
            } else {
                $i = 0;
                while ($data = $all->fetch_assoc()) {
                    $user1 = ($data['sender'] == $user_id ? $data['receiver'] : $data['sender']);
                    $game_detail = $this->betting->GameDetails($data['game_id']);

                    $winner = $data['winner'];
                    $betteam = $data['betteam'];
                    $sender = $data['sender'];
                    $receiver = $data['receiver'];

                    $json[$i]['id'] = $data['ID'];
                    $json[$i]['game_id'] = $data['game_id'];

                    $json[$i]['betteam'] = ($data['sender'] == $user_id ? $data['betteam'] : ($data['betteam'] == 'hometeam' ? 'awayteam' : 'hometeam'));

                    $json[$i]['home_team'] = $game_detail['hometeam'];
                    $json[$i]['away_team'] = $game_detail['awayteam'];
                    $json[$i]['matchtime'] = $game_detail['matchtime'];
                    $json[$i]['details'] = $game_detail['details'];
                    $json[$i]['league_name'] = $game_detail['league_name'];
                    $json[$i]['display_region'] = $game_detail['display_region'];
                    $json[$i]['c_matchtime'] = date('Y-m-d H:i:s', $game_detail['matchtime']);
                    $json[$i]['winner'] = $data['winner'];

                    if ($winner != 'draw') {
                        $winner_id = ($winner == $betteam ? $sender : $receiver);
                        $winner_detail = $this->users->getUserById($winner_id);
                        $json[$i]['winner_id'] = $winner_detail['user_id'];
                        $json[$i]['winner_name'] = $winner_detail['display_name'];
                        $json[$i]['winner_email'] = $winner_detail['useremail'];
                        $json[$i]['winner_age'] = $winner_detail['age'];
                        $json[$i]['winner_pic'] = $winner_detail['profile_pic'];
                    }

                    $json[$i]['amount'] = $data['amount'];
                    $i++;
                }
                json_encode($json);
            }
        }
        $max = 10;
        $limit = ($page - 1) * $max;
        $prev = $page - 1;
        $next = $page + 1;
        $limits = (int) ($page - 1) * $max;
        $pageSlug = $GLOBALS['ep_dynamic_url'] . 'adminmain/userbethistory/' . $user_id;
        $query = "SELECT * FROM tb_gamerequest where (`sender`=$user_id || `receiver`=$user_id ) && `status`='complete'";
        $data['ep_title'] = "User Bet History"; //setting title name
        $data['view_page'] = "admin/user_bet_history.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $json;
        $data['pagination'] = $this->helper->pagination($query, $page, $prev, $next, $pageSlug, $max);
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }

    function completeBet() {

        $page = ($this->helper->getUriSegment(2) ?: 1 );
        $page_id = intval($page) - 1;
        $all = $this->betting->CompleteBet($page_id);

        if (!$all) {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No bet available';
            json_encode($json);
        } else {
            $i = 0;
            while ($data = $all->fetch_assoc()) {
                $game_detail = $this->betting->GameDetails($data['game_id']);
                $winner = $data['winner'];
                $betteam = $data['betteam'];
                $sender = $data['sender'];
                $receiver = $data['receiver'];

                $json[$i]['id'] = $data['ID'];
                $json[$i]['game_id'] = $data['game_id'];
                if ($data['betteam'] == "hometeam")
                    $json[$i]['betteam'] = $game_detail['hometeam'];
                else if ($data['betteam'] == "awayteam")
                    $json[$i]['betteam'] = $game_detail['awayteam'];

                $json[$i]['home_team'] = $game_detail['hometeam'];
                $json[$i]['away_team'] = $game_detail['awayteam'];
                $json[$i]['matchtime'] = $game_detail['matchtime'];
                $json[$i]['details'] = $game_detail['details'];
                $json[$i]['league_name'] = $game_detail['league_name'];
                $json[$i]['display_region'] = $game_detail['display_region'];
                $json[$i]['c_matchtime'] = date('Y-m-d H:i:s', $game_detail['matchtime']);

                if ($data['winner'] == "hometeam")
                    $json[$i]['winner'] = $game_detail['hometeam'];
                else if ($data['winner'] == "awayteam")
                    $json[$i]['winner'] = $game_detail['awayteam'];

                //  $json[$i]['winner']=$data['winner'];

                if ($winner != 'draw') {
                    $winner_id = ($winner == $betteam ? $sender : $receiver);
                    $winner_detail = $this->users->getUserById($winner_id);
                    $json[$i]['winner_id'] = $winner_detail['user_id'];
                    $json[$i]['winner_name'] = $winner_detail['display_name'];
                    $json[$i]['winner_email'] = $winner_detail['useremail'];
                    $json[$i]['winner_age'] = $winner_detail['age'];
                    $json[$i]['winner_pic'] = $winner_detail['profile_pic'];
                }

                $json[$i]['amount'] = $data['amount'];
                $i++;
            }
            json_encode($json);
        }
        $max = 20;
        $limit = ($page - 1) * $max;
        $prev = $page - 1;
        $next = $page + 1;
        $limits = (int) ($page - 1) * $max;
        $pageSlug = $GLOBALS['ep_dynamic_url'] . 'adminmain/completeBet';

        $query = "SELECT * FROM tb_gamerequest ";

        $data['ep_title'] = "Complete Bet"; //setting title name
        $data['view_page'] = "admin/complete_bet.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $json;
        $data['pagination'] = $this->helper->pagination($query, $page, $prev, $next, $pageSlug, $max);
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }

    function liveBet() {

        $page = ($this->helper->getUriSegment(2) ?: 1 );

        $page_id = intval($page) - 1;
        $all = $this->betting->LiveBet($page_id);

        if (!$all) {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No bet available';
            json_encode($json);
        } else {
            $i = 0;
            while ($data = $all->fetch_assoc()) {
                //    $user1=($data['sender'] == $user_id ? $data['receiver'] : $data['sender']);
                $game_detail = $this->betting->GameDetails($data['game_id']);
                $sender_detail = $this->users->getUserById($data['sender']);
                $receiver_detail = $this->users->getUserById($data['receiver']);

                $json[$i]['id'] = $data['ID'];
                $json[$i]['game_id'] = $data['game_id'];
                if ($data['betteam'] == "hometeam")
                    $json[$i]['betteam'] = $game_detail['hometeam'];
                else if ($data['betteam'] == "awayteam")
                    $json[$i]['betteam'] = $game_detail['awayteam'];
                //$json[$i]['betteam']=($data['sender'] == $user_id ? $data['betteam'] : ($data['betteam'] == 'hometeam' ? 'awayteam' : 'hometeam'));

                $json[$i]['home_team'] = $game_detail['hometeam'];
                $json[$i]['away_team'] = $game_detail['awayteam'];
                $json[$i]['matchtime'] = $game_detail['matchtime'];
                $json[$i]['details'] = $game_detail['details'];
                $json[$i]['league_name'] = $game_detail['league_name'];
                $json[$i]['display_region'] = $game_detail['display_region'];
                $json[$i]['c_matchtime'] = date('Y-m-d H:i:s', $game_detail['matchtime']);

                // $json[$i]['sender_id']=$sender_detail['user_id'];
                $json[$i]['sender_name'] = $sender_detail['display_name'];
                $json[$i]['receiver_name'] = $receiver_detail['display_name'];
                // $json[$i]['sender_email']=$sender_detail['useremail'];
                // $json[$i]['sender_age']=$sender_detail['age'];
                // $json[$i]['sender_pic']=$sender_detail['profile_pic']; 
                $json[$i]['amount'] = $data['amount'];
                $i++;
            }
            json_encode($json);
        }
        $max = 20;
        $limit = ($page - 1) * $max;
        $prev = $page - 1;
        $next = $page + 1;
        $limits = (int) ($page - 1) * $max;
        $pageSlug = $GLOBALS['ep_dynamic_url'] . '/adminmain/liveBet';
        $query = "SELECT * FROM tb_gamerequest where status='accept'";
        $data['ep_title'] = "Complete Bet"; //setting title name
        $data['view_page'] = "admin/live_bet.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $json;
        $data['pagination'] = $this->helper->pagination($query, $page, $prev, $next, $pageSlug, $max);
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }

    /*
      function updateAdminProfile(){

      $user_id = $this->helper->getUriSegment(2);
      if($_SERVER['REQUEST_METHOD'] === 'POST'){
      $age=$_POST['age'];
      $useremail=$_POST['useremail'];
      $contact=($_POST['contact'] ?: '');
      $display_name=($_POST['display_name'] ?: '');
      if(strlen($age) == 0 || strlen($useremail) == 0 || intval($user_id) == 0){
      $json[0]['result']=0;
      $json[0]['msg']='Please fill all required data';
      $json[0]['user']=intval($user_id);
      json_encode($json);
      }
      if($this->users->check_useremail_exist($useremail)){
      $userdata=$this->users->check_useremail_exist($useremail);
      if($userdata['ID'] != $user_id){
      $json[0]['result']=2;
      $json[0]['msg']='Email already registered with another user';
      json_encode($json);
      }
      }else{
      $validextensions = array("jpeg", "jpg", "png");
      $temporary = explode(".", $_FILES["profile_pic"]["name"]);
      $file_extension = end($temporary);
      $newfilename='';
      if (($_FILES["profile_pic"]["type"] == "image/png") || ( $_FILES["profile_pic"]["type"] == "image/jpg") || ($_FILES["profile_pic"]["type"] == "image/jpeg")) {
      $sourcePath = $_FILES["profile_pic"]["tmp_name"];
      $t=time();
      $path = dirname($_SERVER["SCRIPT_FILENAME"]).'/images/profile_pic/';
      $newfilename = $t.".jpg";
      $targetPath = $path.$newfilename;
      move_uploaded_file($sourcePath,$targetPath);

      $args['profile_pic']=$GLOBALS['PROFILE_IMAGE_URL'].$newfilename;
      }


      $args['useremail']=$useremail;
      $args['contact']=$contact;
      $args['age']=$age;
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
      json_encode($json);

      }


      }else{
      $json[0]['result']=0;
      $json[0]['msg']='Please fill all required data';
      json_encode($json);
      }

      header("Location: https://www.merrona.com/authscript/adminmain/userprofile/52");

      } */

    function popupdata() {
        $data = $this->betting->getBetDetails($_POST['bet_id']);
        $game_detail = $this->betting->GameDetails($_POST['game_id']);

        $winner = $data['winner'];
        $betteam = $data['betteam'];
        $sender = $data['sender'];
        $receiver = $data['receiver'];

        if ($data['betteam'] == "hometeam")
            $json['betteam'] = $game_detail['hometeam'];
        else if ($data['betteam'] == "awayteam")
            $json['betteam'] = $game_detail['awayteam'];

        $json['home_team'] = $game_detail['hometeam'];
        $json['away_team'] = $game_detail['awayteam'];
        $json['matchtime'] = date('Y-m-d H:i:s', $game_detail['matchtime']);
        //   $json['details'] = $game_detail['details'];
        $json['league_name'] = $game_detail['league_name'];
        $json['display_region'] = $game_detail['display_region'];
        $json['c_matchtime'] = date('Y-m-d H:i:s', $game_detail['matchtime']);

        if ($data['winner'] == "hometeam")
            $json['winner'] = $game_detail['hometeam'];
        else if ($data['winner'] == "awayteam")
            $json['winner'] = $game_detail['awayteam'];

        if ($winner != 'draw' && $winner != '') {
            $winner_id = ($winner == $betteam ? $sender : $receiver);
            //$looser_id = ($winner == $betteam ? $sender : $receiver);
            $winner_detail = $this->users->getUserById($winner_id);
            $json['winner_name'] = $winner_detail['display_name'];
            $json['winner_email'] = $winner_detail['useremail'];
            if ($winner_detail['profile_pic'] != '')
                $json['winner_pic'] = '<img style="width:100px;height:100px;" src="' . $winner_detail['profile_pic'] . '"/>';
            else
                $json['winner_pic'] = '<p style="color:red">Image not available</p>';
        }else {
            $sender_detail = $this->users->getUserById($data['sender']);
            $receiver_detail = $this->users->getUserById($data['receiver']);

            $json['sender_name'] = $sender_detail['display_name'];
            $json['sender_email'] = $sender_detail['useremail'];
            if ($sender_detail['profile_pic'] != '')
                $json['sender_pic'] = '<img style="width:100px;height:100px;" src="' . $sender_detail['profile_pic'] . '"/>';
            else
                $json['sender_pic'] = '<p style="color:red">Image not available</p>';
            $json['receiver_name'] = $receiver_detail['display_name'];
            $json['receiver_email'] = $receiver_detail['useremail'];
            if ($receiver_detail['profile_pic'] != '')
                $json['receiver_pic'] = '<img style="width:100px;height:100px;" src="' . $receiver_detail['profile_pic'] . '"/>';
            else
                $json['receiver_pic'] = '<p style="color:red">Image not available</p>';
        }

        $html = '';
        foreach ($json as $key => $values) {

            $html .= '<div class="col-md-12">
                      <div class="form-group">
                          <div class="input-append"><label class="control-label">' . ucfirst(str_replace("_", " ", $key)) . ' : </label>
                            <label for="mydates">
                              ' . $values . '
                            </label>
                          </div>
                      </div>
                  </div>';
        }
        echo $html;
    }

    /*
     * Withdraw request
     */

    function commisionAmount() {

        $page = ($this->helper->getUriSegment(2) ?: 1 );
        $page_id = intval($page) - 1;
        $withdraw_request = $this->users->admin_commission($page_id);

        if ($withdraw_request) {
            $i = 0;
            while ($row = $withdraw_request->fetch_assoc()) {
                $json[$i] = $row;
                $i++;
            }
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No record found';
        }

        $max = 20;
        $limit = ($page - 1) * $max;
        $prev = $page - 1;
        $next = $page + 1;
        $limits = (int) ($page - 1) * $max;
        $pageSlug = $GLOBALS['ep_dynamic_url'] . 'adminmain/commisionAmount';

        $query = "select tb_wallethistory.*,tb_users.display_name from tb_wallethistory INNER JOIN tb_users on tb_wallethistory.user_id=tb_users.ID where transaction_type ='admin_commission' order by tb_wallethistory.`ID`";

        $data['ep_title'] = "Admin Commission"; //setting title name
        $data['view_page'] = "admin/admin_commission.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['table_data'] = $json;
        $data['pagination'] = $this->helper->pagination($query, $page, $prev, $next, $pageSlug, $max);
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }

    function wallet_withdraw() {
        $user_id = $_POST['user_id'];
        $amount = $_POST['amount'];
        $transaction_id = $_POST['transfer_id'];
        $paypal_id = $_POST['paypal_id'];
//            if (intval($user_id) == 0 || intval($amount) == 0 || strlen($paypal_id) == 0) {
//                $json[0]['result'] = 0;
//                $json[0]['msg'] = 'Please fill all required data';
//                echo json_encode($json);
//            }else{

        $userdata = $this->users->getUserById($user_id);
        if ($userdata['wallet_balance'] < $amount) {
            $json[0]['result'] = 2;
            $json[0]['msg'] = 'Insuficient balance';
            echo json_encode($json);
        } else {
            $total = floatval($userdata['wallet_balance']) - floatval($amount);
            $args['wallet_balance'] = $total;
            $this->users->updateUser($args, $user_id);

            //   $args_w['amount'] = $amount;
            $args_w['transfer_id'] = $transaction_id;
            $args_w['status'] = 'success';
            $wallet_id = $_POST['wallet_id'];
            $this->users->update_wallet($wallet_id, $args_w);
        }
        $to = $userdata['useremail'];
        $subject = 'Merrona wallet';
        $body = '<h3>Hello    ' . $userdata["display_name"] . ',</h3>';
        $body .= '<p>You request $' . $amount . ' to withdraw from wallet. Your available wallet balance $' . $total . '.</p>';
        $body .= "<br /> <br /><h4>Good luck! </h4>";

        $this->users->mail($to, $subject, $body);


        $to = $GLOBALS['Admin_email'];
        $subject = 'Merrona withdraw request';
        $body = '<h3>Hello,</h3>';
        $body .= '<p>' . $userdata["display_name"] . ' request $' . $amount . ' to withdraw from wallet. Please approve request to process withdraw.</p>';
        $body .= "<br /> <br /><h4>Thanks! </h4>";

        $this->users->mail($to, $subject, $body);
        header("Location: " . $GLOBALS['ep_dynamic_url'] . "adminmain/withdrawalRequest");
    }
    
    public function forgot() {

        if (!empty($_POST)) {
            $adminemail = $_POST['email'];
            if (strlen($adminemail) == 0) {
                $data['view_page'] = "admin/forgot-password.php"; //controller view page 
                $data['error'] = "Email not registered.";
                $data['ep_title'] = "Forgot Password";
                return $data;
            }else{

                $userdata = $this->model->check_useremail_exist($adminemail);

                $code= substr(md5(microtime()),rand(0,26),15);
                $args['reset_key']=$code;
                $update=$this->model->updateUser($args);

                $to = $adminemail;
                $subject = 'Merrona app password reset ';
                $body = '<h3>Hello Admin,</h3>';
                $body .= '<p>Someone requested that the password be reset for the following account:</p>';
                $body .= "<table><tr><th>Username</th><td>" . $adminemail . "</td></tr><tr><th>Password</th><td><a href='" . $GLOBALS['ep_dynamic_url'] . "adminmain/codeauthenticate/".$code."'>Click on link</a></td></tr></table>";
                $body .= "<p>Please put this code: " . $code . " to reset password.</p>";
                $body .= "<br /> <br /><h4>Good luck! </h4>";

                $this->users->mail($to, $subject, $body);
                $data['view_page'] = "admin/forgot-password.php"; //controller view page 
                $data['success'] = "Code sent on registered email";
                $data['ep_title'] = "Forgot Password";
                return $data;
            }
        }
        if (!isset($_SESSION['easyphp_sessionid']) && $_SESSION['easyphp_sessionid'] == '') {
            $data['view_page'] = "admin/forgot-password.php"; //controller view page 
            $data['ep_title'] = "Forgot Password";
        } else {
            header("Location: " . $GLOBALS['ep_dynamic_url'] . "adminmain");
        }
        return $data;
    }
    
    function changepassword(){
        if(isset($_POST['updatePassword'])){
           $result= $this->model->checkoldpassword();
           if(trim($result['admin_password'])==md5($_POST['old_password'])){
               $this->model->updateUser(array("admin_password"=>md5($_POST['password1'])));
               $data['success'] = 'Password Successfully Changed.';
           }else{
               $data['error'] = 'Your old password is not valid.';
           }
        }
        $data['ep_title'] = "Change Password"; //setting title name
        $data['view_page'] = "admin/change-admin-password.php"; //controller view page
        $data['ep_header'] = 'header.php'; //header view (Also Ex: "header.php")
        $data['ep_footer'] = 'footer.php'; //footer view 
        return $data;
    }
}

?>