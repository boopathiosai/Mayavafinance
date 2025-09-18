<?php
//**************************************************************************
 //**************************************************************************
 // Project          :  MAYAVA FINANCE                   
 // Module           :  Upload class          
 // Domain           :  http://wwa.mayavafinance.com
 // Programmer       :  SrinivasanM
 // Inprogress       :  \\offlinev5\Mayavafinance
 // Backup           :  \\dataserver\EIT\Projects\In Progress\
 // Last Modified    :  02-Feb-2010
 //**************************************************************************
 // Description      :  This is the Upload class  for Mayavafinance.
//************************************************************************** 


class Upload {

	public static $file = '';
	public static $file_content = '';
	public static $upload_path = '';
	public static $filename = '';
	public static $new_filename = '';
	public static $width = '';
	public static $height = '';
	public static $extension = '';

	public static function upload_file() {

		self::$file_content = self::read_filecontent();

		if(!empty(self::$file_content)) {
			if(empty(self::$new_filename)) {
				self::$new_filename = ereg_replace("[^a-z0-9._]", "", str_replace(" ", "_", str_replace("%20", "_", strtolower(self::$filename))));
			}
			self::$upload_path .= '/' . self::$new_filename;
			return file_put_contents(self::$upload_path, self::$file_content);
		}
	}

	public static function read_filecontent() {

		if($_FILES[self::$file]['size'] > 0) {

			self::$filename = $_FILES[self::$file]['name'];
			self::$extension = substr(strrchr($_FILES[self::$file]['name'], '.'), 1);

			$img_extensions = array('jpg', 'gif', 'png');

			if(in_array(self::$extension, $img_extensions)) {
				$dimensions = getimagesize($_FILES[self::$file]['tmp_name']);
				self::$width = $dimensions[0];
				self::$height = $dimensions[1];
			}

			return file_get_contents($_FILES[self::$file]['tmp_name']);
		}
	}
}
?>