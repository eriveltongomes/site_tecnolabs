<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;


use AcyChecker\Services\UrlService;

class Url
{
    public static function route($url, $xhtml = true, $ssl = null)
    {
        if (ACYC_J40) {
            global $Itemid;
            if (!Security::isAdmin() && !empty($Itemid) && strpos($url, 'Itemid') === false) {
                $url .= (strpos($url, '?') ? '&' : '?').'Itemid='.$Itemid;
            }
        }

        return \JRoute::_($url, $xhtml, $ssl === null ? 0 : $ssl);
    }

    public static function baseURI($pathonly = false)
    {
        return \JURI::base($pathonly);
    }

    public static function completeLink($link, $popup = false, $redirect = false, $forceNoPopup = false)
    {
        if (($popup || Form::isNoTemplate()) && $forceNoPopup == false) {
            $link .= '&'.Form::noTemplate();
        }

        return Url::route('index.php?option=com_acychecker&ctrl='.$link, !$redirect);
    }

    public static function prepareAjaxURL($url)
    {
        return htmlspecialchars_decode(Url::completeLink($url, true));
    }

    public static function getMenu()
    {
        global $Itemid;

        $jsite = \JFactory::getApplication('site');
        $menus = $jsite->getMenu();
        $menu = $menus->getActive();

        if (empty($menu) && !empty($Itemid)) {
            $menus->setActive($Itemid);
            $menu = $menus->getItem($Itemid);
        }

        return $menu;
    }

    public static function rootURI($pathonly = false, $path = null)
    {
        return \JURI::root($pathonly, $path);
    }

    public static function currentURL()
    {
        $url = isset($_SERVER['HTTPS']) ? 'https' : 'http';
        $url .= '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

        return $url;
    }

    public static function frontendLink($link, $complete = true)
    {
        if ($complete) {
            $link = 'index.php?option='.ACYC_COMPONENT.'&ctrl='.$link;
        }

        $mainUrl = UrlService::mainURL($link);

        return $mainUrl.$link;
    }
}
