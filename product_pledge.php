<?php
ob_start();
session_start();

define ('ROOT_PATH', '');
$page_title = 'Product Pledge';
$module_name = 'product_pledge';



include ROOT_PATH . 'includes/config.php';
include_once ROOT_PATH . 'classes/sms.class.php';

//sms::sendData('','');

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
    //echo"not accessible";
    header('Location: index.php');
    exit;
}

/*$product_withs = array('கருப்பு பாசி','வெ.கல்','சி.கல்','கல்','1கல்','வெ.நி.கல்','சி1கல்','வெ1கல்','சி.வெ.கல்','சி.ப.வெ.கல்','ப.வெ.கல்','நீ.வெ. கல்','நி1கல்','கல்பாசி','இரட்டை இலை விலாச','என் விலாச','என்.சி.விலாச','எஸ் விலாச','எஸ்.எம்.கே விலாச','ஏ.எஸ்.விலாச','ஏ.விலாச','கே.எம். விலாச','டி.எஸ் விலாச','பி.எம்.விலாச','பி.ஜி விலாச','வி விலாச','ஜே.எம்.விலாச');


foreach($product_withs as $product_with_data){
 $app->access_table = PRODUCT_WITH;
 $pval['pre_name'] = $product_with_data;
 $pval['status'] = 1;

 $app->add($pval);

}*/

/*
 * Get values from js
 */
$pval = $_POST['pval'];
$pp_pval = $_POST['pp_pval'];
$name = $_POST['name'];
$gross_weight = $_POST['gross_weight'];
$category_id = $_POST['category_id'];
$net_weight = $_POST['net_weight'];
$market_rate = $_POST['market_rate'];
$pre_name_id=$_POST["pre_name_id"];
$product_id=$_POST["product_id"];
$product_name=$_POST["product_name"];
$product_with=$_POST["product_with"];
$product_without =$_POST["product_without"];

$product_items =$_POST["product_items"];
$product_count = $_POST['product_count'];
(int)$loan_amount = $_POST['loan_amount'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$start =  (isset($_GET['start'])) ? $_GET['start'] : $_POST['start'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];
//$change_red_status_array['red_status'] =  (isset($_GET['red_status'])) ? $_GET['red_status'] : $_POST['red_status']; // DSSK changed  13_SEP_2017
(int)$scheme_id=$_POST['scheme_id'];
$customer_id =(isset($_GET['customer_id'])) ? $_GET['customer_id'] : $_POST['customer_id'];
$customerid =(isset($_GET['customerid'])) ? $_GET['customerid'] : $_POST['customerid'];
//$sub_id = $_POST['sub_id'];
$sub_id =(isset($_GET['sub_id'])) ? $_GET['sub_id'] : $_POST['sub_id'];

//constant message for cash flow transaction
$cash_purpose= CASHFLOW_PURPOSE_PLEDGED;
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
$search_mobile_no = (isset($_GET['search_mobile_no'])) ? $_GET['search_mobile_no'] : $_POST['search_mobile_no'];

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
if($search_mobile_no != ""){
    $search_args.="search_mobile_no=".$search_mobile_no."&";
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

$app->access_table = PLEDGE;

/*
 * Assign limit to display records
 */
//print_r($_SESSION['pcount_page']);
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);

//$paging = new Paging();
//$paging->setPerPage(ROWS_PER_PAGE);

//echo $start;

//$paging->setStartRecord($start);

switch($type) {

   case 'formvalidation':

            /*
             * Form validation here . notify error, if exists.
             */
           // print_r($_POST);
            $missing_labels = Validator::validate_json('ProductPledge');

			// As per client requirement I have commented the below validation

           /* if(date("Y-m-d",strtotime($pval['loan_date'])) > date("Y-m-d")){

            $missing_labels['loan_date'] = "Loan Date - Exceed the current date";

            }*/


            if(intval($_POST["product_count"]) <= 0){

            //echo 'Minimum One Product Item Require~~#category_id, #product_name, #gross_weight, #net_weight~~0';
            //exit;

                //$missing_labels['category_id'] = 'Category - Minimum One Category Require';
                //$missing_labels['product_name'] = 'Product Name - Minimum One Product Require';
                //$missing_labels['product_items'] = 'Product Items - Minimum One Product Item Require';
                //$missing_labels['gross_weight'] = 'Gross Weight - Minimum One Product Gross Weight Require';
                //$missing_labels['net_weight'] = 'Net Weight - Minimum One Product Net Weight Require';
                $missing_labels['category_id'] = '';
                $missing_labels['product_name'] = '';
                $missing_labels['product_items'] = '';
                $missing_labels['item_count'] = 'Product Items - Minimum one item require';


            }

            else{

			
				$existing_pre_name_id = array();
                $pre_product_id = array();
                $stack = array(); 
                $cat_id = array();

                for($i=0;$i<$_POST["product_count"];$i++){

                    array_push($stack,$product_name[$i]);  // If the Category first added the product name  wise checking dupllication

                    array_push($cat_id,$category_id[$i]);

                    /*print_r(array_count_values($stack));*/

                    /*if(array_count_values($stack) > 1 && array_count_values($cat_id) > 1 ) {
                        $missing_labels['DUI_'.$i] = 'Dublicated Product found. Remove the product row '.($i+1);
                    }*/
					
					if($product_id[$i] == '' || $product_id[$i] == 0){
						$missing_labels['product_name'] = '';
						$missing_labels['DUI_'.$i] = 'Product name entered in row '.($i+1).' is not found in the table!. Please select the product from the dropdown list';
					}
					
					/*echo "<pre>";
					print_r($existing_pre_name_id);
					echo $pre_name_id[$i]."=="; 
					echo $product_id[$i] ."==". $existing_pre_name_id[$pre_name_id[$i]][$i]; */
					//echo "</br>";
                    /* if(in_array($product_id[$i],$pre_product_id) && $product_id[$i] != '' && $product_id[$i] != 0){
					//if(in_array($product_id[$i],$existing_pre_name_id[$pre_name_id]) && $product_id[$i] != '' && $product_id[$i] != 0){  
					//if($product_id[$i] == $existing_pre_name_id[$pre_name_id]){
						$missing_labels['product_name'] = '';
						$missing_labels['DUI_'.$i] = 'Duplicated Product found. Remove the product row '.($i+1);
					//}
                    } */
                    if($category_id[$i]=="" || $product_id[$i]=="" || intval($product_items[$i]) == 0 ){ //|| $gross_weight[$i]=="" || $net_weight[$i]==""

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
					$existing_pre_name_id[][$pre_name_id[$i]] = $product_id[$i];
					
                }

            }
			//print_r($existing_pre_name_id);
			//print_r(array_unique($existing_pre_name_id));
			$existing_pre_name_id_dup = array_map("unserialize", array_unique(array_map("serialize", $existing_pre_name_id)));
			//echo count($existing_pre_name_id_dup);
			$pro_unique_count = count($existing_pre_name_id_dup);
			if($pro_unique_count != $i){
				//echo "duplicates";
				$missing_labels['product_name'] = '';
				$missing_labels['DUI_'.$i] = 'Duplicated Product found, Prename and Product name should not be same in 2 different rows!';
			}else{
				//echo "No duplicates";
			}
				
//exit;

            //Loan Rate Validation
            $loan_rate = floatval($_POST['loan_rate']);
            $loan_amount = floatval($pval['loan_amount']);
            $gross_weight = floatval($pp_pval['total_gross_weight']);
            $net_weight = floatval($pp_pval['total_net_weight']);
            $max_amount = $loan_rate * $net_weight;

            if($gross_weight < $net_weight){
                $missing_labels['total_net_weight'] = 'Net weight - Should be equal or less than from gross weight';
            }


            if($max_amount < $loan_amount){

                //echo 'Loan amount exceed the limits of maximum amount Rs. '.$max_amount.'~~#loan_amount, #scheme_id, #net_weight~~0';
                //exit;
                $missing_labels['loan_amount'] = 'Loan amount exceed the limits of maximum amount Rs. '.$max_amount;
                $missing_labels['scheme_id'] = 'Scheme - Loan amount exceed the limits, You can change suitable scheme to  get amount more than Rs. '.$max_amount;
                $missing_labels['total_net_weight'] = 'Net weight - Loan amount exceed the limits, You can pledge more item to get amount more than Rs. '.$max_amount;

            }

            $balance = CashFlowTransaction::getBranchBalance($_SESSION['session_user_branch_id']);

                        //echo floatval($balance);
            if(floatval($balance) < $loan_amount ){

                $missing_labels['loan_amount'] = "Loan amount exceed the limits of available branch balance of Rs. ".unitDisplay($balance,false);

            }

            /*$missing_vals = Validator::validate_data('ProductPledge');
            if (!empty( $missing_vals )) {
                $missing_data = explode('@@@', $missing_vals);
                $require_data = Scheme::${$missing_data[1]};
                echo $require_data.'~~' . $missing_data[0].'~~0';
                exit;
            }
            echo '~~~~1';
            exit;*/

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
                \"content\" : \"".PRODUT_PLEGDE_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
            break;

   case 'add':
   case 'update':
  //echo "<script language='javascript'> alert('dfgdfgdfgdf'); </script>";
//       echo $type;
//       echo "<pre>";
//       print_r($_SESSION);
//       echo "<pre>";
//       print_r($pval);
//       exit;
//       echo "<pre>";
//       print_r($pp_pval);
       $branch = $_SESSION['session_user_branch_id'];
        if($type == 'add')
        {
			
			//find duplicate in loan id - Start - 27-01-2020 - Murali //
            $app->access_table = PLEDGE;
            $duplicate_loan_id = $app->find(
                    array("id"),
                    array(
                        "conditions"=>array("loan_id = "=>$pval['loan_id'])
                )); 
                
                $sub_brnch_loanid = list($sub_branch_code,$pledge_status,$branch_code)= productpledge::getNewSubLoanCode($pval['sub_branch_id']); 
                if($pval[loan_id] == '') {
                    if($sub_brnch_loanid){  
                        if($pledge_status=='1') {
                            $pval[loan_id] =$branch_code;                  
                        } else {
                            $pval[loan_id] =$sub_branch_code; 
                        }
                    }
                }  
            
                if( ( $duplicate_loan_id[0]['loan_id'] ) || ($pval[loan_id] == '' ) || ( $pval['customer_id'] == '') || ( $pval['service_charge'] == '')){
                    if( $pval[loan_id] == '' ) {
                        $content = PRODUT_PLEGDE_LOAN_ID_ERROR;
                    } else if( $_POST['customer_name'] == '' ) {
                        $content = PRODUT_PLEGDE_CUSTOMER_NAME_ERROR;
                    } else if($pval['customer_id'] == '') {
                        $content = PRODUT_PLEGDE_CUSTOMER_ID_ERROR;
                    
                    } else if($pval['service_charge'] == '') {
                        $content = PRODUT_PLEGDE_SERVICE_CHARGE_ERROR;
                    } else {
                        $content = PRODUT_PLEGDE_VALIDATE_ERROR;
                    }
                    echo "
                    {                    
                        \"page\" : \"".$page."\",
                        \"status\" : \"".$status."\",
                        \"disp\" : \"".$disp."\",
                        \"content\" : \"".$content."\",
                        \"isAlert\" : \"".true."\" ,
                        \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
                    }";
                    exit;
                }
			
			//find duplicate in loan id - End //
			$app->access_table = PLEDGE;
            //Below one line For Testing Purpose, Enabled. Not for live application
            $pval['loan_date'] = date("Y-m-d H:i:s", strtotime($pval['loan_date'].date(" H:i:s")));

            $pval['item_count'] = $pp_pval['item_count'];

            $pval['total_gross_weight'] = $pp_pval['total_gross_weight'];
            $pval['total_net_weight'] = $pp_pval['total_net_weight'];
            $pval['market_rate'] = $pp_pval['market_rate'];
            $pval['branch_id'] = $_SESSION['session_user_branch_id'];
            /*if($pval['sub_branch_id'] == ""){
            $pval['branch_id'] = $_SESSION['session_user_branch_id'];
      $pval['sub_branch_id'] = "";
      }
      else
      {
      $pval['branch_id'] = "";
      $pval['sub_branch_id'];
      }*/

            $pval['status'] = 1;
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            //echo("pval = "); print_r($pval);echo("<hr>");
            //print_r($pval);exit;
            $app->add($pval);

            $uval['id'] = $app->last_inserted_id();
            //exit;
            $query = "UPDATE ".SETTINGS." SET config_value = config_value+1 WHERE config_name='loan_count'";
            $app->execute($query);
            $_SESSION['success_msg'] = Product_pledge_saved;

            //Insert Cash Flow Data
            //$uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$pval['total_amount_given'],$cash_purpose." - (Loan ID : ".$pval['loan_id'].")", $pval['loan_date'] );

            $uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$pval['loan_amount'], CASHFLOW_PURPOSE_PLEDGED, $pval['loan_date'], 0, 1 );
            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['service_charge'], CASHFLOW_PURPOSE_SERVICE, $pval['loan_date'], $uval['cod'],7);


            //Product Pledge Cash flow Id Relation Setting
            $app->access_table = PLEDGE;
            $app->save(array(
                    "cash_flow_id"=>$uval['cod'],
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
                ), array('id = ' => $uval['id']));

        }
        else
       {

            //Below one line For Testing Purpose, Enabled. Not for live application
       // print_r($pval);
            $pval['loan_date'] = date("Y-m-d H:i:s", strtotime($pval['loan_date'].date(" H:i:s")));

            $pval['item_count'] = $pp_pval['item_count'];

            $pval['total_gross_weight'] = $pp_pval['total_gross_weight'];
            $pval['total_net_weight'] = $pp_pval['total_net_weight'];
            $pval['market_rate'] = $pp_pval['market_rate'];
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            //echo("pval = "); print_r($pval); echo("<hr>");
            $app->access_table = PLEDGE_DETAILS;
            $sval['pledge_id'] = "'".$uval['id']."'";
            $app->delete($sval);
            $app->access_table = PLEDGE;
			$app->save($pval, array('id = ' => $uval['id']));
			
			//find duplicate in loan id - Start - 27-01-2020 - Murali //
            /*$duplicate_loan_id = $app->find(
                            array("id"),
                            array(
                                "conditions"=>array("loan_id = "=>$pval['loan_id'])
                             ));
							 
			if($duplicate_loan_id[0]['loan_id']){
				$app->save($pval, array('id = ' => $uval['id']));
			}*/

            //get Cash Flow ID
            $app->access_table = PLEDGE;
            $cash_detail = $app->find(
                            array("cash_flow_id"),
                            array(
                                "conditions"=>array("id = "=>$uval['id'])
                             ));

            //Update Cash In Flow Amount
            //$res = CashFlowTransaction::updateCashFlow($cash_detail[0]['cash_flow_id'],$pval['total_amount_given']);
            CashFlowTransaction::deleteCashFlow($cash_detail[0]['cash_flow_id']);

            //$uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$pval['total_amount_given'],$cash_purpose." - (Loan ID : ".$pval['loan_id'].")", $pval['loan_date'] );

            $uval['cod'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashout,$pval['loan_amount'], CASHFLOW_PURPOSE_PLEDGED, $pval['loan_date'], 0, 1 );
            $uval['cid'] = CashFlowTransaction::insertCashFlow($pval['branch_id'],$cashin,$pval['service_charge'], CASHFLOW_PURPOSE_SERVICE, $pval['loan_date'], $uval['cod'],7 );


            //Product Pledge Cash flow Id Relation Setting
            $app->access_table = PLEDGE;
            $app->save(array(
                    "cash_flow_id"=>$uval['cod'],
                    "modified_datetime"=> date("Y-m-d H:i:s"),
                    "modified_by" => $_SESSION['session_userid']
                ), array('id = ' => $uval['id']));


            $_SESSION['success_msg'] =Product_pledge_update;
        }
//            $pleadge_id =  $uval['id'];
            $dval['pledge_id'] = $uval['id'];
            $total_weight = 0;
            $total =$product_count;
            $dval['created_datetime'] = date('Y-m-d H:i:s');
            $dval['created_by'] = $_SESSION['session_userid'];
            $ordering = 1;
            $app->access_table = PLEDGE_DETAILS;

            for($i=0;$i<$total;$i++)
            {
                /** rathna modified on 12.10.06 for Product dublication restrict **/
                /* $pquery = "SELECT id FROM ".PRODUCTS." where name LIKE '".trim($product_name[$i])."' AND product_with LIKE '".trim($product_with[$i])."' AND product_without LIKE '".trim($product_without[$i])."'";
                $pid = $app->getrow($pquery);
                if((int)$pid['id'] > 0){
                    $dval['product_id'] = $pid['id'];
                }else{ */
                    if($product_id[$i]==""){
                            /* $pdval['category_id'] = $category_id[$i];
                            $pdval['name'] = trim($product_name[$i]);
                            $pdval['product_with'] = trim($product_with[$i]);
                            $pdval['product_without'] = trim($product_without[$i]);

                            $pdval['created_datetime'] = date('Y-m-d H:i:s');
                            $pdval['created_by'] = $_SESSION['session_userid'];
                            $pdval['modified_datetime'] = date('Y-m-d H:i:s');
                            $pdval['modified_by'] = $_SESSION['session_userid'];
                            $pdval['status'] = 1;
                            //echo("pdval = "); print_r($dval);echo("<hr>");
                            $app->access_table = PRODUCTS;
                            $app->add($pdval);

                            $dval['product_id'] = $app->last_inserted_id(); */

                    }
                    else{
                        $dval['product_id'] =$product_id[$i];
                        $dval['product_with'] = $product_with[$i];
                    }
                //}
                $app->access_table = PLEDGE_DETAILS;

                $dval['category_id'] = $category_id[$i];
				$dval['pre_name_id'] = $pre_name_id[$i];
                //$dval['gross_weight'] = $gross_weight[$i];
                //$dval['net_weight'] = $net_weight[$i];
                //$dval['market_rate'] = $market_rate[$i];
                $dval['product_items'] = $product_items[$i];
                $dval['ordering'] = $ordering;
                $dval['status'] = 1;
                //echo("dval = "); print_r($dval);echo("<hr>");
                //$dval['status'] = $status[$i];
                //$total_weight += $net_weight[$i];
                $app->add($dval);
                $ordering++;
            }
            
//            echo $uval['id'];
//            echo "<pre>";
//       print_r($pval);
//       exit;
            
      // SMS - Sathish santhosh kumar //     
       $pleadge_id = $uval['id'];
        $custmr_id = $pval['customer_id'];
        $app->access_table = CUSTOMERS;
        $find_customer = $app->find(
                array("contact_no","branch_id"), array(
            "conditions" => array("id = " => $custmr_id),
        ));
//        echo "<pre>";
//        print_r($find_customer);
//         if ($pval['branch_id'] != "") {
//            $branch_id = $pval['branch_id'];
//            $app->access_table = BRANCH;
//            $find_branch_name = $app->find(
//                    array("name"), array(
//                "conditions" => array("id = " => $branch_id),
//            ));
//            $branch_name = $find_branch_name[0]["name"];
//        } else {
//            $branch_name = "-";
//        }
        
        
//      -----------        
        
//         $sbranch_id =  $find_customer[0]["branch_id"];
        
            $app->access_table = PLEDGE;
            $find_branchs = $app->find(
                    array("branch_id","sub_branch_id"), array(
                "conditions" => array("id = " => $pleadge_id),
            ));
            $branch_id = $find_branchs[0]["branch_id"];
            $sub_branch_id = $find_branchs[0]["sub_branch_id"];
            
           
//            <!-- BRANCH -->
            
//           $branch_id =   $find_sbranch_name[0]["branch_id"];
            $app->access_table = BRANCH;
            $find_branch_name = $app->find(
                    array("name"), array(
                "conditions" => array("id = " => $branch_id),
            ));
            $branch_name = $find_branch_name[0]["name"];
            
            
 // SMS contant changes 30_MAY_2018 by DSSK//
                  
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
                  
                  // END//            
//   --------------         
//            <!-- SUB BRANCH -->
//         $branch_id =  $find_customer[0]["branch_id"];
//         $branch_id =   $find_sbranch_name[0]["branch_id"];
            $app->access_table = SUB_BRANCH;
            $find_branch_name = $app->find(
                    array("name"), array(
                "conditions" => array("id = " => $sub_branch_id),
            ));
            $sub_branch_name = $find_branch_name[0]["name"];
        
//        echo $branch_id."-".$branch_name."</br>";
        $scheme_id = $pval['scheme_id'];
        $app->access_table = SCHEME;
        $find_schme_name = $app->find(
                array("name"), array(
            "conditions" => array("id = " => $scheme_id),
        ));
        $date = date_create($pval[loan_date]);
        $lon_date = date_format($date, "d/m/Y");

        $lon_id = $pval['loan_id'];
        $lon_amnt = $pval['loan_amount'];
        $pleadge_branch = $branch_name."  ".$sub_branch_name;
        $schme_name_text = $find_schme_name[0]["name"];
        
//        $schme_name =  str_replace("%"," 0025",$schme_name_text);
//        $schme_name =  bin2hex($schme_name_text);
        $schme_name =  $schme_name_text;
        
//         $sms_contant_text = "Pledging Details :- " . "\r\n" . "BR : " . $pleadge_branch . "\r\n" . "DT : " . $lon_date . "\r\n" . "ID : " . $lon_id . "\r\n" . "Loan Amt : " . $lon_amnt. "\r\n" . "Scheme : " . $schme_name;
        
      //$sms_contant_text = $nagai_adagu_vibaram_tamil_name." ". $a_ean_tamil_name.": ".$lon_id." ".$Asal_tamil_name.": ". $lon_amnt." ".$scheme_tamil_name.": ".$schme_name;

        if($scheme_id == 5){
            $sms_contant_text  = 'நகை அடகு விபரம் அ.எண்:'.$lon_id.' அசல்:'.$lon_amnt.' 1.5% தினசரி வட்டி-GOLDLOAN';
            $templateid = '1707161737187975096';
        }else {
            $sms_contant_text  = 'நகை அ.விபரம் அ.எண்:'.$lon_id.' அசல்'.$lon_amnt.' வெள்ளி 3 தினசரி வட்டி-GOLDLOAN';
            $templateid = '1707161737208291855';
        }
        
//Convert Tamil text to hexa decimal codes
           /* $mb_hex = '';
            for($i = 0 ; $i<mb_strlen($sms_contant_text,'UTF-8') ; $i++){
                $c = mb_substr($sms_contant_text,$i,1,'UTF-8');
                $o = unpack('N',mb_convert_encoding($c,'UCS-4BE','UTF-8'));
                $mb_hex .= sprintf('%04X',$o[1]);
            }
            $sms_contant = $mb_hex;    */
        
        $send_no = $find_customer[0]["contact_no"];
//        exit;
//        sms::sendData($sms_contant, $send_no);
        // Stop in testing server
        
        sms::sendData($sms_contant_text, $send_no, $templateid);

//       exit;
//        $ch = curl_init();
//        $user="sathish.d@angleritech.com:123456";
//        $receipientno="9442663215";
//        $senderID= "TEST SMS"; 
//        $msgtxt="Pledging Details :-"."\r\n"."BR:".$branch_name."\r\n"."DT:".$lon_date."\r\n"."ID:".$lon_id."\r\n"."Loan Amt:".$lon_amnt;
////       echo $msgtxt=$branch_name."</br>".$lon_date.;
////            $msgtxt.=$lon_date."</br>";
////            $msgtxt.=$lon_id."</br>";
////            $msgtxt.=$lon_amnt;
////            $msgtxt.=$schme_name;
//        curl_setopt($ch,CURLOPT_URL,  "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_POST, 1);
//        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtxt=$msgtxt");
//        $buffer = curl_exec($ch);     
//        if(empty ($buffer))
//        { echo "buffer is empty "; }
//        else
//        { echo $buffer; }
//        curl_close($ch);
         
         // \.SMS //
       
       
       

            /*$app->access_table = PLEDGE;
            $val['total_weight'] = $total_weight;
            $val['item_count'] = $total;
            $app->save($val, array('id = ' => $uval['id']));*/

            //exit;
        //$_SESSION['notice_info'] = PRODUCT_PLEDGE_UPDATE;
        //header("Location: product_pledge.php?".$search_args."pageno=".$page_no."");
        //ProductPledge::WritePledgeInfo();
        //$_SESSION['success_msg'] = PLEGDE_SAVED;
         echo "
        {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"".PLEGDE_SAVED."\",
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
        }";
         
        
         
        exit;

        break;

   case 'create':
   case 'edit':
   case 'preview':
        /*
         * Get the Department details, if type is edit
         */
        if($type!='create'){$product_details = ProductPledge::Detail($uval['id']);}



       //echo"<pre>";print_r($product_details);exit;
        $product_data = $product_details[0];
                $instead_customer_details = ProductPledge::InsteadCustDetail($uval['id']);
                $instead_customer_data = $instead_customer_details[0];



               

        include ADMIN_LAYOUT_PATH . 'forms/product_pledge.html';

        break;

   case 'paging':
   case 'back':
   case 'search':
   case 'refresh':
            if($type=='search'){ $type_of_page="search";}
          
          $data = ProductPledge::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id,$type_of_page,$search_mobile_no);

//          $count = ProductPledge::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id,$type_of_page,$search_mobile_no);
          $category_data = ProductCategory::getProductCategoryOptData();
          include ADMIN_LAYOUT_PATH . 'product_pledge.html';
          exit;
          break;

   case 'delete':
        /*
         * Delete from database
         */

        /*if($uval['id']!="")
        {

            $code = ProductPledge::getLoanCode($uval['id']);
            $status = ProductPledge::Delete($uval['id']);
            if($status["success"])
            {
                $message[] = $code." - ".PLEDGE_DELETED;
            }
            else
            {
                $message[] .= $code." - ".PLEDGE_NOT_DELETED." - ".$status['error'];
            }
        }

       if(is_array($_POST["list"]))
        {
            $i=0;
            foreach($_POST["list"] as $key=>$val)
            {
                $name = ProductPledge::getLoanCode($val);
                $status = ProductPledge::Delete($val);
                if($status["success"])
                {
                    $message[] = $name." ( ".$val." ) - ".PLEDGE_DELETED;
                }
                else
                {
                    $message[] .= $name." ( ".$val." ) - ".PLEDGE_NOT_DELETED." - ".$status['error'];
                }

            }

        }

         $alert_message = (is_array($message)) ? implode("<br> * ",$message) : $message;
         echo "
           {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \" * ".$alert_message."\",
            \"isAlert\" : \"".true."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\",\"isAlert\":\"".true."\"}
            }";
         exit;*/


        /*
         * Delete from database
         */

        if($uval['id']!="")
        {

            $name = ProductPledge::getLoanCode($uval['id']);
            $status = ProductPledge::Delete($uval['id']);

            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".PLEDGE_DELETED;
            }
            else
            {
                if(is_array($status['error'])){

                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".PLEDGE_NOT_DELETED." - ".$err;

                    }

                }else{

                    $message[] .= $name."( ".$uval['id']." ) - ".PLEDGE_NOT_DELETED." - ".$status['error'];

                }
            }
        }

       if(is_array($_POST["list"]))
            {
                $i=0;

                foreach($_POST["list"] as $key=>$val)
                {

                    $name = ProductPledge::getLoanCode($val);
                    $status = ProductPledge::Delete($val);

                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".PLEDGE_DELETED;
                    }
                    else
                    {

                        if(is_array($status['error'])){

                            foreach($status['error'] as $err){

                                $message[] .= $name."( ".$val." ) - ".PLEDGE_NOT_DELETED." - ".$err;

                            }

                        }else{

                            $message[] .= $name."( ".$val." ) - ".PLEDGE_NOT_DELETED." - ".$status['error'];

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
      /*
       * Change status process for the Department details
       */
   case 'change_status':
        /*
         * Change the stauts in database
         */
          $app->save($change_status_array, array('id = ' => $uval['id']));
          $notice = STATUS_SUCCESS;
          echo $change_status_array['status'].'~~'.$notice;
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
        case 'fetch_service_charge_amount':
            /*
             * Fetch Service Charge Amount
             */
            $service_charge_details = ServiceCharge::service_charge_amount($loan_amount);
            echo (int)$service_charge_details[0]['service_charge_amount'];
        exit;
        break;
 case 'fetch_sub_branch_details':
        /*
         * Fetch sub branch code
         */

                $sub_brnch_loanid = list($sub_branch_code,$pledge_status,$branch_code)= productpledge::getNewSubLoanCode($sub_id);
                    //echo $pledge_status;exit;
				if($sub_brnch_loanid){	
					if($pledge_status=='1'){
					    echo '<input required  type = "text" name="pval[loan_id]" id="sub1_loan_id" value="' . $branch_code . '" />';
					}else{
					  echo $sub_branch_code;
					  echo '<input required  type = "hidden" name="pval[loan_id]" id="sub1_loan_id" value="' . $sub_branch_code . '" />';
					}
				}else{
				   echo '<input required  type = "hidden" name="pval[loan_id]" id="sub1_loan_id" value="" />';
			   }

      exit;
        break;


    case 'fetch_customer_details':
        /*
         * Fetch Customer Details
         */
            $customer_details = Customer::Detail($customer_id);

            if($customer_details){
            echo $customer_details[0]['photo']."~~".$customer_details[0]['sign']."~~".$customer_details[0]['name']."~~".$customer_details[0]['street']."~~";
            echo $customer_details[0]['location']."~~".$customer_details[0]['district']."~~".$customer_details[0]['state']."~~";
            echo $customer_details[0]['postal_code']."~~".$customer_details[0]['contact_no']."~~".$customer_details[0]['customer_id']."~~".$customer_details[0]['id'];
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
	  
	  case 'prename_dropdown':
        /*
         * Fetch Customer Details
         */
    
        $pre_name_data = get_dropdown_list_with_field('id', 'pre_name', PRENAME);
		$pre_name_options .= build_select_options($pre_name_data,$product_data['pre_name_id']);
        echo $pre_name_options;
      exit;
      break;
      case 'product_with_dropdown':
        /*
         * Fetch Customer Details
         */
    
        $product_with_data = get_dropdown_list_with_field('id', 'pre_name', PRODUCT_WITH);
        $product_with_options .= build_select_options($product_with_data,$product_with_data['pre_name_id']);
        echo $product_with_options;
      exit;
      break;
	  case 'name_dropdown':
        /*
         * Fetch Customer Details
         */
    
        //$products_data = get_dropdown_list_with_field('id', 'name', PRODUCTS);
		$products_data = get_dropdown_list_with_field_category('id', 'name','1', PRODUCTS);
		$product_options .= build_select_options($products_data,$product_data['product_id']);
        echo $product_options;
      exit;
      break;
	  
	  case 'find_product_id':
		 /*
         * Found Product id
         */
		 
		$product_details = Product::Detail($product_id);
			if($product_details){
				echo "1";
            }
            else{
				echo "0";
            }
		 
	  exit;
      break;
	  
     case 'fetch_product_data':
            $cate_id  =$_POST['queryString'];
            $pro_name =$_GET['cate_id'];
            $app->access_table = PRODUCTS;
            $data =ProductPledge::AutocompleteDetails(65,"r");
            //echo $data;
            foreach($data as $val){
                //echo $val;
                echo '<li onClick="fill(\''.$val.'\');">'.$val.'</li>';

            }
            //echo "[{name:'my chain', id:'15', category_id:'65'},{name:'chain', id:'17', category_id:'65'},{name:'ring1', id:'18', category_id:'65'},{name:'sdsad', id:'14', category_id:'64'},{name:'ring2', id:'19', category_id:'65'},{name:'ttt', id:'20', category_id:'66'},{name:'asdasd', id:'21', category_id:'65'},{name:'bangle', id:'25', category_id:'71'},{name:'test04p1', id:'32', category_id:'101'},{name:'ring', id:'28', category_id:'64'},{name:'Diamond jewel', id:'29', category_id:'99'},{name:'Diamond necklace', id:'30', category_id:'99'},{name:'diamond necklace1', id:'31', category_id:'99'}]";


      exit;
      break;
      default:
        /*
         * Make the pages with header and footer
         */
        //echo "here";exit;
        $data = ProductPledge::Listview($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        $count = ProductPledge::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
            
       // echo $count;exit;

        /*
          *On 02-Aug-2016
          *$count = ProductPledge::ListviewCount($search_key, $search_cus_id, $search_key_location, $search_fhw,$search_branch_id,$search_loan_id);
        */
        $category_data = ProductCategory::getProductCategoryOptData();
        //echo "<pre>";print_r($category_data);exit;
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'product_pledge.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>
