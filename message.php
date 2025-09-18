<?php
session_start();
define ('ROOT_PATH', '');
$page_title = 'Message';
$module_name = 'message';
include ROOT_PATH . 'includes/config.php';
$menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);
/**
 * Redirect to index page if not logged in
 **/
$mod = (isset($_GET['mod'])) ? $_GET['mod'] : $_POST['mod'];
if($app->is_logged_in() !== true) {

    if($mod == 'Ajax') {
            echo "session_timed_out";
            exit;
    }
    else{
            header('Location: index.php');
            exit;
    }   
} 

/**
 * Get values from js
 **/
$pval = $_POST['pval'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];
$disp = ($disp!='') ? $disp : 'content_div';



$page_no = 1;
/* * Class Instances */
//Get the Mode From Related Page [ADD / EDIT / DELETE]
//======================================================
$mode = (isset($_GET['mode'])) ? $_GET['mode'] : $_POST['mode'];

//Get the ID
//==========
$id = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];

//Get Login Member Id
//===================
   $user_id = $_SESSION[$app->session_userid1];




//Send Operations Here
//=====================
if(($mod == 'add' || $mod == 'update') && $type != "formvalidation")
{
   
  
        //To Get the values from Form
        //===========================
        
         $from=$_SESSION['session_username'];
         $to=$_POST['to'];
		 $tot_cnt=count($to);
		 
         $recipients_to=implode(",", $to);
        
        //To Mass Mail details
        //=====================
        $pval['from']=$from;
        $pval['sender']=$user_id;
        $pval['num_recipients']=$tot_cnt;

        //To Add Send Mail Date
        //=====================
		$timezone = "Asia/Calcutta";
		if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);
        $pval['sent_date']=Date("Y-m-d H:i:s");
    
        //Access the Table
        //================
         $app->access_table = MESSAGE_DETAILS;
                
         if($mod=='update') {
            
            //condition
            //==========
            $params['id = '] = $id;
            //unset($pval['to']);
            $app->save($pval, $params);
            //$pval['to']=$to;
            // To set Last Insert ID
            //=======================
            $last_insert_id=$id;
         }
         else {
            //unset($pval['to']);
            $app->add($pval);
            
            // To Get Last Insert ID
            //======================
            $last_insert_id=$app->last_inserted_id();
            
         }
         //condition
         //========
          $params['id = '] = $last_insert_id;

         //To Insert  Mail List Table
        //==============================
        
        $mailmessage_id=array();
        
        for($i=0;$i<$tot_cnt;$i++)
        {
                
                        $app->access_table = USERS;     
                        $data = $app->find(array('*'), array('conditions' => array('id = ' => $to[$i])));
                        $member_details = $data[0];
                        $recipient_id=$member_details['id'];
                        $recipient_name=$member_details['name'];
                        $mailid=$member_details['email'];
                        
                        //Access the Table
                        //================
                        //if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$mailid)){
                        
                        $app->access_table = MESSAGE_LIST;          
                        $uval['message_id']=$last_insert_id;
                        $uval['recipient_id']=$recipient_id;
                        $uval['recipient_name']=$recipient_name;
                        $uval['email']=$mailid;
                        $app->add($uval);
                                                        
                        // To Get Last Insert ID
                        //========================
                        $last_insert_mailid=$app->last_inserted_id();
                        array_push($mailmessage_id,$last_insert_mailid);

        }
        //Access the Table
        //================
         $app->access_table = MESSAGE_DETAILS;
        //Select Query for to get Content form DB
        //========================================
        $data = $app->find(array('*'), array('conditions' => array('id = ' => $last_insert_id)));
        $message_details = $data[0];
        $message_content=stripcslashes($message_details['message']);
        
        //To Set Count Variable
        //=====================
        $set_count_val = 0;
    
        for($i=0;$i<count($mailmessage_id);$i++)
        {
            //To Get the send mail id
            //=========================
            $send_id=$mailmessage_id[$i];

            //Access the Table
             //================
             $app->access_table = USERS;
            
             //To get the send mail id form DB
            //================================
            $from_data = $app->find(array('*'), array('conditions' => array('id = ' => $user_id)));
            //print_r($from_data);
            
            $from_address = $from_data[0];
            
            $from_address_name=$from_address['name'];
            $from_address=$from_address['email'];
                                                                
            //Access the Table
            //================
            $app->access_table = MESSAGE_LIST;
              
            //To get the send mail id form DB
            //=================================
            $data = $app->find(array('*'), array('conditions' => array('id = ' => $send_id)));
                        
            $send_mail_details = $data[0];      

            $senter_name=$send_mail_details['recipient_name'];
            $send_mailid=$send_mail_details['email'];
            $system_mail_msg = Mayavafinance;

            
        
           if(preg_match("/^([a-zA-Z0-9])+([a-zA-Z0-9\._-])*@([a-zA-Z0-9_-])+([a-zA-Z0-9\._-]+)+$/",$send_mailid)){
                        
                
                $message = "<table width='100%' border='0' cellspacing='1' cellpadding='8'>
                    <tr>
                      <td style='font-family:verdana, Arial, Helvetica, sans-serif;font-size:12px;font-weight:bold;color:#000000;'>Dear $senter_name,
                    </td>   
                    </tr>
                    <tr>
                      <td style='font-family:verdana, Arial, Helvetica, sans-serif;font-size:12px;'>$message_content</td>
                    </tr>
                    <tr>
                        <td style='font-family:verdana, Arial, Helvetica, sans-serif;font-size:12px;'>Thank you!</td>
                    </tr>
                    <tr>
                        <td style='font-family:verdana, Arial, Helvetica, sans-serif;font-size:12px;'><b>Best Regards,</b></td>
                    </tr>
                    <tr>
                        <td>
                            <span style='font-family:verdana, Arial, Helvetica,sans-serif;font-size:14px;color:#A40000;font-weight:bold;'>
                                $system_mail_msg
                            </span>
                        </td>
                    </tr>
                    </table>";

                  $mail = new PHPMailer();
                  $mail->IsHTML(true);

                  $email_from_name=$from_address_name;
                  $fromname=$email_from_name;
                  $email_from_address=$from_address;
                
                  $subject=$subject_name;
                  $email_to=$send_mailid;
                                                 
                                    
                  $mail->From     = $email_from_address;
                  $mail->FromName = $fromname;
                  $mail->Subject  = $subject;
                  $mail->Body     = $message;

                  $mail->AddAddress($email_to);

                              
                /*echo '<pre>';
                print_r($mail);
                exit;*/

                                                          
                if($mail->Send()) {

                  
                 $message='';   
                              
                 $set_count_val++;
                 if($set_count_val=='50') { sleep(60); flush(); $set_count_val = 0; }
            }
          }
        }       
        
        //Show Successfully send Mail message
        //====================================
         $_SESSION['success_msg']= MESSAGE_SEND_SUCESSFULLY;
            
        //Page Should be Redirect
        //=======================
         //echo "<meta http-equiv='refresh' content='0;url=sent_items.php'>";
        ob_end_clean();
        echo "
        { 
            \"page\" : \"message_send\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \"".$_SESSION['success_msg']."\", 			
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
        }";

        exit;
         
        
    }
    



switch($type)
{

    case 'formvalidation':

            /*
             * Form validation here . notify error, if exists.
             */
             
           // $to = ( isset( $pval['to'] ) ) ? $pval['to'] : "";
           // print_r($to);

			 $_POST['pval']['to'] = ( isset( $_POST['to'] ) ) ? implode(",",$_POST['to']) : "";			
			
           /* $missing_vals = Validator::validate_data('Message');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Message::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0].'~~0';
            exit;
            }*/
            
            $missing_labels = Validator::validate_json('Message');
            
            
            if($_POST['pval']['to'] == "" || $_POST['pval']['to'] == "undefined"){
                //echo 'Please select atleast one email Id from the list~~#to~~0';
                //exit;
                $missing_labels['to_label'] = 'Please select atleast one email Id from the list';
            }

            
            //Common Code for all
            if (!empty( $missing_labels )) { 
            
                $fields = implode(",#",array_keys($missing_labels));
                $labels = implode(",",$missing_labels);
                $success = false;
                
            }else{
                $fields ='';
                $labels ='';
                $success = true;
            }
            
            $isMsgDisplay = true;
            
            //p_arr($missing_labels);
            ob_end_clean();
              echo "
               { 
                \"page\" : \"".$page."\", 
                \"success\" : \"".$success."\", 
                \"disp\" : \"".$disp."\", 
                \"content\" : \"".MESSAGE_VALIDATE_ERROR."\", 			
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;
            
            
            //echo '~~~~1';            exit;
            break;

    case 'getemail': 

         $branchemail = $_GET['branch_id'];
         if($branchemail!='')
            {			
					      $emailid = $_SESSION['session_useremail'];
                $select_data = "select id,name,email,branch_id  from ".USERS." where branch_id='$branchemail' AND email!='$emailid'";
                $data=$app->getrows($select_data);
                //print_r($data);
            }
         if(count($data)>0)
            {
                foreach ($data as $key => $row ) {

                $selected = ($row[branch_id]==$branchemail) ? 'selected=" selected "' : '';
                
                $output.='<option value="'.$row[id].'">'.$row[email].'</option>';
                }
            }
         echo $output;
         exit;
         break;

	case 'message_alert' :

			$user_id = $_SESSION[$app->session_userid1];
 
			$msg_query = "SELECT COUNT(MD.status) AS statuscount,MD.id,
                ML.recipient_id,ML.message_id
                   FROM ".MESSAGE_DETAILS." AS MD
                       INNER JOIN ".MESSAGE_LIST." AS ML ON(MD.id = ML.message_id)
                         WHERE ML.recipient_id = $user_id and MD.status = '0' "; 
			$msg_data = $app->getrows($msg_query);
			$countdata = $msg_data[0][statuscount];

			if($countdata > 0) {
				echo $_SESSION['msg_inbox'] = 'You have new message(s)';
				unset($_SESSION['msg_inbox']);
			}else{
				echo 1;
			}
			exit;
		break;

    case 'change_status':
        /*
         * Change the stauts in database
         */
          $app->save($change_status_array, array('id = ' => $uval['id']));
          $notice = STATUS_SUCCESS;
          echo $change_status_array['status'].'~~'.$notice;
          exit;
          break;
   

    default:

        include ADMIN_LAYOUT_PATH . 'common/header.html';       
        include ADMIN_LAYOUT_PATH . 'forms/message.html';       
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>