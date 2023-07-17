<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	March 04, 2014
 ^
 + Project: 	JS Tickets
 ^ 
*/
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewUserPermissions extends JSSupportTicketView
{
	function display($tpl = null)	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		JToolBarHelper::title(JText::_('Assign User Permissions'));
		if($layoutName == 'userpermissions'){                          //rolepermissions
			$staffid = JFactory::getApplication()->input->get('staffid', '');
			$result = $this->getJSModel('userpermissions')->getUserPermissions($staffid);
			$this->userpermission = $result[1];
			$this->userdepartment = $result[2];
			JToolBarHelper::cancel('canceluserpermission'); 
			if(isset($result[0])) $this->roles = $result[0];
		}elseif($layoutName == 'formrole'){                          //roles
            $cids = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id= $cids[0];
            $result =  $this->getJSModel('userpermissions')->getRoleForForm($c_id);
            $this->roleid = $c_id;
            $this->role = $result[0];
            $this->rolepermission = $result[1];
            $this->roledepartment = $result[2];
            $isNew = true;
            if ( isset($result->id) ) $isNew = false;
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('Role').'<small><small> ['.$text.']</small></small>' );
            JToolBarHelper::save('saverolesave','Save Role');
            JToolBarHelper::save2new('saveroleandnew');
            JToolBarHelper::save('saverole');
            if ($isNew)	JToolBarHelper::cancel('cancelrole'); else JToolBarHelper::cancel('cancelrole', 'Close');
            JHTML::_('behavior.formvalidator');
		}
		//$this->pagination=$pagination;
		parent::display($tpl);		
	}
	
}
