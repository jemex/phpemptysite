<div class="search_customers">
               <div class="my_ser_form">
                  <div class="my_se_heading ripple-effect">
                     <h2>User List History<i class="fa fa-angle-double-right" aria-hidden="true"></i></h2>
                  </div>
                  <div class="customers_tables">
                     <div class="form-group pull-right serch_filterss">
                        <input type="text" class="search form-control" placeholder="Search...">
                     </div>
                     <span class="counter pull-right"></span>
                     <div class="table-responsive my_datas_tbles">
                        <table class="table table-hover results table-striped">
                           <thead>
                              <tr>
                                 <th class="text-center col-md-1">#</th>
                                 <th>Name</th>
                                 <th>E-mail</th>
                                 <th>Total Coins</th>
                                 <th>Bet History</th>
                                 <th>Account History</th>
                                 <th>View</th>
                              </tr>
                              <tr class="warning no-result">
                                 <td colspan="4"><i class="fa fa-warning"></i> No result</td>
                              </tr>
                           </thead>
                           <tbody>
                           <?php $count=1; 
                           
                           foreach($table_data as $userdetails){ ?>
                              <tr>
                                 <td class="text-center"><?php echo $count; ?></td>
                                 <td><?php echo $userdetails['display_name'] ?></td>
                                 <td><?php echo $userdetails['useremail'] ?></td>
                                 <td><?php echo $userdetails['wallet_balance'] ?></td>
                                 <td><a href="<?php echo $GLOBALS['ep_dynamic_url'] ?>adminmain/userbethistory/<?php echo  $userdetails['user_id'] ?>" class="btn btn-primary">Bet History</a></td>
                                 <td><a href="<?php echo $GLOBALS['ep_dynamic_url'] ?>adminmain/useraccountHistory/<?php echo  $userdetails['user_id'] ?>" class="btn btn-primary">Account History</a></td>
                                 <td><a href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/userprofile/<?php echo  $userdetails['user_id'] ?>"><input type="submit" value="View" class="mybtnedit ripple-effect" id=""></a></td>
                              </tr>
                              <?php $count++; } ?>
                             
                           </tbody>
                        </table>
                     </div>
                  </div>
               </div>
            </div>