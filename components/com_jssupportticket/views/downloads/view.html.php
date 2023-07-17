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

class jssupportticketViewDownloads extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;
        $can_delete = false;
		if($layoutName == 'formdownload'){
            $id = JFactory::getApplication()->input->get('id','');
            $permission = ($id == '') ? 'Add Download' : 'Edit Download';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
	            $this->id=$id;
	            $result = $this->getJSModel('downloads')->getDownloadForForm($id);
	            $this->lists = $result[1];
	            if(isset($result[0])){
           		$this->form_data = $result[0];
	            }
	            if(isset($result[4])){
	            	$this->downloadattachments = $result[4];
	            }
	        }
			$this->per_granted = $per_granted;
		}elseif($layoutName == 'downloads'){
            $per = $user->checkUserPermission('View Download');
            if ($per == true){
	            $per_granted = true;
	            $can_delete = $user->checkUserPermission('Delete Download');
	            $title= $mainframe->getUserStateFromRequest( $option.'filter_title', 'filter_title',	'',	'string' );
	            $catid= $mainframe->getUserStateFromRequest( $option.'filter_categoryid', 'filter_categoryid',	'',	'string' );
	            $result = $this->getJSModel('downloads')->getAllDownloads($title,$catid,$limitstart,$limit);
	            $total = $result[1];
	            $this->downloads = $result[0];
	            $this->lists = $result['2'];
	            $pagination = new JPagination($total, $limitstart, $limit);
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;
		}elseif($layoutName == 'userdownloads'){
            $id = JFactory::getApplication()->input->get('id','');
            $title = $mainframe->getUserStateFromRequest( $option.'filter_title', 'filter_title',	'',	'string' );
            $keyword = $mainframe->getUserStateFromRequest( $option.'filter_keyword', 'filter_keyword',	'',	'string' );
            $result = $this->getJSModel('downloads')->getUserCatAndDownloads($id,$title,$keyword,$limitstart,$limit);
            $subcategorydownloads = $this->getJSModel('downloads')->getSubCategoryDownloadsByParentCat($id);
            
            $this->categories = $result['categories'];
            $this->categoryname = $result['categoryname'];
            $this->downloads = $result['downloads'];
            $this->subcategorydownloads = $subcategorydownloads;
            $this->lists = $result['lists'];
            $pagination = new JPagination($result['total'], $limitstart, $limit);
	    $this->pagination = $pagination;
	    $this->id = $id;
		}
		require_once(JPATH_COMPONENT."/views/downloads/downloads_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
