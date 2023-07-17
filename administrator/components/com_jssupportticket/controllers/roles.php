<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company: Buruj Solutions
  + Contact: www.burujsolutions.com , info@burujsolutions.com
 * Created on:	March 04, 2014
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerRoles extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
    }

    function saverole() {
        $this->storerole('saveandclose');
    }

    function saverolesave() {
        $this->storerole('save');
    }

    function saveroleandnew() {
        $this->storerole('saveandnew');
    }

    function storerole($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('roles')->storeRole($data);
        if ($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=roles&layout=roles';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=roles&layout=formrole&cid[]=' .JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=roles&layout=formrole';
            }
        } else {
            $link = 'index.php?option=com_jssupportticket&c=roles&layout=formrole';
        }
        $msg = JSSupportticketMessage::getMessage($result,'ROLE');
        $this->setRedirect($link, $msg);
    }

    function editrole() {
        JFactory::getApplication()->input->set('layout', 'formrole');
        $this->display();
    }

    function cancelrole() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'ROLE');
        $link = 'index.php?option=com_jssupportticket&c=roles&layout=roles';
        $this->setRedirect($link, $msg);
    }

    function removerole() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('roles')->deleteRoleAdmin();
        $msg = JSSupportticketMessage::getMessage($result,'ROLE');
        if ($result == DELETE_ERROR){
            $msg = JSSupportticketMessage::$recordid. ' ' . $msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=roles&layout=roles';
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'roles';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
