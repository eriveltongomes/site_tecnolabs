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

class jssupportticketViewRoles extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
		$per_assign_role = false;
        $per_granted = false;       
        $can_delete = false;

		if($layoutName == 'formrole'){
			$id = JFactory::getApplication()->input->get('id');
            $permission = ($id == '') ? 'Add Role' : 'Edit Role';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
	            $per_assign_role = $user->checkUserPermission('Assign Role Permissions');
				$result =  $this->getJSModel('roles')->getRoleForForm($id);
				$this->roleid = $id;
				$this->role = $result[0];
				$this->rolepermission = $result[1];
				$this->roledepartment = $result[2];
				if(isset($result[3])) $this->permissionbysection = $result[3];
			}
			$this->per_granted = $per_granted;
			$this->per_assign_role = $per_assign_role;
		}elseif($layoutName == 'roles'){
            $per = $user->checkUserPermission('View Role');
            if ($per == true){
	            $per_granted = true;
	            $can_delete = $user->checkUserPermission('Delete Role');			
				//$search_role = JFactory::getApplication()->input->get('filter_role');
				$search_role = $mainframe->getUserStateFromRequest($option . 'filter_role' , 'filter_role' , '' , 'string');
				$result = $this->getJSModel('roles')->getAllRoles($search_role,$limitstart, $limit);
				$total = $result[1];
				if ( $total <= $limitstart ) $limitstart = 0;
				$pagination = new JPagination( $total, $limitstart, $limit );
				$this->roles = $result[0];
				if(isset($result[2])) $this->searchrole = $result[2];
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;
		}
		require_once(JPATH_COMPONENT."/views/roles/roles_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
