<?php
  include ('login_required.php');

  require('AdminDbHelper.php');
  
  if(isset($_REQUEST['donor_type'])){
    $donor_type = $_REQUEST['donor_type'];
  }else{
    $donor_type = 'active';
  }
  
  $site_env = 'test';
  if(isset($_SESSION['site_environment']))
    $site_env = $_SESSION['site_environment'];
    
  if($site_env == 'live'){
    $admin_db_helper = new AdminDbHelper(true);
  }else{
    $admin_db_helper = new AdminDbHelper;
  }

  $donor_results = $admin_db_helper->donors_list($donor_type, "donor_type=$donor_type");
  $donors = null;
  $page_nav = null;
  
  
  if($donor_results['results']){
	  $donors = $donor_results['results'];
	  $page_nav = $donor_results['nav_html'];
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
			          if($donor_type == 'active'){
					    echo '<td class="tab_selected" align="center" width="100" height="25">Active</td><td width="1" bgcolor="#000000"></td><td width="110" align="center" class="unsel_tab"><a class="link_subtab" href="donors.php?donor_type=all">All</a></td>';
				      }else{
					    echo '<td class="unsel_tab" align="center" width="100" height="25"><a class="link_subtab" href="donors.php?donor_type=active">Active</a></td><td width="1" bgcolor="#000000"></td><td width="110" align="center" class="tab_selected">All</td>';
					  }
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
              <td class="listheader" width="20%"  align="center">Name</td>
              <td class="listheader" width="24%"  align="center">Email</td>
              <td class="listheader" width="20%"  align="center">Donor Reference</td>
              <td class="listheader" width="10%"  align="center">Registered On</td>
              <?php
                if($donor_type == 'active')
                  echo '<td class="listheader" width="10%"  align="center">Donations Count</td>';
              ?>
            </tr>
            <?php
               $row_cnt = 1;
               if($donors){
   	             while($row = mysql_fetch_array($donors)){
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
				   echo(sprintf("<td class='%s' valign='top'>%s</td>", $css_class, $row['GENDER'] . ". " . $row['FIRST_NAME'] . " " . $row['LAST_NAME']));
				   echo(sprintf("<td class='%s' valign='top'>%s</td>", $css_class, $row['EMAIL']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['DONOR_REFERENCE']));
				   echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['REGISTERED_DATE']));
				   if($donor_type == 'active')
				     echo(sprintf("<td class='%s' align='center' valign='top'>%s</td>", $css_class, $row['DONATION_COUNT']));
				   echo "</tr>";
			     }
		       }else{
				   echo '<tr><td colspan="5" class="listbody">No items found to display.</td></tr>';

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
