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

defined ('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportTicketControllerStaff extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function checkuserexist(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$val=JFactory::getApplication()->input->get( 'val');
		$model=$this->getJSModel('staff');
		$returnvalue = $model->checkUserExist($val);
		echo json_encode($returnvalue);
		JFactory::getApplication()->close();
	}
	function savestaffmember(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		global $mainframe;
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$data = JFactory::getApplication()->input->post->getArray();
		$model=$this->getJSModel('staff');
		$result = $model->storeStaffMember($data);
		if($result == SAVED){
			$link = 'index.php?option=com_jssupportticket&c=staff&layout=staff&Itemid='.$Itemid;
		}else{
			$link = 'index.php?option=com_jssupportticket&c=staff&layout=formstaff&Itemid='.$Itemid;
		}
		$msg = JSSupportTicketMessage::getMessage($result,'STAFF');
		$this->setRedirect(JRoute::_($link , false), $msg);
	}
	function removestaffmember(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$model=$this->getJSModel('staff');
		$staffid=JFactory::getApplication()->input->get( 'rowid');
		$result = $model->deleteStaffMember($staffid);
		$msg = JSSupportTicketMessage::getMessage($result,'STAFF');
		$arr = array();
        if($result != DELETED)
        	$arr[0] = 0;
        else
        	$arr[0] = 1;
        $arr[1] = $msg;
        echo json_encode($arr);
        JFactory::getApplication()->close();
	}

	function searchstaffprofileajax(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$name = JFactory::getApplication()->input->getString('name');
		$emailaddress = JFactory::getApplication()->input->getString('emailaddress');
		$result = $this->getJSModel('staff')->getStaffChangeProfile($name,$emailaddress);
		echo $result;
		JFactory::getApplication()->close();
	}

	function savestaffprofileajax(){
		JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$value = JFactory::getApplication()->input->getString('value', '');
		$datafor = JFactory::getApplication()->input->get('datafor','');
		$result = $this->getJSModel('staff')->saveStaffProfileAjax($value,$datafor);
		echo $result;
		JFactory::getApplication()->close();
	}

	function uploadStaffImageajax(){
		$value = JFactory::getApplication()->input->get('id');
		$result = $this->getJSModel('staff')->uploadStaffImage($value);
		echo json_encode($result);
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
    function getuserlistajax() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('staff')->getuserlistajax();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

    function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','staff');
		$layoutName = JFactory::getApplication()->input->get('layout','staff');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
