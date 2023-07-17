<?php
/**
 * @package       RSForm! Pro
 * @copyright (C) 2019 www.rsjoomla.com
 * @license       GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die;

/**
 * Class plgSystemRsfplegacylayouts
 *
 * @since 1.0.0
 */
class plgSystemRsfplegacylayouts extends JPlugin
{
	/**
	 * @var bool
	 * @since 1.0.0
	 */
	protected $autoloadLanguage = true;

	protected $legacyLayouts = array(
		'classicLayouts' => array('inline', '2lines', '2colsinline', '2cols2lines'),
		'xhtmlLayouts' => array('inline-xhtml', '2lines-xhtml'),
	);

	/**
	 * @param $layouts array
	 * @param $formId int
	 * @since 1.0.0
	 */
	public function onRsformBackendLayoutsDefine(&$layouts, $formId)
	{
		$layouts['classicLayouts'] = $this->legacyLayouts['classicLayouts'];
		$layouts['xhtmlLayouts'] = $this->legacyLayouts['xhtmlLayouts'];
	}

	/**
	 * @param $args array
	 * @since 1.0.0
	 */
	public function onRsformFrontendBeforeFormDisplay($args)
	{
		if (in_array($args['formLayoutName'], array_merge($this->legacyLayouts['classicLayouts'], $this->legacyLayouts['xhtmlLayouts'])))
		{
			if ($form = RSFormProHelper::getForm($args['formId']))
			{
				if (!$form->LoadFormLayoutFramework)
				{
					return;
				}
			}

			RSFormProAssets::addStyleSheet(JHtml::_('stylesheet', 'com_rsform/frameworks/legacy/legacy.css', array('pathOnly' => true, 'relative' => true)));
		}
	}

	/**
	 * @param $viewObject RsformViewForms
	 */
	public function onRsformBackendEditFormComponents($viewObject)
	{
		JHtml::_('stylesheet', 'com_rsform/admin/legacy.css', array('relative' => true, 'version' => 'auto'));
		JHtml::_('script', 'com_rsform/admin/legacy.js', array('relative' => true, 'version' => 'auto'));

		echo $viewObject->loadTemplate('legacy');
	}

	public function onRsformFormRestore($form, $xml, $fields)
	{
		if (in_array($form->FormLayoutName, array_merge($this->legacyLayouts['classicLayouts'], $this->legacyLayouts['xhtmlLayouts'])))
		{
			$db = JFactory::getDbo();
			$query = $db->getQuery(true);
			$query->update('#__rsform_forms')
				->set($db->qn('GridLayout') .'='. $db->q(''))
				->where($db->qn('FormId') .'='. $db->q($form->FormId));
			$db->setQuery($query)->execute();
		}
	}

	public function onRsformBackendFormCopy($args)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select($db->qn('FormLayoutName'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('FormId') . ' = ' . $db->q($args['newFormId']));
		if ($layout = $db->setQuery($query)->loadResult())
		{
			if (in_array($layout, array_merge($this->legacyLayouts['classicLayouts'], $this->legacyLayouts['xhtmlLayouts'])))
			{
				$query->clear()
					->update('#__rsform_forms')
					->set($db->qn('GridLayout') .'='. $db->q(''))
					->where($db->qn('FormId') .'='. $db->q($args['newFormId']));
				$db->setQuery($query)->execute();
			}
		}
	}
}