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
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelEmailBanlistLog extends JSSupportTicketModel{

    function __construct() {
        parent::__construct();
    }

     function updateBanlistLog($name, $email) {
        $ip = $this->getIpAddress();
        $row = $this->getTable('banlistlog');
        $data['title'] = JText::_('Banned Emails');
        $data['log'] = JText::_('Ban email try to create ticket');
        $data['logger'] = $name;
        $data['loggeremail'] = $email;
        $data['ipaddress'] = $ip;
        $data['created'] = date('Y-m-d H:i:s');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->setError($row->getError());
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            return false;
        }
    }
    
    function getBanlistLog($startdate, $enddate, $emailaddress, $limitstart, $limit) {

        $db = $this->getDbo();
        $query = "SELECT Count(id) FROM `#__js_ticket_banlist_log` ";
        $db->setQuery($query);
        $total = $db->loadResult();

        $query = "SELECT * FROM `#__js_ticket_banlist_log` WHERE created <> -1";
        if ($emailaddress) {
            $query .= " AND loggeremail = " . $db->Quote($emailaddress);
        }
        /*$config = $this->getJSModel('config')->getConfigs();
        $dateformat = $config['date_format'];
        if ($dateformat == 'm-d-Y') {
          $arr = explode('-', $startdate);
          $startdate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
          $arr = explode('-', $enddate);
          $enddate = $arr[2] . '-' . $arr[0] . '-' . $arr[1];
        } elseif ($dateformat == 'd-m-Y' OR $dateformat == 'Y-m-d') {
          $arr = explode('-', $startdate);
          $startdate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
          $arr = explode('-', $enddate);
          $enddate = $arr[2] . '-' . $arr[1] . '-' . $arr[0];
        }*/
        $startdate = JHTML::_('date',strtotime($startdate),"Y-m-d H:i:s" );
        $enddate = JHTML::_('date',strtotime($enddate),"Y-m-d H:i:s" );
        if ($startdate && $enddate) {
            $query .= " AND created BETWEEN " . $db->Quote($startdate) . " AND " . $db->Quote($enddate);
        } elseif ($startdate) {
            $query .= " AND created >= " . $db->Quote($startdate);
        }
        $db->setQuery($query, $limitstart, $limit);
        $systemlog = $db->loadObjectList();
        $result[0] = $systemlog;
        $result[1] = $total;
        if ($emailaddress)
            $lists['email_address'] = $emailaddress;
        if ($startdate)
            $lists['start_date'] = $startdate;
        if ($enddate)
            $lists['end_date'] = $enddate;
        if (isset($lists))
            $result[2] = $lists;

        return $result;
    }

    function getIpAddress() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        return $ip;
    }
}
?>
