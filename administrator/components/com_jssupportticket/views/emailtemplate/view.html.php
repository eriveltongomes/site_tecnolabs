<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
 ^
 + Project: 	JS Tickets
 ^
*/

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.html.pagination');

class JSSupportticketViewEmailtemplate extends JSSupportTicketView
{
	function display($tpl = null)
	{
        require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
        JToolBarHelper::title(JText::_('Tickets'));
        if($layoutName == 'emailtemplate'){
            $templatefor = JFactory::getApplication()->input->get('tf');
            switch($templatefor){
                case 'ew-tk' : $text = JText::_('New Ticket'); break;
                case 'sntk-tk' : $text = JText::_('Staff Ticket'); break;
                case 'ew-md' : $text = JText::_('New Department'); break;
                case 'ew-sm' : $text = JText::_('New Staff'); break;
                case 'ew-ht' : $text = JText::_('New Help Topic'); break;
                case 'rs-tk' : $text = JText::_('Reassign Ticket'); break;
                case 'cl-tk' : $text = JText::_('Close Ticket'); break;
                case 'dl-tk' : $text = JText::_('Delete Ticket'); break;
                case 'mo-tk' : $text = JText::_('Mark Overdue'); break;
                case 'be-tk' : $text = JText::_('Ban email'); break;
                case 'be-trtk' : $text = JText::_('Ban email try to create ticket'); break;
                case 'dt-tk' : $text = JText::_('Department Transfer'); break;
                case 'ebct-tk' : $text = JText::_('Ban Email and Close Ticket'); break;
                case 'ube-tk' : $text = JText::_('Unban Email'); break;
                case 'rsp-tk' : $text = JText::_('Response Ticket'); break;
                case 'rpy-tk' : $text = JText::_('Reply Ticket'); break;
                case 'tk-ew-ad' : $text = JText::_('New Ticket Admin Alert'); break;
                case 'lk-tk' : $text = JText::_('Lock Ticket'); break;
                case 'ulk-tk' : $text = JText::_('Unlock ticket'); break;
                case 'minp-tk' : $text = JText::_('In Progress Ticket'); break;
                case 'pc-tk' : $text = JText::_('Ticket Priority Is Changed By'); break;
                case 'ml-ew' : $text = JText::_('New Mail Receviced'); break;
                case 'ml-rp' : $text = JText::_('New Mail Message Recevied'); break;
                case 'fd-bk' : $text = JText::_('Feedback Email to User'); break;
                case 'no-rp' : $text = JText::_('User Reply On Closed Ticket Email'); break;
                case 'd-us-da' : $text = JText::_('Erase user data request for user'); break;
                case 'd-us-da-ad' : $text = JText::_('Erase user data request for admin'); break;
                case 'u-da-de' : $text = JText::_('User data deleted'); break;
            }

            JToolBarHelper::title(JText::_('Email Templates').' <small><small>['.$text.'] </small></small>');
            JToolBarHelper::save('saveemailtemplate','Save email template');
            $template = $this->getJSModel('emailtemplate')->getTemplate($templatefor);
            $ufields = $this->getJSModel('userfields')->getUserfieldsfor(1);
            $this->template = $template;
            $this->ufields = $ufields;
            $this->templatefor = $templatefor;
        }

        parent::display($tpl);
	}
}
?>
