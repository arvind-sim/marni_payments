<?php
  $auth_result = '';
  if(isset($_REQUEST['auth_result']))
    $auth_result = $_REQUEST['auth_result'];
?>

<html>
  <head>
    <script language="JavaScript" src="./js/gen_validatorv4.js" type="text/javascript" xml:space="preserve"></script>
    <script language="javascript" src="./js/site_cssloader.js"></script>
    <script language="javascript">
      function close_this(){
		  try{
			  window.parent.close_lightbox();
		  }catch(Exception){
		  }
	  }
    </script>    
  </head>
  <body>
    <form name='capture_info' id="capture_info" method='post' action='pre_submit_process.php'>
    <div id="main" class="main">
      <div class="header">
        <img src="./images/masthead_logo.png" class="logo" />
        <div class="title"></div>
      </div>
      <div class="field_container">
        <?php
          if($auth_result == 'CANCELLED'){
		    echo '<p class="failure_title">We are sorry to see that you could not contribute at this point.</p>';
            echo '<div class="field_options"><img src="./images/warning.png" border="0"/></div>';
		  }else{
		    echo '<p class="success_title">Thank you for your contribution.</p>';
            echo '<div class="field_options"><img src="./images/confirm.png" border="0"/></div>';
		  }
        ?>       
      </div>
      <div id="footer_close">
        <div class="button_div_close" onclick="close_this();" id="button_link"><p>close</p></div>
      </div>
    </div>
    </form>
  </body>
</html>
