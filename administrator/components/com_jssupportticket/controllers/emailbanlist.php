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

class JSSupportticketControllerEmailbanlist extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function banemailsave() {
        $this->storebanemail('banemail');
    }

    function banemail() {
        $this->storebanemail('banemailandclose');
    }

    function banemailandnew() {
        $this->storebanemail('banemailandnew');
    }

    function storebanemail($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('emailbanlist')->banEmail($data);
        if ($result == SAVED) {
            if ($callfrom == 'banemailandclose'){
                $link = 'index.php?option=com_jssupportticket&c=emailbanlist&layout=emailbanlists';
            } elseif ($callfrom == 'banemail') {
                $link = 'index.php?option=com_jssupportticket&c=emailbanlist&layout=formemailbanlist&cid[]='.JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'banemailandnew'){
                $link = 'index.php?option=com_jssupportticket&c=emailbanlist&layout=formemailbanlist';
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=emailbanlist&layout=formemailbanlist';
        }
        $msg = JSSupportticketMessage::getMessage($result,'BAN_EMAIL');        
        $this->setRedirect($link, $msg);
    }

    function deleteemail() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('emailbanlist')->deleteEmail();
        $msg = JSSupportticketMessage::getMessage($result,'BAN_EMAIL');
        $link = "index.php?option=com_jssupportticket&c=emailbanlist&layout=emailbanlists";
        $this->setRedirect($link, $msg);
    }

    function addnewemail() {
        $layoutName = JFactory::getApplication()->input->set('layout', 'formemailbanlist');
        $this->display();
    }

    function cancelemail() {
        $link = "index.php?option=com_jssupportticket&c=emailbanlist&layout=emailbanlists";
        $msg = JSSupportticketMessage::getMessage(CANCEL,'BAN_EMAIL');
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'emailbanlist';
        $layoutName = JFactory::getApplication()->input->get('layout', 'emailbanlists');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
