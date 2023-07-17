<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyChecker\Classes;

use AcyChecker\Libraries\AcycClass;
use AcyCheckerCmsServices\Database;

class Acymailing5ListClass extends AcycClass
{
    public function getAllListsForSelect()
    {
        $lists = Database::loadObjectList('SELECT name, listid FROM #__acymailing_list WHERE type = "list"');

        if (empty($lists)) return [];

        $return = [];

        foreach ($lists as $list) {
            $return[$list->listid] = $list->name;
        }

        return $return;
    }
}
