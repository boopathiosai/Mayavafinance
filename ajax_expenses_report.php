<?php
include("config.php");

if(isset($_POST['action'])){
    if($_POST['action'] == "get_sub_branches"){
        $branch_id = intval($_POST['branch_id']);
        $res = mysqli_query($conn, "SELECT id,name FROM ".SUB_BRANCH." WHERE branch_id='$branch_id' AND status='Active' ORDER BY name ASC");
        echo "<option value=''>-- Select Sub Branch --</option>";
        while($row=mysqli_fetch_assoc($res)){
            echo "<option value='".$row['id']."'>".$row['name']."</option>";
        }
        exit;
    }

    if($_POST['action'] == "get_sub_categories"){
        $cat_id = intval($_POST['category_id']);
        $res = mysqli_query($conn, "SELECT id,name FROM ".EXPENSES_SUB_CATEGORY." WHERE category_id='$cat_id' AND status='Active' ORDER BY name ASC");
        echo "<option value=''>-- Select Sub Category --</option>";
        while($row=mysqli_fetch_assoc($res)){
            echo "<option value='".$row['id']."'>".$row['name']."</option>";
        }
        exit;
    }
}
?>
