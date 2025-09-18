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
$page_title = 'Auction Management';
$module_name = 'auction_management';

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
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];

$disp = ($disp!='') ? $disp : 'content_div';

$pval = (isset($_GET['pval'])) ? $_GET['pval'] : $_POST['pval'];
$pp_pval = (isset($_GET['pp_pval'])) ? $_GET['pp_pval'] : $_POST['pp_pval'];

$serial_no = $_POST['serial_no'];
$serial_no = $pval['serial_no'];
$auction_charge = $_POST['auction_charge'];
$auction_amount = $_POST['auction_amount'];


$plist = (isset($_GET['plist'])) ? $_GET['plist'] : $_POST['plist'];

$name = $_POST['name'];
$gross_weight = $_POST['gross_weight'];
$category_id = $_POST['category_id'];
$net_weight = $_POST['net_weight'];
$market_rate = $_POST['market_rate'];
$product_id=$_POST["product_id"];
$product_name=$_POST["product_name"];
$product_with=$_POST["product_with"];
$product_without =$_POST["product_without"];
$item_count = $_POST['item_count'];


$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$uval['pa_id'] = (isset($_GET['pa_id'])) ? $_GET['pa_id'] : $_POST['pa_id'];

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];


$uval['pledge_id'] = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];

//constant message for cash flow transaction
$cash_purpose=AUCTION_AMOUNT;
$cash_purpose_loss= AUCTION_AMOUNT_LOSS;
$cash_purpose_update= AUCTION_AMOUNT_UPDATION_LOSS_AMOUNT;
$cashin= CASH_IN;
$cashout= CASH_OUT;

/*LoanId display in the Auction Management*/
$loan_id =  (isset($_GET['loan_code'])) ? $_GET['loan_code'] : $_POST['loan_code'];

/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];
$search_key_location = (isset($_GET['search_key_location'])) ? $_GET['search_key_location'] : $_POST['search_key_location'];
$search_fhw = (isset($_GET['search_fhw'])) ? $_GET['search_fhw'] : $_POST['search_fhw'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_loan_id = (isset($_GET['search_loan_id'])) ? $_GET['search_loan_id'] : $_POST['search_loan_id'];

$search_sdate = (isset($_GET['search_sdate'])) ? $_GET['search_sdate'] : $_POST['search_sdate'];
$search_edate = (isset($_GET['search_edate'])) ? $_GET['search_edate'] : $_POST['search_edate'];

//$search_date = AuctionManagement::getAuctionFilterDate();//getLastAuctionDate();//'2011-05-04';
//$search_sdate = ($search_sdate == '') ? $search_date : $search_sdate;
//$search_sdate = ($search_edate == '') ? date("Y-m-d") : $search_edate;


//echo "  ->".$search_date;


$search_args = "";
if($search_sdate != ""){
    $search_args.="search_sdate=".$search_sdate."&";
}

if($search_edate != ""){
    $search_args.="search_edate=".$search_edate."&";
}

if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
}
if($search_cus_id != ""){
    $search_args.="search_cus_id=".$search_cus_id."&";
}
if($search_key_location != ""){
    $search_args.="search_key_location=".$search_key_location."&";
}
if($search_fhw != ""){
    $search_args.="search_fhw=".$search_fhw."&";
}
if($search_branch_id != ""){
    $search_args.="search_branch_id=".$search_branch_id."&";
}
if($search_loan_id != ""){
    $search_args.="search_loan_id=".$search_loan_id."&";
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

$app->access_table = PRODUCT_AUCTION;

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

            $missing_labels = Validator::validate_json('AuctionManagement');


        // Date and Wavier validation

        $pledge_id = $pval["loan_id"];
        $pay_id = $uval['pay_id'];
        $wavier_days = $pval["wavier_days"];
        $paid_date = date("Y-m-d",strtotime($pval["paid_on"]));;

        $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id,$paid_date,  $type, $module_name);

        if(unitDisplay($interest_details["interest_amount"],false) != $pval['interest_amount']){

            $missing_labels["paid_on_reset"] = AUCTION_PAID_ON_RESET_ALERT;
            $missing_labels["paid_on"] = "";

        }

        if($interest_details["last_pay_date"] > $paid_date || $interest_details["loan_date"] > $paid_date){
            $missing_labels["paid_on_invalid"] = AUCTION_PAID_DATE_INVALID_ALERT;
            $missing_labels["paid_on"] = "";
        }



            $auction_date = date("Y-m-d",strtotime($pval['paid_on']));
            $loan_date = ProductPledge::getLoanDate($pval['loan_id']);
            $loan_date = date("Y-m-d",strtotime($loan_date));
            //echo $auction_date."  > ".date("Y-m-d");
            if($auction_date  > date("Y-m-d"))
            {
                $missing_labels['paid_on_exet']='Invalid auction date. Auction date should not exceed current date';
                $missing_labels["paid_on"] = "";
            //echo 'Invalid auction date. Auction date should not exceed current date~~#auction_date ~~0';
            //exit;
            }

            //echo $loan_date."  > ".$auction_date;
            if($loan_date  > $auction_date)
            {
                $missing_labels['paid_on_exe']='Invalid auction date. Auction date should not less than loan pledged date';
                $missing_labels["paid_on"] = "";
                //echo 'Invalid auction date. Auction date should not less than loan pledged date~~#auction_date ~~0';
                //exit;
            }


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
            //ob_end_clean();
              echo "
               {
                \"page\" : \"".$page."\",
                \"success\" : \"".$success."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".AUCTION_MANAGEMENT_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
            /*$missing_vals = Validator::validate_data('AuctionManagement');
            if (!empty( $missing_vals ))
            {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = AuctionManagement::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1'; */
            exit;
            break;

   case 'save':
   case 'add':
             //p_arr($plist);
            //echo $type; exit;
            $pval['auction_products'] = AuctionManagement::getProductSerialize($plist, $auction_charge, $auction_amount,$serial_no);
            //p_arr($pval['auction_products']);

            //echo "<br>".$pval['auction_products']; exit;
            $pval['paid_on'] = date("Y-m-d H:i:s", strtotime($pval['paid_on'].date(" H:i:s")));

            $pval['branch_id'] = $_SESSION['session_user_branch_id'];
            $pval['customer_id'] = $pp_pval['customer_id'];

            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $pval['status'] = 1;

            //p_arr($pval);

            $app->access_table = PRODUCT_AUCTION;
            //Insert Auction data
            $status = $app->add($pval);
            $uval['pa_id'] = $app->last_inserted_id();



            /** Cash floaw concept Stated **/

            /* *** V1 setup for cash flow - START **

            $cash_in_amt = floatval($pval['auction_amount']) + floatval($pval['auction_charge']);

            //$cash_purpose .= " - (".$pval['loan_id'].")";
            //$cash_purpose_loss .= " - (".$pval['loan_id'].")";
            $cash_purpose .= " - (Loan ID :".$loan_id.")";
            $cash_purpose_loss .= " - (Loan ID :".$loan_id.")";


            //Insert Cash Flow Amount
            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$cash_in_amt,$cash_purpose,$pval['paid_on']);



            //Insert Cash Flow Lose Amount
            if(floatval($pval['amount_difference']) < 0){

                $uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,(-1*floatval($pval['amount_difference'])),$cash_purpose_loss,$pval['paid_on']);

            }else $uval['cod'] = 0;
             *** V1 setup for cash flow - END */

            /*** V2 setup for cash flow - START

            $cashin_amt = $_POST["principle_remain"];
            //$cashin_amt = $pval['auction_amount'];
            $cashin_iamt = $pval['interest_amount'];
            $cashin_samt = $pval['auction_charge'];

            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$cashin_amt,CASHFLOW_PURPOSE_AUCTION, $pval['paid_on'],0,4);
            $uval['icid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$cashin_iamt,CASHFLOW_PURPOSE_AUCTION_INTEREST, $pval['paid_on'],$uval['cid'],10);
            $uval['scid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$cashin_samt,CASHFLOW_PURPOSE_AUCTION_SERVICE, $pval['paid_on'],$uval['cid'],11);


            if(floatval($pval['amount_difference']) < 0){

                $cashout_amt = -1 * $pval['amount_difference'];
                $cashin_pamt =  0;
                $uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$cashout_amt,CASHFLOW_PURPOSE_AUCTION_LOSS, $pval['paid_on'],0, 5);

            }else{
                $cashout_amt = 0;
                $cashin_pamt =  $pval['amount_difference'];
                $uval['pcid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$cashin_pamt,CASHFLOW_PURPOSE_AUCTION_PROFIT, $pval['paid_on'],$uval['cid'], 6);
            }







                //Add Pay Interest Details for the loan id
                $pi_pval['branch_id'] = $pp_pval['branch_id'];
                $pi_pval['customer_id'] = $pp_pval['customer_id'];
                $pi_pval['cash_flow_id'] = $uval['icid'];
                $pi_pval['loan_id'] = $pval['loan_id'];
                $pi_pval['principle_amount'] = 0;//$pval['principle_amount'];
                $pi_pval['interest_rate'] = $pval['interest_rate'];
                $pi_pval['interest_amount'] = $pval['interest_amount'];
                $pi_pval['additional_charge'] = $pval['additional_charge'];
                $pi_pval['interest_days'] = $pval['interest_days'];
                $pi_pval['total_paid_amount'] = floatval($pval['interest_days'])+floatval($pval['additional_charge']);
                $pi_pval['wavier_days'] = $pval['wavier_days'];
                $pi_pval['wavier_reason'] = $pval['wavier_reason'];
                $pi_pval['wavier_amount'] = $pval['wavier_amount'];
                $pi_pval['paid_on'] = $pval['paid_on'];

                $pay_interest_details = PayInterest::getInterestDetails($pval['loan_id'], $pval['wavier_days'],'',$pval['paid_on']);

                $pi_pval['pay_interest_details'] = serialize($pay_interest_details);

                $pi_pval['created_datetime'] = date('Y-m-d H:i:s');
                $pi_pval['created_by'] = $_SESSION['session_userid'];
                $pi_pval['status'] = 1;
                $app->access_table = PRODUCT_INTERESTS;
                $app->add($pi_pval);

                $uval['pay_id'] = $app->last_inserted_id();

            //Update Cash Flow Id in Auction
            $app->access_table = PRODUCT_AUCTION;
            $app->save(array(
                    "pay_id"=>$uval['pay_id'],
                    "cash_in_id"=>$uval['cid'],
                    "cash_out_id"=>$uval['cod']
                ), array('id = ' => $uval['pa_id']));

            *** V2 setup for cash flow - END */

            $cashin_amt = $pval['auction_amount'];
            $cashout_samt = $pval['auction_charge'];

            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$cashin_amt,CASHFLOW_PURPOSE_AUCTION, $pval['paid_on'],0,4);
            $uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$cashout_samt,CASHFLOW_PURPOSE_AUCTION_SERVICE, $pval['paid_on'],0,11);

            //Update Cash Flow Id in Auction
            $app->access_table = PRODUCT_AUCTION;
            $app->save(array(
                    "cash_in_id"=>$uval['cid'],
                    "cash_out_id"=>$uval['cod']
                ), array('id = ' => $uval['pa_id']));

            /** Cash flow concept Ended **/



            $app->access_table = PLEDGE;
            $res = $app->save(array("status"=>-2,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']),array("id = "=>$pval['loan_id']));

            $app->access_table = PLEDGE_DETAILS;
            $res = $app->save(array("status"=>0,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']),array("id = "=>$pval['loan_id']));


            $app->access_table = SETTINGS;
            $app->save(array("config_value"=>$pval['paid_on']),array("config_name ="=>'LAST_AUCTION_DATE'));

            $_SESSION['success_msg'] = BIDAUCTION_ADDED;
            $isMsgDisplay = false;

              //ob_end_clean();
              echo "
               {
                \"page\" : \"".$page."\",
                \"status\" : \"".$status."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".BIDAUCTION_ADDED."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
            exit;
   break;

   case 'update':
            //echo $type; exit;
            $pval['auction_products'] = AuctionManagement::getProductSerialize($plist, $auction_charge, $auction_amount,$serial_no);

            //echo "<br>".$pval['auction_products']; exit;
            $pval['paid_on'] = date("Y-m-d H:i:s", strtotime($pval['paid_on'].date(" H:i:s")));

            $pval['branch_id'] = $_SESSION['session_user_branch_id'];
            $pval['customer_id'] = $pp_pval['customer_id'];            ;

            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $pval['status'] = 1;

            //Save Auction data
            $app->save($pval, array('id = ' => $uval['id']));

            //get Cash Flow ID
            $cash_detail = $app->find(
                            array("cash_in_id", "cash_out_id","pay_id"),
                            array(
                                "conditions"=>array("id = "=>$uval['id'])
                             ));


            //Delete previous cash in records
            CashFlowTransaction::deleteCashFlow($cash_detail[0]['cash_in_id']);

            //Delete previous cash out records
            CashFlowTransaction::deleteCashFlow($cash_detail[0]['cash_out_id']);

            //Delete Interest Details
            $app->access_table = PRODUCT_INTERESTS;
            $res = $app->delete(array("id"=>$cash_detail[0]['pay_id']));

            //$cash_purpose .= " - (".$pval['loan_id'].")";
            //$cash_purpose_loss .= " - (".$pval['loan_id'].")";
            /*$cash_purpose .= " - (Loan ID :".$loan_id.")";
            $cash_purpose_loss .= " - (Loan ID :".$loan_id.")";

            $cash_in_amt = floatval($pval['auction_amount']) + floatval($pval['auction_charge']);
            //Insert Cash Flow Amount
            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$cash_in_amt,$cash_purpose,$pval['paid_on']);
            //Insert Cash Flow Lose Amount
            if(floatval($pval['amount_difference']) < 0){

                $uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,(-1*floatval($pval['amount_difference'])),$cash_purpose_loss,$pval['paid_on']);

            }else $uval['cod'] = 0;*/

            /*** V2 setup for cash flow - START
            $cashin_amt = $_POST["principle_remain"];
            $cashin_iamt = $pval['interest_amount'];
            $cashin_samt = $pval['auction_charge'];

            $uval['cid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_amt,CASHFLOW_PURPOSE_AUCTION, $pval['paid_on'],0,4);
            $uval['icid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_iamt,CASHFLOW_PURPOSE_INTEREST, $pval['paid_on'],$uval['cid'],3);
            $uval['scid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_samt,CASHFLOW_PURPOSE_SERVICE, $pval['paid_on'],$uval['cid'],7);


            if(floatval($pval['amount_difference']) < 0){

                $cashout_amt = -1 * $pval['amount_difference'];
                $cashin_pamt =  0;
                $uval['cod'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashout,$cashout_amt,CASHFLOW_PURPOSE_AUCTION_LOSS, $pval['paid_on'],0,5);

            }else{
                $cashout_amt = 0;
                $cashin_pamt =  $pval['amount_difference'];
                $uval['pcid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashout,$cashin_pamt,CASHFLOW_PURPOSE_AUCTION_PROFIT, $pval['paid_on'],$uval['cid'],6);
            }







                //Add Pay Interest Details for the loan id
                $pi_pval['branch_id'] = $pp_pval['branch_id'];
                $pi_pval['customer_id'] = $pp_pval['customer_id'];
                $pi_pval['cash_flow_id'] = $uval['icid'];
                $pi_pval['loan_id'] = $pval['loan_id'];
                $pi_pval['principle_amount'] = 0;//$pval['principle_amount'];
                $pi_pval['interest_rate'] = $pval['interest_rate'];
                $pi_pval['interest_amount'] = $pval['interest_amount'];
                $pi_pval['additional_charge'] = $pval['additional_charge'];
                $pi_pval['interest_days'] = $pval['interest_days'];
                $pi_pval['total_paid_amount'] = floatval($pval['interest_days'])+floatval($pval['additional_charge']);
                $pi_pval['wavier_days'] = $pval['wavier_days'];
                $pi_pval['wavier_reason'] = $pval['wavier_reason'];
                $pi_pval['wavier_amount'] = $pval['wavier_amount'];
                $pi_pval['paid_on'] = $pval['paid_on'];

                $pay_interest_details = PayInterest::getInterestDetails($pval['loan_id'], $pval['wavier_days'],'',$pval['paid_on']);

                $pi_pval['pay_interest_details'] = serialize($pay_interest_details);

                $pi_pval['created_datetime'] = date('Y-m-d H:i:s');
                $pi_pval['created_by'] = $_SESSION['session_userid'];
                $pi_pval['status'] = 1;
                $app->access_table = PRODUCT_INTERESTS;
                $app->add($pi_pval);

                $uval['pay_id'] = $app->last_inserted_id();



            //Update Cash Flow Id in Auction
            $app->access_table = PRODUCT_AUCTION;
            $app->save(array(
                    "pay_id"=>$uval['pay_id'],
                    "cash_in_id"=>$uval['cid'],
                    "cash_out_id"=>$uval['cod'],
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
             ), array('id = ' => $uval['id']));
            /* V2 setup for cash flow - END **/


            $cashin_amt = $pval['auction_amount'];
            $cashout_samt = $pval['auction_charge'];

            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$cashin_amt,CASHFLOW_PURPOSE_AUCTION, $pval['paid_on'],0,4);
            $uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$cashout_samt,CASHFLOW_PURPOSE_AUCTION_SERVICE, $pval['paid_on'],0,11);

            //Update Cash Flow Id in Auction
            $app->access_table = PRODUCT_AUCTION;
            $app->save(array(
                    "cash_in_id"=>$uval['cid'],
                    "cash_out_id"=>$uval['cod']
                ), array('id = ' => $uval['id']));

            /** Cash flow concept Ended **/

            $app->access_table = PLEDGE;
            $res = $app->save(array("status"=>-2,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']),array("id = "=>$pval['loan_id']));

            $app->access_table = PLEDGE_DETAILS;
            $res = $app->save(array("status"=>0,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']),array("id = "=>$pval['loan_id']));


            $app->access_table = SETTINGS;
            $app->save(array("config_value"=>$pval['auction_date']),array("config_name ="=>'LAST_AUCTION_DATE'));

            $isMsgDisplay = false;
            $_SESSION['success_msg'] = BIDAUCTION_SAVED;
            //ob_end_clean();
              echo "
               {
                \"page\" : \"".$page."\",
                \"status\" : \"".$status."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".BIDAUCTION_SAVED."\",
                \"isAlert\" : \"".$isMsgDisplay."\",
                \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
   break;

   case 'delete':

        $app->access_table = PRODUCT_AUCTION;
        //echo $query = "SELECT cash_in_id, cash_out_id, loan_id FROM ".PRODUCT_AUCTION."  WHERE id = '".$pval['id']."'";
        //$cash_detail = $app->getrows($query);
        //get Cash Flow ID
        $cash_detail = $app->find(
                        array("cash_in_id","cash_out_id","loan_id","pay_id"),
                        array(
                            "conditions"=>array("id = "=>$pval['id'])
                         ));
                //p_arr($cash_detail); exit;
        //Delete current flow id and reset Cash flow records after this flow id
        $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_in_id"]);

        $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_out_id"]);


        $app->access_table = PLEDGE;
        $status = $app->save(array("status"=>1,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']), array('id = ' => $cash_detail[0]['loan_id']));
        $app->access_table = PLEDGE_DETAILS;
        $status = $app->save(array("status"=>1,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']), array('id = ' => $cash_detail[0]['loan_id']));

        $app->access_table = PRODUCT_INTERESTS;
        $app->delete(array("id"=>$cash_detail[0]['pay_id']));

        $app->access_table = PRODUCT_AUCTION;
        //$status = $app->delete($pval, array('id' => $pval['id']));
        $app->delete(array('id' => $pval['id']));



        $isMsgDisplay = false;
        $_SESSION['success_msg'] = BIDAUCTION_SAVED;
        //ob_end_clean();
          echo "
           {
            \"page\" : \"delete_selected\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"".BIDAUCTION_DELETED."\",
            \"isAlert\" : \"".$isMsgDisplay."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
            }";

   break;



  case 'bid':
  case 'create':
  case 'edit':
       /*
        * Get the Customer details, if type is edit
        */
        //echo $uval['id'];
       $product_details = AuctionManagement::Detail($uval['id'], $uval['pa_id']);
       $product_data = $product_details[0];
       //print_r($product_data);exit;
       include ADMIN_LAYOUT_PATH . 'forms/auction_management.html';

    break;
   case 'delete-confirm':
        /* * Get the Customer details, if type is edit*/

        $customer_data = AuctionManagement::Detail($uval['id'],$uval['pa_id']);
        //print_r($product_data); exit;
        $product_data = $customer_data;
        $customer_data = $customer_data[0];

        include ADMIN_LAYOUT_PATH . 'elements/delete_auction_management.html';
        exit;
        break;


   case 'paging':
   case 'back':
   case 'search':
   case 'refresh':

         $data = AuctionManagement::Listview($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         //$count = AuctionManagement::ListviewCount($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);

         include ADMIN_LAYOUT_PATH . 'auction_management.html';
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
   case 'fetch_loan_details':
        /*
         * Fetch schama description and interst type
         */
         $app->access_table = PLEDGE;
         $loan_details = ProductPledge::Detail_Renewal($old_loan_id);
         echo $loan_details[0]['loan_amount']."~~".$loan_details[0]['pro_id']."~~".$loan_details[0]['description']."~~".$loan_details[0]['scheme_name'];
        exit;
        break;


    case 'view':
        /*
         * Make the pages with out header and footer
         */
        $data = AuctionManagement::Listview($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$count = AuctionManagement::ListviewCount($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        include ADMIN_LAYOUT_PATH . 'auction_management.html';

        break;

    default:
        /*
         * Make the pages with header and footer
         */

        //$data = AuctionManagement::Listview($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
       // echo "12";exit;
        //$count = AuctionManagement::ListviewCount($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
       //$category_data = ProductCategory::getProductCategoryOptData();
        include ADMIN_LAYOUT_PATH . 'common/header.html';

        if($uval['pledge_id']!=''){
            $data = AuctionManagement::Listview($search_sdate, $search_edate, $search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
             $category_data = ProductCategory::getProductCategoryOptData();
            $product_data = AuctionManagement::ProductDetail($uval['pledge_id']);
            //$product_data = $product_data[0];
            //echo $uval['pledge_id'];
            //print_r(array_keys($product_data));
            include ADMIN_LAYOUT_PATH . 'forms/auction_management.html';
        }else{
            include ADMIN_LAYOUT_PATH . 'auction_management.html';
        }
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>
