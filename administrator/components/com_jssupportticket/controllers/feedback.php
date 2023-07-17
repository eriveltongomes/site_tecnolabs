<?php

/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerFeedback extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function savefeedback() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $this->getJSModel('feedback')->storeFeedback($data);
        $link = 'index.php?option=com_jssupportticket&c=feedback&layout=formfeedback&successflag=1';
        $this->setRedirect($link);
    }

    function showfeedbackform() {
        $token = JFactory::getApplication()->input->get('token');
        $link = 'index.php?option=com_jssupportticket&c=feedback&layout=formfeedback&token='.$token;
        $this->setRedirect($link);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'feedback';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
