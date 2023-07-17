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

class jssupportticketViewTicket extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		global $sorton,$sortorder;
        $ticket_permissions = $user->checkUserPermissionsByView('tickets');
        $staff_permissions = $user->checkUserPermissionsByView('user');
        if($layoutName == 'formticket'){
			$email='';
			if(!$user->getIsStaff()){
				$captcha = $this->getJSModel('captcha')->getCaptchaForForm();
				$this->captcha = $captcha;
				$email = $user->getEmail();
				$this->email = $email;
			}
			$id = JFactory::getApplication()->input->get('id');
            $data = $mainframe->getUserState('com_jssupportticket.data');
            $mainframe->setUserState('com_jssupportticket.data',null);
			$result = $this->getJSModel('ticket')->getFormData($id,$data);

			JPluginHelper::importPlugin('jssupportticket');
			// $dispatcher = JDispatcher::getInstance();
			JFactory::getApplication()->triggerEvent( 'changeFormField', array(&$result));

			$this->id = $id;
			$this->lists = $result[2];
			if(isset($result[0])){
				$this->editticket = $result[0];
			}
			// for custom plugin
			if(isset($result['custom_params'])){
				$this->custom_params = $result['custom_params'];
			}
			
			if(isset($result[3])) $this->userfields = $result[3];
			$this->fieldsordering = $result[4];
			$this->ticket_permissions = $ticket_permissions;
			$juser = JFactory::getUser();
			$this->email = $juser->email;
			$this->name = $juser->name;
			$this->attachments = $result[5];
		}elseif($layoutName == 'ticketdetail'){
			if(!$user->getIsStaff()){
				$checkstatus = JFactory::getApplication()->input->get('checkstatus',null,'post');
				$jsticket = JFactory::getApplication()->input->get('jsticket',null,'get');
				if($jsticket != null || $checkstatus == 1){
					
					if($checkstatus == 1){
						$ticketid = JFactory::getApplication()->input->get('ticketid');
						$email = JFactory::getApplication()->input->getString('email');
					}else{
						$jsticket = base64_decode($jsticket);
						$array = explode(',', $jsticket);
						$ticketid = $array[0];
						$email = $array[1];
					}
					
					$res = $this->getJSModel('ticket')->checkEmailAndTicketID($email,$ticketid);
					if($res == 1){
						$session = JFactory::getApplication()->getSession();
						$session->set('userticketid',$ticketid);
						$session->set('useremail',$email);
						$id = $this->getJSModel('ticket')->getIdFromTrackingId($ticketid);					
					}else{
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid='.$Itemid;
//			        	JFactory::getApplication()->redirect(JRoute::_($link , false),JText::_('Record not found'));
				        $app = JFactory::getApplication();
				        $app->enqueueMessage(JText::_('Record not found'), 'fail');
				        $app->redirect(JRoute::_($link , false));
						// JFactory::getApplication()->redirect(JRoute::_(JURI::root().'index.php'));
					}
				}else{
					$id = JFactory::getApplication()->input->get('id');
				}
				$result = $this->getJSModel('ticket')->getTicketDetailById($id);
				if(is_array($result)){
					$this->ticketdetail = $result[0];
            		$this->isAttachmentPublished = $result['publishedInfo']->published;
            		$this->isAttachmentVisitorPublished = $result['publishedInfo']->isvisitorpublished;
					if(isset($result[2])) $this->ticketreplies = $result[2];
					if(isset($result[6])) $this->ticketattachment = $result[6];
					if(isset($result[7])) $this->userfields = $result[7];
					if(isset($result[8])) $this->fieldsordering = $result[8];
					if(isset($result[9])) $this->tickethistory = $result[9];
				//}elseif($result == 2){
				}else{
					$val_false = '';
					$this->ticketdetail=$val_false;
					$this->perm_not_allowed=$result;
				}
				$this->ticket_permissions = $ticket_permissions;
				if(isset($email)) $this->email = $email;
				$this->id = $id;
			}else{
				// ticket detail staff colde
				$id = JFactory::getApplication()->input->get('id');
				$result = $this->getJSModel('ticket')->getTicketDetailById($id);
				$this->isAttachmentPublished = $result['publishedInfo']->published;
    			$this->isAttachmentVisitorPublished = $result['publishedInfo']->isvisitorpublished;
				if(isset($result[0])) $this->ticketdetail = $result[0];
				if(isset($result[2])) $this->ticketreplies = $result[2];
				if(isset($result[6])) $this->ticketattachment = $result[6];
				if(isset($result[7])) $this->userfields = $result[7];
				if(isset($result[8])) $this->fieldsordering = $result[8];
				if(isset($result[9])) $this->tickethistory = $result[9];
				if(isset($result[1])) $this->ticketnotes = $result[1];
				if(isset($result[3])) $this->lists = $result[3];
				if(isset($result[4])) $this->isemailban = $result[4];
				if(isset($result[5])) $this->configticket = $result[5];
				$this->ticket_permissions = $ticket_permissions;
				$this->staff_permissions = $staff_permissions;
				if(isset($result[10])) $this->time_taken = $result[10];
				if(isset($result[11])) $this->ticketemail=$result[11];
			}
		}elseif($layoutName == 'print_ticket'){
				$print = JFactory::getApplication()->input->get('print', '');
				$id = JFactory::getApplication()->input->get('id');
				$result = $this->getJSModel('ticket')->getTicketDetailById($id);
				$this->ticketdetail = $result[0];
				$this->ticketreplies = $result[2];
				$this->ticketattachment = $result[6];
				if(isset($result[7])) $this->userfields = $result[7];
				if(isset($result[8])) $this->fieldsordering = $result[8];
				if(isset($result[9])) $this->tickethistory = $result[9];
				$this->ticketnotes = $result[1];
				$this->lists = $result[3];
				if(isset($result[4])) $this->isemailban = $result[4];
				if(isset($result[5])) $this->configticket = $result[5];
				$this->ticket_permissions = $ticket_permissions;
				$this->print = $print;
		}elseif($layoutName == 'myticketsstaff'){
			if(!$user->getIsStaff()){
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid='.$Itemid;
		        JFactory::getApplication()->redirect(JRoute::_($link , false));					
			}
			$defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting(1);
			$sort =  JFactory::getApplication()->input->get('sortby','');
			if ($sort == ''){
				$sort='status';
				$sort .= $defaultsort;	
			} 

			$sortby = $this->getTicketListOrdering($sort);
			$sortlinks = $this->getTicketListSorting($sort);
			$sortlinks['sorton'] = $sorton;
			$sortlinks['sortorder'] = $sortorder;
			
			//$searchkeys = JFactory::getApplication()->input->post->getArray();



	        $mainframe = JFactory::getApplication();
            //$mainframe->setUserState($option.'education',array());
	        $option = 'com_jssupportticket';

			$searchkeys['filter_ticketsearchkeys'] = $mainframe->getUserStateFromRequest($option . 'filter_ticketsearchkeys','filter_ticketsearchkeys','','string');
			$searchkeys['filter_ticketid'] = $mainframe->getUserStateFromRequest($option . 'filter_ticketid' , 'filter_ticketid' , '' , 'string');
			$searchkeys['filter_from'] = $mainframe->getUserStateFromRequest($option . 'filter_from' , 'filter_from' , '' , 'string');
			$searchkeys['filter_email'] = $mainframe->getUserStateFromRequest($option . 'filter_email' , 'filter_email' , '' , 'string');
			$searchkeys['filter_department'] = $mainframe->getUserStateFromRequest($option . 'filter_department' , 'filter_department' , '' , 'string');
			$searchkeys['filter_priority'] = $mainframe->getUserStateFromRequest($option . 'filter_priority' , 'filter_priority' , '' , 'string');
			$searchkeys['filter_subject'] = $mainframe->getUserStateFromRequest($option . 'filter_subject' , 'filter_subject' , '' , 'string');
			$searchkeys['filter_staffmember'] = $mainframe->getUserStateFromRequest($option . 'filter_staffmember' , 'filter_staffmember' , '' , 'string');
			$searchkeys['filter_assignedtome'] = $mainframe->getUserStateFromRequest($option . 'filter_assignedtome' , 'filter_assignedtome' , '' , 'int');
			$searchkeys['filter_datestart'] = $mainframe->getUserStateFromRequest($option . 'filter_datestart' , 'filter_datestart' , '' , 'string');
			$searchkeys['filter_dateend'] = $mainframe->getUserStateFromRequest($option . 'filter_dateend' , 'filter_dateend' , '' , 'string');
			
			$jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
			if($jsresetbutton == 1){ //if filter is reset, we need to put start,end dates explicitly null, because joomla make some problem for dates
				$mainframe->setUserState($option.'filter_dateend',null);
				$mainframe->setUserState($option.'filter_datestart',null);
				$searchkeys['filter_datestart'] = null;
				$searchkeys['filter_dateend'] = null;
			}
			

			$listtype = JFactory::getApplication()->input->get('lt',1);
			$result = $this->getJSModel('ticket')->getStaffMyTickets($searchkeys,$listtype,$sortby,$limitstart,$limit);
			$this->filter_data = $result[4];
			$total = $result[1];
			$this->result = $result[0];
			$this->lists = $result[2];
			$this->ticketinfo = $result[3];
			$this->listtype = $listtype;
			$this->lt = $listtype;
			if ( $total <= $limitstart ) $limitstart = 0;
			$pagination = new JPagination($total, $limitstart, $limit );
			$this->sortlinks = $sortlinks;
			$this->pagination = $pagination;
			$this->ticket_permissions = $ticket_permissions;
	        $viewuser_permission = $user->checkUserPermission('View User');
			$this->viewuser_permission = $viewuser_permission;

		}elseif($layoutName == 'mytickets'){
			if(!$user->getIsGuest()){
				if($user->getIsStaff()){
					$link = 'index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff&Itemid='.$Itemid;
			        JFactory::getApplication()->redirect(JRoute::_($link , false));					
				}
				$defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting(1);
				$sort =  JFactory::getApplication()->input->get('sortby','');
				if ($sort == ''){
			 		$sort='status';
			 		$sort .= $defaultsort;
				}
        			
        		//$searchkeys = JFactory::getApplication()->input->post->getArray();
		        $option = 'com_jssupportticket';

				$searchkeys['filter_ticketsearchkeys'] = $mainframe->getUserStateFromRequest($option . 'filter_ticketsearchkeys','filter_ticketsearchkeys','','string');
				$searchkeys['filter_ticketid'] = $mainframe->getUserStateFromRequest($option . 'filter_ticketid' , 'filter_ticketid' , '' , 'string');
				$searchkeys['filter_from'] = $mainframe->getUserStateFromRequest($option . 'filter_from' , 'filter_from' , '' , 'string');
				$searchkeys['filter_email'] = $mainframe->getUserStateFromRequest($option . 'filter_email' , 'filter_email' , '' , 'string');
				$searchkeys['filter_department'] = $mainframe->getUserStateFromRequest($option . 'filter_department' , 'filter_department' , '' , 'string');
				$searchkeys['filter_priority'] = $mainframe->getUserStateFromRequest($option . 'filter_priority' , 'filter_priority' , '' , 'string');
				$searchkeys['filter_subject'] = $mainframe->getUserStateFromRequest($option . 'filter_subject' , 'filter_subject' , '' , 'string');
				$searchkeys['filter_datestart'] = $mainframe->getUserStateFromRequest($option . 'filter_datestart' , 'filter_datestart' , '' , 'string');
				$searchkeys['filter_dateend'] = $mainframe->getUserStateFromRequest($option . 'filter_dateend' , 'filter_dateend' , '' , 'string');
				$searchkeys['filter_staffmember'] = $mainframe->getUserStateFromRequest($option . 'filter_staffmember' , 'filter_staffmember' , '' , 'string');
				$jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
				if($jsresetbutton == 1){ //if filter is reset, we need to put start,end dates explicitly null, because joomla make some problem for dates
					$mainframe->setUserState($option.'filter_dateend',null);
					$mainframe->setUserState($option.'filter_datestart',null);
					$searchkeys['filter_datestart'] = null;
					$searchkeys['filter_dateend'] = null;
				}

				$email_address = $user->getEmail();
				$email = JFactory::getApplication()->input->getString('email',$email_address);
				$session = JFactory::getApplication()->getSession();
				$session->set('email',$email);
				$listtype = JFactory::getApplication()->input->get('lt',1);
				$sortby = $this->getTicketListOrdering($sort);
				$sortlinks = $this->getTicketListSorting($sort);
				// $sortlinks['sorton'] = $sorton;
				$sortlinks['sorton'] = $sorton;
				$sortlinks['sortorder'] = $sortorder;

				$result = $this->getJSModel('ticket')->getUserMyTickets($email,$listtype,$searchkeys,$sortby,$limitstart,$limit);
				$this->filter_data = $result[4];
				//$this->username = $uname;
				$this->result = $result[0];
				$this->ticketinfo = $result[2];
            	$this->lists = $result[3];
				$this->email = $email;
				$this->lt = $listtype;
				$this->sortlinks = $sortlinks;
				$total = $result[1];
				$pagination = new JPagination($total, $limitstart, $limit );
				$this->pagination = $pagination;
			}
		}elseif($layoutName == 'ticketstatus'){
			if(!$user->getIsGuest()){
				$email = $user->getEmail();
				$this->email = $email;
			}
		}elseif($layoutName == 'visitorsuccessmessage'){
			
		}
		require_once(JPATH_COMPONENT."/views/ticket/ticket_breadcrumbs.php");
		parent::display($tpl);
	}
	function getTicketListOrdering( $sort ) {
		global $sorton, $sortorder;
		$defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting();
		switch ( $sort ) {
			case "subjectdesc": $ordering = "ticket.subject DESC"; $sorton = "subject"; $sortorder="DESC"; break;
			case "subjectasc": $ordering = "ticket.subject ASC";  $sorton = "subject"; $sortorder="ASC"; break;
			case "prioritydesc": $ordering = "priority.priority DESC"; $sorton = "priority"; $sortorder="DESC"; break;
			case "priorityasc": $ordering = "priority.priority ASC";  $sorton = "priority"; $sortorder="ASC"; break;
			case "ticketiddesc": $ordering = "ticket.ticketid DESC";  $sorton = "ticketid"; $sortorder="DESC"; break;
			case "ticketidasc": $ordering = "ticket.ticketid ASC";  $sorton = "ticketid"; $sortorder="ASC"; break;
			case "answereddesc": $ordering = "ticket.isanswered DESC";  $sorton = "answered"; $sortorder="DESC"; break;
			case "answeredasc": $ordering = "ticket.isanswered ASC";  $sorton = "answered"; $sortorder="ASC"; break;
			case "createddesc": $ordering = "ticket.created DESC";  $sorton = "created"; $sortorder="DESC"; break;
			case "createdasc": $ordering = "ticket.created ASC";  $sorton = "created"; $sortorder="ASC"; break;
			case "statusdesc": $ordering = "ticket.status DESC";  $sorton = "status"; $sortorder="DESC"; break;
			case "statusasc": $ordering = "ticket.status ASC";  $sorton = "status"; $sortorder="ASC"; break;
			default: 
				$ordering = "ticket.status ";
				$ordering .= $defaultsort;
		}
		return $ordering;
	}

	function getTicketListSorting( $sort ) {
		$sortlinks['subject'] = $this->getSortArg("subject",$sort);
		$sortlinks['priority'] = $this->getSortArg("priority",$sort);
		$sortlinks['ticketid'] = $this->getSortArg("ticketid",$sort);
		$sortlinks['answered'] = $this->getSortArg("answered",$sort);
		$sortlinks['status'] = $this->getSortArg("status",$sort);
		$sortlinks['created'] = $this->getSortArg("created",$sort);
		return $sortlinks;
	}
	function getSortArg( $type, $sort ) {
		$mat = array();
		$defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting(1);
		if ( preg_match( "/(\w+)(asc|desc)/i", $sort, $mat ) ) {
			if ( $type == $mat[1] ) {
				return ( $mat[2] == "asc" ) ? "{$type}desc" : "{$type}asc";
			} else {
				return $type . $mat[2];
			}
		}
		$sort = "id";
		$sort .= $defaultsort;
		return $sort;
	}

}
?>
