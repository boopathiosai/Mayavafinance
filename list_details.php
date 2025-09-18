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



/*
 * Get values from js
 */
$pval = $_POST['pval'];
$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];



$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];

//print_r($page); exit;
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$module = (isset($_GET['module'])) ? $_GET['module'] : $_POST['module'];

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];
(int)$old_loan_id = $_POST['old_loan_id'];


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

$loandate = (isset($_GET['loandate'])) ? $_GET['loandate'] : $_POST['loandate'];
$loanamount = (isset($_GET['loanamount'])) ? $_GET['loanamount'] : $_POST['loanamount'];

switch($page) {
    
   case 'scheme_details':

        /*
         * Echo Schema detail Based on Scheme ID.
         */

            //echo "before : ".$loandate;



            $loandate = (trim($loandate)=='') ? date("Y-m-d") : $loandate;

            //echo "after : ".$loandate;

        $scheme_details = Scheme::SchemeInterestDetail($uval['id'], $loandate, $loanamount);
        //print_r($scheme_details);
        include ADMIN_LAYOUT_PATH . 'elements/scheme_details.html';

        exit;
   break;
   case 'customer_auto_list':

        /*
         * Echo SCRIPT String of Customer List For Auto Complete
         */

        $customer_details = Customer::CustomerAutoList();


        $count = count($customer_details)-1;

        //print_r( $count); exit;
        if($count>=0){
            $content = '[';
            $i=0;
            foreach($customer_details as $customer){
                $content .= '{
                                id:"'.$customer['id'].'",
                                name:"'.addslashes($customer['name']).'"
                            }';
                if($i < $count)
                    $content .= ',';
                $i++;
            }

            $content .= ']';
        }else{
            $content =  '[]';
        }

        //ob_end_clean();
        echo "
            {
                    page = \"".$page."\";
                    status = \"".true."\";
                    disp = \"null\";
                    content = ".$content.";
                    isAlert = \"".false."\";
                    args = \"\" ;
           }";

        exit;


   break;
   case 'product_auto_list':

        /*
         * Echo SCRIPT String of Product List For Auto Complete
         */

        $category_id = (isset($_GET['category_id'])) ? $_GET['category_id'] : $_POST['category_id'];
        $ind = (isset($_GET['ind'])) ? $_GET['ind'] : $_POST['ind'];

        $product_details = Product::ProductAutoList($category_id);

        $count = count($product_details)-1;
        if($count>=0){
            $content = '[';
            $i=0;
            foreach($product_details as $product){
                $content .= '{
                                "id":"'.$product['id'].'",
                                "name":"'.addslashes($product['name']).'",
                                "details":"'.addslashes($product['name'].'- ( '.$product['product_with'].' | '.$product['product_without'].' ) ').'",
                                "product_with":"'.addslashes($product['product_with']).'",
                                "product_without":"'.addslashes($product['product_without']).'"
                            }';
                if($i < $count)
                    $content .= ',';
                $i++;
            }

            $content .= ']';
        }else
            $content = '[]';

        $args = '{ id : "'.$ind.'" }';

        //ob_end_clean();
        echo "
            page = \"".$page."\";
            status = \"".true."\";
            disp = \"null\";
            content = ".$content.";
            isAlert = \"".false."\";
            args = ".$args."
           ";

        exit;


   break;

   case 'product_list_by_category':

        /*
         * Echo SCRIPT String of Product List For Auto Complete
         */

        $category_id = (isset($_GET['category_id'])) ? $_GET['category_id'] : $_POST['category_id'];
        $ind = (isset($_GET['ind'])) ? $_GET['ind'] : $_POST['ind'];

        $product_details = Product::ProductAutoList($category_id);

        $count = count($product_details)-1;
        if($count>=0){
            $content = '<option value>-- Select Product --</option>';
            $i=0;
            foreach($product_details as $product){
                $content .= '<option  value="'.$product['id'].'">'.$product['name'].'</option>';
            }

        }else
            $content = '';

        $args = '{ id : "'.$ind.'" }';

        
        //ob_end_clean();
        echo $content;

        exit;


   break;

   case 'fetch_customer_details':

        /*
         * Echo SCRIPT String of Product List For Auto Complete
         */

        $customer_id = (isset($_GET['customer_id'])) ? $_GET['customer_id'] : $_POST['customer_id'];
        
 $field = (isset($_GET['field'])) ? $_GET['customer_id'] : $_POST['field'];

        
        $customer_details = Customer::getDetail($customer_id,$field);

        $content = '{';
        $count = count($customer_details);
        $i=0;
        $status = false;

        if($customer_details){
            $contactFirstLetter = substr($customer_details['contact_no'], 0, 1);           
            if((strlen($customer_details['contact_no']) <= 9)&&($customer_details['contact_no'] != '') || ($contactFirstLetter == 0)|| ($contactFirstLetter == 1 ) || ($contactFirstLetter == 2 ) ||  ($contactFirstLetter == 3 ) || ($contactFirstLetter == 4 ) || ($contactFirstLetter == 5 )  ) {                
                $content .= $key.'customer_mobile_number_validation_status:"0",';
            } else {
                $content .= $key.'customer_mobile_number_validation_status:"1",';
            }
            foreach($customer_details as $key=>$value){
              $value = preg_replace("/\n/","<!--NL-->",$value);
              if($customer_details["branch_id"] == $_SESSION['session_user_branch_id']  || is_superadmin($_SESSION['session_usergroupname'])){
                $content .= $key.':"'.$value.'"';
              }else{
                  $content .= $key.':"'.(($key=="name" || $key=="branch_id" || $key=="customer_id" || $key =="photo"  || $key =="sign" || $key =="id" || $key =="customer_ids") ? $value : "-").'"';
              }
                if($i < $count-1)
                    $content .= ",
                    ";
                $i++;
            }
            $status = true;
        }else $count = 0;

        $content .= '}';

        if($customer_details["branch_id"] == $_SESSION['session_user_branch_id']  || is_superadmin($_SESSION['session_usergroupname']))
            $view_status = true;
        else
            $view_status = false;

        $args = '{
                    module : "'.$module.'",
                    type : "'.$type.'",
                    wavier_days : "'.$wavier_days.'",
                    pay_id : "'.$pay_id.'",
                    paid_date : "'.$paid_date.'",
                    pledge_id : "'.$pledge_id.'"
                 }';

        //ob_end_clean();
        echo "
            {
                    page = \"".$page."\";
                    status = \"".$status."\";
                    view_status = \"".$view_status."\";
                    count = \"".$count."\";
                    disp = \"null\";
                    content = ".$content.";
                    isAlert = \"".false."\";
                    args = ".$args.";
           }";

        exit;


   break;


   case 'wavier_interest':

        /*
         * Echo SCRIPT String of Product List For Auto Complete
         */

        $pledge_id = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];
        $pay_id = (isset($_GET['pay_id'])) ? $_GET['pay_id'] : $_POST['pay_id'];
        $wavier_days = (isset($_GET['wavier_days'])) ? $_GET['wavier_days'] : $_POST['wavier_days'];

        if($type=="edit" && $pay_id != "")
            $interest_details = PayInterest::getInterestDetailsByPayid($pledge_id,$pay_id,$wavier_days);
        else
            $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id, $type, $module);


        $content = '{';
        $count = count($interest_details);
        $i=0;
        foreach($interest_details as $key=>$value){
            $content .= $key.':"'.$value.'"';
            if($i < $count-1)
                $content .= ',';
            $i++;
        }

        $content .= '}';

        $args = '{
                    wavier_days : "'.$wavier_days.'",
                    pledge_id : "'.$pledge_id.'"
                 }';

        //ob_end_clean();
        echo "
            {
                    page = \"".$page."\";
                    status = \"".true."\";
                    disp = \"null\";
                    content = ".$content.";
                    isAlert = \"".false."\";
                    args = ".$args.";
           }";

        exit;


   break;

   case 'cashflow_auto_list':

        /*
         * Echo SCRIPT String of Product List For Auto Complete
         */

        $cash_type = (isset($_GET['cash_type'])) ? $_GET['cash_type'] : $_POST['cash_type'];
        $search_condition .= ($cash_type != "")? "AND (CFT.transaction_type = '".$cash_type."')  ":"";
        $app = $GLOBALS['app'];
        $app->access_table = CASH_FLOW_TRANSACTIONS;
        $query = "SELECT DISTINCT CFT.cash_purpose FROM
                       ".CASH_FLOW_TRANSACTIONS." AS CFT
                       WHERE 1 ".$search_condition."
                       AND editable =1
                       GROUP BY CFT.cash_purpose";
        $cash_type_result = $app->getrows($query);

        $i=0;
        $cashflow_details_purpose = array();
        if(is_array($cash_type_result)){
        foreach($cash_type_result as $value)
        {
            $cashflow_details_purpose[$i] = $value['cash_purpose'];
            $i++;
        }
        }
        $cashflow_details = $cashflow_details_purpose;

        $count = count($cashflow_details)-1;
        if($count>=0){
            $content = '[';
            $i=0;
            foreach($cashflow_details as $purpose){
                $content .= '{
                                "id":"'.addslashes($purpose).'",
                                "name":"'.addslashes($purpose).'"
                            }';
                if($i < $count)
                    $content .= ',';
                $i++;
            }

            $content .= ']';
        }else
            $content = '[]';

        $args = '{ id : "'.$ind.'" }';

        //ob_end_clean();
        echo "
            {
                    page = \"".$page."\";
                    status = \"".true."\";
                    disp = \"null\";
                    content = ".$content.";
                    isAlert = \"".false."\";
                    args = { module: \"cash_flow_transactions\"}
           }";

        exit;


   break;


   case 'filter_group_list':

        /*
         * Echo SCRIPT String of Product List For Auto Complete
         */

        $branch_id = (isset($_GET['branch_id'])) ? $_GET['branch_id'] : $_POST['branch_id'];


        $usergroup_details = Privillege::filteredUserGroup($branch_id);


        $count = count($usergroup_details)-1;
        if($count>=0){
            $content = '[';
            $i=0;
            foreach($usergroup_details as $usergroup){
                $content .= '{
                                "id":"'.$usergroup['id'].'",
                                "name":"'.addslashes($usergroup['name']).'"
                            }';
                if($i < $count)
                    $content .= ',';
                $i++;
            }
            $content = preg_replace("/,$/i","",$content);
            $content .= ']';
        }else
            $content = '[]';

        $args = '{ id : "'.$ind.'" }';

        //ob_end_clean();
        echo "
            {
                    page = \"".$page."\";
                    status = \"".true."\";
                    disp = \"null\";
                    content = ".$content.";
                    isAlert = \"".false."\";
                    args = { module: \"privilege\"}
           }";

        exit;


   break;

   case 'paid_date_validation':



        $pay_id = (isset($_GET['pay_id'])) ? $_GET['pay_id'] : $_POST['pay_id'];
        $paid_date = (isset($_GET['paid_date'])) ? $_GET['paid_date'] : $_POST['paid_date'];
        $pledge_id = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];
        $wavier_days = (isset($_GET['wavier_days'])) ? $_GET['wavier_days'] : $_POST['wavier_days'];
        $module = (isset($_GET['module'])) ? $_GET['module'] : $_POST['module'];

        $paid_date = date("Y-m-d",strtotime($paid_date));

        if($type=="edit" && $pay_id != "")
            $interest_details = PayInterest::getInterestDetailsByPayid($pledge_id,$pay_id,$wavier_days,$paid_date );
        else
            $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id,$paid_date,  $type, $module);

       //p_arr($interest_details); exit;
       $checking =  $interest_details["last_pay_date"]." > ".$paid_date." || ".$interest_details["loan_date"]." > ".$paid_date." >>> ".$interest_details["default_wavier_days"]." > ".$interest_details["no_of_days"]." && ".$module." != 'auction_management'";

        if($interest_details["loan_date"] > $paid_date){
            $missing_labels["paid_on"] = PAID_DATE_INVALID_ALERT;
        }

        if($interest_details["default_wavier_days"] > $interest_details["no_of_days"] && $module != "auction_management" && $interest_details["default_wavier_days"] >5){
            $missing_labels["wavier_days"] = WAVIER_DAYS_INVALID_ALERT;
        }


        //Changes - 10May12 - for wavier validation
        if($wavier_days>5) {
            $missing_labels["wavier_days"] = WAVIER_DAYS_MORETHAN_FIVE_DAYS_INVALID_ALERT;
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

        //ob_end_clean();
        echo "
        {
        \"page\" : \"".$page."\",
        \"success\" : \"".$success."\",
        \"disp\" : \"".$disp."\",
        \"checking\" : \"".$checking."\",
        \"content\" : \"".VALIDATE_ERROR."\",
        \"isAlert\" : \"".$isMsgDisplay."\" ,
        \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module."\",\"search\":\"".$search_args."\"}
        }";
        exit;
   break;
   case 'auction_interest_details':
   case 'reset_interest_details':

        /*
         * Echo SCRIPT String of Product List For Auto Complete
         */

        $pay_id = (isset($_GET['pay_id'])) ? $_GET['pay_id'] : $_POST['pay_id'];
        $paid_date = (isset($_GET['paid_date'])) ? $_GET['paid_date'] : $_POST['paid_date'];
        $pledge_id = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];
        $wavier_days = (isset($_GET['wavier_days'])) ? $_GET['wavier_days'] : $_POST['wavier_days'];



        if($type=="edit" && $pay_id != "")
            $interest_details = PayInterest::getInterestDetailsByPayid($pledge_id,$pay_id,$wavier_days,$paid_date );

        else
            $interest_details = PayInterest::getInterestDetails($pledge_id,$wavier_days, $pay_id,$paid_date,  $type, $module);
       // echo "dsf";exit;
            //echo "<prE>";print_r($interest_details);exit;

        $content = '{';
        $count = count($interest_details);

        $i=0;
        foreach($interest_details as $key=>$value){
            $content .= $key.':"'.$value.'"';
            if($i < $count-1)
                $content .= ',';
            $i++;
        }

        $content .= '}';

        $args = '{
                    module : "'.$module.'",
                    type : "'.$type.'",
                    wavier_days : "'.$wavier_days.'",
                    pay_id : "'.$pay_id.'",
                    paid_date : "'.$paid_date.'",
                    pledge_id : "'.$pledge_id.'"
                 }';

        //ob_end_clean();
        echo "
            {
                    page = \"".$page."\";
                    status = \"".true."\";
                    disp = \"null\";
                    content = ".$content.";
                    isAlert = \"".false."\";
                    args = ".$args.";
           }";

        exit;


   break;


}
