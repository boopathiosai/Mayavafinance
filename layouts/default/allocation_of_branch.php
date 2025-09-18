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
$page_title = 'Allocation of Branch';
$module_name = 'allocation_of_branch';

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
        //echo"notaccessible";
        header('Location: index.php');
        exit;   
}
 

/*
 * Get values from js
 */
$page = (isset($_GET['page'])) ? $_GET['page'] : $_POST['page'];
$pval = isset($_POST['pval']) ? $_POST['pval'] : $_GET['pval']; 

$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$type = (isset($_GET['type'])) ? $_GET['type'] : $_POST['type'];
$page_no =  (isset($_GET['pageno'])) ? $_GET['pageno'] : $_POST['pageno'];
$rg =  (isset($_GET['rg'])) ? $_GET['rg'] : $_POST['rg'];
$change_status_array['status'] =  (isset($_GET['status'])) ? $_GET['status'] : $_POST['status'];
 
$disp = ($disp!='') ? $disp : 'content_div';

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

$app->access_table = AMOUNT_TRANSFER;

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
            $missing_vals = Validator::validate_data('Allocationofbranch');
            if (!empty( $missing_vals )) { 
            $missing_data = explode('@@@', $missing_vals);
            $require_data = Customer::${$missing_data[1]};
            echo $require_data.'~~' . $missing_data[0] . '~~0';
            exit;
            }   
            echo '~~~~1'; 
            exit;  
            break;
            
   case 'save':
   	     	//message details
            
            $data = Allocationofbranch ::Msg_details($pval['request_branch_id']);
		
			for($i=0;$i<count($data);$i++)
            {
            $msg_det['branch_id']=$data[$i]['branch_id'];
			$msg_det['sender']=$data[$i]['user_id'];
			$msg_det['from']=$data[$i]['user'];
			$msg_det['num_recipients']=count($data);
			$msg_det['subject']="Amount Allocation";
			$msg_det['message'] ="Amount Allocated Sucessfully"."\n"."Branch Name:".$data[$i]['branch_name']."\nTransfer Amount:".$pval['transfer_amount']."\n";
		    $msg_det['sent_date'] = date('Y-m-d H:i:s');
            $app->access_table =MESSAGE_DETAILS;
			$app->add($msg_det);

            $msgid=$app->last_inserted_id();
            $msg_list['message_id']=$msgid;
			$msg_list['recipient_id']=$data[$i]['user_id'];
            $msg_list['recipient_name']=$data[$i]['recipient_name'];
            $msg_list['email']=$data[$i]['email'];
            $app->access_table = MESSAGE_LIST;
            $app->add($msg_list);
            }
            
    		//Mail functions
			//get the superadmin mailid
			
			$content = "Amount Allocated <br><br>";
			$content .= "<em>Amount Allocation of ".$data[0]['branch_name']." Branch:</em><br>
			----------------------------------------------------------------<br>			
			<strong>Branch Name			    : </strong>".$data[0]['branch_name']." <br>			
			<strong>Shortage Amount			: </strong> ".$pval['transfer_amount']."<br>
			----------------------------------------------------------------<br><br>";	 
			
			$content .= "Best Regards,"."<br>"."-- The Mayava Finance Administrator"."<br>";
			$content .= "<a href='".$html_root_path."'>".$html_root_path."</a>";
			
			$mail = new PHPMailer();
			$mail->SetFrom(ADMIN_EMAIL, ADMIN_NAME);
     		$mail->AddAddress($_SESSION['session_useremail'],$data['0']['email']);
			$mail->Subject    = "Mayava Finance |  Amount Allocation Info";
			$mail->MsgHTML($content);
			$mail->Send();
			$app->access_table = MONEY_SHORTAGE;
			$sval['status'] = 0;
			$app->save($sval,array('id = ' => $pval['id'])); 
			
			//store allocated values
		    $app->access_table = AMOUNT_TRANSFER;
		    $pval['created_datetime'] = date('Y-m-d H:i:s');
            $pval['created_by'] =$_SESSION['session_userid'];
            $pval['status'] = 1;
			$app->add($pval);
		    $_SESSION['success_msg'] = ALLOCATED_SUCCESSFULLY;

            echo "
               { 
                \"page\" : \"".$page."\", 
                \"status\" : \"".$status."\", 
                \"disp\" : \"".$disp."\", 
                \"content\" : \"\", 
                \"isAlert\" : \"".true."\" ,
                \"args\" : \"\" 
               }";            
            exit;
            
        break;
     
   case 'allocate':
        /*
         * Get the Customer details, if type is edit
         */
         
        include ADMIN_LAYOUT_PATH . 'forms/allocation_of_branch.html';
        break;
        
	case 'view':
        /*
         * Get the Customer details, if type is edit
         */
        $data = Allocationofbranch::Listview($search_key, $search_cus_id);
        $count = Allocationofbranch::ListviewCount($search_key, $search_cus_id);
        include ADMIN_LAYOUT_PATH . 'allocation_of_branch.html';
        break;
        

   case 'paging':
   case 'back':
   
         $data = Allocationofbranch::Listview($search_key, $search_cus_id);
         $count = Allocationofbranch::ListviewCount($search_key, $search_cus_id);

         include ADMIN_LAYOUT_PATH . 'allocation_of_branch.html';
         #include ADMIN_LAYOUT_PATH . 'customer.html';
         exit;
         break;
         
 
     
   /*case 'change_status':
        
          $app->save($change_status_array, array('id = ' => $uval['id']));
          $notice = STATUS_SUCCESS;
          echo $change_status_array['status'].'~~'.$notice;
          exit;
        break;*/
    default:
        /*
         * Make the pages with header and footer
         */     
        $data = Allocationofbranch::Listview($search_key, $search_cus_id);
        $count = Allocationofbranch::ListviewCount($search_key, $search_cus_id);
        include ADMIN_LAYOUT_PATH . 'common/header.html'; 
        include ADMIN_LAYOUT_PATH . 'allocation_of_branch.html';
        #include ADMIN_LAYOUT_PATH . 'forms/allocation_of_branch.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>