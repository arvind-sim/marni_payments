<?php
 class DonorDbHelper
 {
	private $db_link = null;
	private $host_name = null;
	private $user_name = null;
	private $password = null;
	private $database_name = null;
    private $donor_table_name = 'mf_donors';
    private $donations_table_name = 'mf_donations';
	private $live_site = false;
	
	//connect to database in the constructor
    function __construct($live = false) {
      $ini_array = parse_ini_file("database.ini", true);
      
      if($live){
		$this->live_site = true;
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
        echo (sprintf("error connecting to host %s, by user %s", $this->host_name, $this->user_name)) ;
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

    function add_donor($donor_params){
	  $email = $donor_params['email'];
	  $submitted_ip = $donor_params['submitted_ip'];	  
      $merchantReference = null;
      $shopperReference = null;

      $prev_donor = $this->find_donor($email);
      if($prev_donor['row_id']){
		 $shopperReference = $prev_donor['donor_reference'];
		 #echo 'donor already exists' . $shopperReference ;
		 $donor_params['donor_id'] = $prev_donor['row_id'];
		 $merchantReference = $this->add_donations_entry($donor_params);
	  }else{
	    $new_donor = $this->add_donor_dbentry($donor_params);
	    if($new_donor['row_id']){
		  $shopperReference = $new_donor['donor_reference'];
		  #echo 'new donor added to db ' . $shopperReference;
		  $donor_params['donor_id'] = $new_donor['row_id'];
  		  $merchantReference = $this->add_donations_entry($donor_params);
	    }
	  }
	  $donor_entries = array('shopper_reference' => null, 'merchant_reference' => null);
	  if ($shopperReference != null){
		  $donor_entries['shopper_reference'] = $shopperReference;
	  }
	  if ($merchantReference != null){
		  $donor_entries['merchant_reference'] = $merchantReference;
	  }	  
	  return $donor_entries;
	}

    private function find_donor($email){
	  $donor = array('row_id' => null, 'shopper_reference' => null);
      $query_check_sql = "SELECT * from $this->donor_table_name where email = '$email'";
      $check_sql = mysql_query($query_check_sql, $this->db_link) or die(mysql_error());
      $totalRows_check_sql = mysql_num_rows($check_sql);      
      if ($totalRows_check_sql != '0') {
	    while($row = mysql_fetch_array($check_sql)){
		  $donor['row_id'] = $row['ID'];
		  $donor['donor_reference'] = $row['DONOR_REFERENCE'];		  
		}
      } 
      return $donor;     
	}
		
	private function add_donor_dbentry($d_params){
	  $donor = array('row_id' => null, 'shopper_reference' => null);
      $rdate =  date("Y-m-d H:i:s");
      $f_name = $d_params['first_name'];
      $l_name = $d_params['last_name'];
      $gender = $d_params['gender'];
      $email = $d_params['email'];
      $address = $d_params['address'];
      $town =  $d_params['town'];
      $post_code = $d_params['post_code'];
      $country =  $d_params['country'];
      
	  $addStmt = "Insert into $this->donor_table_name(FIRST_NAME, LAST_NAME, GENDER, EMAIL, REGISTERED_DATE,ADDRESS, TOWN, POSTAL_CODE, COUNTRY) values('%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')" ;
	  if (!mysql_query(sprintf($addStmt,$f_name, $l_name, $gender, $email, $rdate, $address, $town, $post_code, $country), $this->db_link)) {
        echo (sprintf("Error in executing %s stmt", $addStmt)) ;
        echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
        exit() ;
      }
      
      $added_donor = $this->find_donor($email);
      $donor_id = $added_donor['row_id'];
	  $donor['row_id'] = $donor_id;
      if($donor_id){
        if($this->live_site){
		  $d_reference = "MFDONOR_" . sprintf("%06d",$donor_id);   
		}else{
		  $d_reference = "MFDONORTEST_" . sprintf("%06d",$donor_id);   
		}
    	$donor['donor_reference'] = $d_reference;
        $updateStmt  = "Update $this->donor_table_name set DONOR_REFERENCE='%s' where id='%s'";
  	    if (!mysql_query(sprintf($updateStmt,$d_reference, $donor_id), $this->db_link)) {
          echo (sprintf("Error in executing %s stmt", $updateStmt)) ;
          echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
          exit() ;
        }        
      }
      return $donor;
	}
	
	private function add_donations_entry($donation_params){
	  $merchantReference = null;
	  $donor_id = $donation_params['donor_id'];
      $is_recurring = $donation_params['recurrence'];
      $recurrence_freq = $donation_params['recurrence_freq'];
      $currency = $donation_params['currency'];
      $rc_interval = null;
      $amount = $donation_params['amount'];
      $submit_ip = $donation_params['submitted_ip'];
      $def_status = 'SUBMISSION_PENDING';
      $rdate =  date("Y-m-d H:i:s");
      
      if($is_recurring != ''){
	    $rc_interval = $this->recurrence_interval($recurrence_freq);
  	    $addStmt = "Insert into $this->donations_table_name(MF_DONORS_ID, SUBMIT_DATE, RECURRING, AMOUNT, CURRENCY, SUBMITTED_IP, STATUS) values('%s', '%s', '%s', '%s', '%s', '%s', '%s')";
	    if (!mysql_query(sprintf($addStmt,$donor_id, $rdate, $rc_interval, $amount, $currency, $submit_ip, $def_status), $this->db_link)) {
          echo (sprintf("Error in executing %s stmt", $addStmt)) ;
          echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
          exit();
        }	  
      }else{
   	    $addStmt = "Insert into $this->donations_table_name(MF_DONORS_ID, SUBMIT_DATE, AMOUNT, CURRENCY, SUBMITTED_IP, STATUS) values('%s', '%s', '%s', '%s', '%s', '%s')";
	    if (!mysql_query(sprintf($addStmt,$donor_id, $rdate, $amount, $currency, $submit_ip, $def_status), $this->db_link)) {
          echo (sprintf("Error in executing %s stmt", $addStmt)) ;
          echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
          exit();
        }
	  }
	  
      #fetch the donations table latest entry to configure merchantReference
      $donation_id = $this->latest_donation_entry($donor_id);
      if($donation_id != null){
        if($this->live_site){		  
          $merchantReference = "DONOR_" . $donor_id . "_" . $donation_id;
        }else{
          $merchantReference = "DONORTEST_" . $donor_id . "_" . $donation_id;
		}  
        $updateStmt  = "Update $this->donations_table_name set MERCHANT_REFERENCE='%s' where id='%s'";
  	    if (!mysql_query(sprintf($updateStmt,$merchantReference, $donation_id), $this->db_link)) {
          echo (sprintf("Error in executing %s stmt", $updateStmt)) ;
          echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
          exit() ;
        }        
      }
            
	  return $merchantReference;		
	}

    private function latest_donation_entry($donor_id){
	  $donation_id = null;
      $query_check_sql = "SELECT * from $this->donations_table_name where MF_DONORS_ID = '$donor_id' order by SUBMIT_DATE DESC LIMIT 1";
      $check_sql = mysql_query($query_check_sql, $this->db_link) or die(mysql_error());
      $totalRows_check_sql = mysql_num_rows($check_sql);      
      if ($totalRows_check_sql != '0') {
	    while($row = mysql_fetch_array($check_sql)){
		  $donation_id = $row['ID'];
		}
      } 
      return $donation_id;
    }
	
	private function recurrence_interval($recurrence){
	  $recurrence_interval = 30;
	  switch($recurrence){
	    case 'every_3_months':
		  $recurrence_interval = 90;
          break;
		case 'every_6_months':
		  $recurrence_interval = 180;
          break;
		case 'every_year':
		  $recurrence_interval = 360;
          break;
	  }
	  return $recurrence_interval;
	}
 }
 #check if donor already exists
 #create donation entry
 #return 
?>
