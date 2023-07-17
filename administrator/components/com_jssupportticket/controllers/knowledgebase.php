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
class JSSupportticketControllerKnowledgebase extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function editknowledgebasecategory() {
        $layoutName = JFactory::getApplication()->input->set('layout', 'formcategory');
        $this->display();
    }

    function editknowledgebasearticle() {
        $layoutName = JFactory::getApplication()->input->set('layout', 'formarticle');
        $this->display();
    }

    function saveknowledgebasecategory() {
        $this->storeknowledgebasecategory('saveandclose');
    }

    function saveknowledgebasecategorysave() {
        $this->storeknowledgebasecategory('save');
    }

    function saveknowledgebasecategorysavenew() {
        $this->storeknowledgebasecategory('saveandnew');
    }

    function storeknowledgebasecategory($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('knowledgebase')->storeKnowledgeBaseCategory($data);
        $message = JSSupportticketMessage::getMessage($result,'CATEGORY');
        if ($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=categories';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=formcategory&cid[]='.JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=formcategory';
            }
        } else {
            $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=categories';
        }
        $this->setRedirect($link, $message);
    }

    function cancelknowledgebasecategory() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'CATEGORY');
        $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=categories';
        $this->setRedirect($link, $msg);
    }

    function deleteknowledgebasecategy() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cids = JFactory::getApplication()->input->get('cid', array(0), null, 'array');
        //$categoryid = $cids[0];
        foreach($cids as $categoryid){
            $result = $this->getJSModel('knowledgebase')->deleteKnowledgebaseCategory($categoryid);
        }
        $msg .= JSSupportticketMessage::getMessage($result,'CATEGORY');
        $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=categories';
        $this->setRedirect($link, $msg);
    }

    function saveknowledgebasearticle() {
        $this->storeknowledgebasearticle('saveandclose');
    }

    function saveknowledgebasearticlesave() {
        $this->storeknowledgebasearticle('save');
    }

    function saveknowledgebasearticlesavenew() {
        $this->storeknowledgebasearticle('saveandnew');
    }

    function storeknowledgebasearticle($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $data['content'] = $data['contant_article']; // css main div is content so change for content 
        $result = $this->getJSModel('knowledgebase')->storeKnowledgeBaseArticle($data);
        if ($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=articles';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=formarticle&cid[]='.JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=formarticle';
            }
        }else{
            $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=formarticle';
        }
        $msg = JSSupportticketMessage::getMessage($result,'ARTICLE');
        $this->setRedirect($link, $msg);
    }

    function cancelknowledgebasearticle() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'ARTICLE');
        $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=articles';
        $this->setRedirect($link, $msg);
    }

    function deleteknowledgebasearticle() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid');
        if(is_array($cid)){
            foreach($cid AS $id){
                $result = $this->getJSModel('knowledgebase')->deleteKnowledgebaseArticle($id);    
            }
        }else{
            $result = $this->getJSModel('knowledgebase')->deleteKnowledgebaseArticle($cid);
        }        
        $msg = JSSupportticketMessage::getMessage($result,'ARTICLE');
        $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=articles';
        $this->setRedirect($link, $msg);
    }

    function gettypeforbyparentid() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('knowledgebase')->getTypeForByParentId();
        echo $result;
        JFactory::getApplication()->close();
    }

    function checkparenttype() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('knowledgebase')->checkParentType();
        echo $result;
        JFactory::getApplication()->close();
    }

    function checkchildtype() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('knowledgebase')->checkChildType();
        echo $result;
        JFactory::getApplication()->close();
    }

    function makeparentoftype() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $result = $this->getJSModel('knowledgebase')->makeParentOfType();
        echo $result;
        JFactory::getApplication()->close();
    }

    function deleteattachmentbyid(){
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id');
        $kbid = JFactory::getApplication()->input->get('kbid');
        $result = $this->getJSModel('knowledgebase')->deleteAttachmentById($id);
        $msg = JSSupportticketMessage::getMessage($result,'ATTACHMENT');
        $link = 'index.php?option=com_jssupportticket&c=knowledgebase&layout=formarticle&cid[]='.$kbid;
        $this->setRedirect($link, $msg);
	}

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'knowledgebase';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
