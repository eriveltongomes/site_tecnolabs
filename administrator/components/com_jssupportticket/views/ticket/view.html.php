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
jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewTicket extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        global $sorton,$sortorder;
        if($layoutName == 'tickets'){
            JToolBarHelper::addNew('addnewticket');
            $defaultsort = $this->getJSModel('ticket')->getDefaultTicketSorting(1);
            $sort =  JFactory::getApplication()->input->get('sortby','');
            if ($sort == '') {
                $sort='status';
                $sort .= $defaultsort;
            }

            $sortby = $this->getTicketListOrdering($sort);
            $sortlinks = $this->getTicketListSorting($sort);
            $sortlinks['sorton'] = $sorton;
            $sortlinks['sortorder'] = $sortorder;
            $mainframe->setUserState( $option.'.limitstart', $limitstart );
            $searchsubject = JFactory::getApplication()->input->getString('filter_subject');
            $searchfrom = JFactory::getApplication()->input->get('filter_from');
            $searchfromemail = JFactory::getApplication()->input->getString('filter_fromemail');
            $datestart = JFactory::getApplication()->input->getDate('filter_datestart');
            $dateend = JFactory::getApplication()->input->getDate('filter_dateend');
            $searchticketid = JFactory::getApplication()->input->get('filter_ticketid');
            $searchstaffmember = JFactory::getApplication()->input->get('filter_staffmember');
            $searchdepartmentid = JFactory::getApplication()->input->get('filter_department');
            $searchpriorityid = JFactory::getApplication()->input->get('filter_priority');
            $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
            if($jsresetbutton == 1){
                $datestart = null;
                $dateend = null;
            }

            $listtype = JFactory::getApplication()->input->get('lt',1);
            if($listtype == 1) $text = JText::_('Open');
            if($listtype == 2) $text = JText::_('Answered');
            if($listtype == 3) $text = JText::_('Overdue');
            if($listtype == 4) $text = JText::_('Close');
            if($listtype == 5) $text = JText::_('My Tickets');
            JToolBarHelper::title(JText::_('Tickets') . ' <small><small>[ ' . $text . ' ]</small></small>');
            $result = $this->getJSModel('ticket')->getAdminMyTickets($searchdepartmentid, $searchpriorityid, $searchstaffmember,$searchsubject,$searchfrom,$searchfromemail,$searchticketid,$listtype,$sortby, $datestart, $dateend, $limitstart,$limit);
            $total = $result[1];
            $this->result = $result[0];
            $this->lists = $result[2];
            $this->ticketinfo = $result[3];
            $this->listtype = $listtype;
            $this->sortlinks = $sortlinks;
            $this->sorton = $sorton;
            $this->sortorder = $sortorder;
            $pagination = new JPagination($total, $limitstart, $limit);
            $this->pagination = $pagination;
        }elseif($layoutName == 'ticketdetails'){
            JToolBarHelper::title(JText::_('Ticket'));
            $ticketid = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $ticketid = $ticketid[0];
            $result = $this->getJSModel('ticket')->getTicketDetailById($ticketid);
            $user = JSSupportticketCurrentUser::getInstance();
            $isstaff = $user->getIsStaff();
            $this->ticketdetail = $result[0];
            $this->isAttachmentPublished = $result['publishedInfo']->published;
            if(isset($result[1])) $this->ticketnotes = $result[1];
            if(isset($result[2])) $this->ticketreplies = $result[2];
            $this->lists = $result[3];
            if(isset($result[4])) $this->isemailban = $result[4];
            if(isset($result[6])) $this->ticketattachment = $result[6];
            if(isset($result[7])) $this->userfields = $result[7];
            if(isset($result[8])) $this->fieldsordering = $result[8];
            if(isset($result[10])) $this->time_taken = $result[10];
            $this->isstaff = $isstaff;
            if(isset($result[11])) $this->ticketemail = $result[11];
            if(isset($result[12])) $this->ntickets = $result[12];
            if(isset($result[13])) $this->usertickets = $result[13];
            if(isset($result[9])) $this->tickethistory = $result[9];
        }elseif($layoutName == 'print_ticket'){
			JToolBarHelper::title(JText::_('Print ticket'));
            $ticketid = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $print = JFactory::getApplication()->input->get('print', '');
            $ticketid = $ticketid[0];
            $result = $this->getJSModel('ticket')->getTicketDetailById($ticketid);
			$user = JSSupportticketCurrentUser::getInstance();
			$isstaff = $user->getIsStaff();
            $this->ticketdetail = $result[0];
            if(isset($result[1])) $this->ticketnotes = $result[1];
            if(isset($result[2])) $this->ticketreplies = $result[2];
            $this->lists = $result[3];
            if(isset($result[4])) $this->isemailban = $result[4];
            if(isset($result[6])) $this->ticketattachment = $result[6];
            if(isset($result[7])) $this->userfields = $result[7];
            if(isset($result[8])) $this->fieldsordering = $result[8];
            $this->isstaff = $isstaff;
            $this->print = $print;
            if(isset($result[9])) $this->tickethistory = $result[9];
        }elseif($layoutName == 'formticket'){
			JToolBarHelper::save('saveticketsave','Submit Ticket');
			JToolBarHelper::save2new('saveticketandnew');
			JToolBarHelper::save('saveticket');
			$c_id = JFactory::getApplication()->input->get('cid', array (0), 'array');
			$c_id = $c_id[0];
			$id = JFactory::getApplication()->input->get('id');
			if(isset($id))
				$c_id = $id;
            $data = $mainframe->getUserState('com_jssupportticket.data');
            $mainframe->setUserState('com_jssupportticket.data',null);
			$result = $this->getJSModel('ticket')->getFormData($c_id,$data);
			$isNew = true;
			if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
			$text = $isNew ? JText::_('Add') : JText::_('Edit');
			JToolBarHelper::title(JText::_('Ticket') . ': <small><small>[ ' . $text . ' ]</small></small>');
			if ($isNew) JToolBarHelper::cancel('cancelticket');	else JToolBarHelper::cancel('cancelticket', 'Close');

			$this->lists = $result[2];
			if(isset($result[0]))
			$this->editticket = $result[0];
            $this->data = $data;
            if(isset($result[3])) $this->userfields = $result[3];
			if(isset($result[5])) $this->attachments = $result[5];
			$this->fieldsordering = $result[4];
		}
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
