<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

use \AcyChecker\Classes\TestClass;
use AcyChecker\Services\DateService;
use AcyChecker\Services\StatusService;
use AcyChecker\Services\TooltipService;
use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;

?>
<div class="cell grid-x acyc__test__listing__listing">
	<div class="cell grid-x acyc__test__listing__listing__header">
		<div class="cell small-1 medium-shrink">
			<input id="checkbox_all" type="checkbox" name="checkbox_all">
		</div>
		<div class="cell small-3 acyc__listing__header__title">
            <?php echo Language::translation('ACYC_EMAIL'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center">
            <?php echo Language::translation('ACYC_USER_STATUS'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center show-for-large">
            <?php echo Language::translation('ACYC_DATE'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center show-for-large">
            <?php echo Language::translation('ACYC_TRUSTWORTHINESS'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center show-for-medium">
            <?php echo Language::translation('ACYC_DISPOSABLE'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center show-for-medium">
            <?php echo Language::translation('ACYC_FREE'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center show-for-medium">
            <?php echo Language::translation('ACYC_ACCEPT_ALL'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center show-for-medium">
            <?php echo Language::translation('ACYC_ROLE_EMAIL'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center">
            <?php echo Language::translation('ACYC_CURRENT_STEP'); ?>
		</div>
		<div class="cell auto acyc__listing__header__title text-center">
            <?php echo Language::translation('ACYC_ACTIONS'); ?>
		</div>
	</div>
	<div class="cell grid-x acyc__test__listing__listing__body">
        <?php
        $rowId = 0;
        foreach ($this->data['elements'] as $test) {
            $rowId++;
            ?>
			<div class="cell grid-x acyc__listing__body__row">
				<div class="cell small-1 medium-shrink">
					<input id="checkbox_<?php echo Security::escape($rowId); ?>"
						   type="checkbox"
						   name="elements_checked[]"
						   value="<?php echo Security::escape($test->email); ?>"
						   data-acyc-finished="<?php echo Security::escape(intval($test->current_step) === TestClass::STEP['finished'] ? 'true' : 'false'); ?>">
				</div>
				<div class="cell small-3 acyc__listing__body__cell">
                    <?php echo $test->email; ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center">
                    <?php
                    if (empty($this->data['test_result_texts'][$test->test_result])) {
                        echo '-';
                    } else {
                        if (empty($test->block_reason)) {
                            echo $this->data['tooltipService']::tooltip('<i class="acycicon-check acyc_green"></i>', Language::translation('ACYC_ADDRESS_OK'));
                        } else {
                            if ($test->block_reason === 'manual') {
                                $tooltipText = Language::translation('ACYC_ADDRESS_NOT_OK_MANUAL');
                            } else {
                                $tooltipText = Language::translationSprintf('ACYC_ADDRESS_NOT_OK', $this->data['block_reasons'][$test->block_reason]);
                            }
                            echo $this->data['tooltipService']::tooltip(
                                '<i class="acycicon-times acyc_red"></i>',
                                $tooltipText,
                                '',
                                $this->data['block_reasons'][$test->block_reason]
                            );
                        }
                    }
                    ?>
				</div>
				<div class="cell auto acyc__listing__body__cell show-for-large">
                    <?php echo DateService::date($test->date, Language::translation('ACYC_DATE_FORMAT_LC2')); ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center show-for-large">
                    <?php echo empty($this->data['test_result_texts'][$test->test_result]) ? '-' : $this->data['test_result_texts'][$test->test_result]; ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center show-for-medium">
                    <?php echo empty($this->data['test_result_texts'][$test->test_result]) ? '-' : StatusService::yesNo($test->disposable); ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center show-for-medium">
                    <?php echo empty($this->data['test_result_texts'][$test->test_result]) ? '-' : StatusService::yesNo($test->free); ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center show-for-medium">
                    <?php echo empty($this->data['test_result_texts'][$test->test_result]) ? '-' : StatusService::yesNo($test->accept_all); ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center show-for-medium">
                    <?php echo empty($this->data['test_result_texts'][$test->test_result]) ? '-' : StatusService::yesNo($test->role_email); ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center">
                    <?php echo $this->data['statuses'][$test->current_step]; ?>
				</div>
				<div class="cell auto acyc__listing__body__cell text-center">
                    <?php
                    if ($test->current_step != TestClass::STEP['finished']) {
                        echo '-';
                    } elseif ($test->removed) {
                        echo TooltipService::tooltip(
                            '<i class="acycicon-user-times"></i>',
                            Language::translation('ACYC_USER_REMOVED')
                        );
                    } else {
                        echo TooltipService::tooltip(
                            '<i class="fastAction acycicon-check-circle"
							   data-acyc-action="unblockUsers"
							   data-acyc-elementid="'.Security::escape($rowId).'"></i>',
                            Language::translation('ACYC_UNBLOCK_USER')
                        );
                        echo TooltipService::tooltip(
                            '<i class="fastAction acycicon-ban"
							   data-acyc-action="blockUsers"
							   data-acyc-elementid="'.Security::escape($rowId).'"></i>',
                            Language::translation('ACYC_BLOCK_USER')
                        );
                        echo TooltipService::tooltip(
                            '<i class="fastAction acycicon-trash-o"
							   data-acyc-action="deleteUsers"
							   data-acyc-elementid="'.Security::escape($rowId).'"></i>',
                            Language::translation('ACYC_DELETE_USER')
                        );
                    }
                    ?>
				</div>
			</div>
        <?php } ?>
	</div>
	<div class="cell grid-x margin-top-2">
        <?php echo $this->data['pagination']->display(); ?>
	</div>
</div>
