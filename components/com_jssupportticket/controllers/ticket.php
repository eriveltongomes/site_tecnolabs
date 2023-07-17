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

defined ('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.controller');

class JSSupportTicketControllerticket extends JSSupportTicketController{

	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}

	function saveTicket() {
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid =  JFactory::getApplication()->input->get('Itemid');
		$data = JFactory::getApplication()->input->post->getArray();
		if($data['id'] <> '')
			$id = $data['id'];
		$result = $this->getJSModel('ticket')->storeTicket($data);
		$user = JSSupportTicketCurrentUser::getInstance();
		if($user->getIsStaff()){
			$link = 'index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff&id='.$id.'&Itemid='.$Itemid;
		}elseif(!$user->getIsGuest()){
			$link = 'index.php?option=com_jssupportticket&c=ticket&layout=mytickets&id='.$id.'&Itemid='.$Itemid;
		}elseif($user->getIsGuest()){
			if($result == SAVE_ERROR || $result == MESSAGE_EMPTY || $result == INVALID_CAPTCHA){
				JFactory::getApplication()->setUserState('com_jssupportticket.data',$data);
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid='.$Itemid;
			}else{
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=visitorsuccessmessage&Itemid='.$Itemid;
			}
		}

		if($result == SAVE_ERROR || $result == MESSAGE_EMPTY || $result == TICKET_DUPLICATE){
			JFactory::getApplication()->setUserState('com_jssupportticket.data',$data);
			$link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid='.$Itemid;
		}
        $msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect(JRoute::_($link , false), $msg);
    }

    function actionticket() {
    	JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $ticket = $this->getJSModel('ticket');
        $Itemid = JFactory::getApplication()->input->get('Itemid');
        $data = JFactory::getApplication()->input->post->getArray();
        $action = $data['callfrom'];
        $user = JSSupportTicketCurrentUser::getInstance();
        switch ($action) {
        	case 'postreply':
                $data['responce'] = JFactory::getApplication()->input->get('responce', '', 'raw');
                $result = $ticket->storeTicketReplies($data['id'],$data['responce'], $data['created'], $data);
                $msg = JSSupportTicketMessage::getMessage($result,'REPLY');
                if($user->getIsStaff()){
                	$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                }else{
                	if($user->getIsGuest()){
	                	$session = JFactory::getApplication()->getSession();
	                	$ticketid = $session->get('userticketid','');
	                	$email = $session->get('useremail','');
	                	$token = $ticketid.','.$email;
                		$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&jsticket='. base64_encode($token) .'&Itemid='.$Itemid;
                	}else{ // login user
                		$ticketid = $data['id'];
                		$email = $data['email'];
                		$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$ticketid.'&Itemid='.$Itemid;
                	}
                }
                $this->setRedirect(JRoute::_($link , false), $msg);
                break;
            case 'internalnote':
                $data['internalnote'] = JFactory::getApplication()->input->get('internalnote', '', 'raw');
                $result = $ticket->storeTicket_InternalNote($data['id'],$data['notetitle'], $data['internalnote'], $data['created'], $data);
                $msg = JSSupportTicketMessage::getMessage($result,'INTERNAL_NOTE');
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                $this->setRedirect(JRoute::_($link , false), $msg);
                break;
            case 'departmenttransfer':
                $data['departmenttranfer'] = JFactory::getApplication()->input->get('departmenttranfer', '', 'raw');
                $result = $ticket->ticketDepartmentTransfer($data['id'], $data['departmentid'], $data['departmenttranfer'], $data['created'], $data);
                $msg = JSSupportTicketMessage::getMessage($result,'DEPARTMENT');
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                $this->setRedirect(JRoute::_($link , false), $msg);
                break;
            case 'stafftransfer':
                $data['assigntostaffnote'] = JFactory::getApplication()->input->get('assigntostaffnote', '', 'raw');
                $result = $ticket->ticketStaffTransfer($data['id'],$data['staffid'], $data['assigntostaffnote'], $data['created'], $data);
                $msg = JSSupportTicketMessage::getMessage($result,'STAFF');
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                $this->setRedirect(JRoute::_($link , false), $msg);
                break;
            case 'action':
                switch ($data['callaction']) {
                    case 1://change priority
                        $result = $ticket->changeTicketPriority($data['id'], $data['priorityid'], $data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'PRIORITY');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 10: //change ticket status as inprogress=4
                        $result = $ticket->ticketMarkInprogress($data['id'],$data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'MARK_IN_PROGRESS');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 2: //ticket relase
                        $result = $ticket->ticketRelase($data['id'], $data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'RELEASE');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 3: //ticket close
                        $result = $ticket->ticketClose($data['id'], $data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'CLOSE');
		                if($user->getIsStaff() || (!$user->getIsGuest())){
							$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
		                }elseif($user->getIsGuest()){
		                	$session = JFactory::getApplication()->getSession();
		                	$ticketid = $session->get('userticketid','');
		                	$email = $session->get('useremail','');
		                	$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&ticketid='.$ticketid.'&email='.$email.'&Itemid='.$Itemid;
		     			}
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 5: //ticket delete
                        $result = $ticket->delete_Ticket($data['id']);
                        $msg = JSSupportTicketMessage::getMessage($result,'DELETE');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff';
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 6: // markoverdue
                        $result = $ticket->markOverDueTicket($data['id'], $data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'MARK_OVERDUE');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 8: //reopened ticket
                        $result = $ticket->reopenTicket($data['id'], $data['lastreply']);
                        $msg = JSSupportTicketMessage::getMessage($result,'REOPEN');
		                if($user->getIsStaff() || (!$user->getIsGuest())){
							$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
		                }else{
		                	$session = JFactory::getApplication()->getSession();
		                	$ticketid = $session->get('userticketid','');
		                	$email = $session->get('useremail','');
		                	$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&ticketid='.$ticketid.'&email='.$email.'&Itemid='.$Itemid;
		                }
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 4: //ban email
                        $result = $this->getJSModel('emailbanlist')->banEmailTicket($data['email'],$data['created'], $data['id'], 1);
                        $msg = JSSupportTicketMessage::getMessage($result,'BAN_EMAIL');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                    break;
                    case 9: //unban email
                        $result = $this->getJSModel('emailbanlist')->unbanEmailTicket($data['email'], $data['id']);
                        $msg = JSSupportTicketMessage::getMessage($result,'Unban Email');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 7: //banemail and close ticket
                        $result = $ticket->banEmailAndCloseTicket($data['id'], $data['created'],$data['email']);
                        $msg = $result;
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 11: //lock ticket
						$result = $ticket->lockTicket($data['id']);
						$msg = JSSupportTicketMessage::getMessage($result,'LOCK');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
						$this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 12: //Unlock ticket
						$result = $ticket->unlockTicket($data['id']);
						$msg = JSSupportTicketMessage::getMessage($result,'UNLOCK');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
						$this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                    case 13: //Unlock ticket
						$result = $ticket->unMarkOverDueTicket($data['id'], $data['created']);
                        $msg = JSSupportTicketMessage::getMessage($result,'UN_MARK_OVERDUE');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['id'].'&Itemid='.$Itemid;
                        $this->setRedirect(JRoute::_($link , false), $msg);
                        break;
                }
                break;
        }
    }

    function savemessage(){
    	JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$data = JFactory::getApplication()->input->post->getArray();
		$Itemid =  JFactory::getApplication()->input->get('Itemid');
		$ticketid = $data['ticketid'];
		$model = $this->getJSModel('ticket');
		$action = $data['callfrom'];
		switch($action){
			case 'savemessage':
				$result = $model->storeUserReplies();
				$msg = JSSupportTicketMessage::getMessage($result,'MESSAGE');
				$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$ticketid.'&email='.$data['email'].'&Itemid='.$Itemid;
				$this->setRedirect(JRoute::_($link , false), $msg);
                break;
			case 'action':
				switch ($data['callaction']){
					case 1://change priority
						$result = $model->changeTicketPriority($data['id'],$data['priorityid'],$data['created']);
						$msg = JSSupportTicketMessage::getMessage($result,'PRIORITY');
						$link = 'index.php?option=com_jssupportticket&c=staff&layout=ticketdetail&cid='.$data['id'].'&Itemid='.$Itemid;
						$this->setRedirect(JRoute::_($link , false), $msg);
						break;
					case 3:
						$result = $model->ticketClose($data['ticketid'],$data['created']);
						$msg = JSSupportTicketMessage::getMessage($result,'CLOSE');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['ticketid'].'&email='.$data['email'].'&Itemid'.$Itemid;
						$this->setRedirect(JRoute::_($link , false), $msg);
						break;
					case 8:
						$result = $model->reopenTicket($data['ticketid'],$data['lastreply']);
						$msg = JSSupportTicketMessage::getMessage($result,'REOPEN');
						$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['ticketid'].'&email='.$data['email'].'&Itemid'.$Itemid;
						$this->setRedirect(JRoute::_($link , false), $msg);
						break;
				}
			break;
		}
	}

	function getpremadeforinternalnote(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$val = JFactory::getApplication()->input->get( 'val');
		$model = $this->getJSModel('premade') ;
		$returnvalue = $model->getPremadeForInternalNote($val);
		echo $returnvalue;
		$mainframe->close();
	}

	function listhelptopicandpremade(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$val=JFactory::getApplication()->input->get( 'val');
		$model = $this->getJSModel('helptopic');
		$returnvalue = $model->listHelpTopicAndPremade($val);
		echo json_encode($returnvalue);
		$mainframe->close();
	}

	function saveresponceajax()  {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$val = json_decode(JFactory::getApplication()->input->get('val',"","string"),true);
		$id = $val[0];
		$responce = $val[1];
		$result = $this->getJSModel('ticket')->saveResponceAJAX($id,$responce);
		$msg = JSSupportTicketMessage::getMessage($result,'MESSAGE');
		if($result == SAVED){
			$result = 1;
		}else{
			$result = '<font color="red">'.$msg.'</font>';
		}
		echo $result;
		$mainframe->close();
	}

	function editresponce()  {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$id = JFactory::getApplication()->input->get('id');
		$result = $this->getJSModel('ticket')->editResponceAJAX($id);
		echo $result;
		$mainframe->close();
	}

    function downloadbyname(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id');
        $name = JFactory::getApplication()->input->getString('name');
        $this->getJSModel('ticket')->getDownloadAttachmentByName( $name, $id );

        JFactory::getApplication()->close();
    }


	function deleteresponceajax() {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		global $mainframe;
		$mainframe = JFactory::getApplication();
		$id = JFactory::getApplication()->input->get('id');
		$result = $this->getJSModel('ticket')->deleteResponceAJAX($id);
		$msg = JSSupportTicketMessage::getMessage($result,'MESSAGE');
		if ($result == DELETED){
			$result = '<font color="green">'.$msg.'</font>';
		}elseif($result == PERMISSION_ERROR){
			$result = '<font color="red">'.$msg.'</font>';
		}else{
			$result = '<font color="red">'.$msg.'</font>';
		}
		echo $result;
		$mainframe->close();
	}

	function listhelptopic(){
		JFactory::getApplication();
		$val=JFactory::getApplication()->input->get( 'val');
		$returnvalue = $this->getJSModel('helptopic')->listHelpTopic($val);
		echo json_encode($returnvalue);
		JFactory::getApplication()->close();
	}

	function getdownloadbyid(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$id = JFactory::getApplication()->input->get('id');
		$this->getJSModel('ticket')->getDownloadAttachmentById($id);
		JFactory::getApplication()->close();
	}
	function downloadall(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
    	$this->getJSModel('ticket')->getAllDownloadFiles();
    	JFactory::getApplication()->close();
    }
    function downloadallforreply(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
    	$this->getJSModel('ticket')->getAllReplyDownloadsFiles();
    	JFactory::getApplication()->close();
    }

    function datafordepandantfield() {
    	JSession::checkToken( 'get' ) or die( 'Invalid Token' );
        $val = JFactory::getApplication()->input->get('fvalue','','string');
        $childfield = JFactory::getApplication()->input->get('child');
        $result = $this->getJSModel('userfields')->dataForDepandantField( $val , $childfield);
        $result = json_encode($result);
        echo $result;
        JFactory::getApplication()->close();
    }

	function getReplyDataByID() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('ticket')->getReplyDataByID();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function getLastRepyforMergeTicket(){
    	$returnvalue = $this->getJSModel('ticket')->getLastRepyforMergeTicket();
    	echo $returnvalue;
    	JFactory::getApplication()->close();
    }

	function getTimeByReplyID() {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$returnvalue = $this->getJSModel('ticket')->getTimeByReplyID();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

	function saveeditedtime() {
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid =  JFactory::getApplication()->input->get('Itemid');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $this->getJSModel('ticket')->editTime($data);
		$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['reply-tikcetid'].'&Itemid='.$Itemid;
		$msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect(JRoute::_($link , false), $msg);
    }

	function saveeditedtimenote() {
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid =  JFactory::getApplication()->input->get('Itemid');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $this->getJSModel('ticket')->editTimeForNote($data);
		$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['note-tikcetid'].'&Itemid='.$Itemid;
		$msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect(JRoute::_($link , false), $msg);
    }

    function saveeditedreply() {
    	JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $Itemid =  JFactory::getApplication()->input->get('Itemid');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $this->getJSModel('ticket')->editReply($data);
		$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['reply-tikcetid'].'&Itemid='.$Itemid;
        $this->setRedirect(JRoute::_($link , false));
    }

    function getTicketsForMerging() {
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
    	$returnvalue = $this->getJSModel('ticket')->getTicketsForMerging();
        echo json_encode($returnvalue);
        JFactory::getApplication()->close();
    }

    function getLatestReplyForMerging(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
    	$returnvalue = $this->getJSModel('ticket')->getLatestReplyForMerging();
        echo json_encode($returnvalue);
        JFactory::getApplication()->close();
    }

    function mergeticket() {
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		// $data = JRequest::get('post',JREQUEST_ALLOWRAW);
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $this->getJSModel('ticket')->storeMergeTicket($data);
		$user = JSSupportTicketCurrentUser::getInstance();
		$link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id='.$data['secondaryticket'];
		$msg = JSSupportTicketMessage::getMessage($result,'TICKETMERGE');
        $this->setRedirect(JRoute::_($link , false), $msg);
    }

    function getuserremainmaxticket(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
    	$result = $this->getJSModel('ticket')->getUserRemainMaxticket();
    	echo $result;
        JFactory::getApplication()->close();
    }

	function display($cachable = false, $urlparams = false){
		$document = JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','ticket');
		$layoutName = JFactory::getApplication()->input->get('layout','mytickets');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
