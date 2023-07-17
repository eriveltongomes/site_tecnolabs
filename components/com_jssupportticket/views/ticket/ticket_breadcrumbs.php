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
		$pathway->addItem(JText::_('Control Panel'), $commonpath.'&c=jssupportticket&layout=controlpanel');
		switch($layoutName){
			case 'formticket':
				if($id){ //edit
					$pathway->addItem(JText::_('Edit Ticket'), '');
				}else{ //new
					$pathway->addItem(JText::_('Add Ticket'), '');
				}
				break;
			case 'mytickets':
				$pathway->addItem(JText::_('My Tickets'), $commonpath."&c=ticket&layout=mytickets&Itemid=".$Itemid);
				break;
			case 'ticketdetail':
				$pathway->addItem(JText::_('My Tickets'), $commonpath."&c=ticket&layout=mytickets&Itemid=".$Itemid);
				$pathway->addItem(JText::_('Ticket detail'), '');
			break;
		}
	}
?>
