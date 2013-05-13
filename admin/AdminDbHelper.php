<?php
 include('../lib/ps_pagination.php');
 
 class AdminDbHelper
 {
	private $db_link = null;
	private $host_name = null;
	private $user_name = null;
	private $password = null;
	private $database_name = null;
	private $donors_table_name = 'mf_donors';
    private $payment_logs_table = 'mf_notification_logs';
    private $donations_table_name = 'mf_donations';
    private $admin_users = 'mf_admin_users';
    
	//connect to database in the constructor
    function __construct($live = false) {
      $ini_array = parse_ini_file("../database.ini", true);
      if($live == true){
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

	public function donors_list($donor_type, $page_params){
	  if($donor_type == 'active'){
        $query_check_sql = "SELECT DISTINCT($this->donors_table_name.ID),FIRST_NAME, LAST_NAME, GENDER, EMAIL,DONOR_REFERENCE,REGISTERED_DATE,COUNT($this->donors_table_name.ID) as DONATION_COUNT from $this->donors_table_name LEFT JOIN $this->donations_table_name on $this->donations_table_name.MF_DONORS_ID = $this->donors_table_name.ID where STATUS = 'ACTIVE' group by MF_DONORS_ID";
      }else{
        $query_check_sql = "SELECT DISTINCT($this->donors_table_name.ID),FIRST_NAME, LAST_NAME, GENDER, EMAIL,DONOR_REFERENCE,REGISTERED_DATE,COUNT($this->donors_table_name.ID) as DONATION_COUNT from $this->donors_table_name LEFT JOIN $this->donations_table_name on $this->donations_table_name.MF_DONORS_ID = $this->donors_table_name.ID group by MF_DONORS_ID";
      }
      
      $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
      #$check_sql = mysql_query($query_check_sql, $this->db_link) or die(mysql_error());      
      $check_sql = $pager->paginate();
	  #if(!$check_sql) die(mysql_error());	
      $result_array = array('results' => $check_sql, 'nav_html' => $pager->renderNav('<span>', '</span>'));
      return $result_array;
	}
	
	public function donations_list($donation_type, $page_params){
		
	  switch($donation_type){
		  case 'authorised':
            $query_check_sql = "SELECT $this->donations_table_name.*, PSP_REFERENCE from $this->donations_table_name  left join mf_notification_logs on mf_notification_logs.MERCHANT_REFERENCE=mf_donations.MERCHANT_REFERENCE where STATUS='ACTIVE' and mf_notification_logs.EVENT_CODE='AUTHORISATION' ORDER BY SUBMIT_DATE DESC";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          case 'pending':
            $query_check_sql = "SELECT $this->donations_table_name.*, PSP_REFERENCE from $this->donations_table_name left join mf_notification_logs on mf_notification_logs.MERCHANT_REFERENCE=mf_donations.MERCHANT_REFERENCE where STATUS='PENDING' ORDER BY SUBMIT_DATE DESC";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          case 'recurring':
            $query_check_sql = "SELECT $this->donations_table_name.*,PSP_REFERENCE from $this->donations_table_name left join mf_notification_logs on mf_notification_logs.MERCHANT_REFERENCE=mf_donations.MERCHANT_REFERENCE where RECURRING <> 'NONE' and NEXT_BILLING_DATE IS NOT NULL ORDER BY SUBMIT_DATE DESC";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          case 'refused':
            $query_check_sql = "SELECT $this->donations_table_name.*,PSP_REFERENCE from $this->donations_table_name left join mf_notification_logs on mf_notification_logs.MERCHANT_REFERENCE=mf_donations.MERCHANT_REFERENCE where STATUS='REFUSED' ORDER BY SUBMIT_DATE DESC";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          default:
            $query_check_sql = "SELECT * from $this->donations_table_name where STATUS not in ('AUTHORISED', 'PENDING', 'ACTIVE', 'REFUSED') ORDER BY SUBMIT_DATE DESC";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;   
	  }
	        
      #$check_sql = mysql_query($query_check_sql, $this->db_link) or die(mysql_error());      
      $check_sql = $pager->paginate();
	  #if(!$check_sql) die(mysql_error());	
      $result_array = array('results' => $check_sql, 'nav_html' => $pager->renderNav('<span>', '</span>'));
      return $result_array;
	}

	public function notifications_list($notify_type, $page_params){		
	  switch($notify_type){
		  case 'authorised':
            $query_check_sql = "SELECT * from $this->payment_logs_table where EVENT_CODE='AUTHORISATION'";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          case 'pending':
            $query_check_sql = "SELECT * from $this->payment_logs_table where EVENT_CODE='PENDING'";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          case 'captured':
            $query_check_sql = "SELECT * from $this->payment_logs_table where EVENT_CODE='CAPTURE'";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          case 'cancelled':
            $query_check_sql = "SELECT * from $this->payment_logs_table where EVENT_CODE='CANCELLATION'";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;
          default:
            $query_check_sql = "SELECT * from $this->payment_logs_table where EVENT_CODE not in ('AUTHORISATION', 'PENDING', 'CAPTURE', 'CANCELLATION')";
            $pager = new PS_Pagination($this->db_link, $query_check_sql, 50, 100, $page_params);
            break;   
	  }
	        
      #$check_sql = mysql_query($query_check_sql, $this->db_link) or die(mysql_error());      
      $check_sql = $pager->paginate();
	  #if(!$check_sql) die(mysql_error());	
      $result_array = array('results' => $check_sql, 'nav_html' => $pager->renderNav('<span>', '</span>'));
      return $result_array;
	}	
	
	public function check_admin_user($admin_login, $admin_pwd){
      $query_check_sql = "SELECT * from $this->admin_users where USER_ID='%s' and PASS_CODE='%s'";
      $user_exists = false;
      $check_sql = mysql_query(sprintf($query_check_sql, $admin_login, $admin_pwd), $this->db_link) or die(mysql_error());
      $totalRows_check_sql = mysql_num_rows($check_sql);      
      if ($totalRows_check_sql != '0') {
	    while($row = mysql_fetch_array($check_sql)){
		  $user_exists = true;	 
		}
      }
      return $user_exists;
	}
	
}
?>
