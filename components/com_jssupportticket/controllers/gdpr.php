<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
 ^
 + Project: 	JS Tickets
 ^
*/

defined ('_JEXEC') or die('Not Allowed');
jimport('joomla.application.component.controller');

class JSSupportTicketControllergdpr extends JSSupportTicketController{

	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}

	function saveusereraserequest(){
		JSession::checkToken('post') or jexit(JText::_('JINVALID_TOKEN'));
		$Itemid =  JFactory::getApplication()->input->get('Itemid');
		$data = JFactory::getApplication()->input->post->getArray();
		if($data['id'] <> '')
			$id = $data['id'];
		$result = $this->getJSModel('gdpr')->storeUserEraseRequest($data);
		$link = 'index.php?option=com_jssupportticket&c=gdpr&layout=adderasedatarequest&Itemid='.$Itemid;
		$msg = JSSupportTicketMessage::getMessage($result,'GDPR');
        $this->setRedirect(JRoute::_($link , false), $msg);
	}

	function removeusereraserequest() {
		JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id','');
        if(is_numeric($id)){
        	$result = $this->getJSModel('gdpr')->deleteUserEraseRequest($id);
        	$msg = JSSupportticketMessage::getMessage($result,'GDPR');
		}
		$link = "index.php?option=com_jssupportticket&c=gdpr&layout=adderasedatarequest";
        $this->setRedirect($link, $msg);
    }

    function exportusereraserequest(){
    	$user = JSSupportTicketCurrentUser::getInstance();
        $uid = $user->getId();
        if(is_numeric($uid) && $uid > 0){
        	$return_value = $this->getJSModel('gdpr')->setUserExportByuid($uid);
        	if (!empty($return_value)) {
	            // Push the report now!
	            $msg = JText::_('User Data');
	            $name = 'export-overalll-reports';
	            header("Content-type: application/octet-stream");
	            header("Content-Disposition: attachment; filename=" . $name . ".xls");
	            header("Pragma: no-cache");
	            header("Expires: 0");
	            header("Lacation: excel.htm?id=yes");
	            print $return_value;
	            exit;
	        }else{
	        	$msg = JText::_("No data found for export.");
	        	$link = "index.php?option=com_jssupportticket&c=gdpr&layout=adderasedatarequest";
        		$this->setRedirect($link,$msg);
	        }
	    }else{
        	$link = "index.php?option=com_jssupportticket&c=gdpr&layout=adderasedatarequest";
        	$this->setRedirect($link);
        }
    }

    function display($cachable = false, $urlparams = false){
		$document = JFactory::getDocument();
		$viewName = 'gdpr';
		$layoutName = JFactory::getApplication()->input->post->get('layout', 'adderasedatarequest');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
