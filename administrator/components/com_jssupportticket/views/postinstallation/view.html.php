<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	March 04, 2014
 ^
 + Project: 	JS Tickets
 ^ 
*/
 
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JSSupportticketViewPostInstallation extends JSSupportTicketView
{
	function display($tpl = null)	{
		require_once(JPATH_COMPONENT_ADMINISTRATOR."/views/common.php");
		JToolBarHelper::title(JText::_('Installation Complete'));
		if($layoutName == 'stepone'){
			JToolBarHelper::title(JText::_('General Configurations') );
			$result = $this->getJSModel('postinstallation')->getConfigurationValues();
			$this->result = $result;
		}elseif($layoutName == 'steptwo'){
			JToolBarHelper::title(JText::_('Ticket Configurations') );
			$result = $this->getJSModel('postinstallation')->getConfigurationValues();
			$this->result = $result;
		}elseif($layoutName == 'stepthree'){
			JToolBarHelper::title(JText::_('Feedback Configurations') );
			$result = $this->getJSModel('postinstallation')->getConfigurationValues();
			$this->result = $result;
		}
		parent::display($tpl);
	}
	
}
