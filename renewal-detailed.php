<?php
ob_start();
session_start();

define ('ROOT_PATH', '');
$page_title = 'Product Pledge';
$module_name = 'product_pledge';

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
    //echo"not accessible";
	header('Location: index.php');
	exit;
}


/*
 * Get values from js
 */
$pval = $_POST['pval'];
$name = $_POST['name'];
$gross_weight = $_POST['gross_weight'];
$category_id = $_POST['category_id'];
$net_weight = $_POST['net_weight'];
$market_rate = $_POST['market_rate'];
$product_id=$_POST["product_id"];
$item_count = $_POST['item_count']; 

$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
//exit;
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];
(int)$scheme_id=$_POST['scheme_id'];
$customer_id =(isset($_GET['customer_id'])) ? $_GET['customer_id'] : $_POST['customer_id'];
/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_category = (isset($_GET['search_category'])) ? $_GET['search_category'] : $_POST['search_category'];
$search_cus_key = (isset($_GET['search_cus_key'])) ? $_GET['search_cus_key'] : $_POST['search_cus_key'];
$search_cus_id = (isset($_GET['search_cus_id'])) ? $_GET['search_cus_id'] : $_POST['search_cus_id'];
$search_key_location = (isset($_GET['search_key_location'])) ? $_GET['search_key_location'] : $_POST['search_key_location'];
$search_fhw = (isset($_GET['search_fhw'])) ? $_GET['search_fhw'] : $_POST['search_fhw'];

$search_args = "";
if($search_key != ""){
	$search_args.="search_key=".$search_key."&";
}
if($search_category != ""){
	$search_args.="search_category=".$search_category."&";
}
if($search_cus_key != ""){
	$search_args.="search_cus_key=".$search_cus_key."&";
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
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);

switch($type) {

   case 'formvalidation':

            /*
			 * Form validation here . notify error, if exists.
   		     */
			$missing_vals = Validator::validate_data('Renewal');
			if (!empty( $missing_vals )) {
    			$missing_data = explode('@@@', $missing_vals);
    			$require_data = Scheme::${$missing_data[1]};
    			echo $require_data.'~~' . $missing_data[0].'~~0';
    			exit;
	    	}
			echo '~~~~1';
			exit;
            break;

   case 'add':
   case 'update':
  //echo "<script language='javascript'> alert('dfgdfgdfgdf'); </script>";
		if($type == 'add') {

			$pval['created_datetime'] = date('Y-m-d H:i:s');
			$pval['created_by'] = $_SESSION['session_userid'];
            $app->add($pval);
			$uval['id'] = $app->last_inserted_id();

			$query = "UPDATE ".SETTINGS." SET config_value = config_value+1 WHERE config_name='loan_count'";
            $app->execute($query);
			//header("Location:renewal.php");
		} else {
			$pval['modified_datetime'] = date("Y-m-d H:i:s");
			$pval['modified_by'] = $_SESSION['session_userid'];
			$app->access_table = PLEDGE_DETAILS;
			$sval['pledge_id'] = "'".$uval['id']."'";
			$app->delete($sval);
			$app->save($pval, array('id = ' => $uval['id']));
			$_SESSION['success_msg'] = PRODUCT_UPDATE;
		}
			$dval['pledge_id'] = $uval['id'];
			$total_weight = 0;
            $total =$item_count;
			$dval['created_datetime'] = date('Y-m-d H:i:s');
			$dval['created_by'] = $_SESSION['session_userid'];
			$ordering = 1;
			$app->access_table = PLEDGE_DETAILS;

			for($i=0;$i<$total;$i++)
			{
				$dval['product_id'] =$product_id[$i];
				$dval['category_id'] = $category_id[$i];
				$dval['gross_weight'] = $gross_weight[$i];
				$dval['net_weight'] = $net_weight[$i];
				$dval['market_rate'] = $market_rate[$i];
				$dval['ordering'] = $ordering;
				//$dval['status'] = $status[$i];
				$total_weight += $net_weight[$i];
				$app->add($dval);
				$ordering++;
			}
			$app->access_table = PLEDGE;
			$val['total_weight'] = $total_weight;
			$val['item_count'] = $total;
			$app->save($val, array('id = ' => $uval['id']));
		$_SESSION['success_msg'] = PRODUCT_PLEDGE_UPDATE;
        header("Location: renewal.php?".$search_args."pageno=".$page_no."");
		Renewal::WritePledgeInfo();
		exit;
		break;

   case 'create':
   case 'edit':
   		/*
   		 * Get the Department details, if type is edit
   		 */

   		$product_details = Renewal::Detail($uval['id']);
        $product_data = $product_details[0];
   		include ADMIN_LAYOUT_PATH . 'forms/renewal.html';
		break;
   case 'paging':
   case 'back':
          $data = Renewal::Listview($search_key,$search_category,$search_cus_key,$search_cus_id,$search_key_location,$search_fhw);
		  $count = Renewal::ListviewCount($search_key,$search_category,$search_cus_key,$search_cus_id,$search_key_location,$search_fhw);
  		  include ADMIN_LAYOUT_PATH . 'renewal.html';
          exit;
          break;
   case 'delete':
   		/*
   		 * Delete from database
   		 */
   		$app->delete($uval);
        $notice = PRODUCT_PLEDGE_DELETE;
		$app->access_table = PRODUCTS;
   		$data = Renewal::Listview($search_key,$search_category,$search_cus_key,$search_cus_id,$search_key_location,$search_fhw);
		$count = Renewal::ListviewCount($search_key,$search_category,$search_cus_key,$search_cus_id,$search_key_location,$search_fhw);
		$_SESSION['success_msg'] = $notice;
   		include ADMIN_LAYOUT_PATH . 'renewal.html';
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
			  echo $scheme_details[0]['description']."~~".$scheme_details[0]['interest_type']."~~".$scheme_details[0]['service_charge'];
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
	//	echo "yes";
		//	$id_cate=$_GET['id'];
		//	$category_data_js = get_dropdown_list_with_field('id', 'category', CATEGORIES); 	
		//	echo $category_options_js = build_select_options($category_data_js);
		//$category_options .= " <optgroup label='With Stone'>";
		//$category_data = get_dropdown_with_optgroup_for_pledge('id', 'category','1', CATEGORIES);
		//$category_options .= build_select_options($category_data,$product_data['category_id']); 
		//$category_options .= " <optgroup label='Without Stone'>";
		$category_data = get_dropdown_list_with_field('id', 'category', CATEGORIES);
		$category_options .= build_select_options($category_data,$product_data['category_id']);
		echo $category_options;
      exit;
      break;
	 case 'fetch_product_data':
			$cate_id  =$_POST['queryString'];
			$pro_name =$_GET['cate_id'];
			$app->access_table = PRODUCTS;
			$data =Renewal::AutocompleteDetails(65,"r");
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

		$data = Renewal::Listview($search_key,$search_category,$search_cus_key,$search_cus_id,$search_key_location,$search_fhw);
		$count = Renewal::ListviewCount($search_key,$search_category,$search_cus_key,$search_cus_id,$search_key_location,$search_fhw);

		include ADMIN_LAYOUT_PATH . 'common/header.html';
		include ADMIN_LAYOUT_PATH . 'renewal.html';
		include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>