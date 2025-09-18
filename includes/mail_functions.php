<?php

// mail functions

function sendPasswordEmail($user) {
	
	global $app;

	extract($user, EXTR_PREFIX_ALL, "account");		
	
	$account_pwd = $app->decryptPassword($user['password']);
	$account_name = $user['name_first'];
	$account_username = $user['email'];
	
	if(is_file(APP_LAYOUT_PATH . 'mails/forgot_password.html')) {
		
		$content = file_get_contents(APP_LAYOUT_PATH . 'mails/forgot_password.html');
		$patterns = array('{ACCOUNT_NAME}', '{ACCOUNT_ID}', '{ACCOUNT_PASSWORD}');		
		$replacements = array($account_name, $account_username, $account_pwd);

		$content = str_replace($patterns, $replacements, $content);		
		$mail = new PHPMailer();
		$mail->SetFrom(APP_ADMIN_EMAIL, APP_ADMIN_NAME);
		$mail->AddAddress($account_email, $account_name);
		$mail->Subject    = APP_NAME . " | Your Login Info";
		$mail->MsgHTML($content);
		return (bool) $mail->Send();
	}
}


function moneyRequestEmail($user) {
	
	global $app;

	extract($user, EXTR_PREFIX_ALL, "account");		
	
	$account_pwd = $app->decryptPassword($user['password']);
	$account_name = $user['name_first'];
	$account_username = $user['email'];
	
	if(is_file(APP_LAYOUT_PATH . 'mails/forgot_password.html')) {
		
		$content = file_get_contents(APP_LAYOUT_PATH . 'mails/money_password.html');
		$patterns = array('{ACCOUNT_NAME}', '{ACCOUNT_ID}', '{ACCOUNT_PASSWORD}');		
		$replacements = array($account_name, $account_username, $account_pwd);

		$content = str_replace($patterns, $replacements, $content);		
		$mail = new PHPMailer();
		$mail->SetFrom(APP_ADMIN_EMAIL, APP_ADMIN_NAME);
		$mail->AddAddress($account_email, $account_name);
		$mail->Subject    = APP_NAME . " | Your Money Request Info";
		$mail->MsgHTML($content);
		return (bool) $mail->Send();
	}
}



function sendProductInformation($prodRows, $cust) {

	$sentCount = 0;
	global $app,$prod;

	if(is_file(APP_LAYOUT_PATH . 'mails/product_order.html')) {

			$content = file_get_contents(APP_LAYOUT_PATH . 'mails/product_order.html');
			
			$product_list = "<ul>";
			$total_quantity = 0;
			$total_product = 0;
			$total_amount =0;
			$tax_amount =0;
			foreach($prodRows as $row){
				/*$arrImg = explode(",",$row["photo_names"]);
				array_filter($arrImg);*/
				$photos = $prod->getPhotoNames($row['id']);
				
				//eccc
				$total_quantity += $row["quantity"];
				$total_product ++;
				$total_amount += $row["amount"];
				$imgsrc = returnImageFile(trim($photos["main"]),$row["id"],THUMB);
				$product_list .= "<li><img src=". $imgsrc . " width=".PRODUCT_PHOTO_THUMB_IMAGE_WIDTH ."> - ".$row["product_name"]."-".$row["quantity"]." no of Quantity and Amount of ".convertToCurrency($row["amount"])."</li>";
			}
			$tax_amount = (SHIP_TAX_PERCENTAGE/100) * $total_amount;
			$total_payamount = $total_amount + $tax_amount;

			$product_list .= "</ul><hr>";
			$product_list .= "<div alig=left>Number of Quantities : ".$total_quantity."</div>";
			$product_list .= "<div alig=left>Total Amount : ".convertToCurrency($total_amount)."</div>";
			$product_list .= "<div alig=left>Tax Amount : ".convertToCurrency($tax_amount)."</div>";
			$product_list .= "<div alig=left>Total Amount to Pay  : ".convertToCurrency($total_payamount)."</div>";

			$billing_address = $cust["billing_address"]."<br>".$cust["billing_city"]."<br>".$cust["billing_state"]."<br>".$cust["billing_country"]."<br>".$cust["billing_pincode"];

			$shipping_address = $cust["shipping_address"]."<br>".$cust["shipping_city"]."<br>".$cust["shipping_state"]."<br>".$cust["shipping_country"]."<br>".$cust["shipping_pincode"];

			$patterns = array('{OrderNo}','{CUSTOMER_NAME}', '{CUSTOMER_EMAIL}', '{CUSTOMER_PHONE}', '{BILLING_ADDRESS}', '{SHIPPING_ADDRESS}', '{PRODUCT_LIST}');
			$replacements = array($cust["orderedNo"],$cust["customer_name"],$cust["customer_email"], $cust["customer_phone"], $billing_address,$shipping_address,$product_list);

			$mail_content = str_replace($patterns, $replacements, $content);

			$mail = new PHPMailer();
			$mail->SetFrom(APP_ADMIN_EMAIL, APP_ADMIN_NAME);
			$mail->AddAddress($cust["customer_email"], $cust["customer_name"]);
			$mail->Subject    = APP_NAME . " | Email for Shopping Order Notification";
			$mail->MsgHTML($mail_content);

			if($mail->Send()) {
				$sentCount++;
			}
			sleep(1);

	}

	return $mail_content;

}


?>