<?php 
/**
 * Configuration page
 *
 * @author Azfar Ahmed
 * @version 1.0
 * @date November 02, 2015
 * @EasyPhp MVC Framework
 * @website www.tutbuzz.com
 */

//Basic Settings
$GLOBALS['ep_base_url'] = "http://localhost:81/merrona/authscript/"; 
$GLOBALS['ep_dynamic_url'] = "http://localhost:81/merrona/authscript/"; 
$GLOBALS['seourl'] = "true"; //Set true if your server supports .htaccess, else if your developing in local environment set it false. 
$GLOBALS['website_name'] = "Merrona App"; 
//Database Settings
$GLOBALS['ep_hostname'] = "localhost"; //Database Hostname
$GLOBALS['ep_username'] = "merrona_app"; //Database Username
$GLOBALS['ep_password'] = "root"; //Database Password
$GLOBALS['ep_database'] = ""; //Database Name

$GLOBALS['ABSPATH'] = dirname(__FILE__);

$GLOBALS['PROFILE_IMAGE_URL'] = $GLOBALS['ep_base_url']."images/profile_pic/";
$GLOBALS['POST_IMAGE_URL'] = $GLOBALS['ep_base_url']."images/posts/"; 
$GLOBALS['SIGNATURE_URL'] = $GLOBALS['ep_base_url']."images/signature/"; 

$GLOBALS['commission'] = 15;

/******Android Notification*******/
$GLOBALS['PUSH_API_ACCESS_KEY'] = 'AIzaSyBTp7yZzwHkxqQXGN52QoVGZeM5Ys0cqFo';

//Mailer Settings (In case if you integrate email library)
$GLOBALS['Admin_email'] = "info@merrona.com"; //Admin email
$GLOBALS['ep_smpt_server'] = ""; //SMPT Server Name Ex: smtp.gmail.com for Gmail
$GLOBALS['ep_smpt_port'] = ""; //SMPT Port
$GLOBALS['ep_smpt_username'] = ""; //SMPT Username
$GLOBALS['ep_smpt_password'] = ""; //SMPT Password
$GLOBALS['SMTPSecure'] = "tls"; //SMPT Secure
$GLOBALS['Mailer'] = "smtp"; //SMPT Secure


//Comman Views
$GLOBALS['ep_first_page'] = "pages"; //Default landing page 
$GLOBALS['ep_header'] = ""; //Header Template
$GLOBALS['ep_footer'] = ""; // Footer Template
//$GLOBALS['table_data'] = ""; 
