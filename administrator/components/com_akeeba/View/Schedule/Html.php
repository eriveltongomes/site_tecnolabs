<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\View\Schedule;

// Protect from unauthorized access
defined('_JEXEC') || die();

use Akeeba\Backup\Admin\Model\Schedule;
use Akeeba\Backup\Admin\View\ViewTraits\ProfileIdAndName;
use FOF40\View\DataView\Html as BaseView;
use Joomla\CMS\Factory;

/**
 * View controller for the Scheduling Information page
 */
class Html extends BaseView
{
	use ProfileIdAndName;

	/**
	 * CRON information
	 *
	 * @var  object
	 */
	public $croninfo = null;

	/**
	 * Check for failed backups information
	 *
	 * @var  object
	 */
	public $checkinfo = null;

	/**
	 * URL to automatically enable the legacy frontend API (and set a Secret Key, if necessary)
	 *
	 * @var    string|null
	 * @since  8.2.8
	 */
	public $enableLegacyFrontendURL;

	/**
	 * URL to automatically enable the JSON API (and set a Secret Key, if necessary)
	 *
	 * @var    string|null
	 * @since  8.2.8
	 */
	public $enableJsonApiURL;

	/**
	 * URL to reset the secret word to something that actually works
	 *
	 * @var    string|null
	 * @since  8.2.8
	 */
	public $resetSecretWordURL;

	protected function onBeforeMain()
	{
		$this->getProfileIdAndName();

		// Get the CRON paths
		/** @var Schedule $model */
		$model           = $this->getModel();
		$this->croninfo  = $model->getPaths();
		$this->checkinfo = $model->getCheckPaths();

		$this->enableLegacyFrontendURL = sprintf(
			'index.php?option=com_akeeba&view=schedule&&task=enableFrontend&%s=1',
			Factory::getApplication()->getSession()->getToken()
		);

		$this->enableJsonApiURL = sprintf(
			'index.php?option=com_akeeba&view=schedule&&task=enableJsonApi&%s=1',
			Factory::getApplication()->getSession()->getToken()
		);

		$this->resetSecretWordURL = sprintf(
			'index.php?option=com_akeeba&view=schedule&&task=resetSecretWord&%s=1',
			Factory::getApplication()->getSession()->getToken()
		);
	}
}
