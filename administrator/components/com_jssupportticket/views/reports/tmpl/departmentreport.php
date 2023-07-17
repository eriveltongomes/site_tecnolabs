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
    function resetFrom(){
        document.getElementById('ticket_date_start').value = '';
        document.getElementById('ticket_date_end').value = '';
        var form = jQuery('form#jssupportticketform');
        jQuery("<input type='hidden' value='1' />")
         .attr("id", "jsresetbutton")
         .attr("name", "jsresetbutton")
         .appendTo(form);
        document.getElementById('jssupportticketform').submit();
    }
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawChart);
    var data = null;
    var chart = null;
    var options = null;
    function drawChart() {
        data = new google.visualization.DataTable();
        data.addColumn('date', '<?php echo JText::_('Dates'); ?>');
        data.addColumn('number', '<?php echo JText::_('New'); ?>');
        data.addColumn('number', '<?php echo JText::_('Answered'); ?>');
        data.addColumn('number', '<?php echo JText::_('Pending'); ?>');
        data.addColumn('number', '<?php echo JText::_('Overdue'); ?>');
        data.addColumn('number', '<?php echo JText::_('Closed'); ?>');
        data.addRows([
            <?php echo $this->result['line_chart_json_array']; ?>
        ]);

        options = {
            colors:['#1EADD8','#179650','#D98E11','#DB624C','#5F3BBB'],
            curveType: 'function',
            legend: { position: 'bottom' },
            pointSize: 6,
            // This line will make you select an entire row of data at a time
            focusTarget: 'category',
            chartArea: {width:'90%',top:50}
        };

        chart = new google.visualization.LineChart(document.getElementById('curve_chart'));
        chart.draw(data, options);
    }
    function resizeCharts () {
        // redraw charts, dashboards, etc here
        chart.draw(data, options);
    }
    jQuery(window).resize(resizeCharts);
</script>
<?php
    $curdate = date('Y-m-d');
    $enddate = date('Y-m-d', strtotime("now -1 month"));
    $date_start = !empty($this->result['filter']['date_start']) ? $this->result['filter']['date_start'] : $curdate;
    $date_end = !empty($this->result['filter']['date_end']) ? $this->result['filter']['date_end'] : $enddate;
    $uid = !empty($this->result['filter']['uid']) ? $this->result['filter']['uid'] : '';
?>

<?php 
$t_name = 'getdepartmentexport';
$link_export = 'index.php?option='.$this->option.'&c=export&task='.$t_name.'&date_start='.$date_start.'&date_end='.$date_end;
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
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo JText::_('Dashboard'); ?></a></li>
                        <li><?php echo JText::_('Department Reports'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Department Reports'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=reports&task=reports'; ?>
            <a id="tk-export" class="tk-heading-addbutton" href="<?php echo $link_export; ?>">
                <img alt="Export" src="components/com_jssupportticket/include/images/export-icon.png">
                <?php echo JText::_('Export Data'); ?>
            </a>            
        
        </div>
        <div id="jsstadmin-data-wrp" class="js-bg-null js-padding-all-null"> 
                        
            <form class="js-filter-form js-report-form" name="jssupportticketform" id="jssupportticketform" method="post" action="index.php">
            <div id="js-tk-filter">
                <div class="tk-search-value">
                    <?php echo JHTML::_('calendar', $date_start, 'date_start', 'ticket_date_start', $js_dateformat, array('class' => 'inputbox required', 'size' => '10', 'maxlength' => '19')); ?>
                </div>
                <div class="tk-search-value">
                    <?php echo JHTML::_('calendar', $date_end, 'date_end', 'ticket_date_end', $js_dateformat, array('class' => 'inputbox required', 'size' => '10', 'maxlength' => '19')); ?>
                </div>
                <div class="tk-search-button">
                    <button class="js-form-search" onClick="this.form.submit();" ><?php echo JText::_("Search"); ?></button>
                    <button class="js-form-reset" onclick='resetFrom();'><?php echo JText::_("Reset"); ?></button>
                    <input type="hidden" name="option" value="com_jssupportticket" />
                    <input type="hidden" name="c" value="reports" />
                    <input type="hidden" name="layout" value="departmentreport" />
                </div>
            </div>
            </form>
            <div class="js-admin-report">
            <span class="js-admin-subtitle"><?php echo JText::_('Overall Report'); ?></span>
            <div id="curve_chart" style="height:400px;width:100%; "></div></div> 
            <div class="js-admin-report">
            <span class="js-admin-subtitle"><?php echo JText::_('Departments'); ?></span>
            <?php
            if(!empty($this->result['departments_report'])){
                foreach($this->result['departments_report'] AS $department){ ?>
                    <div class="js-admin-staff-wrapper">
                        <a href="index.php?option=com_jssupportticket&c=reports&layout=departmentdetailreport&id=<?php echo $department->id; ?>&date_start=<?php echo $this->result['filter']['date_start']; ?>&date_end=<?php echo $this->result['filter']['date_end']; ?>" class="js-admin-staff-anchor-wrapper">
                        <div class="js-admin-staff-cnt">
                            <div class="js-report-staff-image">
                                <?php
                                    $imageurl = "components/com_jssupportticket/include/images/user.png";
                                ?>
                                <img class="js-report-staff-pic" src="<?php echo $imageurl; ?>" />
                            </div>
                            <div class="js-report-staff-cnt">
                                <div class="js-report-staff-name">
                                    <?php
                                        $departmentname = $department->display_name;
                                        echo $departmentname;
                                    ?>
                                </div>
                                <div class="js-report-staff-email">
                                    <?php
                                        if(isset($department->department_email)){
                                            echo $department->department_email;
                                        }
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="js-col-md-8 nopadding">
                            <div class="js-admin-report-box-width js-admin-report-box box1">
                                <span class="js-report-box-number"><?php echo $department->openticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('New'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box2">
                                <span class="js-report-box-number"><?php echo $department->answeredticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Answered'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box3">
                                <span class="js-report-box-number"><?php echo $department->pendingticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Pending'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box4">
                                <span class="js-report-box-number"><?php echo $department->overdueticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Overdue'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box5">
                                <span class="js-report-box-number"><?php echo $department->closeticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Closed'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                        </div>
                    </a>
                </div>
            <?php
            }
        }
        ?>
        </div>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
