<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerExport extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
    }

    function getoverallexport() {

        $return_value = $this->getJSModel('export')->setOverallExport();

        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Overall Reports');
            $name = 'export-overall-report';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }

    function getstaffmemberexport() {

        $return_value = $this->getJSModel('export')->setStaffMemberExport();
        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Staff Members Report');
            $name = 'export-all-staff-report';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }
    
    function getstaffmemberexportbystaffid() {
        $return_value = $this->getJSModel('export')->setStaffMemberExportByStaffId();
        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Staff Members Report');
            $name = 'export-staff-report';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }
    
    function getusersexport() {
        $return_value = $this->getJSModel('export')->setUsersExport();
        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Staff Members Report');
            $name = 'export-all-user-report';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }
    
    function getuserexportbyuid() {
        $return_value = $this->getJSModel('export')->setUserExportByuid();
        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Staff Members Report');
            $name = 'export-user-report';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }

    function getdepartmentmemberexportbydepartmentid() {
        $return_value = $this->getJSModel('export')->setDepartmentExportByDepartmentId();
        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Departent Report');
            $name = 'department-report';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }
    function getdepartmentexport() {

        $return_value = $this->getJSModel('export')->setDepartmentExport();
        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Departments Report');
            $name = 'all-departments-report';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }
    
    function getticketsexport() {
        $return_value = $this->getJSModel('export')->setTicketsExport();
        if (!empty($return_value)) {
            // Push the report now!
            $msg = JText::_('Tickets');
            $name = 'tickets-data';
            header("Content-type: application/octet-stream");
            header("Content-Disposition: attachment; filename=" . $name . ".xls");
            header("Pragma: no-cache");
            header("Expires: 0");
            header("Lacation: excel.htm?id=yes");
            print $return_value;
            exit;
        }
        die();
    }
}

?>
