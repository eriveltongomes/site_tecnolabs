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

class JSSupportticketViewKnowledgebase extends JSSupportTicketView
{
	function display($tpl = null){
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Tickets'));
        if($layoutName == 'categories'){
            JToolBarHelper::title(JText::_('Categories'));
            JToolBarHelper::addNew('editknowledgebasecategory');
            JToolBarHelper::editList('editknowledgebasecategory');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deleteknowledgebasecategy');
            $kbcattitle= $mainframe->getUserStateFromRequest( $option.'filter_kb_categories', 'filter_kb_categories',	'',	'string' );
            $result = $this->getJSModel('knowledgebase')->getAllCategories($kbcattitle,$limitstart,$limit);
            $total = $result['total'];
            $this->knowledgebase = $result['result'];
            $this->lists = $result['lists'];
            $pagination = new JPagination($total, $limitstart, $limit);
	        $this->pagination = $pagination;
        }elseif($layoutName == 'articles'){
            JToolBarHelper::title(JText::_('Knowledge base'));
            JToolBarHelper::addNew('editknowledgebasearticle');
            JToolBarHelper::editList('editknowledgebasearticle');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deleteknowledgebasearticle');
            $kbarttitle= $mainframe->getUserStateFromRequest( $option.'filter_kb_articletitle', 'filter_kb_articletitle',	'',	'string' );
            $kbcatid= $mainframe->getUserStateFromRequest( $option.'filter_kb_categoryid', 'filter_kb_categoryid',	'',	'string' );
            $typeid= $mainframe->getUserStateFromRequest( $option.'filter_kb_typeid', 'filter_kb_typeid',	'',	'string' );
            $result = $this->getJSModel('knowledgebase')->getAllArticles($kbarttitle,$kbcatid,$typeid,$limitstart,$limit);
            $total = $result[1];
            $this->article = $result[0];
            $this->lists = $result['2'];
            $pagination = new JPagination($total, $limitstart, $limit);
	        $this->pagination = $pagination;
        }elseif($layoutName == 'formcategory'){
			JToolBarHelper::save('saveknowledgebasecategorysave','Save Category');
			JToolBarHelper::save2new('saveknowledgebasecategorysavenew');
			JToolBarHelper::save('saveknowledgebasecategory');

			$c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
			$c_id = $c_id[0];
			$this->categoryid=$c_id;
			$result = $this->getJSModel('knowledgebase')->getCategoryForForm($c_id);
			$isNew = true;
			if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
			$text = $isNew ? JText::_('Add') : JText::_('Edit');
			JToolBarHelper::title(JText::_('Category') . ': <small><small>[ ' . $text . ' ]</small></small>');
			if ($isNew) JToolBarHelper::cancel('cancelknowledgebasecategory');	else JToolBarHelper::cancel('cancelknowledgebasecategory', 'Close');

			$this->lists = $result[1];
			if(isset($result[0]))
			$this->category = $result[0];
        }elseif($layoutName == 'formarticle'){
			JToolBarHelper::save('saveknowledgebasearticlesave','Save Knowledge base');
			JToolBarHelper::save2new('saveknowledgebasearticlesavenew');
			JToolBarHelper::save('saveknowledgebasearticle');

			$c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
			$c_id = $c_id[0];
			$this->id=$c_id;
			$result = $this->getJSModel('knowledgebase')->getArticleForForm($c_id);
			$isNew = true;
			if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
			$text = $isNew ? JText::_('Add') : JText::_('Edit');
			JToolBarHelper::title(JText::_('Knowledge base') . ': <small><small>[ ' . $text . ' ]</small></small>');
			if ($isNew) JToolBarHelper::cancel('cancelknowledgebasearticle');	else JToolBarHelper::cancel('cancelknowledgebasearticle', 'Close');

			$this->lists = $result[1];
			if(isset($result[0])){
				$this->article_form_data = $result[0];
				$this->articleattachments = $result[4];
			}
		}
	
        parent::display($tpl);
	}
}
?>
