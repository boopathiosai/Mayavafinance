<?php

class TallyReport {   
        
        
        
        public static function ListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();		
			$app = $GLOBALS['app'];
			$s_branch = $args[0];
			$s_sub_branch = $args[1];
			$s_sdate = $args[2];
			$s_edate = $args[3];
			$s_search_type = $args[4];     
			$count = 0;
			$app = $GLOBALS['app'];
			if($s_branch != '' && $s_sub_branch != '' && $s_sdate != "" && $s_edate != "" &&  $s_search_type != ""){		
					if($s_sdate != $s_edate){
						$where = "BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate))."'";
						
					}else{
						$where = "like '%".date("Y-m-d", strtotime($s_sdate))."%'";
					}			
				if($s_search_type == 1){
					//select loan_date, loan_id, count(*) as count, sum(loan_amount) as totalAmount , max(loan_id), branch_id, sub_branch_id from mf_pledge WHERE branch_id = 22 AND sub_branch_id = 37 and loan_date BETWEEN '2015-01-01' and '2017-01-10' group by DATE_FORMAT(loan_date,'%Y-%m-%d')
					//echo $query = "SELECT loan_date, loan_id, count(*) as count, sum(loan_amount) as totalAmount , max(loan_id), branch_id, sub_branch_id FROM `mf_pledge` WHERE branch_id = ".$s_branch." AND sub_branch_id = ".$s_sub_branch." and loan_date BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "' group by DATE_FORMAT(loan_date,'%Y-%m-%d')";
					
					$query = "SELECT
					'redemption' as type,
								paid_on, 
								p.loan_id as loan_id,  
								DATEDIFF(paid_on,'$search_year-4-01') as pay_no, 
								count(*) as count,  
								sum(r.principle_amount) as principle_amount, 
								sum(r.interest_amount) as interest_amount, 
								r.branch_id, 
								r.sub_branch_id FROM `mf_redemption` as r 
								LEFT JOIN mf_pledge as p on p.id = r.loan_id
								WHERE 
								r.branch_id = ".$s_branch." 
								AND r.sub_branch_id =  ".$s_sub_branch."
								and r.paid_on  $where /* BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "' */
								group by DATE_FORMAT(r.paid_on,'%Y-%m-%d')";
								
				}else{
					$query = "SELECT 
								loan_date, 
								loan_id, 
								count(*) as count, 
								sum(loan_amount) as totalAmount , 
								max(loan_id), 
								branch_id, 
								sub_branch_id 
								FROM `mf_pledge` 
								WHERE branch_id = ".$s_branch." 
								AND sub_branch_id = ".$s_sub_branch." 
								and loan_date $where  /*BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "' */
								group by DATE_FORMAT(loan_date,'%Y-%m-%d')";
				}				
				$data = $app->getrows($query);
				$count = count($data);
            }
            return $count;
       }


    public static function Listview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];
        $s_branch = $args[0];
        $s_sub_branch = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        $s_search_type = $args[4];	
        $forxls = $args[5];	
		$sdate = explode('-', $s_sdate);
		$search_year = $sdate[2];
        $count = self::ListviewCount($s_branch, $s_sub_branch, $s_sdate, $s_edate, $s_search_type);		 
		if ($s_sdate != "" && $s_edate != "") {
			$search_condition .= " AND  ( loan_date BETWEEN '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "')";
		} 
		
		if($s_sdate != $s_edate){
			$where = "BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate))."'";
			
		}else{
			$where = "like '%".date("Y-m-d", strtotime($s_sdate))."%'";
		}
		
        //$count  = 10;
        if ($count > 0) {
			//select loan_date, loan_id, count(*) as count, sum(loan_amount) as totalAmount , max(loan_id), branch_id, sub_branch_id from mf_pledge WHERE branch_id = 22 AND sub_branch_id = 37 and loan_date BETWEEN '2015-01-01' and '2017-01-10' group by DATE_FORMAT(loan_date,'%Y-%m-%d')
            /*
             * Return the Customers list
             */
            Paging::set_max($count);
            Paging::calc(); // calculation for paging
			if($forxls == 'xls'){
				$page_limit ='';
			}else{				
				$page_limit = Paging::get_sql_limit();
			}
			
			if($s_search_type == 1){
					//select loan_date, loan_id, count(*) as count, sum(loan_amount) as totalAmount , max(loan_id), branch_id, sub_branch_id from mf_pledge WHERE branch_id = 22 AND sub_branch_id = 37 and loan_date BETWEEN '2015-01-01' and '2017-01-10' group by DATE_FORMAT(loan_date,'%Y-%m-%d')
					//echo $query = "SELECT loan_date, loan_id, count(*) as count, sum(loan_amount) as totalAmount , max(loan_id), branch_id, sub_branch_id FROM `mf_pledge` WHERE branch_id = ".$s_branch." AND sub_branch_id = ".$s_sub_branch." and loan_date BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "' group by DATE_FORMAT(loan_date,'%Y-%m-%d')";
					$query = "SELECT 
								'redemption' as type,
								paid_on, 
								p.loan_id as loan_id,  
								DATEDIFF(paid_on,'$search_year-4-01') as pay_no, 
								count(*) as count, 
								'Cash in Hand'  as cash, 
								sum(r.principle_amount) as principle_amount, 
								/*sum(r.interest_amount) as interest_amount, */
								 Sum( Case 
										When sd.rate_of_interest = 18 Then CEIL(r.interest_amount*0.666667) 
										When sd.rate_of_interest = 24 Then CEIL(r.interest_amount*0.5)
										When sd.rate_of_interest = 36 Then CEIL(r.interest_amount*0.333333)
										When sd.rate_of_interest = 60 Then CEIL(r.interest_amount*0.2) End ) as interest_amount, 
								'NA' as chk_no, 
								'NA' as chk_date, 
								r.branch_id,
								sb.sub_branch_code as sub_branch_code,
								r.sub_branch_id FROM `mf_redemption` as r 
								LEFT JOIN mf_pledge as p on p.id = r.loan_id
								LEFT JOIN mf_sub_branch as sb on sb.id = r.sub_branch_id
								LEFT JOIN mf_scheme_details as sd on sd.scheme_id = p.scheme_id
								WHERE 
								r.branch_id = ".$s_branch." 
								AND r.sub_branch_id =  ".$s_sub_branch."
								and r.paid_on $where /*BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "' */
								group by DATE_FORMAT(r.paid_on,'%Y-%m-%d')".$page_limit;	
			}else{
				 $query = "SELECT 
					'pledge' as type, 
					 p.loan_date, count(*) as count, 
					'Cash in Hand' as cash, 
					'Jewell mortgage loan' as acc, 
					'NA' as chk_no, 
					'NA' as chk_date, 
					sum(p.loan_amount) as totalAmount, 
					p.loan_id, DATEDIFF(p.loan_date,'$search_year-4-01') as pay_no, 
					max(p.loan_id) as max_loanid, 
					p.branch_id, p.sub_branch_id, 
					sb.sub_branch_code as sub_branch_code
					FROM mf_pledge as p
					LEFT JOIN mf_sub_branch as sb on sb.id = p.sub_branch_id 
					WHERE p.branch_id = ".$s_branch." 
					AND p.sub_branch_id = ".$s_sub_branch." 
					and p.loan_date $where /* BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "' */
					group by DATE_FORMAT(p.loan_date,'%Y-%m-%d')".$page_limit;	
					
			}
			
			//$query = "SELECT loan_date, loan_id, count(*) as count, sum(loan_amount) as totalAmount , max(loan_id), branch_id, sub_branch_id FROM `mf_pledge` WHERE branch_id = ".$s_branch." AND sub_branch_id = ".$s_sub_branch." and loan_date BETWEEN  '" . date("Y-m-d", strtotime($s_sdate)) . "' AND '" . date("Y-m-d", strtotime($s_edate)) . "' group by DATE_FORMAT(loan_date,'%Y-%m-%d')".Paging::get_sql_limit();;
            // echo $query = "SELECT CAST(loan_date AS DATE), sum(loan_amount), count(*), loan_id, max(loan_id) FROM `mf_pledge` WHERE branch_id = ".$s_branch." AND sub_branch_id = ".$s_sub_branch. $search_condition . " group by loan_date" . Paging::get_sql_limit();
            return $app->getrows($query); 
        }    
        
    }
    
    
   public static function subListviewCount() {
            /*
             * Get the function parameters , if exist . And make the search condition
             */

            $args = func_get_args();

            $app = $GLOBALS['app'];
            
            $s_branch = $args[0];
            $s_user = $args[1];
            $s_sdate = $args[2];
            $s_edate = $args[3];

            /*
             * Return the Pledges list Count
             */

            $query = self::getSubListQuery($s_branch, $s_user, $s_sdate, $s_edate);
            //echo $query;
            $data = $app->getrows($query);
            
            return $count = count($data);
     
       }


    public static function subListview() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
        $count = self::subListviewCount($s_branch, $s_user, $s_sdate, $s_edate);

        if ($count > 0) 
        {
            Paging::set_max( $count );
            Paging::calc();// calculation for paging

            $query = self::getSubListQuery($s_branch, $s_user, $s_sdate, $s_edate);
            //echo "<pre>".$query."</pre>";
            //exit;
            return $app->getrows($query);
        }    
        
    }

    public static function Listexport() {

        /*
         * Get the function parameters , if exist . And make the search condition
         */

        $args = func_get_args();
        $app = $GLOBALS['app'];

        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
                
        $s_branch = $args[0];
        $s_user = $args[1];
        $s_sdate = $args[2];
        $s_edate = $args[3];
        
        $query = self::getSubListQuery($s_branch, $s_user, $s_sdate, $s_edate);
        //echo $query;
        //exit;
        return $app->getexport($query);


    }
    

}