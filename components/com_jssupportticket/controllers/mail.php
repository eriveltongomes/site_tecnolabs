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

class JSSupportTicketControllerMail extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function showmessage(){
		JFactory::getApplication()->input->set('layout', 'message');
		JFactory::getApplication()->input->set('view', 'mail');
		$this->display();
	}
   
    function removemessage(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getJSModel('mail');
        $id=JFactory::getApplication()->input->get('rowid');
		$result = $model->deleteMessage($id);
		$msg = JSSupportTicketMessage::getMessage($result,'MESSAGE');
		$arr = array();
        if($result != DELETED)
        	$arr[0] = 0;
        else
        	$arr[0] = 1;
        $arr[1] = $msg;
        echo json_encode($arr);
        JFactory::getApplication()->close();    
    }
    function markasread(){
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$model=$this->getJSModel('mail');
		$result = $model->setAsRead();
		$msg = JSSupportTicketMessage::getMessage($result,'MAIL');
		$link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox&Itemid='.$Itemid;
		$this->setRedirect(JRoute::_($link , false), $msg);
    }
    function markasunread(){
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$model=$this->getJSModel('mail');
		$result = $model->setAsUnread();
		$msg = JSSupportTicketMessage::getMessage($result,'MAIL');
		$link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox&Itemid='.$Itemid;
		$this->setRedirect(JRoute::_($link , false), $msg);
    }
	function savemessage(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$model=$this->getJSModel('mail');
		$result=$model->storeMessage();
		$msg = JSSupportTicketMessage::getMessage($result,'MAIL');
		$link = 'index.php?option=com_jssupportticket&c=mail&layout=inbox&Itemid='.$Itemid;
		$this->setRedirect(JRoute::_($link , false), $msg);
	}
	function savemessagereply(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getJSModel('mail');
		$messageid = JFactory::getApplication()->input->get('messageid','');
		$result = $model->storemessagereply();
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$msg = JSSupportTicketMessage::getMessage($result,'MAIL');
		$link = 'index.php?option=com_jssupportticket&c=mail&layout=message&id='.$messageid.'&Itemid='.$Itemid;
		$this->setRedirect(JRoute::_($link , false), $msg);
	}
	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','mail');
		$layoutName = JFactory::getApplication()->input->get('layout','inbox');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
