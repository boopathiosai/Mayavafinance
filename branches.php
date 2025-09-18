<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Brand
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Branch php file  for Branch module,used to process the view,add,edit and delete in Branch module.
//**************************************************************************

session_start();

define ('ROOT_PATH', '');
$page_title = 'Branches';
$module_name = 'branches';

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
 * Redirect to index page if user is not allowed to access the page
 */
if(!is_accessible())
{
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
$list['list'] =  (isset($_GET['list'])) ? $_GET['list'] : $_POST['list'];

$pvalt =  $_POST['pvalt'];


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

$app->access_table = BRANCH;

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
            //$missing_vals = Validator::validate_data('Branch');
            /*if (!empty( $missing_vals )) {
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Branch::${$missing_data[1]};

            // handle organization validation seperatly..
            $org_result = $_POST['organization_name'] == null ? ',#organization_name' : '';

            echo $require_data.'~~' . $missing_data[0].$org_result.'~~0';
            exit;
        }
            else if($_POST['organization_name'] == null)
            {
                // handle organization validation seperatly..
                $require_data = 'Required Fields Missing!';
                $org_result = $_POST['organization_name'] == null ? '#organization_name' : '';
                echo $require_data.'~~'.$org_result.'~~0';
                exit;
            }
            echo '~~~~1';*/
              $missing_labels = Validator::validate_json('Branch');
            //  print_r($missing_labels); exit;
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
                \"content\" : \"".BRANCH_VALIDATE_ERROR."\",
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"}
               }";
            exit;
            break;

   case 'add':
   case 'update':

        if($type == 'add') {

            $pval['branch_code'] = strtoupper(preg_replace("/ /","",$pval['branch_code']));
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $pval['system_ip'] = trim($pval['system_ip']);
            // DSSK changes - out time //
            
               $time = $pval['out_time']." ".$pvalt['time_type'];
               $pval['out_time'] = date("h:i A", strtotime($time)); 
               
             // \.DSSK changes - out time //
            $app->add($pval);

            //update the database with the new details
            $uval['id'] = $app->last_inserted_id();
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $status=$app->save($pval, array('id = ' => $uval['id']));
            $success_msg = BRANCH_SAVED;

        } else {
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            
             // DSSK changes - out time //
            
               $time = $pval['out_time']." ".$pvalt['time_type'];
               $pval['out_time'] = date("h:i A", strtotime($time)); 
            
             // \.DSSK changes - out time //
            
            $status=$app->save($pval, array('id = ' => $uval['id']));
            $success_msg = BRANCH_UPDATED;
              }
            $_SESSION['success_msg']=$success_msg;

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
         * Get the Branch details, if type is edit
         */

        $branch_data = Branch::Detail($uval['id']);
        $branch_data = $branch_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/branch.html';
        break;

   case 'refresh':
   case 'search':
   case 'paging':
   case 'back':
        $data = Branch::Listview();
        $count = Branch::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'branch.html';
        exit;
        break;

   case 'delete':

        /*
         * Delete from database
         */

        if($uval['id']!="")
        {

            $name = Branch::getBranchName($uval['id']);

            $status = Branch::Delete($uval['id']);

            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".BRANCH_DELETED;
            }
            else
            {
                if(is_array($status['error'])){

                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".BRANCH_NOT_DELETED." - ".$err;

                    }

                }else{

                    $message[] .= $name."( ".$uval['id']." ) - ".BRANCH_NOT_DELETED." - ".$status['error'];

                }
            }
        }

       if(is_array($_POST["list"]))
            {
                $i=0;

                foreach($_POST["list"] as $key=>$val)
                {

                    $name = Branch::getBranchName($val);
                    $status = Branch::Delete($val);

                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".BRANCH_DELETED;
                    }
                    else
                    {

                        if(is_array($status['error'])){

                            foreach($status['error'] as $err){

                                $message[] .= $name."( ".$val." ) - ".BRANCH_NOT_DELETED." - ".$err;

                            }

                        }else{

                            $message[] .= $name."( ".$val." ) - ".BRANCH_NOT_DELETED." - ".$status['error'];

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
//       echo "<pre>"; print_r($change_status_array); print_r($uval); 
        $app->save($change_status_array, array('id = ' => $uval['id']));
        $notice = STATUS_SUCCESS;
        echo $change_status_array['status'].'~~'.$notice;
        exit;
        break;

    default:
        /*
         * Make the pages with header and footer
         */

        include ADMIN_LAYOUT_PATH . 'common/header.html';
        $data = Branch::Listview();
        $count = Branch::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'branch.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>
