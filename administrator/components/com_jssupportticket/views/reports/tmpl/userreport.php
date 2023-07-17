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
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
$document->addScript('components/com_jssupportticket/include/js/circle.js');
?>

<script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>
<script type="text/javascript">
    function setUserLink() {
        jQuery("a.js-userpopup-link").each(function () {
            var anchor = jQuery(this);
            jQuery(anchor).click(function (e) {
                var id = jQuery(this).attr('data-id');
                var name = jQuery(this).html();
                jQuery("input#username-text").val(name);
                jQuery("input#uid").val(id);
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });
            });
        });
    }
    function updateuserlist(pagenum){
        var username = jQuery("input#username").val();
        var name = jQuery("input#name").val();
        var emailaddress = jQuery("input#emailaddress").val();
        jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1", {username:username,name:name,emailaddress:emailaddress,userlimit:pagenum}, function (data) {
            if(data){
                jQuery("div#records").html("");
                jQuery("div#records").html(data);
                setUserLink();
            }
        });
    }
    jQuery(document).ready(function ($) {
        jQuery("a#userpopup").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupblack").show();
            jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1", {}, function (data) {
                if(data){
                    jQuery("div#records").html("");
                    jQuery("div#records").html(data);
                    setUserLink();
                }
            });
            jQuery("div#userpopup").slideDown('slow');
        });
        jQuery("form#userpopupsearch").submit(function (e) {
            e.preventDefault();
            var username = jQuery("input#username").val();
            var name = jQuery("input#name").val();
            var emailaddress = jQuery("input#emailaddress").val();
            jQuery.post('index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1', {username:username,name: name, emailaddress: emailaddress}, function (data) {
                if (data) {
                    jQuery("div#records").html(data);
                    setUserLink();
                }
            });//jquery closed
        });
        jQuery("span.close, div#userpopupblack").click(function (e) {
            jQuery("div#userpopup").slideUp('slow', function () {
                jQuery("div#userpopupblack").hide();
            });

        });
    });

    function resetFrom(){
        document.getElementById('ticket_date_start').value = '';
        document.getElementById('ticket_date_end').value = '';
        jQuery('#uid').val('');
        document.getElementById('username-text').value = '';
        var form = jQuery('form#jssupportticketform');
        jQuery("<input type='hidden' value='1' />")
         .attr("id", "jsresetbutton")
         .attr("name", "jsresetbutton")
         .appendTo(form);
        document.getElementById('jssupportticketform').submit();
    }

    google.charts.load('current', {'packages':['corechart']});
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
<div id="userpopupblack" style="display:none;"></div>
<div id="userpopup" style="display:none;">
        <form id="userpopupsearch">
            <div class="search-center">
                <div class="search-center-heading"><?php echo JText::_('Select user'); ?><span class="close"></span></div>
                <div class="js-col-md-12">
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="username" id="username" placeholder="<?php echo JText::_('Username'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="name" id="name" placeholder="<?php echo JText::_('Name'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo JText::_('Email Address'); ?>"/>
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value-button">
                        <div class="js-button">
                            <input class="js-button-search" type="submit" value="<?php echo JText::_('Search'); ?>" />
                        </div>
                        <div class="js-button">
                            <input class="js-button-reset" type="submit" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo JText::_('Reset'); ?>" />
                        </div>
                    </div>
                </div>
            </div>
        </form>
    <div id="records">
        <div id="records-inner">
            <div class="js-staff-searc-desc">
                <?php echo JText::_('Use Search Feature To Select The User'); ?>
            </div>
        </div>
    </div>
</div>
<?php
    $curdate = date('Y-m-d');
    $enddate = date('Y-m-d', strtotime("now -1 month"));
    $date_start = !empty($this->result['filter']['date_start']) ? $this->result['filter']['date_start'] : $curdate;
    $date_end = !empty($this->result['filter']['date_end']) ? $this->result['filter']['date_end'] : $enddate;
    $uid = !empty($this->result['filter']['uid']) ? $this->result['filter']['uid'] : '';
?>

<?php 
$t_name = 'getusersexport';
$link_export = 'index.php?option='.$this->option.'&c=export&task='.$t_name.'&uid='.$uid.'&date_start='.$date_start.'&date_end='.$date_end;
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
                        <li><?php echo JText::_('User Reports'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('User Reports'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=reports&task=reports'; ?>
            <a id="tk-export" class="tk-heading-addbutton" href="<?php echo $link_export; ?>">
                <img alt="Export" src="components/com_jssupportticket/include/images/export-icon.png">
                <?php echo JText::_('Export Data'); ?>
            </a>
        </div>
        <?php
        $open_percentage = 0;
        $close_percentage = 0;
        $answered_percentage = 0;
        $overdue_percentage = 0; 
        if($this->result['ticket_total']['totalticket'] != 0){
            $open_percentage  = round(($this->result['ticket_total']['openticket'] / $this->result['ticket_total']['totalticket']) * 100);
            $close_percentage  = round(($this->result['ticket_total']['closeticket'] / $this->result['ticket_total']['totalticket']) * 100);
            $answered_percentage = round(($this->result['ticket_total']['answeredticket'] / $this->result['ticket_total']['totalticket']) * 100);
            $overdue_percentage = round(($this->result['ticket_total']['overdueticket'] / $this->result['ticket_total']['totalticket']) * 100);
        }
                    
        $allticket_percentage = 100;?>
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
                                echo " ( " .$this->result['ticket_total']['openticket']. " ) "; 
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
                                        echo " ( ".$this->result['ticket_total']['closeticket']. " ) "; 
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
                                        echo " ( " .$this->result['ticket_total']['answeredticket']." ) "; 
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
                                        echo " ( ".$this->result['ticket_total']['overdueticket']." ) "; 
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
                                <div class="js-ticket-circle-count-text">
                                    <?php 
                                        echo JText::_('All Tickets');
                                        if($this->config['show_count_tickets'] == 1)
                                        echo " ( " .$this->result['ticket_total']['totalticket']. " ) "; 
                                    ?>
                                </div>
                            </a>
                        </div>
                    </div>
            <form class="js-filter-form js-report-form" name="jssupportticketform" id="jssupportticketform" method="post" action="index.php">
            <div id="js-tk-filter">
                <div class="tk-search-value">
                    <?php echo JHTML::_('calendar', $date_start, 'date_start', 'ticket_date_start', $js_dateformat, array('class' => 'inputbox required', 'size' => '10', 'maxlength' => '19')); ?>
                </div>
                <div class="tk-search-value">
                    <?php echo JHTML::_('calendar', $date_end, 'date_end', 'ticket_date_end', $js_dateformat, array('class' => 'inputbox required', 'size' => '10', 'maxlength' => '19')); ?>
                </div>
                <div class="tk-search-value">
                    <?php if (!empty($this->result['filter']['username'])) { ?>
                        <div id="username-div">
                            <input type="text" value="<?php echo $this->result['filter']['username']; ?>" id="username-text" readonly="readonly" data-validation="required"/>
                        </div><a href="#" id="userpopup"><?php echo JText::_('Select user'); ?></a>
                    <?php } else { ?>
                        <div id="username-div"></div>
                        <input type="text" value="" id="username-text" readonly="readonly" data-validation="required"/>
                        <a href="#" id="userpopup"><?php echo JText::_('Select user'); ?></a>
                    <?php } ?>
                </div>
                <div class="tk-search-button">
                    <button class="js-form-search" onClick="this.form.submit();" ><?php echo JText::_("Search"); ?></button>
                    <button class="js-form-reset" onclick='resetFrom();'><?php echo JText::_("Reset"); ?></button>
                    <input type="hidden" name="option" value="com_jssupportticket" />
                    <input type="hidden" name="c" value="reports" />
                    <input type="hidden" name="layout" value="userreport" />
                    <input type="hidden" name="uid" id="uid" value="<?php echo $uid; ?>" />
                </div>
            </div>
            </form>
            <div class="js-admin-report">
            <span class="js-admin-subtitle"><?php echo JText::_('Overall Report'); ?></span>
            <div id="curve_chart" style="height:400px;width:100%; "></div>
            </div>
            <div class="js-admin-report">
            <span class="js-admin-subtitle"><?php echo JText::_('Users'); ?></span>
            <?php
            if(!empty($this->result['users_report'])){
                foreach($this->result['users_report'] AS $staff){ ?>
                    <div class="js-admin-staff-wrapper">
                        <a href="index.php?option=com_jssupportticket&c=reports&layout=userdetailreport&id=<?php echo $staff->id; ?>&date_start=<?php echo $this->result['filter']['date_start']; ?>&date_end=<?php echo $this->result['filter']['date_end']; ?>" class="js-admin-staff-anchor-wrapper">
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
                                        $staffname = $staff->display_name;
                                        echo $staffname;
                                    ?>
                                </div>
                                <div class="js-report-staff-email">
                                    <?php
                                        if(isset($staff->email)){
                                            $email = $staff->email;
                                        }else{
                                            $email = $staff->user_email;
                                        }
                                        echo $email;
                                    ?>
                                </div>
                            </div>
                        </div>
                        <div class="js-col-md-8 nopadding">
                            <div class="js-admin-report-box-width js-admin-report-box box1">
                                <span class="js-report-box-number"><?php echo $staff->openticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('New'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box2">
                                <span class="js-report-box-number"><?php echo $staff->answeredticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Answered'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box3">
                                <span class="js-report-box-number"><?php echo $staff->pendingticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Pending'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box4">
                                <span class="js-report-box-number"><?php echo $staff->overdueticket; ?></span>
                                <span class="js-report-box-title"><?php echo JText::_('Overdue'); ?></span>
                                <div class="js-report-box-color"></div>
                            </div>
                            <div class="js-admin-report-box-width js-admin-report-box box5">
                                <span class="js-report-box-number"><?php echo $staff->closeticket; ?></span>
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
