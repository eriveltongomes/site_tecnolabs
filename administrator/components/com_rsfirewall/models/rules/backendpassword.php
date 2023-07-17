<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2020 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

use Joomla\CMS\Form\Form;
use Joomla\Registry\Registry;

defined('_JEXEC') or die('Restricted access');

class JFormRuleBackendpassword extends JFormRule
{
	public function test(\SimpleXMLElement $element, $value, $group = null, Registry $input = null, Form $form = null)
	{
		// If the field is empty and not required, the field is valid.
		$required = ((string) $element['required'] === 'true' || (string) $element['required'] === 'required');

		$minimumLength = 6;

		if (!$required && empty($value))
		{
			return true;
		}

		$valueLength = strlen($value);

		// We don't allow white space inside passwords
		$valueTrim = trim($value);

		// Set a variable to check if any errors are made in password
		$validPassword = true;

		if (strlen($valueTrim) !== $valueLength)
		{
			\JFactory::getApplication()->enqueueMessage(
				\JText::_('COM_RSFIREWALL_MSG_SPACES_IN_PASSWORD'),
				'warning'
			);

			$validPassword = false;
		}

		if (strlen((string) $value) < $minimumLength)
		{
			\JFactory::getApplication()->enqueueMessage(
				\JText::plural('COM_RSFIREWALL_MSG_PASSWORD_TOO_SHORT_N', $minimumLength),
				'warning'
			);

			$validPassword = false;
		}

		return $validPassword;
	}
}