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

class jssupportticketViewStaff extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;       
        $can_delete = false;
        $assign_permissions = false;
        $assign_role = false;

		if($layoutName == 'formstaff'){
			$id = JFactory::getApplication()->input->get('id');
			$permission = ($id == '') ? 'Add User' : 'Edit User';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
		        $assign_role = $user->checkUserPermission('Assign Role To User');
		        $assign_permissions = $user->checkUserPermission('Assign User Permissions');
				$result =  $this->getJSModel('Staff')->getStaffforForm($id);
				$this->staffid = $id;
				if(isset($result[0])) $this->staff = $result[0];
				if(isset($result[1])) $this->username = $result[1];
				if(isset($result[2])) $this->lists = $result[2];
				if(isset($result[3])) $this->userpermissions = $result[3];
				if(isset($result[4])) $this->userdepartments = $result[4];
				if(isset($result[5])) $this->permissionbysection = $result[5];
				$this->assign_role = $assign_role;
				$this->assign_permissions = $assign_permissions;
			}
			$this->per_granted = $per_granted;
		}elseif($layoutName == 'users'){
            $searchname	= $mainframe->getUserStateFromRequest( 'searchname', 'searchname','', 'string' );
            $searchusername	= $mainframe->getUserStateFromRequest( 'searchusername', 'searchusername','', 'string' );
            $result =  $this->getJSModel('Staff')->getAllUsers($searchname,$searchusername,'',$limitstart, $limit);
            $items = $result[0];
            $total = $result[1];
            $lists = $result[2];
            if ( $total <= $limitstart ) $limitstart = 0;
            $pagination = new JPagination( $total, $limitstart, $limit );
            $this->items = $items;
            $this->lists = $lists;
			$this->pagination = $pagination;
		}elseif($layoutName == 'staff'){
			$per = $user->checkUserPermission('View User');
            if ($per == true){
	            $per_granted = true;
	            $can_delete = $user->checkUserPermission('Delete User');
	            //$username = JFactory::getApplication()->input->get('filter_sm_username');
	            $username = $mainframe->getUserStateFromRequest( $option.'filter_sm_username', 'filter_sm_username','',	'string');
	            $roleid = $mainframe->getUserStateFromRequest( $option.'filter_sm_roleid', 'filter_sm_roleid',	'',	'int' );
	            $statusid = $mainframe->getUserStateFromRequest($option.'filter_sm_statusid', 'filter_sm_statusid', '', 'int');
	            $result = $this->getJSModel('Staff')->getAllStaffMembers($username,$roleid,$statusid ,$limitstart, $limit);
	            $total = $result[1];
	            if ( $total <= $limitstart ) $limitstart = 0;
	            $pagination = new JPagination( $total, $limitstart, $limit );
	            $this->staffmembers = $result[0];
	            $this->lists = $result[2];
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;
		}elseif($layoutName == 'staffprofile'){
			$uid = $user->getId();
			$result = $this->getJSModel('Staff')->getStaffSettings($uid);
			$this->profiledata=$result;
		}
		require_once(JPATH_COMPONENT."/views/staff/staff_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
