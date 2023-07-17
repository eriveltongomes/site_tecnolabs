<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;


class Miscellaneous
{
    public static function getGlobal($type)
    {
        $variables = [
            'db' => ['acydb', 'getDBO'],
            'doc' => ['acyDocument', 'getDocument'],
            'app' => ['acyapp', 'getApplication'],
        ];

        global ${$variables[$type][0]};
        if (${$variables[$type][0]} === null) {
            $method = $variables[$type][1];
            ${$variables[$type][0]} = \JFactory::$method();
        }

        return ${$variables[$type][0]};
    }

    public static function isLeftMenuNecessary()
    {
        return (!ACYC_J40 && Security::isAdmin() && !Form::isNoTemplate());
    }

    public static function getLeftMenu($name)
    {
        $isCollapsed = empty($_COOKIE['menuJoomlaAcyc']) ? '' : $_COOKIE['menuJoomlaAcyc'];

        $menus = [
            'dashboard' => ['title' => 'ACYC_DASHBOARD', 'class-i' => 'acycicon-dashboard', 'span-class' => ''],
            'database' => ['title' => 'ACYC_CLEAN_DATABASE', 'class-i' => 'acycicon-list_checked', 'span-class' => ''],
            'registration' => ['title' => 'ACYC_BLOCK_ON_REGISTRATION', 'class-i' => 'acycicon-user_blocked', 'span-class' => ''],
            'tests' => ['title' => 'ACYC_TESTS', 'class-i' => 'acycicon-list', 'span-class' => ''],
            'configuration' => ['title' => 'ACYC_CONFIGURATION', 'class-i' => 'acycicon-gear', 'span-class' => ''],
        ];

        $leftMenu = '<div id="acyc__joomla__left-menu" class="'.$isCollapsed.'">';
        foreach ($menus as $oneMenu => $menuOption) {
            $class = $name === $oneMenu ? 'acyc__joomla__left-menu--current' : '';
            $leftMenu .= '<a href="'.Url::completeLink($oneMenu).'" class="'.$class.'">';
            $leftMenu .= '<i class="'.$menuOption['class-i'].'"></i>';
            $leftMenu .= '<span class="'.$menuOption['span-class'].'">'.Language::translation($menuOption['title']).'</span></a>';
        }

        $leftMenu .= '<a href="#" id="acyc__joomla__left-menu--toggle"><i class="acycicon-keyboard_arrow_left"></i><span>'.Language::translation('ACYC_COLLAPSE').'</span></a>';

        $leftMenu .= '</div>';

        return $leftMenu;
    }

    public static function isPluginActive($plugin, $family = 'system')
    {
        $plugin = \JPluginHelper::getPlugin($family, $plugin);

        return !empty($plugin);
    }

    public static function menuOnly($link)
    {
        $menu = \JFactory::getApplication('site')->getMenu()->getActive();
        if (empty($menu) || $menu->link !== $link) {
            Router::redirect(Url::rootURI(), 'ACYC_UNAUTHORIZED_ACCESS', 'error');
        }
    }

    public static function disableCmsEditor()
    {
    }

    public static function session()
    {
        $sessionID = session_id();
        if (empty($sessionID)) {
            @session_start();
        }
    }
}
