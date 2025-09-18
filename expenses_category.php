<?php
session_start();
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  ExpensesCategory
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the ExpensesCategory php file  for ExpensesCategory module,used to process the view,add,edit and delete in ExpensesCategory module.
//**************************************************************************


define ('ROOT_PATH', '');
$page_title = 'Expenses Category';
$module_name = 'expenses_category';

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
$search_category = (isset($_GET['search_category'])) ? $_GET['search_category'] : $_POST['search_category'];
$search_args = "";
if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
}
if($search_category != ""){
    $search_args.="search_category=".$search_category."&";
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

$app->access_table = EXPENSES_CATEGORY;

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
            /*$missing_vals = Validator::validate_data('ExpensesCategory');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = ExpensesCategory::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            else
            {
                // handle category and product_with duplicate..
            $require_data = 'Category Name Already exists!';
            $count = ExpensesCategory::ValidateDuplication($pval);
            if($count > 0){
                $org_result =  '#category' ;
                echo $require_data.'~~'.$org_result.'~~0';
                exit;
            }
            }
            echo '~~~~1';
            exit;*/
              $missing_labels = Validator::validate_json('ExpensesCategory');
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
                \"content\" : \"".CATEGORY_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;

            break;

   case 'add':
   case 'update':

        if($type == 'add') 
        {
            
            $pval['created_at'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $app->add($pval);

            //update the database with the new details
            $uval['id'] = $app->last_inserted_id();
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = CATEGORIES_SAVED;

        }
        else
        {
            $pval['modified_at'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = CATEGORIES_UPDATED;

        }
        $_SESSION['success_msg'] = $success_msg;

         echo "
        { 
            \"page\" : \"".$page."\", 
            \"status\" : \"".$status."\", 
            \"disp\" : \"".$disp."\", 
            \"content\" : \"".$success_msg."\", 
            \"isAlert\" : \"".false."\" ,
            \"args\" : {\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
        }";

        /*ExpensesCategory::WriteInfo();
        if($type == 'add') {
        header("Location: categories.php");
        }else{
        header("Location: categories.php?".$search_args."pageno=".$page_no."");
        }*/
        exit;

        break;

   case 'create':
   case 'edit':
        /*
         * Get the ExpensesCategory details, if type is edit
         */
   
// Example: edit page request
    if(isset($_GET['id']) && intval($_GET['id']) > 0){
        $id = intval($_GET['id']);
        $expenses_category_data = ExpensesCategory::Detail($id); // fetch row as array  
    } else {
        $expenses_category_data = array(); // Add form
    }


        // $category_data = ExpensesCategory::Detail($uval['id']); 
        // $category_data = $category_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/expenses_category.html';
        break;

   case 'refresh':  
   case 'search':
   case 'paging':
   case 'back':
        $data = ExpensesCategory::Listview();
        $count = ExpensesCategory::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'expenses_category.html';
        exit;
        break;

   case 'delete':
        
        /*
         * Delete from database
         */
        
        if($uval['id']!="")
        {
            
            $name = ExpensesCategory::getCategoryName($uval['id']);
            
            $status = ExpensesCategory::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".CATEGORIES_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){
 
                        $message[] .= $name."( ".$uval['id']." ) - ".CATEGORIES_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".CATEGORIES_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = ExpensesCategory::getCategoryName($val);
                    $status = ExpensesCategory::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".EXPENSES_CATEGORY_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".CATEGORIES_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".CATEGORIES_NOT_DELETED." - ".$status['error'];
                        
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

   case 'change_status':
        /*
         * Change the stauts in database
         */
        $app->save($change_status_array, array('id = ' => $uval['id']));
        $notice = STATUS_SUCCESS;
        echo $change_status_array['status'].'~~'.$notice;
        exit;
        break;

    default:
        /*
         * Make the pages with header and footer
         */
        $data = ExpensesCategory::Listview($search_key,$search_category);    
        $count = ExpensesCategory::ListviewCount($search_key,$search_category);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'expenses_category.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>