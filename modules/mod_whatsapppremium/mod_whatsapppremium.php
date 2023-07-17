<?php
/**
* @package 		mod_whatsapppremium - Whatsapp Premium
* @version		1.0.3
* @created		Nov 2018
* @author		MarvelExtensions
* @email		support@marvelextensions.com
* @website		http://www.marvelextensions.com
* @support		Forum - http://www.marvelextensions.com/support/create-a-new-ticket.html
* @copyright	Copyright (C) 2018 MarvelExtensions. All rights reserved.
* @license		http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
*/
  
	defined('_JEXEC') or die;
	// Include the breadcrumbs functions only once
	JLoader::register('ModWhatsAppPremiumHelper', __DIR__ . '/helper.php');
	// Get the breadcrumbs
	// $marvel_data  = ModWhatsAppPremiumHelper::getMarvelData($params);

	// get a parameter from the module's configuration
	$jqueryload 		= $params->get('jqueryload', '0');
	$selecttemplate 	= $params->get('selecttemplate', 'default');
	$moduleclass_sfx	= htmlspecialchars($params->get('moduleclass_sfx'), ENT_COMPAT, 'UTF-8');
	
	// load scripts
	$document	=	JFactory::getDocument();
	$baseurl	=	JURI::root();

	// load jquery if required
	if( $jqueryload ):
		$document->addScript( $baseurl.'modules/mod_whatsapppremium/assets/js/jquery.js' );	
	endif;
		
	
	switch ($selecttemplate)
	{
		case 'absolute':
		// add style sheet
		$document->addStyleSheet( $baseurl.'modules/mod_whatsapppremium/assets/css/absolutestyles.css' );
		require JModuleHelper::getLayoutPath('mod_whatsapppremium', $selecttemplate);
		break;
		case 'incontent':
		// add style sheet
		$document->addStyleSheet( $baseurl.'modules/mod_whatsapppremium/assets/css/incontentstyles.css' );
		require JModuleHelper::getLayoutPath('mod_whatsapppremium', $selecttemplate);
		break;		
	}