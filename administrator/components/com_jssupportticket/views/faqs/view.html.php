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

class JSSupportticketViewFaqs extends JSSupportTicketView
{
	function display($tpl = null){

        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('FAQs'));
        if($layoutName == 'faqs'){
            JToolBarHelper::title(JText::_('FAQs'));
            JToolBarHelper::addNew('editfaqs');
            JToolBarHelper::editList('editfaqs');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deletefaq');
            $subject= $mainframe->getUserStateFromRequest( $option.'filter_subject', 'filter_subject',	'',	'string' );
            $catid= $mainframe->getUserStateFromRequest( $option.'filter_categoryid', 'filter_categoryid',	'',	'string' );
            $result = $this->getJSModel('faqs')->getAllFaqs($subject,$catid,$limitstart,$limit);
            $total = $result[1];
            $this->faqs = $result[0];
            $this->lists = $result['2'];
            $pagination = new JPagination($total, $limitstart, $limit);
            $this->pagination = $pagination;
        }elseif($layoutName == 'formfaq'){
            JToolBarHelper::save('savefaqsave','Save FAQ');
            JToolBarHelper::save2new('savefaqsavenew');
            JToolBarHelper::save('savefaq');

            $c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id = $c_id[0];
            $this->id=$c_id;
            $result = $this->getJSModel('faqs')->getFaqForForm($c_id);
            $isNew = true;
            if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('FAQs') . ': <small><small>[ ' . $text . ' ]</small></small>');
            if ($isNew) JToolBarHelper::cancel('cancelfaqs');	else JToolBarHelper::cancel('cancelfaqs', 'Close');

            $this->lists = $result[1];
            if(isset($result[0]))
            $this->form_data = $result[0];
        }

        parent::display($tpl);
	}
}
?>
