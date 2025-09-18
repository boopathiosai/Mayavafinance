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
$page_title = 'sub_branch_loanid_generate';
$module_name = 'sub_branch_loanid_generate';

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

$app->access_table = SUB_BRANCH_LOAN_NO;

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
              $missing_labels = Validator::validate_json('Sub_branch_loanid_generate');
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
                \"content\" : \"".SUB_BRANCH_VALIDATE_ERROR."\", 
                \"isAlert\" : \"".$isMsgDisplay."\" ,
                \"args\" : {\"fields\":\"#".$fields."\",\"labels\":\"".$labels."\",\"module\":\"".$module_name."\",\"search\":\"".$search_args."\"} 
               }";
            exit;
            break;

   case 'add':
   case 'update':

        if($type == 'add') {
            
            //$pval['sub_branch_code'] = strtoupper(preg_replace("/ /","",$pval['sub_branch_code']));
            $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $app->add($pval);
            
            //update the database with the new details
            $uval['id'] = $app->last_inserted_id();
            /*$pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];*/
            $status=$app->save($pval, array('id = ' => $uval['id']));
            $success_msg = SUB_BRANCH_LOANID_SAVED;

        } else {
            $pval['modified_datetime'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $status=$app->save($pval, array('id = ' => $uval['id']));
            $success_msg = SUB_BRANCH_LOANID_UPDATED;
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
   //case 'edit':
        /*
         * Get the Branch details, if type is edit
         */

        $sub_branch_data = Sub_branch_loanid_generate::Detail($uval['id']);
        $sub_branch_data = $sub_branch_data[0];
        include ADMIN_LAYOUT_PATH . 'forms/sub_branch_loanid_generate.html';
        break;
    
   case 'refresh':
   case 'search':
   case 'paging':
   case 'back':
        $data = Sub_branch_loanid_generate::Listview();
        $count = Sub_branch_loanid_generate::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'sub_branch_loanid_generate.html';
        exit;
        break;

   case 'delete':

        /*
         * Delete from database
         */
        
        if($uval['id']!="")
        {
            
            $name = Sub_branch_loanid_generate::getSubBranchName($uval['id']);
            
            $status = Sub_branch_loanid_generate::Delete($uval['id']);
            
            if($status["success"])
            {
                $message[] = $name."( ".$uval['id']." ) - ".SUB_BRANCH_LOANID_DELETED;
            }
            else
            {
                if(is_array($status['error'])){
                
                    foreach($status['error'] as $err){

                        $message[] .= $name."( ".$uval['id']." ) - ".SUB_BRANCH_LOANID_NOT_DELETED." - ".$err;

                    }
                
                }else{
                    
                    $message[] .= $name."( ".$uval['id']." ) - ".SUB_BRANCH_LOANID_NOT_DELETED." - ".$status['error'];
                
                }
            }
        }
        
       if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = Sub_branch_loanid_generate::getSubBranchName($val);
                    $status = Sub_branch_loanid_generate::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".SUB_BRANCH_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".SUB_BRANCH_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".SUB_BRANCH_NOT_DELETED." - ".$status['error'];
                        
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
        $app->save($change_status_array, array('id = ' => $uval['id']));
        $notice = STATUS_SUCCESS;
        echo $change_status_array['status'].'~~'.$notice;
        exit;
        break;
    case 'sub_branch_id':

         $sub_branch_id=$_GET['sub_branch_id'];
        if($sub_branch_id!='')
        {      
            $app = $GLOBALS['app'];
            $app->access_table = SUB_BRANCH_LOAN_NO;
            $res = $app->find(
                        array("loan_no"),
                        array(
                            "conditions"=>array("sub_branch_id = "=>$sub_branch_id),
                            "sort"=>" id DESC",
                            "limit"=>"0,1"
                              ));
            //print_r($res);
            if(!$res){
                $query_sub_branch_data = "SELECT sub_branch_code FROM mf_sub_branch WHERE id = $sub_branch_id";
                $row=$app->getrows($query_sub_branch_data); 
                $val= $row[0]['sub_branch_code'];
                $code='0';
            }
            else{
                $val =$res[0][0];
                $code='1';
            }
        }
              echo $val.'~~'.$code;
        exit;
        break;

    case 'sub_branch_name': 

         $branch_id = $_GET['branch_id'];
         if($branch_id!='')
            {			
			$query_sub_branch_data = "SELECT id,name,sub_branch_code FROM mf_sub_branch WHERE branch_id = $branch_id";
			$data_sub_name = $app->getrows($query_sub_branch_data);  
            }
         if(count($data_sub_name)>0)
            {
                foreach ($data_sub_name as $key => $row ) {
					
                $selected = ($row['branch_id']==$branch_id) ? 'selected=" selected "' : '';

                
                $output.='<option value="'.$row[id].'" '.$selected.'>'.$row[name].'</option>';
                }
            }
			else{
				$output='<option value="">Select</option>';
			}
         echo $output;
         exit;
         break;

    default:
        /*
         * Make the pages with header and footer
         */

        include ADMIN_LAYOUT_PATH . 'common/header.html';
        $data = Sub_branch_loanid_generate::Listview();
        $count = Sub_branch_loanid_generate::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'sub_branch_loanid_generate.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>