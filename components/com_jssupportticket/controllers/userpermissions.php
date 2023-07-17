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

class JSSupportTicketControllerUserPermissions extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view' , 'userpermissions');
		$layoutName = JFactory::getApplication()->input->get('layout','userpermissions');
		$viewType = $document->getType();
		$view =  $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
