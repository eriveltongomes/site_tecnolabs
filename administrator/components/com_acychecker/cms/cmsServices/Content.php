<?php
/**
 * @package   acychecker
 * @copyright Copyright (c)2021-2022 Acyba SAS
 * @license   GNU General Public License version 3, or later
 */
?><?php

namespace AcyCheckerCmsServices;


class Content
{
    public static function cmsModal($isIframe, $content, $buttonText, $isButton, $modalTitle = '', $identifier = null, $width = '800', $height = '400')
    {
        if (empty($identifier)) {
            $identifier = 'identifier_'.rand(1000, 9000);
        }

        $params = [
            'title' => $modalTitle,
            'url' => $content,
            'height' => $height.'px',
            'width' => $width.'px',
            'bodyHeight' => '70',
            'modalWidth' => '80',
        ];

        \JHtml::_('jquery.framework');
        if (ACYC_J40) {
            $wa = \JFactory::getApplication()->getDocument()->getWebAssetManager();
            $wa->useScript('field.modal-fields');
            Router::addStyle(
                true,
                '
            #'.$identifier.' {
                height: auto;
                border: none;
            }
            
            #'.$identifier.' .modal-dialog {
                margin: 0;
            }'
            );
        } else {
            \JHtml::_('script', 'system/modal-fields.js', ['version' => 'auto', 'relative' => true]);
            Router::addStyle(true, '#'.$identifier.' .modal-body { overflow: auto; }');
            $params['footer'] = '<a role="button" class="btn" data-dismiss="modal" aria-hidden="true">'.Language::translation('JLIB_HTML_BEHAVIOR_CLOSE').'</a>';
        }


        $html = '<a 
                class="'.($isButton ? 'btn ' : '').'hasTooltip" 
                data-toggle="modal" 
                role="button" 
                href="#'.$identifier.'" 
                id="button_'.$identifier.'"
                data-bs-toggle="modal"
                data-bs-target="#'.$identifier.'">';
        $html .= Language::translation($buttonText).'</a>';
        $html .= \JHtml::_('bootstrap.renderModal', $identifier, $params);

        return $html;
    }

    public static function getAlias($name)
    {
        return \JFilterOutput::stringURLSafe($name);
    }
}
