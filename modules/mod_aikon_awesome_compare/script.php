<?php
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
/**
 * Script file of HelloWorld component
 */
class mod_aikon_awesome_compareInstallerScript
{
        /**
         * method to install the component
         *
         * @return void
         */
        function install($parent) 
        {

        }
 
        /**
         * method to uninstall the component
         *
         * @return void
         */
        function uninstall($parent) 
        {

        }
 
        /**
         * method to update the component
         *
         * @return void
         */
        function update($parent) 
        {

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

            ?>
			       <style>th{display:none;}table{text-align:center;}</style>
					<img style="float: none;margin-left:auto;margin-right:auto;" src="../modules/mod_aikon_awesome_compare/assets/aikonlogo.png" />
				<h1 style="line-height:150%;text-align:center; font-weight: normal; font-family:arial;font-size:15px; color: #333;">
					Display before and after like never before! Sleek and responsive design, Super easy to customize and a better price then ever before!<br>
					The most awesome way to display differences between products or services available, both beatiful and efficient!</h1>
				<p style="font-size:13px;font-weight:bold;text-align:center; font-family:arial;margin-bottom: 30px; color: #666;">For support and more information please visit our website www.aikoncms.com</p>

			
			<?php
        }
}