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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/bootstrap.min.css');
$document->addStyleSheet('components/com_jssupportticket/include/css/jssupportticketdefault.css');
$document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
$document->addScript('components/com_jssupportticket/include/js/circle.js');
?>

<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
    });

    google.load("visualization", "1", {packages:["corechart"]});
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawBarChart);
    function drawBarChart() {
        var data = google.visualization.arrayToDataTable([
         ['<?php echo JText::_('Status'); ?>', '<?php echo JText::_('Tickets By Status'); ?>', { role: 'style' }],
         <?php echo $this->result['bar_chart']; ?>
      ]);
     var view = new google.visualization.DataView(data);
      view.setColumns([0, 1,
                       { calc: "stringify",
                         sourceColumn: 1,
                         type: "string",
                         role: "annotation" },
                       2]);

      var options = {
        //title: "Density of Precious Metals, in g/cm^3",
        width: '95%',
        bar: {groupWidth: "95%"},        
        legend: { position: "none" },
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("bar_chart"));
      chart.draw(view, options);        
    }
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawStackChart);
    function drawStackChart() {
      var data = google.visualization.arrayToDataTable([
        ['<?php echo JText::_('Tickets'); ?>', '<?php echo JText::_('Direct'); ?>', '<?php echo JText::_('Email'); ?>', { role: 'annotation' } ],
        <?php echo $this->result['stack_data']; ?>
      ]);

      var view = new google.visualization.DataView(data);
      var options = {
        width: '95%',
        //height: 400,
        legend: { position: 'top', maxLines: 3 },
        bar: { groupWidth: '75%' },
        isStacked: true,
      };
      var chart = new google.visualization.ColumnChart(document.getElementById("stack_chart"));
      chart.draw(view, options);
    }   
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawPie3d1Chart);
    function drawPie3d1Chart() {
        var data = google.visualization.arrayToDataTable([
          ['<?php echo JText::_('Departments'); ?>', '<?php echo JText::_('Tickets By Department'); ?>'],
          <?php echo $this->result['pie3d_chart1']; ?>
        ]);

        var options = {
          title: '<?php echo JText::_('Ticket by departments'); ?>',
          chartArea :{width:450,height:350,top:80,left:80},
          is3D: true,
        };

        var chart = new google.visualization.PieChart(document.getElementById('pie3d_chart1'));
        chart.draw(data, options);
    }   
    google.charts.load('current', {packages: ['corechart']});    
    google.setOnLoadCallback(drawPie3d2Chart);
    function drawPie3d2Chart() {
        var data = google.visualization.arrayToDataTable([
          ['<?php echo JText::_('Priorities'); ?>', '<?php echo JText::_('Tickets By Priority'); ?>'],
          <?php echo $this->result['pie3d_chart2']; ?>
        ]);

        var options = {
          title: '<?php echo JText::_('Tickets By Priorities'); ?>',
          chartArea :{width:450,height:350,top:80,left:80},
          is3D: true,
          colors:<?php echo $this->result['priorityColorList'] ?>
        };

        var chart = new google.visualization.PieChart(document.getElementById('pie3d_chart2'));
        chart.draw(data, options);
    }   
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawStackChartHorizontal);
    function drawStackChartHorizontal() {
      var data = google.visualization.arrayToDataTable([
        <?php
            echo $this->result['stack_chart_horizontal']['title'].',';
            echo $this->result['stack_chart_horizontal']['data'];
        ?>
      ]);

      var view = new google.visualization.DataView(data);

      var options = {
        legend: { position: 'top', maxLines: 3 },
        bar: { groupWidth: '75%' },
        isStacked: true,
        colors:<?php echo $this->result['priorityColorList'] ?>
      };
      var chart = new google.visualization.BarChart(document.getElementById("stack_chart_horizontal"));
      chart.draw(view, options);
    }
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawSliceChart);
    function drawSliceChart() {
      var data = google.visualization.arrayToDataTable([
        ['<?php echo JText::_('Tickets'); ?>', '<?php echo JText::_('Staff Member Tickets'); ?>'],
        <?php echo $this->result['slice_chart']; ?>
      ]);

      var options = {
        //title: 'Indian Language Use',
        pieSliceText: 'label',
        legend : {position: 'left'},
        chartArea : {width:500,height:300},
        slices: {  2: {offset: 0.2},
                  4: {offset: 0.3},
                  5: {offset: 0.4},
                  7: {offset: 0.5},
                  9: {offset: 0.5},
        },
      };

      var chart = new google.visualization.PieChart(document.getElementById('slice_chart'));
      chart.draw(data, options);
    }
</script>
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
                        <li><?php echo JText::_('Overall Statistics'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Overall Statistics'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=reports&task=reports'; ?>
            <?php 
            $link_exp = 'index.php?option='.$this->option.'&c=export&task=getoverallexport';
            ?>                      
            <a id="tk-export" class="tk-heading-addbutton" href="<?php echo $link_exp; ?>">
              <img alt="Export" src="components/com_jssupportticket/include/images/export-icon.png">
                <?php echo JText::_('Export Data'); ?>  
            </a>
        </div>
        <?php 
        $open_percentage = 0;
        $close_percentage = 0;
        $answered_percentage = 0;
        $overdue_percentage = 0; 
        $allticket_percentage = 0;

        if($this->result['alltickets'] != 0){
          $open_percentage  = round(($this->result['openticket'] / $this->result['alltickets']) * 100);
          $close_percentage  = round(($this->result['closeticket'] / $this->result['alltickets']) * 100);
          $answered_percentage = round(($this->result['answeredticket'] / $this->result['alltickets']) * 100);
          $overdue_percentage = round(($this->result['overdueticket'] / $this->result['alltickets']) * 100);
        }
        $allticket_percentage = 100;
        ?>
        <div id="jsstadmin-data-wrp" class="js-bg-null js-padding-all-null">
            <div class="js-row js-ticket-top-cirlce-count-wrp">
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-open">
                      <a class="js-ticket-green js-myticket-link" href="javascript:void(0);">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $open_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-circle-count-text">
                                <?php 
                                      echo JText::_('Open');
                                      if($this->config['show_count_tickets'] == 1)
                                      echo " ( " . $this->result['openticket'] ." ) "; 
                                ?>
                            </div>
                      </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-answer">
                    <a class="js-ticket-pink js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $answered_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text">
                            <?php 
                                echo JText::_('Answered');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( " . $this->result['answeredticket'] . " ) "; 
                            ?>
                        </div>
                    </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-overdue">
                    <a class="js-ticket-orange js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $overdue_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text">
                            <?php 
                                echo JText::_('Overdue');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( ". $this->result['overdueticket'] ." ) "; 
                            ?>
                        </div>
                    </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-close">
                    <a class="js-ticket-red js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $close_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text">
                            <?php 
                                echo JText::_('Closed');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( ". $this->result['closeticket'] ." ) "; 
                            ?>
                        </div>
                    </a>
                </div>
                <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-allticket">
                    <a class="js-ticket-blue js-myticket-link" href="javascript:void(0);">
                        <div class="js-ticket-cricle-wrp ">
                            <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $allticket_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                <div class="loader-bg"></div>
                            </div>
                        </div>
                        <div class="js-ticket-circle-count-text js-ticket-blue">
                            <?php 
                                echo JText::_('All Tickets');
                                if($this->config['show_count_tickets'] == 1)
                                echo " ( " . $this->result['alltickets'] ." ) "; 
                            ?>
                        </div>
                    </a>
                </div>
            </div>
            <div class="js-admin-report">
                <span class="js-admin-subtitle"><?php echo JText::_('Tickets By Status And Priorities'); ?></span>
                <div id="bar_chart" style="height:500px;width:100%; "></div>
            </div>
            <div class="js-admin-report halfwidth">
                <span class="js-admin-subtitle"><?php echo JText::_('Tickets By Departments'); ?></span>
                <div id="pie3d_chart1" style="height:400px;width:100%;"></div>
            </div>
            <div class="js-admin-report halfwidth">
                <span class="js-admin-subtitle"><?php echo JText::_('Tickets By Priorities'); ?></span>
                <div id="pie3d_chart2" style="height:400px;width:100%;"></div>
            </div>
            <div class="js-admin-report halfwidth">
                <span class="js-admin-subtitle"><?php echo JText::_('Tickets By Status And Priorities'); ?></span>
                <div id="stack_chart_horizontal" style="height:400px;width:100%;"></div>
            </div>
            <div class="js-admin-report halfwidth">
              <span class="js-admin-subtitle"><?php echo JText::_('Tickets By Channel'); ?></span>
              <div id="stack_chart" style="height:400px;width:100%;"></div>
            </div>
            <div class="js-admin-report">
                <span class="js-admin-subtitle"><?php echo JText::_('Tickets By Staff'); ?></span>
                <div id="slice_chart" style="height:400px;width:100%;"></div>
            </div>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>