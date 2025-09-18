<?php

class Myaccount {
	/*
	 * Define the required fields and return text
	 */
	public $check_required = array('name','email');
	public $check_valid_email = array('email');
	public $check_duplication = array('email');

	public $check_required_labels = array('Name','Email');
	public $check_valid_email_labels = array('Email');
	public $check_duplication_labels = array('Email');

	public static $check_valid_email_text = 'Invalid Email';
	public static $check_required_text = 'Required Fields Missing!';
	public static $check_duplication_text = 'Email Address Already exists!';



	public static function Detail($userid) {
		/*
		 * Return the user details
		 */
		$app = $GLOBALS['app'];
		$count = $app->find_count(array('conditions' => array('id = ' => $userid)));
		if ($count > 0) {
			$query = "SELECT *
					  FROM ".USERS."
					  WHERE id ='".$userid."'";
			return $app->getrows($query);
		}
	}
}
?>