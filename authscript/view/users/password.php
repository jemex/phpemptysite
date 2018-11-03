<style type="text/css">
.user-form input, .user-form label{
	border-color: #01579b !important;;
	color: #01579b !important;
}
</style>

<h1 style="float: left; width: 100%;padding: 0 25px;"> Account </h1>

<?php 
if(!empty($errors)) {
	foreach($errors as $message) {
		echo "<span class='error'>".$message[0]."</span><br/>"; 
	}
}
if(!empty($result)) {
	echo "<span class='success'> Succesfully Updated! </span> <a href='".$GLOBALS['ep_dynamic_url']."users/userlist'> Go Back </a>";
}
?>

<div class="user-form" style="float: left; width: 100%;padding: 0 25px;"> 
	<div class="row ">
		<form action="<?php echo $GLOBALS['ep_dynamic_url']; ?>dashboard/password" method="post" class="col s12">
		  <div class="row">
			<div class="input-field col s12">
			  <input  id="username" name="password" type="password" class="validate" value="">
			  <label for="username">Password</label>
			</div>
			<div class="input-field col s12">
			  <input  id="username" name="passwordverify" type="password" class="validate" value="">
			  <label for="username">Password Again</label>
			</div>
			<div class="input-field col s12">
				<button class="btn waves-effect waves-light light-blue darken-4" type="submit" style="margin-top: 20px;">Submit
					<i class="material-icons right">send</i>
				</button>
			</div>
		  </div>
		</form>
</div>

<a href='<?php echo $GLOBALS['ep_dynamic_url']; ?>dashboard'> Go Back </a>