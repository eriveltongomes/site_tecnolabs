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

class TableAnnouncements extends JTable {

    var $id = null;
    var $categoryid = null;
    var $staffid = null;
    var $title = null;
    var $description = null;
    var $type = null;
    var $status = 0;
    var $created = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_announcements', 'id', $db);
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
