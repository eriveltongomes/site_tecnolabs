<?php
/**
 * @package     aikon mod_aikon_awesome_compare
 *
 * @copyright   Copyright (C) 2014 aikon CMS. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

/**
 * Helper for mod_acompare
 *
 * @package     Aikon mod_compare
 * @since       1.0
 * 
 * 
 */
abstract class ModAikonAwesomeCompareHelper
{

    /**
     * get styles for container based on param object
     *
     * @param	JRegistry $params
     * @param   string    $uniqueId id for the selector
     * @package     aikon mod_aikon_awesome_compare
     * @return string
     */
	static public function getContainerStyle(JRegistry $params, $uniqueId){
		
        $snippet = "#{$uniqueId} { \n";
		// add box shadow
		$snippet	.= '    box-shadow: ' . $params->get('shadowSize') . ' ' . $params->get('shadowColor') . ";\n"  ;
		
		// add add border
		$snippet	.= '    border: solid ' . $params->get('borderSize') . ' ' . $params->get('borderColor') . ";\n" ;
		
		// add border radius
		$snippet	.= '    border-radius: ' . $params->get('borderRadius') . "px;\n" ;

        // add float
        $snippet	.= '    float: ' . $params->get('containerFloat') . ";\n" ;


        $width = $params->get('width');
        // default to 100% if width is 0
        if ($width == 0){
            $width = '100%';

          // default to px if no unit provided
        } elseif (!strpos($width, 'px')  && !strpos($width, '%') == -1){
            $width = $width . 'px;';
        }


        // assign width
        $snippet	.= '    width: ' . $width .";\n" ;


        // margin auto if width is px
		if (strpos($width, '%')){
            $snippet	.= "    margin-left: auto; \n    margin-right: auto;\n";
        } else {
            $snippet	.= "    width: 100%; \n    max-width: {$width}; \n    margin-left: auto; \n    margin-right: auto; \n";
        }

        // close style tag
        $snippet .= "} \n";

		return ($snippet);
	}
}
