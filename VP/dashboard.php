<?php
echo"success";
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Mayava Finance</title>
<link href="../css/reset.css" rel="stylesheet" type="text/css" />
<link href="../css/pop-up.css" rel="stylesheet" type="text/css" />
<link href="../css/style.css" rel="stylesheet" type="text/css" />
<link href="../css/p7exp.css" rel="stylesheet" type="text/css" />
<link href="../css/jquery.autocomplete.css" rel="stylesheet" type="text/css" />
<link rel="stylesheet" type="text/css" href="../css/anylinkmenu.css" />
<link rel="stylesheet" type="text/css" href="../css/jquery-ui.custom.css" />

<!--script src="../js/jquery-1.6.2.min.js" type="text/javascript"></script-->
 <script src="../js/jquery.min.js"></script>
<script type="text/javascript" src="../js/jquery-ui-1.8.16.custom.min.js"></script>


<script type="text/javascript" src="../js/function_common.js"></script>
<script type="text/javascript" src="../js/function_common_modules.js"></script>
<script type="text/javascript" src="../js/functions.js"></script>
<script type="text/javascript" src="../js/jquery.autocomplete.js"></script>

<script type="text/javascript" src="../js/anylinkmenu.js"></script>
<script type="text/javascript" src="../js/jquery.validations.js"></script>


<!-- calender -->
<link type="text/css" rel="stylesheet" href="../css/jscal2.css" />
<link type="text/css" rel="stylesheet" href="../css/border-radius.css" />
<link id="skin-win2k" title="Win 2K" type="text/css" rel="alternate stylesheet" href="../css/win2k.css" />
<script src="../js/jscal2.js"></script>
<script src="../js/en.js"></script>

<script type="text/javascript">
//anylinkmenu.init("menu_anchors_class") //Pass in the CSS class of anchor links (that contain a sub menu)
//anylinkmenu.init("menuanchorclass");
</script>
</head>
<body>
<div id="common-floating-message" align="center" class="header-floating" style="display:none"></div>

<div id="main-container"><!--Header cintainer starts here-->
    <div id="header">
            <div class="lftcont"><img src="images/logo.jpg" /></div>
            <div id="rhtcont">
                <div class="rht-contlft"></div>
                <div class="rht-contcenter"> <span>Welcome Mayava Admin(&nbsp;<b>Super Admin</b>&nbsp;)</span>|<a href="settings.php" >Settings </a>|<a href="my_account.php">My Account </a>| <a href="inbox.php" >Messages(0)</a> |<a href="privillege.php">Privilege</a>|<a href="logout.php">Logout</a> </div>
              <div class="rht-contrht"></div>
      </div>
                <div class="clear clearfix"></div>
                      <div id="menuwrapper">
            <ul id="p7menubar">
              <li class=active><a href="dashboard.php" class="trigger">Dashboard</a></li>
              <li ><a href='javascript:' class='trigger'>Masters</a><ul><li><a href='branches.php'>Branch</a></li><li><a href='departments.php'>Department</a></li><li><a href='users.php'>User</a></li><li><a href='customers.php'>Customer</a></li><li><a href='categories.php'>Category</a></li><li><a href='products.php'>Product</a></li><li><a href='schemes.php'>Scheme Management</a></li><li><a href='sub_branches.php'>Sub Branch</a></li><li><a href='sub_branch_loanid_generate.php'>Sub Branch Loadid Generate</a></li><li><a href='leaves.php'>Leave</a></li><li><a href='duplicate_remove.php'>Duplicate Remove</a></li><li><a href='product_pre_name.php'>Pre Name</a></li><li><a href='product_with.php'>Product With</a></li><li><a href='service_charges.php'>Service Charge</a></li></ul></li><li ><a href='javascript:' class='trigger'>Transactions</a><ul><li><a href='product_pledge.php'>Product Pledge</a></li><li><a href='pay_interest.php'>Pay Interest</a></li><li><a href='renewal.php'>Renewal</a></li><li><a href='redemption.php'>Redemption</a></li><li><a href='partial_amount_payment.php'>Partial Amount Payment</a></li><li><a href='pay_excess_amount.php'>Excess amount to customer</a></li><li><a href='cash_flow_transactions.php'>Cash Flow Transactions</a></li><li><a href='auction_management.php'>Auction Management</a></li><li><a href='raise_request.php'>Raise Request for Money shortage</a></li><li><a href='allocation_of_branch.php'>Allocation of Branch</a></li><li><a href='amount_transfer.php'>Amount Transfer</a></li><li><a href='amount_received.php'>Amount Received</a></li><li><a href='bank_transaction_branch_level.php'>Bank Transaction Branch level</a></li></ul></li><li ><a href='javascript:' class='trigger'>Reports</a><ul><li><a href='detailed_ledger_report.php'>Detailed Ledger Report</a></li><li><a href='income_reports_dev.php'>Income report V2</a></li><li><a href='daily_report_dev.php'>Daily report V2</a></li><li><a href='loan_amount_rate_wise_report.php'>Loan Amount & Rate wise report</a></li><li><a href='customer_pledged_report.php'>Customer Product Pledged Report</a></li><li><a href='stock_report.php'>Stock Report</a></li><li><a href='cash_flow_reports.php'>Cash flow reports</a></li><li><a href='pre_auction_report.php'>Pre-Auction Report</a></li><li><a href='post_auction_report.php'>Post Auction Report</a></li><li><a href='log_report.php'>Log Report</a></li><li><a href='summary_reports.php'>Summary Reports</a></li><li><a href='category_wise_report.php'>Category Wise Report</a></li><li><a href='detailed_stock_report.php'>Detailed Stock Report</a></li><li><a href='tally_report.php'>Tally Report</a></li></ul></li><li ><a href='javascript:' class='trigger'>Prints</a><ul><li><a href='customer_slip.php'>Customer Slip</a></li><li><a href='customer_receipt.php'>Customer Receipt</a></li><li><a href='notification_letter.php'>Reminder Notification Letter</a></li><li><a href='customer_id_report.php'>Customer ID Print</a></li><li><a href='pre_auction_notice.php'>Pre Auction Notice</a></li><li><a href='auction_final_notice.php'>Autcion Final Notice </a></li><li><a href='auction_notice.php'>Auction Notice</a></li></ul></li>                            	<li ><a href="customers_details.php" class="trigger">Customers Details</a></li>
                          </ul>
            <br class="clearit" />
      </div>
      <div class="clear clearfix"></div>

    </div>
	<script>
		$(document).ready(function() {
			msg_load();
		});

		function msg_load(){

			setTimeout(function(){
				jQuery.ajax({
				type:"POST",
				url:"message.php",
				data:{
					type : "message_alert",
					mod : "Ajax"
				},
				success : function(msg){
					if(msg != 1) {
						show_status(msg);
					}
					//return false;
				}
			});
				msg_load();
			},300000);
		}
	</script>

	 
   <div class="breadcrumbs">
    <table width="98%" border="0" align="center" cellpadding="0" cellspacing="0">
      <tr>
        <td ><a href="javascript:;">Home</a> &raquo;<a href="javascript:;">Dashboard</a> </td >
        <td >
        
        <!--<span class="btn-rht"><a href="javascript:;" class="greybtn">SAVE</a><a href="javascript:;" class="greybtn">RESET</a> <a href="javascript:;" class="greybtn">BACK</a></span>-->
        
        </td>
      </tr>
      </table>
 </div>

  <div id="contentarea">  
    
    <table class="frm-cntr" width="98%" border="0" align="center" cellpadding="0" cellspacing="0">      
      <tr>
        <td class="title">Dashboard</td>
      </tr>
      <tr>
            <td>
            <div class="calagory-rate">
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
      <td>&nbsp;</td>

    <td colspan="2" class="hding">Category Rates</td>
    </tr>
  <tr class="lit-grey">
  <td width="3%">&nbsp;</td>
    <td width="55%" ><strong>Category Name</strong></td>
    <td width="42%"><strong>Rates</strong></td>
  </tr>
    <tr  >
      <td>&nbsp;</td>

    <td>916 ஹால்மார்க்<!-- 916 ஹால்மார்க் --></td>
    <td>6,500.00</td>
  </tr>
    <tr class="lit-grey" >
      <td>&nbsp;</td>

    <td>கெட்டி அயிட்டம்<!-- கெட்டி அயிட்டம் --></td>
    <td>5,950.00</td>
  </tr>
    <tr  >
      <td>&nbsp;</td>

    <td>பொடி அயிட்டம்<!-- பொடி அயிட்டம் --></td>
    <td>5,600.00</td>
  </tr>
    <tr class="lit-grey" >
      <td>&nbsp;</td>

    <td>ஒரு கிராமுக்கு கீழ<!-- ஒரு கிராமுக்கு கீழ --></td>
    <td>4,700.00</td>
  </tr>
  </table>

            
            </div>
            <div class="admin-msg">
            
            <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
      <td width="3%">&nbsp;</td>

    <td colspan="3" class="hding">Latest Messages</td>
    </tr>
  <tr class="lit-grey">
      <td>&nbsp;</td>

    <td width="19%"><strong>Date </strong></td>
    <td width="28%"><strong>Subjects</strong></td>
    <!--<td width="29%"><strong>Message</strong></td>-->
    <td width="21%"><strong>Status</strong></td>
  </tr>
  
    
  <tr  >
    <td>&nbsp;</td>
    <td>13-Jul-25</td>
    <td>Money Shortage Request</td>
    <!--<td>Dear Admin,
       ...</td>-->
    <td>Open</td>
  </tr>
   </table>
            </div>
<div class="clear clearfix"></div>

    <div class="scheme-det">
    <table width="100%" border="0" cellpadding="0" cellspacing="0">
  <tr>
  <td width="0%">&nbsp;</td>
    <td colspan="7" class="hding">Scheme Details</td>
    </tr>
    
  <tr class="lit-grey">
  <td>&nbsp;</td>
    <!--<td width="16%"><strong>Scheme ID</strong></td>-->
    <td width="20%"><strong>Scheme Name</strong></td>
    <td width="20%"><strong>Scheme Type</strong></td>
    <td width="20%"><strong>Loan Rate</strong></td>
    <td width="25%"><strong>Description</strong></td>
    <td width="20%"><strong>ROI</strong></td>
    <td width="9%"><strong>Status</strong></td>
  </tr>
    <tr  >
    <td>&nbsp;</td>
    <!--<td>5</td>-->
    <td>1.5% தினசரி வட்டி</td>
    <td>Flat</td>
    <td>6,500.00</td>
    <td>gold loan</td>
    <td>18%</td>
    <td>enable</td>
  </tr>
     <tr class="lit-grey" >
    <td>&nbsp;</td>
    <!--<td>4</td>-->
    <td>வெள்ளி 3 தினசரி வட்டி</td>
    <td>Flat</td>
    <td>35.00</td>
    <td>silver loan</td>
    <td>36%</td>
    <td>enable</td>
  </tr>
     <tr  >
    <td>&nbsp;</td>
    <!--<td>3</td>-->
    <td>வெள்ளி 2 தினசரி வட்டி</td>
    <td>Flat</td>
    <td>21.00</td>
    <td>silver loan</td>
    <td>24%</td>
    <td>disable</td>
  </tr>
     <tr class="lit-grey" >
    <td>&nbsp;</td>
    <!--<td>2</td>-->
    <td>வெள்ளி 5 தினசரி வட்டி</td>
    <td>Flat</td>
    <td>21.00</td>
    <td>silver loan</td>
    <td>60%</td>
    <td>disable</td>
  </tr>
     <tr  >
    <td>&nbsp;</td>
    <!--<td>1</td>-->
    <td>2% தினசரி வட்டி</td>
    <td>Flat</td>
    <td>2,200.00</td>
    <td>Gold Loan</td>
    <td>24%</td>
    <td>disable</td>
  </tr>
   </table>

    
    

</div>
</td>
      </tr>
    </table>
    <div class="clear clearfix"></div><!--contentarea ends here-->
  </div>

<div id="footer">
                <div class="ftr-content">
                <p>Copyright © 2011 <a href="https://webmail.pair.com" target="_blank">Mayava Finance</a>, All Rights Reserved. Design: <a href="http://www.angleritech.com/" target="_blank">ANGLER Technologiess</a></p></div>
            
            </div>
   
    
</div>
<!-- Dialog Divs : Edited By Moses C Rathina Kumar on 29 Sep 2011 -->
<div id="print_dialog" title="Print Preview"></div>
<div id="alert_box" title="Alert Dialog"></div>
<div id="confirm_box" title="Confirm Dialog"></div>
<div id="formact_dialog" title="Form Action Dialog"></div>
<div id="page_loading">
<div id="loading-container">
<div class="loading">
<p>Loading.....</p><br>
</div>
<div class="clear clearfix"></div>
</div></div>

<!--main container ends--> 
</body>
</html>
<script type="text/javascript" src="../js/tabnavi.js"></script>
<script>
//Dialog JS Jquery Configuration
jQuery(document).ready(
    function(){
        
    jQuery("#calender_trigger").click(function(){
            jQuery(".calender").click();
        }); 

        
    }

);


</script>