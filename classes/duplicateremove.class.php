<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Duplicate Remove class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Sathish santhosh kumar. D
 // Inprogress       :  
 // Backup           :  
 // Last Modified    :  
 //**************************************************************************
 // Description      :  This is the Leave class  for Mayavafinance.
// **************************************************************************

class DuplicateRemove {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id', 'leave_date', 'description');
        public $check_duplication = array('branch_id', 'leave_date');
        public $check_required_labels = array('Branch Name', 'Leave date','Description');
      //        public $check_duplication_labels = array('Company Branch Name', 'Company Branch Code'); 
        
        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Details Already exists!';
		 
		 
		 public static function Search($loanID){
			 $args = func_get_args();
			 $app = $GLOBALS['app'];
			 $loanID = $args[0];			 
			 $query = "SELECT id FROM ".PLEDGE." WHERE loan_id ='".$loanID."'";
			 $data = $app->getrows($query);
			
			 $loan_tbl_id = $data[0]['id'];
			 if($loan_tbl_id != ''){
				 $dataRedemtionQry = "SELECT id FROM mf_redemption WHERE loan_id ='".$loan_tbl_id."'";
				 $dataRedemtion = $app->getrows($dataRedemtionQry);
				 $redemtioncount = count($dataRedemtion);	
			 }else{
				 return "Enter Correct Loan Id.";
				 exit;
			 }
			 
			 if($redemtioncount <= 1){
				 return "No Duplicate record found";
			 }else{				 
			 $loan_tbl_id = $data[0]['id'];			
			 // SELECT Product interest and Redemption - Cashflow ids			 
			 $productIntQry = "Select * from mf_product_interests where loan_id ='".$loan_tbl_id."' order by id desc limit 1";
			 $productIntData = $app->getrows($productIntQry);			
			 $productCFID = $productIntData[0]['cash_flow_id'];
			 $prod_tbl_id = $productIntData[0]['id'];			 
			 $backupData[] = array('table' => 'mf_product_interests','data' => $productIntData);
			 $redemtionQry = "Select * from mf_redemption where loan_id = '".$loan_tbl_id."' order by id desc limit 1";
			 $redemptionData = $app->getrows($redemtionQry);
		     $backupData[] = array('table' => 'mf_redemption','data' => $redemptionData);
			 $redem_tbl_id =  $redemptionData[0]['id'];
			 $redemptionCFID = $redemptionData[0]['cash_flow_id'];
			 
			 // Backup and delete data from Cash flow id
			 $cashFlowQry = "Select * from mf_cash_flow_transactions where id in('".$productCFID."','".$redemptionCFID."')";
			 $cashFlowData = $app->getrows($cashFlowQry);			
			 $backupData[] = array('table' => 'mf_cash_flow_transactions','data' => $cashFlowData);	
			 $cas_flow_tbl_id1 = $cashFlowData[0]['id'];
			 $cas_flow_tbl_id2 = $cashFlowData[1]['id'];
				//echo "<pre>";
				//print_r($backupData);
			 $backupDataJson = json_encode($backupData);
			 
			 // Take backup mf_duplicate_backup table
			 $backupqry = "Insert into mf_duplicate_backup set user_loan_id = '".$loanID."', loan_id = '".$loan_tbl_id."', data = '".$backupDataJson."'";
			 $bdata['loan_id'] = $loan_tbl_id;
			 $bdata['data'] = $backupDataJson;
			 $backup = $app->add($bdata);
			 // if($backup){
				 // echo "hello";
				$app->access_table = "mf_cash_flow_transactions";
				$app->delete(array("id"=> $productCFID));
				$app->delete(array("id"=> $redemptionCFID));
				
				// print_r($app);
				// exit;
				
				$app->access_table = "mf_redemption";
				$app->delete(array("id"=> $redem_tbl_id));
				
				$app->access_table = "mf_product_interests";
				$app->delete(array("id"=> $prod_tbl_id));
				return "ok";
			 // }
			 }
			 
			
			 
		 }

}


