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

class jssupportticketViewAnnouncements extends JSSupportTicketView{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;
        $can_delete = false;

		if($layoutName == 'formannouncement'){
            $id = JFactory::getApplication()->input->get('id','');
            $permission = ($id == '') ? 'Add Announcement' : 'Edit Announcement';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
	            $this->id = $id;
	            $result = $this->getJSModel('announcements')->getAnnouncementForForm($id);
	            $this->lists = $result[1];
	            if(isset($result[0])){
	            	$this->form_data = $result[0];
	            }
            }
            $this->per_granted = $per_granted;
		}elseif($layoutName == 'announcements'){
			$per = $user->checkUserPermission('View Announcement');
			if ($per == true){
				$per_granted = true;
				$can_delete = $user->checkUserPermission('Delete Announcement');
	            $id = JFactory::getApplication()->input->get('id',''); // may this not need
	            $this->id = $id;
	            $result = $this->getJSModel('announcements')->getAnnouncementForForm($id);
	            $this->lists = $result[1];
	            if(isset($result[0]))
	            $this->form_data = $result[0];	
	            $title= $mainframe->getUserStateFromRequest( $option.'filter_title', 'filter_title',	'',	'string' );
	            $catid= $mainframe->getUserStateFromRequest( $option.'filter_categoryid', 'filter_categoryid',	'',	'string' );
	            //$anntype= $mainframe->getUserStateFromRequest( $option.'filter_type', 'filter_type',	'',	'string' );
	            $result = $this->getJSModel('announcements')->getAllAnnouncements($title,$catid,$limitstart,$limit);
	            $total = $result[1];
	            $this->announcements = $result[0];
	            $this->lists = $result['2'];
	            $pagination = new JPagination($total, $limitstart, $limit);
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;
		}elseif($layoutName == 'userannouncements'){
            $id = JFactory::getApplication()->input->get('id','');
            $title = $mainframe->getUserStateFromRequest( $option.'filter_title', 'filter_title',	'',	'string' );
            $keyword = $mainframe->getUserStateFromRequest( $option.'filter_keyword', 'filter_keyword',	'',	'string' );
            $result = $this->getJSModel('announcements')->getUserCatAndAnnouncements($id,$title,$keyword,$limitstart,$limit);
            $subcategoryannouncement = $this->getJSModel('announcements')->getAnnouncementSubCategoryByParentCat($id);

            $this->categories = $result['categories'];
            $this->categoryname = $result['categoryname'];
            $this->announcements = $result['announcements'];
            $this->subcategoryannouncement = $subcategoryannouncement;
            $this->lists = $result['lists'];
            $pagination = new JPagination($result['total'], $limitstart, $limit);
			$this->pagination = $pagination;
			$this->id = $id;
		}elseif($layoutName == 'userannouncementdetail'){
			$id = JFactory::getApplication()->input->get('id','');
            $result = $this->getJSModel('announcements')->getUserannouncementDetail($id);
            $this->subject = $result->subject;
            $this->detail = $result->detail;
            $this->categoryname = $result->categoryname;
		}
		require_once(JPATH_COMPONENT."/views/announcements/announcements_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
