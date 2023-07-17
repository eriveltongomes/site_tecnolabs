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
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerMail extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function showmessage() {
        JFactory::getApplication()->input->set('layout', 'message');
        $this->display();
    }

    function removemessage() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('mail')->deleteMessages();
        $msg = JSSupportticketMessage::getMessage($result,'MAIL');
        if ($result == DELETE_ERROR){
            $msg = JSSupportticketMessage::$recordid.''.$msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox';
        $this->setRedirect($link, $msg);
    }

    function markasread() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('mail')->setAsRead();
        $msg = JSSupportticketMessage::getMessage($result,'READ');
        if ($result == MAIL_MARKED_ERROR){
            $msg = JSSupportticketMessage::$recordid.''.$msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox';
        $this->setRedirect($link, $msg);
    }

    function markasunread() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('mail')->setAsUnread();
        $msg = JSSupportticketMessage::getMessage($result,'UNREAD');
        if ($result == MAIL_MARKED_ERROR){
            $msg = JSSupportticketMessage::$recordid.''.$msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox';
        $this->setRedirect($link, $msg);
    }

    function savemessage() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('mail')->storeMessage();
        $msg = JSSupportticketMessage::getMessage($result,'MESSAGE');
        $link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox';
        $this->setRedirect($link, $msg);
    }

    function cancelmessage() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'MESSAGE');
        $link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox';
        $this->setRedirect($link, $msg);
    }

    function savemessagereply() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', '');
        $result = $this->getJSModel('mail')->storemessagereply();
        $msg = JSSupportticketMessage::getMessage($result,'REPLY');
        $link = 'index.php?option=com_jssupportticket&c=mail&layout=message&cid[]=' . $cid;
        $this->setRedirect($link, $msg);
    }
    
    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'mail';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
