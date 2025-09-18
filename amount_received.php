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
$page_title = 'Amount Received';
$module_name = 'amount_received';

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


/*
 * Get values from js
 */
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];

$pval = isset($_POST['pval']) ? $_POST['pval'] : $_GET['pval'];
$mpval = isset($_POST['mpval']) ? $_POST['mpval'] : $_GET['mpval'];

$uval['recv_id'] = (isset($_GET['recv_id'])) ? $_GET['recv_id'] : $_POST['recv_id'];
$uval['allot_id'] = (isset($_GET['allot_id'])) ? $_GET['allot_id'] : $_POST['allot_id'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$uval['amount'] = (isset($_GET['amount'])) ? $_GET['amount'] : $_POST['amount'];
$uval['tran_date'] = (isset($_GET['tran_date'])) ? $_GET['tran_date'] : $_POST['tran_date'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];


//constant message for cash flow transaction
$cash_purpose= RAISE_REQUEST_AMOUNT_RECEIVED;
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

$app->access_table = AMOUNT_TRANSFER;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);


switch($type)
{
case 'formvalidation':
            /*
             * Form validation here . notify error, if exists.
             */

            $missing_labels = Validator::validate_json('AmountReceived');


            if(!isset($missing_labels['received_date'])){
            $rec_date = date('Y-m-d',strtotime($pval["received_date"]));
            $cur_date = date('Y-m-d');
            
            
            $trans_date = Amountreceived::getTransferDate($pval['id']);
            
            
            //$missing_labels['received_date'] = $trans_date.' - Received date should not less than the transfer date - '.$trans_date1;

            if($trans_date > $rec_date){

                $missing_labels['received_date'] = 'Received date should not less than the transfer date';

            }
            
            if($rec_date > $cur_date){

                $missing_labels['received_date'] = 'Received date should not greater than the current date ';

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
                \"content\" : \"".AMOUNT_RECEIVED_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";

            exit;
            break;

    case 'save':

            $pval['received_date']=date('Y-m-d H:i:s',strtotime($pval['received_date'].date(" H:i:s")));

           //Message details
            //$data = Amountreceived ::Msg_details($pval['request_branch_id']);
            
            $data1 = Allocationofbranch ::Msg_details($mpval['request_branch_id']);
            $data2 = Allocationofbranch ::Msg_details($mpval['transfer_branch_id']);
            
            if(is_array($data1) && is_array($data2))
                $data = array_merge($data1, $data2);
            else
                $data = (is_array($data1)) ? $data1 : $data2;
            
            
            
            for($i=0;$i<count($data);$i++)
            {
            $msg_det['branch_id']=$_SESSION['session_user_branch_id'];
            $msg_det['sender']=$_SESSION['session_userid'];
            $msg_det['from']=$_SESSION['session_username'];
            $msg_det['num_recipients']=count($data);
            //$msg_det['subject']="Amount Recived";
            //$msg_det['message']="Amount Recived Sucessfully "."\n"."Branch Name:".$_SESSION['session_user_branch_name'];
            
            $msg_det['subject']="Amount Received by ".Branch::getBranchName($mpval['request_branch_id'])." from ".Branch::getBranchName($mpval['transfer_branch_id']);
            $msg_det['message'] ="Dear Admin,
                                    \n\n We Received your transfered amount.
                                    \n\n Request Details as Follows:
                                    \n Requested Branch Name:".Branch::getBranchName($mpval['request_branch_id'])."
                                    \n Requested Amount: Rs. ".convertToCurrency(Raiserequest::getRequestAmount($mpval['request_id']),false)."

                                    \n\n Allocation Details as Follows:
                                    \n Branch Name:".Branch::getBranchName($mpval['transfer_branch_id'])."
                                    \n Allocated Amount: Rs. ".convertToCurrency($mpval['transfer_amount'],false)."
                                    
                                    \n\n Transafer Details as Follows:
                                    \n Transfer User Name:".User::getUserName($mpval['transfer_user_id'])."
                                    \n Transafer Amount: Rs. ".convertToCurrency($mpval['transfer_amount'],false)."
                                    \n Transafer Date: ".date("d-M-Y",strtotime($mpval['transfer_date']))."
                                    
                                    
                                    \n\n Received Details as Follows:
                                    \n Received User Name:".User::getUserName($mpval['receiver_user_id'])."                                    
                                    \n Received Amount: Rs. ".convertToCurrency($mpval['transfer_amount'],false)."
                                    \n Received Date: ".date("d-M-Y",strtotime($pval['received_date']))."
                                    
                                    \n\n Yours Faithfully
                                    \n".$_SESSION['session_username']."
                                    \n".$_SESSION['session_useremail'];
            
            
            $msg_det['sent_date'] = date('Y-m-d H:i:s');
            $app->access_table =MESSAGE_DETAILS;
            $app->add($msg_det);

            //message list
            $msgid=$app->last_inserted_id();
          //$data = Amountreceived ::Msg_list($pval['request_branch_id']);

            $msg_list['message_id']=$msgid;
            $msg_list['recipient_id']=$data[$i]['user_id'];
            $msg_list['recipient_name']=$data[$i]['recipient_name'];
            $msg_list['email']=$data[$i]['email'];
            $app->access_table = MESSAGE_LIST;
            $app->add($msg_list);
            }
            
            
            //cash flow
            $pval['cash_in_id'] = CashFlowTransaction::insertCashFlow(
                                                $mpval['request_branch_id'],
                                                $cashin,
                                                $mpval['transfer_amount'],$cash_purpose, $pval['received_date']);


            //Amount recived
            $pval['status'] = 3;
            $pval['received_by'] = $_SESSION['session_userid'];
            $pval['modified_datetime'] = date('Y-m-d H:i:s');
            $pval['modified_by'] =$_SESSION['session_userid'];
            $pval['received_by'] =$_SESSION['session_userid'];

            $app->access_table = AMOUNT_TRANSFER;
            $app->save($pval,array("id =" =>$pval['id']));






            $_SESSION['success_msg'] = AMOUNT_RECEIVED;
            //header("Location: amount_received.php");

              echo "
               {
                \"page\" : \"".$page."\",
                \"status\" : \"".$status."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"\",
                \"isAlert\" : \"".true."\" ,
                \"args\" : \"\"
               }";


            exit;
            break;


   case 'received':
        $data = Amountreceived::Detail($uval['id']);
        include ADMIN_LAYOUT_PATH . 'forms/amount_received.html';
   break;

   case 'refresh':
   case 'paging':
   case 'back':

         $data = Amountreceived::Listview($search_key, $search_cus_id);
         $count = Amountreceived::ListviewCount($search_key, $search_cus_id);
         include ADMIN_LAYOUT_PATH . 'amount_received.html';
         exit;
         break;

    default:
        /*
         * Make the pages with header and footer
         */
        $data = Amountreceived::Listview($search_key, $search_cus_id);
        $count = Amountreceived::ListviewCount($search_key, $search_cus_id);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'amount_received.html';
        #include ADMIN_LAYOUT_PATH . 'forms/amount_received.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>