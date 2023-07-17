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

class JSSupportticketControllerStaff extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function savestaffmember() {
        $this->storestaffmember('saveandclose');
    }

    function savestaffmembersave() {
        $this->storestaffmember('save');
    }

    function savestaffmemberandnew() {
        $this->storestaffmember('saveandnew');
    }

    function storestaffmember($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('staff')->storeStaffMember($data);
        if ($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=staff&layout=staffmembers';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=staff&layout=formstaff&cid[]=' .JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=staff&layout=formstaff';
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=staff&layout=formstaff';
        }
        $msg = JSSupportticketMessage::getMessage($result,'STAFF');
        $this->setRedirect($link, $msg);
    }

    function editstaffmember() {
        JFactory::getApplication()->input->set('layout', 'formstaff');
        $this->display();
    }

    function cancelstaff() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'STAFF');
        $link = 'index.php?option=com_jssupportticket&c=staff&layout=staffmembers';
        $this->setRedirect($link, $msg);
    }

    function removestaffmember() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('staff')->deleteStaffMemberAdmin();
        $msg = JSSupportticketMessage::getMessage($result,'STAFF');
        if ($result == DELETE_ERROR){
            $msg = JSSupportticketMessage::$recordid. ' ' . $msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=staff&layout=staffmembers';
        $this->setRedirect($link, $msg);
    }

    function listdepartmentsbygroup() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        global $mainframe;
        $version = new JVersion;
        $joomla = $version->getShortVersion();
        $jversion = substr($joomla, 0, 3);
        $mainframe = JFactory::getApplication();
        $val = JFactory::getApplication()->input->get('val');

        $returnvalue = $this->getJSModel('staff')->listDepartmentsByGroup($val);
        echo $returnvalue;
        $mainframe->close();
    }

    function checkuserexist() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $val = JFactory::getApplication()->input->get('val');
        $returnvalue = $this->getJSModel('staff')->checkuserexist($val);
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function getusersearchstaffreportajax() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('staff')->getusersearchstaffreportajax();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function getusersearchajax() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('staff')->getusersearchajax();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function getstaffusersearchajax() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('staff')->getstaffusersearchajax();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function getuserlistajax(){
      $returnvalue = $this->getJSModel('staff')->getuserlistajax();
      echo $returnvalue;
      JFactory::getApplication()->close();
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'staff';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}
?>
