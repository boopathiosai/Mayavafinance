<?php
session_start();
/*
 * Project : MAYAVA FINANCE
 * Module  : Expenses
 */

define('ROOT_PATH', '');
$page_title  = 'Ledger & Expenses';
$module_name = 'expenses';

include ROOT_PATH . 'includes/config.php';

$_SESSION["mcatid"] = TRANSACTIONS_CAT_ID;

/* ------------------ LOGIN CHECK ------------------ */
$mod = isset($_REQUEST['mod']) ? $_REQUEST['mod'] : '';
if ($app->is_logged_in() !== true) {
    if ($mod === 'Ajax') { echo "session_timed_out"; exit; }
    header('Location: index.php'); exit;
}

/* ------------------ COMMON VARIABLES ------------------ */
$pval = isset($_POST['pval']) ? $_POST['pval'] : [];

$uval = [];
$type = isset($_REQUEST['type']) ? $_REQUEST['type'] : '';

/* accept id from id OR edit_id for compatibility with frontend */
$req_id = 0;
if (isset($_REQUEST['id'])) {
    $req_id = (int)$_REQUEST['id'];
} elseif (isset($_REQUEST['edit_id'])) {
    $req_id = (int)$_REQUEST['edit_id'];
}
$uval['id'] = $req_id;

$disp = isset($_REQUEST['disp']) ? $_REQUEST['disp'] : 'content_div';
if ($disp === '') $disp = 'content_div';

$page_no = max(isset($_REQUEST['pageno']) ? (int)$_REQUEST['pageno'] : 1, 1);
$rg      = max(isset($_REQUEST['rg']) ? (int)$_REQUEST['rg'] : 1, 1);

$change_status_array = [];
$change_status_array['status'] = isset($_REQUEST['status']) ? $_REQUEST['status'] : '';

$search_key      = isset($_REQUEST['search_key']) ? $_REQUEST['search_key'] : '';
$search_category = isset($_REQUEST['search_category']) ? $_REQUEST['search_category'] : '';
$search_args = '';

if ($search_key !== '')      $search_args .= 'search_key=' . urlencode($search_key) . '&';
if ($search_category !== '') $search_args .= 'search_category=' . urlencode($search_category) . '&';

/* ------------------ CLASS INSTANCE ------------------ */
$app->access_table = EXPENSES;
Paging::set_per_page(ROWS_PER_PAGE);
Paging::set_current_page($page_no);

/* ------------------ HELPERS ------------------ */
function json_out($arr){
    header('Content-Type: application/json; charset=utf-8');
    echo json_encode($arr, JSON_UNESCAPED_UNICODE);
    exit;
}

$page = $module_name;

/* ------------------ SWITCH ------------------ */
switch ($type) {

    /* ---------------- FORM VALIDATION ---------------- */
    case 'formvalidation':
        $missing_labels = Validator::validate_json('Expenses');
        if (!empty($missing_labels)) {
            $fields  = implode(",#",array_keys($missing_labels));
            $labels  = implode(",",$missing_labels);
            $success = false;
        } else {
            $fields  = '';
            $labels  = '';
            $success = true;
        }
        json_out([
            "page"    => $page,
            "success" => $success,
            "status"  => $success, // compatibility
            "disp"    => $disp,
            "content" => EXPENSES_VALIDATE_ERROR,
            "isAlert" => true,
            "args"    => ["fields"=>"#".$fields,"labels"=>$labels,"module"=>$module_name,"search"=>$search_args]
        ]);

    /* ---------------- GET SUB CATEGORIES ---------------- */
    case 'getSubCategories': 
        $category_id = isset($_POST['category_id']) ? intval($_POST['category_id']) : 0;
        $options = "<option value=''>-- Select Sub Category --</option>";
        if ($category_id > 0) {
            $rows = $app->getrows("SELECT id, name FROM ".EXPENSES_SUB_CATEGORY." WHERE category_id=".$category_id." ORDER BY name ASC");
            foreach($rows as $row){
                $options .= "<option value='".$row['id']."'>".htmlspecialchars($row['name'])."</option>";
            }
        }
        echo $options;
        exit;

    /* ---------------- GET SUB BRANCHES ---------------- */
    case 'getSubBranches': 
        $branch_id = isset($_POST['branch_id']) ? (int)$_POST['branch_id'] : 0;
        $options = "<option value=''>-- Select Sub Branch --</option>";
        if ($branch_id > 0) {
            $rows = $app->getrows("SELECT id, name FROM ".SUB_BRANCH." WHERE branch_id=".$branch_id." ORDER BY name ASC");
            foreach($rows as $r){
                $options .= "<option value='{$r['id']}'>".htmlspecialchars($r['name'])."</option>";
            }
        }
        echo $options;
        exit;

    case "getLastOpeningBalance":
        $branch_id    = $_POST['branch_id'];
        $sub_branch_id = !empty($_POST['sub_branch_id']) ? $_POST['sub_branch_id'] : 0;
        $cond = " WHERE branch_id=" . intval($branch_id);
        if ($sub_branch_id > 0) {
            $cond .= " AND sub_branch_id=" . intval($sub_branch_id);
        }
        $last = $app->getrow("SELECT opening_balance FROM " . EXPENSES . " $cond ORDER BY id DESC LIMIT 1");
        echo isset($last['opening_balance']) ? $last['opening_balance'] : 0;
        exit;

    /* ---------------- ✅ NEW: GET LICENSE NO ---------------- */
    case 'getLicenseNo':
        $sbid = isset($_POST['sub_branch_id']) ? (int)$_POST['sub_branch_id'] : 0;
        $row = $app->getrow("SELECT license_no FROM ".SUB_BRANCH." WHERE id=".$sbid);
        echo !empty($row['license_no']) ? $row['license_no'] : '';
        exit;

    /* ---------------- ADD / UPDATE ---------------- */
    case 'add':
    case 'update':

        // ✅ auto fetch license_no if sub_branch_id given
        if (!empty($pval['sub_branch_id'])) {
            $row = $app->getrow("SELECT license_no FROM ".SUB_BRANCH." WHERE id=".(int)$pval['sub_branch_id']);
            if (!empty($row['license_no'])) {
                $pval['license_no'] = $row['license_no'];
            }
        }

        if ($type == 'add') {
            $pval['created_at'] = date('Y-m-d H:i:s');
            $pval['created_by'] = $_SESSION['session_userid'];
            $ok = (bool)$app->add($pval);
            $uval['id'] = $app->last_inserted_id();
            $success_msg = EXPENSES_SAVED;
        } else {
            $pval['modified_at'] = date("Y-m-d H:i:s");
            $pval['modified_by'] = $_SESSION['session_userid'];
            $ok = (bool)$app->save($pval, ["id = "=>$uval['id']]);
            $success_msg = EXPENSES_UPDATED;
        }
        $_SESSION['success_msg'] = $success_msg;
        json_out([
            "page"    => $page,
            "success" => $ok,
            "status"  => $ok, // compatibility
            "disp"    => $disp,
            "content" => $success_msg,
            "isAlert" => false,
            "args"    => ["module"=>$module_name,"search"=>$search_args]
        ]);

    /* ---------------- CREATE / EDIT ---------------- */
    case 'create':
    case 'edit':
        $id = $uval['id'];
        $expenses_data = $id > 0 ? Expenses::Detail($id) : [];
        include ADMIN_LAYOUT_PATH . 'forms/expenses.html';
        break;

    /* ---------------- LIST / REFRESH ---------------- */
    case 'refresh':
    case 'search':
    case 'paging':
    case 'back':
        $data  = Expenses::Listview();
        $count = Expenses::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'expenses.html';
        exit;

    /* ---------------- DELETE ---------------- */
    case 'delete':
        $message = []; 
        $overall_status = true;  

        if (!empty($uval['id'])) {
            $status = Expenses::Delete($uval['id']);
            if (empty($status['success'])) $overall_status = false;
            $message[] = !empty($status['success'])
                ? "Expense (".$uval['id'].") - ".EXPENSES_DELETED
                : "Expense (".$uval['id'].") - ".EXPENSES_NOT_DELETED." - ".(isset($status['error'])?$status['error']:'');
        }

        if (!empty($_POST['list']) && is_array($_POST['list'])) {
            foreach($_POST['list'] as $val) {
                $status = Expenses::Delete((int)$val);
                if (empty($status['success'])) $overall_status = false;
                $message[] = !empty($status['success'])
                    ? "Expense ($val) - ".EXPENSES_DELETED
                    : "Expense ($val) - ".EXPENSES_NOT_DELETED." - ".(isset($status['error'])?$status['error']:'');
            }
        }

        $alert_message = implode("<br> * ", $message);
        json_out([
            "page"    => $page,
            "success" => $overall_status,
            "status"  => $overall_status,
            "disp"    => $disp,
            "content" => (count($message) ? " * ".$alert_message : "No items selected."),
            "isAlert" => true,
            "args"    => ["module"=>$module_name,"search"=>$search_args,"isAlert"=>true]
        ]);
        exit;

    /* ---------------- CHANGE STATUS ---------------- */
    case 'change_status':
        $app->save($change_status_array, ["id = "=>$uval['id']]);
        echo $change_status_array['status'].'~~'.STATUS_SUCCESS;
        exit;

    /* ---------------- DEFAULT LIST ---------------- */
    default:
        $data  = Expenses::Listview();
        $count = Expenses::ListviewCount();
        include ADMIN_LAYOUT_PATH . 'common/header.html';
        include ADMIN_LAYOUT_PATH . 'expenses.html';
        include ADMIN_LAYOUT_PATH . 'common/footer.html';
        break;
}
