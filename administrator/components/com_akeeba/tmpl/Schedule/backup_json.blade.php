<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

/** @var \Akeeba\Backup\Admin\View\Schedule\Html $this */

// Protect from unauthorized access
defined('_JEXEC') || die();

?>
<div class="akeeba-panel--information">
    <header class="akeeba-block-header">
        <h3>@lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPIBACKUP')</h3>
    </header>

    <div class="akeeba-block--info">
        <p>
            @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPIBACKUP_INFO')
        </p>
    </div>

    @if(!$this->croninfo->info->jsonapi)
        <div class="akeeba-block--failure">
            <p>
                @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_DISABLED')
            </p>
            <p>
                <a href="{{{ $this->enableJsonApiURL }}}" class="akeeba-btn--teal">
                    @lang('COM_AKEEBA_SCHEDULE_BTN_ENABLE_JSONAPI')
                </a>
            </p>
        </div>
    @elseif(!trim($this->croninfo->info->secret))
        <div class="akeeba-block--failure">
            <p>
                @lang('COM_AKEEBA_SCHEDULE_LBL_FRONTEND_SECRET')
            </p>
            <p>
                <a href="{{{ $this->resetSecretWordURL }}}" class="akeeba-btn--teal">
                    @lang('COM_AKEEBA_SCHEDULE_BTN_RESET_SECRETWORD')
                </a>
            </p>
        </div>
    @else
        <h4>@lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_ARCCLI')</h4>
        <p>
            @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_ARCCLI_INTRO')
        </p>
        <p>
            @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_ARCCLI_DOCKER')
        </p>
        <p>
            <code>{{{sprintf('docker run --rm ghcr.io/akeeba/remotecli backup --profile=%d --host="%s/%s" --secret="%s"',
					\Akeeba\Engine\Platform::getInstance()->get_active_profile(),
					$this->escape($this->croninfo->info->root_url ),
					$this->escape($this->croninfo->json->path ),
					$this->escape($this->croninfo->info->secret )
			)}}}</code>
        </p>
        <p>
            @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_ARCCLI_PHAR')
        </p>
        <p>
            <code>{{{sprintf(
					'php remote.phar backup --profile=%d --host="%s/%s" --secret="%s"',
					\Akeeba\Engine\Platform::getInstance()->get_active_profile(),
					$this->escape($this->croninfo->info->root_url ),
					$this->escape($this->croninfo->json->path ),
					$this->escape($this->croninfo->info->secret )
				)}}}</code>
        </p>

        <h4>
            @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_OTHER')
        </h4>

        <p>
            @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_INTRO')
        </p>

        <table class="akeeba-table--striped">
            <tbody>
            <tr>
                <td>@lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_ENDPOINT')</td>
                <td>{{{ $this->croninfo->info->root_url }}}/{{{ $this->croninfo->json->path }}}</td>
            </tr>
            <tr>
                <td>@lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_SECRET')</td>
                <td>{{{ $this->croninfo->info->secret }}}</td>
            </tr>
            </tbody>
        </table>

        <p></p>
        <p>
            <small>
                @lang('COM_AKEEBA_SCHEDULE_LBL_JSONAPI_DISCLAIMER')
            </small>
        </p>
    @endif
</div>
