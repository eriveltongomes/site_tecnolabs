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

class JSSupportticketControllerFaqs extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function editfaqs() {
        JFactory::getApplication()->input->set('layout', 'formfaq');
        $this->display();
    }

    function savefaq() {
        $this->storefaq('saveandclose');
    }

    function savefaqsave() {
        $this->storefaq('save');
    }

    function savefaqsavenew() {
        $this->storefaq('saveandnew');
    }

    function storefaq($callfrom) {
        JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('faqs')->storeFaq($data);
        if($result == SAVED) {
            if ($callfrom == 'saveandclose') {
                $link = 'index.php?option=com_jssupportticket&c=faqs&layout=faqs';
            } elseif ($callfrom == 'save') {
                $link = 'index.php?option=com_jssupportticket&c=faqs&layout=formfaq&cid[]='.JSSupportticketMessage::$recordid;
            } elseif ($callfrom == 'saveandnew') {
                $link = 'index.php?option=com_jssupportticket&c=faqs&layout=formfaq';
            }
        }elseif($result == ALREADY_EXIST) {
            $link = 'index.php?option=com_jssupportticket&c=faqs&layout=formfaq';
        }else{
            $link = 'index.php?option=com_jssupportticket&c=faqs&layout=faqs';
        }
        $msg = JSSupportticketMessage::getMessage($result,'FAQ');
        $this->setRedirect($link, $msg);
    }

    function cancelfaqs() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'FAQ');
        $link = 'index.php?option=com_jssupportticket&c=faqs&layout=faqs';
        $this->setRedirect($link, $msg);
    }

    function deletefaq() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cids = JFactory::getApplication()->input->get('cid',array(0),'array');
        $id = $cids[0];
        $result = $this->getJSModel('faqs')->deleteFaq($id);
        $msg = JSSupportticketMessage::getMessage($result,'FAQ');
        $link = 'index.php?option=com_jssupportticket&c=faqs&layout=faqs';
        $this->setRedirect($link, $msg);
    }

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'faqs';
        $layoutName = JFactory::getApplication()->input->get('layout');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }

}

?>
