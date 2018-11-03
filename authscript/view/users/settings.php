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
		echo "<span class='error' style='color: red'>".$message[0]."</span><br/>"; 
	}
}
if(!empty($result)) {
	echo "<span class='success'> Succesfully Updated! </span> <a href='".$GLOBALS['ep_dynamic_url']."dashboard'> Go Back </a>";
}
?>
<div> <br/> </div>
<div class="user-form" style="float: left; width: 100%;padding: 0 25px;"> 
	<div class="row ">
		<form action="<?php echo $GLOBALS['ep_dynamic_url']; ?>dashboard/settings" method="post" class="col s12">
		  <div class="row">
			<div class="input-field col s12">
			  <input id="name" name="name" type="text" class="validate" value="<?php if(!empty($userdata['name'])) { echo $userdata['name']; } ?>">
			  <label for="name">Name</label>
			</div>	  			
		  <div class="input-field col s12">
			  <input id="email" name="email" type="text" class="validate" value="<?php if(!empty($userdata['email'])) { echo $userdata['email']; } ?>">
			  <label for="email">Email</label>
			</div>
			<div class="input-field col s12">
			  <input id="location" name="location" type="text" class="validate" value="<?php if(!empty($userdata['location'])) { echo $userdata['location']; } ?>">
			  <label for="location">Location</label>
			</div>
			<div class="input-field col s12">
				<select name="gender" class="browser-default" >
				  <option value="" disabled selected>Gender</option>
				  <option value="Male" <?php if($userdata['gender'] == "Male") { echo "selected"; } ?>>Male</option>
				  <option value="Female" <?php if($userdata['gender'] == "Female") { echo "selected"; } ?>>Female</option>
				</select>
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