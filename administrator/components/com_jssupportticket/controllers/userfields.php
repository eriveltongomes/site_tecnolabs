<?php

/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.controller');

class JSSupportticketControllerUserFields extends JSSupportTicketController {

    function __construct() {
        parent::__construct();
        $this->registerTask('add', 'edit');
    }

    function saveuserfield() {
        $this->storeUserFields('saveandclose');
    }

    function saveuserfieldsave() {
        $this->storeUserFields('save');
    }

    function saveuserfieldandnew() {
        $this->storeUserFields('saveandnew');
    }

    function storeUserFields() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $data = JFactory::getApplication()->input->post->getArray();
        $result = $this->getJSModel('userfields')->storeUserField();
        if ($result == SAVED) {
            $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering&ff='.$data['fieldfor'];
        }else{
            $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering&ff='.$data['fieldfor'];
        }
        $msg = JSSupportticketMessage::getMessage($result,'USER_FIELD');
        $this->setRedirect($link, $msg);
    }

    function fieldpublished() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldPublished($fieldid, 1); // published
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field mark as published');
        $this->setRedirect($link, $msg);
    }

    function fieldunpublished() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldPublished($fieldid, 0); // unpublished
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field mark as unpublished');
        $this->setRedirect($link, $msg);
    }

    function visitorfieldpublished() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->visitorFieldPublished($fieldid, 1); // published
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field mark as published');
        $this->setRedirect($link, $msg);
    }

    function visitorfieldunpublished() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->visitorFieldPublished($fieldid, 0); // unpublished
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field mark as unpublished');
        $this->setRedirect($link, $msg);
    }

    function fieldrequired() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldRequired($fieldid, 1); // required
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field mark as required');
        $this->setRedirect($link, $msg);
    }

    function fieldnotrequired() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldRequired($fieldid, 0); // not required
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field mark as not required');
        $this->setRedirect($link, $msg);
    }

    function fieldorderingup() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldOrderingUp($fieldid);
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field ordering down');
        $this->setRedirect($link, $msg);
    }

    function fieldorderingdown() {
        JSession::checkToken('post') or JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->fieldOrderingDown($fieldid);
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $msg = JText::_('Field ordering up');
        $this->setRedirect($link, $msg);
    }

    function adduserfield() {
        $fieldfor = JFactory::getApplication()->input->get('ff',1);
        JFactory::getApplication()->input->set('ff',$fieldfor);
        $layoutName = JFactory::getApplication()->input->set('layout', 'formuserfield');
        $this->display();
    }


    function removeuserfields() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $cid = JFactory::getApplication()->input->get('cid', array(), '', 'array');
        $fieldid = $cid[0];
        $result = $this->getJSModel('userfields')->deleteUserField($fieldid );
        $msg = JSSupportticketMessage::getMessage($result,'USER_FIELD');
        if ($result == DELETE_ERROR){
            $msg = JSSupportticketMessage::$recordid. ' ' . $msg;
        }
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $this->setRedirect($link, $msg);
    }

    function canceluserfield() {
        $msg = JSSupportticketMessage::getMessage(CANCEL,'USER_FIELD');
        $link = 'index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering';
        $this->setRedirect($link, $msg);
    }

    // new
    function getfieldsforcombobyfieldfor() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $fieldfor = JFactory::getApplication()->input->get('fieldfor','','string');
        $parentfield = JFactory::getApplication()->input->get('parentfield');
        $result = $this->getJSModel('userfields')->getFieldsForComboByFieldFor( $fieldfor, $parentfield );
        $result = json_encode($result);
        echo $result;
        JFactory::getApplication()->close();
    }

    function datafordepandantfield() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $val = JFactory::getApplication()->input->get('fvalue','','string'); 
        $childfield = JFactory::getApplication()->input->get('child'); 
        $result = $this->getJSModel('userfields')->dataForDepandantField( $val , $childfield);
        $result = json_encode($result);
        echo $result;
        JFactory::getApplication()->close();
    }

    function getoptionsforfieldedit() {
        $field = JFactory::getApplication()->input->get('field');
        $result = $this->getJSModel('userfields')->getOptionsForFieldEdit( $field);
        $result = json_encode($result);
        echo $result;
        JFactory::getApplication()->close();
    }

    function getsectiontofillvalues() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $field = JFactory::getApplication()->input->get('pfield');
        $result = $this->getJSModel('userfields')->getSectionToFillValues( $field );
        $result = json_encode($result);
        echo $result;
        JFactory::getApplication()->close();
    }        

    function display($cachable = false, $urlparams = false) {
        $document = JFactory::getDocument();
        $viewName = 'userfields';
        $layoutName = JFactory::getApplication()->input->get('layout', 'userfields');
        $viewType = $document->getType();
        $view = $this->getView($viewName, $viewType);
        $view->setLayout($layoutName);
        $view->display();
    }
}
?>
