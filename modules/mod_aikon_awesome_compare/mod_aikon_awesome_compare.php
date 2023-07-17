<?php
/**
 * @package     aikon mod_aikon_awesome_compare
 *
 * @copyright   Copyright (C) 2014 Aikon CMS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

// Dependencies: Include helper
require_once dirname(__FILE__) . '/helper.php';

//get system info & other params we want
$app 		= JFactory::getApplication();
$doc	 	= JFactory::getDocument();
$assetsPath = 'modules/' . $module->module .'/assets/';


/* conditional scripts */
// add jQuery if needed
if ($params->get('loadJquery')){
	$doc->addScript('//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js');
}
// load jQuery Mobile cutsom if needed
if ($params->get('loadJqueryMobile')){
    $doc->addScript($assetsPath . 'js/jquery.mobile.custom.min.js');
}

/* scripts and stylesheeds */
$doc->addScript($assetsPath . 'js/compare.js');
$doc->addStylesheet($assetsPath . 'css/style.css');

/* perp var shorthands */
$uniqueId			    = $module->module . '_' . $module->id;
$handleColor            = $params->get('handleColor');
$handleColorActive      = $params->get('handleColorActive');
$handleColorHover       = $params->get('handleColorHover');
$firstImageSrc		    = $params->get('img1');
$secondImageSrc		    = $params->get('img2');
$handleType             = $params->get('handleType');
$handleTheme		    = $params->get('handleStyle');
$containerStyles		= ModAikonAwesomeCompareHelper::getContainerStyle($params, $uniqueId);


// prep handle class - if themed add classes
$handleClass = 'cd-handle ';
if ($handleType == 'theme') {
    $handleClass .= 'themed ';
    $handleClass .= $handleTheme;
}
// display
require JModuleHelper::getLayoutPath($module->module, $params->get('layout', 'default'));


