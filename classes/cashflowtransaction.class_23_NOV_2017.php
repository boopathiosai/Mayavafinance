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

class CashFlowTransaction {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('branch_id','transaction_type','cash_purpose','date_transaction','amount');
        #public $check_duplication = array('branch_code','name');

        public $check_required_labels = array('Branch Name','Transaction Type','Cash In/Out Purpose','Date Of Transaction','Amount');

        public static $check_required_text = 'Required Fields Missing!';
        #public static $check_duplication_text = 'Branch Already exists!';

        public static function ListviewCount() {

            $args = func_get_args();

            $app = $GLOBALS['app'];

            $s_trans_key = $args[0];
            $s_trans_type = $args[1];
            $s_branch = $args[2];
          $days_ago =  date('Y-m-d',strtotime("-2 days"));
     $day_today = date('Y-m-d',strtotime("+1 days"));


        $search_condition = ($s_trans_key != "")? " AND  ( CFT.cash_purpose LIKE '".$s_trans_key."%')":"";
        $search_condition .= ($s_trans_type != "")? "AND (CFT.transaction_type LIKE '".$s_trans_type."')":"";
        $search_condition .= ($s_branch != "")? "AND ( CFT.branch_id  = '".$s_branch."' )  ":"";
	$search_condition .= "AND ( CFT.date_transaction  between '".$days_ago."' and '".$day_today."' )";


            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CFT.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
            }

            /*
             * Return the Branch Count
             */
            $query = "SELECT COUNT(CFT.id)
                FROM ".CASH_FLOW_TRANSACTIONS." AS CFT
                WHERE 1 ".$search_condition."
                ORDER BY CFT.created_datetime DESC ";
                //echo "<pre>"; echo $query."<hr>";die;
                $data = $app->getrow($query);
                return $count = intval($data[0]);

     }

    public static function Listview() {
        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_trans_key = $args[0];
        $s_trans_type = $args[1];
        $s_branch = $args[2];
	 $days_ago =  date('Y-m-d',strtotime("-2 days"));
       $day_today = date('Y-m-d',strtotime("+1 days"));

        $search_condition = ($s_trans_key != "")? " AND  ( CFT.cash_purpose LIKE '".$s_trans_key."%')":"";
        $search_condition .= ($s_trans_type != "")? "AND (CFT.transaction_type LIKE '".$s_trans_type."')":"";
        $search_condition .= ($s_branch != "")? "AND ( CFT.branch_id  = '".$s_branch."' )  ":"";
	  $search_condition .= "AND ( CFT.date_transaction  between '".$days_ago."' and '".$day_today."' )";

        if(!is_superadmin($_SESSION['session_usergroupname'])){
            $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CFT.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
        }

        $count = self::ListviewCount($s_trans_key, $s_trans_type, $s_branch);
        //$count = 750;
        if ($count > 0) {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging
          /*  $query = "SELECT CFT.id, CFT.opening_balance, CFT.closing_balance, CFT.amount,
                        CFT.transaction_type, DATE_FORMAT(CFT.date_transaction,'%d %b %Y %I:%i %p') AS date_transaction,
                        CFT.cash_purpose, CFT.status, CFT.editable,
                        IF(CFT.status = 0, 'disable', 'enable') AS status_text, B.name as branch_name
                        FROM ".CASH_FLOW_TRANSACTIONS." as CFT
                        LEFT JOIN ".BRANCH." as B ON CFT.branch_id = B.id
                        WHERE 1 ".$search_condition."
                        ORDER BY B.name, CFT.date_transaction DESC	, CFT.id DESC  ". Paging::get_sql_limit();*/
               $query = "SELECT CFT.id, CFT.opening_balance, CFT.closing_balance, CFT.amount,
                        CFT.transaction_type, DATE_FORMAT(CFT.date_transaction,'%d %b %Y %I:%i %p') AS date_transaction,
                        CFT.cash_purpose, CFT.status, CFT.editable,
                        IF(CFT.status = 0, 'disable', 'enable') AS status_text, B.name as branch_name
                        FROM ".CASH_FLOW_TRANSACTIONS." as CFT
                        LEFT JOIN ".BRANCH." as B ON CFT.branch_id = B.id
                        WHERE 1 ".$search_condition."
                        ORDER BY  CFT.date_transaction DESC ". Paging::get_sql_limit();
            //echo "<pre>"; echo $query."<hr>";die;
            return $app->getrows($query);
        }
    }

    public static function Detail($trans_id) {
            /*
             * Return the branch details
             */
            $app = $GLOBALS['app'];


                $query = "SELECT CFT.id, CFT.opening_balance, CFT.closing_balance,
                            CFT.transaction_type, DATE_FORMAT(CFT.date_transaction,'%d-%m-%Y') AS date_transaction,
                            CFT.cash_purpose, CFT.amount, CFT.status,
                            CFT.branch_id, CFT.editable,
                            IF(CFT.status = 0, 'disable', 'enable') AS status_text, B.name as branch_name
                            FROM ".CASH_FLOW_TRANSACTIONS." as CFT
                            LEFT JOIN ".BRANCH." as B ON CFT.branch_id = B.id
                            WHERE CFT.id ='".$trans_id."'";

                return $app->getrows($query);

    }


    public function insertCashFlow($branch_id,$type,$amount,$purpose,$trans_date='',$parent_id = 0,$module_type = 0){


        $trans_date = ($trans_date == '') ? date("Y-m-d H:i:s") : $trans_date ;



        $app = $GLOBALS['app'];
        $app->access_table = CASH_FLOW_TRANSACTIONS;
        $balance = $app->find(
                                array("closing_balance"),
                                array(
                                    "conditions"=>array(
                                                    "branch_id = "=>$branch_id,
                                                    "date_transaction <= "=>$trans_date
                                                  ),
                                    "sort"=>" date_transaction DESC, id DESC",
                                    "limit"=> " 0,1"
                             ));
        //print_r($balance);
        if($balance)
         $balance_amount = $balance[0]["closing_balance"];
        else
         $balance_amount = 0;


        if($type=="cashin")
            $closing_amount = $balance_amount+$amount;
        elseif($type=="cashout")
            $closing_amount = $balance_amount-$amount;


        $cashFlow["branch_id"] = $branch_id;
        $cashFlow["opening_balance"] = $balance_amount;
        $cashFlow["transaction_type"] = $type;
        $cashFlow["cash_purpose"] = $purpose;

        //Commented temporerily for testing purpose
        //$cashFlow["date_transaction"] = date('Y-m-d H:i:s');

        //Enable temporerily for testing purpose
        $cashFlow["date_transaction"] = $trans_date ;

        $cashFlow["amount"] = $amount;
        $cashFlow["closing_balance"] = $closing_amount;
        $cashFlow['parent_id'] = $parent_id;
        $cashFlow['created_datetime'] = date('Y-m-d H:i:s');
        $cashFlow['created_by'] = $_SESSION['session_userid'];
        /*$cashFlow['modified_datetime'] = date("Y-m-d H:i:s");
        $cashFlow['modified_by'] = $_SESSION['session_userid'];*/
        $cashFlow['editable'] = 0;
        $cashFlow['module_type'] = $module_type;
        
        $cashFlow['status'] = 1;
        //print_r($cashFlow);exit;

        $app->add($cashFlow);
        $flow_id = $app->last_inserted_id();

        self::updateCashFlow($flow_id,$amount,$type, $trans_date);

        return $flow_id;
    }

    public function updateCashFlow($flow_id,$new_amount,$cash_type='', $trans_date='',$parent_id = 0){

        if($trans_date='') $trans_date = date("Y-m-d H:i:s");



        $app = $GLOBALS['app'];
        $app->access_table = CASH_FLOW_TRANSACTIONS;

        //Get Cash Flow DEtails for the selected Flow Id
        $cash_detail = $app->find(
                            array("branch_id","amount","date_transaction","transaction_type","cash_purpose","opening_balance","closing_balance"),
                            array(
                                "conditions"=>array("id = "=>$flow_id),
                             ));
        $amount = $cash_detail[0]["amount"];
        $branch_id = $cash_detail[0]["branch_id"];
        $date_transaction = $cash_detail[0]["date_transaction"];
        $transaction_type = $cash_detail[0]["transaction_type"];
        $cash_purpose = $cash_detail[0]["cash_purpose"];
        $closing_balance = $cash_detail[0]["closing_balance"];
        $opening_balance = $cash_detail[0]["opening_balance"];

        $transaction_type = ($transaction_type == $cash_type || $cash_type == '' ) ? $transaction_type : $cash_type ;

        // Get New Openning Balance
        if($transaction_type == "cashout")
            $new_openning_balance = floatval($opening_balance)-floatval($new_amount);
        elseif($transaction_type == "cashin")
            $new_openning_balance = floatval($opening_balance)+floatval($new_amount);


        //echo $amount ." == ". $new_amount;


        /*if($amount == $new_amount && ($transaction_type == $cash_type || $cash_type == '' )) {
            return false;
        }else*/
        {

            // Save New amount and Close  Balance
            $res = $app->save(array("transaction_type"=>$transaction_type,"amount"=>$new_amount,"closing_balance"=>floatval($new_openning_balance)),array("id = "=>$flow_id));

            //echo   $res ;

            //Get All corresponding Cash Flow Details for the reset amount flow from the selected Flow Id
            $cash_flows = $app->find(
                                array("*"),
                                array(
                                    "conditions"=>array(
                                                    "branch_id = "=>$branch_id,
                                                    "date_transaction > "=>$date_transaction
                                                 ),
                                    "sort"=>" date_transaction ASC, id ASC"
                                 ));

            //echo "count = ".count($cash_flows);
            if(count($cash_flows)>0){

                foreach($cash_flows as $cash_flow){

                    $cash_id = $cash_flow["id"];
                    $cash_amount = $cash_flow["amount"];
                    $cash_type = $cash_flow["transaction_type"];

                    if($cash_type == "cashin")
                        $new_closing_balance = floatval($new_openning_balance) + floatval($cash_amount);
                    elseif($cash_type == "cashout")
                        $new_closing_balance = floatval($new_openning_balance) - floatval($cash_amount);

                    $dval["opening_balance"] = $new_openning_balance;
                    $dval["closing_balance"] = $new_closing_balance;
                    //$dval['parent_id'] = $parent_id;
                    $dval['modified_datetime'] = date("Y-m-d H:i:s");
                    $dval['modified_by'] = $_SESSION['session_userid'];
                    


                    $new_openning_balance = $new_closing_balance;

                    $res = $app->save($dval, array("id = "=>$cash_id));
                    //print_r($cash_flow);

                }

            }
            return true;
        }

    }


    public function deleteCashFlow($flow_id){

        $app = $GLOBALS['app'];
        $app->access_table = CASH_FLOW_TRANSACTIONS;
        
        if((int)$flow_id > 0){

        //Get Cash Flow DEtails for the selected Flow Id
        $cash_detail = $app->find(
                            array("branch_id","amount","date_transaction","transaction_type","cash_purpose","opening_balance","closing_balance"),
                            array(
                                "conditions"=>array("id = "=>$flow_id),
                             ));

        $subcash_detail = $app->find(
                            array("id","branch_id","date_transaction","transaction_type"),
                            array(
                                "conditions"=>array("parent_id = "=>$flow_id),
                             ));

        $amount = $cash_detail[0]["amount"];
        $branch_id = $cash_detail[0]["branch_id"];
        $date_transaction = $cash_detail[0]["date_transaction"];
        $transaction_type = $cash_detail[0]["transaction_type"];
        $cash_purpose = $cash_detail[0]["cash_purpose"];
        $closing_balance = $cash_detail[0]["closing_balance"];
        $opening_balance = $cash_detail[0]["opening_balance"];
        
        
         $prev_day_closing_balance = $app->find(
                                array("closing_balance"),
                                array(
                                    "conditions"=>array(
                                                    "branch_id = "=>$branch_id,
                                                    "date_transaction < "=>$date_transaction
                                                 ),
                                    "sort"=>" date_transaction DESC, id DESC LIMIT 0,1"
                                 ));
        
//         echo "<pre>";
//         echo print_r($prev_day_closing_balance);
//         exit;
       $new_openning_balance = $prev_day_closing_balance[0]["closing_balance"];
       

        // Get New Openning Balance
        /*if($transaction_type == "cashin")
            $new_openning_balance = floatval($opening_balance)-floatval($amount);
        elseif($transaction_type == "cashout")
            $new_openning_balance = floatval($opening_balance)+floatval($amount);*/

        //$new_openning_balance = $opening_balance;




        // Delete Current Flow ID
        $res = $app->delete(array("id"=>$flow_id));



        //Get All corresponding Cash Flow Details for the reset amount flow from the selected Flow Id
            $cash_flows = $app->find(
                                array("*"),
                                array(
                                    "conditions"=>array(
                                                    "branch_id = "=>$branch_id,
                                                    "date_transaction >= "=>$date_transaction
                                                 ),
                                    "sort"=>" date_transaction ASC, id ASC"
                                 ));

        /*$cash_flows = $app->find(
                            array("*"),
                            array(
                                "conditions"=>array(
                                                "branch_id = "=>$branch_id,
                                                "id > "=>$flow_id
                                             ),
                                "sort"=>" created_datetime ASC"
                             ));*/

         //print_r($cash_flow);
        //echo "count = ".count($cash_flows);
        if(count($cash_flows)>0){

            foreach($cash_flows as $cash_flow){

                $cash_id = $cash_flow["id"];
                $cash_amount = $cash_flow["amount"];
                $cash_type = $cash_flow["transaction_type"];

                if($cash_type == "cashin")
                    $new_closing_balance = floatval($new_openning_balance) + floatval($cash_amount);
                elseif($cash_type == "cashout")
                    $new_closing_balance = floatval($new_openning_balance) - floatval($cash_amount);


                // $dval["opening_balance"] = $cash_flow['opening_balance'];
                // $dval["closing_balance"] = $cash_flow['opening_balance'];

                
                //mofified on 30-01-2017
                 $dval["opening_balance"] = $new_openning_balance;
                $dval["closing_balance"] = $new_closing_balance;

                
                $dval['modified_datetime'] = date("Y-m-d H:i:s");
                $dval['modified_by'] = $_SESSION['session_userid'];

                $new_openning_balance = $new_closing_balance;
                $res = $app->save($dval, array("id = "=>$cash_id));


            }

        }
        
        if(count($subcash_detail) > 0){
        
            foreach($subcash_detail as $scash){
                $sflow_id = $scash['id'];
                self::deleteCashFlow($sflow_id);
            }
            
            return true;
        
        }else{
        
            return true;
        
        }
        }else{
            return false;
        }
    }

    public static function deleteValidation($cash_id)
    {
                $app = $GLOBALS['app'];

                $retval = true;

                //Amount_Transfer Cash Flow Checking
                $app->access_table = AMOUNT_TRANSFER;
                $cash_in = $app->find(array("cash_in_id"), array("conditions"=>array("cash_in_id = "=>$cash_id)));
                $cash_out = $app->find(array("cash_out_id"), array("conditions"=>array("cash_out_id = "=>$cash_id)));

                if(is_array($cash_in) || is_array($cash_out))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_AMOUNT_TRANSFER;
                }


                //BANK_TRANSACTION Cash Flow Checking
                $app->access_table = BANK_TRANSACTION;
                $cash_in = $app->find(array("cash_in_id"), array("conditions"=>array("cash_in_id = "=>$cash_id)));
                $cash_out = $app->find(array("cash_out_id"), array("conditions"=>array("cash_out_id = "=>$cash_id)));

                if(is_array($cash_in) || is_array($cash_out))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_BANK_TRANSACTION;
                }


                //PAY_EXCESS_AMOUNT Cash Flow Checking
                $app->access_table = PAY_EXCESS_AMOUNT;
                $cash_in = $app->find(array("cash_in_id"), array("conditions"=>array("cash_in_id = "=>$cash_id)));
                $cash_out = $app->find(array("cash_out_id"), array("conditions"=>array("cash_out_id = "=>$cash_id)));

                if(is_array($cash_in) || is_array($cash_out))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_PAY_EXCESS_AMOUNT;
                }


                //PRODUCT_AUCTION Cash Flow Checking
                $app->access_table = PRODUCT_AUCTION;
                $cash_in = $app->find(array("cash_in_id"), array("conditions"=>array("cash_in_id = "=>$cash_id)));
                $cash_out = $app->find(array("cash_out_id"), array("conditions"=>array("cash_out_id = "=>$cash_id)));

                if(is_array($cash_in) || is_array($cash_out))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_PRODUCT_AUCTION;
                }

                //PARTIAL_AMOUNT_PAYMENT Cash Flow Checking
                $app->access_table = PARTIAL_AMOUNT_PAYMENT;
                $cash_in = $app->find(array("cash_in_id"), array("conditions"=>array("cash_in_id = "=>$cash_id)));
                $cash_out = $app->find(array("cash_out_id"), array("conditions"=>array("cash_out_id = "=>$cash_id)));


                if(is_array($cash_in))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_PARTIAL_AMOUNT_PAYMENT;
                }

                //RENEWAL Cash Flow Checking
                $app->access_table = RENEWAL;
                $cash_in = $app->find(array("cash_in_id"), array("conditions"=>array("cash_in_id = "=>$cash_id)));
                $cash_out = $app->find(array("cash_out_id"), array("conditions"=>array("cash_out_id = "=>$cash_id)));


                if(is_array($cash_in))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_RENEWAL_PAYMENT;
                }


                //PLEDGE Cash Flow Checking
                $app->access_table = PLEDGE;
                $cash_in = $app->find(array("cash_flow_id"), array("conditions"=>array("cash_flow_id = "=>$cash_id)));


                if(is_array($cash_in))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_PLEDGE;
                }

                //PRODUCT_INTERESTS Cash Flow Checking
                $app->access_table = PRODUCT_INTERESTS;
                $cash_in = $app->find(array("cash_flow_id"), array("conditions"=>array("cash_flow_id = "=>$cash_id)));


                if(is_array($cash_in))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_PRODUCT_INTERESTS;
                }

                //REDEMPTION Cash Flow Checking
                $app->access_table = REDEMPTION;
                $cash_in = $app->find(array("cash_flow_id"), array("conditions"=>array("cash_flow_id = "=>$cash_id)));


                if(is_array($cash_in))
                {
                    $retval=false;
                    $error_msg[] = CASH_USED_IN_REDEMPTION;
                }

                return array("canact"=>$retval,"error"=>$error_msg);
    }


    public static function Delete($cash_id) {
            /*
             * Return the Customers details
             */


            $app = $GLOBALS['app'];

            $status = self::deleteValidation($cash_id);

            if($status['canact']){

                self::deleteCashFlow($cash_id);

                return array("success"=>true,"error"=>$status['error']);

            }else{

                return array("success"=>false,"error"=>$status['error']);

            }

    }




    public static function getCashFlowPurpose($cash_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = CASH_FLOW_TRANSACTIONS;
        $res = $app->find(array("cash_purpose"),array("conditions"=>array("id = "=>$cash_id)));
        return $res[0][0];
    }

    public static function getBranchBalance($branch_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];

        $query = "SELECT  CFT.closing_balance
                        FROM ".CASH_FLOW_TRANSACTIONS." as CFT
                        LEFT JOIN ".BRANCH." as B ON CFT.branch_id = B.id
                WHERE B.id ='".$branch_id."' ORDER BY CFT.date_transaction DESC LIMIT 0,1";

        $data = $app->getrow($query);
        //p_arr($data);
        return (count($data)>0) ? $data["closing_balance"] : 0;
    }
    
    public static function getBranchTransBalance($branch_id, $request_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];

        $query = "SELECT  CFT.closing_balance
                        FROM ".CASH_FLOW_TRANSACTIONS." as CFT
                        LEFT JOIN ".BRANCH." as B ON CFT.branch_id = B.id
                WHERE B.id ='".$branch_id."' ORDER BY CFT.date_transaction DESC LIMIT 0,1";

        $data = $app->getrow($query);
        $bal_amt = (count($data)>0) ? floatval($data["closing_balance"]) : 0;
        
        $query = "SELECT SUM(T.transfer_amount) AS allocated_amount
                    FROM   ".BRANCH." as B 
                    LEFT JOIN  (".AMOUNT_TRANSFER." AS T, ".MONEY_SHORTAGE." AS RR ) ON (T.transfer_branch_id = B.id AND T.request_id = RR.id AND RR.status = 1)
                    WHERE B.id = '".$branch_id."' AND T.request_id <> '".$request_id."'
                    GROUP BY B.id
                    ORDER BY B.name";
        $data = $app->getrow($query);
        $tran_amt = (count($data)>0) ? floatval($data["allocated_amount"]) : 0;
                    
        
        //p_arr($data);
        return $bal_amt-$tran_amt;
    }



}