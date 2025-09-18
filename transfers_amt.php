<?php
session_start();

define ('ROOT_PATH', '');
$page_title = 'Amount Transfer';
$module_name = 'amount_transfer';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = TRANSACTIONS_CAT_ID;
?> 

<?php 
$id=$_POST['flag_id'];
$data=Allocationofbranch::Shortage_branch_amt($id);
echo stripslashes($data['amount_need']);
?>
