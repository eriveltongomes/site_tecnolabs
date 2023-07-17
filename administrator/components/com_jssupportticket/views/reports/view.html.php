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

class JSSupportticketViewReports extends JSSupportTicketView
{
	function display($tpl = null){

        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        if($layoutName == 'reports'){
            JToolBarHelper::title(JText::_('Reports'));
        }elseif($layoutName == 'overallreport'){
            JToolBarHelper::title(JText::_('Overall Report'));
            $result = $this->getJSModel('reports')->getOverallReportData();
            $this->result=$result;
        }elseif($layoutName == 'staffreport'){
            JToolBarHelper::title(JText::_('Staff Reports'));
            $result = $this->getJSModel('reports')->getStaffReports();
            $this->result=$result;
        }elseif($layoutName == 'userreport'){
            JToolBarHelper::title(JText::_('User Reports'));
            $result = $this->getJSModel('reports')->getUserReports();
            $this->result=$result;
        }elseif($layoutName == 'userdetailreport'){
            JToolBarHelper::title(JText::_('User Reports'));
            $id = JFactory::getApplication()->input->get('id');
            $result = $this->getJSModel('reports')->getStaffDetailReportByUserId($id);
            $this->result=$result;
        }elseif($layoutName == 'staffdetailreport'){
            JToolBarHelper::title(JText::_('Staff Reports'));
            $id = JFactory::getApplication()->input->get('id');
            $result = $this->getJSModel('reports')->getStaffDetailReportByStaffId($id);
            $this->result=$result;
        }elseif($layoutName == 'departmentreport'){
            JToolBarHelper::title(JText::_('Department Reports'));
            $result = $this->getJSModel('reports')->getDepartmentReports();
            $this->result=$result;
        }elseif($layoutName == 'departmentdetailreport'){
            JToolBarHelper::title(JText::_('Department Report'));
            $id = JFactory::getApplication()->input->get('id');
            $result = $this->getJSModel('reports')->getDepartmentDetailReportById($id);
            $this->result=$result;
        }elseif($layoutName == 'export'){
            JToolBarHelper::title(JText::_('Export'));
            $id = JFactory::getApplication()->input->get('id');
            $result = $this->getJSModel('reports')->getFieldsForExport();
            $this->lists=$result;
        }elseif($layoutName == 'satisfactionreport'){
            JToolBarHelper::title(JText::_('Satisfaction Report'));
            $result = $this->getJSModel('reports')->getSatisfactionReport();
            $this->satisfaction=$result;
        }elseif($layoutName == 'stafftimereport'){
            JToolBarHelper::title(JText::_('Staff Time Report'));
            $id = JFactory::getApplication()->input->get('id');
            $result = $this->getJSModel('reports')->getStaffTimeReport($id);
            $this->result=$result;
        }

        parent::display($tpl);
    }
}
?>
