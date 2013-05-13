<?php
  include ('login_required.php');

  require('AdminDbHelper.php');
  if($_REQUEST['donations_type']){
    $donations_type = $_REQUEST['donations_type'];
  }else{
    $donations_type = 'authorised';
  }
  
  $site_env = 'test';
  if(isset($_SESSION['site_environment']))
    $site_env = $_SESSION['site_environment'];
    
  if($site_env == 'live'){
    $admin_db_helper = new AdminDbHelper(true);
  }else{
    $admin_db_helper = new AdminDbHelper;
  }
  
  $donation_results = $admin_db_helper->donations_list($donations_type, "donations_type=$donations_type");
  $donations = null;
  $page_nav = null;
  
  if($donation_results['results']){
	  $donations = $donation_results['results'];
	  $page_nav = $donation_results['nav_html'];
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
              <td width="50%">
     		    <table cellpadding="0" cellspacing="0" border="0">
			      <tr>
			        <?php
			          if($donations_type == 'authorised')
					    echo '<td class="tab_selected" align="center" width="93" height="25">One Time</td>';
					  else
					    echo '<td class="unsel_tab" align="center" width="103" height="25"><a class="link_subtab" href="donations.php?donations_type=authorised">One Time</a></td>';
         					    
			          if($donations_type == 'recurring')
					    echo '<td class="tab_selected" align="center" width="100" height="25">Recurring</td>';
					  else
					    echo '<td class="unsel_tab" align="center" width="100" height="25"><a class="link_subtab" href="donations.php?donations_type=recurring">Recurring</a></td>';

			        ?>
			      </tr>
			    </table>
              </td>
              <td align="right"> 
				<table cellpadding="0" cellspacing="0" border="0">				
					<tr>
					<?php
			          if($donations_type == 'refused')
					    echo '<td align="center" width="90" height="25"><strong>Refused</strong></td>';
					  else
					    echo '<td align="center" width="90" height="25"><a class="link_subtab" href="donations.php?donations_type=refused">Refused</a></td>';

			          if($donations_type == 'pending')
					    echo '<td align="center" width="90" height="25"><strong>Pending</strong></td>';
					  else
					    echo '<td align="center" width="90" height="25"><a class="link_subtab" href="donations.php?donations_type=pending">Pending</a></td>';

			          if($donations_type == 'other')
					    echo '<td align="center" width="80" height="25"><strong>Other</strong></td>';
					  else
					    echo '<td align="center" width="80" height="25"><a class="link_subtab" href="donations.php?donations_type=other">Other</a></td>';
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
              <td class="listheader" width="6%" align="center">Id</td>
              <td class="listheader" width="10%"  align="center">Amount</td>
              <td class="listheader" width="10%"  align="center">PSP Reference</td>
              <td class="listheader" width="10%"  align="center">Merchant Reference</td>
              <td class="listheader" width="20%"  align="center">Submitted IP</td>
              <td class="listheader" width="14%"  align="center">Submit Date</td>
              <?php
                if($donations_type == 'recurring')
                  echo '<td class="listheader" width="14%"  align="center">Next Donation On</td>';
                if($donations_type == 'other')
                  echo '<td class="listheader" width="14%"  align="center">Status</td>';
              ?>
            </tr>
            <?php
               $row_cnt = 1;
               if($donations){
   	             while($row = mysql_fetch_array($donations)){
				   $css_class = 'listbody_one';
				   if($row_cnt != 1){
				     if(($row_cnt % 2) == 1){
					   $css_class = 'listbody_one';
				     }else{
					   $css_class = 'listbody';
					 }
				   }
				   $row_cnt += 1;
				   $nb_date = '';
				   if($donations_type == 'recurring')
				     $nb_date = date("F jS, Y", strtotime($row['NEXT_BILLING_DATE']));
				   echo "<tr>";
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['ID']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, ($row['AMOUNT'] / 100) . " " .$row['CURRENCY']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['PSP_REFERENCE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['MERCHANT_REFERENCE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['SUBMITTED_IP']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['SUBMIT_DATE']));
				   if($donations_type == 'recurring')
				     echo(sprintf("<td class='%s' align='left' valign='top'>%s</td>", $css_class, $nb_date));				     
				   if($donations_type == 'other')
				     echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['STATUS']));				     
				   echo "</tr>";
			   }
		     }else{
				 echo '<tr><td colspan="6" class="listbody">No items found to display.</td></tr>';
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
