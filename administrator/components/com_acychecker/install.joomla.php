<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

include_once 'admin/vendor/autoload.php';
include_once 'admin/defines.php';

use AcyChecker\Classes\ConfigurationClass;
use AcyChecker\Services\UpdateService;

class com_acycheckerInstallerScript
{
    public function install($parent)
    {
        installAcyc();
    }

    public function update($parent)
    {
        installAcyc();
    }

    public function uninstall($parent)
    {
        uninstallAcyc();
    }

    public function preflight($type, $parent)
    {
        if ($type === 'update') {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)->select('*')->from('#__extensions');
            $query->where(
                'type = "component" AND element = "com_acychecker"'
            );
            $db->setQuery($query);

            try {
                $extension = $db->loadObject();
            } catch (Exception $e) {
                echo JText::sprintf('JLIB_DATABASE_ERROR_FUNCTION_FAILED', $e->getCode(), $e->getMessage()).'<br />';

                return false;
            }

            $installer = new JInstaller();
            $installer->refreshManifestCache($extension->extension_id);
        }

        return true;
    }

    public function postflight($type, $parent)
    {
        return true;
    }
}

function installAcyc()
{
    $updateService = new UpdateService();
    $updateService->installTables();
    $updateService->addPref();
    $updateService->updatePref();
    $updateService->updateSQL();
    $updateService->installBackLanguage();

    $newConfig = new stdClass();
    $newConfig->installcomplete = 1;

    $configurationClass = new ConfigurationClass();
    $configurationClass->save($newConfig);
}

function uninstallAcyc()
{
    ?>
	AcyChecker successfully uninstalled.<br />
	If you want to completely uninstall it and remove its data, please uninstall its plugin from the Joomla Extensions Manager then run the following query on your database manager:
	<br /><br />
    <?php

    $tables = [
        'configuration',
        'test',
        'block_history',
        'delete_history',
    ];

    $db = JFactory::getDBO();
    $prefix = $db->getPrefix().'acyc_';
    echo 'DROP TABLE '.$prefix.implode(', '.$prefix, $tables).';';

    ?>
	<br /><br />
	If you don't do this, you will be able to install AcyChecker again without losing your data.<br /><br />
    <?php
}
