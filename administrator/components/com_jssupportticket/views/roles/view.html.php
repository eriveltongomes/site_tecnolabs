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

class JSSupportticketViewRoles extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		JToolBarHelper::title(JText::_('Roles'));
		if($layoutName == 'roles'){                          //roles
			JToolBarHelper::title(JText::_('Roles') );
			$search_role = JFactory::getApplication()->input->getString('filter_role');
			$result = $this->getJSModel('roles')->getAllRoles($search_role,$limitstart, $limit);
			$total = $result[1];
			if ( $total <= $limitstart ) $limitstart = 0;
			$pagination = new JPagination( $total, $limitstart, $limit );
			JToolBarHelper::addNew('editrole');
			JToolBarHelper::editList('editrole');
			JToolBarHelper::deleteList('Are you sure to delete','removerole');
			$this->roles = $result[0];
			if(isset($result[2])) $this->searchrole = $result[2];
			$this->pagination=$pagination;
		}elseif($layoutName == 'formrole'){                          //roles
            $cids = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id= $cids[0];
            $result =  $this->getJSModel('roles')->getRoleForForm($c_id);
            $this->roleid = $c_id;
            $this->role = $result[0];
            $this->rolepermission = $result[1];
            $this->roledepartment = $result[2];
            $isNew = true;
            if ( isset($result[0]->id) ) $isNew = false;
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('Role').'<small><small> ['.$text.']</small></small>' );
            JToolBarHelper::save('saverolesave','Save Role');
            JToolBarHelper::save2new('saveroleandnew');
            JToolBarHelper::save('saverole');
            if ($isNew)	JToolBarHelper::cancel('cancelrole'); else JToolBarHelper::cancel('cancelrole', 'Close');
            JHTML::_('behavior.formvalidator');
		}
		parent::display($tpl);		
	}
	
}
