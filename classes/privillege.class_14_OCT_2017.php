<?php
//**************************************************************************
//**************************************************************************
// Project          :  MAYAVA FINANCE                   
// Module           :  Brand class          
// Domain           :  http://wwa.mayavafinance.com
// Programmer       :  Vinothkuamr V
// Inprogress       :  \\offlinev5\Mayavafinance
// Backup           :  \\dataserver\EIT\Projects\In Progress\
// Last Modified    :  05-Feb-2010
//**************************************************************************
// Description      :  This is the Branch class  for Mayavafinance.
//**************************************************************************

class Privillege {
     /*
         * Define the required fields and return text
         */
        public $check_required = array('user_group_id','branch_id');
        public $check_required_labels = array('User Group','Branch Name');
        
        //public $check_duplication = array('branch_id','user_group_id');         
        //public $check_duplication_labels = array('Branch Name','User Group');         
       
        public static $check_required_text = 'Required Fields Missing!'; 
        //public static $check_duplication_text = 'Privilege Already exists!';
        
        public static function ListviewCount() {
                            
                            $args = func_get_args();
                             
                            $app = $GLOBALS['app'];
                            
                            /*
                             * Return the Branch Count
                             */
                            if(!is_superadmin($_SESSION['session_usergroupname'])){
                                $search_option = "AND MP.branch_id ='".$_SESSION['session_user_branch_id']."' AND UG.name != 'Super Admin'";
                            }
                      $query = "SELECT DISTINCT UG.id, UG.name, MP.status,  IF(MP.status = 0, 'disable', 'enable') AS status_text ,
                                            MP.branch_id, B.name as branch_name
                                            FROM ".MODULE_PRIVILLEGE." as MP
                                            LEFT JOIN ".USER_GROUP." as UG ON UG.id = MP.user_group_id
                                            LEFT JOIN ".BRANCH." as B ON B.id=MP.branch_id 
                                            WHERE  UG.name <> 'Super Admin' ".$search_option." ORDER BY id DESC ";
                            $data = $app->getrows($query);
                            return $count = count($data);
     }

        public static function Listview() {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */
                
                $args = func_get_args();
                 
                $app = $GLOBALS['app'];
                
                /*
                 * Return the branch list
                 */
                 if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_option = "AND MP.branch_id ='".$_SESSION['session_user_branch_id']."' AND UG.name != 'Super Admin'";
                 }

                    $count = self::ListviewCount();
                if ($count > 0) {
                    /*
                     * Return the Customers list
                     */
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT DISTINCT UG.id, UG.name, MP.status,  IF(MP.status = 0, 'disable', 'enable') AS status_text ,
                                MP.branch_id,MP.user_group_id, B.name as branch_name
                                FROM ".MODULE_PRIVILLEGE." as MP
                                LEFT JOIN ".USER_GROUP." as UG ON UG.id = MP.user_group_id
                                LEFT JOIN ".BRANCH." as B ON B.id=MP.branch_id 
                                WHERE  UG.name <> 'Super Admin'  ".$search_option."
                                ORDER BY MP.branch_id ". Paging::get_sql_limit();
                    return $app->getrows($query);
                }
    }
    public static function Detail($userid,$branch_id) {
            /*
             * Return the branch details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('user_group_id = ' => $userid)));
            if ($count > 0) {
             
                if(!empty($branch_id)){
                       $search_option = " AND branch_id = '".$branch_id."'";
                }
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                    $search_option = "AND MP.branch_id ='".$_SESSION['session_user_branch_id']."' AND UG.name != 'Super Admin'";
                }
                $query = "SELECT MP.module_category_id, MP.module_id, MP.action_id, MP.user_group_id, UG.name, MP.branch_id, B.name as branch_name
                            FROM ".MODULE_PRIVILLEGE." as MP
                            LEFT JOIN ".USER_GROUP." as UG ON UG.id = MP.user_group_id
                            LEFT JOIN ".BRANCH." as B ON B.id = MP.branch_id  
                            WHERE MP.user_group_id ='".$userid."'".$search_option;
                            
                //echo $query;
                //exit;
                return $app->getrows($query);
            }
    }

    public static function getModuleDetail($userid,$branch_id=0) {
            /*
             * Return the branch details
             */
             
            $app = $GLOBALS['app'];
            
            $app->access_table = MODULE_PRIVILLEGE;
            $count = $app->find_count(array('conditions' => array('user_group_id = ' => $userid)));
            
            $usermodules = array();
            if ($count > 0 && $userid!=1) {

                $categories = Privillege::getmoduleCategory();
                $count = count($categories);
                
                for($i=0; $i<$count;$i++)
                {
                    if(!empty($branch_id)){
                       $search_option = " AND branch_id = '".$branch_id."'";
                    }
                    if(!is_superadmin($_SESSION['session_usergroupname'])){
                        $search_option = "AND branch_id ='".$_SESSION['session_user_branch_id']."' AND UG.name != 'Super Admin'";
                    }
                    $query = "SELECT GROUP_CONCAT(MP.module_category_id), GROUP_CONCAT(MP.module_id) as module_id, GROUP_CONCAT(M.modulefiles) as modulefiles   
                    FROM ".MODULE_PRIVILLEGE." as MP 
                    LEFT JOIN ".MODULES." as M ON (MP.module_id = M.id) 
                    LEFT JOIN ".USER_GROUP." as UG ON UG.id = MP.user_group_id
                    WHERE user_group_id ='".$userid."' AND module_category_id ='".$categories[$i]['id']."'".$search_option;                     
                    $result = $app->getrow($query);
                    array_filter($result) ? ($usermodules[$categories[$i]['id']] = $app->getrow($query)) : '';
                }
            
                return $usermodules;
                
            }elseif($userid==1){
            
                $categories = Privillege::getmoduleCategory();
                $count = count($categories);
                
                for($i=0; $i<$count;$i++)
                {
                    $query = "SELECT GROUP_CONCAT(M.mcatid), GROUP_CONCAT(M.id) as module_id, GROUP_CONCAT(M.modulefiles) as modulefiles   
                    FROM ".MODULES." as M 
                    WHERE M.mcatid ='".$categories[$i]['id']."'";                     
                    $result = $app->getrow($query);
                    array_filter($result) ? ($usermodules[$categories[$i]['id']] = $app->getrow($query)) : '';
                    //print_r($usermodules);
                }
            
                return $usermodules;
            
            }
    }





    public static function getModuleAction($userid, $cat_id = 0, $module_file = '', $branch_id) {         
            /*
             * Return the action details
             */
            $app = $GLOBALS['app'];
            
            if($module_file != '' && !$cat_id)
            {
                $query = "SELECT mcatid as cat_id FROM ".MODULES." WHERE modulefiles = '".$module_file."'";
                $result = $app->getrow($query);
                $cat_id = $result['cat_id'];                
            }
            
            $app->access_table = MODULE_PRIVILLEGE;
            $count = $app->find_count(array('conditions' => array('user_group_id = ' => $userid)));
            if ($count > 0) {
                if(!empty($branch_id)){
                       $search_option = " AND branch_id = '".$branch_id."'";
                }
                if(!is_superadmin($_SESSION['session_usergroupname'])){
                    $search_option = "AND MP.branch_id ='".$_SESSION['session_user_branch_id']."' AND UG.name != 'Super Admin'";
                } 
                $query = "SELECT GROUP_CONCAT(action_id) as action_id
                            FROM ".MODULE_PRIVILLEGE." as MP
                            LEFT JOIN ".USER_GROUP." as UG ON UG.id = MP.user_group_id
                            WHERE module_category_id='".$cat_id."' AND user_group_id ='".$userid."'".$search_option;
                            
                //echo $query;
                //exit;
                return $app->getrow($query);
            }
    }

     public static function getmoduleCategory() {
        
        $app = $GLOBALS['app'];
        
        $query = "SELECT id, name FROM ".MODULE_CATEGORY." ";       
        
        return $app->getrows($query);       
    }


     public static function get_mneu_modules($category_id) {
        
        $app = $GLOBALS['app'];
        
        $query = "SELECT id, modulename, mcatid, modulefiles FROM ".MODULES." WHERE mcatid='".$category_id."'";     

        return $app->getrows($query);
        
    }


     public static function getmodules($category_id) {
        
        $app = $GLOBALS['app'];
        
        $query = "SELECT id, modulename, mcatid, modulefiles FROM ".MODULES." WHERE mcatid='".$category_id."' AND permission_level = 0";     

       //print_r($query);
       // exit;
        return $app->getrows($query);
        
    }

    public static function getusergroup(){
        $app = $GLOBALS['app'];
        
        $query = "SELECT id, name
                    FROM ".USER_GROUP." ";
        //print_r($query);
        //exit;
    return $app->getrows($query);
    }

    public static function getprivillege($user_type_id) {
        $app = $GLOBALS['app']; 
            
        $query = "select * from ".MODULE_PRIVILLEGE." where user_group_id=".$user_type_id;
        $resp = $app->getrow($query);
        return  $resp[0];   
    }
    
    public static function getactions($user_type_id=1, $category_id='') {
        $app = $GLOBALS['app']; 
        
        if($category_id != ''){
            $app->access_table = MODULE_CATEGORY;
            $res = $app->find(array("action_type"), array("conditions"=>array("id ="=>$category_id)));
        }
        
        if($res[0][0] == '1'){
        
            $query = "SELECT id, action FROM ".MODULE_ACTIONS." ";
            return $app->getrows($query);               
        }else{
        
            return false;
        
        }
    }
    public static function ValidateDuplication($pval) {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $require_data = 'Privillege Name Already exists For This Branch!';
            $query = "SELECT *  FROM ".USER_GROUP." WHERE branch_id = '".trim($pval['branch_id'])."' and name = '".$pval['name']."'";
            $count = count($app->getrows($query));
            return $count;
    }
    
    public static function privilegeDuplicated($branch_id, $user_group_id){
        
        $app = $GLOBALS['app'];
        
        $app->access_table = MODULE_PRIVILLEGE;
        $data = $app->find(array('user_group_id', 'branch_id'),array("conditions"=>array("user_group_id = "=>$user_group_id,"branch_id = "=>$branch_id)));
        
        if(count($data)>0){
            return true;
        }else{
            return false;
        }    
    
    }
    
    public static function deleteValidation($branch_id, $user_group_id) 
    {
                $app = $GLOBALS['app'];
                
                $data = Branch::Usergroupid();
                $head_user_group = $data[0]['user_group'];
                $head_branch_id = $data[0]['branch_id'];
                

                
                $retval = true;
                if($head_user_group == $user_group_id)
                {
                    $retval=false;
                    $error_msg[] = SUPER_ADMIN_PRIVILEGE_CANNOT_DELETE;
                }
                
                $app->access_table = USERS;
                $data = $app->find(array('user_group_id', 'branch_id'),array("conditions"=>array("user_group_id = "=>$user_group_id,"branch_id = "=>$branch_id)));
                if(count($data) > 0)
                {
                    $retval=false;
                    $error_msg[] = USERS_USE_THIS_PRIVILEGE;
                }
                
                return array("canact"=>$retval,"error"=>$error_msg);
    }
    

    public static function Delete($privilege_id) {
            /*
             * Return the Customers details
             */

            $args = explode(" ",$privilege_id);
            
            $branch_id = $args[0];
            $user_group_id = $args[1];
            
            $app = $GLOBALS['app'];

            $status = self::deleteValidation($branch_id, $user_group_id);

            if($status['canact']){

                
                /*** Write Delete function below ****/ 
                $app->access_table = MODULE_PRIVILLEGE;
                $app->delete(array("branch_id"=>$branch_id,"user_group_id"=>$user_group_id));
                
                
                return array("success"=>true,"error"=>$status['error']);
            
            }else{
            
                return array("success"=>false,"error"=>$status['error']);

            }
            
    }




    public static function getPrivilegeGroup($privilege_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        
        $args = explode(" ",$privilege_id);
                    
        $branch_id = $args[0];
        $user_group_id = $args[1];
        
        $query = "SELECT UG.name FROM ".USER_GROUP." AS UG WHERE UG.id = '".$user_group_id."'";
        $user_group = $app->getrow($query);
        
        $query = "SELECT B.name FROM ".BRANCH." AS B WHERE B.id = '".$branch_id."'";
        $branch = $app->getrow($query);
        
        
        
        return $branch[0]." ".$user_group[0];
    }
    
    
    public static function filteredUserGroup($branch_id){
        $app = $GLOBALS['app'];
        
        $query = "SELECT DISTINCT UG.id, UG.name
                    FROM ".USER_GROUP." AS UG 
                    WHERE UG.id NOT IN (SELECT DISTINCT user_group_id FROM ".MODULE_PRIVILLEGE." WHERE branch_id = '".$branch_id."')  AND UG.id <> 1 ";
                    
        //print_r($query);
        //exit;
        return $app->getrows($query);
    }
    
}