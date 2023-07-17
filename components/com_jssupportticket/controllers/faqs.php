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

class JSSupportTicketControllerFaqs extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function editfaqs(){
		JFactory::getApplication()->input->set('layout','formfaq');
		JFactory::getApplication()->input->set('view','faqs');
		$this->display();
	}
	function savefaq(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$model = $this->getJSModel('faqs');
		$data = JFactory::getApplication()->input->post->getArray();
		$result=$model->storeFaq($data);
		if($result == SAVED){
			$link ='index.php?option=com_jssupportticket&c=faqs&layout=faqs&Itemid='.$Itemid;
		}else{
			$link ='index.php?option=com_jssupportticket&c=faqs&layout=formfaq&Itemid='.$Itemid;
		}
		$msg = JSSupportTicketMessage::getMessage($result,'FAQ');
		$this->setRedirect(JRoute::_($link , false), $msg);
	}
	function removefaq() {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$model = $this->getJSModel('faqs');
        $id=JFactory::getApplication()->input->get('rowid');
		$result = $model->deleteUserFaq($id);
		$msg = JSSupportTicketMessage::getMessage($result,'FAQ');
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
		$viewName = JFactory::getApplication()->input->post->get('view','faqs');
		$layoutName = JFactory::getApplication()->input->get('layout','faqs');
		$viewType = $document->getType();
		$view =  $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
