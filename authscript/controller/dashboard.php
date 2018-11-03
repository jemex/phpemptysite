<?php 
/**
 * Dashboard Controller
 *
 * @author Azfar Ahmed
 * @version 1.0
 * @date November 02, 2015
 * @EasyPhp MVC Framework
 * @website www.tutbuzz.com
 */
 
class dashboard extends authcheck{
	
	public function __construct()
	  {
		//Calling your Model class for page
			$this->model = new auth_model(); 
			$this->authcheck = new authcheck(); //check id user session exists
			$this->SessionId = $_SESSION["easyphp_sessionid"]; //Session Id
	  }
	  
	public function index() {
		$userdata = $this->model->userDetails();
		$data['userdata'] = $userdata;
		$data['ep_title'] = "Dashboard"; //setting title name
		$data['view_page'] = "users/dashboard.php"; //controller view page
		$data['ep_header'] = $GLOBALS['ep_header']; //header view (Also Ex: "header.php")
		$data['ep_footer'] = $GLOBALS['ep_footer']; //footer view 
		return $data;
	}
	
	public function settings() {
		$userdata = $this->model->userDetails();
		if (!empty($_POST)) {
			$userdata = $_POST; 
			//including validation
			$v = new Valitron\Validator($_POST); // Input array
			$v->rule('required', 'name');
			$v->rule('lengthMin', 'name', 3);
			$v->rule('required', 'email');
			$v->rule('required', 'location');
			$v->rule('required', 'gender');
			$v->rule('email', 'email');
			$email_exists = $this->checkDuplicateEmail($_POST['email']);
			if($v->validate() && !$email_exists) {
				$final_array = array();
				$keys = array_keys($_POST);
				foreach($keys as $key) {
					$value = strip_tags($_POST[$key]);
					$value = trim($value);
					if($key != "password" && $key != "username") {
						$final_array[$key] = $value;
					}
				}
				$data['result'] = $this->model->update($final_array, "users");
			} else {
				// Errors
				$errors = array();
				$errors_email = array();
				if($email_exists) {
					$errors_email = array(array("Email Already Exists"));
				}
				$errors = $v->errors();
				$data['errors'] = array_merge($errors,$errors_email);
			}
		}
		$data['userdata'] = $userdata;
		$data['ep_title'] = "Settings"; //setting title name
		$data['view_page'] = "users/settings.php"; //controller view page
		$data['ep_header'] = $GLOBALS['ep_header']; //header view (Also Ex: "header.php")
		$data['ep_footer'] = $GLOBALS['ep_footer']; //footer view 
		return $data;
	}
	
	public function password() {
		$userdata = $this->model->userDetails();
		if (!empty($_POST)) {
			//including validation
			$v = new Valitron\Validator($_POST); // Input array
			$v->rule('required', 'password');
			$v->rule('lengthMin', 'password', 6);
			$v->rule('regex', 'password', '/[^a-z_\-0-9]/i')->message('Password should be alpha numeric and should contain atleat 1 special character');
			$v->rule('equals', 'password', 'passwordverify')->message('Please re-enter the password again');
			if($v->validate()) {
				$final_array = array();
				$keys = array_keys($_POST);
				foreach($keys as $key) {
					$value = strip_tags($_POST[$key]);
					$value = trim($value);
					if($key != "passwordverify") {
						$final_array[$key] = md5($value);
					}
				}
				$data['result'] = $this->model->update($final_array, "users");
			} else {
				// Errors
				$data['errors'] = $v->errors();
			}
		}
		$data['userdata'] = $userdata;
		$data['ep_title'] = "Change Password"; //setting title name
		$data['view_page'] = "users/password.php"; //controller view page
		$data['ep_header'] = $GLOBALS['ep_header']; //header view (Also Ex: "header.php")
		$data['ep_footer'] = $GLOBALS['ep_footer']; //footer view 
		return $data;
	}
	
	public function checkDuplicateEmail($email) {
		$userdata = $this->model->userDetails();
		$user_id = $userdata['id'];
		$ownval = $this->model->checkifexists("WHERE email='$email' && id='$user_id'");
		if(!$ownval) {
			$result = $this->model->checkifexists("WHERE email='$email'");
		}
		else {
			$result = false;
		}
		return $result;
	}
	
	public function logout() {
		$this->model->deleteSession();
		$data['ep_title'] = "Logout";
		$data['view_page'] = "false";
		$data['ep_header'] = "false"; 
		$data['ep_footer'] = "false";
	}
	
}