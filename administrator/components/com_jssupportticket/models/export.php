<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelExport extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }


    private function getOverallExportData(){
        $db = $this->getDBO();
        //Overall Data by status
        $result = array();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL)";
        $db->setQuery($query);
        $result['bystatus']['openticket'] = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4";
        $db->setQuery($query);
        $result['bystatus']['closeticket'] = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0";
        $db->setQuery($query);
        $result['bystatus']['answeredticket'] = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4";
        $db->setQuery($query);
        $result['bystatus']['overdueticket'] = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00')";
        $db->setQuery($query);
        $result['bystatus']['pendingticket'] = $db->loadResult();

        //Overall tickets by departments
        $query = "SELECT dept.departmentname,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE departmentid = dept.id) AS totalticket
                    FROM `#__js_ticket_departments` AS dept";
        $db->setQuery($query);
        $result['bydepartments'] = $db->loadObjectList();

        //Overall tickets by prioritys
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority";
        $db->setQuery($query);
        $result['bypriority'] = $db->loadObjectList();

        //Overall tickets by medium
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE ticketviaemail = 1";
        $db->setQuery($query);
        $result['bymedium']['ticketviaemail'] = $db->loadResult();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE ticketviaemail = 0";
        $db->setQuery($query);
        $result['bymedium']['directticket'] = $db->loadResult();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_replies` WHERE ticketviaemail = 1";
        $db->setQuery($query);
        $result['bymedium']['replyviaemail'] = $db->loadResult();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_replies` WHERE ticketviaemail = 0";
        $db->setQuery($query);
        $result['bymedium']['directreply'] = $db->loadResult();

        //Overall tickets by staffmembers
        $query = "SELECT CONCAT(staff.firstname,' ',staff.lastname) AS name ,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE staffid = staff.id) AS totalticket 
                    FROM `#__js_ticket_staff` AS staff";
        $db->setQuery($query);
        $result['bystaff'] = $db->loadObjectList();

        return $result;
    }

    function setOverallExport(){
        $tb = "\t";
        $nl = "\n";
        $result = $this->getOverallExportData();
        if(empty($result))
            return null;
        // by staus
        $data = '';
        $data = JText::_('JS Support Ticket Overall Reports').$nl.$nl;
        $data .= JText::_('Tickets By Status').$nl;
        $data .= JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        $data .= '"'.$result['bystatus']['openticket'].'"'.$tb.'"'.$result['bystatus']['answeredticket'].'"'.$tb.'"'.$result['bystatus']['closeticket'].'"'.$tb.'"'.$result['bystatus']['pendingticket'].'"'.$tb.'"'.$result['bystatus']['overdueticket'].'"'.$nl.$nl.$nl;
        // by dep
        $data .= JText::_('Tickets By Departments').$nl.$nl;
        if(!empty($result['bydepartments'])){
            foreach ($result['bydepartments'] as $key) {
                $data .= JText::_($key->departmentname).$tb;
            }
            $data .= $nl;
            foreach ($result['bydepartments'] as $key) {
                $data .= '"'.$key->totalticket.'"'.$tb;
            }
            $data .= $nl.$nl.$nl;
        }
        // by pri
        $data .= JText::_('Tickets By Priorities').$nl.$nl;
        if(!empty($result['bypriority'])){
            foreach ($result['bypriority'] as $key) {
                $data .= JText::_($key->priority).$tb;
            }
            $data .= $nl;
            foreach ($result['bypriority'] as $key) {
                $data .= '"'.$key->totalticket.'"'.$tb;
            }
            $data .= $nl.$nl.$nl;
        }
        // by channel
        $data .= JText::_('Tickets By Channel').$nl.$nl;
        $data .= JText::_('Direct').$tb.JText::_('Direct reply').$tb.JText::_('Email').$tb.JText::_('Email reply').$nl;
        $data .= '"'.$result['bymedium']['directticket'].'"'.$tb.'"'.$result['bymedium']['directreply'].'"'.$tb.'"'.$result['bymedium']['ticketviaemail'].'"'.$tb.'"'.$result['bymedium']['replyviaemail'].'"'.$nl.$nl.$nl;
        // by staff
        $data .= JText::_('Tickets By staff').$nl.$nl;
        if(!empty($result['bystaff'])){
            foreach ($result['bystaff'] as $key) {
                $data .= JText::_($key->name).$tb;
            }
            $data .= $nl;
            foreach ($result['bystaff'] as $key) {
                $data .= '"'.$key->totalticket.'"'.$tb;
            }
        }
        return $data;
    }

    private function getStaffExportData(){
        $db = $this->getDBO();
        $curdate = JFactory::getApplication()->input->get('date_start');
        $fromdate = JFactory::getApplication()->input->get('date_end');
        $uid = JFactory::getApplication()->input->get('uid');

        if( empty($curdate) OR empty($fromdate))
            return null;
        if($uid)
            if(! is_numeric($uid))
                return null;

        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        $result['uid'] = $uid;

        $staffid = $this->getJSModel('staff')->getStaffId($uid);
        $result['name'] = $this->getJSModel('staff')->getMyName($staffid);

        $tmp = $curdate;
        $curdate = $fromdate;
        $fromdate = $tmp;

         $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if ($dateformat == 'm-d-Y') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
        } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        $fromdate = JHTML::_('date',strtotime($fromdate),"Y-m-d H:i:s" );
        $curdate = JHTML::_('date',strtotime($curdate),"Y-m-d H:i:s" );

        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $result['openticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $result['closeticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $result['overdueticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4  AND isoverdue != 1 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $result['pendingticket'] = $db->loadObjectList();



        $query = "SELECT staff.photo,staff.id,staff.firstname,staff.lastname,staff.username,staff.email,user.name AS display_name,user.email AS user_email,user.username AS user_nicename,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS pendingticket,
                    (SELECT AVG(feed.rating) FROM `#__js_ticket_feedbacks` AS feed JOIN `#__js_ticket_tickets` AS ticket ON ticket.id= feed.ticketid WHERE date(ticket.created) <= " . $db->quote($curdate) . " AND date(ticket.created) >= " . $db->quote($fromdate) . " AND ticket.staffid = staff.id) AS avragerating 
                    FROM `#__js_ticket_staff` AS staff 
                    JOIN `#__users` AS user ON user.id = staff.uid";
        if($uid) $query .= ' WHERE staff.uid = '.$uid;

        $db->setQuery($query);
        $staffs = $db->loadObjectList();
        foreach ($staffs as $staff) {
            $staff->time = $this->getJSModel('staff')->getAverageTimeByStaffId($staff->id);// time 0 contains avergage time in seconds and 1 contains wheter it is conflicted or not
        }
        $result['staffs'] = $staffs;
        return $result;
    }
    
    function setStaffMemberExport(){
        $db = $this->getDBO();
        $tb = "\t";
        $nl = "\n";
        $result = $this->getStaffExportData();
        if(empty($result))
            return '';
        
        $fromdate = date('Y-m-d',strtotime($result['curdate']));
        $todate = date('Y-m-d',strtotime($result['fromdate']));
        if($result['uid']){
            $data = JText::_('Report By').' '.$result['name'].' '.JText::_('staff member').' '.JText::_('From').' '.$fromdate.'-'.JText::_('To').' '.$todate.$nl.$nl;
        }else{
            $data = JText::_('Report By Staff Members').' '.JText::_('From').' '.$fromdate.'-'.JText::_('To').' '.$todate.$nl.$nl;
        }

        // By 1 month
        $data .= JText::_('Ticket status by days').$nl.$nl;
        $data .= JText::_('Date').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        while (strtotime($fromdate) <= strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $data .= '"'.$fromdate.'"'.$tb.'"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl;
            $fromdate = date("Y-m-d", strtotime("+1 day", strtotime($fromdate)));
        }
        $data .= $nl.$nl.$nl;
        // END By 1 month
        
        // by staus
        $openticket = count($result['openticket']);
        $closeticket = count($result['closeticket']);
        $answeredticket = count($result['answeredticket']);
        $overdueticket = count($result['overdueticket']);
        $pendingticket = count($result['pendingticket']);
        $data .= JText::_('Tickets By Status').$nl;
        $data .= JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        $data .= '"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl.$nl.$nl;
        
        // by staffs
        $data .= JText::_('Tickets Staff').$nl.$nl;
        if(!empty($result['staffs'])){
            $data .= JText::_('Name').$tb.JText::_('username').$tb.JText::_('email').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$tb.JText::_('Average Rating').$tb.JText::_('Average Time').$nl;
            foreach ($result['staffs'] as $key) {
                if($key->firstname && $key->lastname){
                    $staffname = $key->firstname . ' ' . $key->lastname;
                }else{
                    $staffname = $key->display_name;
                }
                if($key->username){
                    $username = $key->username;
                }else{
                    $username = $key->user_nicename;
                }
                if($key->email){
                    $email = $key->email;
                }else{
                    $email = $key->user_email;
                }
                $hours = floor($key->time[0] / 3600);
                $mins = floor($key->time[0] / 60 % 60);
                $secs = floor($key->time[0] % 60);
                $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                $data .= '"'.$staffname.'"'.$tb.'"'.$username.'"'.$tb.'"'.$email.'"'.$tb.'"'.$key->openticket.'"'.$tb.'"'.$key->answeredticket.'"'.$tb.'"'.$key->closeticket.'"'.$tb.'"'.$key->pendingticket.'"'.$tb.'"'.$key->overdueticket.'"'.$tb.'"'. round($key->avragerating,2).'"'.$tb.'"'.$time.'"'.$nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }

    private function getStaffExportDataByStaffId(){
        $db = $this->getDBO();
        $curdate = JFactory::getApplication()->input->get('date_start');
        $fromdate = JFactory::getApplication()->input->get('date_end');
        $id = JFactory::getApplication()->input->get('uid');

        if( empty($curdate) OR empty($fromdate))
            return null;
            
        if(! is_numeric($id))
            return null;

        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        $result['id'] = $id;

        $tmp = $curdate;
        $curdate = $fromdate;
        $fromdate = $tmp;

         $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if ($dateformat == 'm-d-Y') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
        } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        $fromdate = JHTML::_('date',strtotime($fromdate),"Y-m-d H:i:s" );
        $curdate = JHTML::_('date',strtotime($curdate),"Y-m-d H:i:s" );

        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND staffid = ".$id;
        $db->setQuery($query);
        $result['openticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND staffid = ".$id;
        $db->setQuery($query);
        $result['closeticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND staffid = ".$id;
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND staffid = ".$id;
        $db->setQuery($query);
        $result['overdueticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND staffid = ".$id;
        $db->setQuery($query);
        $result['pendingticket'] = $db->loadObjectList();

        $query = "SELECT staff.photo,staff.id,staff.firstname,staff.lastname,staff.username,staff.email,user.name AS display_name,user.email AS user_email,user.username AS user_nicename,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND isoverdue != 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS pendingticket ,
                    (SELECT AVG(feed.rating) FROM `#__js_ticket_feedbacks` AS feed JOIN `#__js_ticket_tickets` AS ticket ON ticket.id= feed.ticketid WHERE date(ticket.created) <= " . $db->quote($curdate) . " AND date(ticket.created) >= " . $db->quote($fromdate) . " AND ticket.staffid = staff.id) AS avragerating 
                    FROM `#__js_ticket_staff` AS staff 
                    JOIN `#__users` AS user ON user.id = staff.uid 
                    WHERE staff.id = ".$id;
        $db->setQuery($query);
        $staff = $db->loadObject();
        $staff->time = $this->getJSModel('staff')->getAverageTimeByStaffId($staff->id);// time 0 contains avergage time in seconds and 1 contains wheter it is conflicted or not
        $result['staffs'] = $staff;
        //Tickets
        $query = "SELECT ticket.*,priority.priority, priority.prioritycolour , feedback.rating
                    FROM `#__js_ticket_tickets` AS ticket 
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid                     
                    LEFT JOIN `#__js_ticket_feedbacks` AS feedback ON feedback.ticketid = ticket.id  
                    WHERE staffid = ".$id." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        foreach ($result['tickets'] as $ticket) {
             $ticket->time =  $this->getJSModel('staff')->getTimeTakenByTicketId($ticket->id);
        }
        return $result;
    }

    function setStaffMemberExportByStaffId(){
        $db = $this->getDBO();
        $tb = "\t";
        $nl = "\n";
        $result = $this->getStaffExportDataByStaffId();
        if(empty($result))
            return '';
        
        $fromdate = date('Y-m-d',strtotime($result['curdate']));
        $todate = date('Y-m-d',strtotime($result['fromdate']));
        
        $data = JText::_('Report By staff member').' '.JText::_('From').' '.$fromdate.' - '.$todate.$nl.$nl;

        // By 1 month
        $data .= JText::_('Ticket status by days').$nl.$nl;
        $data .= JText::_('Date').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        while (strtotime($fromdate) <= strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $data .= '"'.$fromdate.'"'.$tb.'"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl;
            $fromdate = date("Y-m-d", strtotime("+1 day", strtotime($fromdate)));
        }
        $data .= $nl.$nl.$nl;
        // END By 1 month
        
        // by staffs
        $data .= JText::_('Tickets Staff').$nl.$nl;
        if(!empty($result['staffs'])){
            $data .= JText::_('Name').$tb.JText::_('username').$tb.JText::_('email').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$tb.JText::_('Average Rating').$tb.JText::_('Average Time').$nl;
            $key = $result['staffs'];
            if($key->firstname && $key->lastname){
                $staffname = $key->firstname . ' ' . $key->lastname;
            }else{
                $staffname = $key->display_name;
            }
            if($key->username){
                $username = $key->username;
            }else{
                $username = $key->user_nicename;
            }
            if($key->email){
                $email = $key->email;
            }else{
                $email = $key->user_email;
            }
            $hours = floor($key->time[0] / 3600);
            $mins = floor($key->time[0] / 60 % 60);
            $secs = floor($key->time[0] % 60);
            $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
            $data .= '"'.$staffname.'"'.$tb.'"'.$username.'"'.$tb.'"'.$email.'"'.$tb.'"'.$key->openticket.'"'.$tb.'"'.$key->answeredticket.'"'.$tb.'"'.$key->closeticket.'"'.$tb.'"'.$key->pendingticket.'"'.$tb.'"'.$key->overdueticket.'"'.$tb.'"'. round($key->avragerating,2).'"'.$tb.'"'.$time.'"'.$nl;
            
            $data .= $nl.$nl.$nl;
        }
        
        // by priorits tickets
        $data .= JText::_('Tickets').$nl.$nl;
        if(!empty($result['tickets'])){
            $data .= JText::_('Subject').$tb.JText::_('Status').$tb.JText::_('Priority').$tb.JText::_('Created').$tb.JText::_('Rating').$tb.JText::_('Time').$nl;
            $status = '';
            foreach ($result['tickets'] as $ticket) {
                switch($ticket->status){
                    case 0:
                        $status = JText::_('New');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 1:
                        $status = JText::_('Pending');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 2:
                        $status = JText::_('In Progress');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 3:
                        $status = JText::_('Answered');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 4:
                        $status = JText::_('Closed');
                    break;
                }
                $created = date('Y-m-d',strtotime($ticket->created));
                $hours = floor($ticket->time / 3600);
                $mins = floor($ticket->time / 60 % 60);
                $secs = floor($ticket->time % 60);
                $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                $data .= '"'.$ticket->subject.'"'.$tb.'"'.$status.'"'.$tb.'"'.$ticket->priority.'"'.$tb.'"'.$created.'"'.$tb.'"'.$ticket->rating.'"'.$tb.'"'.$time.'"'.$nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }

    private function getUsersExportData(){
        $db = $this->getDBO();

        $curdate = JFactory::getApplication()->input->get('date_start');
        $fromdate = JFactory::getApplication()->input->get('date_end');
        $uid = JFactory::getApplication()->input->get('uid');

        if( empty($curdate) OR empty($fromdate))
            return null;
        if($uid)
            if(! is_numeric($uid))
                return null;

        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        $result['uid'] = $uid;

        $tmp = $curdate;
        $curdate = $fromdate;
        $fromdate = $tmp;

         $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if ($dateformat == 'm-d-Y') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
        } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        $fromdate = JHTML::_('date',strtotime($fromdate),"Y-m-d H:i:s" );
        $curdate = JHTML::_('date',strtotime($curdate),"Y-m-d H:i:s" );

        $result['username'] = $this->getJSModel('staff')->getUserNameById($uid);

        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0 AND isoverdue != 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $result['openticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $result['closeticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $result['overdueticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $result['pendingticket'] = $db->loadObjectList();


        $query = "SELECT user.name AS display_name,user.email AS user_email,user.username AS user_nicename,user.id,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS pendingticket
                    FROM `#__users` AS user 
                    WHERE NOT EXISTS (SELECT id FROM `#__js_ticket_staff` WHERE uid = user.id)";
        if($uid) $query .= " AND user.id = ".$uid;
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $result['users'] = $users;
        return $result;
    }

    function setUsersExport(){
        $db = $this->getDBO();
        $tb = "\t";
        $nl = "\n";
        $result = $this->getUsersExportData();
        if(empty($result))
            return '';
        
        $fromdate = date('Y-m-d',strtotime($result['curdate']));
        $todate = date('Y-m-d',strtotime($result['fromdate']));
        if($result['uid']){
            $data = JText::_('User report').' '.$result['username'].' '.JText::_('From').' '.$fromdate.' - '.$todate.$nl.$nl;
        }else{
            $data = JText::_('Users report').' '.JText::_('From').' '.$fromdate.' - '.$todate.$nl.$nl;
        }

        // By 1 month
        $data .= JText::_('Ticket status by days').$nl.$nl;
        $data .= JText::_('Date').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        while (strtotime($fromdate) <= strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $data .= '"'.$fromdate.'"'.$tb.'"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl;
            $fromdate = date("Y-m-d", strtotime("+1 day", strtotime($fromdate)));
        }
        $data .= $nl.$nl.$nl;
        // END By 1 month
        
        // by staus
        $openticket = count($result['openticket']);
        $closeticket = count($result['closeticket']);
        $answeredticket = count($result['answeredticket']);
        $overdueticket = count($result['overdueticket']);
        $pendingticket = count($result['pendingticket']);
        $data .= JText::_('Tickets By Status').$nl;
        $data .= JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        $data .= '"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl.$nl.$nl;
        
        // by staffs
        $data .= JText::_('Users tickets').$nl.$nl;
        if(!empty($result['users'])){
            $data .= JText::_('Name').$tb.JText::_('username').$tb.JText::_('email').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
            foreach ($result['users'] as $key) {
                $name = $key->display_name;
                $username = $key->user_nicename;
                $email = $key->user_email;

                $data .= '"'.$name.'"'.$tb.'"'.$username.'"'.$tb.'"'.$email.'"'.$tb.'"'.$key->openticket.'"'.$tb.'"'.$key->answeredticket.'"'.$tb.'"'.$key->closeticket.'"'.$tb.'"'.$key->pendingticket.'"'.$tb.'"'.$key->overdueticket.'"'.$nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }

    private function getUserDetailReportByUserId(){
        $db = $this->getDBO();
        $curdate = JFactory::getApplication()->input->get('date_start');
        $fromdate = JFactory::getApplication()->input->get('date_end');
        $id = JFactory::getApplication()->input->get('uid');

        if( empty($curdate) OR empty($fromdate))
            return null;
        if(! is_numeric($id))
            return null;

        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        $result['id'] = $id;

        $tmp = $curdate;
        $curdate = $fromdate;
        $fromdate = $tmp;

         $config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if ($dateformat == 'm-d-Y') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
        } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
          $arr = explode('-', $fromdate);
          $fromdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
          $arr = explode('-', $curdate);
          $curdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }
        $fromdate = JHTML::_('date',strtotime($fromdate),"Y-m-d H:i:s" );
        $curdate = JHTML::_('date',strtotime($curdate),"Y-m-d H:i:s" );


        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['openticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['closeticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['overdueticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $result['pendingticket'] = $db->loadObjectList();



        //user detail
        $query = "SELECT user.name AS display_name,user.email AS user_email,user.username AS user_nicename,user.id,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND isoverdue != 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4  AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS pendingticket 
                    FROM `#__users` AS user 
                    WHERE user.id = ".$id;
        $db->setQuery($query);
        $user = $db->loadObject();
        $result['users'] = $user;
        //Tickets
        $query = "SELECT ticket.*,priority.priority, priority.prioritycolour , feedback.rating
                    FROM `#__js_ticket_tickets` AS ticket 
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid                     
                    LEFT JOIN `#__js_ticket_feedbacks` AS feedback ON feedback.ticketid = ticket.id  
                    WHERE uid = ".$id." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        foreach ($result['tickets'] as $ticket) {
            $ticket->time =  $this->getJSModel('staff')->getTimeTakenByTicketId($ticket->id);
        }
        return $result;
    }

    function setUserExportByuid(){
        $db = $this->getDBO();
        $tb = "\t";
        $nl = "\n";
        $result = $this->getUserDetailReportByUserId();
        if(empty($result))
            return '';
        $fromdate = date('Y-m-d',strtotime($result['curdate']));
        $todate = date('Y-m-d',strtotime($result['fromdate']));
        
        $data = JText::_('User Report').' '.JText::_('From').' '.$fromdate.' - '.$todate.$nl.$nl;
        // By 1 month
        $data .= JText::_('Ticket status by days').$nl.$nl;
        $data .= JText::_('Date').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        while (strtotime($fromdate) <= strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $data .= '"'.$fromdate.'"'.$tb.'"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl;
            $fromdate = date("Y-m-d", strtotime("+1 day", strtotime($fromdate)));
        }
        $data .= $nl.$nl.$nl;
        // END By 1 month
        
        // by staffs
        $data .= JText::_('Users Ticekts').$nl.$nl;
        if(!empty($result['users'])){
            $data .= JText::_('Name').$tb.JText::_('username').$tb.JText::_('email').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
            $key = $result['users'];
            $staffname = $key->display_name;
            $username = $key->user_nicename;
            $email = $key->user_email;
            $data .= '"'.$staffname.'"'.$tb.'"'.$username.'"'.$tb.'"'.$email.'"'.$tb.'"'.$key->openticket.'"'.$tb.'"'.$key->answeredticket.'"'.$tb.'"'.$key->closeticket.'"'.$tb.'"'.$key->pendingticket.'"'.$tb.'"'.$key->overdueticket.'"'.$nl;
        
            $data .= $nl.$nl.$nl;
        }
        
        // by priorits tickets
        $data .= JText::_('Tickets').$nl.$nl;
        if(!empty($result['tickets'])){
            $data .= JText::_('Subject').$tb.JText::_('Status').$tb.JText::_('Priority').$tb.JText::_('Created').$tb.JText::_('Rating').$tb.JText::_('Time').$nl;
            $status = '';
            foreach ($result['tickets'] as $ticket) {
                switch($ticket->status){
                    case 0:
                        $status = JText::_('New');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 1:
                        $status = JText::_('Pending');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 2:
                        $status = JText::_('In Progress');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 3:
                        $status = JText::_('Answered');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 4:
                        $status = JText::_('Closed');
                    break;
                }
                $created = date('Y-m-d',strtotime($ticket->created));
                $hours = floor($ticket->time / 3600);
                $mins = floor($ticket->time / 60 % 60);
                $secs = floor($ticket->time % 60);
                $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                $data .= '"'.$ticket->subject.'"'.$tb.'"'.$status.'"'.$tb.'"'.$ticket->priority.'"'.$tb.'"'.$created.'"'.$tb.'"'.$ticket->rating.'"'.$tb.'"'.$time.'"'.$nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }

    // new exports

    private function getDepartmentExportDataByDepartmentId(){
        $db = $this->getDBO();
        $curdate = JFactory::getApplication()->input->get('date_start');
        $fromdate = JFactory::getApplication()->input->get('date_end');
        $id = JFactory::getApplication()->input->get('id');

        if( empty($curdate) OR empty($fromdate))
            return null;
            
        if(! is_numeric($id))
            return null;

        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        $result['id'] = $id;

        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0 AND (lastreply = '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $result['openticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $result['closeticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $result['overdueticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $result['pendingticket'] = $db->loadObjectList();


        $query = "SELECT department.id,department.departmentname,email.email,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND lastreply = '0000-00-00 00:00:00' AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS pendingticket 
                    FROM `#__js_ticket_departments` AS department 
                    LEFT JOIN `#__js_ticket_email` AS email ON department.emailid = email.id
                    WHERE department.id = ".$id;
        $db->setQuery($query);
        $depatments = $db->loadObject();
        $result['depatments'] =$depatments;

        //Tickets
        $query = "SELECT ticket.*,priority.priority, priority.prioritycolour , feedback.rating
                    FROM `#__js_ticket_tickets` AS ticket 
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid                     
                    LEFT JOIN `#__js_ticket_feedbacks` AS feedback ON feedback.ticketid = ticket.id                     
                    WHERE departmentid = ".$id." AND date(ticket.created) >= " . $db->quote($curdate) . " AND date(ticket.created) <= " . $db->quote($fromdate) . " ";
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        foreach ($result['tickets'] as $ticket) {
             $ticket->time =  $this->getJSModel('staff')->getTimeTakenByTicketId($ticket->id);
        }
    return $result;
    }

    function setDepartmentExportByDepartmentId(){
        $db = $this->getDBO();
        $tb = "\t";
        $nl = "\n";
        $result = $this->getDepartmentExportDataByDepartmentId();
        if(empty($result))
            return '';
        
        $fromdate = date('Y-m-d',strtotime($result['curdate']));
        $todate = date('Y-m-d',strtotime($result['fromdate']));
        
        $data = JText::_('Report By department').' '.JText::_('From').' '.$fromdate.' - '.$todate.$nl.$nl;

        // By 1 month
        $data .= JText::_('Ticket status by days').$nl.$nl;
        $data .= JText::_('Date').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        while (strtotime($fromdate) <= strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $data .= '"'.$fromdate.'"'.$tb.'"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl;
            $fromdate = date("Y-m-d", strtotime("+1 day", strtotime($fromdate)));
        }
        $data .= $nl.$nl.$nl;
        // END By 1 month
        
        // by departments
        $data .= JText::_('Tickets By Department').$nl.$nl;
        if(!empty($result['departments'])){
            $data .= JText::_('Department Name').$tb.JText::_('email').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
            $key = $result['departments'];
            $departmentname = $key->departmentname;
            $email = $key->email;
            $data .= '"'.$departmentname.'"'.$tb.'"'.$email.'"'.$tb.'"'.$key->openticket.'"'.$tb.'"'.$key->answeredticket.'"'.$tb.'"'.$key->closeticket.'"'.$tb.'"'.$key->pendingticket.'"'.$tb.'"'.$key->overdueticket.'"'.$nl;
        
            $data .= $nl.$nl.$nl;
        }
        
        // by priorits tickets
        $data .= JText::_('Tickets').$nl.$nl;
        if(!empty($result['tickets'])){
            $data .= JText::_('Subject').$tb.JText::_('Status').$tb.JText::_('Priority').$tb.JText::_('Created').$tb.JText::_('Rating').$tb.JText::_('Time').$nl;
            $status = '';
            foreach ($result['tickets'] as $ticket) {
                $hours = floor($ticket->time / 3600);
                $mins = floor($ticket->time / 60 % 60);
                $secs = floor($ticket->time % 60);
                $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                switch($ticket->status){
                    case 0:
                        $status = JText::_('New');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 1:
                        $status = JText::_('Pending');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 2:
                        $status = JText::_('In Progress');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 3:
                        $status = JText::_('Answered');
                        if($ticket->isoverdue == 1)
                            $status = JText::_('Overdue');
                    break;
                    case 4:
                        $status = JText::_('Closed');
                    break;
                }
                $created = date('Y-m-d',strtotime($ticket->created));
                $hours = floor($ticket->time / 3600);
                $mins = floor($ticket->time / 60 % 60);
                $secs = floor($ticket->time % 60);
                $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                $data .= '"'.$ticket->subject.'"'.$tb.'"'.$status.'"'.$tb.'"'.$ticket->priority.'"'.$tb.'"'.$created.'"'.$tb.'"'.$ticket->rating.'"'.$tb.'"'.$time.'"'.$nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }

    private function getDepartmentExportData(){
        $db = $this->getDBO();
        $curdate = JFactory::getApplication()->input->get('date_start');
        $fromdate = JFactory::getApplication()->input->get('date_end');
        
        if( empty($curdate) OR empty($fromdate))
            return null;
        
        $result['curdate'] = $curdate;
        $result['fromdate'] = $fromdate;
        
        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $result['openticket'] = $db->loadObjectList();
        
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        $db->setQuery($query);
        $result['closeticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        $db->setQuery($query);
        $result['overdueticket'] = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00' OR lastreply IS NOT NULL) AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate);
        $db->setQuery($query);
        $result['pendingticket'] = $db->loadObjectList();

        $query = "SELECT department.id,department.departmentname,email.email,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($curdate) . " AND date(created) <= " . $db->quote($fromdate) . " AND departmentid = department.id) AS pendingticket 
                    FROM `#__js_ticket_departments` AS department 
                    LEFT JOIN `#__js_ticket_email` AS email ON department.emailid = email.id";
        $db->setQuery($query);
        $departments = $db->loadObjectList();
        $result['departments'] = $departments;
        return $result;
    }
    
    function setDepartmentExport(){
        $db = $this->getDBO();
        $tb = "\t";
        $nl = "\n";
        $result = $this->getDepartmentExportData();
        if(empty($result))
            return '';
        
        $fromdate = date('Y-m-d',strtotime($result['curdate']));
        $todate = date('Y-m-d',strtotime($result['fromdate']));
        $data = JText::_('Report By Departments').' '.JText::_('From').' '.$fromdate.'-'.JText::_('To').' '.$todate.$nl.$nl;
        
        // By 1 month
        $data .= JText::_('Ticket status by days').$nl.$nl;
        $data .= JText::_('Date').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        while (strtotime($fromdate) <= strtotime($todate)) {
            $openticket = 0;
            $closeticket = 0;
            $answeredticket = 0;
            $overdueticket = 0;
            $pendingticket = 0;
            foreach ($result['openticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $openticket += 1;
            }
            foreach ($result['closeticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $closeticket += 1;
            }
            foreach ($result['answeredticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $answeredticket += 1;
            }
            foreach ($result['overdueticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $overdueticket += 1;
            }
            foreach ($result['pendingticket'] as $ticket) {
                $ticket_date = date('Y-m-d', strtotime($ticket->created));
                if($ticket_date == $fromdate)
                    $pendingticket += 1;
            }
            $data .= '"'.$fromdate.'"'.$tb.'"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl;
            $fromdate = date("Y-m-d", strtotime("+1 day", strtotime($fromdate)));
        }
        $data .= $nl.$nl.$nl;
        // END By 1 month
        
        // by staus
        $openticket = count($result['openticket']);
        $closeticket = count($result['closeticket']);
        $answeredticket = count($result['answeredticket']);
        $overdueticket = count($result['overdueticket']);
        $pendingticket = count($result['pendingticket']);
        $data .= JText::_('Tickets By Status').$nl;
        $data .= JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
        $data .= '"'.$openticket.'"'.$tb.'"'.$answeredticket.'"'.$tb.'"'.$closeticket.'"'.$tb.'"'.$pendingticket.'"'.$tb.'"'.$overdueticket.'"'.$nl.$nl.$nl;
        
        // by departments
        $data .= JText::_('Tickets By Departments').$nl.$nl;
        if(!empty($result['departments'])){
            $data .= JText::_('Department Name').$tb.JText::_('email').$tb.JText::_('NEW').$tb.JText::_('Answered').$tb.JText::_('Closed').$tb.JText::_('Pending').$tb.JText::_('Overdue').$nl;
            foreach ($result['departments'] as $key) {
                $departmentname = $key->departmentname;
                $email = $key->email;
                $data .= '"'.$departmentname.'"'.$tb.'"'.$email.'"'.$tb.'"'.$key->openticket.'"'.$tb.'"'.$key->answeredticket.'"'.$tb.'"'.$key->closeticket.'"'.$tb.'"'.$key->pendingticket.'"'.$tb.'"'.$key->overdueticket.'"'.$nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }

     private function getTicketsDataForExport(){
        $db = $this->getDBO();
        $data = JFactory::getApplication()->input->post->getArray();
        $wherequery = '';
        if(!empty($data)){
            if(isset($data['startdate']) && $data['startdate'] != '' ){
                $wherequery .= ' AND ticket.created >= '.$db->quote(date("Y-m-d",strtotime($data['startdate']))).' ';
            }
            if(isset($data['enddate']) && $data['enddate'] != '' ){
                $wherequery .= ' AND ticket.created <= '.$db->quote(date("Y-m-d",strtotime($data['enddate']))).' ';
            }
            if(isset($data['departmentid']) && $data['departmentid'] != '' ){
                $wherequery .= ' AND ticket.departmentid = '.$data['departmentid'];
            }
            if(isset($data['staffid']) && $data['staffid'] != '' ){
                $wherequery .= ' AND ticket.staffid = '.$data['staffid'];
            }
            if(isset($data['priorityid']) && $data['priorityid'] != '' ){
                $wherequery .= ' AND ticket.priorityid = '.$data['priorityid'];
            }
            if(isset($data['uid']) && $data['uid'] != '' ){
                $wherequery .= ' AND ticket.uid = '.$data['uid'];
            }
            if(isset($data['ticketstatus']) && $data['ticketstatus'] != '' ){
                $wherequery .= ' AND ticket.status = '.$data['ticketstatus'];
            }
            if(isset($data['isoverdue']) && $data['isoverdue'] != '' ){
                if($data['isoverdue'] == 1){
                    $wherequery .= ' AND ticket.isoverdue = '.$data['isoverdue'];
                }else{
                    $wherequery .= ' AND ticket.isoverdue <> 1';
                }
            }

        }
        
        //Tickets
         $query = "SELECT ticket.*,department.departmentname AS departmentname ,priority.priority AS priority,priority.prioritycolour AS prioritycolour,user.name AS display_name,user.email AS user_email,user.username AS user_nicename,
                    helptopic.topic AS helptopic ,CONCAT(staff.firstname ,'  ' ,staff.lastname) AS staffname, staff.id AS staffid, staff.photo AS staffphoto, staffphoto.photo AS staffphotophoto,staffphoto.id AS staffphotoid,feedback.rating
                    FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                    LEFT JOIN `#__js_ticket_help_topics` AS helptopic ON ticket.helptopicid = helptopic.id
                    LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
                    LEFT JOIN `#__js_ticket_staff` AS staffphoto ON ticket.uid = staffphoto.uid
                    LEFT JOIN `#__users` AS user ON user.ID = ticket.uid
                    LEFT JOIN `#__js_ticket_feedbacks` AS feedback ON feedback.ticketid = ticket.id
                    WHERE 1 = 1 ";
        $query .= $wherequery;
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        $attachmentmodel = $this->getJSModel('attachments');
        foreach ($result['tickets'] as $ticket) {
            $query = "SELECT note.*,ticket.staffid AS staffid,CONCAT(staff.firstname,' ',staff.lastname) AS staffname,staff.photo AS staffphoto,staff.id AS staffid,time.usertime,time.systemtime,time.description
                FROM `#__js_ticket_notes` AS note
                LEFT JOIN `#__js_ticket_tickets` AS ticket ON note.ticketid = ticket.id
                LEFT JOIN `#__js_ticket_staff` AS staff ON note.staffid = staff.id
                LEFT JOIN `#__js_ticket_staff_time` AS time ON time.referenceid = note.id AND time.referencefor = 2
                WHERE note.ticketid=" . $ticket->id . " ORDER BY note.created DESC ";
            $db->setQuery($query);
            $ticket->notes = $db->loadObjectList();

            $query = "SELECT replies.*,  staff.appendsignature AS appendsignature, staff.signature AS signature,
                CONCAT(staff.firstname,' ',staff.lastname) AS staffname,staff.photo AS staffphoto,staff.id AS staffid, time.systemtime,time.usertime AS time,time.description
                FROM`#__js_ticket_replies` AS replies
                LEFT JOIN `#__js_ticket_staff` AS staff ON replies.staffid = staff.id
                LEFT JOIN `#__js_ticket_staff_time` AS time ON time.referenceid = replies.id AND time.referencefor = 1
                WHERE replies.ticketid = " . $ticket->id . " ORDER BY replies.created ASC";
            $db->setQuery($query);
            $replies = $db->loadObjectList();
            foreach ($replies AS $reply) {
                $reply->attachments = $attachmentmodel->getAttachmentForReply($ticket->id, $reply->id);
            }
            $ticket->replies = $replies;
            $query = "SELECT * FROM `#__js_ticket_attachments` WHERE ticketid = ".$ticket->id ." AND replyattachmentid = 0 ";
            $db->setQuery($query);
            $attachments = $db->loadObjectList();
            $ticket->attachments =  $attachments;
        }
        return $result;
    }

    function setTicketsExport(){
        $db = $this->getDBO();
        $tb = "\t";
        $nl = "\n";
        $result = $this->getTicketsDataForExport();
        if(empty($result))
            return '';
        $data = JText::_('Tickets Data').$nl.$nl;
  
        // by priorits tickets
        $data .= JText::_('Tickets').$nl.$nl;
        if(!empty($result['tickets'])){
            $status = '';
            $customfields = getCustomFieldClass()->userFieldsData(1);// custom fields
            // attachment path
            $config = $this->getJSModel('config')->getConfigs();  
            $datadirectory = $config['data_directory'];
            $path = JUri::root();
            $path = $path .'/'.$datadirectory;
            $path = $path . '/attachmentdata';
            foreach ($result['tickets'] as $ticket) {
                // attachment directory
                $folder = $path . '/ticket/' . $ticket->attachmentdir;
                // custom fields for individaul tickets
            $data .= 
            JText::_('Subject').$tb.
            JText::_('Message').$tb.
            JText::_('Status').$tb.
            JText::_('Overdue').$tb.
            JText::_('Priority').$tb.
            JText::_('Ticket Id').$tb.
            JText::_('Department').$tb.
            JText::_('Assigned To').$tb.
            JText::_('Rating').$tb.
            JText::_('Created').$tb.
            JText::_('Last Reply').$tb.
            JText::_('Requester Name').$tb.
            JText::_('Requester Email').$tb.
            JText::_('Requester Phone').$tb.
            JText::_('Requester help Topic').$tb;
            foreach ($customfields as $field) {// custom fields
                $array = getCustomFieldClass()->showCustomFields($field,5, $ticket->params,false);
                $data .= JText::_($array['title']).$tb;
            }
            foreach ($ticket->attachments AS $attachment) {// attachments
                $data .= JText::_('Ticket Attachment').$tb;
            }
            foreach ($ticket->notes AS $note) {// Internal notes
                $data .= JText::_('Posted By').$tb;
                $data .= JText::_('Note Title').$tb;
                $data .= JText::_('Note Message').$tb;
                $data .= JText::_('Posted Date').$tb;
                $data .= JText::_('Note Attachment').$tb;
                $data .= JText::_('User Time').$tb;
                $data .= JText::_('System Time').$tb;
                $data .= JText::_('Edit reason').$tb;
            }
            foreach ($ticket->replies AS $reply) {// ticket Replies
                $data .= JText::_('Reply Date').$tb;
                $data .= JText::_('Reply By').$tb;
                $data .= JText::_('Message').$tb;
                foreach ($reply->attachments AS $attachment) {
                    $data .= JText::_('Reply Attachment').$tb;
                }
                if($reply->staffid !=0){
                    $data .= JText::_('User Time').$tb;
                    $data .= JText::_('System Time').$tb;
                    $data .= JText::_('Edit Reason').$tb;
                }
            }
            $data .= $nl;
                $overdue = ' ';
                $status = ' ';
                switch($ticket->status){
                    case 0:
                        $status = JText::_('New');
                        if($ticket->isoverdue == 1)
                            $overdue = JText::_('Overdue');
                    break;
                    case 1:
                        $status = JText::_('Pending');
                        if($ticket->isoverdue == 1)
                            $overdue = JText::_('Overdue');
                    break;
                    case 2:
                        $status = JText::_('In Progress');
                        if($ticket->isoverdue == 1)
                            $overdue = JText::_('Overdue');
                    break;
                    case 3:
                        $status = JText::_('Answered');
                        if($ticket->isoverdue == 1)
                            $overdue = JText::_('Overdue');
                    break;
                    case 4:
                        $status = JText::_('Closed');
                    break;
                }
                $created = date('Y-m-d',strtotime($ticket->created));
                $lastreply = date('Y-m-d',strtotime($ticket->lastreply));
                $data .= '"'.
                $ticket->subject.'"'.$tb.'"'.
                $ticket->message.'"'.$tb.'"'.
                $status.'"'.$tb.'"'.
                $overdue.'"'.$tb.'"'.
                $ticket->priority.'"'.$tb.'"'.
                $ticket->ticketid.'"'.$tb.'"'.
                $ticket->departmentname.'"'.$tb.'"'.
                $ticket->staffname.'"'.$tb.'"'.
                $ticket->rating.'"'.$tb.'"'.
                $created.'"'.$tb.'"'.
                $lastreply.'"'.$tb.'"'.
                $ticket->name.'"'.$tb.'"'.
                $ticket->email.'"'.$tb.'"'.
                $ticket->phone.'"'.$tb.'"'.
                $ticket->helptopic.'"'.$tb.'"';
                foreach ($customfields as $field) {
                    $array = getCustomFieldClass()->showCustomFields($field,5, $ticket->params,false);
                    $data .= JText::_($array['value']).'"'.$tb.'"';
                }
                foreach ($ticket->attachments AS $attachment) {
                    $data .= $folder.'/'.$attachment->filename .'"'.$tb.'"';
                }
                foreach ($ticket->notes AS $note) {// Internal notes
                    $data .= !empty($note->staffname) ? $note->staffname.'"'.$tb.'"' : 'staff member' .'"'.$tb.'"';
                    $data .= $note->title.'"'.$tb.'"';
                    $data .= $note->note.'"'.$tb.'"';
                    $data .= date("l F d, Y, h:i:s", strtotime($note->created)).'"'.$tb.'"';
                    $data .= $folder.'/'.$note->filename.'"'.$tb.'"';
                    $hours = floor($note->usertime / 3600);
                    $mins = floor($note->usertime / 60 % 60);
                    $secs = floor($note->usertime % 60);
                    $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    $data .= $time.'"'.$tb.'"';
                    $hours = floor($note->systemtime / 3600);
                    $mins = floor($note->systemtime / 60 % 60);
                    $secs = floor($note->systemtime % 60);
                    $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                    $data .= $time.'"'.$tb.'"';
                    $data .= $note->description.'"'.$tb.'"';
                }
                foreach ($ticket->replies AS $reply) {// ticket Replies
                    $data .= date("l F d, Y, h:i:s", strtotime($reply->created)).'"'.$tb.'"';
                    $data .= $reply->name.'"'.$tb.'"';
                    $data .= $reply->message.'"'.$tb.'"';
                    foreach ($reply->attachments AS $attachment) {
                        $data .= $folder.'/'.$attachment->filename.'"'.$tb.'"';
                    }
                    if($reply->staffid !=0){
                        $hours = floor($reply->time / 3600);
                        $mins = floor($reply->time / 60 % 60);
                        $secs = floor($reply->time % 60);
                        $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                        $data .= $time.'"'.$tb.'"';
                        $hours = floor($reply->systemtime / 3600);
                        $mins = floor($reply->systemtime / 60 % 60);
                        $secs = floor($reply->systemtime % 60);
                        $time = sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                        $data .= $time.'"'.$tb.'"';
                        $data .= $reply->description.'"'.$tb.'"';
                    }
                }
            $data .= '"'.$nl;
            $data .= $nl;
            }
            $data .= $nl.$nl.$nl;
        }
        return $data;
    }
}
?>
