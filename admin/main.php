<?php
  include ('login_required.php');

  if(isset($_REQUEST['tab_type']))
    $seltab = $_REQUEST['tab_type'];
  else
    $seltab = 'donations';
  
  $listval = '';
  
  if($seltab == 'donors'){
	if(isset($_REQUEST['donors_type']))
      $listval = 'donors.php?donor_type='. $_REQUEST['donor_type'];
    else
      $listval = 'donors.php?donor_type=active';    
  }

  if($seltab == 'donations'){
	if(isset($_REQUEST['donations_type']))
      $listval = 'donations.php?donations_type='. $_REQUEST['donations_type'];
    else
      $listval = 'donations.php?donations_type=authorised';
  }

  if($seltab == 'notifications'){
    $listval = 'notifications.php';
  }
  
?>
<html>
<head>
 <title>Marni Foundation admin</title>
</head>
<frameset rows = "60,*" frameborder="0" border="0">
  <frame src ="./top.php?tab_type=<?php echo $seltab; ?>" name="top"/>
  <frame src ="<?php echo $listval ?>"  name="bottom"/>
</frameset>
</html>
