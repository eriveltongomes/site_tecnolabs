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

class JSSupportTicketViewMail extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		$per = $user->checkUserPermission('Allow Mail System');
		$uid = $user->getId();
		if($layoutName == 'inbox'){
			/*$subject = JFactory::getApplication()->input->get('filter_subject');
			$read = JFactory::getApplication()->input->get('read');
			$startdate = JFactory::getApplication()->input->get('filter_start_date');
			$enddate = JFactory::getApplication()->input->get('filter_end_date');*/

			$subject = $mainframe->getUserStateFromRequest( $option.'filter_subject', 'filter_subject',	'',	'string' );
			$read = $mainframe->getUserStateFromRequest( $option.'read', 'read',	'',	'int' );
			$startdate = $mainframe->getUserStateFromRequest( $option.'filter_start_date', 'filter_start_date',	'',	'string' );
			$enddate = $mainframe->getUserStateFromRequest( $option.'filter_end_date', 'filter_end_date',	'',	'string' );
			$jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
			if($jsresetbutton == 1){
				$mainframe->setUserState($option.'filter_start_date',null);
				$mainframe->setUserState($option.'filter_end_date',null);
				$startdate = null;
				$enddate = null;
			}

			$result = $this->getJSModel('mail')->getInboxMessages($subject,$startdate,$enddate,$read,$uid,$limitstart,$limit);
			$total = $result[2];
			$this->messages=$result[0];
			if(isset($result[1])) $this->unreadmessages=$result[1];
			if(isset($result[2])) $this->totalinboxmessages=$result[2];
			if(isset($result[3])) $this->outboxmessages=$result[3];
			if(isset($result[4])) $this->lists=$result[4];
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->pagination = $pagination;
		}elseif($layoutName == 'message'){
			$id = JFactory::getApplication()->input->get('id');
			$result = $this->getJSModel('mail')->getMessage($id,$uid);
			$this->message=$result[0];
			$this->unreadmessages=$result[1];
			$this->totalinboxmessages=$result[2];
			$this->outboxmessages=$result[3];
			$this->replytoid=$result[4];
			$this->replies=$result[5];
			//$result[0] = $message;
			//$result[1] = $unreadmessages;
			//$result[2] = $total;
			//$result[3] = $outboxmessages;
			//$result[4] = $message->id;
			//$result[5] = $replies;
		}elseif($layoutName == 'outbox'){
			/*$subject = JFactory::getApplication()->input->get('filter_subject');
			$startdate = JFactory::getApplication()->input->get('filter_start_date');
			$enddate = JFactory::getApplication()->input->get('filter_end_date');	*/
			$subject =  $mainframe->getUserStateFromRequest( $option.'filter_subject', 'filter_subject',	'',	'string' );
			$startdate =  $mainframe->getUserStateFromRequest( $option.'filter_start_date', 'filter_start_date',	'',	'string' );
			$enddate =  $mainframe->getUserStateFromRequest( $option.'filter_end_date', 'filter_end_date',	'',	'string' );
			$jsresetbutton = JFactory::getApplication()->input->get('jsresetbutton',0);
			if($jsresetbutton == 1){
				$mainframe->setUserState($option.'filter_start_date',null);
				$mainframe->setUserState($option.'filter_end_date',null);
				$startdate = null;
				$enddate = null;
			}
			
			$result = $this->getJSModel('mail')->getOutboxMessagesForOutbox($subject,$startdate,$enddate,$uid,$limitstart,$limit);
			$total = $result[3];
			if(isset($result[0])) $this->messages=$result[0];
			if(isset($result[1])) $this->unreadmessages=$result[1];
			if(isset($result[2])) $this->totalinboxmessages=$result[2];
			if(isset($result[3])) $this->outboxmessages=$result[3];
			if(isset($result[4])) $this->lists=$result[4];
			$pagination = new JPagination( $total, $limitstart, $limit );
			$this->pagination = $pagination;
		}elseif($layoutName == 'formmessage'){
			$result = $this->getJSModel('mail')->getFormData($uid);
			$total = $result[2];
			$this->lists=$result[0];
			$this->unreadmessages=$result[1];
			$this->totalinboxmessages=$result[2];
			$this->outboxmessages=$result[3];
		}
		$this->per_granted = $per;
		require_once(JPATH_COMPONENT."/views/mail/mail_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
