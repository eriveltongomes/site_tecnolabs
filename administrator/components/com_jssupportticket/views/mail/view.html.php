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

class JSSupportticketViewMail extends JSSupportTicketView
{
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		JToolBarHelper::title(JText::_('Mail'));
		$user = JSSupportTicketCurrentUser::getInstance();
		$uid = $user->getId();
		if($layoutName == 'inbox'){
			JToolBarHelper::title(JText::_('Message') . ' <small><small>[ ' . JText::_('inbox') . ' ]</small></small>');
			$path = JPATH_BASE;
			$path = substr($path, 0,strlen($path)-14); //remove administrator
			$path = "../components/com_jssupportticket/images/defaulticon.png"; 
			JToolBarHelper::custom( 'markasread', '/images/defaulticon.png', 'save', 'Mark as read', true, false );
			JToolBarHelper::custom( 'markasunread', '/images/defaulticon.png', 'save', 'Mark as unread', true, false );
	        JToolBarHelper::deleteList('JS_ARE_YOU_SURE_DELETE_MESSAGE','removemessage');

	        $subject = JFactory::getApplication()->input->getString('filter_subject');
	        $read = JFactory::getApplication()->input->get('read');
	        $startdate = JFactory::getApplication()->input->get('filter_start_date');
	        $enddate = JFactory::getApplication()->input->get('filter_end_date');
			$result = $this->getJSModel('mail')->getInboxMessages($subject,$startdate,$enddate,$read,$uid,$limitstart,$limit);
			$total = $result[2];
			$isstaff = $this->getJSModel('staff')->isStaffMember($uid);
			$this->messages=$result[0];
			$this->unreadmessages=$result[1];
			$this->totalinboxmessages=$result[2];
			$this->outboxmessages=$result[3];
			$this->lists=$result[4];
			$this->isstaff=$isstaff;
	        $pagination = new JPagination( $total, $limitstart, $limit );
			$this->pagination = $pagination;
		}elseif($layoutName == 'message'){
			JToolBarHelper::title(JText::_('Message'));
            JToolBarHelper::cancel('cancelmessage');
            $cids = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id= $cids[0];
            $result = $this->getJSModel('mail')->getMessage($c_id,$uid);
            $this->message=$result[0];
			$this->unreadmessages=$result[1];
			$this->totalinboxmessages=$result[2];
			$this->outboxmessages=$result[3];
			$this->replytoid=$result[4];
			$this->replies=$result[5];
			$this->uid=$uid;
		}elseif($layoutName == 'outbox'){
			JToolBarHelper::title(JText::_('Message') . ' <small><small>[ ' . JText::_('Outbox') . ' ]</small></small>');
            JToolBarHelper::deleteList('JS_ARE_YOU_SURE_DELETE_MESSAGE','removemessage');
            
            $subject = JFactory::getApplication()->input->getString('filter_subject');
            $startdate = JFactory::getApplication()->input->get('filter_start_date');
            $enddate = JFactory::getApplication()->input->get('filter_end_date');
            
			$result = $this->getJSModel('mail')->getOutboxMessagesForOutbox($subject,$startdate,$enddate,$uid,$limitstart,$limit);
			$total = $result[2];
			$isstaff = $this->getJSModel('staff')->isStaffMember($uid);
			$this->messages=$result[0];
			$this->unreadmessages=$result[1];
			$this->totalinboxmessages=$result[2];
			$this->outboxmessages=$result[3];
			$this->lists=$result[4];
			$this->isstaff=$isstaff;

            $pagination = new JPagination( $total, $limitstart, $limit );
			$this->pagination = $pagination;
		}elseif($layoutName == 'formmessage'){
			JToolBarHelper::title(JText::_('Message') . ' <small><small>[ ' . JText::_('Compose') . ' ]</small></small>');
			JToolBarHelper::custom( 'savemessage','', '', 'Send', false, false );
	        JToolBarHelper::cancel('cancelmessage');
			$result = $this->getJSModel('mail')->getFormData($uid);

			$total = $result[2];
			$this->lists=$result[0];
			$this->unreadmessages=$result[1];
			$this->totalinboxmessages=$result[2];
			$this->outboxmessages=$result[3];
			$this->uid=$uid;
		}
		parent::display($tpl);
	}
}
?>
