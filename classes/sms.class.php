<?php 
class sms{
  public static function sendData($smsData, $sendNo, $templateId)
  {

    $url = "https://sms.textspeed.in/vb/apikey.php?apikey=skG6J1bYH8iAinH8&senderid=KFLOAN";
    $ch = curl_init();
    $postData = array(
      "templateid" => $templateId,
      "message" => $smsData,
      "number" => $sendNo,
      "unicode" => 2,
    );

    // URL-encode the data
    $postFields = http_build_query($postData);

    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/x-www-form-urlencoded'));
    curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);

      // if (curl_errno($ch)) {
      //   echo 'cURL error: ' . curl_error($ch);
      // } else {
      //   $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
      //   echo "HTTP Code: " . $httpCode . "\n";
      //   if ($httpCode == 200) {
      //     echo "Success\n";
      //   } else {
      //     echo "Request failed with HTTP Code: " . $httpCode . "\n";
      //   }
      //   print_r($response);
      // }  
    curl_close($ch);
  }     
}


/* \.SMS */
?>