<?php
  require ('AdminDbHelper.php');
  
  session_start();
  $site_env = 'test';
  if(isset($_POST['site_environment']))
    $site_env = $_POST['site_environment'];
    
  if($site_env == 'live'){
    $admin_helper_class = new AdminDbHelper(true);
  }else{
    $admin_helper_class = new AdminDbHelper;
  }

  $login_failed = false;
  
  if(isset($_POST['admin_login'])){
    $admin_login = $_POST['admin_login'];
    $admin_pwd = $_POST['admin_pwd'];
    if($admin_helper_class->check_admin_user($admin_login, $admin_pwd)){
	  $login_failed = false;
	}else{
	  $login_failed = true;
	}
  }
  
  if($login_failed){
    header( 'Location: login.php?login_failed=true') ;
  }else{
	$_SESSION['admin_id'] = $admin_login;
	$_SESSION['site_environment'] = $_POST['site_environment'];
    header( 'Location: main.php') ;
  }
?>
