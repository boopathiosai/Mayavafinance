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
$page_title = 'Pay Interest';
$module_name = 'pay_interest';

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
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];

$disp = ($disp!='') ? $disp : 'content_div';

$pval = (isset($_GET['pval'])) ? $_GET['pval'] : $_POST['pval'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$uval['pi_id'] = (isset($_GET['pi_id'])) ? $_GET['pi_id'] : $_POST['pi_id'];

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];
(int)$old_loan_id = $_POST['old_loan_id'];

$uval['pledge_id'] = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];


//constant message for cash flow transaction
$cash_purpose= PAY_INTEREST;
$cashin= CASH_IN;
$cashout= CASH_OUT;
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

$app->access_table = PRODUCT_INTERESTS;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);



switch(trim($type)) {

   case 'formvalidation':

            /*
             * Form validation here . notify error, if exists.
             */



            $missing_labels = Validator::validate_json('PayInterest');

            $pledge_id = $pval["loan_id"];
            $pay_id = $uval['pi_id'];
            $wavier_days = $pval["wavier_days"];
            $paid_date = date("Y-m-d",strtotime($pval["paid_on"]));

        if($_POST['pay_id'] != "")
            $interest_details = PayInterest::getInterestDetailsByPayid($pledge_id,$_POST['pay_id'],$wavier_days,$paid_date,  $type, $module_name );
        else
            $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id,$paid_date,  $type, $module_name);

        //if($_POST['pay_id'] == ""){
        $checking = unitDisplay($interest_details["interest_amount"],false)." != ".$pval['interest_amount'];

        if(unitDisplay($interest_details["interest_amount"],false) != $pval['interest_amount']){

            $missing_labels["paid_on_reset"] = PAID_ON_RESET_ALERT;
            $missing_labels["paid_on"] = "";

        }

        if($interest_details["last_pay_date"] > $paid_date || $interest_details["loan_date"] > $paid_date){
            $missing_labels["paid_on"] = PAID_DATE_INVALID_ALERT;
        }


        if($interest_details["default_wavier_days"] > $interest_details["no_of_days"]){
            $missing_labels["wavier_days"] = WAVIER_DAYS_INVALID_ALERT;
        }

       // }



            if(isset($_POST['principle_remain']) ){

                //echo $_POST['principle_remain']." ---  ".$pval["principle_amount"];
                $princ_remain = floatval($_POST['principle_remain']);
                $princ_amt = floatval($pval['principle_amount']);

                if($princ_remain <= 0)
                {
                    //echo "All Principle Amount Paid, Customer can Redeam Theire Product".'~~#principle_closed,#principle_amount,#principle_amt~~0';
                    //exit;
                    $missing_labels['principle_amount'] = "All Principle Amount Paid, Customer can Redeam Their Product";

                }

                if($princ_remain < $princ_amt)
                {
                    //echo "Principle Amount Exceed the limits".'~~#principle_amount,#principle_amt~~0';
                    //exit;
                    $missing_labels['principle_amount'] = "Principle Amount Exceed the limits of Rs. " . unitDisplay($_POST['principle_remain'],false) ;
                }


                if(($princ_remain-$princ_amt) <= 0)
                {
                    //echo "Principle Amount Exceed the limits".'~~#principle_amount,#principle_amt~~0';
                    //exit;
                    unset($missing_labels);
                    $missing_labels['principle_amount'] = "Pay intrerest can not be process.<br> Remaining Principle amount : <b><i> Rs. ".unitDisplay($princ_remain,false)." </i></b><br> Your paying principle amount : <b><i> Rs. ".unitDisplay($princ_amt,false)." </i></b><br> Principle amount exceed the limits of remain amounts to pay.<br> <b><i>You can directly redeem your product by paying this remaining principle</i></b>.";
                }


            }

            // Principle Amount Validation for the flat
            $sc_type = Scheme::getSchemeType($pval['loan_id']);
            //echo $sc_type;
            $prince = $_POST['pay_principle'];
            if( $sc_type == 'flat'){ //pay_principle

                $min_prince = 100; //floatval(Productpledge::getLoanAmount($pval['loan_id']))/12;
                //echo $min_prince;
                if((floatval($pval['principle_amount']) < intval($min_prince)) && $prince == '1' )
                {

                    //echo "Principle amount should not below the limit of " . convertToCurrency($min_prince)  . "~~#principle_amount~~0";
                    //exit;
                    $missing_labels['principle_amount'] = "Principle amount should not below the limit of Rs. " . unitDisplay($min_prince,false) ;

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
              //ob_end_clean();
              echo "
               {
                \"page\" : \"".$page."\",
                \"success\" : \"".$success."\",
                \"disp\" : \"".$disp."\",
                \"checking\" : \"".$checking." - for date ".$paid_date."\",
                \"content\" : \"".PAYINTEREST_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
            //echo '~~~~1';
            exit;
            break;

   case 'save':

            if(isset($pval['id'])) unset($pval['id']);

            $pval['branch_id'] = $_SESSION['session_user_branch_id'];
            $pval['customer_id'] = ProductPledge::getCustomerId($pval['loan_id']);

            $app->access_table = PRODUCT_INTERESTS;

            //Commented temporerily for testing purpose
            //$pval['paid_on'] = date('Y-m-d');

            //Enable temporerily for testing purpose
            $paid_on = date("Y-m-d H:i:s", strtotime($pval['paid_on'].date(" H:i:s")));



            $pay_interest_details = PayInterest::getInterestDetails($pval['loan_id'], $pval['wavier_days'],'',$paid_on);
            $pval['paid_on'] = $pay_interest_details["paid_on"];
						$new_loan_id = $pay_interest_details["loan_id"];
            $pval['pay_interest_details'] = serialize($pay_interest_details);
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $pval['status'] = 1;
            $status = $app->add($pval);
            $uval['id'] = $app->last_inserted_id();


            //$uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['total_paid_amount'],$cash_purpose."-(Loan ID : ".$new_loan_id.")",$paid_on, 0, 3);

            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['interest_amount'],CASHFLOW_PURPOSE_INTEREST,$paid_on, 0, 3);

            if((int)$pval['additional_charge'] > 0){
                $uval['acid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['additional_charge'],CASHFLOW_PURPOSE_ADDITIONAL,$paid_on, $uval['cid'] , 3);
            }
            //pval[principle_amount]

            if((int)$pval['principle_amount'] > 0){
                $uval['pcid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['principle_amount'],CASHFLOW_PURPOSE_PRINCIPLE,$paid_on, $uval['cid'] , 9);
            }


            $app->access_table = PRODUCT_INTERESTS;
            $app->save(array(
                    "cash_flow_id"=>$uval['cid']
                    /*"modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']*/
                ), array('id = ' => $uval['id']));


            $isMsgDisplay = true;
            $args = "save";

              //ob_end_clean();
              echo "
               {
                \"page\" : \"".$page."\",
                \"status\" : \"".$status."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".PAYINTEREST_SUCCESS."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : \"".$args."\"
               }";

            exit;


   break;
   case 'update':


            //Enable temporerily for testing purpose
             $paid_on = date("Y-m-d H:i:s", strtotime($pval['paid_on'].date(" H:i:s")));

            //$pay_interest_details = PayInterest::getInterestDetailsByPayid($customer_data['pledge_id'],$customer_data['pi_id']);

            $pval['branch_id'] = $_SESSION['session_user_branch_id'];
            $pval['customer_id'] = ProductPledge::getCustomerId($pval['loan_id']);

            $pay_interest_details = PayInterest::getInterestDetailsByPayid($pval['loan_id'],$pval['id'], $pval['wavier_days']);

            $pval['paid_on'] = $pay_interest_details["paid_on"];
            $pval['pay_interest_details'] = serialize($pay_interest_details);
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $app->save($pval, array('id = ' => $pval['id']));

            //get Cash Flow ID
            $cash_detail = $app->find(
                            array("cash_flow_id"),
                            array(
                                "conditions"=>array("id = "=>$pval['id'])
                             ));

            //Update current flow id amount and reset Cash flow records after this flow id
            $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_flow_id"]);
            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['total_paid_amount'],$cash_purpose,$paid_on, 0, 3);

            $app->access_table = PRODUCT_INTERESTS;
            $app->save(array(
                    "cash_flow_id"=>$uval['cid'],
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
                ), array('id = ' => $pval['id']));


            $isMsgDisplay = true;
            $args = "update";

              //ob_end_clean();
              echo "
               {
                \"page\" : \"".$page."\",
                \"status\" : \"".$status."edited\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".PAYINTEREST_UPDATE_SUCCESS."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : \"".$args."\"
               }";
   break;

   case 'delete':

        $status = PayInterest::Delete($pval['id']);


       /* //get Cash Flow ID
        $cash_detail = $app->find(
                        array("cash_flow_id"),
                        array(
                            "conditions"=>array("id = "=>$pval['id'])
                         ));

        //Delete current flow id and reset Cash flow records after this flow id
        $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_flow_id"]);

        $app->access_table = PRODUCT_INTERESTS;
        $status = $app->delete($pval, array('id' => $pval['id']));*/

        $isMsgDisplay = true;
        $args = "delete";

          //ob_end_clean();
          echo "
           {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"".PAYINTEREST_DELETED."\",
            \"isAlert\" : \"".$isMsgDisplay."\" ,
            \"args\" : \"".$args."\"
            }";

   break;


   case 'add':
   case 'pay':
        /*  Get the Customer details, if type is edit */
        $customer_data = PayInterest::Detail($uval['id'],$uval['pi_id']);
        $product_data = $customer_data;
        $customer_data = $customer_data[0];
        // print_r($customer_data);
        include ADMIN_LAYOUT_PATH . 'forms/pay_interest.html';
        exit;

        break;

   case 'edit':
        /* * Get the Customer details, if type is edit*/
        $customer_data = PayInterest::Detail($uval['id'],$uval['pi_id']);
        $product_data = $customer_data;
        $customer_data = $customer_data[0];
        //print_r($customer_data );
        include ADMIN_LAYOUT_PATH . 'forms/pay_interest.html';
        exit;

        break;

   case 'delete-confirm':
        /* * Get the Customer details, if type is edit*/

        $customer_data = PayInterest::Detail($uval['id'],$uval['pi_id']);
        //print_r($product_data); exit;
        $product_data = $customer_data;
        $customer_data = $customer_data[0];

        include ADMIN_LAYOUT_PATH . 'elements/delete_pay_interest.html';
        exit;
        break;


   case 'paging':
   case 'back':
   case 'refresh':
   case 'search':

         $data = PayInterest::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         $count = PayInterest::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         $category_data = ProductCategory::getProductCategoryOptData();
         include ADMIN_LAYOUT_PATH . 'pay_interest.html';
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
        $data = PayInterest::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        $count = PayInterest::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        include ADMIN_LAYOUT_PATH . 'pay_interest.html';
     break;

    default:
        /*
         * Make the pages with header and footer
         */
        // $data = PayInterest::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        // $count = PayInterest::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        // $category_data = ProductCategory::getProductCategoryOptData();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        if($uval['pledge_id']!=''){
            $data = PayInterest::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
            $count = PayInterest::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
            $category_data = ProductCategory::getProductCategoryOptData();
            $product_data = PayInterest::ProductDetail($uval['pledge_id']);
            //$product_data = $product_data[0];
            //echo $uval['pledge_id'];
            //print_r(array_keys($product_data));
            include ADMIN_LAYOUT_PATH . 'forms/pay_interest.html';
        }else{
            include ADMIN_LAYOUT_PATH . 'pay_interest.html';
        }
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
