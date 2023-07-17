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

class JSSupportTicketControllerAnnouncements extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function editannouncements(){
		JFactory::getApplication()->input->set('layout','formannouncement');
		JFactory::getApplication()->input->set('view','announcements');
		$this->display();
	}
	function saveannouncement(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$model =  $this->getJSModel('announcements');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $model->storeAnnouncement($data);
		if($result == SAVED)
			$link ='index.php?option=com_jssupportticket&c=announcements&layout=announcements&Itemid='.$Itemid;
		else
			$link ='index.php?option=com_jssupportticket&c=announcements&layout=formannouncement&Itemid='.$Itemid;
		$msg = JSSupportTicketMessage::getMessage($result,'ANNOUNCEMENT');
		$this->setRedirect(JRoute::_($link , false), $msg);
	}
	function removeannouncement() {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getJSModel('announcements');
        $id = JFactory::getApplication()->input->get('rowid');
		$result = $model->deleteUserAnnouncement($id);
        $msg = JSSupportTicketMessage::getMessage($result,'ANNOUNCEMENT');
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
		$viewName = JFactory::getApplication()->input->post->get('view','announcements');
		$layoutName = JFactory::getApplication()->input->get('layout','announcements');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}

}
?>
