<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2020 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

class RsfirewallControllerLists extends JControllerAdmin
{
	function __construct($config = array()) {
		parent::__construct($config);
		
		$user = JFactory::getUser();
		if (!$user->authorise('lists.manage', 'com_rsfirewall')) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('JERROR_ALERTNOAUTHOR'), 'error');
			$app->redirect(JRoute::_('index.php?option=com_rsfirewall', false));
		}
		
		$this->registerTask('trash', 'delete');
	}
	
	public function getModel($name = 'List', $prefix = 'RsfirewallModel', $config = array('ignore_request' => true)) {
		return parent::getModel($name, $prefix, $config);
	}

	public function download()
	{
		$this->checkToken();

		$model 		= $this->getModel('Lists');
		$app		= JFactory::getApplication();
		$document 	= JFactory::getDocument();

		try
		{
			if ($document instanceof JDocument)
			{
				$document->setMimeEncoding('application/json');
			}

			@ob_end_clean();

			header('Expires: 0');
			header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
			header('Cache-Control: public');
			header('Content-Type: application/json; charset=utf-8');
			header('Content-Description: File Transfer');
			header('Content-Disposition: attachment; filename="list_'.JUri::getInstance()->getHost().'.json"');

			$model->toJson();

			$app->close();
		}
		catch (Exception $e)
		{
			$app->enqueueMessage($e->getMessage(), 'error');
			$this->setRedirect('index.php?option=com_rsfirewall&view=lists');
		}
	}
}