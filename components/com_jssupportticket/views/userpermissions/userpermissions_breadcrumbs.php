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
			case 'userpermissions':
				$pathway->addItem(JText::_('Staffs'),$commonpath."&c=staff&layout=staff&Itemid=".$Itemid);
				$pathway->addItem(JText::_('Staff permissions'),'');
				break;
		}
	}
?>