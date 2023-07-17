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

class JSSupportTicketControllerDepartment extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function savedepartment(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$data = JFactory::getApplication()->input->post->getArray();
        $model = $this->getJSModel('department');
		$result = $model->storeDepartment($data);
		if($result == SAVED){
			$link = "index.php?option=com_jssupportticket&c=department&layout=departments&Itemid=".$Itemid;
		}else{
			$link = 'index.php?option=com_jssupportticket&c=department&layout=formdepartment&Itemid='.$Itemid;
		}
		$msg = JSSupportTicketMessage::getMessage($result,'DEPARTMENT');
		$this->setRedirect(JRoute::_($link , false), $msg);
	}
    function removedepartment(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getJSModel('department');
        $id = JFactory::getApplication()->input->get('rowid');
        $result = $model->deleteDepartment($id);
        $msg = JSSupportTicketMessage::getMessage($result,'DEPARTMENT');
        $arr = array();
        if($result != DELETED)
        	$arr[0] = 0;
        else
        	$arr[0] = 1;
        $arr[1] = $msg;
        echo json_encode($arr);
		JFactory::getApplication()->close();
    }
	
	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','department');
		$layoutName = JFactory::getApplication()->input->get('layout','departments');
		$viewType = $document->getType();
		$view =  $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
