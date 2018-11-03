<div class="search_customers">
               <div class="my_ser_form">
                  <div class="my_se_heading ripple-effect">
                     <h2>User Account Detail History<i class="fa fa-angle-double-right" aria-hidden="true"></i></h2>
                  </div>
                  <div class="customers_tables">
                     <div class="form-group pull-right serch_filterss">
                        <input type="text" class="search form-control" placeholder="Search Account">
                     </div>
                     <span class="counter pull-right"></span>
                     <?php if($table_data[0]['result']==0 && $table_data[0]['msg']!=''){
                           echo "<h3>".$table_data[0]['msg']."</h3>";
                           }else if($table_data[0]['ID']!=0){ ?> 
                     <div class="table-responsive my_datas_tbles">
                        <table class="table table-hover results table-striped">
                           <thead>
                              <tr>
                                 <th class="text-center col-md-1">#</th>
                                 <th>Date</th>
                                 <th>Time</th>
                                 <th>Transaction Type</th>
                                 <th>Status</th>
                                 <th>Coins</th>
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
                            foreach($table_data as $history){ ?>
                              <tr>
                                 <td class="text-center"><?= $count; ?></td>
                                 <td><?php echo date('Y-m-d', strtotime($history['entry_time'])); ?></td>
                                 <td><?php echo date('h:i A', strtotime($history['entry_time'])); ?></td>
                                 <td><?php echo ucfirst(str_replace("_"," ",$history['transaction_type'])); ?></td>
                                 <td><?php echo $history['status']; ?></td>
                                 <td><?php echo $history['amount']; ?></td>
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