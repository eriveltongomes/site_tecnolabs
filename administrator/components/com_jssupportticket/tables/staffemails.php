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

class TableStaffMail extends JTable {

    var $id = null;
    var $from = null;
    var $to = null;
    var $message = null;
    var $subject = null;
    var $isread = null;
    var $deleteby = null;
    var $replytoid = null;
    var $status = null;
    var $created = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_staff_mail', 'id', $db);
    }

    /**
     * Validation
     * 
     * @return boolean true if buffer is valid
     * 
     */
    function check() {
        return true;
    }

}

?>
