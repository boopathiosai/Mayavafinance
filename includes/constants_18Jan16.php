<?php
 //**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE                   
 // Module           :  Constants     
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Vinothkuamr V
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  05-Feb-2010
 //**************************************************************************
 // Description      :  This is the Constants file  for Mayavafinance.
 //**************************************************************************

//define ('$dataADMIN_EMAIL', 'subash@angleritech.com');
//define ('ADMIN_EMAIL', 'vinothkumar@angleritech.com');
define ('ADMIN_EMAIL', 'subash@angleritech.com');
define ('ADMIN_NAME', 'Mayava Finance');
 

if($_SERVER["HTTP_HOST"] == "wwa.mayavafinance.com")
{
    define ('SITE_URL','http://wwa.mayavafinance.com');
}
 
//Localization

define('NUMBER_DECIMAL_CHAR', '.');
define('NUMBER_THOUSANDS_CHAR', ',');
define('CURRENCY_SYMBOL', 'Rs.');
define('CURRENCY_SYMBOL_POS', 'before');


//Setting Constant
define('ROWS_PER_PAGE', 5);
define("SHOW_PAGE_LIST",true);

define('AUCTION_GRACE_DAYS', 0);
define('AUCTION_GRACE_MONTH', 2);
define('AUCTION_ENTRY_GRACE_DAYS', 7);


// Alert Message Save and Delete Setup
define('PRIVILEGE_ADDED','Privilege saved  successfully ');
define('PRIVILEGE_UPDATED','Privilege updated  successfully ');
define('BRANCH_SAVED','Branch saved  successfully ');
define('BRANCH_UPDATED','Branch updated  successfully ');
define('BRANCH_DELETED','Branch Deleted. All the branch regarding data transfer to the head office branch.');
define('DEPARTMENT_SAVED','Department saved successfully ');
define('DEPARTMENT_UPDATED','Department updated successfully ');
define('USER_SAVED','User saved successfully ');
define('USER_UPDATED','User updated successfully');
define('CUSTOMER_SAVED','Customer saved successfully');
define('CUSTOMER_UPDATED','Customer updated successfully');
define('CATEGORIES_SAVED','Categories saved successfully');
define('CATEGORIES_UPDATED','Categories updated successfully');
define('PRODUCT_SAVED','Product saved successfully');
define('PRODUCT_UPDATED','Product updated successfully');
define('SCHEME_SAVED','Scheme saved successfully ');
define('SCHEME_UPDATED','Scheme updated successfully ');
define('PAYINTEREST_SUCCESS','Interest payment processed successfully');
define('PAYINTEREST_UPDATE_SUCCESS','Interest payment modified');
define('PAYINTEREST_DELETED','Paid interest been deleted successfully');
define('RENEWAL_SUCCESS','Renewal successfully');
define('PARTIALPAY_SAVE_SUCCESS','Partial amount paid successfully');
define('PAYEXCESS_SAVE_SUCCESS','Excess amount paid to customer successfully');
define('SUB_BRANCH_SAVED','Sub Branch saved  successfully ');
define('SUB_BRANCH_UPDATED','Sub Branch updated  successfully ');
define('SUB_BRANCH_DELETED','Sub Branch Deleted');

define('Product_pledge_saved','Product pledged saved successfully');
define('Product_pledge_update','Pledged product updated successfully');
define('PRODUCT_PLEDGE_DELETE','Pledged product deleted successfully');
define('BIDAUCTION_ADDED','Auction bided successfully');
define('BIDAUCTION_SAVED','Auction biding details saved successfully');
define('BIDAUCTION_DELETED','Bided auction deleted successfully');
define('CASHFLOW_ADDED','Cashflow saved successfully');
define('CASHFLOW_SAVED','Cashflow updated successfully');
define('CASHFLOW_DELETED','Cashflow delete successfully');
define('REDEEM_SUCCESS','Pledged product have been redeemed successfully');
define('Request_money_shortage_saved','Request money shortage saved successfully');
define('Request_money_shortage_update','Request money shortage updated successfully');
define('Request_money_shortage_delete','Request money shortage deleted successfully');
define('ALLOCATED_SUCCESSFULLY','Allocated successfully');
define('AMOUNT_TRANSFERED','Amount tranfered successfully');
define('AMOUNT_RECEIVED','Amount received successfully');
define('Bank_transaction_saved','Bank transaction saved successfully');
define('BANK_TRANSFER_DELETE','Bank transaction deleted successfully');
define('Bank_transaction_update','Bank transaction updated successfully');
define('NO_PRIVILEDGE','You have no priviledge to access');
define('SETTINGS_SAVE','Settings saved successfully');
define('SETTINGS_UPDATE','Settings updated successfully');
define('MESSAGE_SEND_SUCESSFULLY','Message send sucessfully');
//Validation Messages
//Masters
define('BRANCH_VALIDATE_ERROR','Branch data(s) invalid !');
define('SUB_BRANCH_VALIDATE_ERROR','Sub Branch data(s) invalid !');
define('DEPARTMENT_VALIDATE_ERROR','Department data(s) invalid !');
define('USER_VALIDATE_ERROR','User data(s) invalid !');
define('CUSTOMER_VALIDATE_ERROR','Customer data(s) invalid !');
define('CATEGORY_VALIDATE_ERROR','Category data(s) invalid !');
define('PRODUCT_VALIDATE_ERROR','Product data(s) invalid !');
define('SCHEME_MANAGEMENT_VALIDATE_ERROR','Scheme management data(s) invalid !');
define('AMOUNT_RECEIVED_VALIDATE_ERROR','Amount Received data(s) invalid !');



//Transaction
define('PRODUT_PLEGDE_VALIDATE_ERROR','Product pledge data(s) invalid !');
define('PAYINTEREST_ERROR','Pay interest data(s) invalid !');
define('RENEW_THE_PLEGDED_PRODUCT_VALIDATE_ERROR','Renew the Pleged product data(s) invalid !');
define('REDEMPTION_VALIDATE_ERROR','Redemption data(s) invalid !');
define('PARTIAL_AMOUNT_PAYMENT_VALIDATE_ERROR','Partial amount payment data(s) invalid !');
define('PAY_EXCESS_AMOUNT_VALIDATE_ERROR','Pay excess amount data(s) invalid !');
define('CASHFLOW_TRANSACTION_VALIDATE_ERROR','Cash flow transaction data(s) invalid !');
define('AUCTION_MANAGEMENT_ERROR','Auction management data(s) invalid !');
define('RAISE_REQUEST_VALIDATE_ERROR','Raise request money shortage data(s) invalid !');
define('ALLOCATION_OF_BRANCH_VALIDATE_ERROR','Allocation of branch data(s) invalid !');
define('BANK_TRANSACTION_BRANCH_LEVEL_VALIDATE_ERROR','Bank transaction branch level data(s) invalid !');
define('SETTINGS_VALIDATE_ERROR','Setting data(s) invalid !');
define('PRIVILEGE_VALIDATE_ERROR','Privilege data(s) invalid !');
define('MESSAGE_VALIDATE_ERROR','Message data(s) invalid !');
define('VALIDATE_ERROR','Data invalid !');

//Cash Flow Transaction Message
define('CASH_IN','cashin');
define('CASH_OUT','cashout');
define('PRODUCT_PLEDGED_AMOUNT','Product pledged amount');
define('BANK_TRANSACTION_CREDIT','Bank transaction credit');
define('BANK_TRANSACTION_DEBIT','Bank transaction debit');
define('RAISE_REQUEST_AMOUNT_TRANSFER','Raise request amount transfer');
define('RAISE_REQUEST_AMOUNT_RECEIVED','Raise request amount received');
define('PAY_INTEREST','Pay interest');
define('RENEWAL_INTEREST_PAYMENT','Renewal interest payment');
define('REDEMPTION_INTEREST_AND_PRINCIPLE_PAYMENT','Redemption interest and principle payment');
define('PAY_INTEREST_DELETE','Pay interest delete');
define('PARTIAL_AMOUNT_WITH_INTEREST_PAYMENT','Partial amount with interest payment');
define('PAY_EXCESS_INTEREST_PAYMENT','Pay excess interest payment');
define('PAY_EXCESS_AMOUNT_PAYMENT','Pay excess amount payment');
define('AUCTION_AMOUNT','Auction amount');
define('AUCTION_AMOUNT_LOSS','Auction amount loss');
define('AUCTION_AMOUNT_UPDATION_LOSS_AMOUNT','Auction amount updation loss amount');

define('PARTIAL_AMOUNT_RETURN_PAYMENT','Partial amount return old loan  payment');
define('PARTIAL_AMOUNT_RENEW_PAYMENT','Partial amount renewal new loan  payment');
define('PAY_EXCESS_RETURN_PAYMENT','Excess amount to customer return old loan  payment');
define('PAY_EXCESS_RENEW_PAYMENT','Excess amount to customer renewal new loan  payment');
define('RENEWAL_RETURN_PAYMENT','Renewal return old loan payment');
define('RENEWAL_PAYMENT','Renewal new loan payment');
define('REDEMPTION_PAYMENT','Redemption return loan payment');

define('CASHFLOW_PURPOSE_PLEDGED','Pledged Loan');
define('CASHFLOW_PURPOSE_REDEEMED','Redeemed Loan');
define('CASHFLOW_PURPOSE_AUCTION','Auction');
define('CASHFLOW_PURPOSE_AUCTION_LOSE','Auction Loss');
define('CASHFLOW_PURPOSE_AUCTION_PROFIT','Auction Profit');
define('CASHFLOW_PURPOSE_ADDITIONAL','Additional Charge');
define('CASHFLOW_PURPOSE_SERVICE','Service Charge');
define('CASHFLOW_PURPOSE_INTEREST','Pay Interest');
define('CASHFLOW_PURPOSE_PRINCIPLE','Pay Principle Amount');
define('CASHFLOW_PURPOSE_OPENING','Opening Balance');
define('CASHFLOW_PURPOSE_CLOSING','Closing Balance');
define('CASHFLOW_PURPOSE_TOTAL','TOTAL');
define('CASHFLOW_PURPOSE_AUCTION_SERVICE','Auction Service Charge');
define('CASHFLOW_PURPOSE_AUCTION_INTEREST','Auction Pay Interest');

//define('CASHFLOW_PURPOSE_AUCTION_PROFIT','Auction Bidded Loan Profit');
//define('REDEMPTION_PAYMENT','Redemption');


//Delete Messages
define('CASHFLOW_NOT_DELETED','Data(s) not deleted');
define('CASHFLOWID_DELETED','This cash flow details deleted sucessfully!');
define('CASH_USED_IN_BANK_TRANSACTION','This cash flow details is used in bank transaction!');
define('CASH_USED_IN_AMOUNT_TRANSFER','This cash flow details is used in amount transfer transaction!');
define('CASH_USED_IN_PLEDGE','This cash flow details is used in product pledge transaction!');
define('CASH_USED_IN_PRODUCT_INTERESTS','This cash flow details is used in pay interset transaction!');
define('CASH_USED_IN_PAY_EXCESS_AMOUNT','This cash flow details is used in pay excess amount transaction!');
define('CASH_USED_IN_REDEMPTION','This cash flow details is used in redemption transaction!');
define('CASH_USED_IN_PRODUCT_AUCTION','This cash flow details is used in product auction transaction!');
define('CASH_USED_IN_PARTIAL_AMOUNT_PAYMENT','This cash flow details is used in partial amount payment transaction!');
define('CASH_USED_IN_RENEWAL_PAYMENT','This cash flow details is used in renewal payment transaction!');

define('PLEDGE_DELETED','The pledge details are deleted successfully');
define('PLEDGE_NOT_DELETED','Data(s) not deleted');
define('PLEDGE_OPEN_STATUS','This pledge details not closed!');
define('INBOX_MESSAGE_DELETED','This inbox details details are deleted!');
define('INBOX_MESSAGE_NOT_DELETED','This inbox details details are not deleted!');
define('SENT_ITEMS_MESSAGE_DELETED','This sent items details are deleted!');
define('SENT_ITEMS_MESSAGE_NOT_DELETED','This sent items details are not deleted!');
define('SETTINGS_DELETED','This settings details all are deleted!');
define('SETTINGS_NOT_DELETED','This settings details not deleted!');
define('BRANCH_NOT_DELETED','Data(s) not deleted');
define('BRANCH_HEAD_OFFICE_CANNOT_DELETE','Branch head office details can not be deleted!');
define('SCHEME_NOT_DELETED','Data(s) not deleted');
define('SCHEME_USED_IN_PLEDGE','This scheme details are used in pledge transaction');
define('PRODUCTS_NOT_DELETED','Data(s) not deleted');
define('PRODUCT_USED_IN_PLEDGE','This product details are used in pledge transaction');
define('CATEGORIES_NOT_DELETED','Data(s) not deleted');
define('CATEGORIES_USED_IN_PLEDGE','This categories details are used in pledge transaction');
define('CATEGORIES_USED_IN_PLEDGE','This categories details are used in pledge transaction');
define('DEPARTMENTS_DELETED','This department deleted sucessfully!');
define('DEPARTMENTS_NOT_DELETED','Data(s) not deleted');
define('USER_DELETED','This user details are deleted!');
define('USER_NOT_DELETED','Data(s) not deleted');
define('CUSTOMER_DELETED','This customer details are deleted!');
define('CUSTOMER_NOT_DELETED','Data(s) not deleted');
define('PRODUCTS_DELETED','This product details are deleted!');
define('PRODUCTS_NOT_DELETED','Data(s) not deleted');
define('SCHEME_DELETED','This scheme details are deleted!');
define('SCHEME_NOT_DELETED','Data(s) not deleted');
define('BANKTRANSACTION_DELETED','This Bank transaction branch level  details deleted sucessfully!');
define('BANKTRANSACTION_NOT_DELETED','Data(s) not deleted!');
define('RAISE_REQUESTBRANCH_DELETED','Data(s)  deleted!');
define('RAISE_REQUESTBRANCH_NOT_DELETED','Data(s) Not deleted!');
define('PRIVILLEGE_DELETED','privilege deleted!');
define('PRIVILEGE_NOT_DELETED','Privilege not deleted!');
define('SUB_BRANCH_NOT_DELETED','Data(s) not deleted');

define('TRANSFER_CLOSED_BRANCH','Deleted Branch balance amount transfer in to head office account');


define('CUSTOMER_STATUS_NOT_CLOSED_IN_PLEDGED_ITEMS','Customer pledge details must be delete before delete the customer');

//Privilege message
define('NO_PRIVILEGE','No privilege to access!');
define('MY_ACCOUNT','Your Account Has Been Successfully Updated!');
define('PRIVILEGE_DELETED','Privilege deleted');
define('USERS_USE_THIS_PRIVILEGE','Users accessing this privilege. Delete all users for this privilege');
define('SUPER_ADMIN_PRIVILEGE_CANNOT_DELETE','Super admin privilege can not be modified or delete');

define('NO_EDIT_PRIVILEGE','You have no privilege to edit');
define('NO_DELETE_PRIVILEGE','You have no privilege to delete');
define('NO_ACTION_PRIVILEGE','You have no privilege');
define('NO_STATUS_PRIVILEGE','No privilege to change status');
define('PRODUCT_REDEEMED_NO_PRIVILEGE','Product has been redeemed. So no privilege to change status');
define('PRODUCT_AUCTIONED_NO_PRIVILEGE','Product has been bidded in auction. So no privilege to change status');

define('NO_OLD_RECORD_EDIT_PRIVILEGE','No permission to edit old records of this module');
define('NO_OLD_RECORD_DELETE_PRIVILEGE','No permission to delete old records of this module');

define('AMOUNT_NOT_TRANSFER','Amount not transfered');
define('AMOUNT_ALREADY_TRANSFERED','Already Amount transfered');
define('AMOUNT_NOT_RECEIVED','Amount Not Receive');
define('AMOUNT_RECEIVED','Amount Received');
define('AMOUNT_COULD_NOT_RECEIVED','Could not receive amount');

define('INSUFFICIENT_BALANCE','Insufficent balance');
define('MONEY_REQUESTED',"Branch requested money shortage");
define('INSUFFICIENT_BALANCE','Insufficent balance');

define('AMOUNT_ALLOTED',"Amount Allocated");
define('AMOUNT_NOT_ALLOTED','Amount not yet Allocated');


//Missing Fields
define('PAID_ON_RESET_ALERT',"Pay date - Reset the interest rate for the selected date. (Click <img src='images/refresh.png' border=0> Button near new loan date)");
define('PAID_DATE_INVALID_ALERT',"Pay date - Invalid. This should be less than Last Interest paid date and loan date and current date");
define('WAVIER_DAYS_INVALID_ALERT',"Wavier Days - Invalid. This should be greater than number of days from last transaction of this loan");
define('WAVIER_DAYS_MORETHAN_FIVE_DAYS_INVALID_ALERT',"Wavier Days - Invalid. This should be less than or equal to 5 days");
define('SCHEME_RESET_ALERT',"Amount not recalculated for the selected new scheme. Please click the <img src='images/refresh.png' border=0> button near new scheme");

define('AUCTION_PAID_ON_RESET_ALERT',"Auction date - Reset the interest rate for the selected date. (Click <img src='images/refresh.png' border=0> Button near Auction Date)");
define('AUCTION_PAID_DATE_INVALID_ALERT',"Auction date - Invalid. This should be less than Last Interest paid date or loan date");

//Latest
define('SUPER_ADMIN_CANNOT_DELETE',"Super admin user can not be delete"); 
define('CATEGORIES_DELETED',"Category deleted"); 
define('CATEGORIES_CANNOT_DELETED',"Category can not be delete"); 

define('OTHER_BRANCH_ALERT',"The loan products are not pledged in this branch. You could not do any transactions with this loan"); 
define('OTHER_BRANCH_TRANS_ALERT',"This Transaction item are not for this branch"); 


define('VALIDATE_PRODUCT_PLEDGE_PAY_INTEREST',"Interest paid for the loan"); 
define('VALIDATE_PRODUCT_PLEDGE_PRODUCT_AUCTION',"This loan had been bidded"); 
define('VALIDATE_PRODUCT_PLEDGE_REDEMPTION',"This loan had been redeemed"); 
define('VALIDATE_PRODUCT_PLEDGE_RENEWAL_OLD',"This loan had been renewed"); 
define('VALIDATE_PRODUCT_PLEDGE_RENEWAL_NEW',"This is new renewed loan from old loan"); 
define('VALIDATE_PRODUCT_PLEDGE_PAY_EXCESS_OLD',"This loan had been partially paid");
define('VALIDATE_PRODUCT_PLEDGE_PAY_EXCESS_NEW',"This is new partially paid loan from old loan"); 
define('VALIDATE_PRODUCT_PLEDGE_PARTIAL_AMOUNT_OLD',"This loan had been paid excess amount to customer");
define('VALIDATE_PRODUCT_PLEDGE_PARTIAL_AMOUNT_NEW',"This is new excess amount paid loan from old loan"); 

//Status Message
define('CLICK_HERE_TO_ENABLE',"Click here to enable");
define('CLICK_HERE_TO_DISABLE',"Click here to disable");

//Maximum Date restriction
define('ALERT_MAX_LIMIT_DAYS_REPORT',"Maximum 30 days of report can be permitted by Admin");

//Daily Report particulars
define('DR_PARTICULARS_PLEDGE',"Click here to disable");



