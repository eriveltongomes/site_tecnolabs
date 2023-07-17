<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;


class Date
{
    public static function getTimeOffsetCMS()
    {
        static $timeoffset = null;
        if ($timeoffset === null) {

            $dateC = \JFactory::getDate(
                'now',
                Database::getCMSConfig('offset')
            );
            $timeoffset = $dateC->getOffsetFromGMT(true) * 3600;
        }

        return $timeoffset;
    }

    public static function dateTimeCMS($time)
    {
        return \JHtml::_('date', $time, 'Y-m-d H:i:s', null);
    }
}
