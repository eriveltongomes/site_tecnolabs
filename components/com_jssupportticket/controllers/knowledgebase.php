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

class JSSupportTicketControllerknowledgebase extends JSSupportTicketController{
	
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}

	function editknowledgebasecategory(){
		$layoutName = JFactory::getApplication()->input->set('layout','formcategory');
		$view = JFactory::getApplication()->input->set('view','knowledgebase');
		$this->display();
	}

	function saveknowledgebasecategory(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$model =  $this->getJSModel('knowledgebase');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $model->storeKnowledgeBaseCategory($data);
        $msg = JSSupportticketMessage::getMessage($result,'CATEGORY');
        $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=categories&Itemid='.$Itemid;
		$this->setRedirect(JRoute::_($link , false), $msg);
	}

	function editknowledgebasearticle(){
		$layoutName = JFactory::getApplication()->input->set('layout','formarticle');
		$view = JFactory::getApplication()->input->set('view','knowledgebase');
		$this->display();
	}

    function removekbarticle(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $model = $this->getJSModel('knowledgebase');
        $id=JFactory::getApplication()->input->get('rowid');
		$result = $model->deleteKnowledgebaseArticle($id);
		$msg = JSSupportTicketMessage::getMessage($result,'ARTICLE');
		$arr = array();
        if($result != DELETED)
        	$arr[0] = 0;
        else
        	$arr[0] = 1;
        $arr[1] = $msg;
        echo json_encode($arr);
        JFactory::getApplication()->close();   
    }

    function removekbcategory(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $model =  $this->getJSModel('knowledgebase');
        $id=JFactory::getApplication()->input->get('rowid');
		$result = $model->deleteKnowledgebaseCategory($id);
		$msg = JSSupportTicketMessage::getMessage($result,'CATEGORY');
        $arr = array();
        if($result != DELETED)
        	$arr[0] = 0;
        else
        	$arr[0] = 1;
        $arr[1] = $msg;
        echo json_encode($arr);
        JFactory::getApplication()->close();
    }

	function saveknowledgebasearticle() {
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$model =  $this->getJSModel('knowledgebase');
		$data = JFactory::getApplication()->input->post->getArray();
        $data['content']=$data['content_article'];
		$result=$model->storeKnowledgeBaseArticle($data);
		$msg = JSSupportTicketMessage::getMessage($result,'ARTICLE');
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$link ='index.php?option=com_jssupportticket&c=knowledgebase&layout=articles&Itemid='.$Itemid;
		$this->setRedirect(JRoute::_($link , false), $msg);
	}

	function getdownloadbyid(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$id = JFactory::getApplication()->input->get('id');
		$this->getJSModel('knowledgebase')->getDownloadAttachmentById($id);
		JFactory::getApplication()->close();
	}

	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','knowledgebase');
		$layoutName = JFactory::getApplication()->input->get('layout','articles');
		$viewType = $document->getType();
		$view =  $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}

	function deleteattachmentbyid(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$id = JFactory::getApplication()->input->get('id');
		$kbid = JFactory::getApplication()->input->get('kbid');
		$permission = JSSupportticketCurrentUser::getInstance()->checkUserPermission('Edit Knowledge Base');
		if($permission == true){
			$result = $this->getJSModel('knowledgebase')->deleteAttachmentById($id);
			$msg = JSSupportTicketMessage::getMessage($result,'ATTACHMENT');
		}else{
			$msg = PERMISSION_ERROR;
		}
        $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=formarticle&id='.$kbid;
        $this->setRedirect(JRoute::_($link , false), $msg);
	}
}
?>
