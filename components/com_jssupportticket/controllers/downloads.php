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

class JSSupportTicketControllerDownloads extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function editdownloads(){
		JFactory::getApplication()->input->set('layout','formdownload');
		JFactory::getApplication()->input->set('view','downloads');
		$this->display();
	}
	function savedownload(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid = JFactory::getApplication()->input->get('Itemid');
		$model = $this->getJSModel('downloads');
		$data = JFactory::getApplication()->input->post->getArray();
		$result = $model->storeDownload($data);
		if($result == SAVED){
			$link ='index.php?option=com_jssupportticket&c=downloads&layout=downloads&Itemid='.$Itemid;
		}else{
			$link ='index.php?option=com_jssupportticket&c=downloads&layout=formdownload&Itemid='.$Itemid;
		}
		$msg = JSSupportTicketMessage::getMessage($result,'DOWNLOAD');
		$this->setRedirect(JRoute::_($link , false), $msg);
	}
    function removedownload(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $model =  $this->getJSModel('downloads');
        $id=JFactory::getApplication()->input->get('rowid');
		$result = $model->deleteUserDownload($id);
		$msg = JSSupportTicketMessage::getMessage($result,'DOWNLOAD');
        $arr = array();
        if($result != DELETED)
        	$arr[0] = 0;
        else
        	$arr[0] = 1;
        $arr[1] = $msg;
        echo json_encode($arr);
        JFactory::getApplication()->close();
    }

    function getUserDownloadsById(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
    	$id = JFactory::getApplication()->input->get('id');
    	$model = $this->getJSModel('downloads');
    	$result = $model->getUserDownloadById($id);
    	echo json_encode($result);
    	JFactory::getApplication()->close();
    }

    function downloadall(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
    	$this->getJSModel('downloads')->getAllDownloadFiles();
    	JFactory::getApplication()->close();
    }
	
	function getdownloadbyid(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$id = JFactory::getApplication()->input->get('id');
		$this->getJSModel('downloads')->getDownloadAttachmentById($id);
		JFactory::getApplication()->close();
	}

	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','downloads');
		$layoutName = JFactory::getApplication()->input->get('layout','downloads');
		$viewType = $document->getType();
		$view =  $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
	function deleteattachmentbyid(){
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$id = JFactory::getApplication()->input->get('id');
		$downloadid = JFactory::getApplication()->input->get('downloadid');
		$result = $this->getJSModel('downloads')->deleteAttachmentById($id);
		$msg = JSSupportTicketMessage::getMessage($result,'ATTACHMENT');
        $link = 'index.php?option=com_jssupportticket&c=downloads&layout=formdownload&id='.$downloadid;
        $this->setRedirect(JRoute::_($link , false), $msg);
	}

}
?>
