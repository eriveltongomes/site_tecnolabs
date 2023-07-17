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

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelReports extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getOverallReportData(){
        $db = JFactory::getDbo();
        $result = array();

        //Overall Data by status
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL)";
        $db->setQuery($query);
        $openticket = $db->loadResult();
        $result['openticket'] = $openticket;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4";
        $db->setQuery($query);
        $closeticket = $db->loadResult();
        $result['closeticket'] = $closeticket;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 ";
        $db->setQuery($query);
        $answeredticket = $db->loadResult();
        $result['answeredticket'] = $answeredticket;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4";
        $db->setQuery($query);
        $overdueticket = $db->loadResult();
        $result['overdueticket'] = $overdueticket;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets`";
        $db->setQuery($query);
        $alltickets = $db->loadResult();
        $result['alltickets'] = $alltickets;

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND lastreply != '0000-00-00 00:00:00'";
        $db->setQuery($query);
        $pendingticket = $db->loadResult();

        $result['status_chart'] = "['".JText::_('New')."',$openticket],['".JText::_('Answered')."',$answeredticket],['".JText::_('Overdue')."',$overdueticket],['".JText::_('Pending')."',$pendingticket]";
        $total = $openticket + $closeticket + $answeredticket + $overdueticket + $pendingticket;
        $result['bar_chart'] = "
        ['".JText::_('New')."',$openticket,'#FF9900'],
        ['".JText::_('Answered')."',$answeredticket,'#179650'],
        ['".JText::_('Closed')."',$closeticket,'#5F3BBB'],
        ['".JText::_('Pending')."',$pendingticket,'#D98E11'],
        ['".JText::_('Overdue')."',$overdueticket,'#DB624C']        
        ";

        $query = "SELECT dept.departmentname,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE departmentid = dept.id) AS totalticket
                    FROM `#__js_ticket_departments` AS dept";
        $db->setQuery($query);
        $department = $db->loadObjectList();
        $result['pie3d_chart1'] = "";
        foreach($department AS $dept){
            $result['pie3d_chart1'] .= "['$dept->departmentname',$dept->totalticket],";
        }
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $department = $db->loadObjectList();
        $result['pie3d_chart2'] = "";
        foreach($department AS $dept){
            $result['pie3d_chart2'] .= "['".JText::_($dept->priority)."',$dept->totalticket],";
        }
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE ticketviaemail = 1";
        $db->setQuery($query);
        $ticketviaemail = $db->loadResult();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE ticketviaemail = 0";
        $db->setQuery($query);
        $directticket = $db->loadResult();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_replies` WHERE ticketviaemail = 1";
        $db->setQuery($query);
        $replyviaemail = $db->loadResult();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_replies` WHERE ticketviaemail = 0";
        $db->setQuery($query);
        $directreply = $db->loadResult();

        $result['stack_data'] = "['".JText::_('Tickets')."',$directticket,$ticketviaemail,''],['".JText::_('Replies')."',$directreply,$replyviaemail,'']";
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $openticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 4 AND status != 0 ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $answeredticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isoverdue = 1 AND status != 4 ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $overdueticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered != 1 AND status != 4  AND isoverdue != 1 AND (lastreply != '0000-00-00 00:00:00') ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $pendingticket_pr = $db->loadObjectList();
        $result['stack_chart_horizontal']['title'] = "['".JText::_('Tickets')."',";
        $result['stack_chart_horizontal']['data'] = "['".JText::_('Overdue')."',";
        foreach($overdueticket_pr AS $pr){
            $result['stack_chart_horizontal']['title'] .= "'".JText::_($pr->priority)."',";
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }
        $result['stack_chart_horizontal']['title'] .= "]";
        $result['stack_chart_horizontal']['data'] .= "],['".JText::_('Pending')."',";

        foreach($pendingticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".JText::_('Answered')."',";

        foreach($answeredticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".JText::_('New')."',";

        foreach($openticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }
        
        $result['stack_chart_horizontal']['data'] .= "]";

        $query = "SELECT staff.firstname,staff.lastname,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE staffid = staff.id) AS totalticket 
                    FROM `#__js_ticket_staff` AS staff";
        $db->setQuery($query);
        $stafftickets = $db->loadObjectList();
        $result['slice_chart'] = '';
        if(!empty($stafftickets))
        foreach($stafftickets AS $ticket){
            $staffname = $ticket->firstname;
            if(!empty($ticket->lastname)){
                $staffname .= ' '.$ticket->lastname;
            }
            $result['slice_chart'] .= "['".$staffname."',$ticket->totalticket],";
        }

        //To show priority colors on chart
        $jsonColorList = "[";
        $query = "SELECT prioritycolour FROM `#__js_ticket_priorities` ORDER BY priority ";
        $db->setQuery($query);
        foreach($db->loadObjectList() as $priority){
            $jsonColorList.= "'".$priority->prioritycolour."',";
        }
        $jsonColorList .= "]";
        $result['priorityColorList'] = $jsonColorList;
        //end priority colors

        return $result;
    }

    function getStaffReports(){
        $db = JFactory::getDbo();
        $result = array();
        $date_start = JFactory::getApplication()->input->get('date_start');
        $date_end = JFactory::getApplication()->input->get('date_end');
        $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
        if($jsresetbutton == 1){
            $date_start = null;
            $date_end = null;
        }

        if( $date_start != '' && $date_end != '' ){
            $config = $this->getJSModel('config')->getConfigs();
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
            $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );

            if($date_start > $date_end){
                $tmp = $date_start;
                $date_start = $date_end;
                $date_end = $tmp;
            }
        }
        $uid = JFactory::getApplication()->input->get('uid');

        $formsearch = JFactory::getApplication()->input->get('option', 'post');
        if ($formsearch == 'com_jssupportticket') {
            $_SESSION['JSST_SEARCH']['date_start'] = $date_start;
            $_SESSION['JSST_SEARCH']['date_end'] = $date_end;
            $_SESSION['JSST_SEARCH']['uid'] = $uid;
        }

        if (JFactory::getApplication()->input->get('pagenum', 'get', null) != null) {
            $date_start = (isset($_SESSION['JSST_SEARCH']['date_start']) && $_SESSION['JSST_SEARCH']['date_start'] != '') ? $_SESSION['JSST_SEARCH']['date_start'] : null;
            $date_end = (isset($_SESSION['JSST_SEARCH']['date_end']) && $_SESSION['JSST_SEARCH']['date_end'] != '') ? $_SESSION['JSST_SEARCH']['date_end'] : null;
            $uid = (isset($_SESSION['JSST_SEARCH']['uid']) && $_SESSION['JSST_SEARCH']['uid'] != '') ? $_SESSION['JSST_SEARCH']['uid'] : null;
        }
        //Line Chart Data
        $curdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        $dates = '';
        $fromdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
        $result['filter']['date_start'] = $fromdate;
        $result['filter']['date_end'] = $curdate;
        $result['filter']['uid'] = $uid;
        $staffid = $this->getJSModel('staff')->getStaffId($uid);
        $result['filter']['staffname'] = $this->getJSModel('staff')->getMyName($staffid);
        $nextdate = $curdate;
        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $openticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $closeticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $answeredticket = $db->loadObjectList();

        $query = "SELECT count(id) FROM `#__js_ticket_tickets` WHERE  date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $totalticket = $db->loadResult();
    


        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $overdueticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4  AND isoverdue != 1 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$staffid;
        $db->setQuery($query);
        $pendingticket = $db->loadObjectList();

        $date_openticket = array();
        $date_closeticket = array();
        $date_answeredticket = array();
        $date_overdueticket = array();
        $date_pendingticket = array();
        foreach ($openticket AS $ticket) {
            if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($closeticket AS $ticket) {
            if (!isset($date_closeticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = $date_closeticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($answeredticket AS $ticket) {
            if (!isset($date_answeredticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($overdueticket AS $ticket) {
            if (!isset($date_overdueticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($pendingticket AS $ticket) {
            if (!isset($date_pendingticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        $open_ticket = 0;
        $close_ticket = 0;
        $answered_ticket = 0;
        $overdue_ticket = 0;
        $pending_ticket = 0;
        $json_array = "";
        do{
            $year = date('Y',strtotime($nextdate));
            $month = date('m',strtotime($nextdate));
            $month = $month - 1; //js month are 0 based
            $day = date('d',strtotime($nextdate));
            $openticket_tmp = isset($date_openticket[$nextdate]) ? $date_openticket[$nextdate]  : 0;
            $closeticket_tmp = isset($date_closeticket[$nextdate]) ? $date_closeticket[$nextdate] : 0;
            $answeredticket_tmp = isset($date_answeredticket[$nextdate]) ? $date_answeredticket[$nextdate] : 0;
            $overdueticket_tmp = isset($date_overdueticket[$nextdate]) ? $date_overdueticket[$nextdate] : 0;
            $pendingticket_tmp = isset($date_pendingticket[$nextdate]) ? $date_pendingticket[$nextdate] : 0;
            $json_array .= "[new Date($year,$month,$day),$openticket_tmp,$answeredticket_tmp,$pendingticket_tmp,$overdueticket_tmp,$closeticket_tmp],";
            $open_ticket += $openticket_tmp;
            $close_ticket += $closeticket_tmp;
            $answered_ticket += $answeredticket_tmp;
            $overdue_ticket += $overdueticket_tmp;
            $pending_ticket += $pendingticket_tmp;
            if($nextdate == $fromdate){
                break;
            }
            $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
        }while($nextdate != $fromdate);

        $result['ticket_total']['openticket'] = $open_ticket;
        $result['ticket_total']['closeticket'] = $close_ticket;
        $result['ticket_total']['answeredticket'] = $answered_ticket;
        $result['ticket_total']['overdueticket'] = $overdue_ticket;
        $result['ticket_total']['pendingticket'] = $pending_ticket;
        $result['ticket_total']['totalticket'] = $totalticket;

        $result['line_chart_json_array'] = $json_array;

        // Pagination
        $query = "SELECT count(staff.id)
                    FROM `#__js_ticket_staff` AS staff 
                    JOIN `#__users` AS user ON user.id = staff.uid";
        if($uid) $query .= ' WHERE staff.uid = '.$uid;
        $db->setQuery($query);
        $total = $db->loadResult();
        $result['total'] = $total;

        $query = "SELECT staff.photo,staff.id,staff.firstname,staff.lastname,staff.username,staff.email,user.name AS display_name,user.email,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND staffid = staff.id) AS pendingticket ,
                    (SELECT AVG(feed.rating) FROM `#__js_ticket_feedbacks` AS feed JOIN `#__js_ticket_tickets` AS ticket ON ticket.id= feed.ticketid WHERE date(ticket.created) <= " . $db->quote($curdate) . " AND date(ticket.created) >= " . $db->quote($fromdate) . " AND ticket.staffid = staff.id) AS avragerating 
                    FROM `#__js_ticket_staff` AS staff 
                    JOIN `#__users` AS user ON user.id = staff.uid";
        if($uid) $query .= ' WHERE staff.uid = '.$uid;
        $db->setQuery($query);
        $staffs = $db->loadObjectList();
        foreach ($staffs as $staff) {
            $staff->time = $this->getJSModel('staff')->getAverageTimeByStaffId($staff->id);// time 0 contains avergage time in seconds and 1 contains wheter it is conflicted or not
        }
        $result['staffs_report'] =$staffs;
        return $result;        
    }

    function getUserReports(){
        $db = JFactory::getDbo();
        $result = array();
        $date_start = JFactory::getApplication()->input->getDate('date_start');
        $date_end = JFactory::getApplication()->input->getDate('date_end');
        $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
        if($jsresetbutton == 1){
            $date_start = null;
            $date_end = null;
        }
        
        if( $date_start != '' && $date_end != '' ){
              $config = $this->getJSModel('config')->getConfigs();
              $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
            $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );

            if($date_start > $date_end){
                $tmp = $date_start;
                $date_start = $date_end;
                $date_end = $tmp;
            }
        }
        $uid = JFactory::getApplication()->input->get('uid');
        $formsearch = JFactory::getApplication()->input->get('option', 'post');
        if ($formsearch == 'com_jssupportticket') {
            $_SESSION['JSST_SEARCH']['date_start'] = $date_start;
            $_SESSION['JSST_SEARCH']['date_end'] = $date_end;
            $_SESSION['JSST_SEARCH']['uid'] = $uid;
        }

        if (JFactory::getApplication()->input->get('pagenum', 'get', null) != null) {
            $date_start = (isset($_SESSION['JSST_SEARCH']['date_start']) && $_SESSION['JSST_SEARCH']['date_start'] != '') ? $_SESSION['JSST_SEARCH']['date_start'] : null;
            $date_end = (isset($_SESSION['JSST_SEARCH']['date_end']) && $_SESSION['JSST_SEARCH']['date_end'] != '') ? $_SESSION['JSST_SEARCH']['date_end'] : null;
            $uid = (isset($_SESSION['JSST_SEARCH']['uid']) && $_SESSION['JSST_SEARCH']['uid'] != '') ? $_SESSION['JSST_SEARCH']['uid'] : null;
        }
        //Line Chart Data
        $curdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        $dates = '';
        $fromdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
        $result['filter']['date_start'] = $fromdate;
        $result['filter']['date_end'] = $curdate;
        $result['filter']['uid'] = $uid;
        $result['filter']['username'] = $this->getJSModel('staff')->getUserNameById($uid);
        $nextdate = $curdate;
        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0 AND isoverdue != 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $openticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $closeticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $answeredticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $overdueticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE  date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND staffid = ".$uid;
        $db->setQuery($query);
        $totalticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND lastreply != '0000-00-00 00:00:00' AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($uid) $query .= " AND uid = ".$uid;
        $db->setQuery($query);
        $pendingticket = $db->loadObjectList();

        $date_openticket = array();
        $date_closeticket = array();
        $date_answeredticket = array();
        $date_overdueticket = array();
        $date_pendingticket = array();
        $date_totalticket = array();
        foreach ($openticket AS $ticket) {
            if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($closeticket AS $ticket) {
            if (!isset($date_closeticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = $date_closeticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($answeredticket AS $ticket) {
            if (!isset($date_answeredticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($overdueticket AS $ticket) {
            if (!isset($date_overdueticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($pendingticket AS $ticket) {
            if (!isset($date_pendingticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($totalticket AS $ticket) {
            if (!isset($date_totalticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_totalticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_totalticket[date('Y-m-d', strtotime($ticket->created))] = $date_totalticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        $open_ticket = 0;
        $close_ticket = 0;
        $answered_ticket = 0;
        $overdue_ticket = 0;
        $pending_ticket = 0;
        $total_ticket = 0;
        $json_array = "";
        do{
            $year = date('Y',strtotime($nextdate));
            $month = date('m',strtotime($nextdate));
            $month = $month - 1; //js month are 0 based
            $day = date('d',strtotime($nextdate));
            $openticket_tmp = isset($date_openticket[$nextdate]) ? $date_openticket[$nextdate]  : 0;
            $closeticket_tmp = isset($date_closeticket[$nextdate]) ? $date_closeticket[$nextdate] : 0;
            $answeredticket_tmp = isset($date_answeredticket[$nextdate]) ? $date_answeredticket[$nextdate] : 0;
            $overdueticket_tmp = isset($date_overdueticket[$nextdate]) ? $date_overdueticket[$nextdate] : 0;
            $pendingticket_tmp = isset($date_pendingticket[$nextdate]) ? $date_pendingticket[$nextdate] : 0;
            $totalticket_tmp = isset($date_totalticket[$nextdate]) ? $date_totalticket[$nextdate] : 0;
            $json_array .= "[new Date($year,$month,$day),$openticket_tmp,$answeredticket_tmp,$pendingticket_tmp,$overdueticket_tmp,$closeticket_tmp],";
            $open_ticket += $openticket_tmp;
            $close_ticket += $closeticket_tmp;
            $answered_ticket += $answeredticket_tmp;
            $overdue_ticket += $overdueticket_tmp;
            $pending_ticket += $pendingticket_tmp;
            $total_ticket += $totalticket_tmp;
            if($nextdate == $fromdate){
                break;
            }
            $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
        }while($nextdate != $fromdate);

        $result['ticket_total']['openticket'] = $open_ticket;
        $result['ticket_total']['closeticket'] = $close_ticket;
        $result['ticket_total']['answeredticket'] = $answered_ticket;
        $result['ticket_total']['overdueticket'] = $overdue_ticket;
        $result['ticket_total']['pendingticket'] = $pending_ticket;
        $result['ticket_total']['totalticket'] = $total_ticket;

        $result['line_chart_json_array'] = $json_array;

        // Pagination
        $query = "SELECT COUNT(user.id)
                    FROM `#__users` AS user 
                    WHERE NOT EXISTS (SELECT id FROM `#__js_ticket_staff` WHERE uid = user.id) ";
        if($uid) $query .= " AND user.id = ".$uid;
        $db->setQuery($query);
        $total = $db->loadResult();
        $result['total'] = $total;

        $query = "SELECT user.name AS display_name,user.email AS user_email,user.id,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS pendingticket 
                    FROM `#__users` AS user 
                    WHERE NOT EXISTS (SELECT id FROM `#__js_ticket_staff` WHERE uid = user.id) ";
        if($uid) $query .= " AND user.id = ".$uid;
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $result['users_report'] =$users;
        return $result;
    }

    function getStaffDetailReportByStaffId($id){
        $db = JFactory::getDbo();
        $result = array();
        if(!is_numeric($id)) return false;
        if($id != 0 && $id != " " ){
            $date_start = JFactory::getApplication()->input->get('date_start');
            $date_end = JFactory::getApplication()->input->get('date_end');
            $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
            if($jsresetbutton == 1){
                $date_start = null;
                $date_end = null;
            }
            if( $date_start != '' && $date_end != '' ){
                if(JFactory::getApplication()->input->getMethod() == 'POST'){// why this? because
                // if detailed form is posted, we need to get dates in mysql format
                // but if get method, dates are already in mysql format in url
                    $config = $this->getJSModel('config')->getConfigs();
                    $dateformat = $config['date_format'];
                    if ($dateformat == 'm-d-Y') {
                      $arr = explode('-', $date_start);
                      $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                      $arr = explode('-', $date_end);
                      $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = explode('-', $date_start);
                      $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                      $arr = explode('-', $date_end);
                      $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                }
                $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
                $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );
                if($date_start > $date_end){
                    $tmp = $date_start;
                    $date_start = $date_end;
                    $date_end = $tmp;
                }
            }
            $formsearch = JFactory::getApplication()->input->get('option', 'post');
            if ($formsearch == 'com_jssupportticket') {
                $_SESSION['JSST_SEARCH']['date_start'] = $date_start;
                $_SESSION['JSST_SEARCH']['date_end'] = $date_end;
            }

            if (JFactory::getApplication()->input->get('pagenum', 'get', null) != null) {
                $date_start = (isset($_SESSION['JSST_SEARCH']['date_start']) && $_SESSION['JSST_SEARCH']['date_start'] != '') ? $_SESSION['JSST_SEARCH']['date_start'] : null;
                $date_end = (isset($_SESSION['JSST_SEARCH']['date_end']) && $_SESSION['JSST_SEARCH']['date_end'] != '') ? $_SESSION['JSST_SEARCH']['date_end'] : null;
            }
            //Line Chart Data
            $curdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
            $fromdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
            $result['filter']['date_start'] = $fromdate;
            $result['filter']['date_end'] = $curdate;

            $nextdate = $curdate;

            //Query to get Data
            $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
            if($id) $query .= " AND staffid = ".$id;
            $db->setQuery($query);
            $openticket = $db->loadObjectList();

            $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
            if($id) $query .= " AND staffid = ".$id;
            $db->setQuery($query);
            $closeticket = $db->loadObjectList();

            $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
            if($id) $query .= " AND staffid = ".$id;
            $db->setQuery($query);
            $answeredticket = $db->loadObjectList();

            $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
            if($id) $query .= " AND staffid = ".$id;
            $db->setQuery($query);
            $overdueticket = $db->loadObjectList();

            $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
            if($id) $query .= " AND staffid = ".$id;
            $db->setQuery($query);
            $pendingticket = $db->loadObjectList();

            $date_openticket = array();
            $date_closeticket = array();
            $date_answeredticket = array();
            $date_overdueticket = array();
            $date_pendingticket = array();
            foreach ($openticket AS $ticket) {
                if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))]))
                    $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + 1;
            }
            foreach ($closeticket AS $ticket) {
                if (!isset($date_closeticket[date('Y-m-d', strtotime($ticket->created))]))
                    $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = 0;
                $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = $date_closeticket[date('Y-m-d', strtotime($ticket->created))] + 1;
            }
            foreach ($answeredticket AS $ticket) {
                if (!isset($date_answeredticket[date('Y-m-d', strtotime($ticket->created))]))
                    $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = 0;
                $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] + 1;
            }
            foreach ($overdueticket AS $ticket) {
                if (!isset($date_overdueticket[date('Y-m-d', strtotime($ticket->created))]))
                    $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = 0;
                $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] + 1;
            }
            foreach ($pendingticket AS $ticket) {
                if (!isset($date_pendingticket[date('Y-m-d', strtotime($ticket->created))]))
                    $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = 0;
                $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] + 1;
            }
            $open_ticket = 0;
            $close_ticket = 0;
            $answered_ticket = 0;
            $overdue_ticket = 0;
            $pending_ticket = 0;
            $json_array = "";
            do{
                $year = date('Y',strtotime($nextdate));
                $month = date('m',strtotime($nextdate));
                $month = $month - 1; //js month are 0 based
                $day = date('d',strtotime($nextdate));
                $openticket_tmp = isset($date_openticket[$nextdate]) ? $date_openticket[$nextdate]  : 0;
                $closeticket_tmp = isset($date_closeticket[$nextdate]) ? $date_closeticket[$nextdate] : 0;
                $answeredticket_tmp = isset($date_answeredticket[$nextdate]) ? $date_answeredticket[$nextdate] : 0;
                $overdueticket_tmp = isset($date_overdueticket[$nextdate]) ? $date_overdueticket[$nextdate] : 0;
                $pendingticket_tmp = isset($date_pendingticket[$nextdate]) ? $date_pendingticket[$nextdate] : 0;
                $json_array .= "[new Date($year,$month,$day),$openticket_tmp,$answeredticket_tmp,$pendingticket_tmp,$overdueticket_tmp,$closeticket_tmp],";
                $open_ticket += $openticket_tmp;
                $close_ticket += $closeticket_tmp;
                $answered_ticket += $answeredticket_tmp;
                $overdue_ticket += $overdueticket_tmp;
                $pending_ticket += $pendingticket_tmp;
                if($nextdate == $fromdate){
                    break;
                }
                $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
            }while($nextdate != $fromdate);

            $result['line_chart_json_array'] = $json_array;


            $query = "SELECT staff.photo,staff.id,staff.firstname,staff.lastname,staff.username,staff.email,user.name AS display_name,user.email AS user_email,
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
            $result['staff_report'] = $staff;

            // ticket ids for staff member on which he replied but are not assigned to him
            $query = "SELECT DISTINCT(ticketid) AS ticketid
                        FROM `#__js_ticket_staff_time` 
                        WHERE staffid = ".$id;
            $db->setQuery($query);
            $all_tickets = $db->loadObjectList();
            $ticketid_string = '';
            $comma = '';
            foreach ($all_tickets as $ticket) {
                $ticketid_string .= $comma.$ticket->ticketid;
                $comma = ', ';
            }        
            if($ticketid_string == ''){
                $q_strig = "(staffid = ".$id.")";
            }else{
                $q_strig = "(staffid = ".$id." OR ticket.id IN (".$ticketid_string."))";
            }

            // Pagination
            $query = "SELECT COUNT(ticket.id)
                        FROM `#__js_ticket_tickets` AS ticket 
                        JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid                     
                        WHERE ".$q_strig." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
            $db->setQuery($query);
            $total = $db->loadResult();
            $result['total'] = $total;
            //Tickets
            $query = "SELECT ticket.*,priority.priority, priority.prioritycolour , feedback.rating
                        FROM `#__js_ticket_tickets` AS ticket 
                        JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid     
                        LEFT JOIN `#__js_ticket_feedbacks` AS feedback ON feedback.ticketid = ticket.id                
                        WHERE ".$q_strig." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
            $db->setQuery($query);
            $result['staff_tickets'] = $db->loadObjectList();
            foreach ($result['staff_tickets'] as $ticket) {
                 $ticket->time = $this->getJSModel('staff')->getTimeTakenByTicketIdAndStaffId($ticket->id,$id);
            }
            return $result;
        }
    }   

    function getStaffDetailReportByUserId($id){
        $db = JFactory::getDbo();
        $result = array();
        if(!is_numeric($id)) return false;

        $date_start = JFactory::getApplication()->input->get('date_start');
        $date_end = JFactory::getApplication()->input->get('date_end');
        $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
        if($jsresetbutton == 1){
            $date_start = null;
            $date_end = null;
        }
        
        if( $date_start != '' && $date_end != '' ){
            if(JFactory::getApplication()->input->getMethod() == 'POST'){//why this?
            //because, form is posted, then we need to convert dates in mysql format
            //if not posted, dates come from url, that are already in mysql format
                $config = $this->getJSModel('config')->getConfigs();
                $dateformat = $config['date_format'];
                if ($dateformat == 'm-d-Y') {
                  $arr = explode('-', $date_start);
                  $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                  $arr = explode('-', $date_end);
                  $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                  $arr = explode('-', $date_start);
                  $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                  $arr = explode('-', $date_end);
                  $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                }
            }
            $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
            $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );
            if($date_start > $date_end){
                $tmp = $date_start;
                $date_start = $date_end;
                $date_end = $tmp;
            }
        }
        $formsearch = JFactory::getApplication()->input->get('option', 'post');
        if ($formsearch == 'com_jssupportticket') {
            $_SESSION['JSST_SEARCH']['date_start'] = $date_start;
            $_SESSION['JSST_SEARCH']['date_end'] = $date_end;
        }
        if (JFactory::getApplication()->input->get('pagenum', 'get', null) != null) {
            $date_start = (isset($_SESSION['JSST_SEARCH']['date_start']) && $_SESSION['JSST_SEARCH']['date_start'] != '') ? $_SESSION['JSST_SEARCH']['date_start'] : null;
            $date_end = (isset($_SESSION['JSST_SEARCH']['date_end']) && $_SESSION['JSST_SEARCH']['date_end'] != '') ? $_SESSION['JSST_SEARCH']['date_end'] : null;
        }
        //Line Chart Data
        $curdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        $fromdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
        $result['filter']['date_start'] = $fromdate;
        $result['filter']['date_end'] = $curdate;
        $nextdate = $curdate;


        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $openticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $closeticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $answeredticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $overdueticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND uid = ".$id;
        $db->setQuery($query);
        $pendingticket = $db->loadObjectList();

        $date_openticket = array();
        $date_closeticket = array();
        $date_answeredticket = array();
        $date_overdueticket = array();
        $date_pendingticket = array();
        foreach ($openticket AS $ticket) {
            if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($closeticket AS $ticket) {
            if (!isset($date_closeticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = $date_closeticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($answeredticket AS $ticket) {
            if (!isset($date_answeredticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($overdueticket AS $ticket) {
            if (!isset($date_overdueticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($pendingticket AS $ticket) {
            if (!isset($date_pendingticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        $open_ticket = 0;
        $close_ticket = 0;
        $answered_ticket = 0;
        $overdue_ticket = 0;
        $pending_ticket = 0;
        $json_array = "";
        do{
            $year = date('Y',strtotime($nextdate));
            $month = date('m',strtotime($nextdate));
            $month = $month - 1; //js month are 0 based
            $day = date('d',strtotime($nextdate));
            $openticket_tmp = isset($date_openticket[$nextdate]) ? $date_openticket[$nextdate]  : 0;
            $closeticket_tmp = isset($date_closeticket[$nextdate]) ? $date_closeticket[$nextdate] : 0;
            $answeredticket_tmp = isset($date_answeredticket[$nextdate]) ? $date_answeredticket[$nextdate] : 0;
            $overdueticket_tmp = isset($date_overdueticket[$nextdate]) ? $date_overdueticket[$nextdate] : 0;
            $pendingticket_tmp = isset($date_pendingticket[$nextdate]) ? $date_pendingticket[$nextdate] : 0;
            $json_array .= "[new Date($year,$month,$day),$openticket_tmp,$answeredticket_tmp,$pendingticket_tmp,$overdueticket_tmp,$closeticket_tmp],";
            $open_ticket += $openticket_tmp;
            $close_ticket += $closeticket_tmp;
            $answered_ticket += $answeredticket_tmp;
            $overdue_ticket += $overdueticket_tmp;
            $pending_ticket += $pendingticket_tmp;
            if($nextdate == $fromdate){
                break;
            }
            $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
        }while($nextdate != $fromdate);

        $result['line_chart_json_array'] = $json_array;

        $query = "SELECT user.name AS display_name,user.email AS user_email,user.id,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND isoverdue != 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4  AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND uid = user.id) AS pendingticket 
                    FROM `#__users` AS user 
                    WHERE user.id = ".$id;
        $db->setQuery($query);
        $staff = $db->loadObject();
        $result['user_report'] =$staff;
        // Pagination
        $query = "SELECT COUNT(ticket.id)
                    FROM `#__js_ticket_tickets` AS ticket 
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid                     
                    WHERE uid = ".$id." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $total = $db->loadResult();
        $result['total'] = $total;
        //Tickets
        $query = "SELECT ticket.*,priority.priority, priority.prioritycolour , feedback.rating
                    FROM `#__js_ticket_tickets` AS ticket 
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid   
                    LEFT JOIN `#__js_ticket_feedbacks` AS feedback ON feedback.ticketid = ticket.id                  
                    WHERE uid = ".$id." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $result['user_tickets'] = $db->loadObjectList();
        foreach ($result['user_tickets'] as $ticket) {
             $ticket->time = $this->getJSModel('staff')->getTimeTakenByTicketId($ticket->id);
        }
        return $result;
    }

    function getStaffReportsFE($date_start ,$date_end ,  $limitstart, $limit){
        $db = JFactory::getDbo();
        
        if( $date_start != '' && $date_end != '' ){
            $config = $this->getJSModel('config')->getConfigs();
            $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
            $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );

            if($date_start > $date_end){
                $tmp = $date_start;
                $date_start = $date_end;
                $date_end = $tmp;
            }
        }
        $user = JSSupportticketCurrentUser::getInstance();
        $id = $user->getStaffId();

        //Line Chart Data
        $curdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
        $dates = '';
        $fromdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        $results['filter']['date_start'] = $curdate;
        $results['filter']['date_end'] = $fromdate;

        $staffid = $user->getStaffId();

        $results['filter']['staffname'] = $this->getJSModel('staff')->getMyName($staffid);
        $nextdate = $fromdate;
        // find my depats
        $query = "SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $staffid";
        $db->setQuery($query);
        $data = $db->loadObjectList();
        $my_depts = '';
        foreach ($data as $key => $value) {
            if($my_depts)
                $my_depts .= ',';
            $my_depts .= $value->departmentid;
        }        
        // get mytickets, or all tickets with my departments
        if($my_depts)
            $dep_query = " AND (ticket.staffid = $staffid OR ticket.departmentid IN ($my_depts)) ";
        else
            $dep_query = " AND ( ticket.staffid = $staffid ) ";
        //Query to get Data
        $query = "SELECT ticket.created FROM `#__js_ticket_tickets` AS ticket WHERE ticket.status = 0 AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "'";
        $query .= $dep_query;
        $db->setQuery($query);
        $openticket = $db->loadObjectList();

        $query = "SELECT ticket.created FROM `#__js_ticket_tickets` AS ticket WHERE ticket.status = 4 AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "'";
        $query .= $dep_query;
        $db->setQuery($query);
        $closeticket = $db->loadObjectList();

        $query = "SELECT ticket.created FROM `#__js_ticket_tickets` AS ticket WHERE ticket.isanswered = 1 AND ticket.status != 4 AND ticket.status != 0 AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "'";
        $query .= $dep_query;
        $db->setQuery($query);
        $answeredticket = $db->loadObjectList();

        $query = "SELECT ticket.created FROM `#__js_ticket_tickets` AS ticket WHERE ticket.isoverdue = 1 AND ticket.status != 4 AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "'";
        $query .= $dep_query;
        $db->setQuery($query);
        $overdueticket = $db->loadObjectList();

        $query = "SELECT ticket.created FROM `#__js_ticket_tickets` AS ticket WHERE ticket.isanswered != 1 AND isoverdue != 1 AND  ticket.status != 4 AND (ticket.lastreply != '0000-00-00 00:00:00') AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "'";
        $query .= $dep_query;
        $db->setQuery($query);
        $pendingticket = $db->loadObjectList();

        $date_openticket = array();
        $date_closeticket = array();
        $date_answeredticket = array();
        $date_overdueticket = array();
        $date_pendingticket = array();
        foreach ($openticket AS $ticket) {
            if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($closeticket AS $ticket) {
            if (!isset($date_closeticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = $date_closeticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($answeredticket AS $ticket) {
            if (!isset($date_answeredticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($overdueticket AS $ticket) {
            if (!isset($date_overdueticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($pendingticket AS $ticket) {
            if (!isset($date_pendingticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        $open_ticket = 0;
        $close_ticket = 0;
        $answered_ticket = 0;
        $json_array = "";
        $open_ticket = 0;
        $close_ticket = 0;
        $answered_ticket = 0;
        $overdue_ticket = 0;
        $pending_ticket = 0;

        do{
            $year = date('Y',strtotime($nextdate));
            $month = date('m',strtotime($nextdate));
            $month = $month - 1; //js month are 0 based
            $day = date('d',strtotime($nextdate));
            $openticket_tmp = isset($date_openticket[$nextdate]) ? $date_openticket[$nextdate]  : 0;
            $closeticket_tmp = isset($date_closeticket[$nextdate]) ? $date_closeticket[$nextdate] : 0;
            $answeredticket_tmp = isset($date_answeredticket[$nextdate]) ? $date_answeredticket[$nextdate] : 0;
            $overdueticket_tmp = isset($date_overdueticket[$nextdate]) ? $date_overdueticket[$nextdate] : 0;
            $pendingticket_tmp = isset($date_pendingticket[$nextdate]) ? $date_pendingticket[$nextdate] : 0;
            $json_array .= "[new Date($year,$month,$day),$openticket_tmp,$answeredticket_tmp,$pendingticket_tmp,$overdueticket_tmp,$closeticket_tmp],";
            $open_ticket += $openticket_tmp;
            $close_ticket += $closeticket_tmp;
            $answered_ticket += $answeredticket_tmp;
            $overdue_ticket += $overdueticket_tmp;
            $pending_ticket += $pendingticket_tmp;
            if($nextdate == $curdate){
                break;
            }
            $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
        }while($nextdate != $curdate);

        $results['ticket_total']['openticket'] = $open_ticket;
        $results['ticket_total']['closeticket'] = $close_ticket;
        $results['ticket_total']['answeredticket'] = $answered_ticket;
        $results['ticket_total']['overdueticket'] = $overdue_ticket;
        $results['ticket_total']['pendingticket'] = $pending_ticket;

        $results['line_chart_json_array'] = $json_array;

        // Pagination staffs listing
        $query = "SELECT COUNT(DISTINCT staff.id)
            FROM `#__js_ticket_staff` AS staff 
            JOIN `#__users` AS user ON user.id = staff.uid
            LEFT JOIN `#__js_ticket_acl_user_access_departments` AS dep ON dep.staffid = staff.id ";
        $query .= " WHERE (staff.id = $staffid OR dep.departmentid IN (SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $staffid))";
        
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;
        // data
        $query = "SELECT DISTINCT staff.photo,staff.id,staff.firstname,staff.lastname,staff.username,staff.email,user.name AS display_name,user.email AS user_email,user.username AS user_nicename,
            (SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.status = 0  AND isoverdue != 1  AND (ticket.lastreply = '0000-00-00 00:00:00' OR ticket.lastreply IS NULL) AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "' AND ticket.staffid = staff.id) AS openticket, 
            (SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.status = 4 AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "' AND ticket.staffid = staff.id) AS closeticket, 
            (SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.isanswered = 1 AND ticket.status != 4 AND ticket.status != 0 AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "' AND ticket.staffid = staff.id) AS answeredticket, 
            (SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.isoverdue = 1 AND ticket.status != 4 AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "' AND ticket.staffid = staff.id) AS overdueticket, 
            (SELECT COUNT(ticket.id) FROM `#__js_ticket_tickets` AS ticket WHERE ticket.isanswered != 1 AND isoverdue != 1  AND ticket.status != 4 AND (ticket.lastreply != '0000-00-00 00:00:00') AND date(ticket.created) >= '" . $curdate . "' AND date(ticket.created) <= '" . $fromdate . "' AND ticket.staffid = staff.id) AS pendingticket 
            FROM `#__js_ticket_staff` AS staff 
            JOIN `#__users` AS user ON user.id = staff.uid
            LEFT JOIN `#__js_ticket_acl_user_access_departments` AS dep ON dep.staffid = staff.id";
        $query .= " WHERE (staff.id = $staffid OR dep.departmentid IN (SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $staffid))";
        
        $db->setQuery($query, $limitstart, $limit);
        $staffs = $db->loadObjectList();
        $results['staffs_report'] = $staffs;
        $results[1] = $total;
        return $results;
    }

    function isValidStaffid($staffid){
        if( ! is_numeric($staffid))
            return false;
        $db = JFactory::getDbo();
        $user = JSSupportticketCurrentUser::getInstance();
        $id = $user->getStaffId();

        if( $id == $staffid )
            return true;
        $query = "SELECT staff.id AS staffid
            FROM `#__js_ticket_staff` AS staff 
            JOIN `#__users` AS user ON user.id = staff.uid
            JOIN `#__js_ticket_acl_user_access_departments` AS dep ON dep.staffid = staff.id ";
        $query .= " WHERE (dep.departmentid IN (SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $id))";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        foreach ($result as $staff) {
            if($staff->staffid == $staffid)
                return true;
        }
        return false;
    }
    
    function getDepartmentReportsFE($limitstart , $limit){
        $db = JFactory::getDbo();

        $user = JSSupportticketCurrentUser::getInstance();
        $staffid = $user->getStaffId();

        $query = "SELECT dept.departmentname,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE departmentid = dept.id ) AS totalticket
            FROM `#__js_ticket_departments` AS dept
            JOIN `#__js_ticket_acl_user_access_departments` AS acl ON acl.departmentid = dept.id
            WHERE acl.staffid = $staffid AND dept.status = 1";

        $db->setQuery($query);
        $department = $db->loadObjectList();
        $results['pie3d_chart1'] = "";
        $i = 0;
        foreach($department AS $dept){
            if($dept->totalticket == 0)
                $i += 1;
            $results['pie3d_chart1'] .= "['$dept->departmentname',$dept->totalticket],";
        }

        if(count($department) == $i)
            $results['pie3d_chart1'] = '';

        // pagination
        $query = "SELECT count(dept.id)
            FROM `#__js_ticket_departments` AS dept
            JOIN `#__js_ticket_acl_user_access_departments` AS acl ON acl.departmentid = dept.id
            WHERE acl.staffid = $staffid AND dept.status = 1";
        
        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;

        $query = "SELECT dept.departmentname,
            (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND isoverdue != 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND departmentid = dept.id) AS openticket, 
            (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND departmentid = dept.id) AS closeticket, 
            (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND departmentid = dept.id) AS answeredticket, 
            (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND departmentid = dept.id) AS overdueticket, 
            (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND isoverdue != 1  AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND departmentid = dept.id) AS pendingticket     
            FROM `#__js_ticket_departments` AS dept
            JOIN `#__js_ticket_acl_user_access_departments` AS acl ON acl.departmentid = dept.id
            WHERE acl.staffid = $staffid AND dept.status = 1 ";
        
        $db->setQuery($query , $limitstart , $limit);
        $departments = $db->loadObjectList();
        $results['departments_report'] = $departments;
        $results[1] = $total;
        return $results;
    }

    function getDepartmentReports(){
        $db = JFactory::getDbo();
        $result = array();
        $date_start = JFactory::getApplication()->input->get('date_start');
        $date_end = JFactory::getApplication()->input->get('date_end');
        $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
        if($jsresetbutton == 1){
            $date_start = null;
            $date_end = null;
        }
        
        if( $date_start != '' && $date_end != '' ){
              $config = $this->getJSModel('config')->getConfigs();
              $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
            $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );

            if($date_start > $date_end){
                $tmp = $date_start;
                $date_start = $date_end;
                $date_end = $tmp;
            }
        }
        $uid = JFactory::getApplication()->input->get('uid');
        $formsearch = JFactory::getApplication()->input->get('option', 'post');
        if ($formsearch == 'com_jssupportticket') {
            $_SESSION['JSST_SEARCH']['date_start'] = $date_start;
            $_SESSION['JSST_SEARCH']['date_end'] = $date_end;
        }

        if (JFactory::getApplication()->input->get('pagenum', 'get', null) != null) {
            $date_start = (isset($_SESSION['JSST_SEARCH']['date_start']) && $_SESSION['JSST_SEARCH']['date_start'] != '') ? $_SESSION['JSST_SEARCH']['date_start'] : null;
            $date_end = (isset($_SESSION['JSST_SEARCH']['date_end']) && $_SESSION['JSST_SEARCH']['date_end'] != '') ? $_SESSION['JSST_SEARCH']['date_end'] : null;
        }
        //Line Chart Data
        $curdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        $dates = '';
        $fromdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
        $result['filter']['date_start'] = $fromdate;
        $result['filter']['date_end'] = $curdate;
        $nextdate = $curdate;
        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0 AND isoverdue != 1  AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $openticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $closeticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $answeredticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $overdueticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $pendingticket = $db->loadObjectList();

        $date_openticket = array();
        $date_closeticket = array();
        $date_answeredticket = array();
        $date_overdueticket = array();
        $date_pendingticket = array();
        foreach ($openticket AS $ticket) {
            if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($closeticket AS $ticket) {
            if (!isset($date_closeticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = $date_closeticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($answeredticket AS $ticket) {
            if (!isset($date_answeredticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($overdueticket AS $ticket) {
            if (!isset($date_overdueticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($pendingticket AS $ticket) {
            if (!isset($date_pendingticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        $open_ticket = 0;
        $close_ticket = 0;
        $answered_ticket = 0;
        $overdue_ticket = 0;
        $pending_ticket = 0;
        $json_array = "";
        do{
            $year = date('Y',strtotime($nextdate));
            $month = date('m',strtotime($nextdate));
            $month = $month - 1; //js month are 0 based
            $day = date('d',strtotime($nextdate));
            $openticket_tmp = isset($date_openticket[$nextdate]) ? $date_openticket[$nextdate]  : 0;
            $closeticket_tmp = isset($date_closeticket[$nextdate]) ? $date_closeticket[$nextdate] : 0;
            $answeredticket_tmp = isset($date_answeredticket[$nextdate]) ? $date_answeredticket[$nextdate] : 0;
            $overdueticket_tmp = isset($date_overdueticket[$nextdate]) ? $date_overdueticket[$nextdate] : 0;
            $pendingticket_tmp = isset($date_pendingticket[$nextdate]) ? $date_pendingticket[$nextdate] : 0;
            $json_array .= "[new Date($year,$month,$day),$openticket_tmp,$answeredticket_tmp,$pendingticket_tmp,$overdueticket_tmp,$closeticket_tmp],";
            $open_ticket += $openticket_tmp;
            $close_ticket += $closeticket_tmp;
            $answered_ticket += $answeredticket_tmp;
            $overdue_ticket += $overdueticket_tmp;
            $pending_ticket += $pendingticket_tmp;
            if($nextdate == $fromdate){
                break;
            }
            $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
        }while($nextdate != $fromdate);

        $result['ticket_total']['openticket'] = $open_ticket;
        $result['ticket_total']['closeticket'] = $close_ticket;
        $result['ticket_total']['answeredticket'] = $answered_ticket;
        $result['ticket_total']['overdueticket'] = $overdue_ticket;
        $result['ticket_total']['pendingticket'] = $pending_ticket;

        $result['line_chart_json_array'] = $json_array;

        // Pagination
        $query = "SELECT COUNT(department.id)
                    FROM `#__js_ticket_departments` AS department ";
        $db->setQuery($query);
        $total = $db->loadResult();
        $result['total'] = $total;

        $query = "SELECT department.departmentname AS display_name,email.email AS department_email,department.id,
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS pendingticket 
                    FROM `#__js_ticket_departments` AS department 
                    LEFT JOIN `#__js_ticket_email` AS email ON email.id = department.emailid ";
        $db->setQuery($query);
        $users = $db->loadObjectList();
        $result['departments_report'] =$users;
        return $result;
    }

     function getDepartmentDetailReportById($id){
        $db = JFactory::getDbo();
        $result = array();
        if(!is_numeric($id)) return false;

        $date_start = JFactory::getApplication()->input->get('date_start');
        $date_end = JFactory::getApplication()->input->get('date_end');
        $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
        if($jsresetbutton == 1){
            $date_start = null;
            $date_end = null;
        }
        
        if( $date_start != '' && $date_end != '' ){
            if( $date_start != '' && $date_end != '' ){
                if(JFactory::getApplication()->input->getMethod() == 'POST'){//why this?
                //because, form is posted, then we need to convert dates in mysql format
                //if not posted, dates come from url, that are already in mysql format
                    $config = $this->getJSModel('config')->getConfigs();
                    $dateformat = $config['date_format'];
                    if ($dateformat == 'm-d-Y') {
                      $arr = explode('-', $date_start);
                      $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                      $arr = explode('-', $date_end);
                      $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
                    } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
                      $arr = explode('-', $date_start);
                      $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                      $arr = explode('-', $date_end);
                      $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
                    }
                }
                $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
                $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );
                if($date_start > $date_end){
                    $tmp = $date_start;
                    $date_start = $date_end;
                    $date_end = $tmp;
                }
            }
        }
        $formsearch = JFactory::getApplication()->input->get('option', 'post');
        if ($formsearch == 'com_jssupportticket') {
            $_SESSION['JSST_SEARCH']['date_start'] = $date_start;
            $_SESSION['JSST_SEARCH']['date_end'] = $date_end;
        }
        if (JFactory::getApplication()->input->get('pagenum', 'get', null) != null) {
            $date_start = (isset($_SESSION['JSST_SEARCH']['date_start']) && $_SESSION['JSST_SEARCH']['date_start'] != '') ? $_SESSION['JSST_SEARCH']['date_start'] : null;
            $date_end = (isset($_SESSION['JSST_SEARCH']['date_end']) && $_SESSION['JSST_SEARCH']['date_end'] != '') ? $_SESSION['JSST_SEARCH']['date_end'] : null;
        }
        //Line Chart Data
        $curdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        $fromdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
        $result['filter']['date_start'] = $fromdate;
        $result['filter']['date_end'] = $curdate;
        $nextdate = $curdate;


        //Query to get Data
        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $openticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $closeticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $answeredticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $overdueticket = $db->loadObjectList();

        $query = "SELECT created FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        if($id) $query .= " AND departmentid = ".$id;
        $db->setQuery($query);
        $pendingticket = $db->loadObjectList();

        $date_openticket = array();
        $date_closeticket = array();
        $date_answeredticket = array();
        $date_overdueticket = array();
        $date_pendingticket = array();
        foreach ($openticket AS $ticket) {
            if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($closeticket AS $ticket) {
            if (!isset($date_closeticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_closeticket[date('Y-m-d', strtotime($ticket->created))] = $date_closeticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($answeredticket AS $ticket) {
            if (!isset($date_answeredticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] = $date_answeredticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($overdueticket AS $ticket) {
            if (!isset($date_overdueticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] = $date_overdueticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        foreach ($pendingticket AS $ticket) {
            if (!isset($date_pendingticket[date('Y-m-d', strtotime($ticket->created))]))
                $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] = $date_pendingticket[date('Y-m-d', strtotime($ticket->created))] + 1;
        }
        $open_ticket = 0;
        $close_ticket = 0;
        $answered_ticket = 0;
        $overdue_ticket = 0;
        $pending_ticket = 0;
        $json_array = "";
        do{
            $year = date('Y',strtotime($nextdate));
            $month = date('m',strtotime($nextdate));
            $month = $month - 1; //js month are 0 based
            $day = date('d',strtotime($nextdate));
            $openticket_tmp = isset($date_openticket[$nextdate]) ? $date_openticket[$nextdate]  : 0;
            $closeticket_tmp = isset($date_closeticket[$nextdate]) ? $date_closeticket[$nextdate] : 0;
            $answeredticket_tmp = isset($date_answeredticket[$nextdate]) ? $date_answeredticket[$nextdate] : 0;
            $overdueticket_tmp = isset($date_overdueticket[$nextdate]) ? $date_overdueticket[$nextdate] : 0;
            $pendingticket_tmp = isset($date_pendingticket[$nextdate]) ? $date_pendingticket[$nextdate] : 0;
            $json_array .= "[new Date($year,$month,$day),$openticket_tmp,$answeredticket_tmp,$pendingticket_tmp,$overdueticket_tmp,$closeticket_tmp],";
            $open_ticket += $openticket_tmp;
            $close_ticket += $closeticket_tmp;
            $answered_ticket += $answeredticket_tmp;
            $overdue_ticket += $overdueticket_tmp;
            $pending_ticket += $pendingticket_tmp;
            if($nextdate == $fromdate){
                break;
            }
            $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
        }while($nextdate != $fromdate);

        $result['line_chart_json_array'] = $json_array;

        $query = "SELECT department.departmentname AS display_name,email.email AS department_email,department.id, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0  AND isoverdue != 1 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS openticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS closeticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS answeredticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS overdueticket, 
                    (SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1  AND isoverdue != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate) . " AND departmentid = department.id) AS pendingticket 
                    FROM `#__js_ticket_departments` AS department 
                    LEFT JOIN `#__js_ticket_email` AS email ON email.id = department.emailid 
                    WHERE department.id = ".$id;
        $db->setQuery($query);
        $departments = $db->loadObject();
        $result['department_report'] =$departments;
        // Pagination
        $query = "SELECT COUNT(ticket.id)
                    FROM `#__js_ticket_tickets` AS ticket 
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid                     
                    WHERE departmentid = ".$id." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $total = $db->loadResult();
        $result['total'] = $total;
        //Tickets
        $query = "SELECT ticket.*,priority.priority, priority.prioritycolour , feedback.rating
                    FROM `#__js_ticket_tickets` AS ticket 
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid 
                    LEFT JOIN `#__js_ticket_feedbacks` AS feedback ON feedback.ticketid = ticket.id                    
                    WHERE departmentid = ".$id." AND date(ticket.created) >= " . $db->quote($fromdate) . " AND date(ticket.created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $result['department_tickets'] = $db->loadObjectList();
        foreach ($result['department_tickets'] as $ticket) {
             $ticket->time = $this->getJSModel('staff')->getTimeTakenByTicketId($ticket->id);
        }
        return $result;
    }

    function getFieldsForExport(){

        $status_combo = array(
            array('value' => null, 'text' => JText::_('Select Ticket Status')),
            array('value' => '0', 'text' => JText::_('New')),
            array('value' => '1', 'text' => JText::_('Pending')),
            array('value' => '2', 'text' => JText::_('In Progress')),
            array('value' => '3', 'text' => JText::_('Answerd')),
            array('value' => '4', 'text' => JText::_('Closed'))
    );
    $yesno = array(
                array('value' => null, 'text' => JText::_('Select Ticket Overdue Status')),
                array('value' => '1', 'text' => JText::_('Yes')),
                array('value' => '2', 'text' => JText::_('No'))
    );
    
        $departments = $this->getJSModel('department')->getDepartments();
        $priorities = $this->getJSModel('priority')->getPriorities();
        $lists['staffmembers'] = JHTML::_('select.genericList', $this->getJSModel('staff')->getStaffMembers(), 'staffid', 'class="inputbox " ' . '', 'value', 'text', '');
        $lists['departments'] = JHTML::_('select.genericList', $departments, 'departmentid', '', 'value', 'text','');
        $lists['priorities'] = JHTML::_('select.genericList', $priorities, 'priorityid', '', 'value', 'text','');
        $lists['ticketstatus'] = JHTML::_('select.genericList', $status_combo, 'ticketstatus', '', 'value', 'text','');
        $lists['isoverdue'] = JHTML::_('select.genericList', $yesno, 'isoverdue', '', 'value', 'text','');
        return $lists;
    }

    function getSatisfactionReport(){
        $db = JFactory::getDbo();
        $query = "SELECT rating AS rate
                    FROM `#__js_ticket_feedbacks` ";
        $db->setQuery($query);
        $records = $db->loadObjectList();
        $arr =  array();
        $arr[1] = 0;
        $arr[2] = 0;
        $arr[3] = 0;
        $arr[4] = 0;
        $arr[5] = 0;
        $count = 0;
        foreach ($records as $key ) {
            $arr[$key->rate] += 1;
            $count++;
        }
        $arr[6] = $count;
        $result['result'] = $arr;
        $query = "SELECT AVG(rating) as ava
                    FROM `#__js_ticket_feedbacks` ";
        $db->setQuery($query);
        $result['avg'] = $db->loadResult();
        return $result;
    }
   
    function getStaffTimeReport($id){
        $db = JFactory::getDbo();
        $result = array();
        if(!is_numeric($id)) return false;

        $date_start = JFactory::getApplication()->input->get('date_start');
        $date_end = JFactory::getApplication()->input->get('date_end');
        if($date_start > $date_end){
            $tmp = $date_start;
            $date_start = $date_end;
            $date_end = $tmp;
        }
        if( $date_start != '' && $date_end != '' ){
              $config = $this->getJSModel('config')->getConfigs();
              $dateformat = $config['date_format'];
            if ($dateformat == 'm-d-Y') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
            } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
              $arr = explode('-', $date_start);
              $date_start = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
              $arr = explode('-', $date_end);
              $date_end = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
            }
            $date_start = JHTML::_('date',strtotime($date_start),"Y-m-d H:i:s" );
            $date_end = JHTML::_('date',strtotime($date_end),"Y-m-d H:i:s" );
        }
        $formsearch = JFactory::getApplication()->input->get('option', 'post');
        if ($formsearch == 'com_jssupportticket') {
            $_SESSION['JSST_SEARCH']['date_start'] = $date_start;
            $_SESSION['JSST_SEARCH']['date_end'] = $date_end;
        }

        if (JFactory::getApplication()->input->get('pagenum', 'get', null) != null) {
            $date_start = (isset($_SESSION['JSST_SEARCH']['date_start']) && $_SESSION['JSST_SEARCH']['date_start'] != '') ? $_SESSION['JSST_SEARCH']['date_start'] : null;
            $date_end = (isset($_SESSION['JSST_SEARCH']['date_end']) && $_SESSION['JSST_SEARCH']['date_end'] != '') ? $_SESSION['JSST_SEARCH']['date_end'] : null;
        }
        //Line Chart Data
        $curdate = ($date_end != null) ? date('Y-m-d',strtotime($date_end)) : date('Y-m-d');
        $fromdate = ($date_start != null) ? date('Y-m-d',strtotime($date_start)) : date('Y-m-d', strtotime("now -1 month"));
        $result['filter']['date_start'] = $fromdate;
        $result['filter']['date_end'] = $curdate;
        $result['filter']['id'] = $id;

        $nextdate = $curdate;

        //Query to get Data
        $query = "SELECT created,usertime 
                    FROM `#__js_ticket_staff_time` 
                    WHERE staffid = ".$id." AND date(created) >= " . $db->quote($fromdate) . " AND date(created) <= " . $db->quote($curdate);
        $db->setQuery($query);
        $openticket = $db->loadObjectList();
        $date_openticket = array();
        foreach ($openticket AS $ticket) {
            if (!isset($date_openticket[date('Y-m-d', strtotime($ticket->created))])){
                $date_openticket[date('Y-m-d', strtotime($ticket->created))] = 0;
            }
            $date_openticket[date('Y-m-d', strtotime($ticket->created))] = $date_openticket[date('Y-m-d', strtotime($ticket->created))] + $ticket->usertime;
        }
        
        $open_ticket = 0;
        $json_array = "";
        do{
            $year = date('Y',strtotime($nextdate));
            $month = date('m',strtotime($nextdate));
            $month = $month - 1; //js month are 0 based
            $day = date('d',strtotime($nextdate));
            if(isset($date_openticket[$nextdate])){
                $mins = floor($date_openticket[$nextdate] / 60);
                $openticket_tmp =  $mins;
            }else{
                $openticket_tmp =  0;
            }
            $json_array .= "[new Date($year,$month,$day),$openticket_tmp],";
            if($nextdate == $fromdate){
                break;
            }
            $nextdate = date('Y-m-d', strtotime($nextdate . " -1 days"));
        }while($nextdate != $fromdate);

        $result['line_chart_json_array'] = $json_array;
        return $result;
    }

}
?>
