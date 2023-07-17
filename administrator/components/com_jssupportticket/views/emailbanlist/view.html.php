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

class JSSupportticketViewEmailbanlist extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Ban Emails'));
        if($layoutName == 'emailbanlists'){
            JToolBarHelper::addNew('addnewemail');
            JToolBarHelper::editList('addnewemail');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deleteemail');
            $mainframe->setUserState( $option.'.limitstart', $limitstart );
            $searchemail = JFactory::getApplication()->input->getString('filter_email');
            $result = $this->getJSModel('emailbanlist')->getAllEmails($searchemail,$limitstart,$limit);
            $total = $result[1];
            $this->emails = $result[0];
            $this->searchemail = $result[2]['searchemail'];
            $pagination = new JPagination($total, $limitstart, $limit);
	        $this->pagination = $pagination;
        }elseif($layoutName == 'formemailbanlist'){
			JToolBarHelper::save('banemailsave','Ban Email');
			JToolBarHelper::save2new('banemailandnew');
			JToolBarHelper::save('banemail');

			$c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
			$c_id = $c_id[0];
			$isNew = true;
			if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
			$text = $isNew ? JText::_('Add') : JText::_('Edit');
			JToolBarHelper::title(JText::_('Ban Email') . ': <small><small>[ ' . $text . ' ]</small></small>');
			if ($isNew) JToolBarHelper::cancel('cancelemail');	else JToolBarHelper::cancel('cancelemail', 'Close');

			if(isset($c_id)){
			$result = $this->getJSModel('emailbanlist')->getFormData($c_id);
			$this->email = $result[0];

			}
		}
	
		parent::display($tpl);
	}
}
?>
