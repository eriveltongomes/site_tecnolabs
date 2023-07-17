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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class jssupportticketViewReports extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;
		if($layoutName == 'staffreports'){

	        $date_start = JFactory::getApplication()->input->getDate('date_start');
	        $date_end = JFactory::getApplication()->input->getDate('date_end',null);
	        
	        $jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
	        if($jsresetbutton == 1){
	        	$date_start = null;
	        	$date_end = null;
	        }

            // $date_start= $mainframe->getUserStateFromRequest( $option.'date_start', 'date_start',	'',	'string');
            // $date_end= $mainframe->getUserStateFromRequest( $option.'date_end', 'date_end',	'',	'string');

            $permission = 'View Staff Reports';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
	            $results = $this->getJSModel('reports')->getStaffReportsFE($date_start ,$date_end ,  $limitstart, $limit);
	            $total = $results[1];
	            $this->result = $results;
	            $pagination = new JPagination($total, $limitstart, $limit);
				$this->pagination = $pagination;
	    	}
			$this->per_granted = $per_granted;
		
		}elseif($layoutName == 'staffdetailreport'){

	        $staffid = JFactory::getApplication()->input->get('id');
	        // $date_start = JFactory::getApplication()->input->get('date_start');
	        // $date_end = JFactory::getApplication()->input->get('date_end');

            $permission = 'View Staff Reports';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
	            $this->staffid=$staffid;
	            $results = $this->getJSModel('reports')->getStaffDetailReportByStaffId( $staffid );
	            $this->result = $results;
	    	}
			$this->per_granted = $per_granted;
		}elseif($layoutName == 'departmentreports'){

            $permission = 'View Department Reports';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
	            $results = $this->getJSModel('reports')->getDepartmentReportsFE( $limitstart , $limit );
	            $total = $results[1];
	            $pagination = new JPagination($total, $limitstart, $limit);
	            $this->result = $results;
				$this->pagination = $pagination;

	    	}
			$this->per_granted = $per_granted;

		}

		require_once(JPATH_COMPONENT."/views/reports/reports_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
