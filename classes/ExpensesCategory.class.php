<?php
class ExpensesCategory {
    // Required fields
    public $check_required = array('name');
    public $check_duplication = array('name');
    public $check_required_labels = array('Category Name');
    public static $check_required_text = 'Required Fields Missing!';
    public static $check_duplication_text = 'Category Name Already exists!';

    // ---------------- READ ----------------
    public static function ListviewCount() {
        $app = $GLOBALS['app'];
        $query = "SELECT id FROM ".EXPENSES_CATEGORY." ORDER BY id DESC";
        $data = $app->getrows($query);
        return count($data);
    }

    public static function Listview() {
        $app = $GLOBALS['app']; 
        $count = self::ListviewCount();
        if ($count > 0) {
            Paging::set_max($count);
            Paging::calc();
            $query = "SELECT id, name, description, status, IF(status = 0,'disable','enable') AS status_text 
                      FROM ".EXPENSES_CATEGORY." 
                      ORDER BY id DESC ".Paging::get_sql_limit();
            return $app->getrows($query);
        }
        return array();
    }

    public static function Detail($id) {
        $app = $GLOBALS['app'];
        $query = "SELECT * FROM ".EXPENSES_CATEGORY." WHERE id = '".intval($id)."'";
        $data = $app->getrows($query);
        return isset($data[0]) ? $data[0] : null;
    }

    // ---------------- CREATE ----------------
    public static function Create($data) {
        $app = $GLOBALS['app'];

        // Check required
        foreach(['name'] as $field){
            if(empty($data[$field])) return array("success"=>false,"error"=>"".self::$check_required_text);
        }

        // Check duplication
        $dup_query = "SELECT * FROM ".EXPENSES_CATEGORY." WHERE name='".trim($data['name'])."'";
        if(count($app->getrows($dup_query)) > 0){
            return array("success"=>false,"error"=>self::$check_duplication_text);
        }
        
        $app->access_table = EXPENSES_CATEGORY;
        $insert_id = $app->insert($data); // assuming $app->insert handles DB insertion
        return array("success"=>true,"id"=>$insert_id);
    }

    // ---------------- UPDATE ----------------
    public static function Update($id, $data) {
        $app = $GLOBALS['app'];

        // Check required
        foreach(['name'] as $field){
            if(empty($data[$field])) return array("success"=>false,"error"=>"".self::$check_required_text);
        }

        // Check duplication excluding current id
        $dup_query = "SELECT * FROM ".EXPENSES_CATEGORY." WHERE name='".trim($data['name'])."' AND id != '".intval($id)."'";
        if(count($app->getrows($dup_query)) > 0){
            return array("success"=>false,"error"=>self::$check_duplication_text);
        }

        $app->access_table = EXPENSES_CATEGORY;
        $app->update($data, array("id"=>intval($id))); // assuming $app->update handles DB update
        return array("success"=>true);
    }

    // ---------------- DELETE ----------------
    public static function Delete($id) {
        $app = $GLOBALS['app'];

        // Optional: Add validation if category is used elsewhere
        $app->access_table = EXPENSES_CATEGORY;
        $app->delete(array("id"=>intval($id))); // assuming $app->delete handles DB deletion
        return array("success"=>true);
    }

    // ---------------- HELPER ----------------
    public static function getCategoryName($id) {
        $app = $GLOBALS['app'];
        $app->access_table = EXPENSES_CATEGORY;
        $res = $app->find(array("name"), array("conditions"=>array("id = "=>intval($id))));
        return isset($res[0][0]) ? $res[0][0] : null;
    }
}
