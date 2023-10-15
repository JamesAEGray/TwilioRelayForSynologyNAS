<?php

echo '<head><link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.4.1/dist/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous"/></head>';

$serverLocalIP = $_SERVER['SERVER_ADDR']; //Gets and sets your localIP

if(!extension_loaded('curl')){
    echo "<h1>cURL NOT ENABLED. THIS SCRIPT WON'T WORK.</h1>";
    echo '<p>Go to WebStation > Script Language Settings[DSM 7] (or "PHP settings" [DSM 6])</p <p>Select your PHP profile to edit. Click "Edit"</p> <p>Under "extensions" make sure "curl" is checked</p><p>Save and refresh this page.</p>';
    die(); //No Reason to give anyone any hope.
};

if ($_SERVER['REMOTE_ADDR'] != $serverLocalIP){ //You're calling from outside the server. Show the test/setup form.
  if(!empty($_POST)){//Post is not empty. Create and associative array using the supplied $_POST.
        $parameters = $_POST;
	}
	else{//There is no incoming $_POST. Probably first load.
	    $parameters = array("sid"=>"","token"=>"","body"=>"","to"=>"","from"=>"");
	}
	echo    '<h2>Enter Twilio Credentials</h2>
            <p><B>Credentials are not saved. This is just for testing purposes.</B></p>
            <form action="smsRelay.php" method="post"><table>';
            
    foreach($parameters as $key => $value){ //Build the table rows with the supplied $parameters
        echo '<tr><td>'.$key.'</td><td><input name="'.$key.'"   type="text" value="'.$value.'"></td></tr>';
    }            
    echo    '<tr><td><button type="submit">Test</button></td>   <td></td></tr>
                </table>
            </form>';
  if(empty($_POST)){die();}//$_POST is empty, do not send to Twilio.
	
	else{//$_POST is there. Send to Twilio.
	    $result = sendToTwilio($parameters);

	    $resultArray = json_decode($result,TRUE);
	    if(empty($resultArray['code'])){
            //Generate String for SMS URL
            if(isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on'){
                $smsURL = "https://";
            }
            else{  
                $smsURL = "http://";   
            }
            //Append the host(domain name, ip) to the URL.   
            $smsURL .= $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'] . '?';   

            foreach($_POST as $key => $value){//Append the post values
                $smsURL .= $key .'=' . $value . '&';
            }
            $smsURL = substr($smsURL,0,-1); //Chop off the trailing &
	        echo'<div class="alert alert-success">
	            <strong>SMS Sent Successfully.</strong>
	            </div>
	        
	        <div class="panel panel-default">
                <div class="panel-heading">Use the following as your SMS URL</div>
                <div class="panel-body">
                    <p id="smsurl">'.$smsURL.'</p>
                </div>
            </div>';
	    }
	    else{
	        echo'<div class="alert alert-danger">
                <strong>Message Failed to Send.</stong><strong> [Code: '.$resultArray['code'].']</strong> 
                    <p>'.$resultArray['message'].'</p>
            </div>';
	        //echo $result;
	    }
	    
	}
}
else{//You're calling from the local server, parse information for sending to Twilio.
    $parameters = $_GET;[${strtolower($key)}] = $value;
    sendToTwilio($parameters);
}


function sendToTwilio($p){ //Listen, I didn't want to write "parameters[]" every damn time.
    $curlOptions = array(
        CURLOPT_URL => 'https://api.twilio.com/2010-04-01/Accounts/'.$p['sid'].'/Messages.json', 
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => array('Body'=>$p['body'], 'To'=>$p['to'], 'From'=>$p['from']), //The keys have to have to be Title cased, or else Twilio rejects them. *sigh*
        CURLOPT_USERPWD => $p['sid'] .":" .$p['token'],
        CURLOPT_RETURNTRANSFER => TRUE
    );
    
    $ch     = curl_init();
    curl_setopt_array($ch, $curlOptions);
    $result = curl_exec($ch);
    curl_close($ch);
    
    return $result; //This is really nice to have for testing.
}
?>
