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

class JSSupportticketViewEmail extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Emails'));
        if($layoutName == 'emails'){
            JToolBarHelper::addNew('addnewemail');
            JToolBarHelper::editList('addnewemail');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deleteemail');
            $mainframe->setUserState( $option.'.limitstart', $limitstart );
            $searchemail = JFactory::getApplication()->input->getString('filter_email');
            $searchtype = $mainframe->getUserStateFromRequest( $option.'filter_autoresponcetype', 'filter_autoresponcetype',	'',	'string' );
            $result = $this->getJSModel('email')->getAllEmails($searchemail, $searchtype,$limitstart,$limit);
            $total = $result[1];
            $this->emails = $result[0];
            $this->lists = $result[2];
            $pagination = new JPagination($total, $limitstart, $limit);
	        $this->pagination = $pagination;
        }elseif($layoutName == 'formemail'){
			JToolBarHelper::save('saveemailsave','Save Email');
			JToolBarHelper::save2new('saveemailandnew');
			JToolBarHelper::save('saveemail');

			$c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
			$c_id = $c_id[0];
			$result = $this->getJSModel('email')->getFormData($c_id);
			$isNew = true;
			if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
			$text = $isNew ? JText::_('Add') : JText::_('Edit');
			JToolBarHelper::title(JText::_('Email') . ': <small><small>[ ' . $text . ' ]</small></small>');
			if ($isNew)	JToolBarHelper::cancel('cancelemail'); else JToolBarHelper::cancel('cancelemail', 'Close');

			$this->lists = $result[2];
			$this->email = $result[0];
		}

		
		parent::display($tpl);
	}
}
?>
