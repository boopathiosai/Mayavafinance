<?php  error_reporting(0);
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Config
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Vinothkuamr V
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  05-Feb-2010
 //**************************************************************************
 // Description      :  This is the Config file  for Mayavafinance.
//**************************************************************************


define('ADMIN_LAYOUT_PATH', ROOT_PATH . 'layouts/default/');
define('ADMIN_IMAGE_PATH', ROOT_PATH . 'images/');
define('ADMIN_LIBRARY_PATH', ROOT_PATH . 'libs/');
define('LAYOUT_PATH_MAIL', ROOT_PATH . 'layouts/default/mails/');
define('CUSTOMER_IMAGE_PATH', ROOT_PATH . 'customer/images/');
define('SETTINGS_PATH', ROOT_PATH . 'includes/settings/');
date_default_timezone_set('Asia/Kolkata');
require_once ROOT_PATH.'includes/common.php';
$app = new Application();

$html_root_path = find_root_path();


require_once ROOT_PATH . 'includes/settings/settings.php';

define("MINIMUM_LOAN_AMOUNT",100);


$CashFlowPurposes = array(
                        "cashin"=>array(
                                "Rent - Shop Expenses",
                                "Staff Welfare Expenses",
                                "Salary Bonus Expenses",
                                "Cheque received",
                                "Advance Received"
                                ),
                        "cashout"=>array(
                                "Rent - Shop Expenses",
                                "Staff Welfare Expenses",
                                "Salary Bonus Expenses",
                                "Salary Paid to the staff",
                                "Cheque given",
                                "Advance paid",
                                "Tea Expenses"
                                )
                        );


//setlocale(LC_MONETARY, 'en_UK');

$Customer_IDProof = array(
                        "Aadhar Card" => "Aadhar Card",
                        "Ration Card"=>"Ration Card",
                        "Pan Card"=>"Pan Card",
                        "Voter ID"=>"Voter ID",
                        "Driving License"=>"Driving License",
                        "Bank Pass Book"=>"Bank Pass Book",
                        "Other"=>"Others (Specify)"
                        );


$Relation_Type = array(
                        "Father"=>"Father",
                        "Mother"=>"Mother",
                        "Husband"=>"Husband",
                        "Wife"=>"Wife",
                        "Son"=>"Son",
                        "Daughter"=>"Daughter"
                        );
$Relation_category = array(
                        "F/o"=>"F/o",
                        "M/o"=>"M/o",
                        "H/o"=>"H/o",
                        "W/o"=>"W/o",
                        "S/o"=>"S/o",
                        "D/o"=>"D/o"
                        );

$Monhtly_Income = array(
                        "0 to 5000"=>"0 to 5000",
                        "5001 to 15000"=>"5001 to 15000",
                        "15001 and above"=>"15001 and above"
                        );
