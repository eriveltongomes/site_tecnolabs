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

class com_jssupportticketInstallerScript
{
	/**
	 * method to install the component
	 *
	 * @return void
	 */
	function install($parent) 
	{
		// $parent is the class calling this method
		//$parent->getParent()->setRedirectURL('index.php?option=com_jsdocumentation');
	}

	/**
	 * method to uninstall the component
	 *
	 * @return void
	 */
	function uninstall($parent) 
	{
		// $parent is the class calling this method
		echo '<p>' . JText::_('Jstickets Uninstall Text') . '</p>';
	}

	/**
	 * method to update the component
	 *
	 * @return void
	 */
	function update($parent) 
	{
		// $parent is the class calling this method
		
	}

	/**
	 * method to run before an install/update/uninstall method
	 *
	 * @return void
	 */
	function preflight($type, $parent) 
	{
	}

	/**
	 * method to run after an install/update/uninstall method
	 *
	 * @return void
	 */
	function postflight($type, $parent) 
	{
		
		jimport('joomla.installer.helper');
		$user = JFactory::getUser();
		$email = $user->email;
		$created = date('Y-m-d H:i:s');
		$db = JFactory::getDbo();
		$query = "SELECT COUNT(id) FROM `#__js_ticket_email`";
		$db->setQuery($query);
		$result = $db->loadResult();
		if($result == 0){
			$query = "INSERT INTO `#__js_ticket_email`(autoresponce,priorityid,email,status,created) VALUES (1,1,\"".$email."\",1,'".$created."');";
			
			$db->setQuery($query);
			$db->execute();
		}
		
	}

}

