<?php

session_start();
define ('ROOT_PATH', '');

$page_title = 'Inbox';
$module_name ='inbox';
include ROOT_PATH . 'includes/config.php';
$menu = Privillege::getModuleDetail($_SESSION[$app->session_usertype1]);

/**
 * Redirect to index page if not logged in
 **/
$mod = (isset($_GET['mod'])) ? $_GET['mod'] : $_POST['mod'];
if($app->is_logged_in() !== true) {
	if($mod == 'Ajax') {
			echo "session_timed_out";
			exit;
	}
	else{
			header('Location: index.php');
			exit;
	}
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


if($page_no <= 0) {
	$page_no = 1;
}
if($rg <= 0) {
	$rg = 1;
}
$page_no = (int)$page_no;
//echo $page_no;
/*
 * Class Instances
 */

$app->access_table = MESSAGE_DETAILS;

/*
 * Assign limit to display records
 */
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);
switch($type)
{
		case 'refresh':
        case 'paging':
        case 'back':
   	    $data = Message::Listview();
		$count = Message::ListviewCount();
    	include ADMIN_LAYOUT_PATH . 'inbox.html';
       	exit;
        break;

        case 'change_status':

  		$app->save($change_status_array, array('id = ' => $uval['id']));
	    $notice = STATUS_SUCCESS;
        echo $change_status_array['status'].'~~'.$notice;
        exit;
        break;

		case 'delete':
		/*$app->delete($uval);
        $notice = MESSAGE_DELETE;
   		$data = Message::Listview();
		$count = Message::ListviewCount();
		$_SESSION['success_msg'] = $notice;
   		include ADMIN_LAYOUT_PATH . 'inbox.html';
   		exit;*/
		 if(is_array($_POST["list"]))
            {
                $i=0;
                
                foreach($_POST["list"] as $key=>$val)
                {
                    
                    $name = Message::getMessageId($val);
                    $status = Message::Delete($val);
                    
                    if($status["success"])
                    {
                        $message[] = $name."( ".$val." ) - ".INBOX_MESSAGE_DELETED;
                    }
                    else
                    {
                        
                        if(is_array($status['error'])){
                            
                            foreach($status['error'] as $err){
                                
                                $message[] .= $name."( ".$val." ) - ".INBOX_MESSAGE_NOT_DELETED." - ".$err;
                            
                            }
                        
                        }else{
                        
                            $message[] .= $name."( ".$val." ) - ".INBOX_MESSAGE_NOT_DELETED." - ".$status['error'];
                        
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

		default:
		include ADMIN_LAYOUT_PATH . 'common/header.html';
		$data = Message::Listview();
		$count = Message::ListviewCount();
		include ADMIN_LAYOUT_PATH . 'inbox.html';
		include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
?>