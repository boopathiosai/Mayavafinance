<?php
  //**************************************************************************
  //**************************************************************************
  // Project          :  MAYAVA FINANCE
  // Module           :  Global Functions
  // Domain           :  http://wwa.mayavafinance.com
  // Programmer       :  Vinothkuamr V, Subash.S
  // Inprogress       :  \\offlinev5\Mayavafinance
  // Backup           :  \\dataserver\EIT\Projects\In Progress\
  // Last Modified    :  06-Jul-2011
  //**************************************************************************
  // Description      :  This is the Global Functions file  for Mayavafinance and define all global functions.
  //**************************************************************************

function print_array($arr,$tab=" "){
    if(!is_array($arr)) return false;
    echo "<ul>";
    foreach($arr as $key => $val){
        if(is_array($val)) 
            print_array($val);
        else
            echo "<li>{$key} = {$val} </li>";
    }
    echo "</ul>";
    return true;
}

function p_arr($arr){

echo "<pre>"; print_r($arr); echo "</pre>";

}

function build_select_options($data, $selected = '') {
    if(count($data) > 0) {
        $options = '';
        foreach($data as $key => $val) {
            $selected_option = ($selected == $key) ? 'selected' : '';
            $val=stripslashes($val);
            $options.= '<option value="'.$key.'" '.$selected_option.'>'.$val.'</option>';
        }
        return $options;
    }
}

function build_multiple_select_options($data, $selected = '') {

    if(count($data) > 0) {
        $options = '';
		$search_cat_arr = explode(',',$selected);

		$count_multiple_data = count($search_cat_arr);
		$i=0;

        foreach($data as $key => $val) {
		 $selected_option = (in_array($key,$search_cat_arr)) ? 'selected' : '';
            $val=stripslashes($val);
            $options.= '<option value="'.$key.'" '.$selected_option.'>'.$val.'</option>';
			$i++;
        }
        return $options;
    }
}


function buildDropDownBox($query_data, $selected = '') {
    if(!is_array($query_data)){
        $data = $app->getrows($query_data);
    }else{
        $data = $query_data;
    }
    if(count($data) > 0) {
        foreach($data as $val) {
            $selectedOption = ($selected == $val['id']) ? 'selected' : '';
            echo '<option value="'.$val['id'].'" '.$selectedOption.'>'.stripslashes($val['name']).'</option>';
        }
    }
}

function convert_to_dropdown($dropdown_list_result, $field1,$field2)
{
    $app = $GLOBALS['app'];

    $dropdown_list_data = array();
    $dropdown_list_count = count($dropdown_list_result);

    for($i=0; $i<$dropdown_list_count;$i++)
    {
        $dropdown_list_data[$dropdown_list_result[$i][$field1]] = $dropdown_list_result[$i][$field2];
    }
    return $dropdown_list_data;
}

function buildOptDropDownBox($data, $selected = '') {
    if(count($data) > 0) {
        $parent = "";
        $ind = 1;
        foreach($data as $val) {
            if($parent != $val["parent"]){
                if($ind>1) echo "</optgroup>";
                echo '<optgroup label="'.$val['parent'].'">';
                $parent = $val["parent"];

            }
            $selectedOption = ($selected == $val['id']) ? 'selected' : '';
            echo '<option value="'.$val['id'].'" '.$selectedOption.'>'.stripslashes($val['name']).'</option>';
            $ind++;
        }
    }
}


function build_privillege_list($assignedprivilege, $user_type_id, $branch_id) {

    $module_categories = Privillege::getmoduleCategory();

    $response='';
    $category_count = count($module_categories);
    for($j=0;$j<$category_count;$j++)
    {
        $response.="<tr>";

        $user_module_count = $assignedprivilege[$module_categories[$j]['id']]['module_id'] !='' ? count(array_unique(explode(",",$assignedprivilege[$module_categories[$j]['id']]['module_id']))):'';

        // get modules avaialble in application
        $content = Privillege::getmodules($module_categories[$j]['id']);
        $module_count =count($content);

        // populate and set selected for select all check box
        $response.= "<td width='50%' class='title'  class='module_category'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='selall_".$module_categories[$j]['name']."' id='selall_".$module_categories[$j]['name']."' ".($user_module_count == $module_count  ? 'checked' : '' )."  onclick='module_checkall(\"".$module_categories[$j]['name']."_privilegechk\",this.checked)'/>".$module_categories[$j]['name']."</td> ";
        $response.= "<td width='50%' class='title' ><p align='left'>Actions</p></td></tr>";

        $response.= "<tr><td class='module_pages'><div class='scroll_left'>";

        // populate and show saus of the module checkboxes
        if($module_count>0)
        {
            for($k=0;$k<$module_count;$k++)
            {

                $response .= "<br><input id ='".$module_categories[$j]['name']."[]' type='checkbox' class='".$module_categories[$j]['name']."_privilegechk' name='".$module_categories[$j]['name']."[]' ".(in_array($content[$k]['id'],explode(",",$assignedprivilege[$module_categories[$j]['id']]['module_id'])) ? 'checked' : '' )." value='".$content[$k]['id']."'  onchange='module_check_reset(\"".$module_categories[$j]['name']."[]\", \"".$module_categories[$j]['name']."\")' /><span style='font-size:".$fontsize."px;'>".$content[$k]['modulename']."</span>";
            }
        }
        $response .= "</div></td><td>";

        
        
        // get actions avaialble for modules in application
        $actions = Privillege::getactions(1,$module_categories[$j]['id']);
        

        // get actions assigned for the current user for modules
        $module_actions = Privillege::getModuleAction($user_type_id, $module_categories[$j]['id'],'', $branch_id);
        $module_actions = array_unique(explode(",", $module_actions['action_id']));

        $action_count = count($actions);
        if($actions){
        for($m=0;$m<$action_count;$m++)
        {
            $response .= "<br/><input type='checkbox' class='".$module_categories[$j]['name']."_actionchk' name='".$module_categories[$j]['name']."_action[]' ".(in_array($actions[$m]['id'],$module_actions) ? 'checked' : '' )." value='".$actions[$m]['id']."'/>
                    <span style='font-size:".$fontsize."px;'>".$actions[$m]['action']."</span>";
        }
        }else{
            $response .= "<br>View"; 
        }
        $response.= "</td></tr><tr height='20'></tr>";
    }
    return $response;
}



function build_privillege_list11($assignedprivilege, $user_type_id, $branch_id) {

    $module_categories = Privillege::getmoduleCategory();

    $response='';
    $category_count = count($module_categories);
    for($j=0;$j<$category_count;$j++)
    {
        $response.="<tr>";

        $user_module_count = $assignedprivilege[$module_categories[$j]['id']]['module_id'] !='' ? count(array_unique(explode(",",$assignedprivilege[$module_categories[$j]['id']]['module_id']))):'';

        // get modules avaialble in application
        $content = Privillege::getmodules($module_categories[$j]['id']);
        $module_count =count($content);

        // populate and set selected for select all check box
        $response.= "<td width='50%' class='title'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<input type='checkbox' name='selall_".$module_categories[$j]['name']."' id='selall_".$module_categories[$j]['name']."' ".($user_module_count == $module_count  ? 'checked' : '' )."  onclick='checkAll(\"".$module_categories[$j]['name']."\")'/>".$module_categories[$j]['name']."</td> ";
        $response.= "<td width='50%' class='title' ><p align='left'>Actions</p></td></tr>";

        $response.= "<tr><td><div class='scroll_left'><form id=\"".$module_categories[$j]['name']."\"><form id=".$module_categories[$j]['name'].">";

        // populate and show saus of the module checkboxes
        if($module_count>0)
        {
            for($k=0;$k<$module_count;$k++)
            {

                $response .= "<br><input id ='".$module_categories[$j]['name']."[]' type='checkbox' class='".$module_categories[$j]['name']."_privilegechk' name='".$module_categories[$j]['name']."[]' ".(in_array($content[$k]['id'],explode(",",$assignedprivilege[$module_categories[$j]['id']]['module_id'])) ? 'checked' : '' )." value='".$content[$k]['id']."'  onclick='chk_select_count(\"".$module_categories[$j]['name']."[]\", \"".$module_categories[$j]['name']."\")' /><span style='font-size:".$fontsize."px;'>".$content[$k]['modulename']."</span>";
            }
        }
        $response .= "</div></form></td><td>";

        
        
        // get actions avaialble for modules in application
        $actions = Privillege::getactions(1,$module_categories[$j]['id']);
        

        // get actions assigned for the current user for modules
        $module_actions = Privillege::getModuleAction($user_type_id, $module_categories[$j]['id'],'', $branch_id);
        $module_actions = array_unique(explode(",", $module_actions['action_id']));

        $action_count = count($actions);
        if($actions){
        for($m=0;$m<$action_count;$m++)
        {
            $response .= "<br/><input type='checkbox' class='".$module_categories[$j]['name']."_actionchk' name='action' ".(in_array($actions[$m]['id'],$module_actions) ? 'checked' : '' )." value='".$actions[$m]['id']."'/>
                    <span style='font-size:".$fontsize."px;'>".$actions[$m]['action']."</span>";
        }
        }else{
            $response .= "<br>View"; 
        }
        $response.= "</td></tr><tr height='20'></tr>";
    }
    return $response;
}



function build_menu_list($assignedprivilege) {

    $module_categories = Privillege::getmoduleCategory();

    $response='';
    $category_count = count($module_categories);
    for($j=0;$j<$category_count;$j++)
    {

          $content = Privillege::get_mneu_modules($module_categories[$j]['id']);
          $module_count =count($content);

            if($module_count>0)
            {
                $submenu='';
                for($k=0;$k<$module_count;$k++)
                {
//                    $submenu .= in_array($content[$k]['id'],explode(",",$assignedprivilege[$module_categories[$j]['id']]['module_id'])) ? "<li><a href='".$content[$k]['modulefiles'].".php'>".$content[$k]['modulename']."</a></li>" : '';
                  // Sathish santhosh kumar - 19_MAY_2017  //
                     if($content[$k]['modulename'] == "Income Reports"){}elseif($content[$k]['modulename'] == "Daily report"){}else{
                    $submenu .= in_array($content[$k]['id'],explode(",",$assignedprivilege[$module_categories[$j]['id']]['module_id'])) ? "<li><a href='".$content[$k]['modulefiles'].".php'>".$content[$k]['modulename']."</a></li>" : '';
                     
                     }// Sathish santhosh kumar - 05_MAY_2017 //
                    
                    if($content[$k]['modulename'] == "Income Reports"){
                    $submenu .="<li><a href='income_reports_dev.php'>Income report V2</a></li>"; 
                    $submenu .="<li><a href='daily_report_dev.php'>Daily report V2</a></li>";                     
                    }
                   
                  // sathish santhosh kumar - END //  
                    }
                if($submenu != '')
                {
                    $class =  (isset($_SESSION["mcatid"]) && $module_categories[$j]['id'] == $_SESSION["mcatid"] ) ? 'class=active': '';
                    $response .= "<li ".$class."><a href='javascript:' class='trigger'>".$module_categories[$j]['name']."</a>";
                    $response .= "<ul>".$submenu."</ul>";
                    $response .="</li>";
                }
            }

    }
    return $response;
}

function is_accessible(){
    
    $assignedprivilege = $_SESSION['session_user_modules'];
    $count = count($assignedprivilege);
    $user_modules_string ='';
    if($count>0)
    {
        foreach($assignedprivilege as $key =>$value)
        {
            $user_modules_string .=",".$assignedprivilege[$key]['modulefiles'];
        }
        $user_modules = array_unique(explode(",", $user_modules_string));
        //print_r($user_modules);
        $current_page = explode(".",substr($_SERVER["SCRIPT_NAME"],strrpos($_SERVER["SCRIPT_NAME"],"/")+1));
        return in_array($current_page[0], $user_modules);
    }
    return false;
}

function can_edit($user_group_id, $module_name){
    if($user_group_id==1) return true;
    $menu = Privillege::getModuleAction($user_group_id, 0, $module_name,'');
    return ( in_array(2,array_unique(explode(",",$menu['action_id']))));
}

function can_change_status($user_group_name, $module_name=''){
    
    return ($user_group_name == 'Super Admin' || $user_group_name == 'Admin' || $user_group_name == 'Super Admin1') ? true : false;
    
}

function is_admin($user_group_name, $module_name=''){
    
    return ($user_group_name == 'Super Admin' || $user_group_name == 'Admin' || $user_group_name == 'Super Admin1') ? true : false;
    
}

function is_superadmin($user_group_name, $module_name=''){
        
    return ($user_group_name == 'Super Admin' || $user_group_name == 'Super Admin1') ? true : false;
    
}


function can_delete($user_group_id, $module_name){
    if($user_group_id==1) return true;
    $menu = Privillege::getModuleAction($user_group_id, 0, $module_name,'');
    return (in_array(3,array_unique(explode(",",$menu['action_id']))));
}

function can_add($user_group_id, $module_name){
    if($user_group_id==1) return true;
    $menu = Privillege::getModuleAction($user_group_id, 0, $module_name,'');
    return(in_array(1,array_unique(explode(",",$menu['action_id']))));
}

function can_view($user_group_id, $module_name){
    if($user_group_id==1) return true;
    $menu = Privillege::getModuleAction($user_group_id, 0, $module_name,'');
    return(in_array(4,array_unique(explode(",",$menu['action_id']))));
}

function can_act($user_group_id, $module_name){
    if($user_group_id==1) return true;
    if(can_add($user_group_id,$module_name) || can_edit($user_group_id,$module_name) || can_delete($user_group_id,$module_name)  || can_view($user_group_id,$module_name)){
        return true;
    }
    else return false;

}

function country_table_data() {
    $app = $GLOBALS['app'];
    $query = "SELECT country_id,country
                FROM ".COUNTRY;
    return $app->getrows($query);
}


function putcsv($array,$delimiter = ',')
{
    $line = "";

    while (list($key,$val)=each($array))
    {
        /*
         * Remove all Windows newline characters,
         * as they interfere with the parsing.
         */
        $val = str_replace("\r\n", "\n", $val);
        /*
         * If a delimiter char, a double quote char or a newline char
         * is present in the field, add quotes.
         */
        if(ereg("[$delimiter\"\n\r]", $val))
        {
            $val = '"'.str_replace('"', '""', $val).'"';
        }
        /*
         * If empty value add quotes
         */
        if (strlen($val) == 0)
        {
            $val = '""';
        }
        $line .= $val.$delimiter;
    }
    /*
     * Strip the last delimiter.
     */
    $line = substr($line, 0, (strlen($delimiter) * -1));

    /*
     * Add the newline.
     */
    $line .= "\n";

    /*
     * Return the line
     */
    return $line;
}


function find_root_path() {

  $path_parts = pathinfo($_SERVER['PHP_SELF']);
  $is_val = isSSL();
  $server_name =  $_SERVER['HTTP_HOST'];

  if($is_val === TRUE){
    $url_value = 'https://' . $server_name;
  } else {
    $url_value = 'http://' . $server_name;
  }
  return $url_value . $path_parts['dirname'];

}


function isSSL(){

  if($_SERVER['https'] == 1) /* Apache */ {
     return TRUE;
  } elseif ($_SERVER['https'] == 'on') /* IIS */ {
     return TRUE;
  } elseif ($_SERVER['SERVER_PORT'] == 443) /* others */ {
     return TRUE;
  } else {
  return FALSE; /* just using http */

  }

}



/**
 * This function is used to display the notifications after every processings.
 **/
function display_top_notify($data,$type=0)
{
    if($type == '0')
    {
        $tablecont.="<table width=\"775\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#FFFFFF\" class=\"alert_border\">
        <tr class=\"table_cont black\">
            <td class=\"green_alert\">$data</td>
        </tr>
        </table>";
    }
    else if($type == '1')
    {
        $tablecont.="<table width=\"775\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#FFFFFF\" class=\"alert_border\">
        <tr class=\"table_cont black\">
        <td class=\"red_alert\">$data</td>
        </tr>
        </table>";
    }

    return $tablecont;
}

/**
 * This function is used to display the notifications after add and remove filter.
 **/
function display_filter_notify($data,$type=0)
{
    if($type == '0')
    {
        $tablecont.="<table width=\"775\" align=\"center\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#FFFFFF\" class=\"alert_border\">
        <tr class=\"table_cont black\">
            <td class=\"green_alert\">$data</td>
        </tr>
        </table>";
    }
    elseif($type == '1')
    {
        $tablecont.="<table width=\"775\" align=\"center\" border=\"0\" cellpadding=\"0\" cellspacing=\"0\" bgcolor=\"#FFFFFF\" class=\"alert_border\">
        <tr class=\"table_cont black\">
        <td class=\"red_alert\">$data</td>
        </tr>
        </table>";
    }
    return $tablecont;
}


function admin_error_notify($data, $type=0)
{
    if($type == 0)
    {
        $tablecont.="<table width='341' cellspacing='0' border='0' cellpadding='0'>
        <tr>
        <td vlaign='top'width='15' class='green_txt'><img src='images/tick.gif'   /></td>
        <td height='33' class='green_txt'><strong>$data</strong>
        </td>
        </tr>
        </table>";
    }
    else
    {
        $tablecont.="<table width='341' cellspacing='0' border='0' cellpadding='0'>
        <tr>
        <td vlaign='top'width='15' class='red_txt'><img src='images/wrong.gif'   /></td>
        <td height='33' class='red_txt'><strong>$data</strong>
        </td>
        </tr>
        </table>";
    }
    return $tablecont;
}



function get_country_list()
{
    $app = $GLOBALS['app'];

    $smarty_vars['path'] = $GLOBALS['root_path'];

    $app->access_table = COUNTRY;
    $country_list_result = $app->find(array('*'));

    $country_list_data = array();
    $country_list_count = count($country_list_result);

    for($i=0; $i<$country_list_count;$i++)
    {
        $country_list_data[$country_list_result[$i][0]] = $country_list_result[$i]['country'];
    }
    return $country_list_data;
}


function status_dropdown($sel_stat)
{
    global $status_array;

    $status_count = count($status_array)-1;
    for($i=$status_count; $i>=0;$i--)
    {
        $selected = ($sel_stat == $i) ? 'selected' : '';
        $status_list_data.= "<option value='".$i."' $selected>$status_array[$i]</option>";
    }
    return $status_list_data;

}


//Paging Function:
function paging_disp($rg, $page_no, $prevpage, $lastpage, $nextpage, $search_args) {

            if($rg <= 0) {
                $rg = 1;
            }
            $range = RANGE;
            $last = ($rg+$range);

            if($page_no == ($rg) && ($page_no > 1)) {
            $rgs = ($rg-$range);
            $next_page = ($page_no-1);
            echo "<a href='{$_SERVER['PHP_SELF']}?".$search_args."pageno=$next_page&rg=$rgs'><img src='images/prev-arrow.png' /></a> ";
            } elseif($page_no <= ($last-1) && ($page_no > 1)){
            $rgs = ($last-$range);
            $next_page = ($page_no-1);
            echo "<a href='{$_SERVER['PHP_SELF']}?".$search_args."pageno=$next_page&rg=$rgs'><img src='images/prev-arrow.png' /></a> ";
            }

        if($rg != 1) {
            $nextpage = ($rg-$range);
            echo "<p><a href='{$_SERVER['PHP_SELF']}?".$search_args."rg=$nextpage&pageno=$nextpage'>....</a></p>";
        }

        for ($x = ($rg); $x < ($rg + $range); $x++) {
            if (($x > 0) && ($x <= $lastpage)) {
                if ($x == $page_no) {
                    echo "<b class='numbercurrent' >$x</b> ";
                } else {

                echo "<a class='number' href='{$_SERVER['PHP_SELF']}?".$search_args."pageno=$x&rg=$rg'>$x</a> ";
                }
            }
        }

          $nextpage = ($rg+$range);

        if ($nextpage > $lastpage) {
        echo "";
        } else {
        echo "<p><a href='{$_SERVER['PHP_SELF']}?".$search_args."rg=$nextpage&pageno=$last'>....</a></p>";
        }
        $next_page = ($page_no+1);
        if($page_no == ($last-1) && ($page_no < $lastpage)) {
        $rg = ($rg+$range);
        echo "<a class='ext' href='{$_SERVER['PHP_SELF']}?pageno=$next_page&rg=$rg'></a> ";

        } elseif($page_no <= ($last-1) && ($page_no < $lastpage)){
        echo " <a href='{$_SERVER['PHP_SELF']}?".$search_args."pageno=$next_page&rg=$rg'><img src='images/next-arrow.png'/></a> ";
        }

}



function mainrootpath(){

    $hostURL = "http://".$_SERVER['HTTP_HOST'].rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
    $dir = explode("/", $hostURL);
    array_pop($dir);
    $rootpath = implode( "/", $dir );
    return $rootpath;
}

/*
 * Build dropdown for perticular table with ACTIVE status
 */
function get_dropdown_list($tblname)
{
    $app = $GLOBALS['app'];
    $smarty_vars['path'] = $GLOBALS['root_path'];
    $app->access_table = $tblname;
    $dropdown_list_result = $app->find(array('id', 'name'));

    $dropdown_list_data = array();
    $dropdown_list_count = count($dropdown_list_result);

    for($i=0; $i<$dropdown_list_count;$i++)
    {
        $dropdown_list_data[$dropdown_list_result[$i][0]] = $dropdown_list_result[$i]['name'];
    }
    return $dropdown_list_data;
}


function get_dropdown_list_with_field($field1,$field2,$tblname)
{
    $app = $GLOBALS['app'];
    $smarty_vars['path'] = $GLOBALS['root_path'];
    $app->access_table = $tblname;

    $dropdown_list_result = $app->find(array($field1, $field2),array('conditions' => array('status = ' => 1),'sort' => ''.$field2.' ASC'));

    $dropdown_list_data = array();
    $dropdown_list_count = count($dropdown_list_result);

    for($i=0; $i<$dropdown_list_count;$i++)
    {
        if($dropdown_list_result[$i][$field2] != 'Super Admin')
            $dropdown_list_data[$dropdown_list_result[$i][$field1]] = $dropdown_list_result[$i][$field2];
    }
    return $dropdown_list_data;
}

function get_dropdown_list_with_field_filter($field1,$field2,$branch_id,$tblname)
{
    $app = $GLOBALS['app'];
    $smarty_vars['path'] = $GLOBALS['root_path'];
    $app->access_table = $tblname;

    $dropdown_list_result = $app->find(array($field1, $field2),array('conditions' => array('status = ' => 1,'id != ' => $branch_id),'sort' => ''.$field2.' ASC'));

    $dropdown_list_data = array();
    $dropdown_list_count = count($dropdown_list_result);

    for($i=0; $i<$dropdown_list_count;$i++)
    {
        $dropdown_list_data[$dropdown_list_result[$i][0]] = $dropdown_list_result[$i][1];
    }
    return $dropdown_list_data;
}



function get_dropdown_list_with_field_user_group($field1,$field2,$tblname)
{
    $app = $GLOBALS['app'];
    $smarty_vars['path'] = $GLOBALS['root_path'];
    $app->access_table = MODULE_PRIVILLEGE;
    $dropdown_list_result = $app->find(array('DISTINCT branch_id'),array('conditions' => array('user_group_id = ' => 1, 'status = ' => 1)));

    $dropdown_list_data = array();
    $dropdown_list_count = count($dropdown_list_result);
    if($dropdown_list_count == '0'){
        $condition = array('status = ' => 1);    
    }else{
        $condition = array('status = ' => 1,'id != ' => 1);    
    }
    $app->access_table = $tblname;
    
    $dropdown_list_result = $app->find(array($field1, $field2),array('conditions' => $condition,'sort' => ''.$field2.' ASC'));

    $dropdown_list_data = array();
    $dropdown_list_count = count($dropdown_list_result);

    for($i=0; $i<$dropdown_list_count;$i++)
    {
        $dropdown_list_data[$dropdown_list_result[$i][0]] = $dropdown_list_result[$i][1];
    }
    return $dropdown_list_data;
}

function get_dropdown_list_with_field_optiongroup($field1,$field2,$opt,$tblname)
{
    $app = $GLOBALS['app'];
    $smarty_vars['path'] = $GLOBALS['root_path'];
    $app->access_table = $tblname;

    $dropdown_list_result = $app->find(array($field1, $field2),array('conditions' => array('status = ' => 1,'product_with =' => $opt),'sort' => ''.$field2.' ASC'));

    $dropdown_list_data = array();
    $dropdown_list_count = count($dropdown_list_result);

    for($i=0; $i<$dropdown_list_count;$i++)
    {
        $dropdown_list_data[$dropdown_list_result[$i][0]] = $dropdown_list_result[$i][1];
    }

    return $dropdown_list_data;

}


function get_dropdown_with_optgroup_for_pledge($field1,$field2,$opt,$tblname)
{
   $app = $GLOBALS['app'];
    $smarty_vars['path'] = $GLOBALS['root_path'];
    $app->access_table = $tblname;

    //$query = "SELECT DISTINCT  *  FROM ".CATEGORIES." as CAT INNER JOIN ".PRODUCTS." as PR ON PR.category_id = CAT.id where 1 and CAT.product_with =".$opt." and CAT.status = 1";
    $query = "SELECT DISTINCT  *  FROM ".PRODUCTS." as PR where 1 and PR.product_with =".$opt." and PR.status = 1";


    $dropdown_list_result  = $app->getrows($query);

    $dropdown_list_data = array();

    $dropdown_list_count = count($dropdown_list_result);

    for($i=0; $i<$dropdown_list_count;$i++)
    {
        $dropdown_list_data[$dropdown_list_result[$i][0]] = $dropdown_list_result[$i][1];
    }

    return $dropdown_list_data;

}

function date_format_fun($str,$format=""){

        $str = strtotime($str);
        if($format != ""){
            $result = date($format,$str);
        }else{
            $result = date("j-F-Y  g:i a",$str);
            $result."dfgdfgfdg";
        }

        return $result;

}

//Created by Moses C Rathina Kumar for the purpose of display the Amount

function convertcash($num, $currency=""){ 
    if(strlen($num)>3){ 
            $lastthree = substr($num, strlen($num)-3, strlen($num)); 
            $restunits = substr($num, 0, strlen($num)-3); // extracts the last three digits 
            $restunits = (strlen($restunits)%2 == 1)?"0".$restunits:$restunits; // explodes the remaining digits in 2's formats, adds a zero in the beginning to maintain the 2's grouping. 

            $expunit = str_split($restunits, 2); 
            
            for($i=0; $i<sizeof($expunit); $i++){ 
                $explrestunits .= ( ($i==0) ? (int)$expunit[$i] : $expunit[$i]) .","; // creates each of the 2's group and adds a comma to the end 
            }    

            $thecash = $explrestunits.$lastthree; 
    } else { 
           $thecash = $num; 
    } 
    
    return ($thecash=="") ?  '0' : $thecash; // writes the final format where $currency is the currency symbol. 
} 

function convertToCurrency1($str, $sym_disp=true, $cur_sym = '', $pos='', $dec=2, $dec_chr='', $thou_chr='' ){
    if($cur_sym=='') $cur_sym = CURRENCY_SYMBOL;
    if($dec_chr=='') $dec_chr = NUMBER_DECIMAL_CHAR;
    if($thou_chr=='') $thou_chr = NUMBER_THOUSANDS_CHAR;
    if($pos=='') $pos = CURRENCY_SYMBOL_POS;
    if(!$sym_disp) $cur_sym = "";
    
    $converted_currency = number_format(floatval($str),$dec,$dec_chr,"");
    
    $arr_cur = explode($dec_chr,$converted_currency);
    //p_arr($arr_cur);
    
    $dec = $arr_cur[1];
    //$converted_currency = convertcash($arr_cur[0] ,$cur_sym).$dec_chr.$dec;
   
 
   $converted_currency = (($pos=="after") ? 
               $converted_currency." ".$cur_sym :
               $cur_sym." ".$converted_currency
           );
           
    return $converted_currency;       
}


function convertToCurrency($num){
 $pos = strpos((string)$num, ".");
 if ($pos === false) {
 $decimalpart="00";
 }
 if (!($pos === false)) {
 $decimalpart= substr($num, $pos+1, 2); $num = substr($num,0,$pos);
 }

 if(strlen($num)>3 & strlen($num) <= 12){
 $last3digits = substr($num, -3 );
 $numexceptlastdigits = substr($num, 0, -3 );
 $formatted = makeComma($numexceptlastdigits);
 $stringtoreturn = $formatted.",".$last3digits.".".$decimalpart ;
 }elseif(strlen($num)<=3){
 $stringtoreturn = ($num == 0) ? '0.'.$decimalpart : $num.".".$decimalpart ;
 }elseif(strlen($num)>12){
 $stringtoreturn = number_format($num, 2);
 }

 if(substr($stringtoreturn,0,2)=="-,"){
 $stringtoreturn = "-".substr($stringtoreturn,2 );
 }

 return $stringtoreturn;
 }

 function makeComma($input){
 // This function is written by some anonymous person - I got it from Google
 if(strlen($input)<=2)
 { return $input; }
 $length=substr($input,0,strlen($input)-2);
 $formatted_input = makeComma($length).",".substr($input,-2);
 return $formatted_input;
 }

function unitDisplay($str, $isUnit=false , $unit = '', $pos='', $dec=2, $dec_chr='', $thou_chr=''){
    if($unit=='') $unit = "Gm";
    if($dec_chr=='') $dec_chr = NUMBER_DECIMAL_CHAR;
    //if($thou_chr=='') $thou_chr = NUMBER_THOUSANDS_CHAR;
    if($pos=='') $pos = CURRENCY_SYMBOL_POS;
    if($isUnit){
        return (($pos=="before") ? 
               $unit." ".number_format(floatval($str),$dec,$dec_chr,$thou_chr) :
               number_format(floatval($str),$dec,$dec_chr,$thou_chr)." ".$unit
               );
    }else{
        return number_format(floatval($str),$dec,$dec_chr,$thou_chr);
    }

}


function ListDateRange($strDateFrom,$strDateTo) {
  // takes two dates formatted as YYYY-MM-DD and creates an
  // inclusive array of the dates between the from and to dates.

  // could test validity of dates here but I'm already doing
  // that in the main script

  $aryRange=array();

  $iDateFrom=mktime(1,0,0,substr($strDateFrom,5,2),     substr($strDateFrom,8,2),substr($strDateFrom,0,4));
  $iDateTo=mktime(1,0,0,substr($strDateTo,5,2),     substr($strDateTo,8,2),substr($strDateTo,0,4));

  if ($iDateTo>=$iDateFrom) {
    //array_push($aryRange,date('Y-m-d',$iDateFrom)); // first entry
    $aryRange[date('Y-m-d',$iDateFrom)] = '';    
    
    while ($iDateFrom<$iDateTo) {
      $iDateFrom+=86400; // add 24 hours
      //array_push($aryRange,date('Y-m-d',$iDateFrom));
      $aryRange[date('Y-m-d',$iDateFrom)] = '';    
    }
  }
  return $aryRange;
}


function fetchFields($arr_records , $fld_name){
    

    for($i=0;$i<count($arr_records); $i++){
    
        $ret_arr[] = $arr_records[$i][$fld_name]; 
    
    }

    return $ret_arr;


}

function array_merge_sum_by_date()
{

}

function translateToWords($number) 
{
/*****
     * A recursive function to turn digits into words
     * Numbers must be integers from -999,999,999,999 to 999,999,999,999 inclussive.    
     *
     *  (C) 2010 Peter Ajtai
     *    This program is free software: you can redistribute it and/or modify
     *    it under the terms of the GNU General Public License as published by
     *    the Free Software Foundation, either version 3 of the License, or
     *    (at your option) any later version.
     *
     *    This program is distributed in the hope that it will be useful,
     *    but WITHOUT ANY WARRANTY; without even the implied warranty of
     *    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
     *    GNU General Public License for more details.
     *
     *    See the GNU General Public License: <http://www.gnu.org/licenses/>.
     *
     */
    // zero is a special case, it cause problems even with typecasting if we don't deal with it here
    $max_size = pow(10,18);
    if (!$number) return "zero";
    if (is_int($number) && $number < abs($max_size)) 
    {            
        switch ($number) 
        {
            // set up some rules for converting digits to words
            case $number < 0:
                $prefix = "negative";
                $suffix = translateToWords(-1*$number);
                $string = $prefix . " " . $suffix;
                break;
            case 1:
                $string = "One";
                break;
            case 2:
                $string = "Two";
                break;
            case 3:
                $string = "Three";
                break;
            case 4: 
                $string = "Four";
                break;
            case 5:
                $string = "Five";
                break;
            case 6:
                $string = "Six";
                break;
            case 7:
                $string = "Seven";
                break;
            case 8:
                $string = "Eight";
                break;
            case 9:
                $string = "Nine";
                break;                
            case 10:
                $string = "Ten";
                break;            
            case 11:
                $string = "Eleven";
                break;            
            case 12:
                $string = "Twelve";
                break;            
            case 13:
                $string = "Thirteen";
                break;            
            // fourteen handled later
            case 15:
                $string = "Fifteen";
                break;            
            case $number < 20:
                $string = translateToWords($number%10);
                // eighteen only has one "t"
                if ($number == 18)
                {
                $suffix = "een";
                } else 
                {
                $suffix = "teen";
                }
                $string .= $suffix;
                break;            
            case 20:
                $string = "Twenty";
                break;            
            case 30:
                $string = "Thirty";
                break;            
            case 40:
                $string = "Forty";
                break;            
            case 50:
                $string = "Fifty";
                break;            
            case 60:
                $string = "Sixty";
                break;            
            case 70:
                $string = "Seventy";
                break;            
            case 80:
                $string = "Eighty";
                break;            
            case 90:
                $string = "Ninety";
                break;                
            case $number < 100:
                $prefix = translateToWords($number-$number%10);
                $suffix = translateToWords($number%10);
                $string = $prefix . "-" . $suffix;
                break;
            // handles all number 100 to 999
            case $number < pow(10,3):                    
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,2)))) . " Hundred";
                if ($number%pow(10,2)) $suffix = " and " . translateToWords($number%pow(10,2));
                $string = $prefix . $suffix;
                break;
			 case $number < pow(10,5):

                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,3)))) . " Thousand";
                if ($number%pow(10,3)) $suffix = translateToWords($number%pow(10,3));
                $string = $prefix . " " . $suffix;
                break;
            case $number < pow(10,7):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,5)))) . " Lakh";
                if ($number%pow(10,5)) $suffix = translateToWords($number%pow(10,5));
                $string = $prefix . " " . $suffix;
                break;
            case $number < pow(10,9):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,6)))) . " Million";
                if ($number%pow(10,6)) $suffix = translateToWords($number%pow(10,6));
                $string = $prefix . " " . $suffix;
                break;                    
            case $number < pow(10,12):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,9)))) . " Billion";
                if ($number%pow(10,9)) $suffix = translateToWords($number%pow(10,9));
                $string = $prefix . " " . $suffix;    
                break;
            case $number < pow(10,15):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,12)))) . " Trillion";
                if ($number%pow(10,12)) $suffix = translateToWords($number%pow(10,12));
                $string = $prefix . " " . $suffix;    
                break;        
            // Be careful not to pass default formatted numbers in the quadrillions+ into this function
            // Default formatting is float and causes errors
            case $number < pow(10,18):
                // floor return a float not an integer
                $prefix = translateToWords(intval(floor($number/pow(10,15)))) . " Quadrillion";
                if ($number%pow(10,15)) $suffix = translateToWords($number%pow(10,15));
                $string = $prefix . " " . $suffix;    
                break;                    
        }
    } else
    {
        echo "ERROR with - $number<br/> Number must be an integer between -" . number_format($max_size, 0, ".", ",") . " and " . number_format($max_size, 0, ".", ",") . " exclussive.";
    }
    return $string;    
}