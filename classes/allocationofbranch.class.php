<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE                   
// Module           :  Brand class          
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Branch class  for Mayavafinance.
//**************************************************************************

class Allocationofbranch 
    {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('request_branch_id','transfer_branch_id','transfer_amount','request_id');
        public $check_required_labels = array('Requested Branch ', 'Allocated Branch',  'Amount To Be Transferred','Branch Raise request Id');
        public static $check_required_text = 'Required Fields Missing!'; 
        
        #public $check_duplication = array('branch_code','name','request_id');       
        #public static $check_duplication_text = 'Branch Already exists!';
        
        public static function ListviewCount() 
        {
                            
                            $args = func_get_args();
                             
                            $app = $GLOBALS['app'];
                            
                            /*
                             * Return the Branch Count
                             */
                                $query = "SELECT id
                                FROM ".BRANCH."
                                WHERE 1
                                ORDER BY id DESC ";
                                $data = $app->getrows($query);
                                return $count = count($data);

                             
        }

        public static function Listview() 
        { #Rajesh Mar 04, 2021
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                
                $args = func_get_args();
                 
                $app = $GLOBALS['app'];
                
                /*
                 * Return the branch list
                 */
                $query = "SELECT COUNT(id)
                          FROM ".BRANCH."  
                          WHERE 1  
                          ORDER BY id DESC ";
                    $data = $app->getrow($query);
                    $count = intval($data[0]);
                if ($count > 0) 
                {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                               
                                          
                       $query = "SELECT 
                                    B.name,
                                    B.id,
                                    COUNT(T.transfer_branch_id) AS no_allocation,
                                    (SELECT COUNT(sRR.id) FROM ".MONEY_SHORTAGE." AS sRR WHERE sRR.branch_id = B.id and sRR.status=1)  AS no_request,                                            
                                    SUM(T.transfer_amount) AS allocated_amount                                            
                                    FROM   ".BRANCH." as B 
                                    LEFT JOIN  (".AMOUNT_TRANSFER." AS T, ".MONEY_SHORTAGE." AS RR ) ON (T.transfer_branch_id = B.id AND T.request_id = RR.id AND RR.status = 1)
                                    GROUP BY B.id
                                    ORDER BY B.name
                                    ". Paging::get_sql_limit();
                                               
                                  
                                  
                                  
                       // echo "<pre>"; echo $query."<hr>";die;

                    $rows =  $app->getrows($query);  

                    $results_arr = array();
                    foreach($rows as $row_data) {

                             $amt_query = "SELECT closing_balance as amt from ".CASH_FLOW_TRANSACTIONS." where branch_id = ".$row_data['id']." ORDER BY id DESC LIMIT 0,1";

                              $amt_query_row =  $app->getrow($amt_query); 

                              if($amt_query_row){
                                $amt = $amt_query_row['amt'];
                              }else {
                                $amt = '';
                              }
                            $results_arr[] = array(
                                'name'=> $row_data['name'],
                                'id'=> $row_data['id'],
                                'no_allocation'=> $row_data['no_allocation'],
                                'no_request'=> $row_data['no_request'],
                                'amt'=>$amt
                            );
                    }

                   // echo "<pre>"; print_r($results_arr)."<hr>";die;                            

                    return $results_arr; 
                }
         }

        public static function ListviewOld() 
        {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                
                $args = func_get_args();
                 
                $app = $GLOBALS['app'];
                
                /*
                 * Return the branch list
                 */
                $query = "SELECT COUNT(id)
                          FROM ".BRANCH."  
                          WHERE 1  
                          ORDER BY id DESC ";
                    $data = $app->getrow($query);
                    $count = intval($data[0]);
                if ($count > 0) 
                {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                               /*$query = "SELECT 
                                            B.name,
                                            B.id,
                                            COUNT(T.transfer_branch_id) AS no_allocation,
                                            (SELECT COUNT(sRR.id) FROM ".MONEY_SHORTAGE." AS sRR WHERE sRR.branch_id = B.id)  AS no_request,
                                            (SELECT closing_balance from ".CASH_FLOW_TRANSACTIONS." where branch_id = B.id ORDER BY created_datetime DESC, id DESC LIMIT 0,1) AS amt,
                                            SUM(T.transfer_amount) AS allocated_amount
                                            
                                            FROM   ".BRANCH." as B 
                                            LEFT JOIN  (".AMOUNT_TRANSFER." AS T, ".MONEY_SHORTAGE." AS RR ) ON (T.transfer_branch_id = B.id AND T.request_id = RR.id AND RR.status = 1)
                                            GROUP BY B.id
                                            ORDER BY B.name
                                            ". Paging::get_sql_limit();*/
                                          
                                          
                               $query = "SELECT 
                                            B.name,
                                            B.id,
                                            COUNT(T.transfer_branch_id) AS no_allocation,
                                            (SELECT COUNT(sRR.id) FROM ".MONEY_SHORTAGE." AS sRR WHERE sRR.branch_id = B.id and sRR.status=1)  AS no_request,
                                            (SELECT closing_balance from ".CASH_FLOW_TRANSACTIONS." where branch_id = B.id ORDER BY created_datetime DESC, id DESC LIMIT 0,1) AS amt,
                                            SUM(T.transfer_amount) AS allocated_amount
                                            
                                            FROM   ".BRANCH." as B 
                                            LEFT JOIN  (".AMOUNT_TRANSFER." AS T, ".MONEY_SHORTAGE." AS RR ) ON (T.transfer_branch_id = B.id AND T.request_id = RR.id AND RR.status = 1)
                                            GROUP BY B.id
                                            ORDER BY B.name
                                            ". Paging::get_sql_limit();
                                                       
                                          
                                          
                                          
                                //echo "<pre>"; echo $query."<hr>";die;
                              return $app->getrows($query);
                   

                }
         }

        public static function Detail($userid) 
        {
                                /*
                                 * Return the branch details
                                 */
                        /*      $app = $GLOBALS['app'];
                                $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
                                if ($count > 0) 
                                {
                                    $query = "SELECT *
                                                FROM ".ALLOCATION_BRANCH."
                                                WHERE id ='".$userid."'";               
                                    return $app->getrows($query);
                                }
                        */
        }

        public static function Shortage_branch_name($id) 
            {
            
                                /*
                                 * Return the branch name
                                 */
                                $app = $GLOBALS['app'];
                                //$id=$_GET['id'];
                                $query = "SELECT B.name,M.amount_need,M.status,
                                                M.amount_need AS requested_amount,
                                                M.id AS request_id,
                                                T.transfer_amount AS allocated_amount,
                                                T.id AS id,
                                                T.transfer_branch_id AS allocated_branch,
                                                M.branch_id AS requested_branch
                                                
                                                FROM ".BRANCH." AS  B
                                                LEFT JOIN ".MONEY_SHORTAGE." AS  M ON M.branch_id = B.id 
                                                LEFT JOIN ".AMOUNT_TRANSFER." AS T ON T.request_id = M.id 
                                                WHERE M.status=1 and M.branch_id NOT IN($id) ";
                                                return $app->getrows($query);
             }

        public static function Shortage_branch_amt($id,$branch_id) 
            {
                                /*
                                 * Return the branch request anmount
                                 */
                                $app = $GLOBALS['app'];
                                $query = "SELECT M.amount_need,M.branch_id
                                          FROM ".MONEY_SHORTAGE." AS  M
                                          WHERE M.id='".$id."' AND M.status=1 AND  M.branch_id NOT IN($branch_id)";             
                                    return $app->getrow($query);
            }


        public static function Get_branch_name($id) 
            {
                /*
                 * Return the branch name
                 */
                $app = $GLOBALS['app'];
                $query = "SELECT B.name,B.id
                                FROM ".BRANCH." AS  B
                                where B.id='".$id."'";              
                    return $app->getrows($query);
                                
            }

        public static function Msg_details($to_id) 
            {
                $app = $GLOBALS['app'];
                $query ="SELECT  U.name AS recipient_name,U.created_by AS recipient_id, U.email AS EMAIL,U.branch_id,UG.name as user,B.name AS  branch_name,U.id as user_id,U.email as email
                FROM ".USERS." AS U
                LEFT JOIN ".BRANCH." AS B ON B.id = U.branch_id
                LEFT JOIN ".USER_GROUP." AS UG ON UG.id = U.user_group_id
                
                WHERE U.branch_id ='".$to_id."'";
                return $app->getrows($query);
            }
        
     
}