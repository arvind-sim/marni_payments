<?php
  include ('login_required.php');

  require('AdminDbHelper.php');
  if(isset($_REQUEST['notify_type'])){
    $notify_type = $_REQUEST['notify_type'];
  }else{
    $notify_type = 'authorised';
  }
  $site_env = 'test';
  if(isset($_SESSION['site_environment']))
    $site_env = $_SESSION['site_environment'];
    
  if($site_env == 'live'){
    $admin_db_helper = new AdminDbHelper(true);
  }else{
    $admin_db_helper = new AdminDbHelper;
  }

  $notification_results = $admin_db_helper->notifications_list($notify_type, "notify_type=$notify_type");
  $notifications = null;
  $page_nav = null;
  
  if($notification_results['results']){
    $notifications = $notification_results['results'];
	$page_nav = $notification_results['nav_html'];
  }
  
?>
<html>
  <head>
    <script language="javascript" src="../js/cssloader.js"></script>
  </head>
  <body>
    <table width="100%" cellpadding="0" cellspacing="0" border="0">
      <tr>
        <td width="10"></td>
        <td>  
          <table width="100%" cellpadding="0" cellspacing="0" border="0">
            <tr>
              <td width="100%">
     		    <table cellpadding="0" cellspacing="0" border="0">
			      <tr>
			        <?php
			          if($notify_type == 'authorised')
					    echo '<td class="tab_selected" align="center" width="100" height="25">Authorised</td>';
					  else
					    echo '<td class="unsel_tab" align="center" width="100" height="25"><a class="link_subtab" href="notifications.php?notify_type=authorised">Authorised</a></td>';
         					    
			          if($notify_type == 'captured')
					    echo '<td class="tab_selected" align="center" width="100" height="25">Captured</td>';
					  else
					    echo '<td class="unsel_tab" align="center" width="100" height="25"><a class="link_subtab" href="notifications.php?notify_type=captured">Captured</a></td>';

			          if($notify_type == 'other')
					    echo '<td class="tab_selected" align="center" width="80" height="25">Other</td>';
					  else
					    echo '<td class="unsel_tab" align="center" width="80" height="25"><a class="link_subtab" href="notifications.php?notify_type=other">Other</a></td>';
			        ?>
			      </tr>
			    </table>
              </td>
            </tr>
          </table>
		  <table width="100%" cellpadding="0" cellspacing="0" border="0">
		    <tr>
			  <td width="65%" class="status" height="23" >
			    <table cellpadding="2" cellspacing="0" border="0">
				  <tr>
					<td width="20"></td>
				  </tr>
				</table>           
			  </td>			  
			  <td width="35%" class="status" align="right">
				<div id="timetxt"></div>
			  </td>
			</tr>
			<!--<tr>
			  <td width="100%" colspan="2" align="center">
			    <table cellpadding="2" cellspacing="0" border="0">
				  <tr>
				    <td class="msglabel">&nbsp;</td>
				  </tr>
				</table>           
			   </td>
			 </tr>-->
		  </table>
 		  
          <table width="100%" cellpadding="2" cellspacing="0" border="1" style="border-collapse: collapse;" border-color="#000000">
            <tr>
              <td class="listheader" width="4%" align="center">Id</td>
              <td class="listheader" width="5%"  align="center">Amount</td>
              <td class="listheader" width="5%"  align="center">Live</td>
              <td class="listheader" width="10%"  align="center">Status</td>
              <td class="listheader" width="10%"  align="center">Psp Reference</td>
              <td class="listheader" width="10%"  align="center">Merchant Reference</td>
              <td class="listheader" width="5%"  align="center">Success</td>
              <td class="listheader" width="5%"  align="center">Payment Method</td>
              <td class="listheader" width="10%"  align="center">Event date</td>
              <td class="listheader" width="15%"  align="center">Reason</td>
              <td class="listheader" width="4%"  align="center">Emailed</td>
              <td class="listheader" width="7%"  align="center">Emailed on</td>
              <td class="listheader" width="10%"  align="center">Email Failed</td>
            </tr>
            <?php
               $row_cnt = 1;
               if($notifications){
   	             while($row = mysql_fetch_array($notifications)){
				   $css_class = 'listbody_one';
				   if($row_cnt != 1){
				     if(($row_cnt % 2) == 1){
					   $css_class = 'listbody_one';
				     }else{
					   $css_class = 'listbody';
					 }
				   }
				   $row_cnt += 1;
				   echo "<tr>";
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['ID']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, ($row['AMOUNT'] / 100) . " " .$row['CURRENCY']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['LIVE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['EVENT_CODE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['PSP_REFERENCE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['MERCHANT_REFERENCE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['SUCCESS']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['PAYMENT_METHOD']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['EVENT_DATE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['REASON']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['EMAILED']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['EMAILED_ON']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['EMAIL_FAILED']));
				   echo "</tr>";
			   }
		     }else{
				 echo '<tr><td colspan="13" class="listbody">No items found to display.</td></tr>';
			 }
			?>
		  </table>
		  <?php
		    if($page_nav != '<span> 1 </span>'){
     		  echo "<table cellpadding='0' cellspacing='0' border='0' width='100%'><tr><td height='10'></td></tr><tr><td align='center'>$page_nav</td></tr></table>";
        	}
		  ?>
        </td>
      </tr>
    </table>
  </body>
</html>
