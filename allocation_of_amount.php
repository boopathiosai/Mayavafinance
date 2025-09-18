<?php
session_start();

define ('ROOT_PATH', '');
$page_title = 'Allocation of Branch';
$module_name = 'allocation_of_branch';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = TRANSACTIONS_CAT_ID;
?> 

<?php 
$id=$_POST['id'];
$branch_id=$_POST['branch_id'];
$data=Allocationofbranch::Shortage_branch_amt($id,$branch_id);
//print_r($data);
echo stripslashes($data['amount_need']);
//echo stripslashes($data['branch_id']);
/*if($data[0]['status']=='')
{
echo stripslashes($data['amount_need']);
}
else
echo "already amount allocated";*/

