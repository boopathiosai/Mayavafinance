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
$page_title = 'PayExcessAmount';
$module_name = 'pay_excess_amount';

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
$product_name=$_POST["product_name"];
$product_with=$_POST["product_with"];
$product_without =$_POST["product_without"];
$product_items =$_POST["product_items"];
$product_count = $_POST['product_count'];

 (int)$old_loan_id = $_POST['old_loan_id'];
 (int)$scheme_id=$_POST['scheme_id'];


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
$cashin_purpose=CASHFLOW_PURPOSE_REDEEMED;
$cashout_purpose=CASHFLOW_PURPOSE_PLEDGED;

$cashin= CASH_IN;
$cashout= CASH_OUT;

$cash_purpose_delete=PAY_INTEREST_DELETE;
$cash_purpose_excess_pay=PAY_EXCESS_AMOUNT_PAYMENT;

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

$app->access_table = PAY_EXCESS_AMOUNT;

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
            $missing_labels = Validator::validate_json('PayExcessAmount');

            if(floatval($_POST["customer_amount"]) <= 100 ){

                $missing_labels["customer_amount"] = "Customer Partial amount could not be less than Rs. 100";

            }

            // Date and Wavier validation

        $pledge_id = $pval["loan_id"];
        $pay_id = $uval['pay_id'];
        $wavier_days = $pval["wavier_days"];
        $paid_date = date("Y-m-d",strtotime($pval["paid_on"]));
        $cur_date = date("Y-m-d");
        
        $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id,$paid_date,  $type, $module_name);

        if(unitDisplay($interest_details["interest_amount"],false) != $pval['interest_amount']){

            //$missing_labels["paid_on_reset"] = PAID_ON_RESET_ALERT;
            $missing_labels["paid_on"] = PAID_ON_RESET_ALERT;

        }

        if($interest_details["last_pay_date"] > $paid_date || $interest_details["loan_date"] > $paid_date || $cur_date < $paid_date){
            $missing_labels["paid_on"] = PAID_DATE_INVALID_ALERT;
        }

       if($interest_details["default_wavier_days"] > $interest_details["no_of_days"] && $interest_details["wavier_days"]!=0 || $interest_details["wavier_days"]>5 ){
        //  if($interest_details["default_wavier_days"] < 5 && $interest_details["wavier_days"]!=0){
            $missing_labels["wavier_days"] = WAVIER_DAYS_INVALID_ALERT;     
        }

        if($interest_details["wavier_days"]>5) {
             $missing_labels["wavier_days"] = WAVIER_DAYS_MORETHAN_FIVE_DAYS_INVALID_ALERT;
        }


        $old_scheme_id = $_POST["old_scheme_id"];
        $new_scheme_id = $pval['new_scheme'];

        if($old_scheme_id != $new_scheme_id && !isset($missing_labels["new_scheme"])){

            $missing_labels["new_scheme"] = SCHEME_RESET_ALERT;


        }



            if(intval($product_count) <= 0){

                //echo 'Minimum One Product Item Require~~#category_id, #product_name, #gross_weight, #net_weight~~0';
                //exit;
                $missing_labels['category_id'] = '';
                $missing_labels['product_name'] = '';
                $missing_labels['product_items'] = '';
                $missing_labels['item_count'] = 'Product Items - Minimum one item require';

            }

            else{

                $pre_product_id = array();
                for($i=0;$i<$_POST["product_count"];$i++){

                    if(in_array($product_id[$i], $pre_product_id) && $product_id[$i] != '' && $product_id[$i] != 0){

                    $missing_labels['product_name'] = '';
                    $missing_labels['DUI_'.$i] = 'Dublicated Product found. Remove the product row '.($i+1);

                    }
                    if($category_id[$i]=="" || $product_name[$i]=="" || intval($product_items[$i]) == 0 ){ //|| $gross_weight[$i]=="" || $net_weight[$i]==""

                        //echo 'Insufficient Product Information on Row '.($i+1).'~~#category_id, #product_name, #gross_weight, #net_weight~~0';
                        //exit;
                        //$missing_labels['category_id'] = 'Category - Insufficient product information on row '.($i+1);
                        //$missing_labels['product_name'] = 'Product Name - Insufficient product information on row '.($i+1);
                        //$missing_labels['product_items'] = 'Product Items - Insufficient product information on row '.($i+1);
                        //$missing_labels['gross_weight'] = 'Gross Weight - Insufficient product information on row '.($i+1);
                        //$missing_labels['net_weight'] = 'Net Weight - Insufficient product information on row '.($i+1);

                        $missing_labels['category_id'] = '';
                        $missing_labels['product_name'] = '';
                        $missing_labels['product_items'] = '';
                        $missing_labels['UI_'.$i] = 'Insufficient Cateogory Product Information on row '.($i+1);

                    }

                    $pre_product_id[] = $product_id[$i];
                }

            }

            //if(!PayExcessAmount::SchemeExcessValidate($pval['new_scheme'], $pval['old_loan_id'], $pval['excess_amount'])){

            $excess_amount = floatval($_POST['customer_amount']);
            $net_weight = floatval($pp_pval['total_net_weight']);
            $new_loan_amount = floatval($pp_pval['loan_amount']);
            $old_loan_amount = floatval($_POST['old_loan_amount']);
            $loan_rate = floatval(Scheme::getSchemeLoanRate($pval['new_scheme']));
            $max_loan_amount = $loan_rate * $net_weight;
            $max_excess_amount = $max_loan_amount - $old_loan_amount;

            /*echo "<br> excess_amount = ".$excess_amount;
            echo "<br> net_weight = ".$net_weight;
            echo "<br> loan_rate = ".$loan_rate;
            echo "<br> new_loan_amount = ".$new_loan_amount;
            echo "<br> old_loan_amount = ".$old_loan_amount;
            echo "<br> max_loan_amount = ".$max_loan_amount;
            echo "<br> max_excess_amount = ".$max_excess_amount;*/

            if($max_excess_amount < $excess_amount)
            {
                $missing_labels['excess_amount_max']='Customer requested amount has exceeded to the maximum limit of the amount Rs. '.unitDisplay($max_excess_amount,false).' for the selected scheme and pledged products';
                $missing_labels['customer_amount']= "";
                
                //echo 'Excess amount exceed the limits maximum of '.convertToCurrency($max_excess_amount).' for the selected scheme and pledged products~~#excess_amount~~0';
                //exit;

            }

            //Loan Rate Validation
            $loan_rate = floatval($_POST['ld_loan_rate']);
            $loan_amount = floatval($pp_pval['loan_amount']);
            $gross_weight = floatval($pp_pval['total_gross_weight']);
            $net_weight = floatval($pp_pval['total_net_weight']);
            $max_amount = $loan_rate * $net_weight;

            if($max_amount < $loan_amount){

                $missing_labels['loan_amount'] = 'Loan amount has exceeded the maximum limit of the amount Rs. '.$max_amount;
                $missing_labels['scheme_id'] = 'Scheme - Loan amount has exceeded the maximum limit, You can change the suitable scheme to get the amount more than Rs. '.$max_amount;
                $missing_labels['net_weight'] = 'Net weight - Loan amount has exceeded the maximum limit, You can pledge more items to get the amount more than Rs. '.$max_amount;

            }

            $balance = CashFlowTransaction::getBranchBalance($_SESSION['session_user_branch_id']);


            if(floatval($balance) < $excess_amount ){

            $missing_labels['excess_amount_bal'] = 'Excess amount has exceeded the maximum limit of available branch balance of Rs. '.unitDisplay($balance,false);
            $missing_labels['customer_amount']= "";

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

              echo "
               {
                \"page\" : \"".$page."\",
                \"success\" : \"".$success."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".PAY_EXCESS_AMOUNT_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
               exit;



            break;

   case 'add':
   case 'save':
   case 'update':


            // PayExcessAmount Table Insert
            $app->access_table = PAY_EXCESS_AMOUNT;
            //Get loan id for partial amount payment
            
            $loan_id_details = PayExcessAmount::Detail($pval['loan_id']);
            
            unset($pval['loan_id']);

            //Enable temporerily for testing purpose
            $pval['paid_on'] = date("Y-m-d H:i:s", strtotime($pval['paid_on'].date(" H:i:s")));


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


                //Insert Cash Flow Data
                //$cashin_amt = $_POST["principle_remain"] + $pval['interest_amount'] + $pval['additional_charge'];
                //$cashout_amt =  $pp_pval['loan_amount'] - $pval['service_charge'];

                //$uval['cid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_amt,$cashin_purpose."-(Loan ID : ".$loan_id_details[0]['loan_id'].")", $pval['paid_on']);
                //$uval['cod'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashout,$cashout_amt,$cashout_purpose."-(Loan ID : ".$pval['new_loan_id'].")", $pval['paid_on']);

                $cashin_amt = $_POST["principle_remain"]; 
                $cashin_iamt = $pval['interest_amount'];
                $cashin_aamt = $pval['additional_charge'];
                
                $cashout_amt =  $pp_pval['loan_amount'];
                $cashin_samt = $pval['service_charge'];


                $uval['cid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_amt,CASHFLOW_PURPOSE_REDEEMED, $pval['paid_on'],0,2);
				$uval['icid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_iamt,CASHFLOW_PURPOSE_INTEREST, $pval['paid_on'],$uval['cid'],3);
                $uval['acid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_aamt,CASHFLOW_PURPOSE_ADDITIONAL, $pval['paid_on'],$uval['icid'],8);
                $uval['cod'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashout,$cashout_amt,CASHFLOW_PURPOSE_PLEDGED, $pval['paid_on'],0,1);
                $uval['scid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_samt,CASHFLOW_PURPOSE_SERVICE, $pval['paid_on'],$uval['cod'],7);

                //Add Pay Interest Details for the loan id
                $pi_pval['branch_id'] = $pp_pval['branch_id'];
                $pi_pval['customer_id'] = $pp_pval['customer_id'];
                $pi_pval['cash_flow_id'] = $uval['icid'];
                $pi_pval['loan_id'] = $pval['old_loan_id'];
                $pi_pval['principle_amount'] = 0;//$pval['principle_amount'];
                $pi_pval['interest_rate'] = $pval['interest_rate'];
                $pi_pval['interest_amount'] = $pval['interest_amount'];
                $pi_pval['additional_charge'] = $pval['additional_charge'];
                $pi_pval['interest_days'] = $pval['interest_days'];
                $pi_pval['total_paid_amount'] = floatval($pval['principle_amount'])+floatval($pval['interest_amount'])+floatval($pval['additional_charge'])+floatval($pval['wavier_amount']);
                $pi_pval['wavier_days'] = $pval['wavier_days'];
                $pi_pval['wavier_reason'] = $pval['wavier_reason'];
                $pi_pval['wavier_amount'] = $pval['wavier_amount'];
                $pi_pval['paid_on'] = $pval['paid_on'];

                $pay_interest_details = PayInterest::getInterestDetails($pval['old_loan_id'], $pval['wavier_days'],'',$pval['paid_on']);

                $pi_pval['pay_interest_details'] = serialize($pay_interest_details);

                $pi_pval['created_datetime'] = date('Y-m-d H:i:s');
                $pi_pval['created_by'] = $_SESSION['session_userid'];
                $pi_pval['status'] = 1;
                /*$pi_pval['modified_datetime'] = date("Y-m-d H:i:s");
                $pi_pval['modified_by'] = $_SESSION['session_userid'];*/
                $app->access_table = PRODUCT_INTERESTS;
                
                
                
                /* Below commented, because interest added in the new loan, 
                if any change in separete pay interest, just remove below comment 
                and not add the interest amount with new loan*/

                $uval['pay_id'] = 0;
                $app->add($pi_pval);
                $uval['pay_id'] = $app->last_inserted_id();



            //PayExcessAmount Cash flow Id Relation Setting
            $app->access_table = PAY_EXCESS_AMOUNT;
            $app->save(array(
                    "pay_id"=>$uval['pay_id'],
                    "cash_in_id"=>$uval['cid'],
                    "cash_out_id"=>$uval['cod']
                    /*"modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']*/
                ), array('id = ' => $uval['id']));


            //Update and Close Old Loan Id
            $app->access_table = PLEDGE;
            $app->save(array(
                    "status"=> -1,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
                ), array('id = ' => $pval['old_loan_id']));



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
            $pr_pval['total_paid_amount'] = floatval($_POST['principle_remain'])+floatval($pval['interest_amount'])+floatval($pval['additional_charge'])+floatval($pval['wavier_amount']);
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


            //Insert New Loan
            $app->access_table = PLEDGE;
            $pp_pval['loan_id'] = $pval['new_loan_id'];

            //$pp_pval['loan_date'] = date('Y-m-d H:i:s');
            $pp_pval['loan_date'] = $pval['paid_on'];
            $pp_pval['cash_flow_id'] = $uval['cod'];
            $pp_pval['scheme_id'] = $pval['new_scheme'];
            $pp_pval['service_charge'] = $pval['service_charge'];
            $pp_pval['total_amount_given'] = floatval($pp_pval[loan_amount])-floatval($pval['service_charge']);
            $pp_pval['created_datetime'] = date('Y-m-d H:i:s');
            $pp_pval['created_by'] = $_SESSION['session_userid'];
            $pp_pval['status'] = 1;
            /*$pp_pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pp_pval['modified_by'] = $_SESSION['session_userid'];*/
            $app->add($pp_pval);
            $uval['pledge_id'] = $app->last_inserted_id();

            /******** Below Comment is for the Pledged Product Editable mode **************/
            // Pledge Detail Insertion for the new Loan Id
            $dval['pledge_id'] = $uval['pledge_id'];
            $total_weight = 0;
            $total =$product_count;
            $dval['created_datetime'] = date('Y-m-d H:i:s');
            $dval['created_by'] = $_SESSION['session_userid'];
            $ordering = 1;
            $app->access_table = PLEDGE_DETAILS;

            for($i=0;$i<$total;$i++)
            {
                if($product_id[$i]==""){
                    // New Product Insertion
                    $pdval['category_id'] = $category_id[$i];
                    $pdval['name'] = $product_name[$i];
                    $pdval['product_with'] = $product_with[$i];
                    $pdval['created_datetime'] = date('Y-m-d H:i:s');
                    $pdval['created_by'] = $_SESSION['session_userid'];
                    /*$pdval['modified_datetime'] = date('Y-m-d H:i:s');
                    $pdval['modified_by'] = $_SESSION['session_userid'];*/
                    $pdval['status'] = 1;
                    //echo("pdval = "); print_r($dval);echo("<hr>");
                    $app->access_table = PRODUCTS;
                    $app->add($pdval);

                    $dval['product_id'] = $app->last_inserted_id();
                }
                else{
                    $dval['product_id'] =$product_id[$i];
                }

                //Insert Product detail for the Pledge New Iserted Id
                $app->access_table = PLEDGE_DETAILS;

                $dval['category_id'] = $category_id[$i];
                //$dval['gross_weight'] = $gross_weight[$i];
                //$dval['net_weight'] = $net_weight[$i];
                //$dval['market_rate'] = $market_rate[$i];
                $dval['product_items'] = $product_items[$i];
                $dval['ordering'] = $ordering;
                $dval['status'] = 1;
                $total_weight += $net_weight[$i];
                $app->add($dval);
                $ordering++;
            }



          $_SESSION['success_msg'] = PAYEXCESS_SAVE_SUCCESS;

               echo "
           {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"".PAYEXCESS_SAVE_SUCCESS."\",
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
            }";
            exit;


   break;



   case 'delete':

        $status = $app->delete($pval, array('id' => $pval['id']));

        $status = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$_POST['total_amt'],$cash_purpose_delete);

        $isMsgDisplay = true;
        $args = "delete";

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
   case 'renew':
   case 'create':
   case 'edit':
        /*
         * Get the Customer details, if type is edit
         */
         //echo $uval['id'];




        $product_details = PayExcessAmount::Detail($uval['id']);
        $product_data = $product_details[0];
        //print_r($product_data);exit;
        include ADMIN_LAYOUT_PATH . 'forms/pay_excess_amount.html';

        break;

   case 'paging':
   case 'back':
   case 'search':
   case 'refresh':

         $data = PayExcessAmount::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         $count = PayExcessAmount::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
         $category_data = ProductCategory::getProductCategoryOptData();
         include ADMIN_LAYOUT_PATH . 'pay_excess_amount.html';
         #include ADMIN_LAYOUT_PATH . 'customer.html';
         exit;
         break;

   case 'delete':
        /*
         * Delete from database
         */
        $app->delete($uval);
        $data = PayExcessAmount::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        $count = PayExcessAmount::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);

        $_SESSION['success_msg'] = PAY_EXCESS_AMOUNT_DELETE;
        include ADMIN_LAYOUT_PATH . 'pay_excess_amount.html';
        #include ADMIN_LAYOUT_PATH . 'customer.html';
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
         $loan_details = ProductPledge::Detail_PayExcessAmount($old_loan_id);
         echo $loan_details[0]['loan_amount']."~~".$loan_details[0]['pro_id']."~~".$loan_details[0]['description']."~~".$loan_details[0]['scheme_name'];
        exit;
        break;
     case 'fetch_scheme_details':
        /*
         * Fetch schama description and interst type
         */
                $scheme_details = Scheme::Detail($scheme_id);
              echo $scheme_details[0]['description']."~~".$scheme_details[0]['interest_type']."~~".$scheme_details[0]['service_charge']."~~".$scheme_details[0]['loan_rate'];
        exit;
        break;
    default:
    case 'fetch_customer_details':
        /*
         * Fetch Customer Details
         */
            $customer_details = Customer::Detail($customer_id);

            if($customer_details){
            echo $customer_details[0]['photo']."~~".$customer_details[0]['name']."~~".$customer_details[0]['street']."~~";
            echo $customer_details[0]['location']."~~".$customer_details[0]['postal_code']."~~".$customer_details[0]['contact_no'];
            }
            else{
            echo "0";
            }
      exit;
      break;
     case 'category_dropdown':
        /*
         * Fetch Customer Details
         */
    //  echo "yes";
        //  $id_cate=$_GET['id'];
        //  $category_data_js = get_dropdown_list_with_field('id', 'category', CATEGORIES);
        //  echo $category_options_js = build_select_options($category_data_js);
        //$category_options .= " <optgroup label='With Stone'>";
        //$category_data = get_dropdown_with_optgroup_for_pledge('id', 'category','1', CATEGORIES);
        //$category_options .= build_select_options($category_data,$product_data['category_id']);
        //$category_options .= " <optgroup label='Without Stone'>";
        $category_data = get_dropdown_list_with_field('id', 'category', CATEGORIES);
        $category_options .= build_select_options($category_data,$product_data['category_id']);
        echo $category_options;
      exit;
      break;  
    default:
        /*
         * Make the pages with header and footer
         */
        //$data = PayExcessAmount::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$count = PayExcessAmount::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$category_data = ProductCategory::getProductCategoryOptData();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        if($uval['pledge_id']!=''){
            $product_data = PayExcessAmount::ProductDetail($uval['pledge_id']);
            //$product_data = $product_data[0];
            echo $uval['pledge_id'];
            print_r(array_keys($product_data));
            include ADMIN_LAYOUT_PATH . 'forms/pay_excess_amount.html';
        }else{
            include ADMIN_LAYOUT_PATH . 'pay_excess_amount.html';
        }
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
