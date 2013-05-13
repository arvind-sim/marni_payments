<?php
require('ResultHandler.php');

$psp_reference = "";
$payment_method = "";

if(isset($_GET['pspReference'])){
  $psp_reference = $_GET['pspReference'];
}

if(isset($_GET['paymentMethod'])){
  $payment_method = $_GET['paymentMethod'];
}

$result_params = array('merchant_reference' => $_GET['merchantReference'], 'shopper_locale' => $_GET['shopperLocale'], 'payment_method' => $payment_method, 'auth_result' => $_GET['authResult'], 'psp_reference' => $psp_reference, 'skin_code' => $_GET['skinCode']);

$merchant = explode("_", $_GET['merchantReference']);

if($merchant[0] == 'DONORTEST'){
      $rh_class = new ResultHandler(false);	
	}else{
      $rh_class = new ResultHandler(true);	
	}
$handle_results = $rh_class->update_donation($result_params);
  if($handle_results){
    header( "Location: payment_result_message.php?auth_result=". $_GET['authResult']) ;
  }
?>

<!--
Payment result will be handled here.....<br />
This page is displayed in both cases where user has cancelled the payment or completed the payment.
-->
