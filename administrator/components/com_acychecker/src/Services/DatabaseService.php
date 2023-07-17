<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Services;

use AcyChecker\Libraries\AcycObject;
use AcyCheckerCmsServices\Extension;
use AcyCheckerCmsServices\Language;

class DatabaseService extends AcycObject
{
    public static function getTablesForSelect()
    {
        $tablesSelect = [
            [
                'value' => 'cms',
                'text' => Language::translationSprintf('ACYC_X_USERS', ACYC_CMS_TITLE),
            ],
        ];

        if (Extension::isExtensionActive('joomla' == ACYC_CMS ? ACYC_ACYMAILING_COMPONENT : ACYC_ACYMAILING_COMPONENT.'/index.php')) {
            $tablesSelect[] = [
                'value' => 'acymailing',
                'text' => Language::translationSprintf('ACYC_X_USERS', 'AcyMailing 7'),
            ];
        }

        if (Extension::isExtensionActive('joomla' == ACYC_CMS ? ACYC_ACYMAILING5_COMPONENT : ACYC_ACYMAILING5_COMPONENT.'/index.php')) {
            $tablesSelect[] = [
                'value' => 'acymailing5',
                'text' => Language::translationSprintf('ACYC_X_USERS', 'AcyMailing 5'),
            ];
        }

        return $tablesSelect;
    }

    public static function getConditionsForSelect()
    {
        return [
            [
                'value' => 'disposable',
                'text' => Language::translation('ACYC_IS_DISPOSABLE_EMAIL'),
                'description' => Language::translation('ACYC_IS_DISPOSABLE_EMAIL_DESC'),
            ],
            [
                'value' => 'accept_all',
                'text' => Language::translation('ACYC_IS_ACCEPT_ALL_EMAIL'),
                'description' => Language::translation('ACYC_IS_ACCEPT_ALL_EMAIL_DESC'),
            ],
            [
                'value' => 'free_domain',
                'text' => Language::translation('ACYC_IS_FREE_DOMAIN_EMAIL'),
                'description' => Language::translation('ACYC_IS_FREE_DOMAIN_EMAIL_DESC'),
            ],
            [
                'value' => 'role_based',
                'text' => Language::translation('ACYC_IS_ROLE_BASED_EMAIL'),
                'description' => Language::translation('ACYC_IS_ROLE_BASED_EMAIL_DESC'),
            ],
            [
                'value' => 'domain_not_exists',
                'text' => Language::translation('ACYC_IS_NON_EXISTENT_DOMAIN_EMAIL'),
                'description' => Language::translation('ACYC_IS_NON_EXISTENT_DOMAIN_EMAIL_DESC'),
            ],
            [
                'value' => 'invalid_smtp',
                'text' => Language::translation('ACYC_IS_INVALID_SMTP_EMAIL'),
                'description' => Language::translation('ACYC_IS_INVALID_SMTP_EMAIL_DESC'),
            ],
        ];
    }

    public static function getActionsForSelect($doNothing = true)
    {
        $actionSelect = [
            [
                'value' => 'do_nothing',
                'text' => Language::translation('ACYC_DECIDE_LATER'),
            ],
            [
                'value' => 'block_users',
                'text' => Language::translation('ACYC_BLOCK_USERS'),
            ],
            [
                'value' => 'delete_users',
                'text' => Language::translation('ACYC_DELETE_USERS'),
            ],
        ];

        if (!$doNothing) {
            array_shift($actionSelect);
        }

        return $actionSelect;
    }
}
