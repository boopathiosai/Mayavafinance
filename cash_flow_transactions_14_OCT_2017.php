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
//ini_set('memory_limit','1024M');
define ('ROOT_PATH', '');
$page_title = 'Cash Flow Transactions';
$module_name = 'cash_flow_transactions';

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
$search_trans_key = (isset($_GET['search_trans_key'])) ? $_GET['search_trans_key'] : $_POST['search_trans_key'];
$search_trans_type = (isset($_GET['search_trans_type'])) ? $_GET['search_trans_type'] : $_POST['search_trans_type'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];

$search_args = "";
if($search_trans_key != ""){
    $search_args.="search_trans_key=".$search_trans_key."&";
}
if($search_trans_type != ""){
    $search_args.="search_trans_type=".$search_trans_type."&";
}
if($search_branch_id != ""){
    $search_args.="search_branch_id=".$search_branch_id."&";
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

$app->access_table = CASH_FLOW_TRANSACTIONS;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);


switch($type) {

   case 'formvalidation':

            /*
             * Form validation here . notify error, if exists.
             */
            $missing_labels = Validator::validate_json('Cashflowtransaction');
            $date_transaction = date("Y-m-d",strtotime($pval['date_transaction']));

            if($date_transaction > date("Y-m-d")){
                $missing_labels['date_transaction']='Invalid transaction date. Transaction date should not exceed current date';
                //echo 'Invalid transaction date. Transaction date should not exceed current date~~#date_transaction~~0';
                //exit;
            }

			//checking branch balance for cash out

			$balance = CashFlowTransaction::getBranchBalance($_SESSION['session_user_branch_id']);
			$loan_amount = $pval['amount'];

			if($pval['transaction_type'] == 'cashout') {
				 if(floatval($balance) < $loan_amount ){

					 $missing_labels['loan_amount'] = "Cash out exceed the limits of available branch balance of Rs. ".unitDisplay($balance,false);			
				 }
			}


            /*$missing_vals = Validator::validate_data('Cashflowtransaction');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Customer::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1'; */
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
                \"content\" : \"".CASHFLOW_TRANSACTION_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
             exit;
            break;

   case 'add':
   case 'update':

        if($type == 'add') {
            $dval['date_transaction'] = date("Y-m-d",strtotime($pval['date_transaction'].date(" H:i:s")));

            $uval['id'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$pval['transaction_type'],$pval['amount'],$pval['cash_purpose'], $dval['date_transaction']);

            //print_r($pval);

            $dval['created_datetime'] = date('Y-m-d H:i:s');
            $dval['created_by'] = $_SESSION['session_userid'];
            $dval['status'] = 1;
            $dval['editable'] = true;
            /*$dval['modified_datetime'] = date("Y-m-d H:i:s");
            $dval['modified_by'] = $_SESSION['session_userid'];*/
            $app->save($dval, array('id = ' => $uval['id']));
            $uval['id'] = $app->last_inserted_id();
            $_SESSION['success_msg'] =CASHFLOW_ADDED;


        } else {

            $dval['date_transaction'] = date("Y-m-d",strtotime($pval['date_transaction'].date(" H:i:s")));

            //$res = CashFlowTransaction::updateCashFlow($uval['id'], $pval['amount'], $pval['transaction_type'], $dval['date_transaction']);

            $cash_flow = CashFlowTransaction::Detail($uval['id']);
            //Delete Current
            CashFlowTransaction::deleteCashFlow($uval['id']);

            //Insert New with updated one
            $uval['id'] = CashFlowTransaction::insertCashFlow($pval['branch_id'], $pval['transaction_type'],$pval['amount'],$pval['cash_purpose'], $dval['date_transaction']);

            $dval['status'] = 1;
            $dval['editable'] = true;
            $dval['modified_datetime'] = date("Y-m-d H:i:s");
            $dval['modified_by'] = $_SESSION['session_userid'];
            $app->save($dval, array('id = ' => $uval['id']));

            $_SESSION['success_msg'] =CASHFLOW_SAVED;
        }

          echo "
           {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"\",
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
            }";
        exit;
        /*$_SESSION['flash_msg'] = CASH_FLOW_UPDATE;
        header("Location: cash_flow_transactions.php");
        exit;*/
        break;

   case 'create':
   case 'edit':
        /*
         * Get the Customer details, if type is edit
         */
        // echo $uval['id'];
        $cash_data = CashFlowTransaction::Detail($uval['id']);
        $cash_data = $cash_data[0];
         //print_r($cash_data);
        include ADMIN_LAYOUT_PATH . 'forms/cash_flow_transactions.html';
    break;


   case 'paging':
   case 'back':
   case 'refresh':
   case 'search':

         $data = Cashflowtransaction::Listview($search_trans_key, $search_trans_type , $search_branch_id);
         $count = Cashflowtransaction::ListviewCount($search_trans_key, $search_trans_type , $search_branch_id);
         include ADMIN_LAYOUT_PATH . 'cash_flow_transactions.html';
         exit;
         break;

   case 'delete':

        /*
         * Delete from database
         */
        /*if($uval['id']!=""){
            $res = CashFlowTransaction::deleteCashFlow($uval['id']);
        }

        //print_r($_POST["list"]);

        if(is_array($_POST["list"])){
            $i=0;
            foreach($_POST["list"] as $key=>$val){
                $res = CashFlowTransaction::deleteCashFlow($_POST["list"][$i]);
                $i++;
            }
        }
        $_SESSION['success_msg'] =CASHFLOW_DELETED;
         echo "
           {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"\",
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
            }";
       exit;*/


        /*
         * Delete from database
         */

        if($uval['id']!="")
        {

            $name = CashFlowTransaction::getCashFlowPurpose($uval['id']);

            $status = CashFlowTransaction::Delete($uval['id']);

            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".CASHFLOW_DELETED;
            }
            else
            {
                if(is_array($status['error'])){

                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".CASHFLOW_NOT_DELETED." - ".$err;

                    }

                }else{

                    $message[] .= $name."( ".$uval['id']." ) - ".CASHFLOW_NOT_DELETED." - ".$status['error'];

                }
            }
        }

       if(is_array($_POST["list"]))
            {
                $i=0;

                foreach($_POST["list"] as $key=>$val)
                {

                    $name = CashFlowTransaction::getCashFlowPurpose($val);
                    $status = CashFlowTransaction::Delete($val);

                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".CASHFLOWID_DELETED;
                    }
                    else
                    {

                        if(is_array($status['error'])){

                            foreach($status['error'] as $err){

                                $message[] .= $name."( ".$val." ) - ".CASHFLOW_NOT_DELETED." - ".$err;

                            }

                        }else{

                            $message[] .= $name."( ".$val." ) - ".CASHFLOW_NOT_DELETED." - ".$status['error'];

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
   case 'view':
        /*
         * Make the pages with header and footer
         */
        $data = Cashflowtransaction::Listview($search_trans_key, $search_trans_type , $search_branch_id);
        $count = Cashflowtransaction::ListviewCount($search_trans_key, $search_trans_type , $search_branch_id);

        include ADMIN_LAYOUT_PATH . 'cash_flow_transactions.html';


        break;
    default:
        /*
         * Make the pages with header and footer
         */
        $data = CashFlowTransaction::Listview($search_trans_key, $search_trans_type , $search_branch_id);
        //$count = CashFlowTransaction::ListviewCount($search_trans_key, $search_trans_type,$search_branch_id);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'cash_flow_transactions.html';
        #include ADMIN_LAYOUT_PATH . 'forms/cash_flow_transactions.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>