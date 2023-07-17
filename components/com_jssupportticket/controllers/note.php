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

class JSSupportTicketControllerNote extends JSSupportTicketController{

	function __construct(){
		parent::__construct();
	}

    function getdownloadbyid(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $id = JFactory::getApplication()->input->get('id');
        $this->getJSModel('note')->getDownloadAttachmentById($id);
        JFactory::getApplication()->close();
    }

     function getTimeByNoteID() {
        JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $returnvalue = $this->getJSModel('note')->getTimeByNoteID();
        echo $returnvalue;
        JFactory::getApplication()->close();
    }

	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','note');
		$layoutName = JFactory::getApplication()->input->post->get('layout','note');
		$viewType = $document->getType();
		$view =  $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
