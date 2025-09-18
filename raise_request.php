<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Customer
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Customer php file  for Customer module,used to process the view,add,edit and delete in Customer module.
//**************************************************************************

session_start();

define ('ROOT_PATH', '');
$page_title = 'Raise Request for Money shortage';
$module_name = 'raise_request';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = TRANSACTIONS_CAT_ID;

/*
 * Redirect to index page if not logged in
 */
 if($app->is_logged_in() !== true) {

    if($mod == 'Ajax') {
        echo "session_timed_out";
        exit;
    } else {
        header('Location: index.php');
        exit;
    }
}

/*
 * Redirect to index page if user is not allowed to access the page
 */
if(!is_accessible($menu))
{
        //echo"notaccessible";
        header('Location: index.php');
        exit;
}

//p_arr($_SESSION);exit;

/*
 * Get values from js
 */
$pval = $_POST['pval'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];
$disp = ($disp!='') ? $disp : 'content_div';

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

$cashin= CASH_IN;
$cashout= CASH_OUT;

/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];

$search_args = "";
if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
}
if($search_cus_id != ""){
    $search_args.="search_cus_id=".$search_cus_id."&";
}


if($page_no <= 0) {
    $page_no = 1;
}
if($rg <= 0) {
    $rg = 1;
}
$page_no = (int)$page_no;

/*
 * Class Instances
 */

$app->access_table = MONEY_SHORTAGE;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);
//echo $type;
switch($type) {

   case 'formvalidation':

            /*
             * Form validation here . notify error, if exists.
             */

            $missing_labels = Validator::validate_json('Raiserequest');


            $request_date = date('Y-m-d',strtotime($pval["request_on"]));
            $cur_date = date('Y-m-d');

            if($request_date > $cur_date){

                $missing_labels['request_on'] = 'Request date should not greater than the current date';

            }

            if($uval['id']!=''){

            $status = Raiserequest::getRequestStatus($uval['id']);

            if($status > 0) {

            $alloc_amt = floatval(RaiseRequest::getRequestAmount($uval['id']));
            $req_amt = floatval($pval["amount_need"]);

            if($req_amt > $alloc_amt){

            $missing_labels['amount_need'] = 'This requested had been allocated for the Rs.'.unitDisplay($alloc_amt,false).'. You can not edit this record below the allocated amount';

            }

            }


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

              echo "
               {
                \"page\" : \"".$page."\",
                \"success\" : \"".$success."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".RAISE_REQUEST_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";

            exit;
            break;

   case 'add':
   case 'update':

        //$pval['request_on'] = date("Y-m-d H:i:s", strtotime($pval['request_on'].date(" H:i:s")));
        $pval['request_on'] = date("Y-m-d H:i:s",strtotime($pval['request_on'].date(" H:i:s")));
        //$pval['request_on'] = date("Y-m-d H:i:s", $pval['request_on']);
//echo  $pval['request_on']; exit;
        if($type == 'add')
        {



            //Message details store
            $data = Raiserequest::Msg_details();
            //print_r($_SESSION);
            for($i=0;$i<count($data);$i++)
            {
            $msg_det['branch_id']=$_SESSION['session_user_branch_id'];
            $msg_det['sender']=$_SESSION['session_userid'];
            $msg_det['from']=$_SESSION['session_user_branch_name'];
            $msg_det['num_recipients']=$data[$i]['count'];
            $msg_det['subject']="Money Shortage Request";
            $msg_det['message']="Dear Admin,
                                    \n\n \tRaise Request for Money Shortage.
                                    \n\n \tRequested Branch:".Branch::getBranchName($pval['branch_id'])."
                                    \n \tShortage Amount:".$pval['amount_need']."
                                    \n\n \tYours Faithfully
                                    \n".$_SESSION['session_username']."
                                    \n".$_SESSION['session_useremail'];
            $msg_det['sent_date'] = date('Y-m-d H:i:s');
            $app->access_table =MESSAGE_DETAILS;
            $app->add($msg_det);
            $msgid=$app->last_inserted_id();

            //Message list details store
            //$data = Raiserequest::Msg_list();
            $msg_list['message_id']=$msgid;

            $msg_list['recipient_id']=$data[$i]['user_id'];

            $msg_list['recipient_name']=$data[$i]['recipient_name'];

            $msg_list['email']=$data[$i]['email'];

            $app->access_table = MESSAGE_LIST;
            $app->add($msg_list);
            }

            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $pval['status'] = 1;
            //Money Shortage details

            //Mail functions
            //get the superadmin mailid
            /*
            $data = Raiserequest::Getemail();
            $content = "Money Request <br><br>";
            $content .= "<em>Amount Shoratage of ".$_SESSION['session_user_branch_name']." Branch:</em><br>
            ----------------------------------------------------------------<br>
            <strong>Branch Name             : </strong>".$_SESSION['session_user_branch_name']." <br>
            <strong>Shortage Amount         : </strong> ".$pval['amount_need']."<br>
            ----------------------------------------------------------------<br><br>";
            $content .= "Best Regards,"."<br>"."-- The Mayava Finance Administrator"."<br>";
            $content .= "<a href='".$html_root_path."'>".$html_root_path."</a>";
            $mail = new PHPMailer();
            $mail->SetFrom(ADMIN_EMAIL, ADMIN_NAME);
            $mail->AddAddress($_SESSION['session_useremail'],$data['0']['email']);
            $mail->Subject    = "Mayava Finance | Money Request  Info";
            $mail->MsgHTML($content);
            $mail->Send();
            */
            $app->access_table = MONEY_SHORTAGE;
            $app->add($pval);



            $uval['id'] = $app->last_inserted_id();
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $app->save($pval, array('id = ' => $uval['id'])); */
            $success_msg = Request_money_shortage_saved;


        } else {

            //Message details store
            $data = Raiserequest::Msg_details();
            //print_r($_SESSION);
            for($i=0;$i<count($data);$i++)
            {
            $msg_det['branch_id']=$_SESSION['session_user_branch_id'];
            $msg_det['sender']=$_SESSION['session_userid'];
            $msg_det['from']=$_SESSION['session_username'];
            $msg_det['num_recipients']=$data[$i]['count'];
            $msg_det['subject']="Money Shortage Request";
            $msg_det['message']="Dear Admin,
                                    \n\n Raise Request for Money Shortage.
                                    \n\n Requested Branch:".Branch::getBranchName($pval['branch_id'])."
                                    \n Shortage Old Amount:".Raiserequest::getRequestAmount($uval['id'])."
                                    \nShortage New Amount:".$pval['amount_need']."
                                    \n\n Yours Faithfully
                                    \n".$_SESSION['session_username']."
                                    \n".$_SESSION['session_useremail'];
            $msg_det['sent_date'] = date('Y-m-d H:i:s');
            $app->access_table =MESSAGE_DETAILS;
            $app->add($msg_det);
            $msgid=$app->last_inserted_id();

            //Message list details store
            //$data = Raiserequest::Msg_list();
            $msg_list['message_id']=$msgid;

            $msg_list['recipient_id']=$data[$i]['user_id'];

            $msg_list['recipient_name']=$data[$i]['recipient_name'];

            $msg_list['email']=$data[$i]['email'];

            $app->access_table = MESSAGE_LIST;
            $app->add($msg_list);
            }

            //$pval['request_on'] = date("Y-m-d");
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $app->access_table = MONEY_SHORTAGE;
            $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = Request_money_shortage_update;

        }
        $_SESSION['success_msg'] = $success_msg;

         echo "
        {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"".$success_msg."\",
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
        }";

        /*header("Location: raise_request.php");*/
        exit;
        break;

   case 'create':
   case 'edit':
        /*
         * Get the Customer details, if type is edit
         */
        $customer_data = Raiserequest::Detail($uval['id']);
        $customer_data = $customer_data[0];
        #include ADMIN_LAYOUT_PATH . 'forms/customer.html';
        include ADMIN_LAYOUT_PATH . 'forms/raise_request.html';
        break;

   case 'refresh':
   case 'paging':
   case 'back':

         $data = Raiserequest::Listview($search_key, $search_cus_id);
         $count = Raiserequest::ListviewCount($search_key, $search_cus_id);
         include ADMIN_LAYOUT_PATH . 'raise_request.html';
         exit;
         break;

   case 'delete':
        /*
         * Delete from database
         */
         if($uval['id']!="")
        {

            $name = Raiserequest::getBranchName($uval['id']);

            $status = Raiserequest::Delete($uval['id']);

            if($status["success"])
            {
                $message[] = "BranchId=".$name." ".RAISE_REQUESTBRANCH_DELETED;
            }
            else
            {
                if(is_array($status['error'])){

                    foreach($status['error'] as $err){

                        $message[] .= "BranchId=".$name." ".RAISE_REQUESTBRANCH_NOT_DELETED." - ".$err;

                    }

                }else{

                    $message[] .= "BranchId=".$name." ".RAISE_REQUESTBRANCH_NOT_DELETED." - ".$status['error'];

                }
            }
        }

       if(is_array($_POST["list"]))
            {
                $i=0;

                foreach($_POST["list"] as $key=>$val)
                {

                    $name = Raiserequest::getBranchName($val);
                    $status = Raiserequest::Delete($val);

                    if($status["success"])
                    {
                        $message[] = "BranchId=".$name." ".RAISE_REQUESTBRANCH_DELETED;
                    }
                    else
                    {

                        if(is_array($status['error'])){

                            foreach($status['error'] as $err){

                                $message[] .= $name." ".RAISE_REQUESTBRANCH_NOT_DELETED." - ".$err;

                            }

                        }else{

                            $message[] .= $name." ".RAISE_REQUESTBRANCH_NOT_DELETED." - ".$status['error'];

                        }
                    }

                }

            }

         $alert_message = (is_array($message)) ? implode("<br> * ",$message) : $message;

         echo "
           {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \" *  ".$alert_message."\",
            \"isAlert\" : \"".true."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\",\"isAlert\":\"".true."\"}
            }";
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
        /*
         * Make the pages with header and footer
         */
        $data = Raiserequest::Listview($search_key, $search_cus_id);
        $count = Raiserequest::ListviewCount($search_key, $search_cus_id);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'raise_request.html';
        #include ADMIN_LAYOUT_PATH . 'forms/raise_request.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>