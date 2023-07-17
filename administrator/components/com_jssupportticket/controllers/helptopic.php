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

class JSSupportticketControllerHelpTopic extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function savehelptopic() {
        $this->storehelptopic('saveandclose');
    }

    function savehelptopicsave() {
        $this->storehelptopic('save');
    }

    function savehelptopicandnew() {
        $this->storehelptopic('saveandnew');
    }

    function storehelptopic($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('helptopic')->storeHelpTopic($data);
        if($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=helptopic&layout=helptopices';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=helptopic&layout=formhelptopic&cid[]='.JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=helptopic&layout=formhelptopic';
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=helptopic&layout=helptopices';
        }
        $msg = JSSupportticketMessage::getMessage($result,'HELP_TOPIC');
        $this->setRedirect($link, $msg);
    }

    function edithelptopic() {
        JFactory::getApplication()->input->set('layout', 'formhelptopic');
        $this->display();
    }

    function cancelhelptopic() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'HELP_TOPIC');
        $link = 'index.php?option=com_jssupportticket&c=helptopic&layout=helptopices';
        $this->setRedirect($link, $msg);
    }

    function removehelptopic() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('helptopic')->deleteHelpTopic();
        $msg = JSSupportticketMessage::getMessage($result,'HELP_TOPIC');
        $link = 'index.php?option=com_jssupportticket&c=helptopic&layout=helptopices';
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'helptopic';
        $layoutName = JFactory::getApplication()->input->get('layout', 'helptopic');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
