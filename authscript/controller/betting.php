<?php

class betting {

    function __construct() {
        $this->users = new users_model();
        $this->helper = new helper();
        $this->betting = new betting_model();
    }

    function index() {
        echo 'Please Call Required method';
        //echo dirname($_SERVER["SCRIPT_FILENAME"]);
    }

    /*
     * check game result
     * url:  http://merrona.com/authscript/betting/allgamelistsync
     */

    function allgamelistsync() {
        $gametypes = array('soccer', 'cricket', 'tennis', 'boxing', 'golf', 'mma', 'nhl', 'mlb', 'nba', 'ncaab', 'ncaaf', 'nfl');
        foreach ($gametypes as $gametype) {
            $ch = curl_init();
            $api_url = 'https://jsonodds.com/api/odds/' . $gametype;
            curl_setopt($ch, CURLOPT_URL, $api_url);
            $headers = [
                'JsonOdds-API-Key:960c49c4-d4e9-4c4b-bd43-69f1da277106'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $output = json_decode($server_output, true);

            if (count($output) > 0) {
                $i = 0;
                foreach ($output as $matches) {
                    $ID = $matches['ID'];
                    $HomeTeam = $matches['HomeTeam'];
                    $AwayTeam = $matches['AwayTeam'];
                    $MatchTime = strtotime($matches['MatchTime']);
                    $Details = $matches['Details'];
                    $League = (isset($matches['League']['Name']) ? $matches['League']['Name'] : '' );
                    $DisplayRegion = (isset($matches['DisplayRegion']) ? $matches['DisplayRegion'] : '' );
                    $HomeROT = $matches['HomeROT'];
                    $AwayROT = $matches['AwayROT'];

                    $check = $this->betting->GameDetails($ID);

                    if (!$check) {
                        $game_id = $this->betting->InsertSoccerMatchData($ID, $HomeTeam, $AwayTeam, $MatchTime, $Details, $League, $DisplayRegion, $HomeROT, $AwayROT, $gametype);
                        if ($game_id) {
                            if (count($matches['Odds']) > 0) {
                                foreach ($matches['Odds'] as $odds) {
                                    $odds['sc_id'] = $game_id;
                                    $odds['LastUpdated'] = strtotime($odds['LastUpdated']);
                                    $this->betting->InsertSoccerOdds($odds);
                                }
                            }
                        }
                    }
                    $i++;
                }
            }
        }
        mail('bhuneshsatpada.oss@gmail.com', 'merrona test cron new', 'merrona test');
    }

    /*
     * check game result
     * url:  http://merrona.com/authscript/betting/gameresult
     */

    function gameresult() {
        $c_time = time();
        $o_time = strtotime('-2 day');
        echo $c_time . ' ' . $o_time;
        $result = $this->helper->db_select("*", "tb_soccerlist", "WHERE `status`='active' && (`matchtime` BETWEEN $o_time AND $c_time) order by `matchtime` ASC");
        if ($result->num_rows == 0) {
            die;
        }
        $i = 0;
        while ($data = $result->fetch_assoc()) {
            $event_id = $data['event_id'];
            $ID = $data['ID'];
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://jsonodds.com/api/results/getbyeventid/" . $event_id);
            $headers = [
                'JsonOdds-API-Key:960c49c4-d4e9-4c4b-bd43-69f1da277106'
            ];
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            $server_output = curl_exec($ch);
            curl_close($ch);
            $output = json_decode($server_output, true);
            if (count($output) == 0) {
                continue;
            }

            $i = 0;
            foreach ($output as $events) {
                if ($events['OddType'] != 'Game') {
                    continue;
                }

                if ($events['FinalType'] != 'Finished') {
                    continue;
                }

                $args['homescore'] = $events['HomeScore'];
                $args['awayscore'] = $events['AwayScore'];
                $args['binaryscore'] = $events['BinaryScore'];
                $BinaryScore = explode('-', $events['BinaryScore']);

                if ($BinaryScore[0] == $BinaryScore[1]) {
                    $winner = 'draw';
                } else if ($BinaryScore[0] > $BinaryScore[1]) {
                    $winner = 'hometeam';
                } else {
                    $winner = 'awayteam';
                }
                $args['winner'] = $winner;
                $args['status'] = 'completed';
                $this->betting->UpdateSoccerMatchData($args, $ID);

                $gameRequests = $this->betting->GetGameRequest($event_id);
                if (!$gameRequests) {
                    continue;
                }

                while ($rows = $gameRequests->fetch_assoc()) {
                    $request_id = $rows['ID'];
                    $sender = $rows['sender'];
                    $receiver = $rows['receiver'];
                    $amount = $rows['amount'];
                    $commission = $rows['commission'];
                    $betteam = $rows['betteam'];
                    if ($rows['status'] == 'complete' || $rows['status'] == 'cancel') {
                        continue;
                    }
                    if ($rows['status'] == 'accept') {
                        $args_b['status'] = 'complete';
                        $args_b['winner'] = $winner;
                        $this->betting->UpdateBettingData($args_b, $request_id);

                        if ($winner == 'draw') {
                            $senderdata = $this->users->getUserById($sender);
                            $senderdata_balance = $senderdata['wallet_balance'];
                            $total = floatval($senderdata_balance) + floatval($amount);
                            $args_s['wallet_balance'] = $total;
                            $this->users->updateUser($args_s, $sender);

                            $extrainfo['bet_id'] = $request_id;
                            $act_msg = 'bethistory';
                            $message = "hello, your bet draw on game.Please check on history";
                            $this->users->SendPushNotification($sender, $message, $act_msg, $extrainfo);

                            $args_w['user_id'] = $sender;
                            $args_w['transaction_type'] = 'bet_draw';
                            $args_w['amount'] = floatval($amount);
                            $args_w['transfer_id'] = $request_id;
                            $args_w['status'] = 'success';
                            $this->users->add_wallet_history($args_w);

                            $receiverdata = $this->users->getUserById($receiver);
                            $receiverdata_balance = $receiverdata['wallet_balance'];
                            $total = floatval($receiverdata_balance) + floatval($amount);
                            $args_r['wallet_balance'] = $total;
                            $this->users->updateUser($args_r, $receiver);

                            $extrainfo['bet_id'] = $request_id;
                            $act_msg = 'bethistory';
                            $message = "hello, your bet draw on game.Please check on history";
                            $this->users->SendPushNotification($receiver, $message, $act_msg, $extrainfo);

                            $args_w['user_id'] = $receiver;
                            $this->users->add_wallet_history($args_w);


                            $to = $senderdata['useremail'];
                            $subject = 'Merrona Bet Notification';
                            $body = "<h3>Hello," . $senderdata['display_name'] . "</h3>";
                            $body .= '<p>Your Bet drawn and coins added to account. Bet details as follows:</p>';
                            $body .= "<table><tr><th>Home Team</th><td>" . $data['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $data['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $data['matchtime']) . "</td></tr><tr><th>Coins</th><td>" . $amount . "</td></tr></table>";
                            // $body .= '<p>Admin charge '.$commission.' returned.</p>';
                            $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                            $body .= "<br /><strong>Good luck!! </strong>";
                            $this->users->mail($to, $subject, $body);

                            $to = $receiverdata['useremail'];
                            $subject = 'Merrona Bet Notification';
                            $body = "<h3>Hello," . $receiverdata['display_name'] . "</h3>";
                            $body .= '<p>Your Bet drawn and coins added to account. Bet details as follows:</p>';
                            $body .= "<table><tr><th>Home Team</th><td>" . $data['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $data['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $data['matchtime']) . "</td></tr><tr><th>Coins</th><td>" . $amount . "</td></tr></table>";
                            // $body .= '<p>Admin charge '.$commission.' returned.</p>';
                            $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                            $body .= "<br /><strong>Good luck!! </strong>";
                            $this->users->mail($to, $subject, $body);
                        } else {
                            $winner_id = ($winner == $betteam ? $sender : $receiver);

                            $looser_id = ($winner_id == $sender ? $receiver : $sender);

                            $winnerdata = $this->users->getUserById($winner_id);
                            $winnerdata_balance = $winnerdata['wallet_balance'];
                            $total = floatval($winnerdata_balance) + (floatval($amount) * 2);
                            $args_s['wallet_balance'] = $total;
                            $this->users->updateUser($args_s, $winner_id);

                            $extrainfo['bet_id'] = $request_id;
                            $act_msg = 'bethistory';
                            $message = "hello, you won bet on game.Please check history";
                            $this->users->SendPushNotification($winner_id, $message, $act_msg, $extrainfo);

                            $extrainfo['bet_id'] = $request_id;
                            $act_msg = 'bethistory';
                            $message = "hello, you lost bet on game.Please check history";
                            $this->users->SendPushNotification($looser_id, $message, $act_msg, $extrainfo);

                            $args_w['user_id'] = $winner_id;
                            //  $args_w['looser_id']=$looser_id;
                            $args_w['transaction_type'] = 'bet_win';
                            $args_w['amount'] = floatval($amount * 2);
                            $args_w['transfer_id'] = $request_id;
                            $args_w['status'] = 'success';
                            $this->users->add_wallet_history($args_w);
                            
                            $args_w['user_id'] = $looser_id;
                            //  $args_w['looser_id']=$looser_id;
                            $args_w['transaction_type'] = 'bet_loss';
                            $args_w['amount'] = floatval($amount);
                            $args_w['transfer_id'] = $request_id;
                            $args_w['status'] = 'success';
                            $this->users->add_wallet_history($args_w);
                            
//                            $looserdata = $this->users->getUserById($looser_id);
//                            $looserdata_balance = $looserdata['wallet_balance'];
//                            $lossAmount = floatval($looserdata_balance) - (floatval($amount));
//                            $args_s['wallet_balance'] = $lossAmount;
//                            $this->users->updateUser($args_s, $looser_id);
                            
                            $to = $winnerdata['useremail'];
                            $subject = 'Merrona Bet Notification';
                            $body = "<h3>Hello," . $winnerdata['display_name'] . "</h3>";
                            $body .= '<p>Congratulation!! You won the Bet.</p>';
                            $body .= "<table><tr><th>Home Team</th><td>" . $data['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $data['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $data['matchtime']) . "</td></tr><tr><th>Coins</th><td>" . $amount . "</td></tr></table>";

                            $body .= '<p>On behalf of our team and the company as a whole, I wish you unlimited success in the future.</p>';
                            //$body .= '<p>Admin charge '.$commission.' returned.</p>';
                            $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                            $body .= "<br /><strong>Good luck!! </strong>";
                            $this->users->mail($to, $subject, $body);

                            $looserdata = $this->users->getUserById($looser_id);

                            $to = $looserdata['useremail'];
                            $subject = 'Merrona Bet Notification';
                            $body = "<h3>Hello," . $looserdata['display_name'] . "</h3>";
                            $body .= "<p>We express regret on your defeat today. We want you to remember that life's greatest lessons are gained from losses. </p>";
                            $body .= "<table><tr><th>Home Team</th><td>" . $data['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $data['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $data['matchtime']) . "</td></tr><tr><th>Coins</th><td>" . $amount . "</td></tr></table>";
                            //$body .= '<p>Admin charge '.$commission.' applied.</p>';
                            $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                            $body .= "<br /><strong>Good luck!! </strong>";

                            $this->users->mail($to, $subject, $body);
                        }
                    } else {
                        $args_b['status'] = 'cancel';
                        $this->betting->UpdateBettingData($args_b, $request_id);

                        $userdata = $this->users->getUserById($sender);
                        $balance = $userdata['wallet_balance'];
                        $total = floatval($balance) + floatval($amount);
                        $arg_c['wallet_balance'] = $total;
                        $this->users->updateUser($arg_c, $sender);

                        $args_w['user_id'] = $sender;
                        $args_w['transaction_type'] = 'bet_cancel';
                        $args_w['amount'] = floatval($amount);
                        $args_w['transfer_id'] = $request_id;
                        $args_w['status'] = 'success';
                        $this->users->add_wallet_history($args_w);

                        $extrainfo['bet_id'] = $request_id;
                        $act_msg = 'bethistory';
                        $message = "hello, your bet canceled on game.Please check on history";
                        $this->users->SendPushNotification($sender, $message, $act_msg, $extrainfo);

                        $to = $userdata['useremail'];
                        $subject = 'Merrona Bet Cancel Notification';
                        $body = "<h3>Hello," . $userdata['display_name'] . "</h3>";
                        $body .= "<p>Your bet was canceled because your opponent was late to respond to the bet request. No transaction has been taken place.</p>";
                        $body .= "<table><tr><th>Home Team</th><td>" . $data['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $data['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $data['matchtime']) . "</td></tr><tr><th>Coins</th><td>" . $amount . "</td></tr></table>";
                        $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                        $body .= "<br /><strong>Good luck!! </strong>";

                        $this->users->mail($to, $subject, $body);
                    }
                }
            }
        }
    }

    /*
     * game type List
     * url:  http://merrona.com/authscript/betting/gametypes
     */

    function gametypes() {

        $gametypes[0]['ID'] = 0;
        $gametypes[0]['key'] = 'nfl';
        $gametypes[0]['value'] = 'NFL';

        $gametypes[1]['ID'] = 1;
        $gametypes[1]['key'] = 'soccer';
        $gametypes[1]['value'] = 'Soccer';

        $gametypes[2]['ID'] = 2;
        $gametypes[2]['key'] = 'cricket';
        $gametypes[2]['value'] = 'Cricket';

        $gametypes[3]['ID'] = 3;
        $gametypes[3]['key'] = 'tennis';
        $gametypes[3]['value'] = 'Tennis';

        $gametypes[4]['ID'] = 4;
        $gametypes[4]['key'] = 'boxing';
        $gametypes[4]['value'] = 'Boxing';

        $gametypes[5]['ID'] = 5;
        $gametypes[5]['key'] = 'golf';
        $gametypes[5]['value'] = 'Golf';

        $gametypes[6]['ID'] = 6;
        $gametypes[6]['key'] = 'mma';
        $gametypes[6]['value'] = 'MMA';

        $gametypes[7]['ID'] = 7;
        $gametypes[7]['key'] = 'nhl';
        $gametypes[7]['value'] = 'NHL';

        $gametypes[8]['ID'] = 8;
        $gametypes[8]['key'] = 'mlb';
        $gametypes[8]['value'] = 'MLB';

        $gametypes[9]['ID'] = 9;
        $gametypes[9]['key'] = 'nba';
        $gametypes[9]['value'] = 'NBA';

        $gametypes[10]['ID'] = 10;
        $gametypes[10]['key'] = 'ncaab';
        $gametypes[10]['value'] = 'NCAAB';

        $gametypes[11]['ID'] = 11;
        $gametypes[11]['key'] = 'ncaaf';
        $gametypes[11]['value'] = 'NCAAF';

        echo json_encode($gametypes);
    }

    /*
     * game List
     * url:  http://merrona.com/authscript/betting/gamelists
     */

    function gamelists() {
        $uri_game = $this->helper->getUriSegment(2);
        $gametype = (strlen(trim($uri_game)) > 0 ? $uri_game : 'soccer');

        $c_time = time();
        $n_time = strtotime('+1 day', time());
        $result = $this->helper->db_select("*", "tb_soccerlist", "WHERE `matchtime` > $c_time && `gametype`='$gametype' order by `matchtime` ASC");
        if ($result->num_rows > 0) {
            $i = 0;
            while ($data = $result->fetch_assoc()) {
                $data['matchtime_c'] = date('Y-m-d H:i:s', $data['matchtime']);
                $json[$i] = $data;
                $i++;
            }
            echo json_encode($json);
            die;
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No data found';
            echo json_encode($json);
            die;
        }
    }

    function specificGameUsers() {
        $uri_game = $this->helper->getUriSegment(2);
        $gametype = (strlen(trim($uri_game)) > 0 ? $uri_game : 'soccer');

        $result = $this->helper->custom_query("SELECT *,tb_gamerequest.ID as transfer_id FROM  `tb_soccerlist` JOIN  `tb_gamerequest` ON tb_soccerlist.event_id = tb_gamerequest.game_id JOIN  `tb_users` ON tb_users.ID = tb_gamerequest.sender WHERE tb_soccerlist.`gametype`='$gametype' and tb_gamerequest.status='complete'  order by tb_users.`wallet_balance` DESC LIMIT 0,15");
        if ($result->num_rows > 0) {
            $i = 0;
            $test = array();
            while ($data = $result->fetch_assoc()) {
                $transfer_id = $data['transfer_id'];
                $testing[$transfer_id] = $transfer_id;
                $result1 = $this->users->getwinsCount($transfer_id, 'bet_win');
//               if(!empty($result1)){
//                   $result1 = $this->users->getwinsCount($transfer_id,'bet_draw');
//               }
                $sender_detail = $this->users->getUserById($data['sender']);
                if (array_key_exists($sender_detail["useremail"], $test)) {
                    $test[$sender_detail["useremail"]] = ($test[$sender_detail["useremail"]] + 1);
                } else {
                    $test[$sender_detail["useremail"]] = 1;
                }

                if (array_key_exists($result1["user_id"], $test)) {
                    $test[$result1["user_id"]] = $test[$result1["user_id"]] + 1;
                } else {
                    $test[$result1["user_id"]] = 1;
                }
                $receiver_detail = $this->users->getUserById($data['receiver']);
                if (array_key_exists($receiver_detail["useremail"], $test)) {
                    $test[$receiver_detail["useremail"]] = $test[$receiver_detail["useremail"]] + 1;
                } else {
                    $test[$receiver_detail["useremail"]] = 1;
                }
                $i++;
            }
            $ddsfsfdsf = array();
            
            foreach ($testing as $testing1) {
                $detailsdsfsf = $this->users->getDrawMatches($testing1);
                // print_r($detailsdsfsf);
                if(!empty($detailsdsfsf)){
                    while ($data4 = $detailsdsfsf->fetch_assoc()) {
                        if (array_key_exists($data4["user_id"], $ddsfsfdsf)) {
                            $ddsfsfdsf[$data4["user_id"]] = $ddsfsfdsf[$data4["user_id"]] + 1;
                        } else {
                            $ddsfsfdsf[$data4["user_id"]] = 1;
                        }
                    }
                }
            }
            foreach ($test as $key => $test1) {
                if (is_numeric($key)) {
                    $betwin = $key;
                    $display_name1 = $this->users->getUserById($key);
                    $useremail[] = $display_name1["useremail"];
                    $betwinVal[] = $test1;
                }
            }

            $j = 0;
            foreach ($test as $key => $test1) {

                $display_name = $key;
                if (!is_numeric($key)) {
                    //  print_r($testing);

                    $key3 = array_search($display_name, $useremail);
                    if ($key3 !== false) {
                        $amount = $betwinVal[$key3];
                    } else {
                        $amount = 0;
                    }
                    

                    if ($test1 == $amount) {
                        $betloss = 0;
                    } else {
                        $betloss = $test1 - $amount;
                    }
                    $details = $this->users->getuserByemail($display_name);
                    //   print_r($details);
                    if (!empty($details)) {
                        if (array_key_exists($details["user_id"], $ddsfsfdsf)) {
                            $amount2 = $ddsfsfdsf[$details["user_id"]];
                        } else {
                            $amount2 = 0;
                        }
                        
                        if($betloss==$amount2)
                            $betloss=0;
                        else
                            $betloss=$test1-$amount2;
                        
                        $test2[$j]["display_name"] = $details['display_name'];
                        $test2[$j]["profile_pic"] = $details['profile_pic'];
                        $test2[$j]["total_coins"] = $details['wallet_balance'];
                        $test2[$j]["total_matches"] = $test1;
                        $test2[$j]["bet_win"] = $amount;
                        $test2[$j]["bet_loss"] = $betloss;
                        $test2[$j]["bet_draw"] = $amount2;
                        $j++;
                    }
                }
            }
           $this->sort_array_of_array($test2, 'total_coins');
            foreach($test2 as $key=>$value){
                foreach($value as $key6=>$value6){
                    $test3[$key6] = $value6;
                }
                 $json[$key] = $test3;
            }
            echo json_encode($json);
            die;
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'No data found';
            echo json_encode($json);
            die;
        }
    }

    function sort_array_of_array(&$array, $subfield) {
        $sortarray = array();
        foreach ($array as $key => $row) {
            $sortarray[$key] = $row[$subfield];
        }

        array_multisort($sortarray, SORT_DESC, $array);
    }

    function accept() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sender = $_REQUEST['sender'];
            $receiver = $_REQUEST['receiver'];
            $game_id = $_REQUEST['game_id'];
            $id = $_REQUEST['id'];
            if (intval($id) == 0 || strlen($game_id) == 0 || intval($receiver) == 0 || intval($sender) == 0) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'please fill all data';
                echo json_encode($json);
                die;
            }

            if ($this->betting->CheckGameRequest($sender, $receiver, $game_id)) {
                $data = $this->betting->CheckGameRequest($sender, $receiver, $game_id);
                $betAmount=$data['amount'];
              //  $total_charge = $data['amount'] + $data['commission'];
                $userdata = $this->users->getUserById($receiver);
                if ($userdata['wallet_balance'] < $betAmount) {
                    $json[0]['result'] = 2;
                    $json[0]['msg'] = 'You do not have sufficient coins';
                    echo json_encode($json);
                    die;
                }

                if ($data['ID'] != $id) {
                    $json[0]['result'] = 0;
                    $json[0]['msg'] = 'Something wrong check currect';
                    echo json_encode($json);
                    die;
                }
                $game_detail = $this->betting->GameDetails($game_id);
                if ($game_detail['matchtime'] < time()) {
                    $json[0]['result'] = 2;
                    $json[0]['msg'] = 'Time out game started';
                    echo json_encode($json);
                    die;
                }
                $validextensions = array("jpeg", "jpg", "png");
                $temporary = explode(".", $_FILES["signature"]["name"]);
                $file_extension = end($temporary);
                $newfilename = '';
                if (($_FILES["signature"]["type"] == "image/png") || ($_FILES["signature"]["type"] == "image/jpg") || ($_FILES["signature"]["type"] == "image/jpeg")) {
                    $sourcePath = $_FILES['signature']['tmp_name'];
                    $t = time();
                    $path = dirname($_SERVER["SCRIPT_FILENAME"]) . '/images/signature/';
                    $newfilename = $t . "." . $file_extension;
                    ;
                    $targetPath = $path . $newfilename;
                    move_uploaded_file($sourcePath, $targetPath);
                }
                $accept_request = $this->betting->AcceptRequest($id, $newfilename);
                if ($accept_request) {

                    $balance = $userdata['wallet_balance'];
                    $total = floatval($balance) - floatval($betAmount);
                    $args['wallet_balance'] = $total;
                    $this->users->updateUser($args, $receiver);

                    $args_w['user_id'] = $receiver;
                    $args_w['transaction_type'] = 'bet_accept';
                    $args_w['amount'] = $total;
                    $args_w['transfer_id'] = $id;
                    $args_w['status'] = 'success';
                    $this->users->add_wallet_history($args_w);

                    $sender_detail = $this->users->getUserById($receiver);
                    $extrainfo['bet_id'] = $id;
                    $act_msg = 'betaccept';
                    $message = "hello!! " . $sender_detail['display_name'] . " accepted invitation on game";
                    $this->users->SendPushNotification($sender, $message, $act_msg, $extrainfo);
                    $json[0]['result'] = 1;
                    $json[0]['msg'] = 'request accepted succesfully';
                    echo json_encode($json);

                    $receiver_detail = $this->users->getUserById($sender);

                    $to = $receiver_detail['useremail'];
                    $subject = 'Merrona Bet Accept Notification';
                    $body = "<h3>Hello," . $receiver_detail['display_name'] . "</h3>";
                    $body .= '<p>' . $sender_detail['display_name'] . ' accepted your bet request on Merrona:</p>';
                    $body .= "<table><tr><th>Home Team</th><td>" . $game_detail['hometeam'] . "</td></tr><tr><th>Away Team</th><td>" . $game_detail['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $game_detail['matchtime']) . "</td></tr><tr><th>Coins</th><td>" . $data['amount'] . "</td></tr></table>";
                    $body .= "<br /><strong>Stay Classy!! </strong>";
                    $body .= "<br /><strong>Good luck!! </strong>";

                    $this->users->mail($to, $subject, $body);

                    $to = $sender_detail['useremail'];
                    $subject = 'Merrona Bet accept details';
                    $body = "<h3>Hello," . $sender_detail['display_name'] . "</h3>";
                    $body .= '<p>You accepted bet request on game with ' . $receiver_detail['display_name'] . ' on Merrona:</p>';
                    $body .= "<table><tr><th>Home Team</th><td>" . $game_detail['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $game_detail['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $game_detail['matchtime']) . "</td></tr><tr><th>Coins</th><td>" . $data['amount'] . "</td></tr></table>";
                    //$body .= '<p>Please add a convience fee of 15% with the total amount you want to bet.</p>';
                    $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                    $body .= "<br /><strong>Good luck!! </strong>";

                    $this->users->mail($to, $subject, $body);

                    die;
                } else {
                    $json[0]['result'] = 0;
                    $json[0]['msg'] = 'Something went wrong';
                    echo json_encode($json);
                    die;
                }
            } else {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'Something wrong check currect request';
                echo json_encode($json);
                die;
            }
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'Please call required method 12';
            echo json_encode($json);
            die;
        }
    }

    /*
     * bet cancel
     * url:  http://merrona.com/authscript/betting/cancel
     */

    function cancel() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sender = $_REQUEST['sender'];
            $receiver = $_REQUEST['receiver'];
            $game_id = $_REQUEST['game_id'];
            $id = $_REQUEST['id'];
            if (intval($id) == 0 || strlen($game_id) == 0 || intval($receiver) == 0 || intval($sender) == 0) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'please fill all data';
                echo json_encode($json);
                die;
            }

            if ($this->betting->CheckGameRequest($sender, $receiver, $game_id)) {
                $data = $this->betting->CheckGameRequest($sender, $receiver, $game_id);
                $total_charge = $data['amount'] + $data['commission'];
                $userdata = $this->users->getUserById($receiver);

                if ($data['ID'] != $id) {
                    $json[0]['result'] = 0;
                    $json[0]['msg'] = 'Something wrong check currect';
                    echo json_encode($json);
                    die;
                }
                $game_detail = $this->betting->GameDetails($game_id);
                if ($game_detail['matchtime'] < time()) {
                    $json[0]['result'] = 2;
                    $json[0]['msg'] = 'Time out game started';
                    echo json_encode($json);
                    die;
                }

                $accept_request = $this->betting->CancelRequest($id);
                if ($accept_request) {

                    $balance = $userdata['wallet_balance'];
                    $total = floatval($balance) + floatval($total_charge);
                    $args['wallet_balance'] = $total;
                    $this->users->updateUser($args, $sender);

                    $args_w['user_id'] = $receiver;
                    $args_w['transaction_type'] = 'bet_accept';
                    $args_w['amount'] = $total_charge;
                    $args_w['transfer_id'] = $id;
                    $args_w['status'] = 'success';
                    $this->users->add_wallet_history($args_w);

                    $sender_detail = $this->users->getUserById($receiver);
                    $extrainfo['bet_id'] = $id;
                    $act_msg = 'bethistory';
                    $message = "hello!! " . $sender_detail['display_name'] . " cancel invitation on game";
                    $this->users->SendPushNotification($sender, $message, $act_msg, $extrainfo);
                    $json[0]['result'] = 1;
                    $json[0]['msg'] = 'request canceled';
                    echo json_encode($json);

                    $receiver_detail = $this->users->getUserById($sender);

                    $to = $receiver_detail['useremail'];
                    $subject = 'Merrona Bet Cancel Notification';
                    $body = "<h3>Hello," . $receiver_detail['display_name'] . "</h3>";
                    $body .= '<p>' . $sender_detail['display_name'] . ' cancel bet request on Merrona:</p>';
                    $body .= "<table><tr><th>Home Team</th><td>" . $game_detail['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $game_detail['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $game_detail['matchtime']) . "</td></tr><tr><th>Amount</th><td>" . $amount . "</td></tr></table>";
                    $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                    $body .= "<br /><strong>Good luck!! </strong>";

                    $this->users->mail($to, $subject, $body);


                    die;
                } else {
                    $json[0]['result'] = 0;
                    $json[0]['msg'] = 'Something went wrong';
                    echo json_encode($json);
                    die;
                }
            } else {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'Something wrong check currect request';
                echo json_encode($json);
                die;
            }
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'Please call required method 12';
            echo json_encode($json);
            die;
        }
    }

    function request() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sender = $_POST['sender'];
            $receiver = $_POST['receiver'];
            $game_id = $_POST['game_id'];
            $amount = $_POST['amount'];
            $betteam = $_POST['betteam'];
          //  $commission = ($amount * $GLOBALS['commission']) / 100;
         //   $total_charge = $amount + $commission;
            $userdata = $this->users->getUserById($sender);
            if ($userdata['wallet_balance'] < $amount) {
                $json[0]['result'] = 2;
                $json[0]['msg'] = 'You do not have sufficient coins';
                echo json_encode($json);
                die;
            }

            $game_detail = $this->betting->GameDetails($game_id);
            if ($game_detail['matchtime'] < time()) {
                $json[0]['result'] = 2;
                $json[0]['msg'] = 'Time out game started';
                echo json_encode($json);
                die;
            }

            if ($this->betting->CheckGameRequest($sender, $receiver, $game_id)) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'request already sent';
                echo json_encode($json);
                die;
            }

            $newfilename = '';
            $validextensions = array("jpeg", "jpg", "png");
            $temporary = explode(".", $_FILES["signature"]["name"]);
            $file_extension = end($temporary);
            $newfilename = '';
            if (isset($_FILES["signature"])) {
                if (($_FILES["signature"]["type"] == "image/png") || ($_FILES["signature"]["type"] == "image/jpg") || ($_FILES["signature"]["type"] == "image/jpeg")) {
                    $sourcePath = $_FILES['signature']['tmp_name'];
                    $t = time();
                    $path = dirname($_SERVER["SCRIPT_FILENAME"]) . '/images/signature/';
                    $newfilename = $t . "." . $file_extension;
                    ;
                    $targetPath = $path . $newfilename;
                    move_uploaded_file($sourcePath, $targetPath);
                }
            }
            $allusers = $this->betting->GameRequest($sender, $receiver, $game_id, $newfilename, $amount, $betteam);

            if ($allusers) {

                $balance = $userdata['wallet_balance'];
                $total = floatval($balance) - floatval($amount);
                $args['wallet_balance'] = $total;
                $this->users->updateUser($args, $sender);

                $args_w['user_id'] = $sender;
                $args_w['transaction_type'] = 'bet_request';
                $args_w['amount'] = $amount;
                $args_w['transfer_id'] = $allusers;
                $args_w['status'] = 'success';
                $this->users->add_wallet_history($args_w);

                $sender_detail = $this->users->getUserById($sender);
                $extrainfo['bet_id'] = $allusers;
                $message = "hello!! " . $sender_detail['display_name'] . " just invited you to play";
                $this->users->SendPushNotification($receiver, $message, 'betrequest', $extrainfo);
                $json[0]['result'] = 1;
                $json[0]['msg'] = 'request sent succesfully';
                echo json_encode($json);

                $receiver_detail = $this->users->getUserById($receiver);

                $to = $receiver_detail['useremail'];
                $subject = 'Merrona Bet Request';
                $body = "<h3>Hello," . $receiver_detail['display_name'] . "</h3>";
                $body .= '<p>' . $sender_detail['display_name'] . ' sent you a bet request on Merrona:</p>';
                $body .= "<table><tr><th>Home Team</th><td>" . $game_detail['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $game_detail['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $game_detail['matchtime']) . "</td></tr><tr><th>Coins</th><td>".$amount."</td></tr></table>";
                $body .= "<p>Please log in and accept.</p>";
                //$body .= '<p>Please add a convience fee of 15% with the total amount you want to bet.</p>';
                $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                $body .= "<br /><strong>Good luck!! </strong>";

                $this->users->mail($to, $subject, $body);

                $to = $sender_detail['useremail'];
                $subject = 'Merrona Bet Request Notification';
                $body = "<h3>Hello," . $sender_detail['display_name'] . "</h3>";
                $body .= '<p>This is a notification to remind you that you sent ' . $receiver_detail['display_name'] . ' a bet request on Merrona : </p>';
                $body .= "<table><tr><th>Home Team</th><td>" . $game_detail['hometeam'] . "</td></tr><tr><th>Other Team</th><td>" . $game_detail['awayteam'] . "</td></tr><tr><th>Match Time</th><td>" . date('Y-m-d H:i:s', $game_detail['matchtime']) . "</td></tr><tr><th>Coins</th><td>".number_format($amount, 2, ',', ' ')."</td></tr></table>";
                //$body .= '<p>Please remember that you are charged a convience fee of 15% on the total bet,  if you happen to lose the bet.</p>';
                $body .= "<br /> <br /><strong>Stay Classy!! </strong>";
                $body .= "<br /><strong>Good luck!! </strong>";

                $this->users->mail($to, $subject, $body);

                die;
            } else {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'Something went wrong';
                echo json_encode($json);
                die;
            }
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'Please call required method';
            echo json_encode($json);
            die;
        }
    }

    /*
     * change user status
     * url:  http://merrona.com/authscript/betting/userlivebet/40
     */

    function userlivebet() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $this->helper->getUriSegment(2);
            if (intval($user_id) == 0) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'Please fill required data';
                echo json_encode($json);
                die;
            }
            $all = $this->betting->UserLiveBet($user_id);
            if (!$all) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'No bet available';
                echo json_encode($json);
                die;
            }
            $i = 0;
            while ($data = $all->fetch_assoc()) {
                $user1 = ($data['sender'] == $user_id ? $data['receiver'] : $data['sender']);
                $game_detail = $this->betting->GameDetails($data['game_id']);
                $sender_detail = $this->users->getUserById($user1);

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

                $json[$i]['sender_id'] = $sender_detail['user_id'];
                $json[$i]['sender_name'] = $sender_detail['display_name'];
                $json[$i]['sender_email'] = $sender_detail['useremail'];
                $json[$i]['sender_age'] = $sender_detail['age'];
                $json[$i]['sender_pic'] = $sender_detail['profile_pic'];
                $json[$i]['amount'] = $data['amount'];
                $i++;
            }
            echo json_encode($json);
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'Please call required method';
            echo json_encode($json);
            die;
        }
    }

    /*
     * change user status
     * url:  http://merrona.com/authscript/betting/userbethistory/40
     */

    function userbethistory() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $user_id = $this->helper->getUriSegment(2);
            if (intval($user_id) == 0) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'Please fill required data';
                echo json_encode($json);
                die;
            }
            $all = $this->betting->UserBetHistory($user_id);
            if (!$all) {
                $json[0]['result'] = 0;
                $json[0]['msg'] = 'No bet available';
                echo json_encode($json);
                die;
            }
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
            echo json_encode($json);
        } else {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'Please call required method';
            echo json_encode($json);
            die;
        }
    }

    /*
     * change user status
     * url:  http://merrona.com/authscript/betting/requestnotification/40
     */

    function requestnotification() {
        $user_id = $this->helper->getUriSegment(2);
        if (intval($user_id) == 0) {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'Please fill required data';
            echo json_encode($json);
            die;
        }

        $all = $this->betting->RequestNotification($user_id);
        if (!$all) {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'no data found';
            echo json_encode($json);
            die;
        }
        $i = 0;
        while ($data = $all->fetch_assoc()) {
            $sender_detail = $this->users->getUserById($data['sender']);
            if ($sender_detail) {
                $game_detail = $this->betting->GameDetails($data['game_id']);
                if ($game_detail['matchtime'] > time()) {
                    $json[$i]['id'] = $data['ID'];
                    $json[$i]['sender_id'] = $data['sender'];
                    $json[$i]['game_id'] = $data['game_id'];
                    $json[$i]['betteam'] = $data['betteam'];

                    $json[$i]['home_team'] = $game_detail['hometeam'];
                    $json[$i]['away_team'] = $game_detail['awayteam'];
                    $json[$i]['matchtime'] = $game_detail['matchtime'];
                    $json[$i]['details'] = $game_detail['details'];
                    $json[$i]['league_name'] = $game_detail['league_name'];
                    $json[$i]['display_region'] = $game_detail['display_region'];
                    $json[$i]['c_matchtime'] = date('Y-m-d H:i:s', $game_detail['matchtime']);

                    $json[$i]['sender_name'] = $sender_detail['display_name'];
                    $json[$i]['sender_email'] = $sender_detail['useremail'];
                    $json[$i]['sender_age'] = $sender_detail['age'];
                    $json[$i]['sender_pic'] = $sender_detail['profile_pic'];
                    $json[$i]['amount'] = $data['amount'];
                    $i++;
                }
            }
        }
        if ($i == 0) {
            $json[0]['result'] = 0;
            $json[0]['msg'] = 'no data found' . $game_detail['matchtime'] . ' ' . time();
            echo json_encode($json);
            die;
        }
        echo json_encode($json);
        die;
    }

}

?>