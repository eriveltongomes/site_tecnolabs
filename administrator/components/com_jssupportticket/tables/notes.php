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

class TableNotes extends JTable {

    var $id = null;
    var $ticketid = null;
    var $staffid = null;
    var $title = null;
    var $note = null;
    var $from = null;
    var $status = null;
    var $created = null;
    var $filename = null;
    var $filesize = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_notes', 'id', $db);
    }

    /**
     * Validation
     * 
     * @return boolean true if buffer is valid
     * 
     */
    function check() {
        // if (trim( $this->note) == '') {
        //   $this->_error = "Note cannot be empty.";
        //   return false;
        // }
        return true;
    }

}

?>
