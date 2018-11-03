<?php

/**
 * chatting model
 */
class chatting_model extends DBconfig {

    public function __construct() {
        $connection = new DBconfig();  // database connection
        $this->connection = $connection->connectToDatabase();
        $this->helper = new helper(); // calling helper class
        $this->users = new users_model(); // calling helper class
    }

    public function RecentUserMessageList($user_id) {
        $userlists = $this->helper->db_select("DISTINCT GREATEST(`sender_id`,`receiver_id`) AS value1,LEAST(`sender_id`,`receiver_id`) AS value2", "tb_chattingdata", "where (`sender_id`='$user_id' OR `receiver_id`='$user_id') order by `ID` desc");
        if ($userlists->num_rows > 0) {
            $i = 0;
            while ($userlist = $userlists->fetch_assoc()) {
                $user_id1 = $userlist['value1'];
                $user_id2 = $userlist['value2'];
                $usermessages = $this->connection->query("select * from `tb_chattingdata` where (`sender_id`=$user_id1 AND `receiver_id`=$user_id2) OR (`sender_id`=$user_id2 AND `receiver_id`=$user_id1) order by `ID` DESC");
                $usermessage = $usermessages->fetch_assoc();
                $sender = ($usermessage['sender_id'] == $user_id ? $usermessage['receiver_id'] : $usermessage['sender_id']);
                $users[$i]['msg_id'] = (string) $usermessage['ID'];
                $users[$i]['message'] = $usermessage['message'];
                $users[$i]['status'] = $usermessage['read_status'];
                $users[$i]['date'] = date("d/m/y H:i", strtotime($usermessage['sent_time']));
                $unread = $this->connection->query("select * from `tb_chattingdata` where (`sender_id`=$sender AND `receiver_id`=$user_id) AND `read_status`='unread' order by `ID` DESC");
                $users[$i]['unread'] = $unread->num_rows;
                $users[$i]['userdata'] = $this->users->getUserById($sender);
                $i++;
            }
            usort($users, function($a, $b) {
                return $b['msg_id'] - $a['msg_id'];
            });
        } else {
            $users = false;
        }
        return $users;
    }

    public function PostMessage($data = array()) {
        $result = $this->helper->db_insert($data, 'tb_chattingdata');
        return $result;
    }

    public function DetailMessage($sender_id, $reciever_id) {
        $results = $this->connection->query("select * from (select * from `tb_chattingdata` where (`sender_id`='$sender_id' && `receiver_id`='$reciever_id') || (`sender_id`='$reciever_id' && `receiver_id`='$sender_id') order by `sent_time` DESC LIMIT 100) sub ORDER BY `sent_time` ASC");
        $this->ReadMessageStatus($sender_id, $reciever_id);
        return $results;
    }

    public function NewMessage($sender_id, $reciever_id, $msg_id) {
        $results = $this->connection->query("select * from `tb_chattingdata` where ((`sender_id`='$sender_id' && `receiver_id`='$reciever_id') || (`sender_id`='$reciever_id' && `receiver_id`='$sender_id')) && `ID`>$msg_id order by `sent_time` ASC");
        $this->ReadMessageStatus($sender_id, $reciever_id);
        return $results;
    }

    public function OldMessage($sender_id, $reciever_id, $msg_id) {
        $results = $this->connection->query("select * from (select * from `tb_chattingdata` where ((`sender_id`='$sender_id' && `receiver_id`='$reciever_id') || (`sender_id`='$reciever_id' && `receiver_id`='$sender_id')) && `ID`<$id order by `sent_time` DESC LIMIT 100) sub ORDER BY `sent_time` ASC");
        $this->ReadMessageStatus($sender_id, $reciever_id);
        return $results;
    }

    public function ReadMessageStatus($sender_id, $reciever_id) {
        $this->connection->query("update `tb_chattingdata` set `read_status`='read' where (`sender_id`='$sender_id' && `receiver_id`='$reciever_id')");
    }

    /*     * * Typing **** */

    public function insertStatus($data = array()) {
        $result = $this->helper->db_insert($data, 'tb_user_typing');
        return $result;
    }

    public function checkstatus($sender_id, $reciever_id) {
        $results = $this->connection->query("select * from `tb_user_typing` where (`sender_id`='$sender_id' && `receiver_id`='$reciever_id')");
        return $results;
    }

    public function updateStatusMessage($sender_id, $reciever_id,$typing_status) {
        $this->connection->query("update `tb_user_typing` set `typing_status`='$typing_status' where (`sender_id`='$sender_id' && `receiver_id`='$reciever_id')");
        return true;
    }

    /*     * * Typing **** */
}
