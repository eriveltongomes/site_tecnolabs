<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Controllers;


use AcyChecker\Libraries\AcycController;
use AcyChecker\Services\ApiService;
use AcyChecker\Services\DebugService;
use AcyChecker\Services\SecurityService;
use AcyCheckerCmsServices\Ajax;
use AcyCheckerCmsServices\Database;
use AcyCheckerCmsServices\File;
use AcyCheckerCmsServices\Form;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Message;
use AcyCheckerCmsServices\Router;
use AcyCheckerCmsServices\Security;
use AcyCheckerCmsServices\Url;

class ConfigurationController extends AcycController
{
    public function __construct()
    {
        parent::__construct();

        $this->name = 'Configuration';
    }

    public function defaultTask()
    {
        $this->layout = 'default';

        $data = [
            'licenseKey' => $this->config->get('license_key'),
            'blacklist' => $this->config->get('blacklist'),
            'whitelist' => $this->config->get('whitelist'),
        ];

        $this->breadcrumb[Language::translation('ACYC_CONFIGURATION')] = Url::completeLink('configuration');

        $this->display($data);
    }

    public function save()
    {
        Form::checkToken();

        $formData = Security::getVar('array', 'config', []);
        if (empty($formData)) {
            Router::redirect(Url::completeLink('configuration', false, true));
        }

        $licenseKeyBeforeSave = $this->config->get('license_key');
        $isLicenseKeyUpdated = isset($formData['license_key']) && $licenseKeyBeforeSave !== $formData['license_key'];

        $status = $this->config->save($formData);

        if ($status) {
            Message::enqueueMessage(Language::translation('ACYC_SUCCESSFULLY_SAVED'));

            if ($isLicenseKeyUpdated) {
                // If we add a key or edit it, we try to attach it
                if (!empty($formData['license_key'])) {
                    $this->attachLicenseKey();
                } else {
                    // If we remove a key, we unlink it
                    $this->detachLicenseKey();
                }
            }
        } else {
            Message::enqueueMessage(Language::translation('ACYC_ERROR_SAVING'), 'error');
        }

        Router::redirect(Url::completeLink('configuration', false, true));
    }

    public function ajaxCheckDB()
    {
        $messagesNoHtml = [];

        //Parse SQL
        $queries = file_get_contents(ACYC_BACK.'tables.sql');
        $tables = explode('CREATE TABLE IF NOT EXISTS ', $queries);
        $structure = [];
        $createTable = [];
        $indexes = [];
        $constraints = [];

        // For each table, get its name, its column names and its indexes / pkey
        foreach ($tables as $oneTable) {
            if (strpos($oneTable, '`#__') !== 0) {
                continue;
            }

            //find tableName
            $tableName = substr($oneTable, 1, strpos($oneTable, '`', 1) - 1);

            $fields = explode("\n", $oneTable);
            foreach ($fields as $key => $oneField) {
                if (strpos($oneField, '#__') === 1) {
                    continue;
                }
                $oneField = rtrim(trim($oneField), ',');

                // Find the column names and remember them
                if (substr($oneField, 0, 1) == '`') {
                    $columnName = substr($oneField, 1, strpos($oneField, '`', 1) - 1);
                    $structure[$tableName][$columnName] = trim($oneField, ',');
                    continue;
                }

                // Remember the primary key and indexes of the table
                if (strpos($oneField, 'PRIMARY KEY') === 0) {
                    $indexes[$tableName]['PRIMARY'] = $oneField;
                } elseif (strpos($oneField, 'INDEX') === 0) {
                    $firstBackquotePos = strpos($oneField, '`');
                    $indexName = substr($oneField, $firstBackquotePos + 1, strpos($oneField, '`', $firstBackquotePos + 1) - $firstBackquotePos - 1);

                    $indexes[$tableName][$indexName] = $oneField;
                } elseif (strpos($oneField, 'FOREIGN KEY') !== false) {
                    preg_match('/(fk.*)\`/Uis', $fields[$key - 1], $matchesConstraints);
                    preg_match('/(#__.*)\`\(`(.*)`\)/Uis', $fields[$key + 1], $matchesTable);
                    preg_match('/\`(.*)\`/Uis', $oneField, $matchesColumn);
                    if (!empty($matchesConstraints) && !empty($matchesTable) && !empty($matchesColumn)) {
                        if (empty($constraints[$tableName])) $constraints[$tableName] = [];
                        $constraints[$tableName][$matchesConstraints[1]] = [
                            'table' => $matchesTable[1],
                            'column' => $matchesColumn[1],
                            'table_column' => $matchesTable[2],
                        ];
                    }
                }
            }
            $createTable[$tableName] = 'CREATE TABLE IF NOT EXISTS '.$oneTable;
        }


        $columnNames = [];
        $tableNames = array_keys($structure);

        // Good, we have the structure acyc SHOULD have, now we get the CURRENT structure so we can compare and add what's missing
        foreach ($tableNames as $oneTableName) {
            try {
                $columns = Database::loadObjectList('SHOW COLUMNS FROM '.$oneTableName);
            } catch (\Exception $e) {
                $columns = null;
            }

            if (!empty($columns)) {
                foreach ($columns as $oneField) {
                    $columnNames[$oneTableName][$oneField->Field] = $oneField->Field;
                }
                continue;
            }

            // We didn't get the columns, the table crashed or doesn't exist

            $errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200));
            $messagesNoHtml[] = ['error' => false, 'color' => 'blue', 'msg' => Language::translationSprintf('ACYC_CHECKDB_LOAD_COLUMNS_ERROR', $oneTableName, $errorMessage)];

            if (strpos($errorMessage, 'marked as crashed')) {
                //The table is apparently crashed, let's repair it!
                $repairQuery = 'REPAIR TABLE '.$oneTableName;

                try {
                    $isError = Database::query($repairQuery);
                } catch (\Exception $e) {
                    $isError = null;
                }

                if ($isError === null) {
                    $errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200));
                    $messagesNoHtml[] = ['error' => true, 'color' => 'red', 'msg' => Language::translationSprintf('ACYC_CHECKDB_REPAIR_TABLE_ERROR', $oneTableName, $errorMessage)];
                } else {
                    $messagesNoHtml[] = ['error' => false, 'color' => 'green', 'msg' => Language::translationSprintf('ACYC_CHECKDB_REPAIR_TABLE_SUCCESS', $oneTableName)];
                }
                continue;
            }

            //Table does not exist? lets create it...
            try {
                $isError = Database::query($createTable[$oneTableName]);
            } catch (\Exception $e) {
                $isError = null;
            }

            if ($isError === null) {
                $errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200));
                $messagesNoHtml[] = ['error' => true, 'color' => 'red', 'msg' => Language::translationSprintf('ACYC_CHECKDB_CREATE_TABLE_ERROR', $oneTableName, $errorMessage)];
            } else {
                $messagesNoHtml[] = ['error' => false, 'color' => 'green', 'msg' => Language::translationSprintf('ACYC_CHECKDB_CREATE_TABLE_SUCCESS', $oneTableName)];
            }
        }

        //Add missing columns in tables
        foreach ($tableNames as $oneTableName) {
            if (empty($columnNames[$oneTableName])) continue;

            $idealColumnNames = array_keys($structure[$oneTableName]);
            $missingColumns = array_diff($idealColumnNames, $columnNames[$oneTableName]);

            if (!empty($missingColumns)) {
                // Some columns are missing, add them
                foreach ($missingColumns as $oneColumn) {
                    $messagesNoHtml[] = ['error' => false, 'color' => 'blue', 'msg' => Language::translationSprintf('ACYC_CHECKDB_MISSING_COLUMN', $oneColumn, $oneTableName)];
                    try {
                        $isError = Database::query('ALTER TABLE '.$oneTableName.' ADD '.$structure[$oneTableName][$oneColumn]);
                    } catch (\Exception $e) {
                        $isError = null;
                    }
                    if ($isError === null) {
                        $errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200));
                        $messagesNoHtml[] = [
                            'error' => true,
                            'color' => 'red',
                            'msg' => Language::translationSprintf('ACYC_CHECKDB_ADD_COLUMN_ERROR', $oneColumn, $oneTableName, $errorMessage),
                        ];
                    } else {
                        $messagesNoHtml[] = [
                            'error' => false,
                            'color' => 'green',
                            'msg' => Language::translationSprintf('ACYC_CHECKDB_ADD_COLUMN_SUCCESS', $oneColumn, $oneTableName),
                        ];
                    }
                }
            }


            // Add missing index and primary keys
            $results = Database::loadObjectList('SHOW INDEX FROM '.$oneTableName, 'Key_name');
            if (empty($results)) {
                $results = [];
            }

            foreach ($indexes[$oneTableName] as $name => $query) {
                $name = Database::prepareQuery($name);
                if (in_array($name, array_keys($results))) continue;

                // The index / primary key is missing, add it

                $keyName = $name == 'PRIMARY' ? 'primary key' : 'index '.$name;

                $messagesNoHtml[] = ['error' => false, 'color' => 'blue', 'msg' => Language::translationSprintf('ACYC_CHECKDB_MISSING_INDEX', $keyName, $oneTableName)];
                try {
                    $isError = Database::query('ALTER TABLE '.$oneTableName.' ADD '.$query);
                } catch (\Exception $e) {
                    $isError = null;
                }

                if ($isError === null) {
                    $errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200));
                    $messagesNoHtml[] = [
                        'error' => true,
                        'color' => 'red',
                        'msg' => Language::translationSprintf('ACYC_CHECKDB_ADD_INDEX_ERROR', $keyName, $oneTableName, $errorMessage),
                    ];
                } else {
                    $messagesNoHtml[] = ['error' => false, 'color' => 'green', 'msg' => Language::translationSprintf('ACYC_CHECKDB_ADD_INDEX_SUCCESS', $keyName, $oneTableName)];
                }
            }

            if (empty($constraints[$oneTableName])) continue;
            $tableNameQuery = str_replace('#__', Database::getPrefix(), $oneTableName);
            $databaseName = Database::loadResult('SELECT DATABASE();');
            $foreignKeys = Database::loadObjectList(
                'SELECT i.TABLE_NAME, i.CONSTRAINT_TYPE, i.CONSTRAINT_NAME, k.REFERENCED_TABLE_NAME, k.REFERENCED_COLUMN_NAME, k.COLUMN_NAME
                FROM information_schema.TABLE_CONSTRAINTS AS i 
                LEFT JOIN information_schema.KEY_COLUMN_USAGE AS k ON i.CONSTRAINT_NAME = k.CONSTRAINT_NAME 
                WHERE i.TABLE_NAME = '.Database::escapeDB($tableNameQuery).' AND i.CONSTRAINT_TYPE = "FOREIGN KEY" AND i.TABLE_SCHEMA = '.Database::escapeDB($databaseName),
                'CONSTRAINT_NAME'
            );

            Database::query('SET foreign_key_checks = 0');

            foreach ($constraints[$oneTableName] as $constraintName => $constraintInfo) {
                $constraintTableNamePrefix = str_replace('#__', Database::getPrefix(), $constraintInfo['table']);
                $constraintName = str_replace('#__', Database::getPrefix(), $constraintName);
                if (empty($foreignKeys[$constraintName]) || (!empty($foreignKeys[$constraintName]) && ($foreignKeys[$constraintName]->REFERENCED_TABLE_NAME != $constraintTableNamePrefix || $foreignKeys[$constraintName]->REFERENCED_COLUMN_NAME != $constraintInfo['table_column'] || $foreignKeys[$constraintName]->COLUMN_NAME != $constraintInfo['column']))) {
                    $messagesNoHtml[] = [
                        'error' => false,
                        'color' => 'blue',
                        'msg' => Language::translationSprintf('ACYC_CHECKDB_WRONG_FOREIGN_KEY', $constraintName, $oneTableName),
                    ];

                    if (!empty($foreignKeys[$constraintName])) {
                        try {
                            $isError = Database::query('ALTER TABLE `'.$oneTableName.'` DROP FOREIGN KEY `'.$constraintName.'`');
                        } catch (\Exception $e) {
                            $isError = null;
                        }
                        if ($isError === null) {
                            $errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200));
                            $messagesNoHtml[] = [
                                'error' => true,
                                'color' => 'red',
                                'msg' => Language::translationSprintf('ACYC_CHECKDB_ADD_FOREIGN_KEY_ERROR', $constraintName, $oneTableName, $errorMessage),
                            ];
                            continue;
                        }
                    }

                    try {
                        $isError = Database::query(
                            'ALTER TABLE `'.$oneTableName.'` ADD CONSTRAINT `'.$constraintName.'` FOREIGN KEY (`'.$constraintInfo['column'].'`) REFERENCES `'.$constraintInfo['table'].'` (`'.$constraintInfo['table_column'].'`) ON DELETE NO ACTION ON UPDATE NO ACTION;'
                        );
                    } catch (\Exception $e) {
                        $isError = null;
                    }

                    if ($isError === null) {
                        $errorMessage = (isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200));
                        $messagesNoHtml[] = [
                            'error' => true,
                            'color' => 'red',
                            'msg' => Language::translationSprintf('ACYC_CHECKDB_ADD_FOREIGN_KEY_ERROR', $constraintName, $oneTableName, $errorMessage),
                        ];
                    } else {
                        $messagesNoHtml[] = [
                            'error' => false,
                            'color' => 'green',
                            'msg' => Language::translationSprintf('ACYC_CHECKDB_ADD_FOREIGN_KEY_SUCCESS', $constraintName, $oneTableName),
                        ];
                    }
                }
            }
            Database::query('SET foreign_key_checks = 1');
        }

        $result = '';
        if (empty($messagesNoHtml)) {
            $result = '<i class="acycicon-check-circle acyc__color__green"></i>';
        } else {
            $nbMessages = count($messagesNoHtml);
            foreach ($messagesNoHtml as $i => $oneMsg) {
                $result .= '<span style="color:'.$oneMsg['color'].'">'.$oneMsg['msg'].'</span>';
                if ($i < $nbMessages) {
                    $result .= '<br />';
                }
            }
        }

        Ajax::sendAjaxResponse('', ['html' => $result]);
    }

    public function seeLogs()
    {
        SecurityService::noCache();

        $type = Security::getVar('cmd', 'type');
        $types = [
            'batch' => 'batch_tests',
            'callback' => 'callback_controller',
            'individual' => 'individual_tests',
        ];

        if (!in_array($type, array_keys($types))) {
            exit;
        }

        $reportPath = DebugService::getLogPath($types[$type].'.log');

        if (file_exists($reportPath)) {
            try {
                $lines = 5000;
                $f = fopen($reportPath, 'rb');
                fseek($f, -1, SEEK_END);
                if (fread($f, 1) != "\n") {
                    $lines -= 1;
                }

                $report = '';
                while (ftell($f) > 0 && $lines >= 0) {
                    $seek = min(ftell($f), 4096); // Figure out how far back we should jump
                    fseek($f, -$seek, SEEK_CUR);
                    $report = ($chunk = fread($f, $seek)).$report; // Get the line
                    fseek($f, -mb_strlen($chunk, '8bit'), SEEK_CUR);
                    $lines -= substr_count($chunk, "\n"); // Move to previous line
                }

                while ($lines++ < 0) {
                    $report = substr($report, strpos($report, "\n") + 1);
                }
                fclose($f);
            } catch (\Exception $e) {
                $report = '';
            }
        }

        if (empty($report)) {
            $report = Language::translation('ACYC_EMPTY_LOG');
        }

        echo nl2br($report);
        exit;
    }

    public function deleteLogs()
    {
        $type = Security::getVar('cmd', 'type');
        $types = [
            'batch' => 'batch_tests',
            'callback' => 'callback_controller',
            'individual' => 'individual_tests',
        ];

        if (!in_array($type, array_keys($types))) {
            exit;
        }

        $reportPath = DebugService::getLogPath($types[$type].'.log');
        if (file_exists($reportPath)) {
            unlink($reportPath);
            Message::enqueueMessage(Language::translation('ACYC_LOGS_DELETED'));
        }

        Router::redirect(Url::completeLink('configuration', false, true));
    }

    public function attachLicenseKey()
    {
        $formData = Security::getVar('array', 'config', []);
        $licenseKey = $formData['license_key'];
        $this->config->save(['license_key' => $licenseKey]);

        if (empty($licenseKey)) {
            Message::enqueueMessage(Language::translation('ACYC_PLEASE_SET_VALID_LICENSE_KEY'), 'error');
        } else {
            $apiService = new ApiService();
            $result = $apiService->getCredits();
            if (empty($result['success'])) {
                Message::enqueueMessage($result['message'], 'error');
            }
        }

        if (!empty($result['success'])) {
            $newConfig = [
                'license_key' => $licenseKey,
                'credits_used_batch' => $result['data']['credits_used_batch'],
                'credits_used_simple' => $result['data']['remaining_credits_simple'],
                'remaining_credits_batch' => $result['data']['remaining_credits_batch'],
                'remaining_credits_simple' => $result['data']['remaining_credits_simple'],
                'license_level' => $result['data']['license_level'],
                'license_last_check' => time(),
                'license_end_date' => $result['data']['end_date'],
            ];
        } else {
            $newConfig = [
                'license_key' => '',
                'credits_used_batch' => 0,
                'remaining_credits_batch' => 0,
                'credits_used_simple' => 0,
                'remaining_credits_simple' => 0,
            ];
        }

        $this->config->save($newConfig);

        Router::redirect(Url::completeLink('configuration', false, true));
    }

    public function detachLicenseKey()
    {
        $newConfig = [
            'license_key' => '',
            'credits_used_batch' => 0,
            'remaining_credits_batch' => 0,
            'credits_used_simple' => 0,
            'remaining_credits_simple' => 0,
            'license_level' => '',
            'license_last_check' => time(),
            'license_end_date' => '',
        ];

        $this->config->save($newConfig);

        Router::redirect(Url::completeLink('configuration', false, true));
    }
}
