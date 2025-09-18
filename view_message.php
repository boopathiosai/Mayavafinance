<?php

session_start();

define ('ROOT_PATH', '');

include ROOT_PATH . 'includes/config.php';


/*
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

$uval['id'] = (isset($_GET['id'])) ? $_GET['id'] : $_POST['id'];
$dval['id'] = (isset($_GET['mid'])) ? $_GET['mid'] : $_POST['mid'];

switch($type) {
     

    default:
		/*
		* Make the pages with header and footer
		*/
		$a = $uval['id'];
		$mid = $dval['mid'];

        
        $app->access_table = MESSAGE_DETAILS;
		      		
		$app->execute("UPDATE ".MESSAGE_DETAILS." SET status=1 where id ='".$uval['id']."'");

		$selectQuery = "SELECT message_details.id,
		               	message_details.subject,
						message_details.message,
						message_details.status
						
					FROM ".MESSAGE_DETAILS." message_details 
					WHERE message_details.id = $a";
						
		$testdata = $app->getrows($selectQuery);
	    include ADMIN_LAYOUT_PATH . 'common/header.html';
		include ADMIN_LAYOUT_PATH . 'forms/view_message.html';
		include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}

?>
<script type="text/javascript" language="javascript">

function reload_page()
{
	window.document.location.href = 'inbox.php?id=<?php echo $uval['id']; ?>';
}
</script>