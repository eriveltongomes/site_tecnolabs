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

class jssupportticketViewUserPermissions extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		$per_granted = false;
		if($layoutName == 'userpermissions'){
			$per = $user->checkUserPermission('View User');
            if ($per == true){
	            $per_granted = true;
				$staffid = JFactory::getApplication()->input->get('staffid', '');
				$result = $this->getJSModel('userpermissions')->getUserPermissions($staffid);
				if(isset($result[1])) $this->userpermission = $result[1];
				if(isset($result[2])) $this->userdepartment = $result[2];
				if(isset($result[3])) $this->permissionbysection = $result[3];
			}
			$this->per_granted = $per_granted;
        }
        require_once(JPATH_COMPONENT."/views/userpermissions/userpermissions_breadcrumbs.php");
        parent::display($tpl);
    }
}
?>
