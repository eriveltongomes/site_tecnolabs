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

class JSSupportticketViewAnnouncements extends JSSupportTicketView
{
	function display($tpl = null){

        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");

        JToolBarHelper::title(JText::_('Announcements'));
        if($layoutName == 'announcements'){
            JToolBarHelper::title(JText::_('Announcements'));
            JToolBarHelper::addNew('editannouncements');
            JToolBarHelper::editList('editannouncements');
            JToolBarHelper::deleteList(JText::_('Are you sure to delete'),'deleteannouncement');
            $title= $mainframe->getUserStateFromRequest( $option.'filter_title', 'filter_title',	'',	'string' );
            $catid= $mainframe->getUserStateFromRequest( $option.'filter_categoryid', 'filter_categoryid',	'',	'string' );
            //$anntype= $mainframe->getUserStateFromRequest( $option.'filter_type', 'filter_type',	'',	'string' );
            $result = $this->getJSModel('announcements')->getAllAnnouncements($title,$catid,$limitstart,$limit);
            $total = $result[1];
            $this->announcements = $result[0];
            $this->lists = $result['2'];
            $pagination = new JPagination($total, $limitstart, $limit);
            $this->pagination = $pagination;
        }elseif($layoutName == 'formannouncement'){
            JToolBarHelper::save('saveannouncementsave','Save Announcement');
            JToolBarHelper::save2new('saveannouncementsavenew');
            JToolBarHelper::save('saveannouncement');

            $c_id = JFactory::getApplication()->input->get('cid', array (0), '', 'array');
            $c_id = $c_id[0];
            $this->id = $c_id;
            $result = $this->getJSModel('announcements')->getAnnouncementForForm($c_id);
            $isNew = true;
            if (isset($c_id) && ($c_id <> '' || $c_id <> 0)) $isNew = false;
            $text = $isNew ? JText::_('Add') : JText::_('Edit');
            JToolBarHelper::title(JText::_('Announcements') . ': <small><small>[ ' . $text . ' ]</small></small>');
            if ($isNew) JToolBarHelper::cancel('cancelannouncements');	else JToolBarHelper::cancel('cancelannouncements', 'Close');

            $this->lists = $result[1];
            if(isset($result[0]))
            $this->form_data = $result[0];
        }

        parent::display($tpl);
	}
}
?>
