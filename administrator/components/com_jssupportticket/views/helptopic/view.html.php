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

class JSSupportticketViewHelpTopic extends JSSupportTicketView
{
	function display($tpl = null)
	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");		
		JToolBarHelper::title(JText::_('Tickets'));
		if($layoutName == 'formhelptopic'){
			$cids = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
			$c_id= $cids[0];
			$result=$this->getJSModel('helptopic')->gethelpTopicForForm($c_id);
			$this->helptopicid = $c_id;
			if(isset($result[0])) $this->helptopic = $result[0];
			$this->lists = $result[1];
			$isNew = true;
			if ( isset($result[0]->id) ) $isNew = false;
			$text = $isNew ? JText::_('Add') : JText::_('Edit');
			JToolBarHelper::title(JText::_('Help Topic').'<small><small> ['.$text.']</small></small>' );
			JToolBarHelper::save('savehelptopicsave','Save Help Topic');
			JToolBarHelper::save2new('savehelptopicandnew');
			JToolBarHelper::save('savehelptopic');
			if ($isNew)	JToolBarHelper::cancel('cancelhelptopic'); else JToolBarHelper::cancel('cancelhelptopic', 'Close');
			JHTML::_('behavior.formvalidator');
		}elseif($layoutName == 'helptopices'){                          //helptopics
			JToolBarHelper::title(JText::_('Help Topics') );
			$helptopic = JFactory::getApplication()->input->getString('filter_ht_helptopic');
			$statusid = $mainframe->getUserStateFromRequest($option.'filter_ht_statusid', 'filter_ht_statusid', '', 'string');
			$result = $this->getJSModel('helptopic')->getAllHelpTopices($helptopic,  $statusid ,$limitstart, $limit);
			$total = $result[1];
			if ( $total <= $limitstart ) $limitstart = 0;
			$pagination = new JPagination( $total, $limitstart, $limit );
			JToolBarHelper::addNew('edithelptopic');
			JToolBarHelper::editList('edithelptopic');
			//JToolBarHelper::deleteList('JS_ARE_YOU_SURE_DELETE_HELPTOPIC','removehelptopic');
			JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'removehelptopic');
			$this->helptopic = $result[0];
			$this->lists = $result[2];
			$this->pagination = $pagination;
		}
		
		parent::display($tpl);
	}
}
?>
