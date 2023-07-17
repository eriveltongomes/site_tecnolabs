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

class JSSupportticketViewFeedback extends JSSupportTicketView
{
	function display($tpl = null){

        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Feedbacks'));
        if($layoutName == 'feedbacks'){
            JToolBarHelper::title(JText::_('Feedbacks'));
            $subject= $mainframe->getUserStateFromRequest( $option.'subject', 'subject',	'',	'string' );
            $ticketid= $mainframe->getUserStateFromRequest( $option.'ticketid', 'ticketid',  '', 'string' );
            $staffid= $mainframe->getUserStateFromRequest( $option.'staffid', 'staffid',  '', 'string' );
            $from= $mainframe->getUserStateFromRequest( $option.'from', 'from',  '', 'string' );
            $departmentid= $mainframe->getUserStateFromRequest( $option.'departmentid', 'departmentid',	'',	'string' );
            $result = $this->getJSModel('feedback')->getAllFeedbacks($subject,$ticketid,$staffid,$from,$departmentid,$limitstart,$limit);
            $total = $result[1];
            $this->feedbacks = $result[0];
            $this->lists = $result['2'];
            $pagination = new JPagination($total, $limitstart, $limit);
        }
        $this->pagination = $pagination;
        parent::display($tpl);
	}
}
?>
