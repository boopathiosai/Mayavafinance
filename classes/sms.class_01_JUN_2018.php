<?php
/* SMS - Integration(Mvaayoo) - sathish santhosh kumar */

class sms{     

    public static function sendData($smsData, $sendNo)
    {
        /*
         * Return the Department details
         */
        $ch = curl_init();
        $user="sar_psg@yahoo.com:sarpsg140617";
        $receipientno = $sendNo;
        $senderID= "JWLOAN";
        $msgtxt=$smsData;
        curl_setopt($ch,CURLOPT_URL,  "http://api.mVaayoo.com/mvaayooapi/MessageCompose");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "user=$user&senderID=$senderID&receipientno=$receipientno&msgtype=4&dcs=8&ishex=1&msgtxt=$msgtxt");
        $buffer = curl_exec($ch);     
        if(empty ($buffer))
        { 
//            echo "buffer is empty "; 
            
        }
        else
        {
//            echo $buffer; 
            
        }
        
        curl_close($ch);
    }

}


/* \.SMS */
?>