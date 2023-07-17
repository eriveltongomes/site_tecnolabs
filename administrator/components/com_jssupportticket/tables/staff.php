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

class TableStaff extends JTable {

    var $id = null;
    var $uid = null;
    var $roleid = null;
    var $groupid = null;
    var $departmentid = null;
    var $username = null;
    var $firstname = null;
    var $lastname = null;
    var $email = null;
    var $phone = null;
    var $phoneext = null;
    var $mobile = null;
    var $signature = null;
    var $isadmin = null;
    var $isvisible = 0;
    var $onvocation = 0;
    var $appendsignature = null;
    var $autorefreshrate = null;
    var $status = 0;
    var $created = null;
    var $lastlogin = null;
    var $update = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_staff', 'id', $db);
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
