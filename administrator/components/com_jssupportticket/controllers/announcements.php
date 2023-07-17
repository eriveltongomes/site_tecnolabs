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

class JSSupportticketControllerAnnouncements extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function editannouncements() {
        JFactory::getApplication()->input->set('layout', 'formannouncement');
        $this->display();
    }

    function saveannouncement() {
        $this->storeannouncement('saveandclose');
    }

    function saveannouncementsave() {
        $this->storeannouncement('save');
    }

    function saveannouncementsavenew() {
        $this->storeannouncement('saveandnew');
    }

    function storeannouncement($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('announcements')->storeAnnouncement($data);
        if($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=announcements&layout=announcements';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=announcements&layout=formannouncement&cid[]=' .JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=announcements&layout=formannouncement';
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=announcements&layout=announcements';
        }
        $msg = JSSupportticketMessage::getMessage($result,'ANNOUNCEMENT');
        $this->setRedirect($link, $msg);
    }

    function cancelannouncements() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'ANNOUNCEMENT');
        $link = 'index.php?option=com_jssupportticket&c=announcements&layout=announcements';
        $this->setRedirect($link, $msg);
    }

    function deleteannouncement() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('announcements')->deleteAnnouncement();
        $msg = JSSupportticketMessage::getMessage($result,'ANNOUNCEMENT');
        $link = 'index.php?option=com_jssupportticket&c=announcements&layout=announcements';
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'announcements';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
