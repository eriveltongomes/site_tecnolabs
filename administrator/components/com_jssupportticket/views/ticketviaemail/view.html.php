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
// Options button.
if (JFactory::getUser()->authorise('core.admin', 'com_jssupportticket')) {
    JToolBarHelper::preferences('com_jssupportticket');
}

class JSSupportticketViewTicketViaEmail extends JSSupportTicketView
{
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		JToolBarHelper::title(JText::_('Ticket via email'));
		if($layoutName == 'ticketviaemail'){
			JToolBarHelper::addNew('addnewticketviaemail');
			$mainframe->setUserState( $option.'.limitstart', $limitstart );
			$searchemail = JFactory::getApplication()->input->getString('filter_email');
			$result = $this->getJSModel('ticketviaemail')->getAllTicketsviaEmail($searchemail,$limitstart,$limit);
			$total = $result[1];
			$this->result = $result[0];
			$pagination = new JPagination($total, $limitstart, $limit);
			$this->pagination = $pagination;
		}elseif($layoutName == 'ticketviaemailform'){
			JToolBarHelper::save('saveticketviaemailsave','Save Ticket via email');
           	JToolBarHelper::save2new('saveticketviaemailandnew');
           	JToolBarHelper::save('saveticketviaemail');
			$id = JFactory::getApplication()->input->get('cid');
			$data = $this->getJSModel('ticketviaemail')->getTicketViaEmailforFormbyId($id); 
			$isNew = true;
			if (isset($id) && ($id <> '' || $id <> 0)) $isNew = false;
           	$text = $isNew ? JText::_('Add') : JText::_('Edit');
           	JToolBarHelper::title(JText::_('Ticket via email') . ': <small><small>[ ' . $text . ' ]</small></small>');
           	if ($isNew)	JToolBarHelper::cancel('cancelticketviaemail'); else JToolBarHelper::cancel('cancelticketviaemail', 'Close');
           	if(isset($data[0])) $this->tve = $data[0];
		}
		parent::display($tpl);
	}
}
?>
