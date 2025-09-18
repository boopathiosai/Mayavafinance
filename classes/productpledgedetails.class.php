<?php
 //**************************************************************************
  //**************************************************************************
  // Project          :  MAYAVA FINANCE                   
  // Module           :  Product class          
  // Domain           :  http://wwa.mayavafinance.com
  // Programmer       :  Vinothkuamr V
  // Inprogress       :  \\offlinev5\Mayavafinance
  // Backup           :  \\dataserver\EIT\Projects\In Progress\
  // Last Modified    :  05-Feb-2010
  //**************************************************************************
  // Description      :  This is the Product class  for Mayavafinance.
 //************************************************************************** 


class ProductPledgeDetails {
	    /*
	 	 * Define the required fields and return text
	 	 */
	 	public $check_required = array('name', 'gross_weight', 'net_weight', 'category_id');
	 	public $check_duplication = array('code');
	 	 
	 	 
	 	public static $check_required_text = 'Required Fields Missing!';
	 	public static $check_duplication_text = 'Product Code Name Already exists!';
	    
		public static function ListviewCount() {
							/*
							 * Get the function parameters , if exist . And make the search condition
							 */
							
							$args = func_get_args();
							 
							$app = $GLOBALS['app'];
							$s_key = $args[0]; 
							$s_category = $args[1]; 
							$search_condition .= ($s_key != "")? " AND  name  like '".$s_key."%'":""; 
							$search_condition .= ($s_category != "")? "AND category_id=".$s_category."":"";
							/*
							 * Return the Products list Count
							 */
							$query = "SELECT id
								FROM ".PLEDGE_DETAILS."
								WHERE 1
								".$search_condition."
								ORDER BY id DESC ";
								$data = $app->getrows($query);								
								return $count = count($data);

							 
	 }

	    public static function Listview() {
				/*
				 * Get the function parameters , if exist . And make the search condition
				 */
				
				$args = func_get_args();
				$app = $GLOBALS['app'];
				$s_key = $args[0];
				$s_category = $args[1];
				$search_condition .= ($s_key != "")? " AND  PD.name  like '".$s_key."%'":"";  
				$search_condition .= ($s_category != "")? "AND PD.category_id = ".$s_category."":"";
				$count = self::ListviewCount($s_key,$s_category);
				/*
				 * Return the Product list
				 */
				if ($count > 0) {
					Paging::set_max( $count );
		        	Paging::calc();// calculation for paging
					$query = "SELECT PD.id, PD.product_id, C.category as category, PD.name, PD.status,
					          IF(PD.status = 0, 'disable', 'enable') AS status_text 
								FROM ".PLEDGE_DETAILS." as PD
						        LEFT JOIN ".CATEGORIES." as C ON C.id = PD.category_id
								WHERE 1
								".$search_condition."
								ORDER BY PD.id DESC ". Paging::get_sql_limit();					
					return $app->getrows($query);
				}
	}
	public static function Detail($userid) {
			/*
			 * Return the Product details
			 */
			$app = $GLOBALS['app'];
			$count = $app->find_count(array('conditions' => array('id =' => $userid)));
			if ($count > 0) {
				$query = "SELECT *
							FROM ".PLEDGE_DETAILS." WHERE id ='".$userid."'";							
				return $app->getrows($query);
			}	
	}

	public static function WriteInfo() {

		$app = $GLOBALS['app'];

		$app->access_table = PLEDGE_DETAILS;
		$data = $app->find(array('id', 'name'));
		$count = count($data);
		$pledge_data = array();
       
		if($count > 0) {

			foreach($data as $option) {
				$pledge_data[] = "{name:".str_replace('"', '\"', $option['name']).", id:".str_replace('"', '\"', $option['id'])."";
				//$pledge_values[] = str_replace('"', '\"', $option['id']);
			}
		}
		
		$app->access_table = CUSTOMERS;
		$data = $app->find(array('id ', 'name'));
		$count = count($data);
		$customer_data = array();
        echo "<PRE>";
		print_r($pledge_data);
		exit;
		if($count > 0) {

			foreach($data as $attribute) {
				$customer_data[] = "{name:'".str_replace('"', '\"', $attribute['name'])."', id:'".str_replace('"', '\"', $attribute['id'])."'";
				//$customer_data[] .= str_replace('"', '\"', $attribute['id']);
			}
		}
		
		//echo "<PRE>";
		//print_r($customer_values);
		//exit;

		if(count($pledge_data) != 0 && count($pledge_values) != 0 ) {
			$json_content = 'var pledge_names = ["' . implode('", "', $pledge_data) . '"];' . "\n\n";
		}
		else {
			$json_content = 'var pledge_names = [];' . "\n\n";
		}

		/*if(count($pledge_values) != 0 ) {
			$json_content = 'var pledge_values = ["' . implode('", "', $pledge_values) . '"];' . "\n\n";
		}
		else {
			$json_content = 'var pledge_values = [];' . "\n\n";
		}


		if(count($customer_data) != 0 ) {
			$json_content .= 'var customer_names = ["' . implode('", "', $customer_data) . '"];' . "\n\n";
		}
		else {
			$json_content .= 'var customer_names = [];' . "\n\n";
		}*/
		
		if(count($customer_names) != 0 && count($pledge_values) != 0 ) {
			$json_content .= 'var customer_names = [' . implode(', ', $customer_names) .'];' . "\n\n";
		}
		else {
			$json_content .= 'var customer_names = [];' . "\n\n";
		}
		/*
		if(count($customer_values) != 0 ) {
			$json_content .= 'var customer_values = ["' . implode('", "', $customer_values) . '"];' . "\n\n";
		}
		else {
			$json_content .= 'var customer_values = [];' . "\n\n";
		}*/


		file_put_contents("js/data/localdata.js", stripslashes($json_content));
	}

	
}
