<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;


class Extension
{
    public static function isExtensionActive($extension)
    {
        return \JComponentHelper::isEnabled($extension, true);
    }

    public static function getPluginsPath($file, $dir)
    {
        return rtrim(JPATH_ADMINISTRATOR, DS).DS.'components'.DS;
    }
}
