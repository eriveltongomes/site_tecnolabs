<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 03, 2012
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
?>

<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script type="text/javascript">
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawChart);

      function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', '<?php echo JText::_('Date'); ?>');
        data.addColumn('number', '<?php echo JText::_('Minutes'); ?>');
        data.addRows([
            <?php echo $this->result['line_chart_json_array']; ?>
        ]);        

        var options = {
          colors:['#1EADD8'],
          curveType: 'function',
          legend: { position: 'bottom' },
          pointSize: 6,
          // This line will make you select an entire row of data at a time
          focusTarget: 'category',
          chartArea: {width:'90%',top:50}
        };

        var chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
    }

    function resetFrom(){
        document.getElementById('ticket_date_start').value = '';
        document.getElementById('ticket_date_end').value = '';
        document.getElementById('jssupportticketform').submit();
    }
</script>
<?php 
    $curdate = date('Y-m-d');
    $enddate = date('Y-m-d', strtotime("now -1 month"));
    $date_start = !empty($this->result['filter']['date_start']) ? $this->result['filter']['date_start'] : $curdate;
    $date_end = !empty($this->result['filter']['date_end']) ? $this->result['filter']['date_end'] : $enddate;
    $dash = '-';
$dateformat = $this->config['date_format'];
$firstdash = strpos($dateformat, $dash, 0);
$firstvalue = substr($dateformat, 0, $firstdash);
$firstdash = $firstdash + 1;
$seconddash = strpos($dateformat, $dash, $firstdash);
$secondvalue = substr($dateformat, $firstdash, $seconddash - $firstdash);
$seconddash = $seconddash + 1;
$thirdvalue = substr($dateformat, $seconddash, strlen($dateformat) - $seconddash);
$js_dateformat = '%' . $firstvalue . $dash . '%' . $secondvalue . $dash . '%' . $thirdvalue;

?>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li>
                            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard">
                                <?php echo JText::_('Dashboard'); ?>
                            </a>
                        </li>
                        <li>
                            <a href="index.php?option=com_jssupportticket&c=reports&layout=staffreport" title="Dashboard">
                                <?php echo JText::_('Staff Report'); ?>
                            </a>
                        </li>
                        <li>
                            <?php echo JText::_('Staff Time Report'); ?>
                        </li>
                    </ul>
                </div>
            </div>
            <div id="jsstadmin-wrapper-top-right">
                <div id="jsstadmin-config-btn">
                    <a title="Configuration" href="index.php?option=com_jssupportticket&c=config&layout=config">
                        <img alt="Configuration" src="components/com_jssupportticket/include/images/config.png">
                    </a>
                </div>
                <div id="jsstadmin-vers-txt">
                    <?php echo JText::_('Version').JText::_(' : '); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Staff Time Reports'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=reports&task=reports'; ?>
        </div>
            <form class="jsstadmin-data-wrp" name="jssupportticketform" id="jssupportticketform" method="post" action="index.php">
                <div id="js-tk-filter">
                    <div class="tk-search-value">
                        <?php echo JHTML::_('calendar', $date_start, 'date_start', 'ticket_date_start', $js_dateformat, array('class' => 'inputbox required', 'size' => '10', 'maxlength' => '19')); ?>
                    </div>
                    <div class="tk-search-value">
                        <?php echo JHTML::_('calendar', $date_end, 'date_end', 'ticket_date_end', $js_dateformat, array('class' => 'inputbox required', 'size' => '10', 'maxlength' => '19')); ?>
                    </div>
                    <div class="tk-search-button">
                        <button class="jsst-search" onClick="this.form.submit();" ><?php echo JText::_("Search"); ?></button>
                        <button class="jsst-reset" onclick='resetFrom();'><?php echo JText::_("Reset"); ?></button>
                        <input type="hidden" name="id" id="id" value="<?php echo $this->result['filter']['id']; ?>" />
                        <input type="hidden" name="option" id="option" value="com_jssupportticket" />
                        <input type="hidden" name="c" id="c" value="reports" />
                        <input type="hidden" name="layout" id="layout" value="stafftimereport" />
                    </div>
                </div>
                <div class="js-admin-report">
                    <span class="js-admin-subtitle"><?php echo JText::_('User'); ?></span>
                    <div id="curve_chart" style="height:400px;width:98%; "></div>
                </div>
            </form>
            
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
