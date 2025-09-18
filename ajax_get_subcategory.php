<?php
include "config.php";
$cat_id = $_POST['cat_id'];
$q = mysqli_query($conn, "SELECT id,name FROM ".SUB_CATEGORY." WHERE category_id='$cat_id' AND status=1 ORDER BY name ASC");
echo '<option value="">-- Select Sub Category --</option>';
while($r = mysqli_fetch_assoc($q)){
    echo "<option value='{$r['id']}'>{$r['name']}</option>";
}
?>
