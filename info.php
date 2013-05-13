<?php
  include('countries.php');
  #generate country select list
  $country_options = '<option value="">[Choose]</option>';
  foreach ($country_list as $country){
	$country_options = $country_options . '<option value="'. $country .'">'. $country . '</option>';
  }

  $site_env = 'live';
  if(isset($_REQUEST['site_environment']))
    $site_env = $_REQUEST['site_environment'];
    
?>
<html>
  <head>
    <script language="JavaScript" src="./js/gen_validatorv4.js" type="text/javascript" xml:space="preserve"></script>
    <script language="javascript" src="./js/site_cssloader.js"></script>
    <script language="javascript">
      function display_other(){
        selected_amount = document.getElementById('donation_amount').value;
        if(selected_amount == 'other'){
	      document.getElementById('donation_amount_other').style.display = 'inline';
	    }else{
	      document.getElementById('donation_amount_other').style.display = 'none';
	    }
	  }	
	  
	  function reset_error_field(){
		document.capture_info.donor_f_name.className='none';
		document.capture_info.donor_f_name_img.style.visibility='hidden';		
		document.capture_info.donor_l_name.className='none';
		document.capture_info.donor_l_name_img.style.visibility='hidden';
		document.capture_info.donor_email.className='none';
		document.capture_info.donor_email_img.style.visibility='hidden';		
		document.capture_info.donation_amount_other.className='none';		
		document.capture_info.donation_amount_other_img.style.visibility='hidden';

		document.capture_info.donor_address.className='none';		
		document.capture_info.donor_address_img.style.visibility='hidden';

		document.capture_info.donor_town.className='none';		
		document.capture_info.donor_town_img.style.visibility='hidden';

		document.capture_info.donor_pcode.className='none';		
		document.capture_info.donor_pcode_img.style.visibility='hidden';
		document.capture_info.donor_country_img.style.visibility='hidden';
	  }
	  
	  function submit_form(){
		  document.capture_info.form_submit.click();
		  //document.capture_info.submit();
	  }
    </script>    
  </head>
  <body>
    <form name='capture_info' id="capture_info" method='post' action='pre_submit_process.php'>
    <input type="hidden" name="site_environment" value="<?php echo $site_env ?>" />
    <div id="main" class="main">
      <div class="header">
        <img src="./images/masthead_logo.png" class="logo" />
        <div class="title"></div>
      </div>
      <div class="field_container">
        <p class="title">Please enter your donation details</p>
        <div class="field_options">
			<div class="detail_field">
			  <p>Salutation</p>
			  <p class="field">
				<select name="donor_gender" id="donor_gender">
				  <option value="Mr">Mr</option>
				  <option value="Mrs">Mrs</option>
				  <option value="Miss">Miss</option>
				</select>
			  </p>
			</div>
			<div class="detail_field">
			  <p>First Name</p>
			  <p class="field"><img src="./images/error.png" id="donor_f_name_img" /><input type="text" name="donor_f_name" id="donor_f_name" class="none" /></p>
			</div>
			<div class="detail_field">
			  <p>Last Name</p>
			  <p class="field"><img src="./images/error.png" id="donor_l_name_img" /><input type="text" name="donor_l_name" id="donor_l_name"  class="none"  /></p>
			</div>
			<div class="detail_field">
			  <p>Email</p>
			  <p class="field"><img src="./images/error.png" id="donor_email_img" /><input type="text" name="donor_email" id="donor_email"  class="none"  /></p>
			</div>
			<div class="detail_field">
			  <p>Amount</p>
			  <p class="field">
			      <img src="./images/error.png" id="donation_amount_other_img" />
				  <select name="donation_amount" id="donation_amount" onchange="javascript:display_other()">
					<option value="5">5</option>
					<option value="10">10</option>
					<option value="15" selected>15</option>
					<option value="20">20</option>
					<option value="25">25</option>
					<option value="50">50</option>
					<option value="100">100</option>
					<option value="other">other</option>
				  </select>
				  <input type="text" name="donation_amount_other" id="donation_amount_other" style="display:none;" size="5" />
				  <select name="donation_currency" id="donation_currency">
				    <option value="USD">&#36;</option>
				    <option value="GBP">&#163;</option>
				    <option value="EUR">&#128;</option>
			   	  </select>
			  </p>
			</div>
			<div class="detail_field">
			  <p>Recurrence</p>
			  <p class="field">
				 <select name="recurrence_frequency" id="recurrence_frequency"> 
				   <option value="one_time">One time</option>
				   <option value="every_month">Monthly</option>
				   <option value="every_3_months">3 months</option>
				   <option value="every_6_months">6 months</option>
				   <option value="every_year">Yearly</option>
				 </select>
			  </p>
			</div>
			<div class="detail_field"><p class="sep">&nbsp;</p></div>
			<div class="detail_field">
			  <p>Address</p>
			  <p class="field"><img src="./images/error.png" id="donor_address_img" /><input type="text" name="donor_address" id="donor_address"  class="none"  /></p>
			</div>
			<div class="detail_field">
			  <p>Town</p>
			  <p class="field"><img src="./images/error.png" id="donor_town_img" /><input type="text" name="donor_town" id="donor_town"  class="none"  /></p>
			</div>
			<div class="detail_field">
			  <p>Postal Code</p>
			  <p class="field"><img src="./images/error.png" id="donor_pcode_img" /><input type="text" name="donor_pcode" id="donor_pcode"  class="none"  /></p>
			</div>
			<div class="detail_field">
			  <p>Country</p>
			  <p class="field"><img src="./images/error.png" id="donor_country_img" /><select name="donor_country" id="donor_country"><?php echo $country_options; ?></select></p>
			</div>			
        </div>
		<div id='capture_info_errorloc' class='error_strings' style="visibility:hidden;"></div>
      </div>
      <div id="footer">
        <p>Next step: Select your payment method</p>
        <div class="button_div" onclick="submit_form()">
          <p>next<img src='./images/next.png' border="0"/></p>          
          <input type="submit" name="form_submit" id="form_submit" style="display:none;" />
        </div>
      </div>
    </div>
    </form>
  <script language="JavaScript" type="text/javascript"
    xml:space="preserve">//<![CDATA[
//You should create the validator only after the definition of the HTML form
  var frmvalidator  = new Validator("capture_info");
    //frmvalidator.EnableOnPageErrorDisplay();
    frmvalidator.EnableOnPageErrorDisplaySingleBox();
    frmvalidator.EnableMsgsTogether();


    frmvalidator.addValidation("donor_email","req", "Please enter your Email");
    frmvalidator.addValidation("donor_email","email", "Enter a valid Email");

    frmvalidator.addValidation("donor_l_name","req","Please enter your Last Name");

    frmvalidator.addValidation("donor_f_name","req","Please enter your First Name");
    //frmvalidator.addValidation("donor_name","maxlen=30",	"Max length for FirstName is 20");

    frmvalidator.addValidation("donor_address","req","Please enter your Address");
    frmvalidator.addValidation("donor_town","req","Please enter your Town");
    frmvalidator.addValidation("donor_pcode","req","Please enter your Postal code");
    frmvalidator.addValidation("donor_country","req","Please choose your country");

        
    frmvalidator.addValidation("donation_amount_other","req","Please enter donation amount",
        "VWZ_IsListItemSelected(document.forms['capture_info'].donation_amount,'other')");    

    frmvalidator.addValidation("donation_amount_other","numeric","Please enter a valid donation amount",
        "VWZ_IsListItemSelected(document.forms['capture_info'].donation_amount,'other')");

    frmvalidator.addValidation("donation_amount_other","greaterthan=0","Please enter a valid donation amount",
        "VWZ_IsListItemSelected(document.forms['capture_info'].donation_amount,'other')");
        
//]]></script>
  </body>
</html>
