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

class jssupportticketViewDepartment extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;       
        $can_delete = false;
		if($layoutName == 'formdepartment'){
			$id = JFactory::getApplication()->input->get('id','');
            $permission = ($id == '') ? 'Add Department' : 'Edit Department';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
				$per_granted = true;
				$result = $this->getJSModel('department')->getDepartmentForForm($id);
				$this->lists = $result[1];
				if(isset($result[0])) $this->department = $result[0];
				$this->depid = $id;
			}
			$this->per_granted = $per_granted;
		}elseif($layoutName == 'departments'){
			$per = $user->checkUserPermission('View Department');
            if ($per == true){
	            $per_granted = true;
	            $can_delete = $user->checkUserPermission('Delete Department');
				//$searchdepartment = JFactory::getApplication()->input->get('filter_departmentname');
				$searchdepartment = $mainframe->getUserStateFromRequest( $option.'filter_departmentname', 'filter_departmentname',	'',	'string' );
				//$searchtype = $mainframe->getUserStateFromRequest( $option.'filter_type', 'filter_type',	'',	'string' );
				$result = $this->getJSModel('department')->getAllDepartments($searchdepartment,$limitstart,$limit);
				$total = $result[1];
				$this->department = $result[0];
				$this->lists = $result[2];
				$pagination = new JPagination($total, $limitstart, $limit);
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;		
		}
		require_once(JPATH_COMPONENT."/views/department/department_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
