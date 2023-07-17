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

class TablePrivateCredentials extends JTable {

    var $id = '';
	var $ticketid = '';
	var $uid = '';
	var $data = '';
	var $status = '';
	var $created = '';

    function __construct(&$db) {
        parent::__construct('#__js_ticket_privatecredentials', 'id', $db);
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
