<?php
/**
 * @version     1.3 2019 Rapicode
 * @copyright   Copyright (C) 2019 All rights reserved.
 * @license     GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

class plgSystemRapi_Copyproof extends JPlugin
{
    public function onAfterDispatch()
    {
        $app = JFactory::getApplication();

        if ($app->isSite()) {
            $params = $this->params;
            $menus = $params->get('menus');
            $menuID = JRequest::getCmd('Itemid');

            if (!empty($menus)) {
                if ((!in_array($menuID, $menus, true))) {
                    return;
                }
            }

            $doc = JFactory::getDocument();

            $user = JFactory::getUser();
            $user_groups = $user->getAuthorisedGroups();
            $restricted_groups = $this->params->get('restrict_groups', array());
            settype($restricted_groups, 'array');

            if(!in_array(1, $restricted_groups) && !empty($restricted_groups)){
                array_push($restricted_groups, 1);
            }elseif(in_array(1, $restricted_groups) && count($restricted_groups) == 1){
                array_push($restricted_groups, 9);
            }
			
			$right_click = $params->get("right_click", 1);
			$right_click = ($right_click == 1) ? "" : "//";
			
			$ctrl = $params->get("ctrl", 1);
			$ctrl = ($ctrl == 1) ? "" : "//";
			
			$drag = $params->get("drag", 1);
			$drag = ($drag == 1) ? "" : "//";
			
			$select = $params->get("select", 1);
			$select = ($select == 1) ? "" : "//";
			
			$f12 = $params->get("f12", 1);
			$f12 = ($f12 == 1) ? "" : "//";

            if(count(array_diff($user_groups, $restricted_groups)) == 0) {
				$script = "function disableSelection(n) {
					'undefined' != typeof n.onselectstart ? n.onselectstart = function() {
						return !1
					} : 'undefined' != typeof n.style.MozUserSelect ? n.style.MozUserSelect = 'none' : n.onmousedown = function() {
						return !1
					}, n.style.cursor = 'default'
				}

				function md(n) {
					try {
						if (2 == event.button || 3 == event.button) return !1
					} catch (n) {
						if (3 == n.which) return !1
					}
				}
				document.onkeydown = function(n) {
					// page load key
					return n = n || window.event, 67 == n.keyCode ? !1 : void 0
				}, window.addEventListener('keydown', function(n) {
					// disable ctrl + * (print - save page - view source , ...)
					{$ctrl}!n.ctrlKey || 65 != n.which && 66 != n.which && 67 != n.which && 70 != n.which && 73 != n.which && 80 != n.which && 83 != n.which && 85 != n.which && 86 != n.which || n.preventDefault()
				}), document.keypress = function(n) {
					// disable ctrl + * (print - save page - view source , ...)
					{$ctrl}return n.ctrlKey && (65 == n.which || 66 == n.which || 70 == n.which || 67 == n.which || 73 == n.which || 80 == n.which || 83 == n.which || 85 == n.which || 86 == n.which), !1
				}, document.onkeydown = function(n) {
					// disable f12
					{$f12}return n = n || window.event, 123 == n.keyCode || 18 == n.keyCode ? !1 : void 0
				}, window.onload = function() {
					// disable select (ctrl + a , mouse select , 2 click , 3 click)
					{$select}disableSelection(document.body)
				}, document.oncontextmenu = function() {
					// Disable Right Click
					{$right_click}return !1
				}, document.ondragstart = function() {
					// disable mouse drag (image , link , ...)
					{$drag}return !1
				}
					, document.onmousedown = md;";
				$doc->addScriptDeclaration($script);
            }
        }
    }
}
