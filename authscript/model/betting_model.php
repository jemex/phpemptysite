<?php

/**
 * betting model
 */
class betting_model extends DBconfig {

    public function __construct() {
        $connection = new DBconfig();  // database connection
        $this->connection = $connection->connectToDatabase();
        $this->helper = new helper(); // calling helper class
    }

    public function InsertSoccerMatchData($ID, $HomeTeam, $AwayTeam, $MatchTime, $Details, $League, $DisplayRegion, $HomeROT, $AwayROT, $gametype) {

        $data = array(
            'event_id' => $ID,
            'hometeam' => $HomeTeam,
            'awayteam' => $AwayTeam,
            'matchtime' => $MatchTime,
            'details' => $Details,
            'league_name' => $League,
            'display_region' => $DisplayRegion,
            'homeROT' => $HomeROT,
            'awayROT' => $AwayROT,
            'gametype' => $gametype
        );

        $insert = $this->helper->db_insert($data, 'tb_soccerlist');
        if ($insert) {
            return $insert;
        } else {
            return false;
        }
    }

    public function UpdateSoccerMatchData($args, $id) {
        $result = $this->helper->db_update($args, 'tb_soccerlist', "WHERE ID='$id'");
        return $result;
    }

    public function UpdateBettingData($args, $id) {
        $result = $this->helper->db_update($args, 'tb_gamerequest', "WHERE ID='$id'");
        return $result;
    }

    public function InsertSoccerOdds($arr = array()) {

        $data = array(
            'soccer_id' => $arr['sc_id'],
            'odd_id' => $arr['ID'],
            'event_id' => $arr['EventID'],
            'money_line_home' => $arr['MoneyLineHome'],
            'money_line_away' => $arr['MoneyLineAway'],
            'point_spread_home' => $arr['PointSpreadHome'],
            'point_spread_away' => $arr['PointSpreadAway'],
            'point_spread_home_line' => $arr['PointSpreadHomeLine'],
            'point_spread_away_line' => $arr['PointSpreadAwayLine'],
            'total_number' => $arr['TotalNumber'],
            'over_line' => $arr['OverLine'],
            'under_line' => $arr['UnderLine'],
            'draw_line' => $arr['DrawLine'],
            'last_updated' => $arr['LastUpdated'],
            'site_id' => $arr['SiteID'],
            'odd_type' => $arr['OddType']
        );

        $insert = $this->helper->db_insert($data, 'tb_soccerodds');
        if ($insert) {
            return $insert;
        } else {
            return false;
        }
    }

    public function GameDetails($id) {
        $detail = $this->helper->db_select("*", "tb_soccerlist", "WHERE `event_id`='$id'");
        if ($detail->num_rows != 0) {
            return $detail->fetch_assoc();
        } else {
            return false;
        }
    }

    public function GetGameRequest($game_id) {
        $resultRaw = $this->helper->db_select("*", "tb_gamerequest", "where `game_id`='$game_id'");
        if ($resultRaw->num_rows != 0) {
            return $resultRaw;
        } else {
            return false;
        }
    }

    public function CheckGameRequest($sender, $receiver, $game_id) {
        $resultRaw = $this->helper->db_select("*", "tb_gamerequest", "where ((`sender`=$sender && `receiver`=$receiver) || (`receiver`=$sender && `sender`=$receiver)) && `status`='pending' && `game_id`='$game_id'");
        return $resultRaw->fetch_assoc();
    }

    public function GameRequest($sender, $receiver, $game_id, $message, $amount, $betteam) {

        if (!($this->CheckGameRequest($sender, $receiver, $game_id))) {
            $data = array(
                'sender' => $sender,
                'receiver' => $receiver,
                'game_id' => $game_id,
                'signature' => $message,
                'amount' => $amount,
                //'commission' => $commission,
                'betteam' => $betteam
            );

            $insert = $this->helper->db_insert($data, 'tb_gamerequest');
            if ($insert) {
                return $insert;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public function AcceptRequest($id, $newfilename) {
        $data = array('status' => 'accept', 'accept_time' => date('Y-m-d H:i:s'), 'accept_signature' => $newfilename);
        $result = $this->helper->db_update($data, 'tb_gamerequest', "WHERE ID='$id'");
        return $result;
    }

    public function CancelRequest($id) {
        $data = array('status' => 'cancel');
        $result = $this->helper->db_update($data, 'tb_gamerequest', "WHERE ID='$id'");
        return $result;
    }

    public function UserLiveBet($user_id) {
        $check = $this->helper->db_select("*", "tb_gamerequest", "where (`sender`=$user_id || `receiver`=$user_id ) && `status`='accept'");
        if ($check->num_rows) {
            return $check;
        } else {
            return false;
        }
    }

    public function LiveBet($page_id = 0) {
        $offset = $page_id * 20;
        $query = $this->helper->db_select("*", "tb_gamerequest", "where status='accept' order by `ID` desc LIMIT $offset , 20");
        if ($query->num_rows) {
            return $query;
        } else {
            return false;
        }
    }

    public function UserBetHistory($user_id) {
        $check = $this->helper->db_select("*", "tb_gamerequest", "where (`sender`=$user_id || `receiver`=$user_id ) && `status`='complete'");
        if ($check->num_rows) {
            return $check;
        } else {
            return false;
        }
    }
    
    public function UserBetWithpaginationHistory($user_id,$page_id=0) {
        $offset = $page_id * 10;
        $query = $this->helper->db_select("*", "tb_gamerequest", "where (`sender`=$user_id || `receiver`=$user_id ) && `status`='complete' order by `ID` desc LIMIT $offset , 10");
        if ($query->num_rows) {
            return $query;
        } else {
            return false;
        }
    }

    public function CompleteBet($page_id = 0) {
            $offset = $page_id * 20;
            $query = $this->helper->db_select("*", "tb_gamerequest", " order by `ID` desc LIMIT $offset , 20");
            if ($query->num_rows) {
                return $query;
            } else {
                return false;
            }
    }
    
    public function getBetDetails($id) {
            $resultRaw = $this->helper->db_select("*", "tb_gamerequest", "where ID='$id'");
            if ($resultRaw->num_rows) {
               return $resultRaw->fetch_assoc();
            } else {
                return false;
            }
        
    }

    public function RequestNotification($user_id) {
        $check = $this->helper->db_select("*", "tb_gamerequest", "where `receiver`=$user_id && `status`='pending'");
        if ($check->num_rows != 0) {
            return $check;
        } else {
            return false;
        }
    }

}
