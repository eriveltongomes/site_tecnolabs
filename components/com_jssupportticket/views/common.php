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

$option = 'com_jssupportticket';
$Itemid = JFactory::getApplication()->input->get('Itemid');
$layoutName = JFactory::getApplication()->input->get('layout','');
$config = $this->getJSModel('config')->getConfigs();
$user = JSSupportTicketCurrentUser::getInstance();
$mainframe = JFactory::getApplication();
$limit = $mainframe->getUserStateFromRequest( 'global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int' );
if($limit > 0){
	$limitstart = $mainframe->getUserStateFromRequest( $option.'.limitstart', 'limitstart', 0, 'int' );
}else{
	$limitstart = 0;
}

JPluginHelper::importPlugin('jssupportticket');
// $dispatcher = JDispatcher::getInstance();
// $dispatcher->trigger( 'changeConfig', array(&$config));
JFactory::getApplication()->triggerEvent( 'changeFormField', array(&$result));

$this->option = $option;
$this->Itemid = $Itemid;
$this->layoutname = $layoutName;
$this->config = $config;
$this->user = $user;
?>
<input type="hidden" id="joomlinkforjs" value="<?php echo JURI::root(); ?>" />
