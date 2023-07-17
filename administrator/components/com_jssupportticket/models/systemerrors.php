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
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelSystemErrors extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function updateSystemErrors($error) {
        $row = $this->getTable('systemerrors');
        $user = JFactory::getUser();
        $uid = $user->id;
        $data['uid'] = $uid;
		$data['error'] = $error;
        $data['isview'] = 2; // means not viewd
        $data['created'] = $cur = date('Y-m-d H:i:s');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return 2;
        }
        if (!$row->store()) {
            $this->setError($row->getError());
            return false;
        }

        return true;
    }
    
    function getSystemErrors($limitstart, $limit) {
        $db = $this->getDbo();
        $query = "SELECT Count(id) FROM `#__js_ticket_system_errors` ";
        $db->setQuery($query);
        $total = $db->loadResult();

        $query = "SELECT error.*,concat(staff.firstname,' ',staff.lastname) AS staffname 
					FROM `#__js_ticket_system_errors` AS error
					LEFT JOIN `#__js_ticket_staff` AS staff ON error.uid = staff.uid";
        $db->setQuery($query, $limitstart, $limit);
        $systemerrors = $db->loadObjectList();
        $result[0] = $systemerrors;
        $result[1] = $total;
        return $result;
    }

    function getErrorDetail($id) {
        if (!is_numeric($id))
            return False;
        $db = $this->getDbo();
        $this->updateIsView($id);
        $query = "SELECT error.*,concat(staff.firstname,' ',staff.lastname) AS staffname
					FROM `#__js_ticket_system_errors` AS error
					LEFT JOIN `#__js_ticket_staff` AS staff ON error.uid = staff.uid
					WHERE error.id = " . $id;
        $db->setQuery($query);
        $result = $db->loadObject();
        return $result;
    }

    function updateIsView($id) {
        if (!is_numeric($id))
            return False;
        $db = $this->getDbo();
        $query = "UPDATE `#__js_ticket_system_errors` set isview = 1 WHERE id = " . $id;
        $db->setQuery($query);
        $db->execute();
    }

}

?>
