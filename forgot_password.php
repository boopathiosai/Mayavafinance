<?php
 //**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE                   
 // Module           :  Forgot Password          
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Vinothkuamr V
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  05-Feb-2010
 //**************************************************************************
 // Description      :  This is the Forgot Password php file  for Forgot Password module,used to send password through the Mails.
//**************************************************************************

session_start();

define ('ROOT_PATH', '');
$page_title = 'Forgot Password';
$module_name = 'forgot_password';

include ROOT_PATH . 'includes/config.php';
 
$pval = $_POST['pval'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];

class ForgotPassword {
	public $check_required = array('email_address');
	public static $check_required_text = 'Required Fields Missing!';
}

if($type == 'forgot_password') {

	$app->access_table = USERS;

    /*
     * Checking for required fields
     */
   $missing_vals = Validator::validate_data('ForgotPassword');

   if (!empty( $missing_vals )) {
		echo '<div style="color:red;"><b>Invalid Email Address!</b></div>';
		die();
	}

	$params = array();
	$params['conditions']['email LIKE '] = $pval['email_address'];
	$params['conditions']['status = '] = 1;
	$data = $app->find(array('*'), $params);

	if(count($data) != 1) {
		echo '<div style="color:red;"><b>Please check the email address!</b></div>';
		die();
	}	

	$admin_password = base64_decode($data[0]['password']);
	
	$content = "Dear ".$data[0][name]."<br><br>";

	$content .= "<em>As on your request, we send your login details:</em><br>
			----------------------------------------------------------------<br>			
			<strong>User Name  : </strong> ".$data[0][email]."<br>			
			<strong>Password   : </strong> ".$admin_password."<br>
			----------------------------------------------------------------<br><br>";	 
   $content .= "Best Regards,"."<br>"."-- The Mayava Finance Administrator"."<br>";
   $content .= "<a href='".$html_root_path."'>".$html_root_path."</a>";
   
	                $mail = new PHPMailer();
	 				$mail->SetFrom(ADMIN_EMAIL, ADMIN_NAME);
	 				$mail->AddAddress($data[0][email], $data[0][name]);
	 				$mail->Subject    = "Mayava Finance | Your Login Info";
	 				$mail->MsgHTML($content);
				    $mail->Send();

	 $success = "1";
	 echo '<div style="color:green;"><b>Mail sent to your Email Address!</b></div>';
	 die();
}

include ADMIN_LAYOUT_PATH . 'forgot_password.html';

?>