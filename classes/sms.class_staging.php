<?php
/* SMS - Integration(Mvaayoo) - sathish santhosh kumar */
// http://smsserver9.creativepoint.in/api.php?username=creativetsms&password=id123&to=9677775754,&from=CREATI&message=Welcome To Creative Point
class sms{     

    public static function sendData($smsData, $sendNo)
    {

    }
    
    public static function sendDataOld($smsData, $sendNo)
    {   
        //$smsData ='நகை அடகு விபரம் அ.எண்:1 அசல்:2 1.5% தினசரி வட்டி-GOLDLOAN';
         /*
         * Return the Department details
          * http://sms.creativepoint.in/api/push.json?apikey=5b04fd8b66b15&route=transpromo&sender=NATESA&mobileno=9500305976&text=Test
         */
        /*$ch = curl_init();
        $apikey='5b04fd8b66b15';
        $route ='transpromo';
//        $sender = 'NATESA';
        $sender = 'JWLOAN';
        $text = $smsData;             
        $mobileno = $sendNo;        
        curl_setopt($ch,CURLOPT_URL,  "http://sms.creativepoint.in/api/push.json?");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "apikey=$apikey&route=$route&sender=$sender&mobileno=$mobileno&text=$text");
        $buffer = curl_exec($ch);     
        if(empty ($buffer))
        { // echo "buffer is empty ";             
        } 
        else
        {//echo $buffer;
            }
        
        curl_close($ch);*/

        // sms gateway Rajesh 09-03-2020

        $url = "https://sms.nettyfish.com/vendorsms/pushsms.aspx?";
        
        // create a new cURL resource
        $ch = curl_init();

        $apiKey = "642cd7b5-2823-4fe9-aacc-a200b94c7b6e&clientid=5e8244fc-5b15-4d23-ac93-e80b5982ba19";

        $msisdn = "9095960991";

        //$msisdn = $sendNo;

       // $sid = "JWLOAN";
        
       // $sid = "JWLSOF";

         $sid = "KFLOAN";

        //$message = "Angler Test%20Message";

        $message = $smsData;

        $dc = 8; 
        $gwid = 2;
        // set URL and other appropriate options
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, "apiKey=" . $apiKey . "&msisdn=" . $msisdn . "&sid=". $sid . "&msg=" . $message . "&fl=0&dc=".$dc."&gwid=" . $gwid);
        curl_setopt($ch, CURLOPT_HEADER, 0);

        // grab URL and pass it to the browser
        $response = curl_exec($ch); 

        //print_r($response); 

        // close cURL resource, and free up system resources
        curl_close($ch); 
    }

}


/* \.SMS */
?>