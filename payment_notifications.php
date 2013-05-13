<?php
  #This file just logs the payment notifications into the payment_notifications table.
  #The notifications are handled by a cron job.
  require ('NotificationsDbHelper.php');
    
  $username = null; 
  $password = null;
  

  $ini_array = parse_ini_file("./config/basic_auth.ini");      
  $allowed_user_id =  $ini_array['http_auth_userid'];
  $allowed_pwd =  $ini_array['http_auth_pwd'];

  /*
  // mod_php
  if (isset($_SERVER['PHP_AUTH_USER'])) {
    $username = $_SERVER['PHP_AUTH_USER'];
    $password = $_SERVER['PHP_AUTH_PW']; 
  // most other servers
  } elseif (isset($_SERVER['HTTP_AUTHENTICATION'])) { 
    if (strpos(strtolower($_SERVER['HTTP_AUTHENTICATION']),'basic')===0) 
      list($username,$password) = explode(':',base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6))); 
  }

  if (is_null($username)) { 
    header('WWW-Authenticate: Basic realm="My Realm"');
    header('HTTP/1.0 401 Unauthorized');
    echo 'Text to send if user hits Cancel button'; 
    die(); 
  } else {
	 if(($username == $allowed_user_id) && ($password == $allowed_pwd)){  		
  */ 
       if($_POST['live'] == 'true')
         $ndh_class = new NotificationsDbHelper(true);
       else
         $ndh_class = new NotificationsDbHelper(false);
         
       $payment_options = array('live' => $_POST['live'], 'eventCode' => $_POST['eventCode'], 'amount' => $_POST['value'], 'currency' => $_POST['currency'], 'pspReference' => $_POST['pspReference'], 'merchantReference' => $_POST['merchantReference'], 'merchantAccountCode' => $_POST['merchantAccountCode'], 'success' => $_POST['success'], 'paymentMethod' => $_POST['paymentMethod'], 'originalReference' => $_POST['originalReference'], 'eventDate' => $_POST['eventDate'],'operations' => $_POST['operations'], 'reason' => $_POST['reason']);
	   if($ndh_class->log_payment($payment_options)){	 	   	 
         echo "[accepted]";
       }       
       /*
     }else{
      echo "Invalid login credentials submitted";
	 }
	 
  }*/
?>
