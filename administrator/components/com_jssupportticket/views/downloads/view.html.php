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

class JSSupportticketViewDownloads extends JSSupportTicketView
{
	function display($tpl = null){

        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Downloads'));
        if($layoutName == 'downloads'){
            JToolBarHelper::title(JText::_('Downloads'));
            JToolBarHelper::addNew('editdownloads');
            JToolBarHelper::editList('editdownloads');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deletedownloads');
            $title= $mainframe->getUserStateFromRequest( $option.'filter_title', 'filter_title',	'',	'string' );
            $catid= $mainframe->getUserStateFromRequest( $option.'filter_categoryid', 'filter_categoryid',	'',	'string' );
            $result = $this->getJSModel('downloads')->getAllDownloads($title,$catid,$limitstart,$limit);
            $total = $result[1];
            $this->downloads = $result[0];
            $this->lists = $result['2'];
            $pagination = new JPagination($total, $limitstart, $limit);
            $this->pagination = $pagination;
        }elseif($layoutName == 'formdownload'){
            JToolBarHelper::save('savedownloadsave','Save Download');
            JToolBarHelper::save2new('savedownloadsavenew');
            JToolBarHelper::save('savedownload');

            $c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id = $c_id[0];
            $this->id = $c_id;
            $result = $this->getJSModel('downloads')->getDownloadForForm($c_id);
            $isNew = true;
            if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('Downloads') . ': <small><small>[ ' . $text . ' ]</small></small>');
            if ($isNew) JToolBarHelper::cancel('canceldownloads');	else JToolBarHelper::cancel('canceldownloads', 'Close');

            $this->lists = $result[1];
            if(isset($result[0])){
				$this->form_data = $result[0];
				$this->downloadattachments = $result[4];				
			}
        }

	
        parent::display($tpl);
	}
}
?>
