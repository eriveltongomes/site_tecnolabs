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
			case 'formfaq':
				if($id){ //edit
					$pathway->addItem(JText::_('Add FAQ'), $commonpath."&c=faqs&layout=formfaq&Itemid=".$Itemid);
					$pathway->addItem(JText::_('Edit FAQ'), '');
				}else{ //NEW
					$pathway->addItem(JText::_('Add FAQ'), '');
				}
				break;
			case 'faqs':
				$pathway->addItem(JText::_('FAQs'),'');
				break;
			case 'userfaqs':
				$pathway->addItem(JText::_('FAQs'), '');
				break;
			case 'userfaqdetail':
				$pathway->addItem(JText::_('FAQs'),$commonpath."&c=faqs&layout=userfaqs&Itemid=".$Itemid);
				$pathway->addItem(JText::_('FAQ Detail'),'');
				break;
		}
	}
?>