<div class="search_customers">
               <div class="my_ser_form">
                  <div class="my_se_heading ripple-effect">
                     <h2>Completed Bet History<i class="fa fa-angle-double-right" aria-hidden="true"></i></h2>
                  </div>
                  <div class="customers_tables">
                     <div class="form-group pull-right serch_filterss">
                        <input type="text" class="search form-control" placeholder="Search...">
                     </div>
                     <span class="counter pull-right"></span>
                     <?php if($table_data[0]['result']==0 && $table_data[0]['msg']!=''){
                           echo "<h3>".$table_data[0]['msg']."</h3>";
                           }else{ ?> 
                     <div class="table-responsive my_datas_tbles">
                     
                        <table class="table table-hover results table-striped">
                           <thead>
                              <tr>
                                 <th class="text-center col-md-1">#</th>
                                 <th>Date</th>
                                 <th>Time</th>
                                 <th>Home Team</th>
                                 <th>Away Team</th>
                                 <th>Coins</th>
                                 <th>Winner Name</th>
                                 <th>View</th>

                              </tr>
                              <tr class="warning no-result">
                                 <td colspan="4"><i class="fa fa-warning"></i> No result</td>
                              </tr>
                           </thead>
                           <tbody>
                           <?php 
                           $link = $_SERVER['REQUEST_URI'];
                            $link_array = explode('/',$link);
                            $page = end($link_array);
                            if(is_numeric($page)){
                                $count=($page*20)-19;
                            }else{
                                $count=1;
                            }
                           foreach($table_data as $bets){ ?>
                              <tr>
                                 <td class="text-center"><?php echo $count; ?></td>
                                 <td><?php echo date("d-m-Y",strtotime($bets['c_matchtime'])); ?></td>
                                 <td><?php echo date('h:i A', strtotime($bets['c_matchtime'])); ?></td>
                                 <td><?php echo $bets['home_team']; ?></td>
                                 <td><?php echo $bets['away_team']; ?></td>
                                 <td><?php echo $bets['amount']; ?></td>
                                 <td><?php echo $bets['winner']; ?></td>
                                 <td><button type="button" game_id="<?= $bets['game_id'] ?>" bet_id="<?= $bets['id'] ?>" class="mybtnedit ripple-effect complete_view">View</button></td>
                              </tr>
                              <?php $count++; } ?>
                              
                           </tbody>
                        </table>
                         <?php if(isset($pagination)) echo $pagination; ?>
                     </div>
                     <?php } ?>
                  </div>
               </div>
            </div>