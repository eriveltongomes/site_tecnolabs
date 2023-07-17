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

	$commonpath="index.php?option=com_jssupportticket";
	$pathway = $mainframe->getPathway();
	if ($config['cur_location'] == 1) {
		$pathway->addItem(JText::_('Control Panel'), $commonpath."&c=jssupportticket&layout=controlpanel&Itemid=".$Itemid);
		switch($layoutName){
			case 'formmessage':
				$pathway->addItem(JText::_('Mail'), $commonpath."&c=mail&layout=inbox&Itemid=".$Itemid);
				$pathway->addItem(JText::_('Send message'), '');
				break;
			case 'inbox':
				$pathway->addItem(JText::_('Mail'), $commonpath."&c=mail&layout=inbox&Itemid=".$Itemid);
				$pathway->addItem(JText::_('inbox'), '');
				break;
			case 'outbox':
				$pathway->addItem(JText::_('Mail'), $commonpath."&c=mail&layout=inbox&Itemid=".$Itemid);
				$pathway->addItem(JText::_('Outbox'), '');
				break;
			case 'message':
				$pathway->addItem(JText::_('Mail'), $commonpath."&c=mail&layout=inbox&Itemid=".$Itemid);
				$pathway->addItem(JText::_('Message'), '');
				break;
		}
	}
?>