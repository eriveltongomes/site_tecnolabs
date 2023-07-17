<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
 ^
 + Project: 	JS Tickets
 ^ 
*/
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class jssupportticketViewjssupportticket extends JSSupportTicketView{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		if($layoutName == 'controlpanel'){
			$user = JSSupportticketCurrentUser::getInstance();
			if($user->getStaffid()){
				$latest_tickets = $this->getJSModel('jssupportticket')->getStaffLatestTickets();
				$result_graph = $this->getJSModel('jssupportticket')->getControlPanelData();
				$this->result_graph=$result_graph;
					
			}else{
				$latest_tickets = $this->getJSModel('ticket')->getUserMyTicketsForCP();	
				$latest_downloads = $this->getJSModel('downloads')->getLatestDownloadsForUserCP();	
				$latest_announcements = $this->getJSModel('announcements')->getLatestAnnouncementsForUserCP();	
				$latest_knowledgebase = $this->getJSModel('knowledgebase')->getLatestKnowledgebaseForUserCP();	
				
				$this->latest_downloads=$latest_downloads;
		        $this->latest_announcements=$latest_announcements;
		        $this->latest_knowledgebase=$latest_knowledgebase;
			}
        	$userticketstats = $this->getJSModel('jssupportticket')->getUserTicketStatsForCP();	

	        $this->userticketstats=$userticketstats;
			$this->latest_tickets=$latest_tickets;
	        
		}
		parent::display($tpl);
	}
}
?>
