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

class TableKnowledgebaseCategory extends JTable {

    var $id = null;
    var $name = null;
    var $parentid = null;
    var $totalarticles = null;
    var $hits = null;
    var $metadesc = null;
    var $metakey = null;
    var $downloads = 0;
    var $announcement = 0;
    var $faqs = 0;
    var $kb = 0;
    var $staffid = null;
    var $ordering = null;
    var $type = null;
    var $status = 0;
    var $created = null;

    function __construct(&$db) {
        parent::__construct('#__js_ticket_categories', 'id', $db);
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
