<div class="search_customers">
               <div class="my_ser_form">
                  <div class="my_se_heading ripple-effect">
                     <h2>Edit Profile<i class="fa fa-angle-double-right" aria-hidden="true"></i></h2>
                  </div>
                  <div class="main-notification">
                     <div class="row">
                        <div class="col-md-12 col-sm-12 col-xs-12">
                        <?php if($table_data[0]['result']==0 && $table_data[0]['msg']!=''){
                           echo "<h3><i class='fa fa-warning'></i>".$table_data[0]['msg']."</h3>";
                           } ?>
                        <form method="post" action="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/updateAdminProfile/52" enctype="multipart/form-data">
                           <div class="col-md-3 col-sm-4 col-xs-12">
                              <!-- <div class="Notification-img">
                                 <img src="assets/images/g1.jpg">
                                 </div> -->
                              <div class="label-content">
                                 <label>Profile Image</label>
                                 <div class="upld_groupf">
                                    <i class="file-image">
                                    <input  id="_" name="profile_pic"  type="file" onchange="readImage(this)" title="" />
                                    <i class="reset" onclick="resetImage(this.previousElementSibling)"></i>
                                    <?php if(isset($table_data['profile_pic'])){ ?>
                                    <label for="_" class="image" style="background-image: url('<?php echo $table_data['profile_pic']; ?>')"></label>
	                              <?php }else{ ?>
	                              <label for="_" class="image"></label>
	                              <?php } ?>     
                                    </i>
                                 </div>
                                 <!--IMAGE DIV-->
                              </div>
                           </div>
                           <div class="col-md-9 col-sm-8 col-xs-12">
                              <div class="col-md-12 col-sm-12 col-xs-12 ro-input-form">
                                 <div class="form-group">
                                    <input id="user" class="ro-input-text ro-color-picton_blue ro-font form-control" value="<?php echo $table_data['display_name'] ?>" type="text" placeholder="User Name" name="display_name" />
                                    <label for="user">
                                       <p class="ro-font">User Name</p>
                                    </label>
                                 </div>
                              </div>
                              <div class="col-md-12 col-sm-12 col-xs-12 ro-input-form">
                                 <div class="form-group">
                                    <input id="email2" class="ro-input-text ro-color-picton_blue ro-font form-control" value="<?php echo $table_data['useremail'] ?>" type="email" placeholder="info@gmail.com" name="useremail" />
                                    <label for="email2">
                                       <p class="ro-font">Email Address</p>
                                    </label>
                                 </div>
                              </div>
                              
                              <div class="col-md-12 col-sm-12 col-xs-12 ro-input-form">
                                 <div class="form-group">
                                    <input id="email2" class="ro-input-text ro-color-picton_blue ro-font form-control" value="<?php echo $table_data['age'] ?>" type="number" placeholder="info@gmail.com" name="age" />
                                    <label for="email2">
                                       <p class="ro-font">Age</p>
                                    </label>
                                 </div>
                              </div>
                             
                              <div class="col-md-12 col-sm-12 col-xs-12 ro-input-form">
                                 <div class="form-group">
                                    <input id="dateb" class="ro-input-text ro-color-picton_blue ro-font form-control" value="<?php echo $table_data['contact'] ?>" type="text"  name="contact" />
                                    <label for="dateb">
                                       <p class="ro-font">Contact</p>
                                    </label>
                                 </div>
                              </div>
                              
                              <div class="col-xs-12 ">
                                <input type="submit" value="Update" class="mybtns45 ripple-effect" id="">  
                              </div>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>