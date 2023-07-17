<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
 ^
 + Project: 	JS Tickets
 ^
*/

defined('_JEXEC') or die('Restricted access');


class JSTicketsModelcronjob{

	protected static $crontime = '';


	public static function checkCronJob($crontime){
		//self::runCronJob();// cron developement phase
		self::$crontime = $crontime;
		if(self::$crontime < date('Y-m-d H:i:s')){ // run cron job
			self::runCronJob();
		}
		if(self::$crontime < date('Y-m-d H:i:s')){ // run cron job for the ticket via email
			self::runCronJobTicketViaEmail();
		}
		return true;
	}

	public static function runCronJobTicketViaEmail(){
		JSSupportTicketModel::getJSMOdel('ticketviaemail')->getAllEmailsforticket();
		self::updateCronJobTime();
		return;
	}

	public static function runCronJob(){

		$db = JFactory::getDBO();

		$query  = "SELECT configname,configvalue FROM `#__js_ticket_config` WHERE configfor = 'default'";
		$db->setQuery($query);
		$configs = $db->loadObjectList();
		$config_array =  array();
		foreach ($configs as $conf) {
			$config_array[$conf->configname] = $conf->configvalue;	
		}
		$curdate = date('Y-m-d H:i:s');
	    // close ticket
	    $query = "SELECT id FROM `#__js_ticket_tickets` WHERE date(DATE_ADD(lastreply,INTERVAL " . $config_array['ticket_auto_close_indays'] . " DAY)) < CURDATE() AND isanswered = 1 AND status != 4";
	    $db->setQuery($query);
	    $ticketids = $db->loadObjectList();
	    if(!empty($ticketids)){
	        foreach ($ticketids as $key) {
	            if(is_numeric($key->id)){
	            	JSSupportTicketModel::getJSMOdel('ticket')->ticketClose($key->id,$curdate);
	            }
	        }
	    }
	    // overdue ticket admin/staff set overdue date and ticket not closed and not is answered
	    $query = "SELECT id FROM `#__js_ticket_tickets` WHERE date(duedate) < CURDATE() AND status != 4 AND isanswered != 1 AND duedate != '0000-00-00 00:00:00' AND (isoverdue != 1 OR isoverdue IS NULL)";
	    $db->setQuery($query);
	    $ticketids = $db->loadObjectList();
	    if(!empty($ticketids)){
	        foreach ($ticketids as $key) {
	            if(is_numeric($key->id)){
	                JSSupportTicketModel::getJSMOdel('ticket')->markOverDueTicket($key->id,$curdate,$cron_flag=1);
	            }
	        }
	    }
		// overdue type by priority
	    $query = "SELECT id, overduetypeid, overdueinterval FROM `#__js_ticket_priorities` WHERE status = 1";
	    $db->setQuery($query);
	    $priority_overdue = $db->loadObjectList();
	    if(!empty($priority_overdue)){
	    	// overdue ticket admin/staff not answered mark overdue
	    	foreach ($priority_overdue as $key) {
	    		if($key->overduetypeid == 1){
	    			$intrval_string = " date(DATE_ADD(lastreply,INTERVAL " . (int)$key->overdueinterval." DAY)) < '".date("Y-m-d")."' AND priorityid =" .$key->id;
	    		}else{
	    			$intrval_string = " DATE_ADD(lastreply,INTERVAL " .(int) $key->overdueinterval . " HOUR) < '".date("Y-m-d H:i:s")."' AND priorityid =" .$key->id;
	    		}
	    		$query = "SELECT id FROM `#__js_ticket_tickets` WHERE ".$intrval_string." AND status != 0 AND status != 4 AND (isanswered != 1  OR isanswered IS NULL) AND (isoverdue != 1  OR isoverdue IS NULL)";
			    $db->setQuery($query);
			    $ticketids = $db->loadObjectList();
			    if(!empty($ticketids)){
			        foreach ($ticketids as $ticket) {
			            if(is_numeric($ticket->id)){
			                JSSupportTicketModel::getJSMOdel('ticket')->markOverDueTicket($ticket->id,$curdate,$cron_flag=1);
			            }
			        }
			    }
	    	}

	    	// overdue ticket new ticket not reply anyone
	    	foreach ($priority_overdue as $key) {
	    		if($key->overduetypeid == 1){
			        $intrval_string = " date(DATE_ADD(created,INTERVAL " . (int)$key->overdueinterval." DAY)) < '".date("Y-m-d")."' AND priorityid =" .$key->id;
			    }else{
			        $intrval_string = " DATE_ADD(created,INTERVAL " . (int)$key->overdueinterval . " HOUR) < '".date("Y-m-d H:i:s")."' AND priorityid =" .$key->id;
			    }
			    $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ".$intrval_string." AND status = 0 AND (isoverdue != 1  OR isoverdue IS NULL)";
			    $db->setQuery($query);
			    $ticketids = $db->loadObjectList();
			    if(!empty($ticketids)){
			        foreach ($ticketids as $ticket) {
			            if(is_numeric($ticket->id)){
			                JSSupportTicketModel::getJSMOdel('ticket')->markOverDueTicket($ticket->id,$curdate,$cron_flag=1);
			            }
			        }
			    }		
	    	}
	    }

	    
	    // feedback email function
	    JSSupportTicketModel::getJSMOdel('feedback')->sendFeedbackMail();

		$query  = "SELECT configvalue FROM `#__js_ticket_config` WHERE configname = 'ticket_auto_close_indays'";
		$db->setQuery($query);
		$autoclose = $db->loadResult();

		$query = "UPDATE `#__js_ticket_tickets` AS ticket SET ticket.`status` = 4
					WHERE ticket.`status` = 3 AND (DATE_ADD(ticket.lastreply,INTERVAL $autoclose DAY) < CURDATE() AND ticket.isanswered = 1)";
		$db->setQuery($query);
		$db->execute();

		self::updateCronJobTime();
		return;
	}

	public static function getCronTime(){
		$db = JFactory::getDBO();
		$query  = "SELECT configvalue FROM `#__js_ticket_config` WHERE configname = 'cronjob_time'";
		$db->setQuery($query);
		$result = $db->loadResult();
		self::$crontime = $result;
		return $result;
	}

	public static function updateCronJobTime(){
		$db = JFactory::getDBO();

        if(self::$crontime != ""){
			$spdate = explode(" ", self::$crontime);
			if ($spdate[1])
				$crontime = explode(':', $spdate[1]);

			$curdate = explode("-", date('Y-m-d'));

			$nextcrontime = date('Y-m-d H:i:s',strtotime("$curdate[0]-$curdate[1]-$curdate[2] $crontime[0]:$crontime[1]:$crontime[2]"));
		}else{
			$nextcrontime = date("Y-m-d H:i:s");
		}

		$nextcrontime = date('Y-m-d H:i:s',strtotime($nextcrontime." +30 minutes"));


		$query  = "UPDATE `#__js_ticket_config` SET configvalue = '".$nextcrontime."' WHERE configname = 'cronjob_time'";
		$db->setQuery($query);
		$result = $db->execute();

		//clean the cache
		$cache = JFactory::getCache('com_jssupportticket');
		$cache->setCaching( 1 );
		$cache->clean();

		return;
	}
}
	// Get a reference to the global cache object.
	//$cache = JFactory::getCache();
	$cache = JFactory::getCache('com_jssupportticket');
	$cache->setCaching( 1 );
	//$cache->clean();

	// Run the test with caching.
//	$crontime  = $cache->call( array( 'JSTicketsModelcronjob', 'getCronTime' ) );
	//$crontime = JFactory::getCache(array( 'JSTicketsModelcronjob', 'getCronTime' ) );
	$crontime = JSTicketsModelcronjob::getCronTime();
	JSTicketsModelcronjob::checkCronJob($crontime);
