<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Libraries;

use AcyChecker\Classes\ConfigurationClass;
use AcyCheckerCmsServices\User;

class AcycObject
{
    public $config;
    public $cmsUserVars;

    public function __construct()
    {
        $this->cmsUserVars = User::getCmsUserDbStructure();
        $this->config = $this->getConfiguration(get_class($this));
    }

    public function getConfiguration($currentClass)
    {
        static $configClass = null;
        if ($configClass === null) {
            $configClass = 'AcyChecker\\Classes\\ConfigurationClass' === $currentClass ? $this : new ConfigurationClass();
            $configClass->load();
        }

        return $configClass;
    }
}
