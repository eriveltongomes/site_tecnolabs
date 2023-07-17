<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.controller');

class JSSupportticketControllerTicket extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function saveticket() {
        $this->storeticket('saveandclose');
    }

    function saveticketsave() {
        $this->storeticket('save');
    }

    function saveticketandnew() {
        $this->storeticket('saveandnew');
    }

    function storeticket($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('ticket')->storeTicket($data);
        if($result == SAVED) {
            switch ($callfrom) {
                case 'save':
                    $link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket&cid[]='.JSSupportticketMessage::$recordid;
                    break;
                case 'saveandnew':
                    $link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket';
                    break;
                case 'saveandclose':
                    $link = 'index.php?option=com_jssupportticket&c=ticket&layout=tickets';
                    break;
            }
        }else{
            JFactory::getApplication()->setUserState('com_jssupportticket.data',$data);
            $link = 'index.php?option=com_jssupportticket&c=ticket&layout=formticket';
        }
        $msg = JSSupportticketMessage::getMessage($result,'TICKET');
        $this->setRedirect($link, $msg);
    }

    function actionticket() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $ticket = $this->getJSModel('ticket');
        $data = JFactory::getApplication()->input->post->getArray();
        $action = $data['callfrom'];
        switch ($action) {
            case 'postreply':
                $data['responce'] = JFactory::getApplication()->input->get('responce', '', 'raw');
                $result = $ticket->storeTicketReplies($data['id'],$data['responce'], $data['created'], $data);
                $msg = JSSupportticketMessage::getMessage($result,'REPLY');
                $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                $this->setRedirect($link, $msg);
                break;
            case 'internalnote':
                $data['internalnote'] = JFactory::getApplication()->input->get('internalnote', '', 'raw');
                $result = $ticket->storeTicket_InternalNote($data['id'],$data['notetitle'], $data['internalnote'], $data['created'], $data);
                $msg = JSSupportticketMessage::getMessage($result,'INTERNAL_NOTE');
                $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                $this->setRedirect($link, $msg);
                break;
            case 'departmenttransfer':
                $data['departmenttranfer'] = JFactory::getApplication()->input->get('departmenttranfer', '', 'raw');
                $result = $ticket->ticketDepartmentTransfer($data['id'], $data['departmentid'], $data['departmenttranfer'], $data['created'], $data);
                $msg = JSSupportticketMessage::getMessage($result,'DEPARTMENT');
                $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                $this->setRedirect($link, $msg);
                break;
            case 'stafftransfer':
                $data['assigntostaffnote'] = JFactory::getApplication()->input->get('assigntostaffnote', '', 'raw');
                $result = $ticket->ticketStaffTransfer($data['id'],$data['assigntostaff'], $data['assigntostaffnote'], $data['created'], $data);
                $msg = JSSupportticketMessage::getMessage($result,'STAFF');
                $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                $this->setRedirect($link, $msg);
                break;
            case 'action':
                switch ($data['callaction']) {
                    case 1://change priority
                        $result = $ticket->changeTicketPriority($data['id'], $data['priorityid'], $data['created']);
                        $msg = JSSupportticketMessage::getMessage($result,'PRIORITY');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 10: //change ticket status as inprogress=4
                        $result = $ticket->ticketMarkInprogress($data['id'],$data['created']);
                        $msg = JSSupportticketMessage::getMessage($result,'MARK_IN_PROGRESS');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 3: //ticket close
                        $result = $ticket->ticketClose($data['id'], $data['created']);
                        $msg = JSSupportticketMessage::getMessage($result,'CLOSE');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 5: //ticket delete
                        $result = $ticket->delete_Ticket($data['id']);
                        $msg = JSSupportticketMessage::getMessage($result,'DELETE');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=tickets';
                        $this->setRedirect($link, $msg);
                        break;
                    case 6: // markoverdue
                        $result = $ticket->markOverDueTicket($data['id'], $data['created']);
                        $msg = JSSupportticketMessage::getMessage($result,'MARK_OVERDUE');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 13: // unmarkoverdue
                        $result = $ticket->unMarkOverDueTicket($data['id'], $data['created']);
                        $msg = JSSupportticketMessage::getMessage($result,'MARK_OVERDUE');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 8: //reopened ticket
                        $result = $ticket->reopenTicket($data['id'], $data['lastreply']);
                        $msg = JSSupportticketMessage::getMessage($result,'REOPEN');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 4: //ban email
                        $result = $this->getJSModel('emailbanlist')->banEmailTicket($data['email'],$data['created'], $data['id'], 1);
                        $msg = JSSupportticketMessage::getMessage($result,'BAN_EMAIL');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 9: //unban email
                        $result = $this->getJSModel('emailbanlist')->unbanEmailTicket($data['email'], $data['id']);
                        $msg = JSSupportticketMessage::getMessage($result,'UNBAN_EMAIL');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 7: //banemail and close ticket
                        $result = $ticket->banEmailAndCloseTicket($data['id'], $data['created'],$data['email']);
                        $msg = $result;
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 11: //lock ticket
                        $result = $ticket->lockTicket($data['id']);
                        $msg = JSSupportticketMessage::getMessage($result,'LOCK');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                    case 12: //unlock ticket
                        $result = $ticket->unlockTicket($data['id']);
                        $msg = JSSupportticketMessage::getMessage($result,'UNLOCK');
                        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=' . $data['id'];
                        $this->setRedirect($link, $msg);
                        break;
                }
                break;
        }
    }
    
    function enforcedelete() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('ticket')->enforcedeleteTicket();
        $msg = JSSupportticketMessage::getMessage($result,'TICKET');
        $link = "index.php?option=com_jssupportticket&c=ticket&layout=tickets";
        $this->setRedirect($link, $msg);
    }

    function delete() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('ticket')->deleteTicket();
        $msg = JSSupportticketMessage::getMessage($result,'TICKET');
        $link = "index.php?option=com_jssupportticket&c=ticket&layout=tickets";
        $this->setRedirect($link, $msg);
    }

    function addnewticket() {
        $layoutName = JFactory::getApplication()->input->set('layout', 'formticket');
        $this->display();
    }

    function cancelticket() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'TICKET');
        $link = "index.php?option=com_jssupportticket&c=ticket&layout=tickets";
        $this->setRedirect($link, $msg);
    }

    function deleteattachment() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $jinput = JFactory::getApplication()->input;
        $id = $jinput->get('id');
        $ticketid = $jinput->get('ticketid');

        $result = $this->getJSModel('attachments')->removeAttachment($id,$ticketid);
        if($result == true){
            $msg = JText::_("Attachment has been removed");
        }else{
            $msg = JText::_("Attachment has not been removed");
        }
        $link = "index.php?option=com_jssupportticket&c=ticket&task=addnewticket&cid[]=".$ticketid;
        $this->setRedirect($link, $msg);
    }

    function editresponce() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id');
        $returnvalue = $this->getJSModel('ticket')->editResponceAJAX($id);
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function saveresponceajax() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        global $mainframe;
        $mainframe = JFactory::getApplication();

        $id = JFactory::getApplication()->input->get('id');
        //$responce = JFactory::getApplication()->input->get('val', '', '', 'string', JREQUEST_ALLOWHTML);
        $responce = JFactory::getApplication()->input->get('val', '', 'raw');
        $returnvalue = $this->getJSModel('ticket')->saveResponceAJAX($id, $responce);
        if ($returnvalue != 1)
            $returnvalue = JText::_('Mail has not been send');
        echo $responce;
        $mainframe->close();
    }

    function deleteresponceajax() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id');
        $returnvalue = $this->getJSModel('ticket')->deleteResponceAJAX($id);
        if ($returnvalue == 1)
            $returnvalue = '<font color="green">' . JText::_('Mail has been deleted') . '</font>';
        else
            $returnvalue = '<font color="red">' . JText::_('Mail has not been deleted') . '</font>';
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function getpremadeforinternalnote() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        global $mainframe;
        $mainframe = JFactory::getApplication();
        $val = JFactory::getApplication()->input->get('val');
        $returnvalue = $this->getJSModel('premade')->getPremadeForInternalNote($val);
        $editor = JFactory::getConfig()->get('editor');
	    $editor = JEditor::getInstance($editor);
        echo $returnvalue;
        $mainframe->close();
    }

    function listhelptopicandpremade() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        global $mainframe;
        $mainframe = JFactory::getApplication();
        $val = JFactory::getApplication()->input->get('val');
        $returnvalue = $this->getJSModel('helptopic')->listHelpTopicAndPremade($val);
        echo json_encode($returnvalue);
        $mainframe->close();
    }

    function getdownloadbyid(){
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id');
        $this->getJSModel('ticket')->getDownloadAttachmentById($id);
        JFactory::getApplication()->close();
    }
    
    function downloadbyname(){
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id');
        $name = JFactory::getApplication()->input->get('name');
        $this->getJSModel('ticket')->getDownloadAttachmentByName( $name, $id );

        JFactory::getApplication()->close();
    }

    function getReplyDataByID() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('ticket')->getReplyDataByID();
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
        $Itemid = JFactory::getApplication()->input->get('Itemid');
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('ticket')->editTime($data);
        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]='.$data['reply-tikcetid'];
        $msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect($link, $msg);
    }

    function saveeditedtimenote() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $Itemid = JFactory::getApplication()->input->get('Itemid');
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('ticket')->editTimeForNote($data);
        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]='.$data['note-tikcetid'];
        $msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect($link, $msg);
    }

    function saveeditedreply() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $Itemid = JFactory::getApplication()->input->get('Itemid');
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('ticket')->editReply($data);
        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]='.$data['reply-tikcetid'];
        $msg = JSSupportTicketMessage::getMessage($result,'TICKET');
        $this->setRedirect($link, $msg);
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
        $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]='.$data['secondaryticket'];
        $msg = JSSupportTicketMessage::getMessage($result,'TICKETMERGE');
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'ticket';
        $layoutName = JFactory::getApplication()->input->get('layout', 'tickets');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
