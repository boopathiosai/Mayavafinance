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

class PayInterest {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('interest_amount','loan_id','interest_days', 'interest_rate','paid_on');
        public $check_duplication = array('code');

        public $check_required_labels = array('Interest Amount','Loan Id','Interest Day', 'Rate of interest', 'Interest Paid Date');
        public $check_duplication_labels = array('Pledged Loan Number');

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Pledge Code Name Already exists!';

        public static function ListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];

        /* Search Query */
        $s_key = $args[0];
        $s_cus = $args[1];
        $s_location = $args[2];
        $s_fhw = $args[3];
        $s_branch = $args[4];
        $s_loan = $args[5];

        $search_condition  = " AND  ( P.status = 1)";
        $search_condition .= ($s_key != "")? " AND  ( C.name LIKE '".$s_key."%')":"";
        $search_condition .= ($s_cus != "")? "AND (C.id LIKE '".$s_cus."' OR C.customer_id LIKE '".$s_cus."')":"";
        $search_condition .= ($s_location != "")? "AND ( C.street LIKE '".$s_location."%' OR C.location LIKE '".$s_location."%')  ":"";
        $search_condition .= ($s_fhw != "")? "AND ( C.relation_name LIKE '".$s_fhw."%' )  ":"";
        $search_condition .= ($s_branch != "")? "AND ( C.branch_id  = '".$s_branch."' )  ":"";
        $search_condition .= ($s_loan != "")? "AND ( P.loan_id  LIKE '".$s_loan."'  OR P.id  = '".$s_loan."')  ":"";

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }


            /*
             * Return the Pledges list Count
             */
            $query = "SELECT DISTINCT P.id
            FROM ".PLEDGE." as P
            LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
            LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
            LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
            WHERE 1 ".$search_condition." ORDER BY id DESC ";
            $data = $app->getrows($query);
            return $count = count($data);
     }

        public static function Listview() {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */

                $args = func_get_args();
                $app = $GLOBALS['app'];

        /* Search Query */
        $s_key = $args[0];
        $s_cus = $args[1];
        $s_location = $args[2];
        $s_fhw = $args[3];
        $s_branch = $args[4];
        $s_loan = $args[5];

        $search_condition  = " AND  ( P.status = 1)";
        $search_condition .= ($s_key != "")? " AND  ( C.name LIKE '".$s_key."%')":"";
        $search_condition .= ($s_cus != "")? "AND (C.id LIKE '".$s_cus."' OR C.customer_id LIKE '".$s_cus."')":"";
        $search_condition .= ($s_location != "")? "AND ( C.street LIKE '".$s_location."%' OR C.location LIKE '".$s_location."%')  ":"";
        $search_condition .= ($s_fhw != "")? "AND ( C.relation_name LIKE '".$s_fhw."%' )  ":"";
        $search_condition .= ($s_branch != "")? "AND ( C.branch_id  = '".$s_branch."' )  ":"";
        $search_condition .= ($s_loan != "")? "AND ( P.loan_id  LIKE '".$s_loan."'  OR P.id  = '".$s_loan."')  ":"";

            if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND P.branch_id  = '".$_SESSION['session_user_branch_id']."'":"";
            }

                $count = self::ListviewCount($s_key,$s_cus,$s_location,$s_fhw,$s_branch, $s_loan);


                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT DISTINCT P.*,
                                P.branch_id AS pledged_branch,
                                B.name AS branch_name,
                                P.loan_amount AS loan_amount,
                                P.total_gross_weight AS gross, P.total_net_weight AS net,
                                DATE_FORMAT(P.loan_date,'%d-%b-%y') as loan_date,
                                (SELECT
                                    PI.id
                                    FROM ".PRODUCT_INTERESTS." AS PI
                                    WHERE PI.loan_id = P.id ORDER BY PI.paid_on DESC LIMIT 0,1
                                ) AS pi_id,
                                SC.name as scheme_name,
                                C.name as customer_name, IF(P.status = 0, 'disable', 'enable') AS status_text
                                FROM ".PLEDGE." as P
                                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                                LEFT JOIN ".BRANCH." as B ON C.branch_id = B.id
                                LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                                WHERE 1 ".$search_condition."
                                ORDER BY P.created_datetime DESC ". Paging::get_sql_limit();
                    //echo $query;
                    //exit;
                    return $app->getrows($query);




                }
    }

    public static function Detail($pledge_id,$pay_id='') {
            /*
             * Return the Pledge details
             */
            //echo $pledge_id;
            $app = $GLOBALS['app'];
            if($pay_id!=''){
                $search_fillter = " AND PT.id = '".$pay_id."'";
                $search_field = "PT.additional_charge, PT.interest_amount, PT.principle_amount,";
            }
            else{
                $search_fillter = "";
                $search_field = "";
            }


               $query = "SELECT P.id AS pledge_id,PT.id AS pi_id, P.branch_id AS pay_branch,
                        ".$search_field."
                        P.branch_id AS pledged_branch,
                        PT.paid_on , P.loan_id, P.loan_amount,
                        DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                        MONTH(P.loan_date) AS loan_month, MONTH(CURRENT_DATE) AS cur_month,
                        DATEDIFF(CURRENT_DATE, P.loan_date) AS total_days,
                        DATEDIFF(CURRENT_DATE, PT.paid_on) AS no_days,
                        IF(SC.interest_type=1,'floting','flat') AS scheme_type,
                        SC.id AS scheme_id,SC.name as scheme_name, SC.interest_type, SC.loan_rate , SC.service_charge,
                        C.customer_id,  C.name as customer_name,
                        PT.wavier_amount, PT.wavier_days, PT.wavier_reason,
                        PT.interest_rate, PT.additional_charge, PT.interest_days

                        FROM ".PLEDGE." as P
                        LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                        LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                        LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                        WHERE P.id ='".$pledge_id."' ".$search_fillter." ORDER BY PT.paid_on DESC";


                //echo $query;
                return $app->getrows($query);

    }

    public function Delete($pay_id) {
        $app = $GLOBALS['app'];

        //get Cash Flow ID
        $cash_detail = $app->find(
                        array("cash_flow_id"),
                        array(
                            "conditions"=>array("id = "=>$pay_id)
                         ));

        //Delete current flow id and reset Cash flow records after this flow id
        $status = CashFlowTransaction::deleteCashFlow($cash_detail[0]["cash_flow_id"]);

        // Delete Pay interest Details
        $query = "DELETE FROM ".PRODUCT_INTERESTS." WHERE id='".$pay_id."'";

        return $app->execute($query);

    }

    public function paidInterestDetail($pledge_id) {
            /*
             * Return the Scheme Intrest details
             */

            $app = $GLOBALS['app'];
            $query = "SELECT P.id AS pledge_id, PT.id AS pi_id,
                        DATE_FORMAT(PT.paid_on,'%d-%b-%y') AS paid_on ,
                        PT.additional_charge, PT.interest_amount, PT.principle_amount,
                        PT.total_paid_amount AS total_amount,
                        PT.wavier_amount AS wavier_amount
                        FROM ".PLEDGE." as P
                        RIGHT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                        WHERE P.id ='".$pledge_id."' ORDER BY PT.paid_on ASC";
                    //echo($query);
            return $app->getrows($query);
    }


    public function slabInterestDetail($pledge_id) {
            /*
             * Return the Scheme Intrest details
             */

            $app = $GLOBALS['app'];

            $query = "SELECT

                    P.id pledge_id,

                    DATE_FORMAT(DATE_ADD(DATE_ADD(P.loan_date, INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AS start_date,
                    DATE_FORMAT(DATE_SUB(DATE_ADD(P.loan_date, INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d') AS end_date,
                    DATE_FORMAT(DATE_ADD(DATE_ADD(P.loan_date, INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%d-%b-%Y') AS start ,
                    DATE_FORMAT(DATE_SUB(DATE_ADD(P.loan_date, INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%d-%b-%Y') AS end ,

                    (SELECT SUM(sPI.principle_amount) FROM ".PRODUCT_INTERESTS." sPI WHERE (sPI.paid_on BETWEEN DATE_FORMAT(DATE_ADD(DATE_ADD(P.loan_date, INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AND DATE_FORMAT(DATE_SUB(DATE_ADD(P.loan_date, INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d')) AND sPI.loan_id = P.id) AS principle_amount,
                    (SELECT SUM(sPI.interest_amount) FROM ".PRODUCT_INTERESTS." sPI WHERE (sPI.paid_on BETWEEN DATE_FORMAT(DATE_ADD(DATE_ADD(P.loan_date, INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AND DATE_FORMAT(DATE_SUB(DATE_ADD(P.loan_date, INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d')) AND sPI.loan_id = P.id) AS interest_amount,
                    (SELECT SUM(sPI.additional_charge) FROM ".PRODUCT_INTERESTS." sPI WHERE (sPI.paid_on BETWEEN DATE_FORMAT(DATE_ADD(DATE_ADD(P.loan_date, INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AND DATE_FORMAT(DATE_SUB(DATE_ADD(P.loan_date, INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d')) AND sPI.loan_id = P.id) AS additional_amount,
                    (SELECT SUM(sPI.total_paid_amount) FROM ".PRODUCT_INTERESTS." sPI WHERE (sPI.paid_on BETWEEN DATE_FORMAT(DATE_ADD(DATE_ADD(P.loan_date, INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AND DATE_FORMAT(DATE_SUB(DATE_ADD(P.loan_date, INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d')) AND sPI.loan_id = P.id) AS total_amount,
                    (SELECT SUM(sPI.wavier_amount) FROM ".PRODUCT_INTERESTS." sPI WHERE (sPI.paid_on BETWEEN DATE_FORMAT(DATE_ADD(DATE_ADD(P.loan_date, INTERVAL SD.above MONTH), INTERVAL 0 DAY),'%Y-%m-%d') AND DATE_FORMAT(DATE_SUB(DATE_ADD(P.loan_date, INTERVAL SD.upto MONTH), INTERVAL 1 DAY),'%Y-%m-%d')) AND sPI.loan_id = P.id) AS wavier_amount,


                    SD.rate_of_interest

                    FROM ".PLEDGE." as P
                    JOIN  ".SCHEME." AS SC ON SC.id = P.scheme_id
                    RIGHT JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id
                    WHERE P.id ='".$pledge_id."' ORDER BY SD.above, SD.upto";

                    //echo($query);
            return $app->getrows($query);
    }


    public function getInterestDataById($pay_id){
        $app = $GLOBALS['app'];
        $app->access_table = PRODUCT_INTERESTS;
        return $app->find(array('*'),array("conditions"=>array("id = "=>$pay_id)));
    }


    public function getInterestDetailsByPayid($pledge_id, $pa_id, $wavier_days=-1, $current_date=''){

        $wavier_days = intval($wavier_days);

        $app = $GLOBALS['app'];
        $app->access_table = PRODUCT_INTERESTS;
        $interest_details = $app->find(array("pay_interest_details"), array("conditions"=>array("id = "=>$pa_id)));

        $pay_interest_details = unserialize($interest_details[0]["pay_interest_details"]);

        if($wavier_days == intval($pay_interest_details["default_wavier_days"]) || $wavier_days == -1)
        {

            return $pay_interest_details;

        }else{

        extract($pay_interest_details);



        $mod_interest = self::getInterestDataById($pa_id);
        if(count($mod_interest) > 0){
            $cash_flow_id = $mod_interest[0]["cash_flow_id"];
            $wav_days = floatval($mod_interest[0]["wavier_days"]);
            $int_days = floatval($mod_interest[0]["interest_days"]);
            $principle_amount = floatval($mod_interest[0]["principle_amount"]);
            $old_interest_amount = floatval($mod_interest[0]["interest_amount"]);
            $additional_charge = floatval($mod_interest[0]["additional_charge"]);
            $wavier_reason = $mod_interest[0]["wavier_reason"];
            $wavier_amount = floatval($mod_interest[0]["wavier_amount"]);
            $paid_on = $mod_interest[0]["paid_on"];
            $interest_rate = floatval($mod_interest[0]["interest_rate"]);
        }




        if($pay_interest_details["penalty_status"]=="Over Due"){

            /*$normal_interest_days = $normal_interest_days + $normal_wavier_days;
            $penalty_interest_days = $penalty_interest_days + $penalty_wavier_days;*/

            $default_wavier_days = $wavier_days;
            $default_wavier_reason = ($wavier_days>0) ? $wavier_reason : "";


            $normal_per_day_interest = floatval((($loan_amount/ 100) * $normal_interest_rate ) / $total_days_of_year);
            $penalty_per_day_interest = floatval((($loan_amount/ 100) * $penalty_interest_rate ) / $total_days_of_year);

            $normal_wavier_days = ($default_wavier_days > $penalty_days) ? $default_wavier_days - $penalty_days : 0;
            $penalty_wavier_days = ($default_wavier_days > $penalty_days) ? $penalty_days : $default_wavier_days;

            $normal_interest_days = ( $first_month_days - $normal_wavier_days );
            $penalty_interest_days = ( $penalty_days - $penalty_wavier_days );


            $normal_interest_amount = ($normal_per_day_interest * $normal_interest_days) ;
            $penalty_interest_amount = ($penalty_per_day_interest * $penalty_interest_days);

            $normal_wavier_amount = (($loan_amount * ($ini_slab_rate / 100)) * ($normal_default_wavier_days / $total_days_of_year)) ;
            $penalty_wavier_amount = (($loan_amount * ($current_slab_rate / 100)) * ($penalty_default_wavier_days / $total_days_of_year));

            $new_interest_amount = $normal_interest_amount + $penalty_interest_amount;

            $default_wavier_amount =  $normal_wavier_amount + $penalty_wavier_amount;

            $interest_rate = $current_slab_rate;

            $no_of_days = $normal_interest_days + $penalty_interest_days + $default_wavier_days;


            $pay_interest_details['normal_interest_days'] = $normal_interest_days;
            $pay_interest_details['penalty_interest_days'] = $penalty_interest_days;
            $pay_interest_details['normal_interest_amount'] = $normal_interest_amount;
            $pay_interest_details['penalty_interest_amount'] = $penalty_interest_amount;
            $pay_interest_details['normal_wavier_days'] = $normal_wavier_days;
            $pay_interest_details['penalty_wavier_days'] = $penalty_wavier_days;
            $pay_interest_details['normal_wavier_amount'] = $normal_wavier_amount;
            $pay_interest_details['penalty_wavier_amount'] = $penalty_wavier_amount;

            /*$pay_interest_details['default_wavier_days'] = $default_wavier_days;
            $pay_interest_details['default_wavier_reason'] = $default_wavier_reason;
            $pay_interest_details['interest_amount'] = $new_interest_amount;
            $pay_interest_details['no_of_days'] = $no_of_days;*/

            $pay_interest_details['default_wavier_days'] = $default_wavier_days;
            //$pay_interest_details['default_wavier_reason'] = $default_wavier_reason;
            $pay_interest_details['default_wavier_reason'] = ($default_wavier_reason=='') ? 'Interest edited Wavier for - ' : $default_wavier_reason;
            $pay_interest_details['default_wavier_amount'] = $default_wavier_amount;
            $pay_interest_details['interest'] = $new_interest_amount;
            $pay_interest_details['interest_amount'] = $new_interest_amount;
            $pay_interest_details['no_of_days'] = $no_of_days;


        }else{

            if($int_days>0 && $old_interest_amount>0){

                $new_interest_amount = (($old_interest_amount/$int_days) * (($int_days + $wav_days) - $wavier_days));
                $default_wavier_amount = (($old_interest_amount/$int_days) * $wavier_days);
                $no_of_days = (($int_days + $wav_days));

            }elseif($int_days==0 && $old_interest_amount==0 && $wavier_amount > 0){

                $int_days = ($wavier_days < $wav_days) ? ($wav_days - $wavier_days) : 0;
                $new_interest_amount = ($wavier_amount/$wav_days) * $int_days;
                $default_wavier_amount = (($wavier_amount/$wav_days) * $wavier_days);
                $no_of_days = ($wavier_days < $wav_days) ? ($int_days + $wavier_days) : $int_days;

            }else{

                $new_interest_amount = 0;
                $default_wavier_amount = 0;
                $no_of_days = 0;

            }

            $default_wavier_days = $wavier_days;
            $default_wavier_reason = ($wavier_days>0) ? $wavier_reason : "";

            $pay_interest_details['default_wavier_days'] = $default_wavier_days;
            $pay_interest_details['default_wavier_reason'] = ($default_wavier_reason=='') ? 'Interest edited Wavier for - ' : $default_wavier_reason;
            $pay_interest_details['default_wavier_amount'] = $default_wavier_amount;
            $pay_interest_details['interest'] = $new_interest_amount;
            $pay_interest_details['interest_amount'] = $new_interest_amount;
            $pay_interest_details['no_of_days'] = $no_of_days;

        }

        $pay_interest_details['pay_id'] = $pa_id;

        return $pay_interest_details;

        }

    }


public function getStatusOfPayInterest($pledge_id, $wavier_days, $current_date){
    $app = $GLOBALS['app'];
	//echo "<pre>";
	/*To get the maximum interval count for the loan_date_exceed exceed*/
    $query_scheme = "SELECT  SC.name,MIN(above) AS min_interval_count,MAX(upto)AS max_interval_count FROM
	".PLEDGE." AS P
    LEFT JOIN ".SCHEME." AS SC ON SC.id = P.scheme_id
    LEFT JOIN ".SCHEME_DETAILS." AS SCH ON SCH.scheme_id= P.scheme_id
	WHERE P.id = '".$pledge_id."'";
    $row = $app->getrow($query_scheme);
	$max_interval = $row['max_interval_count'];

    $query = "SELECT

                DATE_FORMAT(PT.paid_on,'%Y-%m-%d') AS paid_on,
                DATE_FORMAT(P.loan_date,'%Y-%m-%d') AS loan_date,

                '".$current_date."' AS cur_date,
                '".$wavier_days."' AS wavier_days,
                DATE_FORMAT(DATE_SUB('".$current_date."', INTERVAL ".$wavier_days." DAY),'%Y-%m-%d') AS wavier_date,

                DATE_FORMAT(DATE_ADD(P.loan_date, INTERVAL 12 MONTH),'%Y-%m-%d') AS loan_end_date,
                DATE_FORMAT(DATE_ADD(IF(PT.paid_on<>'', PT.paid_on, P.loan_date), INTERVAL 12 MONTH),'%Y-%m-%d') AS pay_end_date,

                IF(
                PT.paid_on > DATE_FORMAT(DATE_ADD(P.loan_date, INTERVAL 12 MONTH),'%Y-%m-%d'),
                'Yes',
                'No'
                ) AS loan_date_exceed,

                IF(
                (SELECT DATE_ADD(sPT.paid_on, INTERVAL 12 MONTH) FROM ".PRODUCT_INTERESTS." AS sPT WHERE sPT.paid_on < PT.paid_on AND sPT.loan_id = PT.loan_id ORDER BY paid_on DESC LIMIT 0,1)  < DATE_FORMAT(IF(PT.paid_on<>'', PT.paid_on, P.loan_date),'%Y-%m-%d'),
                'Yes',
                'No'
                ) AS pay_date_exceed,

                IF(
                    DATE_FORMAT(DATE_SUB('".$current_date."', INTERVAL ".$wavier_days." DAY),'%Y-%m-%d') > DATE_FORMAT(DATE_ADD(IF(PT.paid_on<>'', PT.paid_on, P.loan_date), INTERVAL ".$max_interval." MONTH),'%Y-%m-%d'),
                    'Yes',
                    'No'

                ) AS cur_date_exceed

          FROM ".PLEDGE." AS P
          LEFT JOIN ".PRODUCT_INTERESTS." AS PT  ON P.id = PT.loan_id
          WHERE P.id = '".$pledge_id."' ORDER BY PT.paid_on ASC";

    $rows = $app->getrows($query);
    $cnt = count($rows);
    $i=1;

    //p_arr($rows);
    if($cnt > 0){
        foreach($rows as $row){
            //echo $i."  ".$cnt."  ".$row["loan_date_exceed"]."  ".$row["pay_date_exceed"]."  ".$row["cur_date_exceed"]."<br>";
            if(($i==1 && $row["loan_date_exceed"]=="Yes") || ($i==$cnt && $row["cur_date_exceed"]=="Yes") || $row["pay_date_exceed"]=="Yes"){
                //echo "*Inside*";

                return true;

            }

        $i++;
        }
    }else{

    return false;

    }
}


public function getInterestDetails($pledge_id, $wavier_days=0, $pay_id='', $current_date='', $type='', $module_name='', $retVar=''){

          if($current_date=='') $current_date = date("Y-m-d");
			else $current_date = date("Y-m-d",strtotime($current_date));

        $app = $GLOBALS['app'];
        $wavier_days = intval($wavier_days);
        $is_exceed = self::getStatusOfPayInterest($pledge_id, $wavier_days, $current_date);

        $query = "SELECT
                    PT.id AS pt_id,

                    (
                        DATEDIFF(
                            PT.paid_on,
                            P.loan_date
                        )
                        -
                        (
                            PT.wavier_days
                        )
                    ) AS no_of_day_from_loan,

                    (
                        DATEDIFF(
                            PT.paid_on,
                            (SELECT paid_on FROM ".PRODUCT_INTERESTS." WHERE paid_on < PT.paid_on AND loan_id = PT.loan_id ORDER BY paid_on DESC LIMIT 0,1)
                        )
                        -
                        (
                            PT.wavier_days
                        )
                    ) AS no_of_day_from_last_pay,


                    DATE_FORMAT(
                        PT.paid_on,
                        '%Y-%m-%d'
                    ) AS paid_on ,

                    PT.additional_charge,
                    PT.interest_amount,
                    PT.principle_amount,
                    PT.total_paid_amount,
                    P.loan_amount,

                    DATE_FORMAT(
                        P.loan_date,
                        '%Y-%m-%d'
                    ) AS loan_date,

                    MONTH(
                        P.loan_date
                    ) AS loan_month,

                    MONTH(
                        '".$current_date."'
                    ) AS cur_month,

                    IF(PT.paid_on <> '', 0, 1) AS on_day,

                    DATE_FORMAT(
                        DATE_ADD(
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                            INTERVAL 1 MONTH
                        ),
                        '%d-%M-%Y'
                    ) AS due_date,

                    DATEDIFF(
                        '".$current_date."',
                        DATE_ADD(
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                            INTERVAL 1 MONTH
                        )
                    ) AS penalty_days,

                    DATEDIFF(

                        DATE_ADD(
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                            INTERVAL 1 MONTH
                        ),
                        IF(PT.paid_on <> '', PT.paid_on, P.loan_date)
                    ) AS first_month_days,

                    IF(
                        (
                            DATE_FORMAT(
                                DATE_ADD(
                                    IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                    INTERVAL 1 MONTH
                                ),
                                '%Y-%m-%d'
                            )
                        ) > '".$current_date."',

                        'No Due',

                        'Over Due'

                    ) AS penalty_status,

                    IF(PT.paid_on <> '', 'Paid', 'Not Paid') AS paid_on_status,

                    DATEDIFF(
                        '".$current_date."',
                        DATE_ADD(
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                            INTERVAL 1 MONTH
                        )

                    ) AS over_due_days,

                    DATE_FORMAT(
                        DATE_ADD(
                            P.loan_date,
                            INTERVAL 1 MONTH
                        ),
                        '%d-%M-%Y'
                    ) AS ini_due_date,

                    DATEDIFF(
                        '".$current_date."',
                        P.loan_date
                    ) AS total_days,

                    IF(
                        (
                          '".$current_date."' > DATE_FORMAT(DATE_SUB(DATE_ADD(
                                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                            INTERVAL 12 MONTH
                                         ), INTERVAL ".$wavier_days." DAY)
                                         ,'%Y-%m-%d')
                        ),
                        'Period End',
                        CONCAT(
                            DATEDIFF(
                                DATE_ADD(
                                    IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                    INTERVAL 12 MONTH
                                ),
                                '".$current_date."'
                            ),
                            ' Days are available to renew or close the Loan'
                        )

                    ) AS time_status,




                    DATEDIFF(
                        '".$current_date."',
                        PT.paid_on
                    ) AS no_days,


                    IF(
                        DATEDIFF(
                            DATE_FORMAT(
                                '".$current_date."',
                                '%Y-%m-%d'
                            ),
                            DATE_FORMAT(
                                DATE_ADD(
                                    IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                    INTERVAL 1 MONTH
                                ),
                                '%Y-%m-%d'
                            )
                        ) = 1,

                        (IF(DAYOFWEEK('".$current_date."')=2,1,0)),

                        0

                    ) AS default_wavier_days,


                    SC.interest_type,
                    IF(SC.interest_type=1,'floting','flat') AS scheme_type,
                    SC.service_charge,
                    SC.id AS scheme_id,
                    SC.name AS scheme_name,

                    P.total_net_weight AS product_net_weight,
                    SC.loan_rate AS loan_rate,P.redeem_status as redeem_status,P.old_redemption_loan_date as previous_loan_date


                    FROM ".PLEDGE." as P
                    LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                    LEFT JOIN ".SCHEME." as SC ON SC.id = P.scheme_id
                    WHERE P.id ='".$pledge_id."' ORDER BY PT.paid_on DESC";

        //echo $query;//exit;
        $product_interest = $app->getrows($query);

        //print_r($product_interest);

        // Common Unchanging variable
        $redeem_status = $product_interest[0]["redeem_status"];
        $previous_loan_date = $product_interest[0]["previous_loan_date"];
        $scheme_interest_type = $product_interest[0]["scheme_type"];
        $product_net_weight = floatval($product_interest[0]["product_net_weight"]);
        $loan_rate = floatval($product_interest[0]["loan_rate"]);

        // Other Calculative Variable
        $last_pay_id =  $product_interest[0]["pt_id"];
        $last_pay_date =  $product_interest[0]["paid_on"];
        $paid_date = $current_date;
        $loan_amount =  floatval($product_interest[0]["loan_amount"]);
        $loan_date =  $product_interest[0]["loan_date"];
        $no_of_days = ($product_interest[0]["no_days"]!='' || $product_interest[0]["no_days"]!=0) ?
                        $product_interest[0]["no_days"] :
                        $product_interest[0]["total_days"];
        $cumulative_days=$product_interest[0]["total_days"];
        $cur_month = $product_interest[0]["cur_month"];
        $loan_month = $product_interest[0]["loan_month"];
        $loan_due_date = $product_interest[0]["due_date"];

        $penalty_days = $product_interest[0]["penalty_days"];
        $first_month_days = $product_interest[0]["first_month_days"];
        $penalty_status = $product_interest[0]["penalty_status"];
        $paid_on_status = $product_interest[0]["paid_on_status"];

        $loan_ini_due_date = $product_interest[0]["ini_due_date"];
        $last_paid_amount = $product_interest[0]["total_paid_amount"];//$product_interest[0]["principle_amount"]+$product_interest[0]["interest_amount"]+$product_interest[0]["additional_charge"];
        $last_paid_principle_amount = $product_interest[0]["principle_amount"];
        $last_paid_interest_amount = $product_interest[0]["interest_amount"];
        $last_paid_additional_charge = $product_interest[0]["additional_charge"];
        $over_due_days = $product_interest[0]["over_due_days"];
        $no_of_day_from_loan = $product_interest[0]["no_of_day_from_loan"];
        $no_of_day_from_last_pay = $product_interest[0]["no_of_day_from_last_pay"];
        $on_day = floatval($product_interest[0]["on_day"]);
        $previous_last_day =  ($no_of_day_from_last_pay != '') ? $no_of_day_from_last_pay : $no_of_day_from_loan;

        $time_status = ($is_exceed) ? "Period End" : "Not End"; //$product_interest[0]["time_status"];

        //Default Wavier Settings
        $default_wavier_days = intval($product_interest[0]["default_wavier_days"]);
        $default_wavier_reason = ($default_wavier_days==1) ? "Yesterday is Sunday-Office Leave" : "";

        if($wavier_days > $default_wavier_days){
            $default_wavier_days = $wavier_days;
            $default_wavier_reason .= "Additional Wavier Added By ".$_SESSION['session_username']."(".$_SESSION['session_usergroupname'].")";
        }


        //Slab Wise Initial Rate of Interest Calculation
        $query = "SELECT SD.rate_of_interest, SD.above, SD.upto FROM ".PLEDGE." as P
                LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id
                WHERE P.id ='".$pledge_id."' AND SD.above=0";
                //echo $query;
        $ini_slab = $app->getrow($query);

        $ini_slab_rate = $ini_slab['rate_of_interest'];
        $ini_above = $ini_slab['above'];
        $ini_upto = $ini_slab['upto'];



        //Final Slab Interest Rate
        $query = "SELECT SD.rate_of_interest, SD.above, SD.upto FROM ".PLEDGE." as P
                LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id
                WHERE P.id ='".$pledge_id."' AND SD.upto = (SELECT MAX(upto) FROM ".SCHEME_DETAILS." WHERE scheme_id = SD.scheme_id GROUP BY scheme_id)";
        //echo $query;
        $final_slab = $app->getrow($query);

        $final_slab_rate = $final_slab['rate_of_interest'];
        $final_above = $final_slab['above'];
        $final_upto = $final_slab['upto'];


        //Slab Wise Current date Rate of Interest Calculation
        $query = "SELECT SD.rate_of_interest, SD.above, SD.upto FROM ".PLEDGE." as P
                        LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                        LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                        JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id
                        WHERE P.id ='".$pledge_id."' AND

                        '".$current_date."'

                        BETWEEN

                            DATE_FORMAT(
                                    DATE_ADD(
                                        DATE_ADD(
                                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                            INTERVAL SD.above MONTH
                                        ),
                                        INTERVAL 0 DAY
                                    ),
                                    '%Y-%m-%d'
                                )

                                AND

                            DATE_FORMAT(
                                    DATE_ADD(
                                        IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                        INTERVAL SD.upto MONTH
                                    ),
                                    '%Y-%m-%d'
                                )
                         ORDER BY PT.paid_on DESC
                        ";
                //echo $query;
        $current_slab = $app->getrow($query);
        if(count($current_slab)>0){
            $current_slab_rate = $current_slab['rate_of_interest'];
            $current_above = $current_slab['above'];
            $current_upto = $current_slab['upto'];
        }else{
            $current_slab_rate = $final_slab['rate_of_interest'];
            $current_above = $final_slab['above'];
            $current_upto = $final_slab['upto'];
        }


        //Time Exit Checking for Scheme wise interest data
        if($time_status != 'Period End'){
            $query = "SELECT
                        MONTH(P.loan_date) AS loan_month,
                        MONTH('".$current_date."') AS cur_month,
                        SD.id AS slab_id,

                        DATEDIFF(
                            '".$current_date."',
                            P.loan_date
                        ) AS total_days,

                        PERIOD_DIFF(
                            DATE_FORMAT(
                                '".$current_date."',
                                '%Y%m'
                            ),
                            DATE_FORMAT(
                                P.loan_date,
                                '%Y%m'
                            )
                        ) AS no_months,

                        DATEDIFF(
                            DATE_FORMAT(
                                DATE_ADD(
                                    IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                    INTERVAL 12 MONTH
                                ),
                                '%Y-%m-%d'
                            ),
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date)
                        )  AS div_days1,

                        '365' AS div_days,

                        SC.interest_type,
                        IF(SC.interest_type=1,'floting','flat') AS scheme_type,
                        SC.service_charge,
                        SC.id AS scheme_id,
                        SC.name AS scheme_name,

                        P.total_net_weight AS product_net_weight,
                        SC.loan_rate AS loan_rate,
                        SD.rate_of_interest

                        FROM ".PLEDGE." as P
                        LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                        LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                        JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id
                        WHERE P.id ='".$pledge_id."' AND

                        '".$current_date."'

                        BETWEEN

                            DATE_FORMAT(
                                    DATE_ADD(
                                        DATE_ADD(
                                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                            INTERVAL SD.above MONTH
                                        ),
                                        INTERVAL 0 DAY
                                    ),
                                    '%Y-%m-%d'
                                )

                                AND

                            DATE_FORMAT(
                                    DATE_ADD(
                                        IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                        INTERVAL SD.upto MONTH
                                    ),
                                    '%Y-%m-%d'
                                )

                        ORDER BY PT.paid_on DESC
                        ";



            $scheme_slab = $app->getrows($query);

            //echo "<br>{$query}<br>";
            //print_r($scheme_slab[0]);
                        $slab_rate = floatval($scheme_slab[0]["rate_of_interest"]);

            //$slab_interest_rate = ($scheme_interest_type == 'flat') ? floatval($slab_rate/12) : floatval($slab_rate);
            $slab_interest_rate = floatval($slab_rate);
            $slab_service_rate = floatval($scheme_slab[0]["service_charge"]);
            $no_months_from_loan_date = $scheme_slab[0]["no_months"];
            $slab_id = $scheme_slab[0]["slab_id"];

            $div_days = ($scheme_slab[0]["div_days"] > 0 ) ? $scheme_slab[0]["div_days"] : 365;
        }else{
            $query = "SELECT
                        MONTH(P.loan_date) AS loan_month,
                        MONTH('".$current_date."') AS cur_month,
                        -1 AS slab_id,

                        DATEDIFF(
                            '".$current_date."',
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date)
                        ) AS total_days,

                        PERIOD_DIFF(
                            DATE_FORMAT(
                                '".$current_date."',
                                '%Y%m'
                            ),
                            DATE_FORMAT(
                                P.loan_date,
                                '%Y%m'
                            )
                        ) AS no_months,

                        DATEDIFF(
                            DATE_FORMAT(
                                DATE_ADD(
                                    IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                    INTERVAL 12 MONTH
                                ),
                                '%Y-%m-%d'
                            ),
                            IF(PT.paid_on <> '', PT.paid_on, P.loan_date)
                        )  AS div_days,




                        P.total_net_weight AS product_net_weight,
                        SC.loan_rate AS loan_rate,

                        SC.interest_type,
                        IF(SC.interest_type=1,'floting','flat') AS scheme_type,
                        SC.service_charge,
                        SC.id AS scheme_id,
                        SC.name AS scheme_name

                        FROM ".PLEDGE." as P
                        LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                        LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                        WHERE P.id ='".$pledge_id."'
                        ORDER BY PT.paid_on DESC
                        ";
            //echo $query;

            $scheme_slab = $app->getrows($query);


            $slab_interest_rate = ($scheme_interest_type == 'flat') ? ($final_slab_rate) : $final_slab_rate ;
            $slab_service_rate = floatval($scheme_slab[0]["service_charge"]);
            $no_months_from_loan_date = $scheme_slab[0]["no_months"];
            $slab_id = $scheme_slab[0]["slab_id"];


            $div_days = ($scheme_slab[0]["div_days"] > 0 ) ? $scheme_slab[0]["div_days"] : 365;


        }

        $interest_rate = $slab_interest_rate;




        //Total Principle Amount Calculation
        if($scheme_interest_type == 'flat'){

           $query = "SELECT

                        SUM(PT.principle_amount) AS pamt_total_paid,
                        (
                            PERIOD_DIFF(
                            DATE_FORMAT(
                                '".$current_date."',
                                '%Y%m'
                            ),
                            DATE_FORMAT(
                                P.loan_date,
                                '%Y%m'
                            )
                            ) *
                            (P.loan_amount/12)
                        ) AS pamt_total_pay,

                        PERIOD_DIFF(
                            DATE_FORMAT(
                                '".$current_date."',
                                '%Y%m'
                            ),
                            DATE_FORMAT(
                                P.loan_date,
                                '%Y%m'
                            )
                        ) no_month,


                        (
                            IF(

                                PERIOD_DIFF(
                                    DATE_FORMAT(
                                        '".$current_date."',
                                        '%Y%m'
                                    ),
                                    DATE_FORMAT(
                                        P.loan_date,
                                        '%Y%m'
                                    )
                                ) > 12,

                                    P.loan_amount
                                    ,

                                    (
                                        PERIOD_DIFF(
                                            DATE_FORMAT(
                                                '".$current_date."',
                                                '%Y%m'
                                            ),
                                            DATE_FORMAT(
                                                P.loan_date,
                                                '%Y%m'
                                            )
                                        )

                                        *
                                        (P.loan_amount/12)
                                    )

                            )
                            -  (SUM(PT.principle_amount))

                        ) AS pamt_to_pay
                     FROM ".PLEDGE." as P
                     LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                     WHERE P.id ='".$pledge_id."'
                     ORDER BY PT.paid_on DESC
                     ";
              //echo $query;
            $sum_principle = $app->getrow($query);

            $principle_payment = $loan_amount/12;

            if(count($sum_principle)){

                $no_month = floatval($sum_principle["no_month"]);

                $total_paid_principle = $sum_principle["pamt_total_paid"];
                $total_pay_till_today = $sum_principle["pamt_total_pay"];

                if($time_status != 'Period End'){
                    $to_pay_principle = ($sum_principle["pamt_to_pay"]=='' || floatval($sum_principle["pamt_to_pay"]) > floatval($total_pay_till_today)) ? $total_pay_till_today : $sum_principle["pamt_to_pay"];
                }else{
                    $to_pay_principle = $loan_amount-$total_paid_principle;
                }

            }else{
                $total_paid_principle = 0;
                $to_pay_principle = 0;
                $total_pay_till_today = 0;
            }



        }else{
            $principle_payment = 0;
            $total_paid_principle = 0;
            $to_pay_principle = 0;
            $total_pay_till_today = 0;
        }




        //Interest rate fixing based on wavier
        if($default_wavier_days>0 && $time_status != 'Period End'){
            $query = "
                    SELECT

                    SD.rate_of_interest

                    FROM ".PLEDGE." as P
                    LEFT JOIN ".PRODUCT_INTERESTS." as PT ON PT.loan_id = P.id
                    LEFT JOIN ".SCHEME." AS SC  ON P.scheme_id = SC.id
                    JOIN ".SCHEME_DETAILS." AS SD ON SD.scheme_id = SC.id
                    WHERE P.id ='".$pledge_id."' AND

                    DATE_SUB('".$current_date."', INTERVAL ".$default_wavier_days." DAY)

                    BETWEEN

                        DATE_FORMAT(
                                DATE_ADD(
                                    DATE_ADD(
                                        IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                        INTERVAL SD.above MONTH
                                    ),
                                    INTERVAL 1 DAY
                                ),
                                '%Y-%m-%d'
                            )

                            AND

                        DATE_FORMAT(
                                DATE_SUB(
                                    DATE_ADD(
                                        IF(PT.paid_on <> '', PT.paid_on, P.loan_date),
                                        INTERVAL SD.upto MONTH
                                    ),
                                    INTERVAL 0 DAY
                                ),
                                '%Y-%m-%d'
                            )
                    ";
            // echo $query;
            $wavier_interest = $app->getrow($query);
            //print_r($wavier_interest);
            $interest_rate = $wavier_interest["rate_of_interest"];
            //echo $interest_rate;



        }
                if($new_loan_id == "") {
                    $new_query = " SELECT * FROM ".PLEDGE." as P WHERE P.id =".$pledge_id;
                    $org_loan_id = $app->getrow($new_query);
                    $new_loan_id = $org_loan_id['loan_id'];
                }


        $pay_date = date("Y-m-d",mktime(0,0,0,date("m",strtotime($current_date)),date("d",strtotime($current_date))-$default_wavier_days, date("Y",strtotime($current_date))));

        //Interest rate setting based on interest payment record and Slab
        if($over_due_days < 1 && $scheme_interest_type != 'flat' && $time_status != 'Period End'){
            // Came to pay interest before due
            $interest_rate = $ini_slab_rate;
        }


        //Redemtion Flat Annual Interest Rate Setting
        if($module_name!="pay_interest" && $module_name!="" && strtolower($scheme_interest_type) == 'flat'){

            $interest_rate = ($final_slab_rate);
            $normal_interest_rate = $final_slab_rate;
            $penalty_interest_rate = ($final_slab_rate);
        }/*else if($module_name =="pay_interest" && strtolower($scheme_interest_type) == 'flat'){
            $normal_interest_rate = $ini_slab_rate/12;
            $penalty_interest_rate = $current_slab_rate/12;
        }*/else{

            $normal_interest_rate = $ini_slab_rate;
            $penalty_interest_rate = $current_slab_rate;

        }

        if($no_of_days == 0 && $module_name!="pay_interest"){
            $no_of_days = 1;
        }

/*
    //Status variable value details for developer reference logical functional flow
    $time_status  [ 'Period End' , ' n Days are available to renew or close the Loan' ]
    $penalty_status [ 'No Due' , 'Over Due' ]
    $paid_on_status [ 'Paid' , 'Not Paid' ]
*/

       //**** Commented the overdue intial month calculation**** on 06-Nov-2011 -> To activate the initial month Calculation remove the comments and also below else condition
       //**** If you remove this comment you have to remove the comment in forms/payinterest.html, elements/loan_interest_details.html and remove JS script comment form the function_common_module.js-> pay_wavier_interest();

       // if($penalty_status == 'No Due'){

            //$no_of_days = $no_of_days + $on_day;

            //Interest Amount Calculation
            if($scheme_interest_type=="flat"){
                $per_day_interest = floatval(((($loan_amount-$total_paid_principle) / 100) * $interest_rate ) / $div_days);
            }else{
                $per_day_interest = floatval((($loan_amount/ 100) * $interest_rate ) / $div_days);
            }

			/*If the no of days is '0', interest amount calculate for the one day interest */
			if($no_of_days == 0)
				{
					$interest_amount = $per_day_interest * ( 1 - $default_wavier_days );
                    $no_of_days = 1;
				}
			else{
					//$interest_amount = $per_day_interest * ( $no_of_days - $default_wavier_days );
                $interest_amount = $per_day_interest * ( $cumulative_days - $default_wavier_days );
			}

            $default_wavier_amount = ($loan_amount * ($interest_rate / 100)) * ($default_wavier_days/$div_days);


       //**** Commented the overdue intial month calculation**** on 06-Nov-2011 -> To activate the initial month Calculation remove the comments and also above if condition
       //**** If you remove this comment you have to remove the comment in forms/payinterest.html, elements/loan_interest_details.html and remove JS script comment form the function_common_module.js-> pay_wavier_interest();

       /* }else{



            //Interest Amount Calculation
            if($scheme_interest_type=="flat"){


                $normal_per_day_interest = floatval(((($loan_amount-$total_paid_principle) / 100) * $normal_interest_rate ) / $div_days);
                $penalty_per_day_interest = floatval(((($loan_amount-$total_paid_principle) / 100) * $penalty_interest_rate ) / $div_days);




                if($module_name=="redemption"){

                $normal_wavier_days = 0;
                $penalty_wavier_days = $default_wavier_days;

                $normal_interest_days = 0;
                $penalty_interest_days = ( $first_month_days + $penalty_days ) - ( $normal_wavier_days + $penalty_wavier_days );


                }else{

                $normal_wavier_days = ($default_wavier_days > $penalty_days) ? $default_wavier_days - $penalty_days : 0;
                $penalty_wavier_days = ($default_wavier_days > $penalty_days) ? $penalty_days : $default_wavier_days;

                $normal_interest_days = ( $first_month_days - $normal_wavier_days );
                $penalty_interest_days = ( $penalty_days - $penalty_wavier_days );

                }

            }else{
                $normal_per_day_interest = floatval((($loan_amount/ 100) * $normal_interest_rate ) / $div_days);
                $penalty_per_day_interest = floatval((($loan_amount/ 100) * $penalty_interest_rate ) / $div_days);


                $normal_wavier_days = ($default_wavier_days > $penalty_days) ? $default_wavier_days - $penalty_days : 0;
                $penalty_wavier_days = ($default_wavier_days > $penalty_days) ? $penalty_days : $default_wavier_days;

                $normal_interest_days = ( $first_month_days - $normal_wavier_days );
                $penalty_interest_days = ( $penalty_days - $penalty_wavier_days );

            }

            $normal_interest_amount = ($normal_per_day_interest * $normal_interest_days) ;
            $penalty_interest_amount = ($penalty_per_day_interest * $penalty_interest_days);

            $normal_wavier_amount = (($loan_amount * ($normal_interest_rate / 100)) * ($normal_wavier_days / $div_days)) ;
            $penalty_wavier_amount = (($loan_amount * ($penalty_interest_rate / 100)) * ($penalty_wavier_days / $div_days));

            $interest_amount = $normal_interest_amount + $penalty_interest_amount;
            $default_wavier_amount =  $normal_wavier_amount + $penalty_wavier_amount;

            //$interest_rate = $current_slab_rate;

        } */



        $service_charge = ($loan_amount * ($slab_service_rate / 100));


        //Pay Excess Setup for the current scheme

        $excess_pay_amount = ($loan_rate * $product_net_weight) - ($loan_amount - $total_paid_principle);

        if($excess_pay_amount<0) $excess_pay_amount = 0;
        //echo $interest_amount."<br>".(int)$interest_amount;exit;
        if($interest_amount > (int)$interest_amount) $interest_amount = (int)$interest_amount + 1;


        $interest_details = array(
                                "pledge_id"             =>$pledge_id,
                                "loan_id"                    =>$new_loan_id,
                                "pay_id"                =>$pay_id,
                                "act_type"                  =>$type,
                                "module"                  =>$module,
                                "paid_on"               => $pay_date,
                                "interest_rate"         =>$interest_rate,
                                "product_net_weight"         =>$product_net_weight,
                                "loan_rate"         =>$loan_rate,
                                "excess_pay_amount"         =>$excess_pay_amount,
                                "slab_interest_rate"    =>$slab_interest_rate,
                                "ini_interest_rate"     =>$ini_slab_rate,
                                "current_interest_rate"     =>$current_slab_rate,
                                "final_interest_rate"     =>$final_slab_rate,
                                "ini_above"     =>$ini_above,
                                "current_above"     =>$current_above,
                                "final_above"     =>$final_above,
                                "ini_upto"     =>$ini_upto,
                                "current_upto"     =>$current_upto,
                                "final_upto"     =>$final_upto,
                                "interest"              =>$interest_amount,
                                "interest_amount"       =>$interest_amount,
                                "on_day"                   => $on_day,
                                "normal_wavier_days"       =>$normal_wavier_days,
                                "penalty_wavier_days"       =>$penalty_wavier_days,
                                "normal_wavier_amount"       =>$normal_wavier_amount,
                                "penalty_wavier_amount"       =>$penalty_wavier_amount,
                                "normal_interest_rate"       =>$normal_interest_rate,
                                "penalty_interest_rate"       =>$penalty_interest_rate,
                                "normal_interest_amount"       =>$normal_interest_amount,
                                "penalty_interest_amount"       =>$penalty_interest_amount,
                                "penalty_interest_days"          =>$penalty_interest_days,
                                "normal_interest_days"          =>$normal_interest_days,
                                "penalty_days"          =>$penalty_days,
                                "first_month_days"          =>$first_month_days,
                                "penalty_status"          =>$penalty_status,
                                "paid_on_status"          =>$paid_on_status,
                                "paid_date"                 =>$paid_date,
                                "service"               =>$service_charge,
                                "service_charge"        =>$service_charge,
                                "service_rate"        =>$slab_service_rate,
                                "slab_service_rate"        =>$slab_service_rate,
                                "per_day_interest"      =>$per_day_interest,
                                "interest_type"         =>$scheme_interest_type,
                                "loan_amount"           =>$loan_amount,
                                "last_pay_id"           =>$last_pay_id,
                                "last_pay_date"         =>$last_pay_date,
                                "last_paid_amount"              =>$last_paid_amount,
                                "last_paid_principle_amount"    =>$last_paid_principle_amount,
                                "last_paid_interest_amount"     =>$last_paid_interest_amount,
                                "last_paid_additional_charge"   =>$last_paid_additional_charge,
                                "loan_date"             =>$loan_date,
                                "no_of_days"            =>$no_of_days,
                                "cumulative_days"        => $cumulative_days,
                                "over_due_days"            =>$over_due_days,
                                "due_date"              =>$loan_due_date,
                                "ini_due_date"          =>$loan_ini_due_date,

                                "total_days_of_year"    =>$div_days,
                                "slab_id"               =>$slab_id,
                                "month_gab"             =>$no_months_from_loan_date,
                                "principle_payment"     =>$principle_payment,
                                "total_paid_principle"     =>$total_paid_principle,
                                "to_pay_principle"     =>$to_pay_principle,
                                "total_pay_till_today"     =>$total_pay_till_today,
                                "no_of_day_from_loan"     =>$no_of_day_from_loan,
                                "no_of_day_from_last_pay"     =>$no_of_day_from_last_pay,
                                "previous_last_day"     =>$previous_last_day,
                                "wavier_days"     =>$wavier_days,
                                "default_wavier_days"     =>$default_wavier_days,
                                "default_wavier_reason"     =>$default_wavier_reason,
                                "default_wavier_amount"     =>$default_wavier_amount,
                                "time_status"     =>$time_status,
                                "redeem_status"   => $redeem_status,
                                "previous_loan_date" => $previous_loan_date

                            );

// $redeem_status = $product_interest[0]["redeem_status"];
//         $previous_loan_date = $product_interest[0]["previous_loan_date"];
        //echo "<pre>"; print_r($interest_details); echo "</pre>";
       // exit;
        if($retVar=='')
            return $interest_details;
        else if(isset($interest_details[$retVar]))
            return $interest_details[$retVar];
        else
            return false;


    }


}
