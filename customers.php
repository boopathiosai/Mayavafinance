<?php
session_start();
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
$page_title = 'Customers';
$module_name = 'customers';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = MASTERS_CAT_ID;

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
$pval = $_POST['pval'];

//echo "Test";
//print_r($pval);
//exit;



$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$disp = (isset($_GET['disp'])) ? $_GET['disp'] : $_POST['disp'];
$disp = ($disp!='') ? $disp : 'content_div';

$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];

$list['list'] =  (isset($_GET['list'])) ? $_GET['list'] : $_POST['list'];



 /*
 * Get search options
 */

$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];
$search_key_location = (isset($_GET['search_key_location'])) ? $_GET['search_key_location'] : $_POST['search_key_location'];
$search_fhw = (isset($_GET['search_fhw'])) ? $_GET['search_fhw'] : $_POST['search_fhw'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_contact_no = (isset($_GET['search_contact_no'])) ? $_GET['search_contact_no'] : $_POST['search_contact_no'];
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
if($search_contact_no != "") {
    $search_args.="search_branch_id=".$search_contact_no."&";
}
$from_module = (isset($_GET['from_module'])) ? $_GET['from_module'] : $_POST['from_module'];
$from_type = (isset($_GET['from_type'])) ? $_GET['from_type'] : $_POST['from_type'];
$pledge_id = (isset($_GET['pledge_id'])) ? $_GET['pledge_id'] : $_POST['pledge_id'];


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

$app->access_table = CUSTOMERS;

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
            /*$missing_vals = Validator::validate_data('Customer');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Customer::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1';
            */

            $missing_labels = Validator::validate_json('Customer');
            $contactFirstLetter = substr($pval['contact_no'], 0, 1);
            //$missing_labels['dob'] = "Date of birth - Invalid".date("Y-m-d",mktime(0,0,0,date('m'),date('d'),date('Y')-150));

            if(date("Y-m-d",strtotime($pval['dob'])) > date("Y-m-d",mktime(0,0,0,date('m'),date('d'),date('Y')-10))){

            $missing_labels['dob'] = "Date of birth - Invalid";//.date("Y-m-d",strtotime($pval['dob']));

            }
            if( (strlen($pval['contact_no']) != 10) && ($pval['contact_no'] != '') || ($contactFirstLetter == 0)|| ($contactFirstLetter == 1 ) || ($contactFirstLetter == 2 ) ||  ($contactFirstLetter == 3 ) || ($contactFirstLetter == 4 ) || ($contactFirstLetter == 5 )  ) {

				$missing_labels['contact_no'] = "Please enter valid mobile number";
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
                \"content\" : \"".CUSTOMER_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
				\"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"from_module\":\"".$from_module."\", \"search\":\"".$search_args."\",\"pledge_id\" : \"".$pledge_id."\",\"from_type\":\"".$from_type."\"}
               }";

            exit;
            break;

   case 'add':
   case 'update':

            if(strstr($pval['photo'],$_SERVER['SERVER_NAME'])){
            $file_fld=explode($_SERVER['SERVER_NAME']."/",$pval['photo']);
            $photo_name=$file_fld[1];
            }
            else{
            $photo_name=$pval['photo'];
            }

            // Code Modified By Moses C Rathina Kumar on 29/Sep/2011
            if(strstr($pval['sign'],$_SERVER['SERVER_NAME'])){
            $file_fld=explode($_SERVER['SERVER_NAME']."/",$pval['sign']);
            $sign_name=$file_fld[1];
            }
            else{
            $sign_name=$pval['sign'];
            }
            //echo "'".$photo_name ." - ".$sign_name."';";
        if($type == 'add') {
			//print_r($pval);
			//exit;

            //if(Customer::isExistCustomer($pval['email'])){}
            $pval['dob'] = date('Y-m-d', strtotime($pval['dob']));
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $app->add($pval);
            $uval['id'] = $app->last_inserted_id();


            if(file_exists(CUSTOMER_IMAGE_PATH.$uval['id'])) deleteFolder(CUSTOMER_IMAGE_PATH.$uval['id']);
            mkdir(CUSTOMER_IMAGE_PATH.$uval['id'],777);
            chmod(CUSTOMER_IMAGE_PATH.$uval['id'],0777);
            //echo $pval['photo'].",".CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg';
            //echo "<br>".$pval['photo'].",".CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg';

            // rename the picture using user-id
            @rename($photo_name, CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg');
            @rename($sign_name, CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'_sign.jpg');

            //update the database with the new details
            $pval['photo'] = CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg';
            $pval['sign'] = CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'_sign.jpg'; // Edit By Moses

			$test = $uval['id'];

			$query = "SELECT C.id, C.branch_id, B.name, B.id
                      FROM ".CUSTOMERS." AS C
                      LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                      WHERE C.id = $test
                      ORDER BY C.id DESC ";
			$result = $app->getrows($query);
			$branch_name = $result[0]['name'];

			/*Edited by siva - Customer id prefix based on location*/
			$customer_id_prefix = substr($branch_name,0,3);
			$pval['customer_id'] = $customer_id_prefix.''.$uval['id'];
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/

            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = CUSTOMER_SAVED;


        } else {
            //echo $pval['photo'];
            //exit;
            //file_exists(CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg') ? unlink(CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg') : '';
            //echo CUSTOMER_IMAGE_PATH;

			/*Edited by priya - Customer id prefix based on branch*/
			$test = $uval['id'];

			$query = "SELECT C.id, C.branch_id, B.name, B.id
                      FROM ".CUSTOMERS." AS C
                      LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                      WHERE C.id = $test
                      ORDER BY C.id DESC ";
			$result = $app->getrows($query);
			$branch_name = $result[0]['name'];

			/*Edited by priya - Customer id prefix based on branch*/
			$customer_id_prefix = strtoupper(substr($branch_name,0,3));
			$pval['customer_id'] = $customer_id_prefix.''.$uval['id'];
//			if(file_exists(CUSTOMER_IMAGE_PATH.$uval['id'])) deleteFolder(CUSTOMER_IMAGE_PATH.$uval['id']);
            mkdir(CUSTOMER_IMAGE_PATH.$uval['id'],777);
            chmod(CUSTOMER_IMAGE_PATH.$uval['id'],0777);
			@rename($photo_name,CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg');
            @rename($sign_name,CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'_sign.jpg');
            $pval['dob'] = date('Y-m-d', strtotime($pval['dob']));
            $pval['photo'] = CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'.jpg';
            $pval['sign'] = CUSTOMER_IMAGE_PATH.$uval['id'].'/'.$uval['id'].'_sign.jpg';
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];

            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = CUSTOMER_UPDATED;


        }

        $_SESSION['success_msg'] = $success_msg;

        echo "
        {
            \"page\" : \"".$page."\",
            \"status\" : \"".$status."\",
            \"disp\" : \"".$disp."\",
            \"content\" : \"".CUSTOMER_UPDATE."\",
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\",\"customer_id\":\"".
            $uval['id']."\", \"from_type\":\"".$from_type."\",\"from_module\":\"".$from_module."\", \"pledge_id\" : \"".$pledge_id."\"}
        }";

        exit;

        /*if($type == 'add') {
        header("Location: customers.php");
        //echo "<meta http-equiv=\"REFRESH\" content=\"0;url=customers.php\">";
        //echo "<script>window.history.go(-1);</script>";
        }else{
        header("Location: customers.php?".$search_args."pageno=".$page_no."");
        //echo "<meta http-equiv=\"REFRESH\" content=\"0;url=customers.php?".$search_args."pageno=".$page_no."\">";
        //echo "<script>window.history.go(-1);</script>";
        }
        exit;*/

        break;

   case 'create':
   case 'edit':
        /*
         * Get the Customer details, if type is edit
         */
        $customer_data = Customer::Detail($uval['id']);


        $customer_data = $customer_data[0];
        //echo "<pre>";print_r($customer_data);exit;
        include ADMIN_LAYOUT_PATH . 'forms/customer.html';
        break;

   case 'refresh':
   case 'search':
   case 'paging':
   case 'back':

         $data = Customer::Listview($search_key, $search_cus_id,$search_fhw,$search_key_location,$search_branch_id,$search_contact_no);
         $count = Customer::ListviewCount($search_key, $search_cus_id,$search_fhw,$search_key_location,$search_branch_id,$search_contact_no);

         include ADMIN_LAYOUT_PATH . 'customer.html';
         exit;
         break;

   case 'delete':


        /*
         * Delete from database
         */

        if($uval['id']!="")
        {

            $name = Customer::getCustomerName($uval['id']);
			$cust_id = Customer::getCustomerCode($uval['id']);

			$status = Customer::Delete($uval['id']);
            if($status["success"])
            {
                //$message[] = $name."( MF".$uval['id']." ) - ".CUSTOMER_DELETED;
				$message[] = $name."( ".$cust_id." ) - ".CUSTOMER_DELETED;
            }
            else
            {
                //$message[] .= $name."( MF".$uval['id']." ) - ".CUSTOMER_NOT_DELETED." - ".$status['error'];
				$message[] .= $name."( ".$cust_id." ) - ".CUSTOMER_NOT_DELETED." - ".$status['error'];
            }
        }
       if(is_array($_POST["list"]))
            {
                $i=0;
                foreach($_POST["list"] as $key=>$val)
                {
                    $name = Customer::getCustomerName($val);
					$cust_id = Customer::getCustomerCode($uval['id']);

                    $status = Customer::Delete($val);
                    if($status["success"])
                    {
                        //$message[] = $name."( MF".$val." ) - ".CUSTOMER_DELETED;
						$message[] = $name."( ".$cust_id." ) - ".CUSTOMER_DELETED;
                    }
                    else
                    {
                        //$message[] .= $name."( MF".$val." ) - ".CUSTOMER_NOT_DELETED." - ".$status['error'];
						$message[] .= $name."( ".$cust_id." ) - ".CUSTOMER_NOT_DELETED." - ".$status['error'];
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
         exit;
        break;

   case 'change_status':
        /*
         * Change the stauts in database
         */
          ;
          $app->save($change_status_array, array('id = ' => $uval['id']));
          $notice = STATUS_SUCCESS;
          echo $change_status_array['status'].'~~'.$notice;
          exit;
    break;

   case 'fetch_customer_details':
        /*
         * Fetch schama description and interst type
         */

	     $customer_details = Customer::Detail($customer_id);
            echo $customer_details[0]['name']."~~".$customer_details[0]['street']."~~".$customer_details[0]['location']."~~";
            echo $customer_details[0]['postal_code']."~~".$customer_details[0]['contact_no'];
            // echo $customer_details[0][1]."~~".$customer_details[0]['street']."~~".$customer_details[0]['location']."~~";
            //  echo $customer_details[0]['postal_code']."~~".$customer_details[0]['contact_no'];
        exit;
        break;
        case "export_xls":
            $data = Customer::ListExport($search_key, $search_cus_id,$search_fhw,$search_key_location,$search_branch_id);
          
            if(count($data)>0) {     
                for($i=0;$i<count($data);$i++) {
                    $xls_row['customer_id']     = $data[$i]['customer_id'];
                    $xls_row['loan_id']         = $data[$i]['loan_id'];
                    $xls_row['street']          = $data[$i]['street'];
                    $xls_row['branch']          = $data[$i]['branch'];
                    $xls_row['name']            = $data[$i]['name'];
                    $xls_row['relation']        = $data[$i]['relation'];
                    $xls_row['location']        = $data[$i]['location'];
                    $xls_row['contact_no']      = $data[$i]['contact_no'];
                    $xls_row['reference_name']  = $data[$i]['reference_name'];
                    $xls_data[] = $xls_row;
                    array_push($xls_data); 
                }
            } else {
                $xls_data[] = array('required'=>'Start date and end date');
            }
                //echo "<pre>";print_r($a);exit;
            require_once("classes/excel.class.php");
    
            //File NAme Setting
            $prifix = date("Ymdhis");
            $file_name = "tmp/".$module_name."_".$prifix.".xls";
            // echo $file_name;exit;
            //Report Title
            if($heading_suffix == "")
                $title = $page_title." - Date:".date("d-m-Y h:i:sa");
            else
                $title = $page_title . " - ".$heading_suffix." - Date:".date("d-m-Y h:i:sa");   
            //Excel Object
            $excel = new ExcelWriter($file_name, $title);   
            //Export the XLS Report   
            $excel->exportData($xls_data, $file_name);
            // echo "z";exit;
            exit;
        break;
    default:

    default:
        /*
         * Make the pages with header and footer
         */
        $data = Customer::Listview($search_key, $search_cus_id,$search_fhw,$search_key_location,$search_branch_id,$search_contact_no);
        $count = Customer::ListviewCount($search_key, $search_cus_id,$search_fhw,$search_key_location,$search_branch_id,$search_contact_no);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'customer.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
