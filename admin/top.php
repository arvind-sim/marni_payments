<?php
  include ('login_required.php');

  $site_env = 'test';
  if(isset($_SESSION['site_environment']))
	$site_env =  $_SESSION['site_environment'];
	
  if($site_env == 'live')
    $site_type = 'Live payment site';
  else
	$site_type = 'Test payment site';

  $seltab = $_REQUEST['tab_type']; 
  $section_name = '';
?>

<html>
<head>
 <title>Marni Foundation Administration : <?php echo $section_name; ?></title>
 <link href="../css/admin.css" media="screen" rel="Stylesheet" type="text/css" />
</head>

<body marginheight="0" marginwidth="0" leftmargin="0" topmargin="0" class="main">
<table  align="center" class="main" border="0" style="border-collapse: collapse;" cellpadding="2" cellspacing="2" width="100%">
    <tr>
	  <td colspan="3" height="5"></td>
    </tr>
    <tr>
     <td width="12"></td>
     <td>
       <table width="100%"  border="0" cellspacing="0" cellpadding="0">
          <tr> 
            <td colspan="3" valign="top">
            <table width="100%%"  border="0" cellspacing="0" cellpadding="0">
                <tr> 
                  <td width="500" height="50" valign="bottom" class="line_hor">
			        <table border="0" cellspacing="0" cellpadding="0">
  				      <tr> 
				        <td> 
  				           <table width="100%" border="0" cellspacing="0" cellpadding="0">
  				             <tr>
							   <?php
								 if($seltab == 'donations'){
								   echo '<td><img src="../images/header_left.gif" width="10" height="30"></td>';
								   echo '<td class="tab_selected">Donations</td>';
								   echo '<td><img src="../images/header_right.gif" width="10" height="30"></td>';
								 }else{
								   echo '<td>&nbsp;</td>';
								   echo '<td class="tab"><a class="tab_text" href="main.php?tab_type=donations" target="_top">Donations</a></td>';
								   echo '<td>&nbsp;</td>';
								 }
							   ?>
  				             </tr>
          				  </table>
 				        </td>
         		    <td width="20"></td>
		       		<td>
    				  <table width="100%" border="0" cellspacing="0" cellpadding="0">
            	        <tr> 
						   <?php
							 if($seltab == 'donors'){
							   echo '<td><img src="../images/header_left.gif" width="10" height="30"></td>';
							   echo '<td class="tab_selected">Donors</td>';
							   echo '<td><img src="../images/header_right.gif" width="10" height="30"></td>';
							 }else{
							   echo '<td>&nbsp;</td>';
							   echo '<td class="tab"><a class="tab_text" href="main.php?tab_type=donors" target="_top">Donors</a></td>';
							   echo '<td>&nbsp;</td>';
							 }
						   ?>
            			</tr>
    				  </table>
				    </td>
         		    <td width="20"></td>
				<!--<td> <table width="100%" border="0" cellspacing="0" cellpadding="0">
				<tr> 
					<td>&nbsp;</td>
					<td class="tab"><a class="tab_text" href="contact.htm">Contact</a></td>
					<td>&nbsp;</td>
				</tr>
				</table></td>-->
		
				<td width="5">&nbsp;</td>
				</tr>
			</table>
		  </td>
          <td align="right" height="50" class="line_hor">
            <table cellpadding="2" cellspacing="0" border="0"> 
              <tr>
                <td colspan="3" align="right" class="error">
                <?php echo $site_type; ?>
                </td>
              </tr>
		      <tr> 
		        <td valign="top" align="right" class="maintabtext">
		        <?php
                  if($seltab == 'notifications'){
					  echo 'Notification Logs';
			      }else{
					  echo '<a class="bodynavlink" target="_top" href="main.php?tab_type=notifications">Notification Logs</a>';
				  } 
		       ?>		        
		        </td>
		        <td width="10"></td>
		        <td valign="top" align="right" class="maintabtext"><a href="logout.php" target="_top" class="bodynavlink">Logout</a></td>
		      </tr>
            </table>            
          </td>
          </tr>
          </table>
            </td>
          </tr>
  
          <tr> 
            <td colspan="3" valign="top" class="line_hor"></td>
          </tr>
 </table>
 </td>
     <td width="12"></td>
     </tr>

</table>




</body>

</html>

