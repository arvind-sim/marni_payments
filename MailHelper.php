<?php
  require("./lib/class.phpmailer.php");
  
  class MailHelper
  {
    private $from_address = 'donations@marnifoundation.com';
    private $mail = null;
    
    function __construct(){
	  $this->mail = new PHPMailer();
	  $this->mail->IsSMTP();
      $this->mail->Host = "relay-hosting.secureserver.net";
      $this->mail->From = $this->from_address;
      $this->mail->FromName = "Marni Foundation";
	}
	
    public function authorized_message($gender, $f_name, $l_name, $to_address){
	  $full_name = $gender . ". " . $f_name . " " . $l_name;	  
      $this->mail->AddAddress($to_address, $full_name);
      $this->mail->AddCC('info@marnifoundation.com', "Marni Foundation");
	  $message = "Dear $full_name, \n\nWe really appreciate your donation and are now processing your payment. \n\n Sincerely, \nMarni Foundation.";
      $this->mail->Subject = "Marni Foundation";
      $this->mail->Body    = $message;
      $this->mail->Send();
	}

    public function success_message($gender, $f_name, $l_name, $to_address, $amount, $currency, $next_billing_on){
	  $full_name = $gender . ". " . $f_name . " " . $l_name;
	  $currencies = array ('USD' => '&#36;', 'GBP' => '&#163;', 'EUR' => '&#128;');
      $this->mail->AddAddress($to_address, $full_name);
      $this->mail->AddCC('info@marnifoundation.com', "Marni Foundation");
	  $message = "Dear $full_name,\n\nWe confirm receipt of your payment and on behalf of our MARNI schoolchildren thank you very much for your donation.\n\nFollowing are the details of the donation you have made:";
	  $donation_amount = ((int) $amount / 100);
	  $message = $message . "\nDonation Amount: $donation_amount $currency";
   	  if($next_billing_on != 'NULL' && $next_billing_on != NULL && $next_billing_on != ''){
	    $nxt_date = date("F jS, Y", strtotime($next_billing_on));
	    $message = $message . "\nDonation will be repeated on: $nxt_date";
	  }
	  $message = $message . "\n\nSincerely,\nMarni Foundation.";
      $this->mail->Subject = "Marni Foundation";
      $this->mail->Body    = $message;
      $this->mail->Send();
	}
	
	public function failure_message($gender, $f_name, $l_name, $to_address){
	  $full_name = $gender . ". " . $f_name . " " . $l_name;	  
      $this->mail->AddAddress($to_address, $full_name);
      $this->mail->AddCC('info@marnifoundation.com', "Marni Foundation");
	  $message = "Dear $full_name,\n\n We regret that your payment/transfer failed to reach us; please be assured that you will receive a separate email with the details of this transfer denial.\n\nSincerely,\nMarni Foundation.";
      $this->mail->Subject = "Marni Foundation";
      $this->mail->Body    = $message;
      $this->mail->Send();
	}
	
  }  
?>
