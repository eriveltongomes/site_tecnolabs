<?php
/**
 * @package    RSFirewall!
 * @copyright  (c) 2009 - 2020 RSJoomla!
 * @link       https://www.rsjoomla.com
 * @license    GNU General Public License http://www.gnu.org/licenses/gpl-3.0.en.html
 */

defined('_JEXEC') or die('Restricted access');

JText::script('COM_RSFIREWALL_LEVEL_LOW');
JText::script('COM_RSFIREWALL_LEVEL_MEDIUM');
JText::script('COM_RSFIREWALL_LEVEL_HIGH');
JText::script('COM_RSFIREWALL_LEVEL_CRITICAL');

JHtml::_('script', 'com_rsfirewall/chart.min.js', array('relative' => true, 'version' => 'auto'));
?>
<h2><?php echo JText::_('COM_RSFIREWALL_ATTACKS_BLOCKED_PAST_MONTH'); ?></h2>
<div>
	<canvas id="com-rsfirewall-logs-chart"></canvas>
</div>

<script type="text/javascript">
	document.addEventListener('DOMContentLoaded', function() {
		var data = {
			labels: <?php echo json_encode(array_keys($this->lastMonthLogs)); ?>,
			datasets: [
				{
					label: Joomla.JText._('COM_RSFIREWALL_LEVEL_LOW'),
					backgroundColor: 'rgb(146,255,99)',
					borderColor: 'rgb(146,255,99)',
					data: []
				},
				{
					label: Joomla.JText._('COM_RSFIREWALL_LEVEL_MEDIUM'),
					backgroundColor: 'rgb(255,161,99)',
					borderColor: 'rgb(255,161,99)',
					data: []
				},
				{
					label: Joomla.JText._('COM_RSFIREWALL_LEVEL_HIGH'),
					backgroundColor: 'rgb(255, 99, 132)',
					borderColor: 'rgb(255, 99, 132)',
					data: []
				},
				{
					label: Joomla.JText._('COM_RSFIREWALL_LEVEL_CRITICAL'),
					backgroundColor: 'rgb(0,0,0)',
					borderColor: 'rgb(0,0,0)',
					data: []
				}
			]
		};

		<?php
		foreach ($this->lastMonthLogs as $date => $item)
		{
		?>
		data.datasets[0].data.push(<?php echo $item['low']; ?>);
		data.datasets[1].data.push(<?php echo $item['medium']; ?>);
		data.datasets[2].data.push(<?php echo $item['high']; ?>);
		data.datasets[3].data.push(<?php echo $item['critical']; ?>);
		<?php
		}
		?>

		new Chart(
			document.getElementById('com-rsfirewall-logs-chart'),
			{
				type: 'line',
				data: data,
				options: {
					scales: {
						y: {
							beginAtZero: true
						}
					}
				}
			}
		);
	});
</script>