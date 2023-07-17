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

class TableGroups extends JTable {

    var $id = null;
    var $name = null;
    var $cancreatetickets = null;
    var $canedittickets = null;
    var $candeletetickets = null;
    var $canclosetickets = null;
    var $cantransfertickets = null;
    var $canbanemail = null;
    var $canmessagekb = null;
    var $status = null;
    var $created = null;
    var $updated = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_groups', 'id', $db);
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
