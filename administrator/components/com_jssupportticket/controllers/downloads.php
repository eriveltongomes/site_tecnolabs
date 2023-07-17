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

class JSSupportticketControllerDownloads extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function editdownloads() {
        JFactory::getApplication()->input->set('layout', 'formdownload');
        $this->display();
    }

    function savedownload() {
        $this->storedownload('saveandclose');
    }

    function savedownloadsave() {
        $this->storedownload('save');
    }

    function savedownloadsavenew() {
        $this->storedownload('saveandnew');
    }

    function storedownload($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('downloads')->storeDownload($data);
        if ($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=downloads&layout=downloads';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=downloads&layout=formdownload&cid[]='.JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=downloads&layout=formdownload';
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=downloads&layout=formdownload';
        }
        $msg = JSSupportticketMessage::getMessage($result,'DOWNLOAD');
        $this->setRedirect($link, $msg);
    }

    function canceldownloads() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'DOWNLOAD');
        $link = 'index.php?option=com_jssupportticket&c=downloads&layout=downloads';
        $this->setRedirect($link, $msg);
    }

    function deletedownloads() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('downloads')->deleteDownload();
        $msg = JSSupportticketMessage::getMessage($result,'DOWNLOAD');
        $link = 'index.php?option=com_jssupportticket&c=downloads&layout=downloads';
        $this->setRedirect($link, $msg);
    }
    
    function deleteattachmentbyid(){
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
		$id = JFactory::getApplication()->input->get('id');
		$downloadid = JFactory::getApplication()->input->get('downloadid');
		$result = $this->getJSModel('downloads')->deleteAttachmentById($id);
        $msg = JSSupportticketMessage::getMessage($result,'ATTACHMENT');
        $link = 'index.php?option=com_jssupportticket&c=downloads&layout=formdownload&cid[]='.$downloadid;
        $this->setRedirect($link, $msg);
	}

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'downloads';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
