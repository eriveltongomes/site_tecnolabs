<?php
/**
 + Created by:	Ahmad Bilal
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
				www.joomsky.com, ahmad@joomsky.com
 * Created on:	Dec 2, 2009
 ^
 + Project: 		JS Jobs 
 * File Name:	module/hotjsjobs.php
 ^ 
 * Description: Module for JS Jobs
 ^ 
 * History:		1.0.2 - Nov 27, 2010
 ^ 
 */
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion < 3){
	$document->addScript('components/com_jssupportticket/js/jquery.js');
	JHtml::_('behavior.mootools');
}else{
	JHtml::_('bootstrap.framework');
	JHtml::_('jquery.framework');
}
$document->addStyleSheet('components/com_jssupportticket/include/css/jssupportticketdefault.css');
$title = $params->get('title', 'JS Support Ticket Knowledge Base');
$showtitle = $params->get('showtitle', 1);
$titlebackgroundcolor = $params->get('titlebackgroundcolor', 1);
$titlecolor = $params->get('titlecolor', 1);
$viewall = $params->get('viewall', 1);
$maxrecord = $params->get('maxrecord', 10);
$recordperrow = $params->get('recordperrow', 1);
$textoverflow = $params->get('textoverflow', 2);

if($params->get('Itemid')) $itemid = $params->get('Itemid');			
else $itemid = JFactory::getApplication()->input->get('Itemid');
$lang = JFactory::getLanguage();
$lang->load('com_jssupportticket', JPATH_ADMINISTRATOR, null, true);
$moduleclass_sfx = $params->get('moduleclass_sfx');
$componentPath =  JPATH_ADMINISTRATOR.'/components/com_jssupportticket/';
require_once $componentPath.'JSApplication.php';
require_once 'components/com_jssupportticket/include/css/color.php';
$content = JSSupportTicketModel::getJSModelForMP('moduleplugin')->getContentForMP($title,$showtitle,$titlebackgroundcolor,$titlecolor,2,$viewall,$maxrecord,$recordperrow,$textoverflow,$itemid,'knowledgebase',$moduleclass_sfx);
?>
