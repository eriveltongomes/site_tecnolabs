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
// Access check.
if (!JFactory::getUser()->authorise('core.manage', 'com_jssupportticket')) {
	throw new Exception(JText::_('Authorise error'),404);
}

$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla, 0, 3);

if (!defined('JVERSION')) {
    define('JVERSION', $jversion);
}

$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/bootstrap.min.css');
$language = JFactory::getLanguage();
if($language->isRTL()){
	$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadminrtl.css');
}
if (JVERSION < 3) {
    JHtml::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} elseif (JVERSION < 4)  {
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
$document->addScript('components/com_jssupportticket/include/js/tree.js');

require_once(JPATH_COMPONENT.'/JSApplication.php');
$base = JPATH_BASE;
$base = substr($base, 0, strlen($base) - 14); //remove administrator
require_once($base.'/components/com_jssupportticket/views/messageslayout.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/currentuser.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/constants.php');
require_once(JPATH_COMPONENT_ADMINISTRATOR.'/models/messages.php');
require_once (JPATH_COMPONENT_ADMINISTRATOR.'/models/cronjob.php');


function getCustomFieldClass() {
    include_once JPATH_COMPONENT_ADMINISTRATOR.'/include/classes/customfields.php';
    $obj = new customfields();
    return $obj;
}

$jinput = JFactory::getApplication()->input;
$task = $jinput->getCmd('task');
$c = '';
if (strstr($task, '.')) {
	$array = explode('.', $task);
	$c = $array[0];
	$task = $array[1];
} else {
	$c = $jinput->getCmd('c', 'jssupportticket');
	$task = $jinput->getCmd('task', 'display');
}
if ($c != '') {
	$path = JPATH_COMPONENT . '/controllers/' . $c . '.php';
	jimport('joomla.filesystem.file');
	if (JFile::exists($path)) {
		require_once ($path);
	} else {
		throw new Exception(JText::_('Unknown Controller: <br>' . $c . ':' . $path),500);		
	}
}
$c = 'JSSupportticketController'.$c;
$controller = new $c ();
$controller->execute($task);

$controller->redirect();

?>
