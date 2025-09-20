<?php
class ExpensesSubCategory {
    // Required fields
    public $check_required = array('category_id','name');
    public $check_duplication = array('name','category_id');
    public $check_required_labels = array('Category','Subcategory Name');
    public static $check_required_text = 'Required Fields Missing!';
    public static $check_duplication_text = 'Subcategory already exists in this category!';

    // ---------------- READ ----------------
    public static function ListviewCount() {
        $app = $GLOBALS['app'];
        $query = "SELECT id FROM ".EXPENSES_SUB_CATEGORY." ORDER BY id DESC";
        $data = $app->getrows($query);
        return count($data);
    }

    public static function Listview() {
        $app = $GLOBALS['app'];
        $count = self::ListviewCount();
        if ($count > 0) {
            Paging::set_max($count);
            Paging::calc();
            // 🔹 FIXED: status_text இப்போது Active/Inactive return செய்யும்
            $query = "SELECT s.id, s.category_id, s.name, s.description, s.status,
                              IF(s.status=1,'enable','disable') AS status_text,
                             c.name AS category_name
                      FROM ".EXPENSES_SUB_CATEGORY." s
                      JOIN ".EXPENSES_CATEGORY." c ON s.category_id=c.id
                      ORDER BY s.id DESC ".Paging::get_sql_limit();
            return $app->getrows($query);
        }
        return array();
    }

    public static function Detail($id) {
        $app = $GLOBALS['app'];
        $query = "SELECT * FROM ".EXPENSES_SUB_CATEGORY." WHERE id = '".intval($id)."'";
        $data = $app->getrows($query);
        return isset($data[0]) ? $data[0] : null;
    }

    // ---------------- CREATE ----------------
    public static function Create($data) {
        $app = $GLOBALS['app'];

        // Check required
        foreach (array('category_id','name') as $field) {
            if (empty($data[$field])) {
                return array("success"=>false,"error"=>self::$check_required_text);
            }
        }

        // Check duplication
        $dup_query = "SELECT * FROM ".EXPENSES_SUB_CATEGORY." 
              WHERE category_id='".intval($data['category_id'])."' 
              AND name='".trim($data['name'])."'";
              echo $dup_query; exit;
              
if(count($app->getrows($dup_query)) > 0){
    return array("success"=>false,"error"=>"Subcategory already exists in this category!");
}


        // Auto fields
        $data['created_datetime'] = date('Y-m-d H:i:s');
        $data['created_by'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

        $app->access_table = EXPENSES_SUB_CATEGORY;
        $insert_id = $app->insert($data);
        return array("success"=>true,"id"=>$insert_id);
    }

    // ---------------- UPDATE ----------------
    public static function Update($id, $data) {
        $app = $GLOBALS['app'];

        // Check required
        foreach (array('category_id','name') as $field) {
            if (empty($data[$field])) {
                return array("success"=>false,"error"=>self::$check_required_text);
            }
        }

        // Check duplication excluding current id
        $dup_query = "SELECT * FROM ".EXPENSES_SUB_CATEGORY."
                      WHERE name='".trim($data['name'])."'
                      AND category_id='".intval($data['category_id'])."'
                      AND id != '".intval($id)."'";
        if (count($app->getrows($dup_query)) > 0) {
            return array("success"=>false,"error"=>self::$check_duplication_text);
        }

        // Auto fields
        $data['modified_datetime'] = date('Y-m-d H:i:s');
        $data['modified_by'] = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;

        $app->access_table = EXPENSES_SUB_CATEGORY;
        $app->update($data, array("id"=>intval($id)));
        return array("success"=>true);
    }

    // ---------------- DELETE ----------------
    public static function Delete($id) {
        $app = $GLOBALS['app'];
        $app->access_table = EXPENSES_SUB_CATEGORY;
        $app->delete(array("id"=>intval($id)));
        return array("success"=>true);
    }

    // ---------------- HELPER ----------------
    public static function getSubCategoryName($id) {
        $app = $GLOBALS['app'];
        $app->access_table = EXPENSES_SUB_CATEGORY;
        $res = $app->find(array("name"), array("conditions"=>array("id = "=>intval($id))));
        return isset($res[0][0]) ? $res[0][0] : null;
    }
}
?>
