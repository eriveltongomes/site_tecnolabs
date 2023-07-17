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
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class jssupportticketViewRolePermissions extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;

		if($layoutName == 'rolepermissions'){
			$per = $user->checkUserPermission('View Role');
            if ($per == true){
	            $per_granted = true;
				$id = JFactory::getApplication()->input->get('roleid');
				$result = $this->getJSModel('rolepermissions')->getRolePermissions($id);
				$this->rolepermission = $result[1];
				$this->roledepartment = $result[2];
				$this->permissionbysection = $result[3];
				$this->per_granted = $per_granted;
			}            
		}
		require_once(JPATH_COMPONENT."/views/rolepermissions/rolepermissions_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
