<html>
<?php
require('DonorDbHelper.php');

$adyen_url = "https://test.adyen.com/hpp/select.shtml";

$site_env = 'live';
if(isset($_REQUEST['site_environment']))
  $site_env = $_REQUEST['site_environment'];

if($site_env == 'live'){
  $adyen_url = "https://live.adyen.com/hpp/select.shtml";
  $s_class = new DonorDbHelper(true);
}else{	
  $s_class = new DonorDbHelper(false);
}

$shopperFirstName = $_POST['donor_f_name'];
$shopperLastName = $_POST['donor_l_name'];
$shopperGender = $_POST['donor_gender'];
$paymentAmount = $_POST['donation_amount'];



if($paymentAmount == 'other'){
	$paymentAmount = $_POST['donation_amount_other'];
}

#Adyen expects us to send amount in base currency
$paymentAmount = $paymentAmount * 100;

$currencyCode = $_POST['donation_currency'];
$shopperEmail = $_POST['donor_email'];
$submit_ip =  $_SERVER['REMOTE_ADDR'];
$secret = "marnifoundation";
$recurringFrequency = '';
$shopperReference = "";
$merchantReference = "";

if (($_POST['recurrence_frequency']) == "one_time"){
  $recurringContract = '';
  $recurringFrequency = '';
}else{
  $recurringContract = 'RECURRING';
  $recurringFrequency = $_POST['recurrence_frequency'];
}

#Donor address details
$address  = $_POST['donor_address'];
$town  = $_POST['donor_town'];
$post_code  = $_POST['donor_pcode'];
$country  = $_POST['donor_country'];

#to get the merchantRefernence and shopperReference call CreateDonor->add_donor method
$donor_params = array('first_name' => $shopperFirstName, 'last_name' => $shopperLastName, 'gender' => $shopperGender, 'email' => $shopperEmail, 'amount' => $paymentAmount, 'currency' => $currencyCode, 'recurrence' => $recurringContract, 'submitted_ip' => $submit_ip, 'recurrence_freq' => $recurringFrequency, 'address' => $address, 'town' => $town, 'post_code' => $post_code, 'country' => $country);

$new_donor = $s_class->add_donor($donor_params);
if($new_donor['shopper_reference'] != null){
	$shopperReference = $new_donor['shopper_reference'];
}
if($new_donor['merchant_reference'] != null){
	$merchantReference = $new_donor['merchant_reference'];
}

$shipBeforeDate = date('c', strtotime("+2 day"));
$skinCode = "5HAfUJAo";
$merchantAccount = 'MarniFoundationCOM' ;
$sessionValidity = date('Y-m-d', strtotime("+2 day"));

$plaintext = $paymentAmount . $currencyCode . $shipBeforeDate . $merchantReference . $skinCode . $merchantAccount . $sessionValidity . $shopperEmail . $shopperReference . $recurringContract;

$signature = base64_encode(hash_hmac('sha1',$plaintext,$secret,true));


?>
<head>
    <script language="javascript" src="./js/site_cssloader.js"></script>
    <script language="javascript">
      function submit_form(){
		  document.payment_details.submit();
	  }
    </script>
</head>

<body>
<form name="payment_details" method="post" action="<?php echo $adyen_url ?>" >
   <input type="hidden" name="merchantReference" value="<?php echo $merchantReference; ?>" />
   <input type="hidden" name="paymentAmount" value="<?php echo $paymentAmount; ?>" />
   <input type="hidden" name="currencyCode" value="<?php echo $currencyCode; ?>" />

   <input type="hidden" name="shipBeforeDate" value="<?php echo $shipBeforeDate; ?>" />
   <input type="hidden" name="sessionValidity" value="<?php echo $sessionValidity; ?>" />
   <input type="hidden" name="skinCode" value="<?php echo $skinCode; ?>" />
   <input type="hidden" name="merchantAccount" value="<?php echo $merchantAccount; ?>" />
   <input type="hidden" name="merchantSig" value="<?php echo $signature; ?>" />
   <input type="hidden" name="shopperEmail" value="<?php echo $shopperEmail; ?>" />
   <input type="hidden" name="shopperReference" value="<?php echo $shopperReference; ?>" />
   <input type="hidden" name="recurringContract" value="<?php echo $recurringContract; ?>" />

   <input type="hidden" name="signingString" value="<?php echo $plaintext; ?>" />
   <!--<input type="hidden" name="shopperLocale" value="de_DE" />	-->
   <!--<input type='submit' name='make_payment' id='make_payment' value="Make Payment" />-->
    <div id="main" class="main">
      <div class="header">
        <img src="./images/masthead_logo.png" class="logo" />
        <div class="title"></div>
      </div>
      <div class="field_container">
	    <p class="failure_title">Please wait while you are being redirected to the payment site...</p>
        <div class="field_options"></div>
      </div>
    </div> 
  </form>
  
</body>
  <script language="javascript">
    <?php
      if($shopperReference != "" && $merchantReference != ""){
        echo "setTimeout('submit_form()', 1500);" ;
	  }
	?>
  </script>
</html>

<!--<input type="hidden" name="recurringContract" value="ONECLICK" />

paymentAmount + currencyCode + shipBeforeDate + merchantReference + skinCode +
merchantAccount + sessionValidity + shopperEmail + shopperReference + recurringContract +
allowedMethods + blockedMethods + shopperStatement + merchantReturnData +
billingAddressType + offset

-->
