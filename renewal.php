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
$page_title = 'Renewal';
$module_name = 'renewal';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = TRANSACTIONS_CAT_ID;


 $menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);

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
 $pp_pval = $_POST['pp_pval'];
 $pval = $_POST['pval'];
 $name = $_POST['name'];
 $gross_weight = $_POST['gross_weight'];
 $category_id = $_POST['category_id'];
 $net_weight = $_POST['net_weight'];
 $market_rate = $_POST['market_rate'];
 $product_id=$_POST["product_id"];
 $item_count = $_POST['item_count'];
 (int)$old_loan_id = $_POST['old_loan_id'];
 (int)$scheme_id=$_POST['scheme_id'];

$product_items =$_POST["product_items"];
$product_count = $_POST['product_count'];

$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];

$disp = ($disp!='') ? $disp : 'content_div';



$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

$customer_id =(isset($_GET['customer_id'])) ? $_GET['customer_id'] : $_POST['customer_id'];
$uval['pledge_id'] = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];
$uval['renew_id'] = (isset($_GET['renew_id'])) ? $_GET['renew_id'] : $_POST['renew_id'];


//constant message for cash flow transaction
$cashin_purpose=RENEWAL_RETURN_PAYMENT;
$cashout_purpose=RENEWAL_PAYMENT;
$cash_purpose_delete=PAY_INTEREST_DELETE;
$cashin= CASH_IN;
$cashout= CASH_OUT;

/*LoanId display in the Auction Management*/
$old_loan_id =  (isset($_GET['old_loan_code'])) ? $_GET['old_loan_code'] : $_POST['old_loan_code'];


/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];
$search_key_location = (isset($_GET['search_key_location'])) ? $_GET['search_key_location'] : $_POST['search_key_location'];
$search_fhw = (isset($_GET['search_fhw'])) ? $_GET['search_fhw'] : $_POST['search_fhw'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_loan_id = (isset($_GET['search_loan_id'])) ? $_GET['search_loan_id'] : $_POST['search_loan_id'];

$search_args = "";
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
$timezone = "Asia/Calcutta";
if(function_exists('date_default_timezone_set')) date_default_timezone_set($timezone);

$app->access_table = RENEWAL;

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
             $missing_labels = Validator::validate_json('Renewal');


            // Date and Wavier validation

        $pledge_id = $pval["old_loan_id"];
        $pay_id = $uval['pay_id'];
        $wavier_days = $pval["wavier_days"];
        $paid_date = date("Y-m-d",strtotime($pval["paid_on"]));
        $cur_date = date("Y-m-d");

        $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id,$paid_date,  $type, $module_name);

        if(unitDisplay($interest_details["interest_amount"],false) != $pval['interest_amount']){

            $missing_labels["paid_on_reset"] = PAID_ON_RESET_ALERT;
            $missing_labels["paid_on"] = "";

        }

        if($interest_details["last_pay_date"] > $paid_date || $interest_details["loan_date"] > $paid_date || $cur_date < $paid_date){
            $missing_labels["paid_on"] = PAID_DATE_INVALID_ALERT;
        }

        if($interest_details["default_wavier_days"] > $interest_details["no_of_days"] && $interest_details["wavier_days"]!=0){
            $missing_labels["wavier_days"] = WAVIER_DAYS_INVALID_ALERT;
        }


        $old_scheme_id = $_POST["old_scheme_id"];
        $new_scheme_id = $pval['new_scheme'];

        if($old_scheme_id != $new_scheme_id && !isset($missing_labels["new_scheme"])){

            $missing_labels["new_scheme"] = SCHEME_RESET_ALERT;

        }

            if(isset($_POST['principle_remain'])){
                if(floatval($_POST['principle_remain']) <= 0)
                {
                 $missing_labels['principle_amount']='All Principle Amount Paid, Customer can Redeam Their Product';
                    //echo "All Principle Amount Paid, Customer can Redeam Theire Product".'~~#principle_closed,#principle_amount,#principle_amt~~0';
                    //exit;
                }

                if(floatval($_POST['principle_remain']) < floatval($pval["principle_amount"]))
                {
                     $missing_labels['principle_amount']='Principle Amount Exceed the limits';
                //echo "Principle Amount Exceed the limits".'~~#principle_amount,#principle_amt~~0';
                //exit;
                }

            }

             /*
            $missing_vals = Validator::validate_data('Renewal');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Renewal::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1';
            exit; */
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
                \"content\" : \"".RENEW_THE_PLEGDED_PRODUCT_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
               exit;
            break;


   case 'save':
   case 'update':

            // Renewal Table Insert
            $app->access_table = RENEWAL;
            unset($pval['loan_id']);



            //Enable temporerily for testing purpose
            $pval['paid_on'] = date('Y-m-d',strtotime($pval["paid_on"]));

            $pval['branch_id'] = $_SESSION['session_user_branch_id'];
            $pval['customer_id'] = $pp_pval['customer_id'];
            
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $pval['status'] = 1;
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $app->add($pval);
            $uval['id'] = $app->last_inserted_id();
            //exit;

            //Update Loan Config data
            $query = "UPDATE ".SETTINGS." SET config_value = config_value+1 WHERE config_name='loan_count'";
            $app->execute($query);
            $add_to_renew = (isset($_GET['add_to_renew'])) ? $_GET['add_to_renew'] : $_POST['add_to_renew'];

            //Cash Flow Insertion
            /*
            $cashin_amt = $_POST["principle_remain"] + (($add_to_renew=="1") ? ($pval['interest_amount'] + $pval['additional_charge']) : 0);
            $cashout_amt =  $pp_pval['loan_amount'] - $pval['service_charge'];

            $cashin_purpose .= " - (Old Loan ID :".$old_loan_id.")";
            $cashout_purpose .= " - (Old Loan ID :".$old_loan_id.")";

            $uval['cid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],CASH_IN,$cashin_amt,$cashin_purpose,$pval['paid_on']);
            $uval['cod'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],CASH_OUT,$cashout_amt,$cashout_purpose,$pval['paid_on']);
            */

            $cashin_amt = $_POST["principle_remain"]; 
            $cashout_amt =  $pp_pval['loan_amount'];
            $cashin_samt = $pval['service_charge'];

            $uval['cid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_amt,CASHFLOW_PURPOSE_REDEEMED, $pval['paid_on'],0, 2);


			$cashin_iamt = $pval['interest_amount'];
			$cashin_aamt = $pval['additional_charge'];
			
			$uval['icid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_iamt,CASHFLOW_PURPOSE_INTEREST, $pval['paid_on'],$uval['cid'], 3);
			$uval['acid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_aamt,CASHFLOW_PURPOSE_ADDITIONAL, $pval['paid_on'],$uval['icid'], 8);
            
            $uval['cod'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashout,$cashout_amt,CASHFLOW_PURPOSE_PLEDGED, $pval['paid_on'],0, 1);
            
            $uval['scid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_samt,CASHFLOW_PURPOSE_SERVICE, $pval['paid_on'],$uval['cod'],7);

            //if($add_to_renew!="1") { Mdofied by vinoth 02Mar2013

                //Add Pay Interest Details for the loan id
                $pi_pval['branch_id'] = $pp_pval['branch_id'];
                $pi_pval['customer_id'] = $pp_pval['customer_id'];
                $pi_pval['loan_id'] = $pval['old_loan_id'];
                $pi_pval['cash_flow_id'] = $uval['icid'];
                $pi_pval['principle_amount'] = 0;
                $pi_pval['interest_rate'] = $pval['interest_rate'];
                $pi_pval['interest_amount'] = $pval['interest_amount'];
                $pi_pval['additional_charge'] = $pval['additional_charge'];
                $pi_pval['interest_days'] = $pval['interest_days'];
                $pi_pval['total_paid_amount'] = floatval($pval['principle_amount'])+floatval($pval['interest_amount'])+floatval($pval['additional_charge'])+floatval($pval['wavier_amount']);
                $pi_pval['wavier_days'] = $pval['wavier_days'];
                $pi_pval['wavier_reason'] = $pval['wavier_reason'];
                $pi_pval['wavier_amount'] = $pval['wavier_amount'];

                //Commented temporerily for testing purpose
                //$pi_pval['paid_on'] = date('Y-m-d');

                //Enable temporerily for testing purpose
                $pi_pval['paid_on'] = $pval['paid_on'];

                $pay_interest_details = PayInterest::getInterestDetails($pval['old_loan_id'], $pval['wavier_days'],'',$pval['paid_on']);
                $pi_pval['pay_interest_details'] = serialize($pay_interest_details);

                $pi_pval['created_datetime'] = date('Y-m-d H:i:s');
                $pi_pval['created_by'] = $_SESSION['session_userid'];
                $pi_pval['status'] = 1;
                /*$pi_pval['modified_datetime'] = date("Y-m-d H:i:s");
                $pi_pval['modified_by'] = $_SESSION['session_userid'];*/
                $app->access_table = PRODUCT_INTERESTS;
                $app->add($pi_pval);
                $uval['pay_id'] = $app->last_inserted_id();

            /*}else{


                $uval['pay_id'] = 0;
            }*/

            //Update and Close Old Loan Id
            $app->access_table = PLEDGE;
            $app->save(array(
                    "status"=> -1,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
                ), array('id = ' => $pval['old_loan_id']));


            //Renewal Cash flow Id Relation Setting
            $app->access_table = RENEWAL;
            $app->save(array(
                    "pay_id"=>$uval['pay_id'],
                    "cash_in_id"=>$uval['cid'],
                    "cash_out_id"=>$uval['cod']
                    /*"modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']*/
                ), array('id = ' => $uval['id']));


            //Redemption Updates
            $pr_pval['branch_id'] = $pp_pval['branch_id'];
            $pr_pval['customer_id'] = $pp_pval['customer_id'];
            $pr_pval['loan_id'] = $pval['old_loan_id'];
            $pr_pval['cash_flow_id'] = $uval['cid'];
            $pr_pval['pay_id'] = $uval['pay_id'];
            $pr_pval['paid_on'] = $pval['paid_on'];
            $pr_pval['principle_amount'] = $_POST['principle_remain'];
            $pr_pval['interest_rate'] = $pval['interest_rate'];
            $pr_pval['interest_amount'] = $pval['interest_amount'];
            $pr_pval['additional_charge'] = $pval['additional_charge'];
            $pr_pval['interest_days'] = $pval['interest_days'];
            $pr_pval['total_paid_amount'] = floatval($pval['total_paid_amount'])+floatval($_POST['principle_remain']) +floatval($pval['wavier_amount']);
            $pr_pval['wavier_days'] = $pval['wavier_days'];
            $pr_pval['wavier_reason'] = $pval['wavier_reason'];
            $pr_pval['wavier_amount'] = $pval['wavier_amount'];


            $loan_details = Redemption::Detail($pval['old_loan_id']);
            $pr_pval['loan_details'] = serialize($loan_details);

            $pr_pval['created_datetime'] = date('Y-m-d H:i:s');
            $pr_pval['created_by'] = $_SESSION['session_userid'];
            $pr_pval['status'] = 1;
            /*$pr_pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pr_pval['modified_by'] = $_SESSION['session_userid'];*/
            $app->access_table = REDEMPTION;
            $app->add($pr_pval);
            




            //Renew New Loan
            $app->access_table = PLEDGE;
            $pp_pval['loan_id'] = $pval['new_loan_id'];

            //Commented temporerily for testing purpose
            //$pp_pval['loan_date'] = date('Y-m-d H:i:s');

            //Enable temporerily for testing purpose
            $pp_pval['loan_date'] = $pval['paid_on'];
            $pp_pval['cash_flow_id'] = $uval['cod'];
            $pp_pval['scheme_id'] = $pval['new_scheme'];
            $pp_pval['service_charge'] = $pval['service_charge'];
            $pp_pval['total_amount_given'] = floatval($pp_pval[loan_amount])-floatval($pval['service_charge']);
            $pp_pval['service_charge'] = $pval['service_charge'];
            $pp_pval['created_datetime'] = date('Y-m-d H:i:s');
            $pp_pval['created_by'] = $_SESSION['session_userid'];
            $pp_pval['status'] = 1;
            /*$pp_pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pp_pval['modified_by'] = $_SESSION['session_userid'];*/
            $app->add($pp_pval);
            $uval['pledge_id'] = $app->last_inserted_id();

            //Pledge Product Detail Insertion for the renewed pledge

            $app->access_table = PLEDGE_DETAILS;

            $old_rec = $app->find(array('*'),array('conditions'=>array('pledge_id = '=>$pval['old_loan_id'])));

            foreach($old_rec as $rec){

                $ins['pledge_id'] = $uval['pledge_id'];
                $ins['category_id'] = $rec['category_id'];
                $ins['product_id'] = $rec['product_id'];
                //$ins['gross_weight'] = $rec['gross_weight'];
                //$ins['net_weight'] = $rec['net_weight'];
                //$ins['market_rate'] = $rec['market_rate'];
                $ins['product_items'] = $rec['product_items'];

                $ins['created_datetime'] = date('Y-m-d H:i:s');
                $ins['created_by'] = $_SESSION['session_userid'];
                $ins['status'] = 1;
                /*$ins['modified_datetime'] = date("Y-m-d H:i:s");
                $ins['modified_by'] = $_SESSION['session_userid'];*/
                $app->add($ins);

            }

            /*$app->save(array(
                                "pledge_id"=> $uval['pledge_id'],
                                "modified_datetime"=> date("Y-m-d H:i:s"),
                                "modified_by" => $_SESSION['session_userid']
                ), array('pledge_id = ' => $pval['old_loan_id']));*/

            //exit;

            $isMsgDisplay = false;
            $args = "save";

            ob_end_clean();
            $_SESSION['success_msg'] = RENEWAL_SUCCESS;
              echo "
               {
                \"page\" : \"".$page."\",
                \"status\" : \"".$status."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".RENEWAL_SUCCESS."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";

            exit;
   break;


   case 'delete':

   break;

   case 'add':
   case 'renew':
   case 'create':
   case 'edit':
        /*
         * Get the Customer details, if type is edit
         */
         //echo $uval['id'];
        $product_details = Renewal::Detail($uval['id']);
        $product_data = $product_details[0];
        //print_r($product_data);exit;
        include ADMIN_LAYOUT_PATH . 'forms/renewal.html';

        break;

   case 'paging':
   case 'back':
   case 'refresh':
   case 'search':

         $data = Renewal::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         $count = Renewal::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         $category_data = ProductCategory::getProductCategoryOptData();
         include ADMIN_LAYOUT_PATH . 'renewal.html';

         exit;
         break;

   case 'delete':
        /*
         * Delete from database
         */

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

    default:
        /*
         * Make the pages with header and footer
         */
        //$data = Renewal::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
       // $count = Renewal::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$category_data = ProductCategory::getProductCategoryOptData();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        if($uval['pledge_id']!=''){
            $product_details = Renewal::ProductDetail($uval['pledge_id']);
            $product_data = $product_details[0];
            //echo $uval['pledge_id'];
            //print_r(array_keys($product_data));
            include ADMIN_LAYOUT_PATH . 'forms/renewal.html';
        }else{
            include ADMIN_LAYOUT_PATH . 'renewal.html';
        }
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
