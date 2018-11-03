<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login Page</title>
<!--bootstrap css-->
<link href="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/css/vendor/bootstrap.min.css" rel="stylesheet" type="text/css" />
<!-- default css -->
<link href="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/css/styles.css" rel="stylesheet" type="text/css" />
<!--amimate css-->
<link href="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/css/animate/animate.css" rel="stylesheet" type="text/css" />
<link href="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/css/font-awesome.css" rel="stylesheet" type="text/css" />
</head>
<body>
<div class="dlogin">
  <div class="center-dlogin"> <img src="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/images/logo1.png">
    <div class="dlogin-from">
      <!-- <h3>Sign In</h3> -->
      <div class="dlogin-cfrom">
      <div class="row">
      <?php 
if(!empty($errors)) {
	foreach($errors as $message) {
		echo "<span class='error'>".$message[0]."</span><br/>"; 
	}
}
?>
	  <form action="<?php echo $GLOBALS['ep_dynamic_url']; ?>login" method="post" class="col-sm-12">
        <div class="col-sm-12 ro-input-form">
          <div class="form-group">
           <input id="email2" name="email" type="text" class="validate ro-input-text ro-color-picton_blue ro-font form-control" placeholder="info@gmail.com" value="<?php if(isset($_POST['email'])) { echo $post['email']; } ?>" autocomplete="off">
            <label for="email2">
            <p class="ro-font">Email Address</p>
            </label>
          </div>
          
        </div>
        <div class="col-sm-12 ro-input-form">
          <div class="form-group">
            <input  id="pass" class="ro-input-text ro-color-picton_blue ro-font form-control validate" name="password" type="password" value="" autocomplete="off">
            <label for="pass">
            <p class="ro-font">Password</p>
            </label>
          </div>
          
        </div>
		<div class="col-sm-12 ro-input-form">
          <div class="form-group">
			  <input id="remember" name="remember" type="checkbox" onclick="validate()" value="0">
			  <label for="remember">Stay signed in</label>
          </div>
        </div>
		<div class="col-sm-12 ro-input-form">
          <div class="form-group">
			  <button class="mybtns45 ripple-effect" type="submit" style="margin-top: 20px;">Login
					<i class="material-icons right">send</i>
				</button>
          </div>
        </div>
		<div class="col-sm-12 ro-input-form">
          <div class="form-group">
			  <a href='<?php echo $GLOBALS['ep_dynamic_url']; ?>register'> Register </a> | <a href='<?php echo $GLOBALS['ep_dynamic_url']; ?>login/forgot'> Forgot Password </a>
          </div>
        </div>
         <input id="remember2" type="hidden" name="remember"/>
		 </form>
      </div>
      </div>
      <!-- <input type="submit" value="LOGIN" class="dmybtns45 ripple-effect" id=""> --> 
      
    </div>
  </div>
  <div class="dcopyright"> 2017 © Merrona Dashboard </div>
</div>

<!-- jquery min --> 
<script type="text/javascript" src="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/js/vendor/3.1.1.jquery.min.js"></script> 
<!-- bootstrap --> 
<script type="text/javascript" src="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/js/vendor/bootstrap.min.js"></script> 
<!-- bootstrap --> 
<script type="text/javascript" src="<?php echo $GLOBALS['ep_dynamic_url'] ?>view/js/custom.js"></script> 
<script type="text/javascript">
    function validate() {
        if (document.getElementById('remember').checked) {
        
            $('#remember2').attr('name', '');
        } else {
        
             $('#remember2').attr('name', 'remember');
        }
    }
</script>


</body>
</html>