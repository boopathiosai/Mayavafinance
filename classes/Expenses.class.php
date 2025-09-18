<?php
class Expenses {
    // Required fields
    public $check_required = array('branch_id','category_id','sub_category_id','opening_balance','amount','cash_type','expenses_date');
    public $check_required_labels = array('Branch','Category','Sub Category','Opening Balance','Amount','Cash Type','expenses_date');
    public static $check_required_text = 'Required Fields Missing!';

    // ---------------- READ ----------------
    public static function ListviewCount() {
        $app = $GLOBALS['app'];
        $query = "SELECT id FROM ".EXPENSES." ORDER BY id DESC";
        $data = $app->getrows($query);
        return count($data);
    }

    public static function Listview() {
        $app = $GLOBALS['app']; 
        $count = self::ListviewCount();
        if ($count > 0) {
            Paging::set_max($count);
            Paging::calc();
             $sql = "SELECT 
                            e.id, 
                            b.name AS branch_name, 
                            sb.name AS sub_branch_name, 
                            c.name AS category, 
                            sc.name AS sub_category, 
                            e.opening_balance, 
                            e.closing_balance, 
                            e.description, 
                            e.amount, 
                            e.cash_type, 
                            e.expenses_date,
                            e.created_at
                            FROM mf_expenses e
                            LEFT JOIN mf_branch b ON e.branch_id = b.id
                            LEFT JOIN mf_sub_branch sb ON e.sub_branch_id = sb.id
                            LEFT JOIN mf_expenses_category c ON e.category_id = c.id
                            LEFT JOIN mf_expenses_sub_category sc ON e.sub_category_id = sc.id
                            ORDER BY e.id DESC";

         return $app->getrows($sql);
        }
        return array();
    }

    public static function Detail($id) {
        $app = $GLOBALS['app'];
        $query = "SELECT * FROM ".EXPENSES." WHERE id = '".intval($id)."'";
        $data = $app->getrows($query);
        return isset($data[0]) ? $data[0] : null;
    }

    // ---------------- CREATE ----------------
    public static function Create($data) {
        $app = $GLOBALS['app'];   

        // Check required
        foreach(['branch_id','category_id','sub_category_id','opening_balance','amount','cash_type'] as $field){
            if(empty($data[$field])) return array("success"=>false,"error"=>self::$check_required_text);
        }

        $app->access_table = EXPENSES;
        $insert_id = $app->insert($data);
        return array("success"=>true,"id"=>$insert_id);
    }

    // ---------------- UPDATE ----------------
    public static function Update($id, $data) {
        $app = $GLOBALS['app'];

        // Check required
        foreach(['branch_id','category_id','sub_category_id','opening_balance','amount','cash_type'] as $field){
            if(empty($data[$field])) return array("success"=>false,"error"=>self::$check_required_text);
        }

        $app->access_table = EXPENSES;
        $app->update($data, array("id"=>intval($id)));
        return array("success"=>true);   
    }

    // ---------------- DELETE ----------------
    public static function Delete($id) {
        $app = $GLOBALS['app'];
        $app->access_table = EXPENSES;
        $app->delete(array("id"=>intval($id)));
        return array("success"=>true);
    }
}
