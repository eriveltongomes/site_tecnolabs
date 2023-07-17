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
			case 'formcategory':
				if($id){ //edit
					$pathway->addItem(JText::_('Add Category'), $commonpath."&c=knowledgebase&layout=formcategory&Itemid=".$Itemid);
					$pathway->addItem(JText::_('Edit category'), '');
				}else{ //new
					$pathway->addItem(JText::_('Add Category'), '');
				}
				break;
			case 'formarticle':
				if($id){ //edit
					$pathway->addItem(JText::_('Add Knowledge Base'), $commonpath."&c=knowledgebase&layout=formarticle&Itemid=".$Itemid);
					$pathway->addItem(JText::_('Edit knowledge base'), '');
				}else{ //new
					$pathway->addItem(JText::_('Add Knowledge Base'), '');
				}
				break;
			case 'categories':
				$pathway->addItem(JText::_('Categories'),'');
				break;
			case 'articles':
				$pathway->addItem(JText::_('Knowledge Base'),'');
				break;
			case 'userarticles':
			case 'usercatarticles':
				$pathway->addItem(JText::_('Knowledge Base'), '');
				break;
			case 'usercatarticledetails':
				$pathway->addItem(JText::_('Knowledge Base'),$commonpath."&c=knowledgebase&layout=userarticles&Itemid=".$Itemid);
				$pathway->addItem(JText::_('Knowledge Base Detail'),'');
				break;
		}
	}
?>