<?php
 class ResultHandler
 {
	private $db_link = null;
	private $host_name = null;
	private $user_name = null;
	private $password = null;
	private $database_name = null;
    private $donor_table_name = 'mf_donors';
    private $donations_table_name = 'mf_donations';
    private $donation_logs_table = 'mf_donation_logs';
    
	//connect to database in the constructor
    function __construct($live = false) {
      $ini_array = parse_ini_file("database.ini", true);
      if($live){
	    $this->host_name =  $ini_array['production']['hostName'];
	    $this->user_name =  $ini_array['production']['userName'];
	    $this->password =  $ini_array['production']['password'];
	    $this->database_name = $ini_array['production']['databaseName'];
      }else{
	    $this->host_name =  $ini_array['test']['hostName'];
	    $this->user_name =  $ini_array['test']['userName'];
	    $this->password =  $ini_array['test']['password'];
	    $this->database_name = $ini_array['test']['databaseName'];
      }
	  #echo "values loaded from ini file are ". $this->host_name;	  
	  #$this->db_link = mysql_pconnect($this->host_name, $this->user_name, $this->password);
	  if (!($this->db_link = mysql_pconnect($this->host_name, $this->user_name, $this->password))) {
        echo (sprintf("error connecting to host %s, by user %s", $hostName, $userName)) ;
        exit() ;
      }
      if($this->db_link){
        if (!mysql_select_db($this->database_name, $this->db_link)) {
          echo (sprintf("Error in selecting %s database", $this->database_name)) ;
          echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
          exit() ;
        }      
      }
    }

    function __destruct(){
	  if($this->db_link){
        mysql_close($this->db_link);
      }
	}
    
    public function update_donation($payment_info){
	  $id_array = explode("_", $payment_info['merchant_reference']);
	  $donor_id = 	$id_array[1];
	  $donation_id = $id_array[2];
	  
	  $auth_result = $payment_info['auth_result'];
	  if($auth_result == 'CANCELLED'){
	    $this->cancel_donation($donation_id);
	  }else{
		$this->update_donation_status($donation_id, $payment_info);  
	  }
	  return true;
	}
	
	public function cancel_donation($donation_id){
      $updateStmt  = "Update $this->donations_table_name set STATUS='USER_CANCELLED' where id='%s'";
  	  if (!mysql_query(sprintf($updateStmt, $donation_id), $this->db_link)) {
        echo (sprintf("Error in executing %s stmt", $updateStmt)) ;
        echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
        exit() ;
      }
	}
	
	public function update_donation_status($donation_id, $p_info){
	  $a_result = 'SUBMITTED';
	  if(isset($p_info['auth_result'])){
		  if($p_info['auth_result'] == 'PENDING')
		    $a_result = $p_info['auth_result'];
	  }
	  $pay_info = $p_info;
      $updateStmt  = "Update $this->donations_table_name set STATUS='%s' where id='%s'";
  	  if (!mysql_query(sprintf($updateStmt, $a_result, $donation_id), $this->db_link)) {
        echo (sprintf("Error in executing %s stmt", $updateStmt)) ;
        echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
        exit() ;
      }
      #create donation_log_entry
      $this->log_donation($donation_id, $pay_info);
	}
	
	public function log_donation($d_id, $p_info){
      $p_status      = $p_info['auth_result'];
      $u_locale      = $p_info['shopper_locale'];
      $p_method      = $p_info['payment_method'];
      $psp_reference = $p_info['psp_reference'];
      $skin_code     = $p_info['skin_code'];     
      
      $query_check_sql = "SELECT * from $this->donation_logs_table where PSP_REFERENCE = '$psp_reference'";
      $check_sql = mysql_query($query_check_sql, $this->db_link) or die(mysql_error());
      $totalRows_check_sql = mysql_num_rows($check_sql);      
      if ($totalRows_check_sql == '0') {
  	    $addStmt = "Insert into $this->donation_logs_table(MF_DONATIONS_ID, PAYMENT_STATUS, USER_LOCALE, PAYMENT_METHOD, PSP_REFERENCE, SKIN_CODE) values('%s', '%s', '%s', '%s', '%s', '%s')";
 	    if (!mysql_query(sprintf($addStmt,$d_id, $p_status, $u_locale, $p_method, $psp_reference, $skin_code), $this->db_link)) {
          echo (sprintf("Error in executing %s stmt", $addStmt)) ;
          echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
          exit();
        }
      }
	}
 }
?>
