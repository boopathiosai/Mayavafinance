<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Validator class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  SrinivasanM
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  02-Feb-2010
 //**************************************************************************
 // Description      :  This is the Validator class  for Mayavafinance.
// **************************************************************************

class Validator {

    public static function validate_data ($class) {
        //echo $class;

        $data = ( isset( $_POST['pval'] ) ) ? $_POST['pval'] : $_GET['pval'];

        $pdata = ( isset( $_POST['pp_pval'] ) ) ? $_POST['pp_pval'] : $_GET['pp_pval'];

        if(count($pdata) > 0){
            foreach($pdata as $key=>$val)
                $data[$key] = $val;
        }

        $vars  = get_class_vars($class);

        /*if(count($vars) > 0){
            foreach($vars as $key=>$val){
                if($val){
                    $tdata = ( isset( $_POST[$val] ) ) ? $_POST[$val] : $_GET[$val];
                    $data[$val] = $tdata;
                }
            }
        }

        print_r($data);*/

        if (count( $vars) > 0 ) {
            foreach ($vars as $key => $value) {

                if( strpos( $key, '_text' ) === false ) {
                 //print_r($data);print_r($value);exit;
                $missing_vals = self::$key($data, $value);
                    if (count( $missing_vals ) > 0) {
                        return '#'.implode(', #', $missing_vals) . '@@@' . $key . '_text';
                    }
                }
            }
        }
    }


    public static function validate_json ($class) {
        //echo $class;

        $data = ( isset( $_POST['pval'] ) ) ? $_POST['pval'] : $_GET['pval'];

        $pdata = ( isset( $_POST['pp_pval'] ) ) ? $_POST['pp_pval'] : $_GET['pp_pval'];

        if(count($pdata) > 0){
            foreach($pdata as $key=>$val)
                $data[$key] = $val;
        }

        $vars  = get_class_vars($class);

        /*if(count($vars) > 0){
            foreach($vars as $key=>$val){
                if($val){
                    $tdata = ( isset( $_POST[$val] ) ) ? $_POST[$val] : $_GET[$val];
                    $data[$val] = $tdata;
                }
            }
        }

        print_r($data);*/

        if (count( $vars) > 0 ) {

        //p_arr($vars);

            foreach ($vars as $key => $value) {

                if( strpos( $key, '_text' ) === false && strpos( $key, '_labels' ) === false) {
                     //p_arr($data);p_arr($value);p_arr(${$value.'_labels'});
                    $missing_vals = self::$key($data, $value);
                    //p_arr($missing_vals);
                    if(is_array($missing_vals)){
                    foreach($missing_vals as $ind=>$fld){

                        $lkey = array_search($fld,$value);

                        //echo "**".$fld."**".$lkey.' **';
                        //p_arr($vars[$key.'_labels']);


                        $missing_lables[$fld] =  $vars[$key.'_labels'][$lkey]." - ".$vars[$key.'_text'];
                    }

                    }
                    //$missing_lables['validate_heading'] =  $vars[$key.'_text'];

                    //p_arr($missing_lables); exit;

                    if (count( $missing_vals ) > 0) {
                        //return '#'.implode(', #', $missing_vals) . '@@@' . $key . '_text';

                        return $missing_lables;

                    }
                }

            }
        }
    }

    public static function check_required (&$data, $fields) {

        //print_r($data);print_r($fields);exit;

        $missing_vals = array();


        foreach ($fields as $field) {

            $data[$field] = trim($data[$field]);

            if (empty( $data[$field] )) {

                $missing_vals[$field] = 'required';

            }

            if(is_numeric($data[$field]) && floatval($data[$field]) == 0.00){

                    $missing_vals[$field] = 'required';

            }

        }

        if (count( $missing_vals ) > 0) {
            return array_keys($missing_vals);
        }

        return;
    }
      public static function check_valid_ip (&$data, $fields) {

           $missing_vals = array();

        foreach ($fields as $field) {

                $data[$field] = trim(strtolower($data[$field]));

                if($data[$field]!="")
                {
                    $ip=$data[$field];
                    if (filter_var($ip, FILTER_VALIDATE_IP) === false) {
                        $missing_vals[$field] = 'invalid';
                    }

                }
            }
              if (count( $missing_vals ) > 0) {
           // print_r($missing_vals);exit;
                    return array_keys($missing_vals);
                }
            return;
    }

    public static function check_valid_email (&$data, $fields) {

        //print_r($data);exit;

        $missing_vals = array();

        foreach ($fields as $field) {
                $data[$field] = trim(strtolower($data[$field]));
                if($data[$field]!="")
                {
                    if (!preg_match( '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)+$/', $data[$field] )) {

                        $missing_vals[$field] = 'invalid';
                    }
                }
            }

        if (count( $missing_vals ) > 0) {
            return array_keys($missing_vals);
        }

        return;
    }


    public static function check_min_length ($data, $args) {


        $missingvals = array();

        foreach ($args[0] as $field) {

            if (strlen ($data[$field]) < $args[1]) {
                $missing_vals[$field] = 'invalid';
            }
        }

        if (count( $missing_vals ) > 0) {
            return array_keys($missing_vals);
        }

        return;

    }
    public static function check_duplication($data, $fields) {

        $missingvals = array();
        $select_fields = array( 'id' );
        $app = $GLOBALS['app'];
        $uval = $GLOBALS['uval'];
        $b_id = $_POST[pval]['branch_id'];
        $email = $_POST[pval]['email'];
        $user_group_id = $_POST[pval]['user_group_id'];
        $sub_branch_code = $_POST[pval]['sub_branch_code'];

        foreach ($fields as $field) {
            if($data[$field]!="")
            {
             if(!empty($b_id)){
                 if(!empty($email) && $GLOBALS['module_name'] == 'users'){
                     $where['conditions'] = array( "$field LIKE " => $app->escape($data[$field]));
                 }else if($GLOBALS['module_name'] == 'privillege'){
                     if($_POST[pval]['action'] == 'add'){
                         $where['conditions'] = array( "branch_id =" => $b_id, "user_group_id =" => $user_group_id);
                     }else{
                         $where['conditions'] = array( "branch_id =" => "", "user_group_id =" => "");
                     }
                 }else{
                     $where['conditions'] = array( "$field LIKE " => $app->escape($data[$field]), "branch_id =" => $b_id);
                 }
             }else{
                 $where['conditions'] = array( "$field LIKE " => $app->escape($data[$field]));
             }

            if ($uval['id']){
                if(!empty($b_id) && !empty($email)){
                        $where['conditions'] = array("$field LIKE " => $data[$field], 'id != ' => $uval['id'], 'branch_id =' => $branch_id);
                    }elseif($sub_branch_code){

                        $where['conditions'] = array("$field LIKE " => $data[$field], 'id != ' => $uval['id'], 'branch_id =' => $b_id);
                        //$where['conditions'] = array("$field LIKE " => $data[$field], 'branch_id =' =>$b_id);
                    }


                    else{
                        $where['conditions'] = array("$field LIKE " => $data[$field], 'id != ' => $uval['id']);
                        //$where['conditions'] = array("$field LIKE " => $data[$field], 'branch_id =' =>$b_id);
                    }

                // print_r($where['conditions']);
                // exit;
            }

            // if ($uval['id']){
            //     if(!empty($b_id) && !empty($email)){
            //             $where['conditions'] = array("$field LIKE " => $data[$field], 'id != ' => $uval['id'], 'branch_id =' => $branch_id);
            //         }else{
            //             $where['conditions'] = array("$field LIKE " => $data[$field], 'id != ' => $uval['id']);
            //         }

            //     //print_r($where['conditions']);
            //     //exit;
            // }

            $result = $app->find($select_fields, $where);
            }
            if (count( $result ) > 0) {
                $missing_vals[$field] = 'duplicate';
                return array_keys($missing_vals);
            }

        }


        return;

    }
    public static function check_login($data, $fields) {

        $missingvals = array();
        $select_fields = array( '*' );
        $app = $GLOBALS['app'];
        $uval = $GLOBALS['uval'];

        $where['conditions'] = array("email = " => $data['email']);
        $result = $app->find($select_fields, $where);

        if (count( $result ) == "") {
            $missing_vals[$field] = 'duplicate';
            return array_keys($missing_vals);
        }
        return;
    }

  public static function check_valid_date(&$data, $fields) {

        //print_r($data);exit;

        $missing_vals = array();

        foreach ($fields as $field) {

        $entered_date = $data[$field];

        $todays_date = date("m/d/Y");
        $today = strtotime($todays_date);


        $date_entered = strtotime($entered_date);
          if($date_entered > $today){
                $missing_vals[$field] = 'invalid';
            }

       }
        if (count( $missing_vals ) > 0) {
            return array_keys($missing_vals);
        }
        return;
    }

    public static function check_valid_dateformat(&$data, $fields) {


        $missing_vals = array();

        foreach ($fields as $field) {

        $entered_date = $data[$field];
        list( $m, $d, $y ) =explode('/', $entered_date);

        if($entered_date!="" && $entered_date!="--")
            {
        $valide_formate = checkdate( $m, $d, $y );
        if($valide_formate=="")
        {
              $missing_vals[$field] = 'invalid';

        }
            }

       }
        if (count( $missing_vals ) > 0) {
            return array_keys($missing_vals);
        }
        return;
    }

 }

?>
