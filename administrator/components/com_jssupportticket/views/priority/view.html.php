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

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewPriority extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Priorities'));
        if($layoutName == 'priorities'){
            JToolBarHelper::makeDefault('makeprioritydefault');
            JToolBarHelper::addNew('addnewpriority');
            JToolBarHelper::editList('addnewpriority');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deletepriority');
            $searchpriority = $mainframe->getUserStateFromRequest( $option.'filter_priority', 'filter_priority', '', 'string' );
            $result = $this->getJSModel('priority')->getAllPriorities($searchpriority,$limitstart,$limit);
            $this->priority = $result[0];
            $this->searchpriority = $result[2];
            $total = $result[1];            
            $pagination = new JPagination($total, $limitstart, $limit);
            $this->pagination = $pagination;
        }elseif($layoutName == 'formpriority'){
            JToolBarHelper::save('saveprioritysave','Save Priority');
            JToolBarHelper::save2new('savepriorityandnew');
            JToolBarHelper::save('savepriority');

            $c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id = $c_id[0];
            $result = $this->getJSModel('priority')->getFormData($c_id);
            $isNew = true;
            if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
            if ($isNew) JToolBarHelper::cancel('cancelpriority'); else JToolBarHelper::cancel('cancelpriority', 'Close');
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('Priority') . ': <small><small>[ ' . $text . ' ]</small></small>');
            if(isset($result[0])) $this->priority = $result[0];
            
        }
        parent::display($tpl);
	}
}
?>
