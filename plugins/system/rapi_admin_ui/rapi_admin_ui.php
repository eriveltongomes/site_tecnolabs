<?php
/**
 * @version     1.1 2020 Rapicode
 * @copyright   Copyright (C) 2020 All rights reserved.
 * @license     GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRapi_Admin_Ui extends JPlugin
{
    public function onAfterDispatch()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            $params = $this->params;
            $document = JFactory::getDocument();

            $style = "";
            $script = "";

            $font_family = $params->get('font_family', 'Oswald');

            $login_opacity = $params->get('login_opacity', '0.7');
            $login_form_background_color = $params->get('login_form_background_color', '#ffffff');
            $login_form_button_color = $params->get('login_form_button_color', '#ffffff');
            $login_form_button_background = $params->get('login_form_button_background', 'info');
            $login_form_button_radius = $params->get('login_form_button_radius', '4');

            $login_background_color = $params->get('login_background_color', '#1a3867');
            $login_background_image = $params->get('login_background_image', '');

            $small_logo = $params->get('small_logo', '');
            $medium_logo = $params->get('medium_logo', '');

            $inClass = $params->get('inclass', 'fade-in');
            $outClass = $params->get('outclass', 'fade-out');
            $inDuration = $params->get('inDuration', '1000');
            $outDuration = $params->get('outDuration', '500');
            $preloader = $params->get('preloader', '0');
            $preloader_color = $params->get('preloader_color', '#1a3867');
            $preloader_svg = $params->get('preloader_svg', 'puff');
            $preloader_radius = $params->get('preloader_radius', '50%');
			$root_path = JURI::root();
            $preloader_image = "<img style=\"background-color:{$preloader_color}; border-radius: {$preloader_radius}; padding: 10px;\" src=\"{$root_path}plugins/system/rapi_admin_ui/images/{$preloader_svg}.svg\" />";

            $background_effect = $params->get('background_effect', 'ripples');

            // Font Family
            if ($font_family) {
                $style .= "body {font-family: '{$font_family}', Helvetica, Arial, sans-serif !important;}";
                $style .= "label, input, button, select, textarea {font-family: '{$font_family}', Helvetica, Arial, sans-serif !important;}";
            }

            // Login Bottom Bar
            $style .= ".view-login .navbar-fixed-bottom { padding: 10px 10px 0 10px; background-color: rgba(0,0,0,0.7);}";

            // Login Form Button Color
            $style .= ".view-login .btn-primary { color: {$login_form_button_color};}";

            // Login Form Button Radius
            $style .= ".view-login .btn-large { border-radius: {$login_form_button_radius}px !important;}";

            // Login Form Button Background
            if ($login_form_button_background != "default") {
                $style .= ".view-login .btn-large {display: none;}";
                $script .= "jQuery(document).ready(function() {
                jQuery('.view-login .btn-large').addClass('btn-{$login_form_button_background}');
                jQuery('.view-login .btn-large').css('display', 'block');
            });";
            }

            // Login Background Ripples Effect
            if ($background_effect == "ripples") {
                $document->addScript(JURI::root().'plugins/system/rapi_admin_ui/js/jquery.ripples-min.js');
                $script .= "
                jQuery(document).ready(function() {
                    jQuery('body.com_login').ripples({
	                    resolution: 512,
	                    dropRadius: 20,
	                    perturbance: 0.04,
                    });
                });
                ";
            }

            // Login Background ParticleGround Effect
            if ($background_effect == "particleground") {
                $document->addScript(JURI::root().'plugins/system/rapi_admin_ui/js/jquery.particleground.min.js');
                $script .= "
                jQuery(document).ready(function() {
                    jQuery('body.com_login').particleground();
                });
                ";
            }

            // Transition Effect
            if ($params->get('transition')) {
                $document->addStyleSheet(JURI::root().'plugins/system/rapi_admin_ui/css/page_transition.min.css');
                $document->addScript(JURI::root().'plugins/system/rapi_admin_ui/js/page_transition.min.js');
                $script .= "jQuery(document).ready(function() {
	            jQuery('body').animsition({
    	            inClass: '{$inClass}',
    	            outClass: '{$outClass}',
    	            inDuration: {$inDuration},
    	            outDuration: {$outDuration},
    	            linkElement: 'a:not([target=\"_blank\"]):not([href^=\"#\"]):not([href^=\"javascript\"]):not(a:not([href]))',
    	            loading: {$preloader},
    	            loadingParentElement: 'html',
    	            loadingClass: 'animsition-loading',
    	            loadingInner: '{$preloader_image}',
    	            browser: [ 'animation-duration', '-webkit-animation-duration'],
    	            transition: function(url){ window.location.href = url; }
  	            });
            });
            ";
            }

            // Login Form Background Color
            list($form_r, $form_g, $form_b) = array_map('hexdec', str_split(ltrim($login_form_background_color, '#'), 2));
            $style .= ".view-login .well {background-color: rgba({$form_r},{$form_g},{$form_b},{$login_opacity}); border: 1px solid transparent;}";

            // Login Background Color
            $style .= "body.com_login {background-color: {$login_background_color};}";

            // Login Background Image
            if ($login_background_image) {
				$login_background_image = JURI::root(). $login_background_image;
                $style .= "body.com_login {background-image: url('{$login_background_image}'); background-repeat: no-repeat; background-size: cover;}";
            }

            // Joomla Small Logo
            if ($small_logo) {
				$small_logo = JURI::root(). $small_logo;
                $style .= ".icon-joomla::before {content: ''; background-image:url('{$small_logo}'); background-size: 100% 100%; display: inline-block; height:20px; width:20px;}";
            }

            // Joomla Medium Logo
            if ($medium_logo) {
				$medium_logo = JURI::root(). $medium_logo;
                $style .= ".container-logo .logo, .login.well img {display: none;}";
                $script .= "jQuery(document).ready(function() {
                jQuery('.container-logo .logo, .login.well img').attr('src','{$medium_logo}');
                jQuery('.container-logo .logo, .login.well img').css('display', 'block');
            });";
            }

            // Add Font Family From Google Fonts
            $font_family = str_replace(" ", "+", $font_family);
            $document->addStyleSheet("https://fonts.googleapis.com/css?family={$font_family}:400,700");

            // Add Style & Script
            $document->addStyleDeclaration($style);
            $document->addScriptDeclaration($script);

            return true;
        }
    }
}
