<?php
define ('ROOT_PATH', '');
include ROOT_PATH . 'includes/config.php';

$app->access_table = USERS;

$user =  $app->find(array('email,password,branch_id'));


foreach($user as $val) {
$app->access_table = BRANCH;
$branch =  $app->find(array('name'), $params = array('conditions' => array('id = ' => $val['branch_id'])));
echo 'Email :&nbsp;&nbsp;&nbsp;'.$val['email'];
echo '<br>';
echo 'Password :&nbsp;&nbsp;&nbsp;'.base64_decode($val['password']);
echo '<br>';
echo 'Branch :&nbsp;&nbsp;&nbsp;'.($branch[0]['name']);
echo '<hr>';

}