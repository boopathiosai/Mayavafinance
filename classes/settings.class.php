<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE
 // Module           :  Settings class
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  Subash S
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  07-Jun-2011
 //**************************************************************************
 // Description      :  This is the Settings class  for Mayavafinance.
//**************************************************************************


class Settings {
     /*
         * Define the required fields and return text
         */
     public $check_required = array('config_name','config_value');
     public $check_required_labels = array('Name','Value');

     public static $check_required_text = 'Required Fields Missing!';
     public static function ListviewCount() {

                            $args = func_get_args();

                            $app = $GLOBALS['app'];

                            /*
                             * Return the Branch Count
                             */
                            $query = "SELECT *
                                FROM ".SETTINGS."
                                WHERE editable NOT IN(0)
                                ORDER BY id ASC ";
                                $data = $app->getrows($query);

                                return $count = count($data);


        }

       public static function Listview() {
                /*
                 * Get the function parameters , if exist . And make the search condition
                 */

                $args = func_get_args();

                $app = $GLOBALS['app'];
                /*$query = "SELECT *
                          FROM ".SETTINGS."
                          ORDER BY id ASC ";
                    $data = $app->getrows($query);*/
                $count = self::ListviewCount();
                if ($count > 0) {
                    Paging::set_max( $count );
                    Paging::calc();// calculation for paging
                    $query = "SELECT *
                          FROM ".SETTINGS."
                          WHERE editable NOT IN(0)
                          ORDER BY id ASC ". Paging::get_sql_limit();
                    return $app->getrows($query);
                }
    }


    public static function CategoryDetail(){
    
        $args = func_get_args();

        $app = $GLOBALS['app'];

        /*
         * Return the Branch Count
         */
        $query = "SELECT *
            FROM ".SETTINGS."
            WHERE editable IN(2)
            ORDER BY id ASC ";
            $data = $app->getrows($query);

            return $data;
    
    }

    public static function LoadConfigurations() {

        $app = $GLOBALS['app'];
        $app->access_table = SETTINGS;

        $select_fields = array();
        $select_fields[] = 'config_name';
        $select_fields[] = 'config_value';

        $params = array();

        if(func_num_args() == 1) {
            $setting = func_get_args();
            $params['conditions']['config_name LIKE '] = $setting[0];
        }

        $settings = $app->find($select_fields, $params);
        $config_data = array();
        //print_r($config_data);

        if(count($settings) > 0) {
            foreach($settings as $setting) {
                $config_data[$setting[0]] = $setting[1];
            }
        }

        if(count($config_data) == 1) {
            return stripslashes($config_data[$setting[0]]);
        }

        return $config_data;
    }

    public static function FormatDatetoDB($date, $date_format) {

        switch($date_format) {
            case '%m-%d-%Y':
                list($month, $day, $year) = explode('-', $date);
                break;
            case '%d-%m-%Y':
                list($day, $month, $year) = explode('-', $date);
                break;
        }
        return $year . '-' . $month . '-' . $day;
    }

    public static function Detail($userid) {
            /*
             * Return the branch details
             */
            $app = $GLOBALS['app'];
            $count = $app->find_count(array('conditions' => array('id = ' => $userid)));
            if ($count > 0) {
                $query = "SELECT *
                            FROM ".SETTINGS."
                            WHERE id ='".$userid."'";
                return $app->getrows($query);
            }
    }
    public static function DeleteCheckMapped($val) {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            $del_val = implode(",",$val);
            $query = "SELECT *  FROM ".$table." WHERE ".$field." in(".$del_val.")";
            $data = $app->getrows($query);
            $count = count($data);
            return $count;
        }
public static function settings_value()
    {
            /*
             * Return the Department details
             */
            $app = $GLOBALS['app'];
            
            $query = "SELECT *
                      FROM ".SETTINGS."
                      WHERE 1 "; 
            return $app->getrows($query);
                      
        }
 public static function Delete($settings_id) {
            /*
             * Return the Customers details
             */
              $app = $GLOBALS['app'];
              $app->access_table = SETTINGS;
              $app->delete(array("id"=>$settings_id));
              return array("success"=>true,"error"=>$status['error']);
       
    }

    public static function getSettingsName($settings_id) 
    {
        /*
         * Return the Department details
         */
        $app = $GLOBALS['app'];
        $app->access_table = SETTINGS;
        $res = $app->find(array("config_name"),array("conditions"=>array("id = "=>$settings_id)));
        return $res[0][0];
    }
}