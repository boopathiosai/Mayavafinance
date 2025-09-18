<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Leave class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Sathish santhosh kumar. D
 // Inprogress       :  
 // Backup           :  
 // Last Modified    :  
 //**************************************************************************
 // Description      :  This is the LoanDate class  for Mayavafinance.
// **************************************************************************

class LoanDate {
    /*
     * Get Loan date based on leave date
     */
    
    public static function GetloanDate(){
        
        $app = $GLOBALS['app'];
        $branch_id = $_SESSION['session_user_branch_id'];
        
        // Get weekend //
        $query_weekend = "SELECT out_time, weekend FROM ". BRANCH ." WHERE id = ".$branch_id;
        $weekend = $app->getrows($query_weekend);      
        
        $arr_leaves = self::getleaveDate();    
//        print_r($arr_leaves);
        $curr_date = date('Y-m-d'); 
        $out_time = date('h:i A',strtotime($weekend[0]['out_time']));      
//        echo date("h:i")."-".date('h:i',strtotime($weekend[0]['out_time']));
        if (strtotime(date("h:i A")) >= strtotime($out_time)) {
            $curr_date = date('Y-m-d', strtotime('+1 day', strtotime($curr_date)));
            if(date('w', strtotime($curr_date)) == $weekend[0]['weekend']){
             $curr_date = date('Y-m-d', strtotime('+1 day', strtotime($curr_date))); 
            }
        }   
       
    if(!empty($arr_leaves)){
        do {
            if (in_array($curr_date, array_values($arr_leaves))) {
                $curr_date = date('Y-m-d', strtotime('+1 day', strtotime($curr_date)));
                 if(date('w', strtotime($curr_date)) == $weekend[0]['weekend']){
                    $curr_date = date('Y-m-d', strtotime('+1 day', strtotime($curr_date))); 
                   }
                $loan_date = $curr_date;
            } else {
                $loan_date = $curr_date;
                break;
            }
        } while ($curr_date <= max(array_values($arr_leaves)));
        }else{
          $loan_date =  $curr_date;
        }
               
        return date('d-m-Y',strtotime($loan_date));
    }
    
    public static function getleaveDate(){
        $app = $GLOBALS['app'];
        $branch_id = $_SESSION['session_user_branch_id']; 
        $query = "SELECT leave_date FROM ". LEAVES ." WHERE 1 and branch_id = ".$branch_id. " and status = 1 ORDER BY leave_date";
        $arr_leaves = $app->getrows($query);
        $leave_lists = array();
        if(!empty($arr_leaves)){
         foreach ($arr_leaves as $key => $value) {             
            array_push($leave_lists,$value['leave_date']);
        }        
         }
         
         return $leave_lists;
        
    }
    
}


