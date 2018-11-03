<div class="user-form" style="padding:25px;"> 
    <?php 
    if(isset($success) && $success!=''){
        echo "<p>".$success."</p>";
    }
    
    if(isset($error) && $error!=''){
        echo "<p style='color:red;'>".$error."</p>";
    }
    ?>
    <div class="row ">
        <?php //echo md5("77606414304f766313140280874a686b"); //print_r($_POST) ?>
        <form action="<?php echo $GLOBALS['ep_dynamic_url']; ?>adminmain/changepassword" method="post" id="formCheckPassword">
                <div class="col-sm-12">
                <div class="form-group">
                    <label class="control-label col-sm-3" for="password">Old Password</label>
                    <input  name="old_password" type="password" class="validate" value="<?php if(isset($_POST['old_password'])) echo $_POST['old_password']; ?>" required="">
                </div>
                <div class="form-group">
                    <label class="control-label col-sm-3" for="password">New Password</label>
                    <input name="password1" id="password1" type="password" class="validate" value="<?php if(isset($_POST['password1'])) echo $_POST['password1']; ?>" required="">
                </div>
                <div class="form-group">
                     <label class="control-label col-sm-3" for="password">Password Again</label>
                     <input name="password2" id="password2" type="password" class="validate" value="<?php if(isset($_POST['password2'])) echo $_POST['password2']; ?>" required="">
                </div>
                <div class="form-group">
                    <input type="submit" name="updatePassword" value="save" class="btn waves-effect waves-light light-blue darken-4" onClick="validatePassword();"/>
                </div>
                    </div>
        </form>
        
    </div>
</div>