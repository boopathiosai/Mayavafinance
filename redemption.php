<?php session_start();

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

define ('ROOT_PATH', '');
$page_title = 'Redemption';
$module_name = 'redemption';

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
 // Get Loan date - DSSK //       
         $date_edit = Application::DateEditoption();
         $crr_loan_date = LoanDate::GetloanDate();
 // Get Loan date - DSSK //
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

$pval = $_POST['pval'];
$random_no =$_POST["random_no"];
$random_sc = $_POST['random_sc'];
//$p_pval = $_POST['p_pval'];
//$pd_pval = $_POST['pd_pval'];
$pp_pval = $_POST['pp_pval'];

$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

$customer_id =(isset($_GET['customer_id'])) ? $_GET['customer_id'] : $_POST['customer_id'];
$uval['pledge_id'] = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];
$uval['renew_id'] = (isset($_GET['renew_id'])) ? $_GET['renew_id'] : $_POST['renew_id'];

//constant message for cash flow transaction
$cash_purpose= CASHFLOW_PURPOSE_REDEEMED;
$cashin= CASH_IN;
$cashout= CASH_OUT;
$cash_purpose_delete=PAY_INTEREST_DELETE;

/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];
$search_key_location = (isset($_GET['search_key_location'])) ? $_GET['search_key_location'] : $_POST['search_key_location'];
$search_fhw = (isset($_GET['search_fhw'])) ? $_GET['search_fhw'] : $_POST['search_fhw'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_loan_id = (isset($_GET['search_loan_id'])) ? $_GET['search_loan_id'] : $_POST['search_loan_id'];
$search_contact_no = (isset($_GET['search_mobile_no'])) ? $_GET['search_mobile_no'] : $_POST['search_mobile_no'];

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
if($search_contact_no != ""){
    $search_args.="search_contact_no=".$search_contact_no."&";
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

$app->access_table = REDEMPTION;

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
            /*$missing_vals = Validator::validate_data('Redemption');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Redemption::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1';
            exit; */

            $missing_labels = Validator::validate_json('Redemption');

            // Date and Wavier validation

        $pledge_id = $pval["loan_id"];
        $pay_id = $uval['pay_id'];
        $wavier_days = $pval["wavier_days"];
        $paid_date = date("Y-m-d",strtotime($pval["paid_on"]));


        $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id,$paid_date,  $type, $module_name);
 // Cahnged - refresh alert remove - 11_OCT_2017 //
        if( $_SESSION['session_user_edit_status'] == 1){
        if(unitDisplay($interest_details["interest_amount"],false) != $pval['interest_amount']){

            $missing_labels["paid_on_reset"] = PAID_ON_RESET_ALERT;
            $missing_labels["paid_on"] = "";

        }

        /*
        existing code from 27-Jun-16
        if($interest_details["last_pay_date"] > $paid_date || $interest_details["loan_date"] > $paid_date)

        */

        if($interest_details["loan_date"] > $paid_date){
            $missing_labels["paid_on"] = PAID_DATE_INVALID_ALERT;
        }
        }
       // \.Cahnged - refresh alert remove - 11_OCT_2017 //
        if($interest_details["default_wavier_days"] > $interest_details["no_of_days"] && $interest_details["wavier_days"]!=0){
            $missing_labels["wavier_days"] = WAVIER_DAYS_INVALID_ALERT;
        }
        $customer_mobile = Customer::getCustomerMobile($pp_pval['customer_id']);
      
        $contactFirstLetter = substr($customer_mobile, 0, 1);           
        if((strlen($customer_mobile) != 10 ) || ($customer_mobile == '') || ($contactFirstLetter == 0)|| ($contactFirstLetter == 1 ) || ($contactFirstLetter == 2 ) ||  ($contactFirstLetter == 3 ) || ($contactFirstLetter == 4 ) || ($contactFirstLetter == 5 )  ) {                
            $missing_labels["wavier_days"] = PRODUT_PLEGDE_CUSTOMER_MOBILE_ERROR;
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
                \"content\" : \"".REDEMPTION_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
               exit;
            break;


    case 'save':
    case 'update':
        //Enable temporerily for testing purpose
        $pval['paid_on'] = date("Y-m-d H:i:s", strtotime($pval['paid_on'].date(" H:i:s")));
        // Redemption Table Insert
        $pval['branch_id'] = $_SESSION['session_user_branch_id'];
        $pval['customer_id'] = ProductPledge::getCustomerId($pval['loan_id']);
            //Image capture process for sub customers
        if(strstr($pval['photo'],$_SERVER['SERVER_NAME'])){
            $file_fld=explode($_SERVER['SERVER_NAME']."/",$pval['photo']);
            $photo_name=$file_fld[1];
        } else{
            $photo_name=$pval['photo'];
        }
        // Code Modified By Moses C Rathina Kumar on 29/Sep/2011
        if(strstr($pval['sign'],$_SERVER['SERVER_NAME'])){
            $file_fld=explode($_SERVER['SERVER_NAME']."/",$pval['sign']);
            $sign_name=$file_fld[1];
        } else {
            $sign_name=$pval['sign'];
        }
        unset($pval['photo']);
        unset($pval['sign']);
        $app->access_table = REDEMPTION;
        unset($pval['old_loan_id']);
        $loan_details = Redemption::Detail($pval['loan_id']);
        $redemption_count           = "SELECT id FROM ".REDEMPTION."  WHERE loan_id = ".$pval['loan_id']." ";
        $redemption_data            = $app->getrow($redemption_count);
        $redemption_count           = count($redemption_data);
        $pval['loan_details']       = serialize($loan_details);
        $pval['created_datetime']   = date('Y-m-d H:i:s');
        $pval['created_by']         = $_SESSION['session_userid'];
        $pval['status']             = 1;
        $pval['random_no']          = $random_no;
        $pval['random_sc']          = $random_sc;
        if($redemption_count == 0) {
            $app->add($pval);
            $redeem_id = $app->last_inserted_id();
            $uval['id'] = $app->last_inserted_id();
            $query = "UPDATE ".SETTINGS." SET config_value = config_value+1 WHERE config_name='loan_count'";
            $app->execute($query);
            $cashin_amt = $_POST["principle_remain"];
            $cashin_iamt = $pval['interest_amount'];
            $cashin_aamt = $pval['additional_charge'];
            $uval['cid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_amt,CASHFLOW_PURPOSE_REDEEMED, $pval['paid_on'],0,2);
            $uval['icid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_iamt,CASHFLOW_PURPOSE_INTEREST, $pval['paid_on'], $uval['cid'],3);
            $uval['acid'] = CashFlowTransaction::insertCashFlow($pp_pval['branch_id'],$cashin,$cashin_aamt,CASHFLOW_PURPOSE_ADDITIONAL, $pval['paid_on'],  $uval['icid'],8);


        //Add Pay Interest Details for the loan id
            $pi_pval['branch_id'] = $pp_pval['branch_id'];
            $pi_pval['customer_id'] = $pval['customer_id'];
            $pi_pval['loan_id'] = $pval['loan_id'];
            $pi_pval['cash_flow_id'] = $uval['icid'];
            $pi_pval['principle_amount'] = 0;
            $pi_pval['interest_rate'] = $pval['interest_rate'];
            $pi_pval['interest_amount'] = $pval['interest_amount'];
            $pi_pval['additional_charge'] = $pval['additional_charge'];
            $pi_pval['interest_days'] = $pval['interest_days'];
            $pi_pval['total_paid_amount'] = floatval($pi_pval['principle_amount'])+floatval($pval['interest_amount'])+floatval($pval['additional_charge'])+floatval($pval['wavier_amount']);
            $pi_pval['wavier_days'] = $pval['wavier_days'];
            $pi_pval['wavier_reason'] = $pval['wavier_reason'];
            $pi_pval['wavier_amount'] = $pval['wavier_amount'];
            $pi_pval['paid_on'] = $pval['paid_on'];


            $pay_interest_details = PayInterest::getInterestDetails($pval['loan_id'], $pval['wavier_days'],'',$pval['paid_on']);
            $pi_pval['pay_interest_details'] = serialize($pay_interest_details);
            $pay_interest_details_arr = serialize($pay_interest_details);
            $pi_pval['created_datetime'] = date('Y-m-d H:i:s');
            $pi_pval['created_by'] = $_SESSION['session_userid'];
            $pi_pval['status'] = 1;
            $pi_pval['sub_branch_id'] = $pp_pval['sub_branch_id']; // added 28_MAR_2019
            /*$pi_pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pi_pval['modified_by'] = $_SESSION['session_userid'];*/
            $app->access_table = PRODUCT_INTERESTS;
            $app->add($pi_pval);
            $uval['pay_id'] = $app->last_inserted_id();
            //Redemption Cash flow Id Relation Setting
            $app->access_table = REDEMPTION;
            $app->save(array(
                    "pay_id"=>$uval['pay_id'],
                    "cash_flow_id"=>$uval['cid'],
                    "sub_branch_id" => $pp_pval['sub_branch_id'] // Added 25_DEC_2017 by DSSK
                    /*"modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']*/
                ), array('id = ' => $uval['id']));
            //Update Close Old Loan Id
            $app->access_table = PLEDGE;
            $app->save(array(
                    "status"=> 0,
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
                ), array('id = ' => $pval['loan_id']));

            if($type == 'update') {
                $app->access_table = SUB_CUSTOMERS;
                $sval['created_date'] = date('Y-m-d H:i:s');
                $sval['created_by'] = $_SESSION['session_userid'];
                $sval['loan_id'] = $pval['loan_id'];
                $app->add($sval);
                $uval['id'] = $app->last_inserted_id();

                if(file_exists(CUSTOMER_IMAGE_PATH.$pval['customer_id'])) {
                    if(!file_exists(CUSTOMER_IMAGE_PATH.$pval['customer_id'].'/sub')) {
                        mkdir(CUSTOMER_IMAGE_PATH.$pval['customer_id'].'/'.'sub',0777,true);
                        chmod(CUSTOMER_IMAGE_PATH.$pval['customer_id'].'/'.'sub',0777);
                    }
            // rename the picture using user-id
                    @rename($photo_name, CUSTOMER_IMAGE_PATH.$pval['customer_id'].'/sub/'.$uval['id'].'.jpg');
                    @rename($sign_name, CUSTOMER_IMAGE_PATH.$pval['customer_id'].'/sub/'.$uval['id'].'_sign.jpg');

                    //update the database with the new details
                    if($photo_name != '') {
                            $sval['photo'] = CUSTOMER_IMAGE_PATH.$pval['customer_id'].'/sub/'.$uval['id'].'.jpg';
                    }
                    if($sign_name != '') {
                            $sval['sign'] = CUSTOMER_IMAGE_PATH.$pval['customer_id'].'/sub/'.$uval['id'].'_sign.jpg'; // Edit By Moses
                    }
                    $sval['customer_id'] = $pval['customer_id'];
                    $sval['process_id'] = 'Redem_'.$uval['pay_id'];
                    $status = $app->save($sval, array('id = ' => $uval['id']));
                    //$success_msg = CUSTOMER_SAVED;
                }
            }
            $_SESSION['success_msg'] = REDEEM_SUCCESS;
            $isMsgDisplay = false;
            $args = "save";

            $custmr_id = $pval['customer_id'];
            $app->access_table = CUSTOMERS;
            $find_customer = $app->find(
                    array("contact_no","branch_id"), array(
                "conditions" => array("id = " => $custmr_id),
            ));
            $pleadge_id = $redeem_id;
            $app->access_table = REDEMPTION;
            $find_branch_and_loan_id = $app->find(
                    array("loan_id","branch_id"), array(
                "conditions" => array("id = " => $pleadge_id),
            ));
            $branch_id = $find_branch_and_loan_id[0]["branch_id"];
            $red_loan_id = $find_branch_and_loan_id[0]["loan_id"];
            $app->access_table = PLEDGE;
            $find_sub_branchs = $app->find(
                    array("sub_branch_id"), array(
                "conditions" => array("id = " => $red_loan_id),
            ));
            $sub_branch_id = $find_sub_branchs[0]["sub_branch_id"];
        // Sub branch
            $app->access_table = SUB_BRANCH;
            $find_branch_name = $app->find(
                    array("name"), array(
                "conditions" => array("id = " => $sub_branch_id),
            ));
            $sub_branch_name = $find_branch_name[0]["name"];
            $app->access_table = BRANCH;
            $find_branch_name = $app->find(
                    array("name"), array(
                "conditions" => array("id = " => $branch_id),
            ));
            $branch_name = $find_branch_name[0]["name"];

            $app->access_table = SMS_SUPPORT;
            $find_loan_tamil = $app->find(
                    array("tamil_name"), array(
                "conditions" => array("name = " => "Loan_amount"),
            ));
            $loan_tamil_name = $find_loan_tamil[0]["tamil_name"];
            $app->access_table = SMS_SUPPORT;
            $find_loan_tamil = $app->find(
                    array("tamil_name"), array(
                "conditions" => array("name = " => "Interest"),
            ));
            $interest_tamil_name = $find_loan_tamil[0]["tamil_name"];

            // SMS contant changes 23_MAY_2018 by DSSK//

            $app->access_table = SMS_SUPPORT;
            $find_loan_tamil = $app->find(
                    array("tamil_name"), array(
                "conditions" => array("name = " => "nagai_adagu_vibaram"),
            ));
            $nagai_adagu_vibaram_tamil_name = $find_loan_tamil[0]["tamil_name"];

            $app->access_table = SMS_SUPPORT;
            $find_loan_tamil = $app->find(
                    array("tamil_name"), array(
                "conditions" => array("name = " => "nagai_thirupiya_vibaram"),
            ));
            $nagai_thirupiya_vibaram_tamil_name = $find_loan_tamil[0]["tamil_name"];

            $app->access_table = SMS_SUPPORT;
            $find_loan_tamil = $app->find(
                    array("tamil_name"), array(
                "conditions" => array("name = " => "Asal"),
            ));
            $Asal_tamil_name = $find_loan_tamil[0]["tamil_name"];

            $app->access_table = SMS_SUPPORT;
            $find_loan_tamil = $app->find(
                    array("tamil_name"), array(
                "conditions" => array("name = " => "a_ean"),
            ));
            $a_ean_tamil_name = $find_loan_tamil[0]["tamil_name"];

            $app->access_table = SMS_SUPPORT;
            $find_loan_tamil = $app->find(
                    array("tamil_name"), array(
                "conditions" => array("name = " => "scheme"),
            ));
            $scheme_tamil_name = $find_loan_tamil[0]["tamil_name"]; 

            $redemption_branch = $branch_name." ".$sub_branch_name;
            $lon_date = date("d-M-Y", strtotime($pay_interest_details['paid_date']));
            $lon_id = $pay_interest_details['loan_id'];
            $lon_amnt = $pay_interest_details['loan_amount'];
            $lon_intr_amnt = $pay_interest_details['interest_amount'];
            $sms_contant_text  = 'நகை திருப்பிய விவரம் அ.எண்:'.$lon_id.' அசல்:'.$lon_amnt.' வட்டி:'.$lon_intr_amnt.'-GOLDLOAN';
            $send_no = $find_customer[0]["contact_no"];
            sms::sendData($sms_contant_text,$send_no,'1707161737194835530');
            echo "
            {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"".REDEEM_SUCCESS."\",
            \"isAlert\" : \"".$isMsgDisplay."\" ,
            \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
            }";            
        } else {
            $app->save(array(
                "loan_details"      => serialize($loan_details),
                "modified_datetime" => date('Y-m-d H:i:s'),
                "created_by"        => $_SESSION['session_userid'],                    
                "status"            => 1
            ), array('id = ' => $redemption_data['id']));
        }
        exit;   
   break;

   // case 'delete':

   //      $status = $app->delete($pval, array('id' => $pval['id']));

   //      $status = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$_POST['total_amt'],$cash_purpose_delete);

   //      $isMsgDisplay = true;
   //      $args = "delete";

   //        echo "
   //         {
   //          \"page\" : \"".$page."\",
   //          \"status\" : \"".$status."\",
   //          \"disp\" : \"".$disp."\",
   //          \"content\" : \"".PAYINTEREST_DELETED."\",
   //          \"isAlert\" : \"".$isMsgDisplay."\" ,
   //          \"args\" : \"".$args."\"
   //          }";
   // break;

   case 'delete':
        /*
         * Delete from database

         /for single delete/
         */
         //echo $uval[id];exit;
          if($uval[id]!="")
            {
            //$name = ProductPledge::getLoanCode($uval['id']);
               $pledge_id= $uval[id];
              //exit;
               $app->access_table = PRODUCT_INTERESTS;
               $status = $app->delete(array("loan_id"=>$pledge_id));


                $query = "UPDATE ".PLEDGE." SET status = 0 , redeem_status = 1 WHERE id=".$pledge_id."";
                $app->execute($query);

                ///--------------///

                $cash_flow_query = "SELECT  cash_flow_id FROM ".REDEMPTION."  WHERE loan_id = ".$pledge_id."";
                 $data = $app->getrows($cash_flow_query);

                $cash_flow_id = $data[0][cash_flow_id];
//               $app->access_table = CASH_FLOW_TRANSACTIONS;
//               $cash_flow_delete = $app->delete(array("id"=>$cash_flow_id));
                $cash_flow_delete = CashFlowTransaction::DeleteRedemption($cash_flow_id);
               if($cash_flow_delete){
                    $app->access_table = REDEMPTION;
                    $redem_delete = $app->delete(array("loan_id"=>$pledge_id));
               }
               /// -----END-----  ///
               ///////////////////////////////////////////////////

               /* $sql_query="select * from ".REDEMPTION." where loan_id=".$pledge_id." AND redeem_status=0 ";
                $check_redeem_delete=$app->getrows($sql_query);
                //echo $check_redeem_delete[0][4];exit;
                echo "<pre>";print_r($check_redeem_delete);exit;
                if($check_redeem_delete[0][4]!='')
                {
                    $redeem_cash_flow_id[]=$check_redeem_delete[0][4];
                    $redeem_cash_flow_id[]=$check_redeem_delete[0][4]+1;
                    $redeem_cash_flow_id[]=$check_redeem_delete[0][4]+2;

                }
               if(is_array($redeem_cash_flow_id))
               {
                    //echo "<pre>";print_r($redeem_cash_flow_id);exit;
                   foreach($redeem_cash_flow_id as $key => $ids_del)
                   {
                        $query = "DELETE FROM ".CASH_FLOW_TRANSACTIONS." WHERE id = ".$ids_del."";
                       //echo $query;exit;
                        $app->execute($query);
                   }
                    $query1 = "UPDATE ".REDEMPTION." SET status = 0 , redeem_status = 1 WHERE loan_id=".$pledge_id."";
                   // echo $query1;exit;
                    $app->execute($query1);
               }*/

              //////////////////////////////////////////////
            }


         //for multiple delete//


          $app->access_table = PRODUCT_INTERESTS;
          if(is_array($_POST["list"]))
            {
               foreach($_POST["list"] as $key => $val)
                {

                    $status = $app->delete(array("loan_id"=>$val));

//                        $query = "UPDATE ".PLEDGE." SET status = 0 , redeem_status = 1 WHERE id=".$pledge_id.""; // Dssk changes 11_SEP_2017
                        $query = "UPDATE ".PLEDGE." SET status = 0 , redeem_status = 1 WHERE id=".$val."";
                        $app->execute($query);
                        // $query = "UPDATE ".REDEMPTION." SET redeem_status = 1 WHERE id=".$pledge_id."";
                        // $app->execute($query);

                }

            }


        // $data = Redemption::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        // $count = Redemption::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        // echo "here";exit;
         $_SESSION['success_msg'] = REDEEM_SUCCESS_DELETE;
            $isMsgDisplay = false;
            $args = "save";
              echo "
               {
                \"page\" : \"".$page."\",
                \"status\" : \"".$status."\",
                \"disp\" : \"".$disp."\",
                \"content\" : \"".REDEEM_SUCCESS_DELETE."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";

            exit;
        break;




   case 'add':
   case 'renew':
   case 'create':
   case 'edit':
        /*
         * Get the Customer details, if type is edit
         */
         //echo $uval['id'];
        $product_details = Redemption::Detail($uval['id']);
        $product_data = $product_details[0];
        //print_r($product_data);exit;
        include ADMIN_LAYOUT_PATH . 'forms/redemption.html';

        break;

   case 'paging':
   case 'back':
   case 'refresh':
   case 'search':

         $data = Redemption::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id,$search_contact_no);
       
//       LED display function   
            $count_data = count($data);
            $branch_code = $_SESSION['session_user_branch_code'];
            $branch_id = $_SESSION['session_user_branch_id'];         
            if($type == 'paging' || $type == "back" || $type == "refresh"){

                $ttl_redeem_amnt = "";
                $search_loan = "";
                Redemption::ledDataUpdate($branch_code, $search_loan, $ttl_redeem_amnt);

                ?>
                 <script>alert('Successfully redemed!'); location.href='redemption.php';</script> 
                <?php
                exit;
            }else{          
           if($count_data == 1){        
           $loan_amount = stripslashes($data[0]['loan_amount']);
          //       $gross = round($data[0]['gross']);
		   $interest_details = @PayInterest::getInterestDetails($data[0]['pledge_id'],'','', $crr_loan_date);
           //$interest_details = @PayInterest::getInterestDetails($data[0]['pledge_id']);
           $interest_amnt = htmlentities(stripslashes($interest_details['interest']), false);       
           $ttl_redeem_amnt = $loan_amount+$interest_amnt;
           $ttl_redeem_amnt = stripslashes($ttl_redeem_amnt);
           $search_loan = $search_loan_id;
           Redemption::ledDataUpdate($branch_code, $search_loan, $ttl_redeem_amnt);
           }        
           }
           if($search_key != "" || $search_cus_id != "" || $search_key_location != "" || $search_fhw != "" || $search_branch_id != "" || $search_contact_no != ""){
                $set_redeem = 0;
             }else{
                 $set_redeem = 1;
             }
//        END
        
       //  $count = Redemption::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id,$search_contact_no);
        // print_r($count); exit;
        // $category_data = ProductCategory::getProductCategoryOptData();
         include ADMIN_LAYOUT_PATH . 'redemption.html';
         #include ADMIN_LAYOUT_PATH . 'customer.html';
         exit;
         break;

   case 'delete':
        /*
         * Delete from database
         */
        $app->delete($uval);
        $data = Redemption::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        $count = Redemption::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);

        $_SESSION['success_msg'] = REDEMPTION_DELETE;
        include ADMIN_LAYOUT_PATH . 'redemption.html';
        #include ADMIN_LAYOUT_PATH . 'customer.html';
        exit;
        break;



    default:
        /*
         * Make the pages with header and footer
         */
       // $data = Redemption::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$count = Redemption::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        //$category_data = ProductCategory::getProductCategoryOptData();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        if($uval['pledge_id']!=''){
            $product_data = Redemption::ProductDetail($uval['pledge_id']);
            //$product_data = $product_data[0];
            echo $uval['pledge_id'];
            //print_r(array_keys($product_data));
            include ADMIN_LAYOUT_PATH . 'forms/redemption.html';
        }else{
            include ADMIN_LAYOUT_PATH . 'redemption.html';
        }
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}