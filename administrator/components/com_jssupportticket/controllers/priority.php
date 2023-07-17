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
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerPriority extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }
    
    function savepriority() {
        $this->storepriority('saveandclose');
    }

    function savepriorityandnew() {
        $this->storepriority('saveandnew');
    }

    function saveprioritysave() {
        $this->storepriority('save');
    }

    function storepriority($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('priority')->storePriority($data);
        if ($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = "index.php?option=com_jssupportticket&c=priority&layout=priorities";
            } elseif ($callfrom == 'save') {
                $link = "index.php?option=com_jssupportticket&c=priority&layout=formpriority&cid[]=" . JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = "index.php?option=com_jssupportticket&c=priority&layout=formpriority";
            }
        } else {
            $link = "index.php?option=com_jssupportticket&c=priority&layout=formpriority";
        }
        $msg = JSSupportticketMessage::getMessage($result,'PRIORITY');
        $this->setRedirect($link, $msg);
    }

    function deletepriority() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('priority')->deletePriority();
        $msg = JSSupportticketMessage::getMessage($result,'PRIORITY');
        if ($result == DELETE_ERROR){
            $msg = JSSupportticketMessage::$recordid. ' ' . $msg;
        }
        $link = "index.php?option=com_jssupportticket&c=priority&layout=priorities";
        $this->setRedirect($link, $msg);
    }

    function addnewpriority() {
        $layoutName = JFactory::getApplication()->input->set('layout', 'formpriority');
        $this->display();
    }

    function cancelpriority() {
        $link = "index.php?option=com_jssupportticket&c=priority&layout=priorities";
        $msg = JSSupportticketMessage::getMessage(CANCEL,'PRIORITY');
        $this->setRedirect($link, $msg);
    }

    function makeprioritydefault() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $priorityid = $cid[0];
        $result = $this->getJSModel('priority')->makePriorityDefault($priorityid);
        $msg = JSSupportticketMessage::getMessage($result,'PRIORITY');
        $link = 'index.php?option=com_jssupportticket&c=priority&layout=priorities';
        $this->setRedirect($link, $msg);
    }
    
    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'priority';
        $jinput = JFactory::getApplication()->input;
        $layoutName = $jinput->get('layout', 'priority');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
