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

class jssupportticketViewknowledgebase extends JSSupportTicketView
{
	function display($tpl = null){
		require_once(JPATH_COMPONENT."/views/common.php");
        $per_granted = false;
        $can_delete = false;

		if($layoutName == 'formcategory'){
			$id = JFactory::getApplication()->input->get('id','');
            $permission = ($id == '') ? 'Add Category' : 'Edit Category';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
				$result = $this->getJSModel('knowledgebase')->getCategoryForForm($id);
				$this->lists = $result[1];
				if(isset($result[0])){
					$this->category = $result[0];
				}
				$this->categoryid=$id;
			}
			$per_granted;
			$this->per_granted = $per_granted;
		}elseif($layoutName == 'formarticle'){
			$id = JFactory::getApplication()->input->get('id','');
            $permission = ($id == '') ? 'Add Knowledge Base' : 'Edit Knowledge Base';
            $per = $user->checkUserPermission($permission);
            if ($per == true){
	            $per_granted = true;
				$this->articleid=$id;
				$result = $this->getJSModel('knowledgebase')->getArticleForForm($id);
				$this->lists = $result[1];
				if(isset($result[0])){
					$this->article = $result[0];
					$this->article_attachments = $result[4];
				}
			}
			$this->per_granted = $per_granted;	
		}elseif($layoutName == 'categories'){
            $per = $user->checkUserPermission('View Category');
            if ($per == true){
	            $per_granted = true;
	            $can_delete = $user->checkUserPermission('Delete Category');                    
	            $kbcattitle= $mainframe->getUserStateFromRequest( $option.'filter_kb_categories', 'filter_kb_categories',	'',	'string' );
				$result = $this->getJSModel('knowledgebase')->getAllCategories($kbcattitle,$limitstart,$limit);
				$total = $result['total'];
				$this->knowledgebase = $result['result'];
				$this->lists = $result['lists'];
				$pagination = new JPagination($total, $limitstart, $limit);
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;
		}elseif($layoutName == 'articles'){
            $per = $user->checkUserPermission('View Knowledge Base');
            if ($per == true){
	            $per_granted = true;
	            $can_delete = $user->checkUserPermission('Delete Knowledge Base');			
				$kbarttitle= $mainframe->getUserStateFromRequest( $option.'filter_kb_articletitle', 'filter_kb_articletitle',	'',	'string' );
	            $kbcatid= $mainframe->getUserStateFromRequest( $option.'filter_kb_categoryid', 'filter_kb_categoryid',	'',	'string' );
	            //$typeid= $mainframe->getUserStateFromRequest( $option.'filter_kb_typeid', 'filter_kb_typeid',	'',	'string' );
	            $result = $this->getJSModel('knowledgebase')->getAllArticles($kbarttitle,$kbcatid,$limitstart,$limit);
				$total = $result[1];
				$this->article = $result[0];
	            $this->lists = $result['2'];
				$pagination = new JPagination($total, $limitstart, $limit);
				$this->pagination = $pagination;
			}
			$this->per_granted = $per_granted;
			$this->can_delete = $can_delete;
		}elseif($layoutName == 'userarticles'){
			$kbarttitle = $mainframe->getUserStateFromRequest( $option.'filter_kb_articletitle', 'filter_kb_articletitle',	'',	'string' );
			$kbartkeyword = $mainframe->getUserStateFromRequest( $option.'filter_kb_articlekeyword', 'filter_kb_articlekeyword',	'',	'string' );
            $result = $this->getJSModel('knowledgebase')->getKnowledgebaseCat($kbarttitle,$kbartkeyword,$limitstart,$limit);
			$total = $result['total'];
			$this->articles = $result['articles'];
            $this->categories = $result['categories'];
            $this->lists = $result['lists'];
			$pagination = new JPagination($total, $limitstart, $limit);
			$this->pagination = $pagination;
		}elseif($layoutName == 'usercatarticles'){
			$id = JFactory::getApplication()->input->get('id','');
            $result = $this->getJSModel('knowledgebase')->getUserKnowledgebase($id,$limitstart,$limit);
            $subcategories = $this->getJSModel('knowledgebase')->getSubCategoryKnowledge($id);
			$total = $result['total'];
			$this->articles = $result['articles'];
			$this->category = $result['category'];
            $this->categories = $result['categories'];
			$pagination = new JPagination($total, $limitstart, $limit);
			$this->pagination = $pagination;
			$this->id = $id;
			$this->subcategories = $subcategories;
		}elseif($layoutName == 'usercatarticledetails'){
			$id = JFactory::getApplication()->input->get('id','');
			if($id != 0){
	            $result = $this->getJSModel('knowledgebase')->getUserArticleDetails($id);
				$article = $result['article'];
				if(isset($article)){
			        $article_id = $article->id;
                                $subject = $article->subject;
				$name = $article->name;
				$detail = $article->content;
				$article_id = $article->id;
				$this->subject=$subject;
				$this->name=$name;
				$this->detail=$detail;
				$this->article_id=$article_id;
            $this->articleattachments = $result['articleattachments'];}
			}
		}
		require_once(JPATH_COMPONENT."/views/knowledgebase/knowledgebase_breadcrumbs.php");
		parent::display($tpl);
	}
}
?>
