<?php
#This file will scan through payment_notifications table and process the entries
  require ('NotificationsDbHelper.php');
  
  $ndh_class = new NotificationsDbHelper(false);
  $notification_list_sql = $ndh_class->list_notifications();
  $processed_entries = false;
  $num_entries = 0;  
  $msg = "Test environment <br />";
  if($notification_list_sql){
    while($row = mysql_fetch_array($notification_list_sql)){
	  $num_entries += 1;	 
	  $eventStatus = $row['SUCCESS'];
	  $pspReference = $row['PSP_REFERENCE'];
	  $eventCode = $row['EVENT_CODE'];
      $result_array = null;
      $deleted = false;
	  #check if there is other notification with same status;
	  $log_entry = $ndh_class->check_log_entry($eventCode, $pspReference);
	  if ($log_entry['log_exists']){
		 if ($log_entry['current_success'] ==  $eventStatus){
			#nothing needs to be done in this case, event already handled. Delete the payment notification.
			$ndh_class->delete_payment_notification($row['ID']);
			$deleted = true;
		 }else{		   
		   if($eventStatus == 'true'){
    		 $result_array = $ndh_class->handle_notification($row);
		   }
		 }
	  }else{
        $result_array = $ndh_class->handle_notification($row);
	  }
	  $msg = $msg ."psp_reference: ". $pspReference . "====deleted====". $deleted ."====emailed====". $result_array['emailed'] . "====email_failure_message===" . $result_array['email_msg']. "<br />";
    }
  }
  echo "Number of entries processed : " . $num_entries . "<br />";
  echo $msg . "<br />";
?>
