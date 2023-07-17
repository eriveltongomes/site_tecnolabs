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

class JSSupportticketControllerRolePermissions extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
    }

    function getRolePermissionForStaff() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $roleid = JFactory::getApplication()->input->get('roleid');
        $result = $this->getJSModel('rolepermissions')->getRolePermissionsAdminAjax($roleid);
        echo $result;
        JFactory::getApplication()->close();
    }

    function cancelrolepermission() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'ROLE_PERMISSION');
        $link = 'index.php?option=com_jssupportticket&c=roles&layout=roles';
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'rolepermissions';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}
