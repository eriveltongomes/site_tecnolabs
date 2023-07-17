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

class JSSupportticketViewPremade extends JSSupportTicketView
{
	function display($tpl = null)
	{
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Tickets'));
        if($layoutName == 'formpremade'){
            $cids = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id= $cids[0];
            $result=$this->getJSModel('premade')->getPremadeForForm($c_id);
            $this->premadeid = $c_id;
            if(isset($result[0])) $this->premade = $result[0];
            $this->lists = $result[1];
            $isNew = true;
            if ( isset($result[0]->id) ) $isNew = false;
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('Premade Messages').'<small><small> ['.$text.']</small></small>' );
            JToolBarHelper::save('savepremadesave','Save Premade Message');
            JToolBarHelper::save2new('savepremadeandnew');
            JToolBarHelper::save('savepremade');
            if ($isNew)	JToolBarHelper::cancel('cancelpremade'); else JToolBarHelper::cancel('cancelpremade', 'Close');
            JHTML::_('behavior.formvalidator');
        }elseif($layoutName == 'departmentspremade'){
            JToolBarHelper::title(JText::_('Premade Messages') );
            $title = JFactory::getApplication()->input->getString('filter_dp_title');
            $departmentid = $mainframe->getUserStateFromRequest($option.'filter_dp_departmentid', 'filter_dp_departmentid', '', 'int');
            $statusid = $mainframe->getUserStateFromRequest($option.'filter_dp_statusid', 'filter_dp_statusid', '', 'int');
            $result = $this->getJSModel('premade')->getAllDepartmentsPremade($title,$departmentid, $statusid ,$limitstart, $limit);
            $total = $result[1];
            if ( $total <= $limitstart ) $limitstart = 0;
            $pagination = new JPagination( $total, $limitstart, $limit );
            JToolBarHelper::addNew('editpremade');
            JToolBarHelper::editList('editpremade');
            JToolBarHelper::deleteList('Are you sure to delete','removedepartmentpremade');
            $this->premade = $result[0];
            $this->lists = $result[2];
            $this->pagination = $pagination;
        }
        parent::display($tpl);
	}
}
?>
