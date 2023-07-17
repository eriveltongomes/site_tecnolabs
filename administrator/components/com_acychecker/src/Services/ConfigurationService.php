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

class ConfigurationService extends AcycObject
{
    public static function prepareTableSelect(&$data)
    {
        $data['tables_select'] = [
            [
                'value' => 'cms',
                'text' => Language::translationSprintf('ACYC_X_USERS', ACYC_CMS_TITLE),
            ],
        ];

        $acym7Extension = 'joomla' == ACYC_CMS ? ACYC_ACYMAILING_COMPONENT : ACYC_ACYMAILING_COMPONENT.'/index.php';
        if (Extension::isExtensionActive($acym7Extension)) {
            $data['tables_select'][] = [
                'value' => 'acymailing',
                'text' => Language::translationSprintf('ACYC_X_USERS', 'AcyMailing 7'),
            ];
        }
        $acy5Extension = 'joomla' == ACYC_CMS ? ACYC_ACYMAILING5_COMPONENT : ACYC_ACYMAILING5_COMPONENT.'/index.php';
        if (Extension::isExtensionActive($acy5Extension)) {
            $data['tables_select'][] = [
                'value' => 'acymailing5',
                'text' => Language::translationSprintf('ACYC_X_USERS', 'AcyMailing 5'),
            ];
        }
    }

    public static function prepareConditionSelect(&$data)
    {
        $data['condition_select'] = [
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
}
