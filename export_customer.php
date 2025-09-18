<?php
define ('ROOT_PATH', '');

include ROOT_PATH . 'includes/config.php';
/*define ('ROOT_PATH', '');

include ROOT_PATH . 'includes/config.php';
include_once ROOT_PATH . 'classes/sms.class.php';
$app->access_table = CUSTOMERS;

$query = "SELECT C.customer_id, C.relationship_type, C.relation_name, C.reference_name, C.status, C.name, C.contact_no, B.name as branch_name
                  FROM ".CUSTOMERS." AS C
                   LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                  WHERE C.branch_id = 7
                   ";
$results = $app->getrows($query);


if(count($results)>0){
    $i = 1;
    foreach($results as $rows){
        $xls_row['s_no']                    = $i++;
		$xls_row['code']                    = $rows['customer_id'];
        $xls_row['branch']                  = $rows['branch_name'];        
		$xls_row['customer_name']           = $rows['name'];
        $xls_row['relation_details']        = $rows['relationship_type'] . '-' .  $rows['relation_name'];
		$xls_row['mobile_no']               = $rows['contact_no'];
        $xls_row['ref_preson_name']         = $rows['reference_name'];
        $xls_row['status']                  = $rows['status'];
        $xls_data[]                         = $xls_row;
    } 
    require_once("classes/excel.class.php");
    //File NAme Setting
    $file_name = "tmp/Branch-7 Customers.xls";
    //Report Title
    $title = 'Branch-7 Customers List';
    //Other Common page and header styles
    $other["column_count"] = count($results[0]);
    //Excel Object
    $excel = new ExcelWriter($file_name, $title);
    //Column styles sheet information
    $column_style = array(
    "color"=>"#000000"
    );
    //Export the XLS Report
    $excel->exportData($xls_data, $file_name);

    exit;
}*/

$xls_row['status']                  = 'test';
$xls_data[]                         = $xls_row;
require_once("classes/excel.class.php");
$file_name = "tmp/Branch-7 Customers.xls";
$title = 'Branch-7 Customers List';
$other["column_count"] = 1;
$excel = new ExcelWriter($file_name, $title);
$excel->exportData($xls_data, $file_name);
exit;


?>