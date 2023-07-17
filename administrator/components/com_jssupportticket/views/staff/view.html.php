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

class JSSupportticketViewStaff extends JSSupportTicketView
{
    function display($tpl = null)
	{
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");        
        JToolBarHelper::title(JText::_('Tickets'));
        if($layoutName == 'formstaff'){
            $cids = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id= $cids[0];
            $result =  $this->getJSModel('staff')->getStaffforForm($c_id);
            $this->staffid = $c_id;
            if(isset($result[0])) $this->staff = $result[0];
            if(isset($result[1])) $this->user = $result[1];
            if(isset($result[2])) $this->lists = $result[2];
            if(isset($result[3])) $this->userpermissions = $result[3];
            if(isset($result[4])) $this->userdepartments = $result[4];
            
            $isNew = true;
            if ( isset($result[0]->id) ) $isNew = false;
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('Staff member').'<small><small> ['.$text.']</small></small>' );
            JToolBarHelper::save('savestaffmembersave','Save Staff Member');
            JToolBarHelper::save2new('savestaffmemberandnew');
            JToolBarHelper::save('savestaffmember');
            if ($isNew)	JToolBarHelper::cancel('cancelstaff'); else JToolBarHelper::cancel('cancelstaff', 'Close');
            JHTML::_('behavior.formvalidator');
        }elseif($layoutName == 'staffmembers'){                          //vehicles
            JToolBarHelper::title(JText::_('Staff members') );
            $username = JFactory::getApplication()->input->getString('filter_sm_username');
            $roleid = $mainframe->getUserStateFromRequest( $option.'filter_sm_roleid', 'filter_sm_roleid',	'',	'int' );
            $statusid = $mainframe->getUserStateFromRequest($option.'filter_sm_statusid', 'filter_sm_statusid', '', 'int');
            $result = $this->getJSModel('staff')->getAllStaffMembers($username,$roleid,$statusid ,$limitstart, $limit);
            $total = $result[1];
            if ( $total <= $limitstart ) $limitstart = 0;
            $pagination = new JPagination( $total, $limitstart, $limit );
            JToolBarHelper::addNew('editstaffmember');
            JToolBarHelper::editList('editstaffmember');
            JToolBarHelper::deleteList('Are you sure to delete','removestaffmember');
            $this->staffmembers = $result[0];
            $this->lists = $result[2];
            $this->pagination=$pagination;
        }elseif($layoutName == 'users'){										// users
            JToolBarHelper::title(JText::_('Users'));
            JToolBarHelper::editList();
            $form = 'com_jssupportticket.users.list.';
            $searchname	= $mainframe->getUserStateFromRequest( $form.'searchname', 'searchname','', 'string' );
            $searchusername	= $mainframe->getUserStateFromRequest( $form.'searchusername', 'searchusername','', 'string' );
            $searchrole	= $mainframe->getUserStateFromRequest( $form.'searchrole', 'searchrole','', 'string' );
            $result =  $this->getJSModel('staff')->getAllUsers($searchname,$searchusername,$searchrole, $limitstart, $limit);
            $items = $result[0];
            $total = $result[1];
            $lists = $result[2];
            if ( $total <= $limitstart ) $limitstart = 0;
            $pagination = new JPagination( $total, $limitstart, $limit );
            $this->pagination=$pagination;
            $this->items = $items;
            $this->lists = $lists;
        }
        parent::display($tpl);
	}
}
?>
