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

class JSSupportTicketControllerPrivatecredentials extends JSSupportTicketController {

    var $_jinput = null;
    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
        $this->_jinput = JFactory::getApplication()->input;
    }

    function getprivatecredentials() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $ticketid = $this->_jinput->get('ticketid');
        $returnvalue = $this->getJSModel('privatecredentials')->getPrivateCredentials($ticketid);
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function removeprivatecredential(){
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('privatecredentials')->removePrivateCredential();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function getformforprivatecredentials(){
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('privatecredentials')->getFormForPrivateCredentials();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function storeprivatecredentials(){
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('privatecredentials')->storePrivateCredentials();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function display($cachable = false, $urlparams = false) {
        $document =  JFactory::getDocument();
        $jinput = JFactory::getApplication()->input;
        $viewName = $jinput->get('view','privatecredentials');
        $layoutName = $jinput->get('layout','privatecredentials');
        $viewType = $document->getType();
        $view =  $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
