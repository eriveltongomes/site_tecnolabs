<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php


namespace AcyChecker\Services;


use AcyCheckerCmsServices\Language;

class StatusService
{
    public static function initStatusListing($status, $current)
    {
        $statusHtml = '';

        if (empty($status)) return $statusHtml;

        $statusHtml = '<div class="cell grid-x acyc__listing__status__container"><input name="status" type="hidden" id="acyc__listing__status" value="'.$current.'">';

        $i = 0;
        foreach ($status as $value => $oneStatus) {
            $class = $value == $current ? 'acyc__listing__status__selected' : '';
            if ($i != 0) $statusHtml .= '<span class="cell shrink acyc__listing__status__separator">|</span>';
            $statusHtml .= '<a href="#" class="acyc__listing__status__one cell shrink '.$class.'" data-acyc-status="'.$value.'">'.$oneStatus['text'].' ('.$oneStatus['number'].')</a>';
            $i++;
        }

        $statusHtml .= '</div>';

        return $statusHtml;
    }

    public static function yesNo($value)
    {
        if (intval($value) === 0) {
            return Language::translation('ACYC_NO');
        } else {
            return Language::translation('ACYC_YES');
        }
    }
}
