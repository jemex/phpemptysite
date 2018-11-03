<div class="search_customers">
               <div class="my_ser_form">
                  <div class="my_se_heading ripple-effect">
                     <h2>Notification Detail<i class="fa fa-angle-double-right" aria-hidden="true"></i></h2>
                  </div>
                  <div class="main-notification">
                     <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                           <div class="col-md-3 col-sm-5 col-xs-12">
                              <div class="Notification-img">
                                 <?php if(isset($table_data['profile_pic'])){ ?>
                                 <img src="<?php echo $table_data['profile_pic'] ?>" class="img-responsive">
                                 <?php }else{ ?>
                                   <p>Image not available</p>
                                 <?php } ?>
                              </div>
                              
                           </div>
                          
                           <div class="col-md-8 col-sm-7 col-xs-12">
                              <div class="profile-body">
                                 <ul class="list-info">
                                    <li class="ng-binding">
                                       <i class="fa fa-user icon" aria-hidden="true"></i>
                                       <label>User name</label>
                                       <span><?php echo $table_data['display_name'] ?></span>
                                    </li>
                                    <li class="ng-binding">
                                       <i class="fa fa-envelope" aria-hidden="true"></i>
                                       <label>Email</label>
                                       <span><?php echo $table_data['useremail'] ?></span>
                                    </li>
                                    <li>
                                       <i class="fa fa-phone" aria-hidden="true"></i>
                                       <label>Contact</label>
                                       <span> <?php echo $table_data['contact'] ?></span>
                                    </li>
                                    <li>
                                       <i class="fa fa-calendar icon" aria-hidden="true"></i>
                                       <label>Description</label>
                                       <span><?php echo $table_data['description'] ?></span>
                                    </li>
                                    <li>
                                       <i class="fa fa-users icon" aria-hidden="true"></i>
                                       <label>Age</label>
                                       <span> <?php echo $table_data['age'] ?></span>
                                    </li>
                                    <li>
                                       <i class="fa fa-google-wallet" aria-hidden="true"></i>
                                       <label>Total Coins</label>
                                       <span><?php echo $table_data['wallet_balance'] ?></span>
                                    </li>
                                 </ul>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>