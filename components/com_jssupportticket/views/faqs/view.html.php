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

class jssupportticketViewFaqs extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;       
        $can_delete = false;
		if($layoutName == 'formfaq'){
            $id = JFactory::getApplication()->input->get('id','');
            $permission = ($id == '') ? 'Add FAQ' : 'Edit FAQ';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
	            $this->id=$id;
	            $result = $this->getJSModel('faqs')->getFaqForForm($id);
	            $this->lists = $result[1];
	            if(isset($result[0])){
	            	$this->form_data = $result[0];
	            }
	    	}
			$this->per_granted = $per_granted;
		}elseif($layoutName == 'faqs'){
			$per = $user->checkUserPermission('View FAQ');
            if ($per == true){
	            $per_granted = true;
	            $can_delete = $user->checkUserPermission('Delete FAQ');
	            $subject= $mainframe->getUserStateFromRequest( $option.'filter_subject', 'filter_subject',	'',	'string' );
	            $catid= $mainframe->getUserStateFromRequest( $option.'filter_categoryid', 'filter_categoryid',	'',	'string' );
	            $result = $this->getJSModel('faqs')->getAllFaqs($subject,$catid,$limitstart,$limit);
	            $total = $result[1];
	            $this->faqs = $result[0];
	            $this->lists = $result['2'];
	            $pagination = new JPagination($total, $limitstart, $limit);
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;
		}elseif($layoutName == 'userfaqs'){
			$id = JFactory::getApplication()->input->get('id','');
			$subject= $mainframe->getUserStateFromRequest( $option.'filter_subject', 'filter_subject',	'',	'string' );
			$keywords= $mainframe->getUserStateFromRequest( $option.'filter_faq_keyword', 'filter_faq_keyword',	'',	'string' );

            $result = $this->getJSModel('faqs')->getUserCatAndFaqs($id,$subject,$keywords,$limitstart,$limit);
            $subcategoryfaqs = $this->getJSModel('faqs')->getSubCategoryFaqsByParentCat($id);
            $this->categories = $result['categories'];
            $this->categoryname = $result['categoryname'];
            $this->faqs = $result['faqs'];
            $this->lists = $result['lists'];
            $this->subcategoryfaqs = $subcategoryfaqs;
            $pagination = new JPagination($result['total'], $limitstart, $limit);
			$this->pagination = $pagination;
			$this->id = $id;
			
		}elseif($layoutName == 'userfaqdetail'){
			$id = JFactory::getApplication()->input->get('id','');
            $result = $this->getJSModel('faqs')->getUserFaqDetail($id);
            $this->subject = $result->subject;
            $this->detail = $result->detail;
            $this->categoryname = $result->categoryname;
		}
		require_once(JPATH_COMPONENT."/views/faqs/faqs_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
