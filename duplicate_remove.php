<?php

session_start();

define ('ROOT_PATH', '');
$page_title = 'Duplicate Remove';
$module_name = 'duplicate_remove';

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
        //echo"not accessible";
        //header('Location: index.php');
        //exit;
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

/*
 * Get search options
 */
$search_key = (isset($_GET['search_key'])) ? $_GET['search_key'] : $_POST['search_key'];
$search_branch_id = (isset($_GET['search_branch_id'])) ? $_GET['search_branch_id'] : $_POST['search_branch_id'];
$search_args = "";
if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
}
if($search_branch_id != ""){
    $search_args.="search_branch_id=".$search_branch_id."&";
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

$app->access_table = "mf_duplicate_backup";

/*
 * Assign limit to display records
 */
// Paging::set_per_page(ROWS_PER_PAGE);
// Paging::set_current_page($page_no);
switch($type) {

    case 'formvalidation':

            /*
             * Form validation here . notify error, if exists.
             */
          
             $missing_labels = Validator::validate_json('DuplicateRemove');
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
                \"content\" : \"".LEAVE_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;
            break;   

   case 'refresh':  
   case 'search':   
   case 'paging':
   case 'back':
         $data = DuplicateRemove::Search($search_key);
		 if($data == "ok"){
			 $data = "Duplicate Record Removed Sucessfully";
		 }
         include ADMIN_LAYOUT_PATH . 'duplicate_remove.html';
         exit;
         break;

   case 'change_status':
        /*
         * Change the stauts in database
         */
//        echo "<pre>"; print_r($change_status_array); print_r($uval); 
          $app->save($change_status_array, array('id = ' => $uval['id']));
          $notice = STATUS_SUCCESS;
          echo $change_status_array['status'].'~~'.$notice;
          exit;
        break;

    default:
        /*
         * Make the pages with header and footer
         */
       // $menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);
       // $data = Leave::Listview($search_key,$search_branch_id);
        //$count = Leave::ListviewCount($search_key,$search_branch_id);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'duplicate_remove.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
    break;
}

?>