<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  Feb 24, 2020
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerGdpr extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function eraseidentifyinguserdata() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $uid = JFactory::getApplication()->input->get('id');
        $result = $this->getJSModel('gdpr')->anonymizeUserData($uid);
        $link = 'index.php?option=com_jssupportticket&c=gdpr&layout=erasedatarequests';
        $msg = JSSupportticketMessage::getMessage($result,'ERASED');
        $this->setRedirect($link, $msg);
    }

    function deleteuserdata() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $uid = JFactory::getApplication()->input->get('id');
        $result = $this->getJSModel('gdpr')->deleteUserData($uid);
        $link = 'index.php?option=com_jssupportticket&c=gdpr&layout=erasedatarequests';
        $msg = JSSupportticketMessage::getMessage($result,'ERASED');
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'gdpr';
        $jinput = JFactory::getApplication()->input;
        $layoutName = $jinput->get('layout', 'erasedatarequests');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
