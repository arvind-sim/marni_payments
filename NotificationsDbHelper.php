<?php
 require ('MailHelper.php');

 class NotificationsDbHelper
 {
	private $db_link = null;
	private $host_name = null;
	private $user_name = null;
	private $password = null;
	private $database_name = null;
    private $donor_table_name = 'mf_donors';
    private $donations_table_name = 'mf_donations';
    private $payment_notifications_table = 'mf_payment_notifications';
    private $payment_logs_table = 'mf_notification_logs';
       
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
	
	public function log_payment($payment_options){
	  $addStmt = "INSERT into $this->payment_notifications_table(LIVE,EVENT_CODE,AMOUNT,CURRENCY,PSP_REFERENCE,MERCHANT_REFERENCE,MERCHANT_ACCOUNT_CODE,SUCCESS,PAYMENT_METHOD,ORIGINAL_REFERENCE,EVENT_DATE,OPERATIONS,REASON,CREATED_ON) values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')";
	  
      $live = $payment_options['live'];
      $eventCode = $payment_options['eventCode'];
      $amount = $payment_options['amount'];
      $currency = $payment_options['currency'];
      $pspReference = $payment_options['pspReference'];
      $merchantReference = $payment_options['merchantReference'];
      $merchantAccountCode = $payment_options['merchantAccountCode'];
      $success = $payment_options['success'];
      $paymentMethod = $payment_options['paymentMethod'];

      $originalReference = $payment_options['originalReference'];
      $eventDate = $payment_options['eventDate'];
      $operations = $payment_options['operations'];
      $reason = $payment_options['reason'];
      $utc_str = gmdate("Y-m-d\TH:i:s\Z");
      
	  if(!mysql_query(sprintf($addStmt,$live,$eventCode,$amount,$currency,$pspReference,$merchantReference,$merchantAccountCode,$success,$paymentMethod,$originalReference,$eventDate,$operations,$reason,$utc_str), $this->db_link)) {
        echo (sprintf("Error in executing %s stmt", $addStmt)) ;
        echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
        exit();
      }else{		  
        return true;
      }
	}

    public function process_log_entry($payment_options){
      #$log_entry_exits = $this->check_log_entry($eventCode, $pspReference, $success) ;
      #$entry_exists = $log_entry_exits['log_exists'];
      #if(!$entry_exists){
     # }else{
		#  if($log_entry_exits['current_success'] == 0){
			  
		 # }else{
			  #success notification has already being set so no need to update the notification.
		 # }
		  
	 # }		
	}
	
	public function list_notifications(){
      $query_check_sql = "SELECT * from $this->payment_notifications_table ORDER BY CREATED_ON ASC";      
      $check_sql = mysql_query(sprintf($query_check_sql), $this->db_link) or die(mysql_error());
      return $check_sql;
	}
	
	public function check_log_entry($eventCode, $pspReference){
	  $log_entry = array('row_id' => null, 'log_exists' => false);
      $query_check_sql = "SELECT * from $this->payment_logs_table where EVENT_CODE = '%s' and PSP_REFERENCE = '%s'";
      $check_sql = mysql_query(sprintf($query_check_sql, $eventCode, $pspReference), $this->db_link) or die(mysql_error());
      $totalRows_check_sql = mysql_num_rows($check_sql);      
      if ($totalRows_check_sql != '0') {
	    while($row = mysql_fetch_array($check_sql)){
		  $log_entry['log_exists'] = true;
		  $log_entry['row_id'] = $row['ID'];
		  $log_entry['current_success'] = $row['SUCCESS'];		  
		}
      } 
      return $log_entry;     
	}
	
	public function handle_notification($note_info){
	  #handle the event code.
	  $mailed = false;
	  $mail_failure_msg = '';
	  $fullname = '';
	  $email = '';
      $merchantReference = $note_info['MERCHANT_REFERENCE'];
      $eventCode = $note_info['EVENT_CODE'];
      $mailer_class = new MailHelper;
      
      #get details of the user.
      $donor_info = $this->donor_details($merchantReference);
	  if($donor_info['donor_exists']){
		//  
		switch($eventCode){
          case 'AUTHORISATION':
            if($note_info['SUCCESS'] == 'true'){
              $b_updated = $this->update_donation_status($donor_info['donation_id'], 'ACTIVE', $donor_info['recurring']);
              if($b_updated){
				#make another call to db to get latest donation details.  
				$donor_info = $this->donor_details($merchantReference);
                try{
				  $mailer_class->success_message($donor_info['gender'], $donor_info['first_name'], $donor_info['last_name'],$donor_info['email'], $donor_info['amount'], $donor_info['currency'], $donor_info['next_billing_on']);
			      $mailed = true;
                }catch(Exception $e){
			      $mailed = false;
	              $mail_failure_msg = $e->getMessage();
                }
		      }
		    }else{
              $this->update_donation_status($donor_info['donation_id'], 'REFUSED', $donor_info['recurring']);
              try{
				  $mailer_class->failure_message($donor_info['gender'], $donor_info['first_name'], $donor_info['last_name'],$donor_info['email']);
			      $mailed = true;
              }catch(Exception $e){
			    $mailed = false;
	            $mail_failure_msg = $e->getMessage();
              }
			}
            break;
          case 'CANCELLATION':
            $this->update_donation_status($donor_info['donation_id'], 'CANCELLED', $donor_info['recurring']);
            break;
          case 'PENDING':
            if($note_info['SUCCESS'] == 'true'){
              $this->update_donation_status($donor_info['donation_id'], 'AUTHORISE_PENDING', $donor_info['recurring']);
		    }else{
              $this->update_donation_status($donor_info['donation_id'], 'REFUSED', $donor_info['recurring']);
              try{
				  $mailer_class->failure_message($donor_info['gender'], $donor_info['first_name'], $donor_info['last_name'],$donor_info['email']);
			      $mailed = true;
              }catch(Exception $e){
			    $mailed = false;
	            $mail_failure_msg = $e->getMessage();
              }
			}
            break;
          default:
            $this->update_donation_status($donor_info['donation_id'], $eventCode, $donor_info['recurring']);
            break;
		}
   	    #copy the info into payment_logs table.
   	    $note_info['emailed'] = $mailed;
   	    $note_info['email_failure'] = $mail_failure_msg;   	    
        if($this->copy_log_info($note_info)){
			#delete from notifications table.
		  #$this->delete_payment_notification($note_info['ID']);
		}        
	  }
	  return array('emailed' => $mailed, 'email_msg' => $mail_failure_msg);
	}
	
	public function delete_payment_notification($pn_id){
      $deleteStmt  = "DELETE from $this->payment_notifications_table where id='%s'";
  	  if (!mysql_query(sprintf($deleteStmt,$pn_id), $this->db_link)) {
        echo (sprintf("Error in executing %s stmt", $deleteStmt)) ;
        echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
        exit() ;
      }
	}
	
	
	private function update_donation_status($donation_id, $status, $recurring){
	  $b_updated = true;	
      if($donation_id != null){
        $updateStmt  = "Update $this->donations_table_name set STATUS='%s' where id='%s'";
  	    if (!mysql_query(sprintf($updateStmt,$status, $donation_id), $this->db_link)) {
          echo (sprintf("Error in executing %s stmt", $updateStmt)) ;
          echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
   	      $b_updated = false;	
          exit() ;
        }
		if($recurring != "NONE" && $status == 'ACTIVE'){
	      $nxt_billing_date = $this->recurrence_date($recurring);
	      if($nxt_billing_date){
            $updateStmt  = "Update $this->donations_table_name set NEXT_BILLING_DATE= '%s' where id='%s'";
  	        if (!mysql_query(sprintf($updateStmt,$nxt_billing_date, $donation_id), $this->db_link)) {
              echo (sprintf("Error in executing %s stmt", $updateStmt)) ;
              echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
   	          $b_updated = false;
              exit() ;
            }
		  }
		}                
      }
      return $b_updated;
	}
	
	private function recurrence_date($recurrence){
	  $nxt_date = null;	
	  switch($recurrence){
        case '30': 
		  $nxt_date = date('Y-m-d', strtotime("+ 1 month"));
		  break;
        case '90': 
		  $nxt_date = date('Y-m-d', strtotime("+ 3 months"));
		  break;
        case '180': 
		  $nxt_date = date('Y-m-d', strtotime("+ 6 months"));
		  break;
        case '360': 
		  $nxt_date = date('Y-m-d', strtotime("+ 1 year"));
		  break;
      }
      return $nxt_date;
	}
	
	private function donor_details($merchantReference){
	  $donor_entry = array('donation_id' => null, 'donor_exists' => false);
      $query_check_sql = "SELECT $this->donations_table_name.ID as donation_id, FIRST_NAME, LAST_NAME, GENDER ,EMAIL, RECURRING, AMOUNT, CURRENCY, NEXT_BILLING_DATE from $this->donations_table_name LEFT JOIN $this->donor_table_name on $this->donor_table_name.ID = $this->donations_table_name.MF_DONORS_ID where MERCHANT_REFERENCE = '%s'";
      $check_sql = mysql_query(sprintf($query_check_sql, $merchantReference), $this->db_link) or die(mysql_error());
      $totalRows_check_sql = mysql_num_rows($check_sql);      
      if ($totalRows_check_sql != '0') {
	    while($row = mysql_fetch_array($check_sql)){
		  $donor_entry['donor_exists'] = true;
		  $donor_entry['donation_id'] = $row['donation_id'];
		  $donor_entry['first_name'] = $row['FIRST_NAME'];
		  $donor_entry['last_name'] = $row['LAST_NAME'];
		  $donor_entry['gender'] = $row['GENDER'];
		  $donor_entry['email'] = $row['EMAIL'];  
		  $donor_entry['recurring'] = $row['RECURRING'];  
		  $donor_entry['amount'] = $row['AMOUNT'];  
		  $donor_entry['currency'] = $row['CURRENCY'];  
		  $donor_entry['next_billing_on'] = $row['NEXT_BILLING_DATE'];  
		}
      } 
      return $donor_entry;     
	}
	
	
	public function copy_log_info($notification_info){
	  $addStmt = "INSERT into $this->payment_logs_table(LIVE,EVENT_CODE,AMOUNT,CURRENCY,PSP_REFERENCE,MERCHANT_REFERENCE,MERCHANT_ACCOUNT_CODE,SUCCESS,PAYMENT_METHOD,ORIGINAL_REFERENCE,EVENT_DATE,OPERATIONS,REASON,EMAILED,EMAILED_ON,EMAIL_FAILED) values('%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s','%s')";
	  
	  $log_copied = false;
	  
      $live = $notification_info['LIVE'];
      $eventCode = $notification_info['EVENT_CODE'];
      $amount = $notification_info['AMOUNT'];
      $currency = $notification_info['CURRENCY'];
      $pspReference = $notification_info['PSP_REFERENCE'];
      $merchantReference = $notification_info['MERCHANT_REFERENCE'];
      $merchantAccountCode = $notification_info['MERCHANT_ACCOUNT_CODE'];
      $success = $notification_info['SUCCESS'];
      $paymentMethod = $notification_info['PAYMENT_METHOD'];

      $originalReference = $notification_info['ORIGINAL_REFERENCE'];
      $eventDate = $notification_info['EVENT_DATE'];
      $operations = $notification_info['OPERATIONS'];
      $reason = $notification_info['REASON'];
      if($notification_info['emailed']){
		    $emailed = 1;
		  }else{
		    $emailed = 0;
		  }
	  $email_failure = $notification_info['email_failure'];	        
      $emailed_on = gmdate("Y-m-d\TH:i:s\Z");
      
	  if(!mysql_query(sprintf($addStmt,$live,$eventCode,$amount,$currency,$pspReference,$merchantReference,$merchantAccountCode,$success,$paymentMethod,$originalReference,$eventDate,$operations,$reason,$emailed, $emailed_on, $email_failure), $this->db_link)) {
        echo (sprintf("Error in executing %s stmt", $addStmt)) ;
        echo (sprintf("error:%d %s", mysql_errno($this->db_link), mysql_error($this->db_link)));
        exit();
      }else{
	    $log_copied = true;
	  }
      
      return $log_copied;
	}
}
?>
