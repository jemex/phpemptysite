<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Merrona</title>
<!--bootstrap css-->
<link href="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/css/vendor/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- default css -->
<link href="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/css/styles.css" rel="stylesheet" type="text/css" />
<!--amimate css-->
<link href="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/css/animate/animate.css" rel="stylesheet" type="text/css" />
 <link href="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/css/font-awesome.css" rel="stylesheet" type="text/css" /> 


</head>
<body>

  <section class="wrapper">
    <div class="parents_div">
      <div class="sidebar">
        <div class="my_sidebars">
        <div class="logo2">
            <a class="simple-text" href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain">
                <div class="logo-img">
                    <img src="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/images/logo1.png">
                </div>
            </a>
        </div>
        <ul id="tabs" class="nav" role="tablist">
          <li role="presentation" class="ripple-effect" id="clickbtns">
            <a href="javascript:void(0)" role="tab" data-toggle="tab" class="myyyy">
              <i class="fa fa-history" aria-hidden="true"></i>Bet Histroy
              <i class="fa fa-caret-down pull-right" aria-hidden="true"></i>
            </a>
            <ul class="list-unstyled" id="listupdwn" style="display:none">
              <li><a href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/completeBet" class="Completed"> <i class="fa fa-list" aria-hidden="true"></i> Completed Bet</a></li>
              <li><a href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/liveBet"> <i class="fa fa-desktop" aria-hidden="true"></i>Live Bet</a></li>
              <!-- <li><a href="notification.html"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> All Bet Notification</a></li> -->
            </ul>
          </li>

        
          <li role="presentation" class="ripple-effect" id="clickbtns">
            <a href="javascript:void(0)" role="tab" data-toggle="tab" class="myyyy">
              <i class="fa fa-user" aria-hidden="true"></i>  User History
              <i class="fa fa-caret-down pull-right" aria-hidden="true"></i>
            </a>
            <ul class="list-unstyled" id="listupdwn" style="display:none">
              <li><a href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/allusers"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> User List</a></li>
              <li><a href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/accountHistory"> <i class="fa fa-user" aria-hidden="true"></i> Account History</a></li>
            </ul>
          </li>
<!--          <li role="presentation" class="ripple-effect">
            <a href="<?php //echo $GLOBALS['ep_dynamic_url']; ?>adminmain/commisionAmount"><i class="fa fa-money" aria-hidden="true"></i>Commission amount</a>
          </li>  -->
         <li role="presentation" class="ripple-effect">
            <a href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/changePassword"><i class="fa fa-lock" aria-hidden="true"></i>Change Password</a>
          </li> 
          <li role="presentation" class="ripple-effect">
            <a href="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/adminlogout">
              <i class="fa fa-power-off" aria-hidden="true"></i>  Sign Out
            </a>
          </li>
        </ul>
        <!-- <div class="hr_dv"><hr/></div>  --> 
        </div>
      </div>
      <div class="right_bar">
        <div class="bars_set visible-xs">
          <i class="fa fa-bars" aria-hidden="true"></i>
        </div>
        <div class="loding_content">
            <div class="main-header">
           <div class="header-serch pull-right">
               <input type="text" placeholder="Search..." class="form-control"> <a href="" class="active"><i class="fa fa-search"></i></a>
               <a class="profile-pic" href="#"> <img src="<?php echo $GLOBALS['ep_dynamic_url']; ?>view/images/logo1.png" alt="user-img" width="36" class="img-circle"><b class="hidden-xs"><?php if(isset($_SESSION["admin_username"])) echo $_SESSION["admin_username"]; ?></b></a> 
           </div>

        </div>
            <input type="hidden" id="site_url" value="<?= $GLOBALS['ep_dynamic_url'] ?>"/>