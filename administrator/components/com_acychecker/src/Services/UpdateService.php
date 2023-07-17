<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Services;

use AcyChecker\Classes\ConfigurationClass;
use AcyCheckerCmsServices\Database;
use AcyCheckerCmsServices\Message;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\File;

class UpdateService
{
    private $cms = 'joomla';
    private $version = '1.2.6';
    private $update = false;
    private $fromVersion = '';

    public function installTables()
    {
        $queries = file_get_contents(ACYC_BACK.'tables.sql');
        $tables = explode('CREATE TABLE IF NOT EXISTS', $queries);

        foreach ($tables as $oneTable) {
            $oneTable = trim($oneTable);
            if (empty($oneTable)) {
                continue;
            }
            Database::query('CREATE TABLE IF NOT EXISTS'.$oneTable);
        }
    }

    public function addPref()
    {
        try {
            $configuration = Database::loadObjectList('SELECT * FROM `#__acyc_configuration`', 'name');
        } catch (\Exception $e) {
            $configuration = null;
        }
        if ($configuration === null) {
            Message::display(isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200).'...', 'error');

            return false;
        }


        $allPref = [];
        // if there are rows in the config database but no version, insert "1.0.0" instead of $this->version to make sure we run the update scripts for early users
        $allPref['version'] = empty($configuration) || !empty($configuration['version']) ? $this->version : '1.0.0';
        $allPref['installcomplete'] = '0';

        $query = 'INSERT IGNORE INTO `#__acyc_configuration` (`name`,`value`) VALUES ';
        foreach ($allPref as $namekey => $value) {
            $query .= '('.Database::escapeDB($namekey).','.Database::escapeDB($value).'),';
        }
        $query = rtrim($query, ',');

        try {
            $res = Database::query($query);
        } catch (\Exception $e) {
            $res = null;
        }
        if ($res === null) {
            Message::display(isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200).'...', 'error');

            return false;
        }

        return true;
    }

    public function updatePref()
    {
        try {
            $version = Database::loadResult('SELECT `value` FROM `#__acyc_configuration` WHERE `name` = "version"');
        } catch (\Exception $e) {
            $version = null;
        }

        if ($version === null) {
            Message::display(isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200).'...', 'error');

            return false;
        }

        if ($version == $this->version) {
            return true;
        }

        $this->update = true;
        $this->fromVersion = $version;

        Database::query('REPLACE INTO `#__acyc_configuration` (`name`,`value`) VALUES ("version",'.Database::escapeDB($this->version).'),("installcomplete","0")');

        return true;
    }

    public function updateSQL()
    {
        if (!$this->update) return;

        $config = new ConfigurationClass();
        if (version_compare($this->fromVersion, '1.1.0', '<')) {
            $this->updateQuery('ALTER TABLE #__acyc_test ADD `batch_id` INT NULL');

            $urlsBatch = $config->get('urls_results_batch', '[]');
            $urlsBatch = json_decode($urlsBatch, true);
            foreach ($urlsBatch as $batchId => $oneBatch) {
                $urlsBatch[$batchId] = [
                    'url' => $oneBatch,
                    'last_check' => time(),
                    'attempts' => 0,
                ];
            }
            $config->save(['urls_results_batch' => json_encode($urlsBatch)]);

            $this->updateQuery('ALTER TABLE #__acyc_test CHANGE `final_status` `test_result` VARCHAR(50) NOT NULL');
            $this->updateQuery('ALTER TABLE #__acyc_test CHANGE `status` `current_step` INT NULL');
            $this->updateQuery(
                'CREATE TABLE IF NOT EXISTS `#__acyc_block_history` (
                            `email`         VARCHAR(255) NOT NULL,
                            `block_date`    DATETIME     NOT NULL,
                            `block_reason`  VARCHAR(50)  NOT NULL,
                            `block_action`  VARCHAR(50)  NOT NULL,
                            PRIMARY KEY (`email`)
                        )   
                        ENGINE = InnoDB'
            );
        }

        if (version_compare($this->fromVersion, '1.1.7', '<')) {
            $this->updateQuery(
                'CREATE TABLE IF NOT EXISTS `#__acyc_delete_history` (
                        `email`         VARCHAR(255) NOT NULL,
                        `delete_date`    DATETIME     NOT NULL,
                        `delete_reason`  VARCHAR(50)  NOT NULL,
	                    `delete_action`  VARCHAR(50)  NOT NULL,
                        PRIMARY KEY (`email`)
                    )
                    ENGINE = InnoDB'
            );

            $this->updateQuery('INSERT INTO `#__acyc_configuration` (`name`,`value`) VALUES ("action_selected","block_users")');
            $this->updateQuery('DROP TABLE #__acyc_global_stat');

            // Store the domain_exists information in a column for faster filter
            $this->updateQuery('ALTER TABLE #__acyc_test ADD `domain_exists` INT NULL');
            $this->updateQuery(
                'UPDATE #__acyc_test 
                        SET `domain_exists` = 0 
                        WHERE `raw_result` LIKE '.Database::escapeDB('%"d_exists":"0"%').' 
                            OR `raw_result` LIKE '.Database::escapeDB('%"d_exists":false%')
            );
            $this->updateQuery(
                'UPDATE #__acyc_test 
                        SET `domain_exists` = 1 
                        WHERE `raw_result` LIKE '.Database::escapeDB('%"d_exists":1"%').' 
                            OR `raw_result` LIKE '.Database::escapeDB('%"d_exists":true%')
            );
        }
    }

    public function updateQuery($query)
    {
        try {
            $res = Database::query($query);
        } catch (\Exception $e) {
            $res = null;
        }
        if ($res === null) {
            Message::enqueueMessage(isset($e) ? $e->getMessage() : substr(strip_tags(Database::getDBError()), 0, 200).'...', 'error');
        }
    }

    public function installBackLanguage()
    {
        $menuStrings = [
            'ACYC_DASHBOARD',
            'ACYC_CLEAN_DATABASE',
            'ACYC_BLOCK_ON_REGISTRATION',
            'ACYC_TESTS',
            'ACYC_CONFIGURATION',
        ];

        $siteLanguages = array_keys(Language::getLanguages());

        foreach ($siteLanguages as $code) {

            $path = Language::getLanguagePath(ACYC_ROOT, $code).DS.$code.'.com_acychecker.ini';
            if (!file_exists($path)) continue;

            $content = file_get_contents($path);
            if (empty($content)) continue;

            // The first key is to translate "Acyc" into "AcyChecker" in the Joomla Extension manager
            // The second key is to translate "com_acychecker" into "AcyChecker" in the Joomla global configuration page
            // DON'T CHANGE THE KEYS!
            $menuFileContent = 'ACYC="AcyChecker"'."\r\n";
            $menuFileContent .= 'COM_ACYCHECKER="AcyChecker"'."\r\n";
            $menuFileContent .= 'COM_ACYCHECKER_CONFIGURATION="AcyChecker"'."\r\n";

            foreach ($menuStrings as $oneString) {
                preg_match('#[^_]'.$oneString.'="(.*)"#i', $content, $matches);
                if (empty($matches[1])) continue;

                $menuFileContent .= $oneString.'="'.$matches[1].'"'."\r\n";
            }

            $menuPath = ACYC_ROOT.'administrator'.DS.'language'.DS.$code.DS.$code.'.com_acychecker.sys.ini';

            if (!File::writeFile($menuPath, $menuFileContent)) {
                Message::enqueueMessage(Language::translationSprintf('ACYC_FAIL_SAVE_FILE', $menuPath), 'error');
            }
        }
    }
}
