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

class JSSupportticketControllerConfig extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function saveconf() {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $return_value = $this->getJSModel('config')->storeConfig();
        if ($return_value == 1) {
            $type = 'message';
            $msg = JText::_('The configuration has been stored');
        }elseif($return_value == 2){
            $msg = JText::_('Invalid data directory');
            $type = 'error';
        }elseif($return_value == 3){
            $msg = JText::_('Data directory in not writable');
            $type = 'error';
        }else {
            $msg = JText::_('Configuration not has been stored');
            $type = 'error';
        }
        $link = 'index.php?option=com_jssupportticket&c=config&layout=config';
        $this->setRedirect($link, $msg, $type);
    }

    function cancelconfig() {
        $link = "index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel";
        $msg = JText::_('Operation Cancel');
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'config';
        $layoutName = JFactory::getApplication()->input->get('layout', 'config');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
