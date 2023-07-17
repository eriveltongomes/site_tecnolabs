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
        data.addColumn('date', '<?php echo JText::_('Dates'); ?>');
        data.addColumn('number', '<?php echo JText::_('New'); ?>');
        data.addColumn('number', '<?php echo JText::_('Answered'); ?>');
        data.addColumn('number', '<?php echo JText::_('Pending'); ?>');
        data.addColumn('number', '<?php echo JText::_('Overdue'); ?>');
        data.addColumn('number', '<?php echo JText::_('Closed'); ?>');
        data.addRows([
            <?php echo $this->result['line_chart_json_array']; ?>
        ]);        

        var options = {
          colors:['#1EADD8','#179650','#D98E11','#DB624C','#5F3BBB'],
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
        var form = jQuery('form#jssupportticketform');
        jQuery("<input type='hidden' value='1' />")
         .attr("id", "jsresetbutton")
         .attr("name", "jsresetbutton")
         .appendTo(form);
        document.getElementById('jssupportticketform').submit();
    }
</script>
<?php 
    $curdate = date('Y-m-d');
    $enddate = date('Y-m-d', strtotime("now -1 month"));
    $date_start = !empty($this->result['filter']['date_start']) ? $this->result['filter']['date_start'] : $curdate;
    $date_end = !empty($this->result['filter']['date_end']) ? $this->result['filter']['date_end'] : $enddate;
?>


<?php 
$t_name = 'getdepartmentmemberexportbydepartmentid';
$link_export = 'index.php?option='.$this->option.'&c=export&task='.$t_name.'&id='.$this->result['department_report']->id.'&date_start='.$date_start.'&date_end='.$date_end;
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
                            <a href="index.php?option=com_jssupportticket&c=reports&layout=departmentreport" title="Dashboard">
                                <?php echo JText::_('Department Reports'); ?>
                            </a>
                        </li>
                        <li>
                            <?php echo JText::_('Department Detail Report'); ?>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Department Detail Report'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=reports&task=reports'; ?>           
            <a id="tk-export" class="tk-heading-addbutton" href="<?php echo $link_export; ?>">
                <img alt="Export" src="components/com_jssupportticket/include/images/export-icon.png">
                <?php echo JText::_('Export Data'); ?>
            </a>        
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
                        <input type="hidden" name="id" id="id" value="<?php echo $this->result['department_report']->id; ?>" />
                        <input type="hidden" name="option" id="option" value="com_jssupportticket" />
                        <input type="hidden" name="c" id="c" value="reports" />
                        <input type="hidden" name="layout" id="layout" value="departmentdetailreport" />
                    </div>
                </div>
                <div class="js-admin-report">
                    <span class="js-admin-subtitle"><?php echo JText::_('Department Statistics'); ?></span>
                    <div id="curve_chart" style="height:400px;width:98%; "></div>
                </div>
            <?php 
                $department = $this->result['department_report'];
                if(!empty($department)){ ?> 
                <div class="js-admin-report">     
                    <div class="js-admin-staff-wrapper padding">            
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
                        <div class="js-col-md-8 nopadding ">
                            <div class="js-col-md-2 js-admin-report-box box1">
                                <span class="js-report-box-number"><?php echo $department->openticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('New'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-col-md-2 js-admin-report-box box2">
                                <span class="js-report-box-number"><?php echo $department->answeredticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Answered'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-col-md-2 js-admin-report-box box3">
                                <span class="js-report-box-number"><?php echo $department->pendingticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Pending'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-col-md-2 js-admin-report-box box4">
                                <span class="js-report-box-number"><?php echo $department->overdueticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Overdue'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-col-md-2 js-admin-report-box box5">
                                <span class="js-report-box-number"><?php echo $department->closeticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Closed'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php
                } ?>
                <div class="js-admin-report">
                <span class="js-admin-subtitle"><?php echo JText::_('Tickets'); ?></span>
                <?php
                    if(!empty($this->result['department_tickets'])){ 
                        ?>
                            <div class="js-ticket-admin-cp-tickets">
                                <div class="js-ticket-admin-cp-head js-ticket-admin-hide-head">
                                    <div class="js-col-xs-12 js-col-md-3 js-report-detail-head"><?php echo JText::_('Subject'); ?></div>
                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-head"><?php echo JText::_('Status'); ?></div>
                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-head"><?php echo JText::_('Priority'); ?></div>
                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-head"><?php echo JText::_('Created'); ?></div>
                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-head"><?php echo JText::_('Rating'); ?></div>
                                    <div class="js-col-xs-12 js-col-md-1 js-report-detail-head"><?php echo JText::_('Time'); ?></div>
                                </div>
                            <?php
                            foreach($this->result['department_tickets'] AS $ticket){ 
                                $hours = floor($ticket->time / 3600);
                                $mins = floor(($ticket->time / 60) % 60);
                                $secs = floor($ticket->time % 60);
                                $avgtime = sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                $rating_color = 0;
                                if($ticket->rating > 4){
                                    $rating_color = '#ea1d22';
                                }elseif($ticket->rating > 3){
                                    $rating_color = '#f58634';
                                }elseif($ticket->rating > 2){
                                    $rating_color = '#a8518a';
                                }elseif($ticket->rating > 1){
                                    $rating_color = '#0098da';
                                }elseif($ticket->rating > 0){
                                    $rating_color = '#069a2e';
                                }
                                ?>
                                <div class="js-ticket-admin-cp-data">
                                    <div class="js-col-xs-12 js-col-md-3 js-report-detail-body"><span class="js-ticket-admin-cp-showhide"><?php echo JText::_('Subject');
                            echo " : "; ?></span> <a href="index.php?option=com_jssupportticket&c=ticket&layout=ticketdetails&cid[]=<?php echo $ticket->id; ?>"><?php echo $ticket->subject; ?></a></div>
                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-body">
                                        <span class="js-ticket-admin-cp-showhide" ><?php echo JText::_('Status');
                            echo " : "; ?></span>
                                        <?php
                                        $style = "red;";
                                        $status = ' ';
                                        if ($ticket->status == 0) {
                                            $style = "red;";
                                            $status = JText::_('New');
                                        } elseif ($ticket->status == 1) {
                                            $style = "orange;";
                                            $status = JText::_('Waiting Staff Reply');
                                        } elseif ($ticket->status == 2) {
                                            $style = "#FF7F50;";
                                            $status = JText::_('In progress');
                                        } elseif ($ticket->status == 3) {
                                            $style = "green;";
                                            $status = JText::_('Waiting your reply');
                                        } elseif ($ticket->status == 4) {
                                            $style = "blue;";
                                            $status = JText::_('Closed');
                                        } elseif ($ticket->status == 5) {
                                            $style = "red;";
                                            $status = JText::_('Close Due To Merge');
                                        }
                                        echo '<span style="color:' . $style . '">' . $status . '</span>';
                                        ?>
                                    </div>
                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-body"> 
                                        <span class="js-ticket-admin-cp-showhide" >
                                            <?php echo JText::_('Priority');
                                            echo " : "; ?>
                                        </span>
                                        <span class="js-tkt-rep-prty" style="background-color:<?php echo $ticket->prioritycolour; ?>;">
                                            <?php echo JText::_($ticket->priority); ?>
                                        </span>
                                    </div>

                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-body">
                                        <span class="js-ticket-admin-cp-showhide" >
                                            <?php echo JText::_('Created'); echo " : "; ?>
                                        </span> 
                                        <?php echo JHtml::_('date',$ticket->created,$this->config['date_format']); ?>
                                    </div>
                                    <div class="js-col-xs-12 js-col-md-2 js-report-detail-body">
                                        <span class="js-ticket-admin-cp-showhide" >
                                            <?php echo JText::_('Rating'); echo " : "; ?>
                                        </span> 
                                        <?php if($ticket->rating > 0){ ?>
                                            <span style="color:<?php echo $rating_color; ?>;font-weight:bold;font-size:16px;" > <?php echo $ticket->rating;?></span>
                                            <?php echo JText::_('out of').'<span style="font-weight:bold;font-size:15px;" >&nbsp;5</span>';
                                        }else{
                                            echo 'NA';
                                        } ?>
                                    </div>
                                    <div class="js-col-xs-12 js-col-md-1 js-report-detail-body">
                                        <span class="js-ticket-admin-cp-showhide" >
                                            <?php echo JText::_('Time'); echo " : "; ?>
                                        </span> 
                                        <?php echo $avgtime; ?>
                                    </div>

                                </div>
                            <?php
                            } ?>
                        </div>
                    <?php } ?>
                </div>
                </div>
            </form>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
