<?php

/**
 * User Model
 *
 * @author Azfar Ahmed
 * @version 1.0
 * @date November 02, 2015
 * @EasyPhp MVC Framework
 * @website www.tutbuzz.com
 */
class users_model extends DBconfig {

    public function __construct() {
        $connection = new DBconfig();  // database connection
        $this->connection = $connection->connectToDatabase();
        $this->helper = new helper(); // calling helper class
    }

    public function logincheck($useremail, $password) {
        $email = mysqli_real_escape_string($this->connection, $useremail);
        $password = mysqli_real_escape_string($this->connection, $password);
        $hash = $this->password_hash($password);
        $result = $this->helper->check("tb_users", "WHERE useremail='$email' && password='$hash'");
        if ($result) {

            $resultRaw = $this->helper->db_select("*", "tb_users", "WHERE useremail='$email' && password='$hash'");
            $result = $resultRaw->fetch_assoc();
            $result = $this->getUserById($result['ID']);
        }
        return $result;
    }

    public function check_useremail_exist($email) {
        $resultRaw = $this->helper->db_select("*", "tb_users", "WHERE useremail='$email'");
        return $resultRaw->fetch_assoc();
    }

    public function password_hash($str = '') {
        return md5($str);
    }

    public function getAllUsers($user_id = 0) {
        $result = $this->helper->db_select("*", "tb_users", "WHERE ID!=$user_id && status='verify'");
        return $result;
    }

    public function getAllOnlineUsers($user_id = 0) {
        //Ex: $result = $this->helper->db_select("column_selector", "table_name", "where conditions");
        $result = $this->helper->db_select("*", "tb_usertokens", "WHERE `status`='online' && user_id != $user_id");
        return $result;
    }

    public function insertUser($data) {
        $result = $this->helper->db_insert($data, 'tb_users');
        return $result;
    }

    public function updateUser($data, $id) {
        $result = $this->helper->db_update($data, 'tb_users', "WHERE ID='$id'");
        return $result;
    }

    public function getUserById($id) {
        $resultRaw = $this->helper->db_select("*", "tb_users", "WHERE ID='$id'");
        if ($resultRaw->num_rows != 0) {
            $data = $resultRaw->fetch_assoc();
            $arr['user_id'] = $data['ID'];
            $arr['age'] = $data['age'];
            $arr['useremail'] = $data['useremail'];
            $arr['display_name'] = ($data['display_name'] ?: $data['useremail']);
            $arr['contact'] = $data['contact'];
            $arr['profile_pic'] = ($data['profile_pic'] ?: '');
            $arr['description'] = $data['description'];
            $arr['status'] = $data['status'];
            $arr['wallet_balance'] = $data['wallet_balance'];
            return $arr;
        } else {
            return false;
        }
    }
    
    
    public function getwinsCount($transfer_id,$type) {
        $resultRaw = $this->helper->custom_query("SELECT * FROM `tb_wallethistory` WHERE `transfer_id`='$transfer_id' and transaction_type='$type'");
        //echo "SELECT * FROM `tb_wallethistory` WHERE `transfer_id`='$transfer_id' and transaction_type='$type'";
        if ($resultRaw->num_rows != 0) {
            $data = $resultRaw->fetch_assoc();
            $arr['user_id'] = $data['user_id'];
            return $arr;
        } else {
            return false;
        }
    }
    
    public function getDrawMatches($transfer_id) {
        $resultRaw = $this->helper->custom_query("SELECT * , tb_gamerequest.ID AS transfer_id FROM  `tb_wallethistory` JOIN  `tb_gamerequest` ON tb_wallethistory.transfer_id = tb_gamerequest.ID WHERE tb_wallethistory.`transfer_id` ='$transfer_id' AND tb_wallethistory.transaction_type =  'bet_draw'");
      //  echo "SELECT * , tb_gamerequest.ID AS transfer_id FROM  `tb_wallethistory` JOIN  `tb_gamerequest` ON tb_wallethistory.transfer_id = tb_gamerequest.ID WHERE tb_wallethistory.`transfer_id` ='$transfer_id' AND tb_wallethistory.transaction_type =  'bet_draw'";
        if ($resultRaw->num_rows != 0) {
            return $resultRaw;
        } else {
            return false;
        }
    }
    
    public function getuserByemail($email) {
        $resultRaw = $this->helper->db_select("*", "tb_users", "WHERE useremail='$email'");
        //echo "SELECT * FROM `tb_wallethistory` WHERE `transfer_id`='$transfer_id' and transaction_type='$type'";
        if ($resultRaw->num_rows != 0) {
            $data = $resultRaw->fetch_assoc();
            $arr['user_id'] = $data['ID'];
            $arr['display_name'] = $data['display_name'];
            $arr['profile_pic'] = $data['profile_pic'];
            $arr['wallet_balance'] = $data['wallet_balance'];
            return $arr;
        } else {
            return false;
        }
    }
    

    public function resetcodeauthenticate($email, $code = '', $id = 0) {
        $resultRaw = $this->helper->db_select("*", "tb_users", "WHERE useremail='$email' && reset_key='$code' && ID='$id'");
        if ($resultRaw->num_rows != 0) {
            return true;
        } else {
            return false;
        }
    }

    public function deleteUserById($id) {
        //Ex: $result = $this->helper->db_delete("table_name", "where conditions");
        $result = $this->helper->db_delete("users", "WHERE id='$id'");
        return $result;
    }

    public function RegisterDeviceToken($token, $type, $user_id) {
        if ($this->CheckDevideToken($token, $type, $user_id)) {
            $id = $this->CheckDevideToken($token, $type, $user_id);
            $this->UpdateDeviceToken($id, $token, $type, $user_id);
        } else {
            $this->InsertDeviceToken($token, $type, $user_id);
        }
    }

    public function CheckDevideToken($token, $type, $user_id) {
        $return = false;

        $device = $this->helper->db_select("*", "tb_usertokens", "WHERE device_id='$token'");
        $device_user = $this->helper->db_select("*", "tb_usertokens", "WHERE user_id='$user_id' && device_type='$type'");

        if ($device->num_rows != 0) {
            $data = $device->fetch_assoc();
            $return = $data['ID'];
        } else if ($device_user->num_rows != 0) {
            $data = $device_user->fetch_assoc();
            $return = $data['ID'];
        } else {
            $return = false;
        }
        return $return;
    }

    public function InsertDeviceToken($token, $type, $user_id) {
        $data['device_id'] = $token;
        $data['device_type'] = $type;
        $data['user_id'] = $user_id;
        $result = $this->helper->db_insert($data, 'tb_usertokens');
        return $result;
    }

    public function UpdateDeviceToken($id, $token, $type, $user_id) {
        $data['device_id'] = $token;
        $data['device_type'] = $type;
        $data['user_id'] = $user_id;
        $result = $this->helper->db_update($data, 'tb_usertokens', "WHERE ID='$id'");
        return $result;
    }

    function SendPushNotification($user_id, $message, $type = '', $extrainfo = array()) {
        $device = $this->helper->db_select("*", "tb_usertokens", "WHERE user_id='$user_id'");
        if ($device->num_rows != 0) {
            while ($row = $device->fetch_assoc()) {
                $deviceToken = $row['device_id'];
                $device_type = $row['device_type'];
                if ($device_type == 'IOS') {
                    
                } else {
                    //Android
                    $API_ACCESS_KEY = $GLOBALS['PUSH_API_ACCESS_KEY'];
                    $registrationIds = array($deviceToken);
                    $msg = array('message' => $message, 'vibrate' => 1, 'sound' => 1, 'type' => $type, 'extrainfo' => $extrainfo);
                    $fields = array('registration_ids' => $registrationIds, 'data' => $msg);

                    $headers = array('Authorization: key=' . $API_ACCESS_KEY, 'Content-Type: application/json');
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, 'https://android.googleapis.com/gcm/send');
                    curl_setopt($ch, CURLOPT_POST, true);
                    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));
                    $result = curl_exec($ch);
                    curl_close($ch);
                }
            }
        } else {
            return false;
        }
    }

    public function userLogOut($user_id, $token) {
        $delete = $this->helper->db_delete("tb_usertokens", "where `user_id`='$user_id' and `device_id`='$token'");
        return $delete;
    }

    public function userOnline($user_id, $token) {
        $post_data = $this->helper->db_select("*", "tb_usertokens", "where `user_id`='$user_id' && `device_id`='$token'");
        if ($post_data->num_rows != 0) {
            $data = $post_data->fetch_assoc();
            $data['status'] = 'online';
            $result = $this->helper->db_update($data, 'tb_usertokens', "WHERE `user_id`='$user_id' and `device_id`='$token'");
            return $result;
        } else {
            return false;
        }
    }

    public function userOffline($user_id, $token) {
        $data['status'] = 'offline';
        $result = $this->helper->db_update($data, 'tb_usertokens', "WHERE `user_id`='$user_id' and `device_id`='$token'");
        return $result;
    }

    public function codeauthenticate($email, $code) {
        $post_data = $this->helper->check("tb_users", "where `useremail`='$email' and `reset_key`='$code'");
        return $post_data;
    }

    public function authenticatepassword($user_id, $pass) {
        $password = $this->password_encript($pass);
        $post_data = $this->helper->db_select("*", "tb_users", "where `ID`='$user_id' and `password`='$password'");
        if ($post_data->num_rows != 0) {
            $data = $post_data->fetch_assoc();
            return $this->getUserById($data['ID']);
        } else {
            return false;
        }
    }

    public function add_wallet_history($args) {
        $result = $this->helper->db_insert($args, 'tb_wallethistory');
        return $result;
    }

    public function wallet_history($user_id = 0, $page_id = 0) {
        $offset = $page_id * 20;
        $query = $this->helper->db_select("*", "tb_wallethistory", "where user_id='$user_id' order by `ID` desc LIMIT $offset , 20");
        if ($query->num_rows != 0) {
            return $query;
        } else {
            return false;
        }
    }

    public function mail($email, $subject, $body, $replyto = '') {
        $mail = new PHPMailer;

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        //$mail->isSMTP();        
        //$mail->Mailer = $GLOBALS['Mailer'];                              // Set mailer to use SMTP
        //$mail->Host = $GLOBALS['ep_smpt_server'];  // Specify main and backup SMTP servers
        //$mail->SMTPAuth = true;                               // Enable SMTP authentication
        //$mail->Username = $GLOBALS['ep_smpt_username'];                 // SMTP username
        //$mail->Password = $GLOBALS['ep_smpt_password'];                           // SMTP password
        //$mail->SMTPSecure = $GLOBALS['SMTPSecure'];                          // Enable TLS encryption, `ssl` also accepted
        //$mail->Port = $GLOBALS['ep_smpt_port'];                                    // TCP port to connect to

        $mail->setFrom($GLOBALS['Admin_email'], $GLOBALS['website_name']);
        $mail->addAddress($email);
        if (strlen($replyto) != 0) {
            $mail->addReplyTo($replyto);
        }
        $mail->isHTML(true);

        $mail->Subject = $subject;
        $mail->Body = $body;

        if (!$mail->send()) {
            //echo 'Email Error';
            //echo 'Mailer Error: ' . $mail->ErrorInfo;
        } else {
            //echo 'sent';
        }
    }
    
     /*     * * Add paypal ID (14-8-2017) *** */

    public function add_wallet_paypal_id($args) {
        $result = $this->helper->db_insert($args, 'tb_wallethistory_meta');
        return $result;
    }

    /*     * * Add paypal ID (14-8-2017) *** */
    
    public function user_specific_wallet_history($id) {
       $query = $this->connection->query("select tb_wallethistory.*,tb_gamerequest.amount as bet_amount,tb_gamerequest.commission from tb_wallethistory INNER JOIN tb_gamerequest on tb_wallethistory.transfer_id=tb_gamerequest.ID where transaction_type !='paypal_withdraw' and transaction_type !='admin_commission' order by tb_wallethistory.`ID` desc");
        if ($query->num_rows != 0) {
            return $query;
        } else {
            return false;
        }
    }
    
    public function complete_wallet_history($page_id = 0) {
        $offset = $page_id * 20;
       $query = $this->connection->query("select tb_wallethistory.*,tb_gamerequest.amount as bet_amount,tb_gamerequest.commission from tb_wallethistory INNER JOIN tb_gamerequest on tb_wallethistory.transfer_id=tb_gamerequest.ID where transaction_type !='paypal_withdraw' and transaction_type !='admin_commission' order by tb_wallethistory.`ID` desc LIMIT $offset , 20");
        //$query = $this->helper->db_select("*", "tb_wallethistory", " where transaction_type !='paypal_withdraw' order by `ID` desc LIMIT $offset , 20");
        if ($query->num_rows != 0) {
            return $query;
        } else {
            return false;
        }
    }

    public function update_wallet($wallet_id,$data) {
        $result = $this->helper->db_update($data, 'tb_wallethistory', "WHERE `ID`='$wallet_id'");
        return $result;
    }

    public function admin_commission($page_id = 0) {
        $offset = $page_id * 20;
        $query = $this->connection->query("select tb_wallethistory.*,tb_users.display_name from tb_wallethistory INNER JOIN tb_users on tb_wallethistory.user_id=tb_users.ID where transaction_type ='admin_commission' order by tb_wallethistory.`ID` desc LIMIT $offset , 20");
        //echo $query;
      //  $query = $this->helper->db_select("*", "tb_wallethistory", " where transaction_type='paypal_withdraw' and status='pending' order by `ID` desc LIMIT $offset , 20");
        if ($query->num_rows != 0) {
            return $query;
        } else {
            return false;
        }
    }

}