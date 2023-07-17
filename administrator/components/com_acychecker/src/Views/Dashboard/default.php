<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use AcyChecker\Services\ViewService;

?>
<div id="acyc__dashboard" class="cell grid-x grid-margin-x">
    <?php include ViewService::getView('Dashboard', 'default_current'); ?>
    <?php include ViewService::getView('Dashboard', 'default_charts'); ?>
</div>
