<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php


namespace AcyChecker\Services;


use AcyCheckerCmsServices\Language;
use AcyCheckerCmsServices\Security;

class TooltipService
{
    public static function tooltip($hoveredText, $textShownInTooltip, $classContainer = '', $titleShownInTooltip = '', $link = '', $classText = '')
    {
        if (!empty($link)) {
            $hoveredText = '<a href="'.$link.'" title="'.Security::escape($titleShownInTooltip).'" target="_blank">'.$hoveredText.'</a>';
        }

        if (!empty($titleShownInTooltip)) {
            $titleShownInTooltip = '<span class="acyc__tooltip__title">'.$titleShownInTooltip.'</span>';
        }

        return '<span class="acyc__tooltip '.$classContainer.'"><span class="acyc__tooltip__text '.$classText.'">'.$titleShownInTooltip.$textShownInTooltip.'</span>'.$hoveredText.'</span>';
    }

    public static function info($tooltipText, $class = '', $containerClass = '', $classText = '', $warningInfo = false)
    {
        $classWarning = $warningInfo ? 'acyc__tooltip__info__warning' : '';

        return self::tooltip(
            '<span class="acyc__tooltip__info__container '.$class.'"><i class="acyc__tooltip__info__icon acycicon-question-circle-o '.$classWarning.'"></i></span>',
            $tooltipText,
            'acyc__tooltip__info '.$containerClass,
            '',
            '',
            $classText
        );
    }
}
