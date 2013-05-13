<?php
  require("./lib/class.phpmailer.php");
  $mail = new PHPMailer();
  $mail->IsSMTP();
  $mail->SMTPDebug = 1; // 1 tells it to display SMTP errors and messages, 0 turns off all errors and messages, 2 prints messages only.
  $mail->Host = "relay-hosting.secureserver.net";
  #$mail->SMTPAuth = false;     // turn on SMTP authentication
  #$mail->Username = "mailer@marnifoundation.com";  // SMTP username
  #$mail->Password = "marnifoundation"; // SMTP password
  #$mail->SMTPSecure = "ssl";

  $mail->From = "donations@marnifoundation.com";
  $mail->Sender = "donations@marnifoundation.com";
  $mail->FromName = "Marni Foundation";
  $mail->AddAddress('arvind7@gmail.com', "Arvind Simhadri");
  $mail->AddCC('arvind07@rediffmail.com', "Arvind Simhadri");
  //$mail->AddReplyTo(“Email Address HERE”, “Name HERE”); // Adds a “Reply-to” address. Un-comment this to use it.
  $mail->Subject = "Your Login Information";
  $mail->Body = "Put body of message HERE.";

  if ($mail->Send() == true) {
    echo "The message has been sent"; 
  }else {
    echo "The email message has NOT been sent for some reason. Please try again later.";
    echo "Mailer error: " . $mail->ErrorInfo;
  }  
  
  #http://community.godaddy.com/groups/web-hosting/forum/topic/phpmailer-with-godaddy-smtp-email-server-script-working/
?>
