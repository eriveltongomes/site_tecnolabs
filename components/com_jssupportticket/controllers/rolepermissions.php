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

class JSSupportTicketControllerRolePermissions extends JSSupportTicketController{
	function __construct(){
		parent::__construct();
		$this->registerTask('add', 'edit');
	}
    
    function getRolePermissionForStaff(){
    	JSession::checkToken('get') or jexit(JText::_('JINVALID_TOKEN'));
        $roleid = JFactory::getApplication()->input->get('roleid');
        $c_p_grant = JFactory::getApplication()->input->get('cp'); // change user permission allow or not 
        $result = $this->getJSModel('rolepermissions')->getRolePermissionsAjax($roleid,$c_p_grant);
        echo json_encode($result);
        JFactory::getApplication()->close();
    }
	
	function display($cachable = false, $urlparams = false){
		$document =  JFactory::getDocument();
		$viewName = JFactory::getApplication()->input->post->get('view','rolepermissions');
		$layoutName = JFactory::getApplication()->input->post->get('layout','rolepermissions');
		$viewType = $document->getType();
		$view = $this->getView($viewName, $viewType);
		$view->setLayout($layoutName);
		$view->display();
	}
}
?>
