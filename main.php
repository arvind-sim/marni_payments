<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<?php echo date('Y-m-d', strtotime("+" . "3" ." months")); ?>
<html>
  <head>
    <link rel="stylesheet" type="text/css" href="css/lightwindow.css" />
    
	<!-- JavaScript -->
	<script type="text/javascript" src="./js/prototype.js"></script>
	<script type="text/javascript" src="./js/effects.js"></script>
	<script type="text/javascript" src="./js/lightwindow.js"></script>

    <script language="javascript">
      function display_lightbox(){
		myLightWindow.activateWindow({
			href: 'info.php', 
			//title: 'Waiting for the show to start in Las Vegas', 
			//author: 'Jazzmatt', 
			//caption: 'Mmmmmm Margaritas! And yes, this is me...', 
			width: 650,
			height: 640
		});		  
      }
      
      function close_lightbox(){
	    myLightWindow.deactivate();
	  }
    </script>
  </head>
  <body> 
    <input type="button" value="Donate to Marni Foundation" onclick="javascript:display_lightbox();" />
  </body>
</html>
