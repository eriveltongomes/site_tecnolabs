<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2023 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Controller;

// Protect from unauthorized access
defined('_JEXEC') || die();

use Akeeba\Backup\Admin\Controller\Mixin\CustomACL;
use Akeeba\Engine\Util\RandomValue;
use FOF40\Controller\Controller;
use Joomla\CMS\Uri\Uri;

/**
 * Scheduling information page controller
 */
class Schedule extends Controller
{
	use CustomACL;

	public function enableFrontend(bool $cachable = false, array $urlparams = [])
	{
		// CSRF prevention
		$this->csrfProtection();

		$params = $this->container->params;

		$params->set('legacyapi_enabled', 1);

		$secretWord = $params->get('frontend_secret_word', null);

		if (empty($secretWord))
		{
			$random    = new RandomValue();
			$newSecret = $random->generateString(32);
			$params->set('frontend_secret_word', $newSecret);
		}

		$params->save();

		$url = Uri::base() . 'index.php?option=com_akeeba&view=Schedule';

		$this->setRedirect($url);
	}

	public function enableJsonApi(bool $cachable = false, array $urlparams = [])
	{
		// CSRF prevention
		$this->csrfProtection();

		$params = $this->container->params;

		$params->set('jsonapi_enabled', 1);

		$secretWord = $params->get('frontend_secret_word', null);

		if (empty($secretWord))
		{
			$random    = new RandomValue();
			$newSecret = $random->generateString(32);
			$params->set('frontend_secret_word', $newSecret);
		}

		$params->save();

		$url = Uri::base() . 'index.php?option=com_akeeba&view=Schedule';

		$this->setRedirect($url);
	}

	public function resetSecretWord(bool $cachable = false, array $urlparams = []): void
	{
		// CSRF prevention
		$this->csrfProtection();

		$newSecret = $this->container->platform->getSessionVar('newSecretWord', null, 'akeeba.cpanel');

		if (empty($newSecret))
		{
			$random    = new RandomValue();
			$newSecret = $random->generateString(32);
		}

		$params = $this->container->params;

		$params->set('frontend_secret_word', $newSecret);

		$params->save();

		$this->container->platform->setSessionVar('newSecretWord', null, 'akeeba.cpanel');

		$url = Uri::base() . 'index.php?option=com_akeeba&view=Schedule';

		$this->setRedirect($url);
	}
}
