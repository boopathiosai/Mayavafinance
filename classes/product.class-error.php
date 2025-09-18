<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Product class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Subash S
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  07-Jul-2010
//**************************************************************************
// Description      :  This is the Product class  for Mayavafinance.
//**************************************************************************

class Product {
	 /*
	 	 * Define the required fields and return text
	 	 */
	 	public $check_required = array('name', 'category_id');
	 	public $check_duplication = array('name');

		public static $check_required_text = 'Required Fields Missing!';
	 	public static $check_duplication_text = 'Product Name Already exists!';

	   public static function ListviewCount() {
				/*
				 * Get the function parameters , if exist . And make the search condition
				 */

				$args = func_get_args();

				$app = $GLOBALS['app'];

				$s_key = $args[0];
				$s_category = $args[1];
				$search_condition .= ($s_key != "")? " AND  P.name  like '".$s_key."%'":"";
				$search_condition .= ($s_category != "")? "AND P.category_id = ".$s_category."":"";
				

				/*
				 * Return the Product list
				 */
				
					Paging::set_max( $count );
		        	Paging::calc();// calculation for paging
					$query = "SELECT P.id 
								FROM ".PRODUCTS." as P LEFT JOIN  ".CATEGORIES." as C ON P.category_id = C.id  WHERE 1 ".$search_condition."
								ORDER BY id DESC ";
					$rows = $app->getrows($query);
				return count($rows);
	}
		

	   public static function Listview() {
				/*
				 * Get the function parameters , if exist . And make the search condition
				 */

				$args = func_get_args();

				$app = $GLOBALS['app'];

				$s_key = $args[0];
				$s_category = $args[1];
				$search_condition .= ($s_key != "")? " AND  P.name  like '".$s_key."%'":"";
				$search_condition .= ($s_category != "")? "AND P.category_id = ".$s_category."":"";
				$count = self::ListviewCount($s_key,$s_category);

				/*
				 * Return the Product list
				 */
				if ($count > 0) {
					Paging::set_max( $count );
		        	Paging::calc();// calculation for paging
					$query = "SELECT P.id, P.product_with, P.name, P.status as status, C.category as category_name,
					          IF(P.status = 0, 'disable', 'enable') AS status_text
								FROM ".PRODUCTS." as P LEFT JOIN  ".CATEGORIES." as C ON P.category_id = C.id  WHERE 1 ".$search_condition."
								ORDER BY id DESC ". Paging::get_sql_limit();
					return $app->getrows($query);
				}
	}
	public static function Detail($product_id) {
			/*
			 * Return the Product details
			 */
			$app = $GLOBALS['app'];
			$count = $app->find_count(array('conditions' => array('id = ' => $product_id)));
			if ($count > 0) {
				$query = "SELECT *
							FROM ".PRODUCTS."
							WHERE id ='".$product_id."'";
				return $app->getrows($query);
			}
  }

}
