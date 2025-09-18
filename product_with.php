<?php

session_start();

define ('ROOT_PATH', '');
$page_title = 'Product With';
$module_name = 'product_with';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = MASTERS_CAT_ID;

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
 * Get values from js
 */
$pval = $_POST['pval']; 
//$pval['pre_name'] = $_POST['pval']['name'];


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
$search_args = "";
if($search_key != ""){
    $search_args.="search_key=".$search_key."&";
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

$app->access_table = PRODUCT_WITH;

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
            /*$missing_vals = Validator::validate_data('Product');
            if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Product::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }
            echo '~~~~1';
            exit;*/


             $missing_labels = Validator::validate_json('ProductWith');
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
                \"content\" : \"".PRODUCT_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";

            exit;
            break;

   case 'add':
   case 'update':

       
        if($type == 'add') {
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $app->add($pval);
            //update the database with the new details
            $uval['id'] = $app->last_inserted_id();
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = WITH_SAVED;

                    
        } else {
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $status = $app->save($pval, array('id = ' => $uval['id']));
            $success_msg = WITH_UPDATED;

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
        exit;
        break;

   case 'create':
   case 'edit':
        /*
         * Get the Department details, if type is edit
         */
        $product_data = ProductWith::Detail($uval['id']);
        $product_data = $product_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/product_with.html';
        break;
   
   case 'refresh':  
   case 'search':
   case 'paging':
   case 'back':
          $data = ProductWith::Listview($search_key);
          $count = ProductWith::ListviewCount($search_key);
          include ADMIN_LAYOUT_PATH . 'product_with.html';
          exit;
          break;

   case 'delete':

        /*
         * Delete from database
         */
        
        if($uval['id']!="")
        {
            
            $name = ProductWith::getProductName($uval['id']);
            
            $status = ProductWith::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".WITH_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".WITH_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".PRODUCTS_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = ProductWith::getProductName($val);
                    $status = ProductWith::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".WITH_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".WITH_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".WITH_NOT_DELETED." - ".$status['error'];
                        
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
    default:
        /*
         * Make the pages with header and footer
         */
        $data = ProductWith::Listview($search_key);

      //print_r($data); exit;
        
        $count = ProductWith::ListviewCount($search_key);
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'product_with.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>