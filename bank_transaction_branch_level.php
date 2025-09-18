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
$page_title = 'Bank Transaction branch Level';
$module_name = 'bank_transaction_branch_level';

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

//constant message for cash flow transaction
$cash_purpose_cr= BANK_TRANSACTION_CREDIT;
$cash_purpose_dr= BANK_TRANSACTION_DEBIT;
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

$app->access_table = BANK_TRANSACTION;

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
            /*$missing_vals = Validator::validate_data('Banktransactionbranchlevel');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Customer::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1';
            exit;*/
            $missing_labels = Validator::validate_json('Banktransactionbranchlevel');



            if($pval['amount_credited']=="" && $pval['amount_debited']==""){

            $missing_labels['amount_credited'] = "Both Amount Credited and Amount Debited could not be null. Require minimum any one of these detail";
            $missing_labels['amount_debited'] = "";

            }

            if(($pval['mode_of_deposit']=="both" || $pval['mode_of_deposit']=="cheque" ) && $pval['cheque_no']==""){

            $missing_labels['cheque_no'] = "Cheque No could not be empty. Minimum one cheque no required. Mode of payment included cheque mode";


            }

            $trans_date = date('Y-m-d',strtotime($pval["transaction_date"]));
            $cur_date = date('Y-m-d');

            if($trans_date > $cur_date){

                $missing_labels['transaction_date'] = 'Transaction date should not greater than the current date';

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


              echo "
               {
                \"page\" : \"".$page."\",
                \"success\" : \"".$success."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".BANK_TRANSACTION_BRANCH_LEVEL_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";

            break;

   case 'add':
   case 'save':
   case 'update':

    $pval['transaction_date'] = date("Y-m-d H:i:s",strtotime($pval['transaction_date'].date(" H:i:s")));

        if($type == 'add')
        {
            if(isset($pval['id'])) unset($pval['id']);
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];

            $pval['status'] = 1;
            $app->add($pval);

            $uval['id'] = $app->last_inserted_id();

            if($pval['amount_credited']){
            //cash_in
                $uval['cash_in_id'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['amount_credited'],$cash_purpose_cr,$pval['transaction_date']);
            }else{
                $uval['cash_in_id'] =  0;
            }
             //cash in
             $app->access_table = BANK_TRANSACTION;
             $app->save(array(
                    "cash_in_id"=>$uval['cash_in_id']
                    /*"modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']*/
                ), array('id = ' => $uval['id']));


            if($pval['amount_debited']){
            //cash_out
                $uval['cash_out_id'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$pval['amount_debited'],$cash_purpose_dr,$pval['transaction_date']);
            }else{
                $uval['cash_out_id'] =  0;
            }

            //amount debit
            //cash out
             $app->access_table = BANK_TRANSACTION;
              $app->save(array(
                    "cash_out_id"=>$uval['cash_out_id']
                    /*"modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']*/
                ), array('id = ' => $uval['id']));


            //$status=$app->save($pval, array('id = ' => $uval['id']));
            $success_msg = Bank_transaction_saved;


        } else {
            $app->access_table = BANK_TRANSACTION;
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];

            $status=$app->save($pval, array('id = ' => $uval['id']));

            //get Cash Flow ID
              $app->access_table = BANK_TRANSACTION;
              $cash_detail = $app->find(
                            array("cash_in_id","cash_out_id"),
                            array(
                                "conditions"=>array("id = "=>$uval['id'])
                             ));


            //Update Cash In Flow Amount
            $res = CashFlowTransaction::deleteCashFlow($cash_detail[0]['cash_in_id']);

           //Update Cash Out Flow Amount
            $res = CashFlowTransaction::deleteCashFlow($cash_detail[0]['cash_out_id']);

            //cash_in
            $uval['cash_in_id'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['amount_credited'],$cash_purpose_cr,$pval['transaction_date']);
            //cash_out
            $uval['cash_out_id'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$pval['amount_debited'],$cash_purpose_dr, $pval['transaction_date']);

             $app->access_table = BANK_TRANSACTION;
              $app->save(array(
                    "cash_in_id"=>$uval['cash_in_id'],
                    "cash_out_id"=>$uval['cash_out_id'],
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
                ), array('id = ' => $uval['id']));

            $success_msg = Bank_transaction_update;
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

        exit;

    break;

   case 'create':
   case 'edit':
        /*
         * Get the Branch Transaction details, if type is edit
         */
        $customer_data = Banktransactionbranchlevel::Detail($uval['id']);
        $customer_data = $customer_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/bank_transaction_branch_level.html';
        break;

   case 'refresh':
   case 'paging':
   case 'back':

         $data = Banktransactionbranchlevel::Listview($search_key, $search_cus_id);
         $count = Banktransactionbranchlevel::ListviewCount($search_key, $search_cus_id);
         include ADMIN_LAYOUT_PATH . 'bank_transaction_branch_level.html';
         #include ADMIN_LAYOUT_PATH . 'customer.html';
         exit;
         break;

   case 'delete':
        /*
         * Delete from database
         */

        if($uval['id']!="")
        {
           //get Cash Flow ID
           $app->access_table = BANK_TRANSACTION;
           $cash_detail = $app->find(
                            array("cash_in_id","cash_out_id"),
                            array(
                                "conditions"=>array("id = "=>$uval['id'])
                             ));
            //Delete current flow id and reset Cash flow records after this flow id
            $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_in_id"]);
            $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_out_id"]);

            $name = Banktransactionbranchlevel::getBankName($uval['id']);

            $status = Banktransactionbranchlevel::Delete($uval['id']);

            if($status["success"])
            {
                $message[] = "Branchid=".$name."( Transactionid=".$uval['id']." ) - ".BANKTRANSACTION_DELETED;
            }
            else
            {
                if(is_array($status['error']))
                {

                    foreach($status['error'] as $err){

                        $message[] .= "Branchid=".$name."( Transactionid=".$uval['id']." ) - ".BANKTRANSACTION_NOT_DELETED." - ".$err;

                    }

                }else
                {

                    $message[] .= "Branchid=".$name."( Transactionid=".$uval['id']." ) - ".BANKTRANSACTION_NOT_DELETED." - ".$status['error'];

                }
            }
        }

       if(is_array($_POST["list"]))
            {
                $i=0;

                foreach($_POST["list"] as $key=>$val)
                {

                    $cash_detail = $app->find(
                            array("cash_in_id","cash_out_id"),
                            array(
                                "conditions"=>array("id = "=>$val)
                             ));
                    //Delete current flow id and reset Cash flow records after this flow id
                    $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_in_id"]);
                    $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_out_id"]);

                    $name = Banktransactionbranchlevel::getBankName($val);
                    $status = Banktransactionbranchlevel::Delete($val);

                    if($status["success"])
                    {
                        $message[] = "Branchid=".$name."( Transactionid=".$val." ) - ".BANKTRANSACTION_DELETED;
                    }
                    else
                    {

                        if(is_array($status['error'])){

                            foreach($status['error'] as $err){

                                $message[] .= "Branchid=".$name."( Transactionid=".$val." ) - ".BANKTRANSACTION_NOT_DELETED." - ".$err;

                            }

                        }else{

                            $message[] .= "Branchid=".$name."( Transactionid=".$val." ) - ".BANKTRANSACTION_NOT_DELETED." - ".$status['error'];

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
        $data = Banktransactionbranchlevel::Listview($search_key, $search_cus_id);
        $count = Banktransactionbranchlevel::ListviewCount($search_key, $search_cus_id);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'bank_transaction_branch_level.html';
        #include ADMIN_LAYOUT_PATH . 'forms/bank_transaction_branch_level.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>