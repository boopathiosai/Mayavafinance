<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE
// Module           :  Brand class
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  -----------
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Branch class  for Mayavafinance.
//**************************************************************************


class Category {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('organization_name', 'name', 'address', 'location','description','address');
        public $check_duplication = array('name', 'branch_code');

        public $check_required_labels = array('Company  Name', 'Branch Name', 'Branch Location','Branch Description', 'Branch Address');
        public $check_duplication_labels = array('Company Branch Name', 'Company Branch Code');

        public static $check_required_text = 'Required Fields Missing!';
        public static $check_duplication_text = 'Details Already exists!';
    public static function getCategoryname($search_category_id)
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = CATEGORIES;
        $res = $app->find(array("category"),array("conditions"=>array("id = "=>$search_category_id)));
        return $res[0][0];
    }
}
?>