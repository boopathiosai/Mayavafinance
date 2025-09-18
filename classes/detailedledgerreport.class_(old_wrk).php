<?php
 //**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Customer class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Karthik B
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  29-Oct-2011
 //**************************************************************************
 // Description      :  This is the Customer class  for Mayavafinance.
//**************************************************************************
class DetailedLedgerReport {


        public static function ListviewCount()
        {
        /*
         * Get the function parameters , if exist . And make the search condition
         */
        //Modified by karthik
        $args = func_get_args();

        $app = $GLOBALS['app'];
        //$s_branch = $args[0];

        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
		$s_sub_branch = $args[4];
		$s_key = $args[5];
		$search_key_location=$args[6];
         // Addded BY DSSK 15_MAR_2018
        $type_of_page = $args[7];

         if($type_of_page==1)
            {
              $search_condition  = " AND  ( P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
            }
// END
        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";
                $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";
              //  $search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
                
         // DSSK changed 15_MAR_2018 //
         if ((is_array($s_sub_branch)) && ($s_sub_branch != '') && ($s_sub_branch[0] > 0)) {
            foreach ($s_sub_branch as $key => $value) {
                $sub_branch_id_overall[] = $value;
            }
            $sub_branch_ids = implode(',', $sub_branch_id_overall);
            $search_condition .= ($s_sub_branch != "") ? "AND ( P.sub_branch_id IN(" . $sub_branch_ids . "))" : "";
        }
        // \.DSSK changed 15_MAR_2018//
				$search_condition .= ($s_key != "")? " AND  C.name  like '".$s_key."%'":"";
				$search_condition .= ($search_key_location != "")? " AND  C.location   like '".$search_key_location."%'":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif(((!is_superadmin($_SESSION['session_usergroupname'])&&$s_sdate == "" && $s_edate == "" && $s_sub_branch==""))){
            // echo "sdf";exit;
                 $sess_branch_id=$_SESSION['session_user_branch_id'];
                 $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE branch_id = $sess_branch_id";
                 $data_sub_id = $app->getrows($query_sub_branch);


                 $sub_branch_count=count($data_sub_id);
                 for($i=0; $i<$sub_branch_count;$i++){
                  $id[]=$data_sub_id[$i][0];
            }
                 $a=implode(',', array_map('intval', $id));
                 //echo $a;exit;
                // echo "<pre>";print_r($id);exit;
                $search_condition .= " AND  ( P.sub_branch_id IN($a))";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }




        /*
         * Return the Customers list
         */
        $query = "SELECT

        P.id

        FROM ". PLEDGE." AS P
        LEFT JOIN ".CUSTOMERS." AS C ON P.customer_id = C.id
        LEFT JOIN ". BRANCH." AS B ON B.id = P.branch_id
        LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
        LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
        LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
        LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
		LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
        WHERE 1 ".$search_condition."
        GROUP BY P.id
        ORDER BY P.id ";

        $data = $app->getrows($query);

        return $count = count($data);

       }

        public static function Listview()
        {
            /*
             * Get the function parameters , if exist . And make the search condition
             */
            //Modified by karthik
            $args = func_get_args();

            $app = $GLOBALS['app'];
            $paging = $GLOBALS['paging'];

        //$s_branch = $args[0];


        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
        $s_sub_branch = $args[4];
        $s_key = $args[5];
        $search_key_location = $args[6];
        // Addded BY DSSK 15_MAR_2018
        $type_of_page = $args[7];
//           echo "<pre>";print_r($args);

        if ($type_of_page == 1) {
            $search_condition .= " AND  ( P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";
        }

// END
        $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";
        //$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";
        // DSSK changes 15_MAR_2018 //
 if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
        {
          foreach ($s_sub_branch as $key => $value) {
            $sub_branch_id_overall[] = $value;
          }
          $sub_branch_ids = implode(',',$sub_branch_id_overall);
          $search_condition .= ($s_sub_branch != "")? "AND ( SB.id IN(".$sub_branch_ids."))":"";
        } 

// \.DSSK changes 15_MAR_2018 //
                
        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";
		$search_condition .= ($s_key != "")? " AND  C.name  like '".$s_key."%'":"";
		$search_condition .= ($search_key_location != "")? " AND  C.location   like '".$search_key_location."%'":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date  BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date  <= '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif(((!is_superadmin($_SESSION['session_usergroupname'])&&$s_sdate == "" && $s_edate == "" && $s_sub_branch==""))){
             // echo "sdf";exit;
                 $sess_branch_id=$_SESSION['session_user_branch_id'];
                 $query_sub_branch = "SELECT id FROM mf_sub_branch WHERE branch_id = $sess_branch_id";
                 $data_sub_id = $app->getrows($query_sub_branch);


                 $sub_branch_count=count($data_sub_id);
                 for($i=0; $i<$sub_branch_count;$i++){
                  $id[]=$data_sub_id[$i][0];
            }
                 $a=implode(',', array_map('intval', $id));
                // echo $a;exit;
                // echo "<pre>";print_r($id);exit;
                $search_condition .= " AND  ( P.sub_branch_id IN($a))";
            }
            
            
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }



//                echo $search_condition;
                //exit;

             /*
              * Return the Customers list count
              */
            $count = self::ListviewCount($s_lid,$s_sdate,$s_edate,$s_branch,$s_sub_branch,$s_key,$search_key_location,$type_of_page);

            if ($count > 0)
               {
                    /*
                    * Return the Customers list
                    */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging

                    $query = "SELECT
                    DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS report_date,
                    P.loan_id AS id,
                    P.customer_id,
                    P.loan_id,
                    P.branch_id,
					P.sub_branch_id,
                    SC.name AS scheme_name,

                    P.total_net_weight AS total_weight,
                    P.item_count AS total_items,

                    CONCAT(
                    C.name,' - (',C.customer_id,')','\n',
                    C.relationship_category, ' ', C.relation_name, '\n',
                    C.street,'\n',
                    C.location, '-',
                    C.postal_code, '\n',
                    'PH:',C.contact_no,'\n'                    
                    ) AS customer_details,

                    GROUP_CONCAT(
                    CT.category,'-',
                    PRO.name,'-[',PRO.product_with,',',
                    PRO.product_without,']'
                    ORDER BY CT.category DESC SEPARATOR '\n'
                    ) product_details,

                    P.loan_amount AS loan_amount,
                    IF(PAI.status=1 || P.status=0 ,'Closed','Open') AS auction_status
                    FROM ". PLEDGE." AS P
                    LEFT JOIN ".CUSTOMERS." AS C ON P.customer_id = C.id
                    LEFT JOIN ".BRANCH." AS B ON B.id = P.branch_id
                    LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                    LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                    LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                    LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
                    LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.loan_id = P.id
					LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                    WHERE 1 ".$search_condition."
                    GROUP BY P.id
                    ORDER BY P.created_datetime DESC". Paging::get_sql_limit();


                    //echo $query;
                    return $app->getrows($query);

               }
        }

        public static function subListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];

        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];



        $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";
        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }


            $search_having = ($s_lid != "") ? " AND ( loan_no LIKE '".$s_lid."')" : "";

            //$search_condition ="";
            if($s_sdate != "" && $s_edate != ""){
                $search_having .= " AND  ( report_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_having .= " AND  ( report_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_having .= " AND  ( report_date <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND  branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }

            /*
             * Return the Pledges list Count
             */
/*             $query = "SELECT

                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS report_date,

                        CF.branch_id AS branch_id,


                        (
                        IF(P.loan_id <> '',
                            P.loan_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    IF(PAI.loan_id <> '',
                                        (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PAI.loan_id),
                                        IF(PAO.loan_id <> '',
                                            (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PAO.loan_id),
                                            '-'
                                        )
                                    )
                                )
                            )

                        )
                        ) AS loan_no

                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF

                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id

                    LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                    LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id


                    LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.cash_in_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." as PAO ON PAO.cash_out_id = CF.id

                        HAVING  (loan_no <> '' )  ".$search_having;
*/                       // echo $query;

            $query = "SELECT
            COUNT(id)
            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
            WHERE CF.editable = 0 ".$search_condition ." ORDER BY CF.id ";


            $data = $app->getrow($query);





            return $count = $data[0];

       }

    public static function subListview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];


        $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";
        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND CF.branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }


            $search_having = ($s_lid != "") ? " AND ( loan_no LIKE '".$s_lid."')" : "";

            //$search_condition ="";
            if($s_sdate != "" && $s_edate != ""){
                $search_having .= " AND  ( report_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_having .= " AND  ( report_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_having .= " AND  ( report_date <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( CF.date_transaction = '".date("Y-m-d")."')";
            }

             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_having .= ($_SESSION['session_user_branch_id'] != "")?" AND  branch_id = '".$_SESSION['session_user_branch_id']."'":"";
             }

        $count = 1;//self::subListviewCount($s_lid, $s_sdate, $s_edate);
        /*
         * Return the Pledge list
         */



        if ($count > 0) {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging
            /*$query = "SELECT

                        DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                        DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,

                        P.customer_id,
                        P.loan_id,

                        CF.branch_id AS branch_id,

                        (
                        IF(P.loan_id <> '',
                            P.loan_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    IF(PAI.loan_id <> '',
                                        (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PAI.loan_id),
                                        IF(PAO.loan_id <> '',
                                            (SELECT sP.loan_id FROM ".PLEDGE." AS sP WHERE sP.id = PAO.loan_id),
                                            '-'
                                        )
                                    )
                                )
                            )

                        )
                        ) AS loan_no,


                        (
                        IF(P.loan_id <> '',
                            P.customer_id,
                            IF(PI.loan_id <> '',
                                (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PR.loan_id),
                                    IF(PAI.loan_id <> '',
                                        (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PAI.loan_id),
                                        IF(PAO.loan_id <> '',
                                            (SELECT sP.customer_id FROM ".PLEDGE." AS sP WHERE sP.id = PAO.loan_id),
                                            '-'
                                        )
                                    )
                                )
                            )

                        )
                        ) AS customer_no,



                        (
                        IF(P.loan_id <> '',
                            CONCAT(C.name,' - (',C.customer_id,')','\n',C.street,'\n',C.location, '-', C.postal_code, '\n','PH:',C.contact_no,'\n','Email:',C.email,'\n'),
                            IF(PI.loan_id <> '',
                                (SELECT CONCAT(sC.name,' - (',sC.customer_id,')','\n',sC.street,'\n',sC.location, '-', sC.postal_code, '\n','PH:',sC.contact_no,'\n','Email:',sC.email,'\n') FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PI.loan_id),
                                IF(PR.loan_id <> '',
                                    (SELECT  CONCAT(sC.name,' - (',sC.customer_id,')','\n',sC.street,'\n',sC.location, '-', sC.postal_code, '\n','PH:',sC.contact_no,'\n','Email:',sC.email,'\n')  FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PR.loan_id),
                                    IF(PAI.loan_id <> '',
                                        (SELECT  CONCAT(sC.name,' - (',sC.customer_id,')','\n',sC.street,'\n',sC.location, '-', sC.postal_code, '\n','PH:',sC.contact_no,'\n','Email:',sC.email,'\n')  FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PAI.loan_id),
                                        IF(PAO.loan_id <> '',
                                            (SELECT  CONCAT(sC.name,' - (',sC.customer_id,')','\n',sC.street,'\n',sC.location, '-', sC.postal_code, '\n','PH:',sC.contact_no,'\n','Email:',sC.email,'\n')  FROM ".CUSTOMERS." AS sC JOIN ".PLEDGE." AS sP  ON sP.customer_id = sC.id WHERE sP.id = PAO.loan_id),
                                            '-'
                                        )
                                    )
                                )
                            )
                        )
                        ) AS customer_details,

                        (
                        IF(P.loan_id <> '',
                            (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name,'-[',sPRO.product_with,',',sPRO.product_without,']' ORDER BY sCT.category DESC SEPARATOR '\n')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = P.id GROUP BY sP.id),
                            IF(PI.loan_id <> '',
                                (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name,'-[',sPRO.product_with,',',sPRO.product_without,']' ORDER BY sCT.category DESC SEPARATOR '\n')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PI.loan_id GROUP BY sP.id),
                                IF(PR.loan_id <> '',
                                    (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name,'-[',sPRO.product_with,',',sPRO.product_without,']' ORDER BY sCT.category DESC SEPARATOR '\n')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PR.loan_id GROUP BY sP.id),
                                    IF(PAI.loan_id <> '',
                                        (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name,'-[',sPRO.product_with,',',sPRO.product_without,']' ORDER BY sCT.category DESC SEPARATOR '\n')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PAI.loan_id GROUP BY sP.id),
                                        IF(PAO.loan_id <> '',
                                            (SELECT GROUP_CONCAT(sCT.category,'-',sPRO.name,'-[',sPRO.product_with,',',sPRO.product_without,']' ORDER BY sCT.category DESC SEPARATOR '\n')  FROM ".PLEDGE." AS sP LEFT JOIN ".PLEDGE_DETAILS." as sPD ON sP.id = sPD.pledge_id LEFT JOIN ".CATEGORIES." as sCT ON sCT.id = sPD.category_id  LEFT JOIN ".PRODUCTS." as sPRO on sPRO.id = sPD.product_id WHERE sP.id = PAO.loan_id GROUP BY sP.id),
                                            '-'
                                        )
                                    )
                                )
                            )

                        )
                        ) AS product_details,

                        CF.cash_purpose AS transaction_detail,



                        IF(CF.id=P.cash_flow_id,
                            P.loan_amount,
                            0.00
                        ) AS pleged_amount,

                        PAP.partial_amount AS partial_amount,
                        PEA.excess_amount AS excess_amount,

                        IF(CF.id=PI.cash_flow_id,
                            PI.interest_amount,
                            0.00
                        ) AS interest_amount,


                        IF(CF.id=PR.cash_flow_id,
                            PR.principle_amount,
                            0.00
                        ) AS redeam_amount,



                        IF(CF.id=PAI.cash_in_id,
                            PAI.auction_amount,
                            0.00
                        ) AS auction_amount,

                        IF(CF.id=PAO.cash_out_id,
                            (-1 * PAO.amount_difference),
                            0.00
                        ) AS auction_lose

                    FROM ".CASH_FLOW_TRANSACTIONS." AS CF

                    LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
                    LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEA ON PEA.new_loan_id = P.loan_id
                    LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAP ON PAP.new_loan_id = P.loan_id
                    LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id

                    LEFT JOIN ".PRODUCT_INTERESTS." as PI ON PI.cash_flow_id = CF.id
                    LEFT JOIN ".REDEMPTION." as PR ON PR.cash_flow_id = CF.id

                    LEFT JOIN ".PRODUCT_AUCTION." as PAI ON PAI.cash_in_id = CF.id
                    LEFT JOIN ".PRODUCT_AUCTION." as PAO ON PAO.cash_out_id = CF.id

                        HAVING (loan_no <> '' )  ".$search_having."


                        ORDER BY CF.id ";*/
            $query = "SELECT
                DATE_FORMAT(CF.date_transaction,'%Y-%m-%d') AS report_date,
                DATE_FORMAT(CF.date_transaction,'%d-%b-%Y') AS trans_date,
                P.customer_id,
                P.loan_id as loan_code,
                P.id as pledge_id,
                CF.id AS cash_flow_id,
                P.loan_amount,
                CF.branch_id AS branch_id
            FROM ".CASH_FLOW_TRANSACTIONS." AS CF
            LEFT JOIN ".PLEDGE." as P ON P.cash_flow_id = CF.id
            WHERE CF.editable = 0 ".$search_condition ." ORDER BY CF.id ";

            //echo $query."<hr>";
            //exit;

        $preresult = $app->getexport($query);
        $result = self::subresultData($preresult);

            return $result;

        }
    }

    public static function Listexport() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_lid = $args[0];
        $s_sdate = $args[1];
        $s_edate = $args[2];
        $s_branch = $args[3];
		$s_sub_branch = $args[4];
        $s_key = $args[5];
        $search_key_location = $args[6];

         // Addded BY DSSK 15_MAR_2018
         $type_of_page = $args[7];
            if($type_of_page==1)
            {
                $search_condition  .= " AND  ( P.loan_date BETWEEN DATE_SUB(NOW(),INTERVAL 3 DAY) AND NOW())";  
            }        
         //END
            
        $search_condition .= ($s_branch != "")? "AND ( P.branch_id  = '".$s_branch."' )  ":"";
        //$search_condition .= ($s_sub_branch != "")? "AND ( P.sub_branch_id  = '".$s_sub_branch."' )  ":"";

         // DSSK changes 15_MAR_2018 //
            if((is_array($s_sub_branch))&&($s_sub_branch!='')&&($s_sub_branch[0]>0))
                   {
                     foreach ($s_sub_branch as $key => $value) {
                       $sub_branch_id_overall[] = $value;
                     }
                     $sub_branch_ids = implode(',',$sub_branch_id_overall);
                     $search_condition .= ($s_sub_branch != "")? "AND ( SB.id IN(".$sub_branch_ids."))":"";
                   } 

        // \.DSSK changes 15_MAR_2018 //

        $search_condition .= ($s_key != "")? " AND  C.name  like '".$s_key."%'":"";
        $search_condition .= ($search_key_location != "")? " AND  C.location   like '".$search_key_location."%'":"";
        $search_condition .= ($s_lid != "")? "AND ( P.id = '".$s_lid."' OR P.loan_id = '".$s_lid."' OR P.loan_id = 'MFL".$s_lid."')  ":"";

            if($s_sdate != "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date BETWEEN '".date("Y-m-d",strtotime($s_sdate))."' AND '".date("Y-m-d",strtotime($s_edate))."')";
            }elseif($s_sdate != "" && $s_edate == ""){
                $search_condition .= " AND  ( P.loan_date >= '".date("Y-m-d",strtotime($s_sdate))."')";
            }elseif($s_sdate == "" && $s_edate != ""){
                $search_condition .= " AND  ( P.loan_date  <= '".date("Y-m-d",strtotime($s_edate))."')";
            }else{
                //$search_condition .= " AND  ( DATE_FORMAT(P.loan_date,'%Y-%m-%d') = '".date("Y-m-d")."')";
            }
             if(!is_superadmin($_SESSION['session_usergroupname'])){
                $search_condition .= ($_SESSION['session_user_branch_id'] != "")?" AND B.id = '".$_SESSION['session_user_branch_id']."'":"";
             }



            // echo "1".$search_condition;exit;
        //echo "src = ".$search_condition;

//        $count = self::ListviewCount($s_lid, $s_sdate, $s_edate,$s_sub_branch,$type_of_page);
//        $count = self::ListviewCount($s_lid, $s_sdate, $s_edate,$s_sub_branch,$s_key,$search_key_location,$type_of_page);
        $count = self::ListviewCount($s_lid,$s_sdate,$s_edate,$s_branch,$s_sub_branch,$s_key,$search_key_location,$type_of_page);
        /*
         * Return the Pledge list
         */
        if ($count > 0)
        {

               /* $query = "SELECT
                            P.loan_id,
                            DATE_FORMAT(P.loan_date,'%d-%b-%Y') AS loan_date,
                            P.loan_amount,

                            SC.name AS scheme_name,

                            CONCAT(
                                C.name,' - (',C.customer_id,')',', ',
                                C.street,', ',
                                C.location, ' - ', C.postal_code
                            ) AS customer_details,

                            GROUP_CONCAT(
                                CT.category,'-',PRO.name

                                ORDER BY CT.category DESC SEPARATOR '  ,\n'
                            ) AS product_details,


                            IF(PA.loan_id <> '',
                                'Returned - Bidded',
                                IF(PN.old_loan_id <> '',
                                    'Returned - Renewal',
                                    IF(PEA.old_loan_id <> '',
                                        'Returned - Excess Pay',
                                        IF(PAP.old_loan_id <> '',
                                            'Returned - Partial',

                                            IF(PR.loan_id <> '',
                                                'Returned - Redeemed',
                                                'Open'
                                            )
                                        )
                                    )
                                )
                            ) AS status_text,

                            P.total_net_weight AS net_weight,
                            P.item_count AS quantity,

                            IF(P.status = 1,
                                'Open',
                                IF(PA.loan_id <> '',
                                    'Bidded',
                                    'Returned'
                                )
                             )   AS status_text1,

                            IF(PA.id <> '',
                                DATE_FORMAT(PA.paid_on,'%d-%b-%y'),
                                IF(PR.id <> '',
                                    DATE_FORMAT(PR.paid_on,'%d-%b-%y'),
                                    '-'
                                )
                            ) AS return_date



                            FROM ".PLEDGE." as P
                            LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
                            LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                            LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                            LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                            LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                            LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
                            LEFT JOIN ".REDEMPTION." as PR on PR.loan_id = P.id
                            LEFT JOIN ".RENEWAL." as PN on PN.old_loan_id = P.id
                            LEFT JOIN ".PAY_EXCESS_AMOUNT." as PEA on PEA.old_loan_id = P.id
                            LEFT JOIN ".PARTIAL_AMOUNT_PAYMENT." as PAP on PAP.old_loan_id = P.id
                            LEFT JOIN ".PRODUCT_AUCTION." as PA on PA.loan_id = P.id
                            WHERE 1 ".$search_condition."
                            GROUP BY P.loan_id
                            ORDER BY P.id ";*/

            $query = "SELECT
                    P.id AS pledge_id,
                    P.loan_id AS loan_code,
                    P.loan_date AS loan_date,
                    P.loan_amount,

                    SC.name AS scheme_name,

                    CONCAT(
                        C.name,' - (',C.customer_id,')',', ',
                        C.relationship_category, ' ', C.relation_name,', ', 
                        C.street,', ',
                        C.location, ' - ', C.postal_code,', ', 
                        'PH:',C.contact_no
                    ) AS customer_details,

                    GROUP_CONCAT(
                        CT.category,'-',PRO.name

                        ORDER BY CT.category DESC SEPARATOR '  ,\n'
                    ) AS product_details,
                    P.total_net_weight AS net_weight,
                    P.item_count AS quantity,
                    IF(P.status = 1,'Open','Returned') AS status_text
                FROM ".PLEDGE." as P
                LEFT JOIN ".BRANCH." as B ON B.id=P.branch_id
                LEFT JOIN ".PLEDGE_DETAILS." as PD ON P.id = PD.pledge_id
                LEFT JOIN ".CATEGORIES." as CT ON CT.id = PD.category_id
                LEFT JOIN ".CUSTOMERS." as C ON C.id = P.customer_id
                LEFT JOIN ".SCHEME." as SC ON P.scheme_id = SC.id
                LEFT JOIN ".PRODUCTS." as PRO on PRO.id = PD.product_id
						LEFT JOIN ".SUB_BRANCH." AS SB ON SB.id = P.sub_branch_id
                WHERE 1 ".$search_condition."
                GROUP BY P.loan_id
                ORDER BY P.created_datetime DESC";


        $preresult = $app->getexport($query);
        $result = self::resultData($preresult);


                return $result;

    }
    }

    private static function resultData($reports){

       global $app;

       $result = array();

       if(count($reports)>0){

          $dpi = 0;
          $di = 1;

          foreach($reports as $rows)
          {
            /** Set Result data for the record **/

            $weight_in = floatval($rows['net_weight']);

            $weight_in_gs =  intval($weight_in);
            $weight_in_mgs =  intval(floatval($weight_in - intval($weight_in)) * 1000);

            $rows['weight_in'] = $weight_in;
            $rows['weight_in_gs'] = $weight_in_gs;
            $rows['weight_in_mgs'] = $weight_in_mgs;

            $loan_id = $rows['pledge_id'];
            $loan_code = $rows['loan_code'];
            $paid_on = '-';

            $pquery = "SELECT COUNT(id) FROM ".PRODUCT_AUCTION." WHERE loan_id =  ".$loan_id;
            $pc = $app->getrow($pquery);
            if((int)$pc[0] > 0){

                $status_text = "Bidded";

                $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM ".PRODUCT_AUCTION." WHERE loan_id =  ".$loan_id;
                $pc = $app->getrow($pquery);
                $paid_on = $pc[0];


            }else{

                //$rquery = "SELECT COUNT(id)  FROM ".REDEMPTION." WHERE loan_id =  ".$loan_id;
                $pquery = "SELECT COUNT(id) FROM ".RENEWAL." WHERE old_loan_id =  ".$loan_id;
                $pc = $app->getrow($pquery);
                if((int)$pc[0] > 0){
                  $status_text = "Renewed";

                $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM ".RENEWAL." WHERE old_loan_id =  ".$loan_id;
                $pc = $app->getrow($pquery);
                $paid_on = $pc[0];


                }else{
                    $pquery = "SELECT COUNT(id) FROM ".PAY_EXCESS_AMOUNT." WHERE old_loan_id =  ".$loan_id;
                    $pc = $app->getrow($pquery);
                    if((int)$pc[0] > 0){

                      $status_text = "Excess paid";
                        $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM ".PAY_EXCESS_AMOUNT." WHERE old_loan_id =  ".$loan_id;
                        $pc = $app->getrow($pquery);
                        $paid_on = $pc[0];



                    }else{
                        $pquery = "SELECT COUNT(id) FROM ".PARTIAL_AMOUNT_PAYMENT." WHERE old_loan_id =  ".$loan_id;
                        $pc = $app->getrow($pquery);
                        if((int)$pc[0] > 0){
                          $status_text = "Partial paid";
                        $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM ".PARTIAL_AMOUNT_PAYMENT." WHERE old_loan_id =  ".$loan_id;
                        $pc = $app->getrow($pquery);
                        $paid_on = $pc[0];

                        }else{
                            $pquery = "SELECT COUNT(id)  FROM ".REDEMPTION." WHERE loan_id =  ".$loan_id;
                            $pc = $app->getrow($pquery);
                            if((int)$pc[0] > 0){
                              $status_text = "Redeemed";
                        $pquery = "SELECT  DATE_FORMAT(paid_on,'%d-%b-%y') FROM ".REDEMPTION." WHERE loan_id =  ".$loan_id;
                        $pc = $app->getrow($pquery);
                        $paid_on = $pc[0];

                            }
                        }

                    }
                }
            }

            if(@$rows['status_text'] != '' && $rows['status_text'] != 'Open'){
                $rows['status_text'] .= '-'.$status_text;
            }

            if(@$rows['return_date'] == ''){


                $rows['return_date'] = $paid_on;
            }


            $result[] = $rows;
          }
        }else{
          $result = $reports;
        }

        return $result;

    }


    private static function subresultData($reports){

       global $app;

       $result = array();

       if(count($reports)>0){


          foreach($reports as $rows)
          {
            /** Set Result data for the record **/

            $loan_id = $rows['pledge_id'];
            $loan_code = $rows['loan_code'];
            $paid_on = $rows['report_date'];;

            /*$loan_id = 0;
            $pquery = "SELECT id FROM ".PLEDGE." WHERE cash_flow_id = ".$rows['cash_flow_id'];
            $pcid = $app->getrow($pquery);

            if((int)$pcid[0] > 0){
              $cash_purpose = CASHFLOW_PURPOSE_PLEDGED;
              $loan_id = (int)$pcid[0];
            }else{

                $rquery = "SELECT loan_id FROM ".REDEMPTION." WHERE cash_flow_id =  ".$rows['cash_flow_id'];
                $rcid = $app->getrow($rquery);
                if((int)$rcid[0] > 0){
                  $cash_purpose = CASHFLOW_PURPOSE_REDEEMED;
                  $loan_id = (int)$rcid[0];
                }else{

                    $apquery = "SELECT loan_id FROM ".PRODUCT_AUCTION." WHERE cash_in_id =  ".$rows['cash_flow_id'];
                    $apcid = $app->getrow($apquery);
                    if((int)$apcid[0] > 0){
                      $cash_purpose = AUCTION_AMOUNT;
                      $loan_id = (int)$apcid[0];
                    }else{

                        $alquery = "SELECT loan_id FROM ".PRODUCT_AUCTION." WHERE cash_out_id = ".$rows['cash_flow_id'];
                        $alcid = $app->getrow($alquery);
                        if((int)$alcid[0] > 0){
                          $cash_purpose = CASHFLOW_PURPOSE_AUCTION_SERVICE;
                          $loan_id = (int)$alcid[0];
                        }else{

                            //
                            $piquery = "SELECT loan_id FROM ".PRODUCT_INTERESTS." WHERE cash_flow_id = ".$rows['cash_flow_id'];
                            $picid = $app->getrow($piquery);
                            if((int)$picid[0] > 0){
                              $cash_purpose = PAY_INTEREST;
                              $loan_id = (int)$picid[0];
                            }
                        }

                    }
                }
            }
            */
            if($loan_id > 0){

                $rows['transaction_detail'] = $cash_purpose;

                if((int)$rows['pleged_amount'] == 0){
                    $paq = "SELECT loan_amount FROM ".PLEDGE." WHERE id = ".$loan_id;
                    $pa = $app->getrow($paq);
                    $rows['pleged_amount'] = (int)$pa[0];
                }

                if((int)$rows['interest_amount'] == 0){
                    $paq = "SELECT SUM(interest_amount) FROM ".PRODUCT_INTERESTS." WHERE loan_id = ".$loan_id;
                    $pa = $app->getrow($paq);
                    $rows['interest_amount'] = (int)$pa[0];
                }

                if((int)$rows['redeam_amount'] == 0){
                    $paq = "SELECT principle_amount FROM ".REDEMPTION." WHERE loan_id = ".$loan_id;
                    $pa = $app->getrow($paq);
                    $rows['redeam_amount'] = (int)$pa[0];
                }

                if((int)$rows['partial_amount'] == 0){
                    $paq = "SELECT partial_amount FROM ".PARTIAL_AMOUNT_PAYMENT." WHERE old_loan_id = ".$loan_id;
                    $pa = $app->getrow($paq);
                    $rows['partial_amount'] = (int)$pa[0];
                }

                if((int)$rows['excess_amount'] == 0){
                    $paq = "SELECT excess_amount FROM ".PAY_EXCESS_AMOUNT." WHERE old_loan_id = ".$loan_id;
                    $pa = $app->getrow($paq);
                    $rows['excess_amount'] = (int)$pa[0];
                }

                if((int)$rows['auction_lose'] == 0 && (int)$rows['auction_profit'] == 0){
                    $paq = "SELECT interest_amount, auction_charge, auction_amount,amount_difference FROM ".PRODUCT_AUCTION." WHERE loan_id =  ".$loan_id;
                    $pa = $app->getrow($paq);
                    //$amount_diff = (int)$pa[2] - intval($rows['pleged_amount']+$pa[0]+$pa[1]);
                    $amount_diff = (int)$pa[3];
                    if($amount_diff < 0){
                        $amount_diff = -1 * $amount_diff;
                        $rows['auction_profit'] = 0;
                        $rows['auction_lose'] = $amount_diff;
                    }elseif($amount_diff > 0){
                        $rows['auction_profit'] = $amount_diff;
                        $rows['auction_lose'] = 0;
                    }else{
                        $rows['auction_profit'] = $amount_diff;
                        $rows['auction_lose'] = $amount_diff;
                    }

                }

                /*if((int)$rows['auction_profit'] == 0){
                    $paq = "SELECT auction_amount FROM ".PRODUCT_AUCTION." WHERE cash_in_id =  ".$rows['cash_flow_id'];
                    $pa = $app->getrow($paq);
                    $rows['auction_profit'] = (int)$pa[0];
                }

                if((int)$rows['auction_lose'] == 0){
                    $paq = "SELECT auction_charge FROM ".PRODUCT_AUCTION." WHERE cash_out_id =  ".$rows['cash_flow_id'];
                    $pa = $app->getrow($paq);
                    $rows['auction_lose'] = (int)$pa[0];
                }*/

            }

            $result[] = $rows;
          }
        }else{
          $result = $reports;
        }

        return $result;

    }


}
