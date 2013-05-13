<html>
  <head>
    <title>Marni Foundation Administration</title>
    <script language="JavaScript" src="../js/cssloader.js"></script> 
    <noscript> 
       <link rel=stylesheet type=text/css href=../css/ie.css>
    </noscript>
  </head>
  <body marginheight="0" marginwidth="0">
<form name="form1" method="post" action="process_login.php"> 
<table width="100%%"  border="0" cellspacing="0" cellpadding="0"> 
  <tr> 
    <td align="center"><table width="550" border="0" cellspacing="0" cellpadding="0"> 
        <tr> 
          <td align="left" height="20"></td> 
        </tr>
        <?php
          if(isset($_GET['login_failed'])){
		    echo " <p class='error'>Login failed</p>";
		  }
        ?> 
        <tr> 
          <td><table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td></td> 
                <td width="100%" valign="top" background="../images/topbg_1.gif">
                  <table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
                    <tr> 
                      <td height="50">&nbsp;</td> 
                      <td width="5">&nbsp;</td> 
                    </tr> 
                    <tr> 
                      <td width="5" align="left" class="pagetitle">&nbsp;</td> 
                      <td align="center" class="pagetitle">Administration Login</td> 
                    </tr> 
                    <tr> 
                      <td align="right" valign="top">&nbsp;</td> 
                      <td width="5" align="right">&nbsp;</td> 
                    </tr> 
                  </table>
                </td> 
                <td></td> 
              </tr> 
            </table></td> 
        </tr> 
        <tr> 
          <td valign="top" class="body_bg"> 
            <table width="100%"  border="0" cellspacing="0" cellpadding="0"> 
              <tr> 
                <td width="25">&nbsp;</td> 
                <td width="150" align="right" class="body">Login ID</td> 
                <td width="10">&nbsp;</td> 
                <td align="left"> <input name="admin_login" type="text" class="text"></td> 
                <td width="25">&nbsp;</td> 
              </tr> 
              <tr> 
                <td width="25">&nbsp;</td> 
                <td width="150" align="right" class="body">Password</td> 
                <td width="10">&nbsp;</td> 
                <td align="left"><input name="admin_pwd" type="password" class="text"></td> 
                <td width="25">&nbsp;</td> 
              </tr> 
              <tr> 
                <td width="25">&nbsp;</td> 
                <td width="150" align="right" class="body">Environment</td> 
                <td width="10">&nbsp;</td> 
                <td align="left">
                  <select name="site_environment" id="site_environment">
                    <option value="test">Testing</option>
                    <option value="live" selected>Live</option>
                  </select>
                </td> 
                <td width="25">&nbsp;</td> 
              </tr> 
              <tr> 
                <td>&nbsp;</td> 
                <td width="150" align="right" class="body">&nbsp;</td> 
                <td width="10">&nbsp;</td> 
                <td align="left"> 
				  <table cellpadding="0" cellspacing="0" border="0"> 
				    <tr> 
				      <td><input class="buttonmain" type="submit" name="Submit222" value="Login"></td> 
				    </tr>   
				  </table> 
                </td> 
                <td width="25">&nbsp;</td> 
              </tr> 
              <tr> 
                <td>&nbsp;</td> 
                <td width="150" align="right" class="body">&nbsp;</td> 
                <td width="10">&nbsp;</td> 
                <td align="left">&nbsp;</td> 
                <td width="25">&nbsp;</td> 
              </tr> 
              <tr> 
                <td>&nbsp;</td> 
                <td width="150" align="right" class="body">&nbsp;</td> 
                <td width="10">&nbsp;</td> 
                <td align="left">&nbsp;</td> 
                <td width="25">&nbsp;</td> 
              </tr> 
            </table></td> 
        </tr> 
      </table></td> 
  </tr> 
</table> 
</form> 
</body> 
</html>
