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
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('a[href="#"]').click(function(e){
            e.preventDefault();
        });
    });
</script>
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/reports-staffreports.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){ ?> 
    <script type="text/javascript" src="https://www.google.com/jsapi?autoload={'modules':[{'name':'visualization','version':'1','packages':['corechart']}]}"></script>                
<script type="text/javascript">
    jQuery(document).ready(function ($) {

    });
    google.charts.load('current', {packages: ['corechart']});
    google.setOnLoadCallback(drawChart);

    function drawChart() {
        var data = new google.visualization.DataTable();
        data.addColumn('date', '<?php echo JText::_('Dates','js-support-ticket'); ?>');
        data.addColumn('number', '<?php echo JText::_('New','js-support-ticket'); ?>');
        data.addColumn('number', '<?php echo JText::_('Answered','js-support-ticket'); ?>');
        data.addColumn('number', '<?php echo JText::_('Pending','js-support-ticket'); ?>');
        data.addColumn('number', '<?php echo JText::_('Overdue','js-support-ticket'); ?>');
        data.addColumn('number', '<?php echo JText::_('Closed','js-support-ticket'); ?>');
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
</script>

        <?php 
        $curdate = date('Y-m-d', strtotime("now -1 month"));
        $enddate = date('Y-m-d');
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
        <div id="jsst-wrapper-top">
            <?php if($this->config['cur_location'] == 1){ ?>
                <div id="jsst-wrapper-top-left">
                    <div id="jsst-breadcrunbs">
                        <ul>
                            <li>
                                <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
                                    <?php echo JText::_('Dashboard'); ?>
                                </a>
                            </li>
                            <li>
                                <?php echo JText::_('Staff Reports'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="js-ticket-staff-report-wrapper">
            <div class="js-ticket-top-search-wrp">
                <?php /* <div class="js-ticket-search-heading-wrp">
                    <div class="js-ticket-heading-left">
                        <?php echo JText::_('Reports') ?>
                    </div>
                </div> */?>
                <div class="js-ticket-search-fields-wrp">
                    <form class="js-filter-form" action="index.php" method="post" name="adminForm" id="adminForm">
                        
                        <div class="js-ticket-fields-wrp">
                            <div class="js-ticket-form-field">
                                <?php echo JHTML::_('calendar', $date_start, 'date_start', 'ticket_date_start', $js_dateformat, array('class' => 'inputbox js-ticket-field-input required', 'size' => '10', 'maxlength' => '19')); ?>
                            </div>
                            <div class="js-ticket-form-field">
                                <?php echo JHTML::_('calendar', $date_end, 'date_end', 'ticket_date_end', $js_dateformat, array('class' => 'inputbox js-ticket-field-input required', 'size' => '10', 'maxlength' => '19')); ?>
                               
                            </div>
                        </div>
                        <div class="js-ticket-search-form-btn-wrp">
                            <button class="js-search-button" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                            <button class="js-reset-button" onclick="resetJsForm();this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                        </div>
                        <input type="hidden" name="c" value="reports" />
                        <input type="hidden" name="view" value="reports" />
                        <input type="hidden" name="layout" value="staffreports" />
                        <input type="hidden" name="boxchecked" value="0" />
                        <input type="hidden" name="task" value="" />
                        <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    </form>
                </div>
            </div>
            <div class="js-ticket-downloads-wrp">
                <div class="js-ticket-downloads-heading-wrp">
                    <?php echo JText::_('Reports Statistics') ?>
                </div>
                <div id="curve_chart" style="height:400px;width:100%; float: left;" class="js-ticket-pie-chart-wrapper"></div>
                <div class="js-admin-report-box-wrapper">
                    <div class="jsreportfixwidth js-admin-box box1" >
                        <div class="js-col-md-4 js-admin-box-image">
                            <img src="components/com_jssupportticket/include/images/ticket_icon.png" />
                        </div>
                        <div class="js-col-md-8 js-admin-box-content">
                            <div class="js-col-md-12 js-admin-box-content-number"><?php echo $this->result['ticket_total']['openticket']; ?></div>
                            <div class="js-col-md-12 js-admin-box-content-label"><?php echo JText::_('New'); ?></div>
                        </div>
                        <div class="js-col-md-12 js-admin-box-label"></div>
                    </div>
                    <div class="jsreportfixwidth js-admin-box box2">
                        <div class="js-col-md-4 js-admin-box-image">
                            <img src="components/com_jssupportticket/include/images/ticket_answered.png" />
                        </div>
                        <div class="js-col-md-8 js-admin-box-content">
                            <div class="js-col-md-12 js-admin-box-content-number"><?php echo $this->result['ticket_total']['answeredticket']; ?></div>
                            <div class="js-col-md-12 js-admin-box-content-label"><?php echo JText::_('Answered'); ?></div>
                        </div>
                        <div class="js-col-md-12 js-admin-box-label"></div>
                    </div>
                    <div class="jsreportfixwidth js-admin-box box3">
                        <div class="js-col-md-4 js-admin-box-image">
                            <img src="components/com_jssupportticket/include/images/ticket_pending.png" />
                        </div>
                        <div class="js-col-md-8 js-admin-box-content">
                            <div class="js-col-md-12 js-admin-box-content-number"><?php echo $this->result['ticket_total']['pendingticket']; ?></div>
                            <div class="js-col-md-12 js-admin-box-content-label"><?php echo JText::_('Pending'); ?></div>
                        </div>
                        <div class="js-col-md-12 js-admin-box-label"></div>
                    </div>
                    <div class="jsreportfixwidth js-admin-box box4">
                        <div class="js-col-md-4 js-admin-box-image">
                            <img src="components/com_jssupportticket/include/images/ticket_overdue.png" />
                        </div>
                        <div class="js-col-md-8 js-admin-box-content">
                            <div class="js-col-md-12 js-admin-box-content-number"><?php echo $this->result['ticket_total']['overdueticket']; ?></div>
                            <div class="js-col-md-12 js-admin-box-content-label"><?php echo JText::_('Overdue'); ?></div>
                        </div>
                        <div class="js-col-md-12 js-admin-box-label"></div>
                    </div>
                    <div class="jsreportfixwidth js-admin-box box5">
                        <div class="js-col-md-4 js-admin-box-image">
                            <img src="components/com_jssupportticket/include/images/ticket_close.png" />
                        </div>
                        <div class="js-col-md-8 js-admin-box-content">
                            <div class="js-col-md-12 js-admin-box-content-number"><?php echo $this->result['ticket_total']['closeticket']; ?></div>
                            <div class="js-col-md-12 js-admin-box-content-label"><?php echo JText::_('Closed'); ?></div>
                        </div>
                        <div class="js-col-md-12 js-admin-box-label"></div>
                    </div>
                </div>
            </div>
            <div class="js-ticket-downloads-wrp">
                <div class="js-ticket-downloads-heading-wrp">
                    <?php echo JText::_('Staff Members Reports') ?>
                </div>
                <?php if(!empty($this->result['staffs_report'])){
                        foreach($this->result['staffs_report'] AS $staff){ ?>   
                            <div class="js-admin-staff-wrapper">
                                <a href="index.php?option=com_jssupportticket&c=reports&layout=staffdetailreport&id=<?php echo $staff->id; ?>&date_start=<?php echo $this->result['filter']['date_start']; ?>&date_end=<?php echo $this->result['filter']['date_end']; ?>" class="js-admin-staff-anchor-wrapper">
                                    <div class="js-col-md-5 nopadding jsticket-left">
                                        <div class="js-col-md-3 js-report-staff-image-wrapper">
                                        <?php
                                            if($staff->photo){
                                                $imageurl = JURI::root().$this->config['data_directory']."/staffdata/staff_".$staff->id."/".$staff->photo;
                                            }else{
                                                $imageurl = "components/com_jssupportticket/include/images/user.png";
                                            }
                                        ?>
                                        <img class="js-report-staff-pic" src="<?php echo $imageurl; ?>" />
                                        </div>
                                        <div class="js-col-md-9 js-report-staff-cnt-wrapper">
                                            <div class="js-report-staff-name">
                                                <?php
                                                    if($staff->firstname && $staff->lastname){
                                                        $staffname = $staff->firstname . ' ' . $staff->lastname;
                                                    }else{
                                                        $staffname = $staff->display_name;
                                                    }
                                                    echo $staffname;
                                                ?>
                                            </div>
                                            <div class="js-report-staff-username">
                                                <?php
                                                    if($staff->username){
                                                        $username = $staff->username;
                                                    }else{
                                                        $username = $staff->user_nicename;
                                                    }
                                                    echo $username;
                                                ?>
                                            </div>
                                            <div class="js-report-staff-email">
                                                <?php
                                                    if($staff->email){
                                                        $email = $staff->email;
                                                    }else{
                                                        $email = $staff->user_email;
                                                    }
                                                    echo $email;
                                                ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="js-col-md-7 nopadding jsticket-right">
                                        <div class="jsreportfixwidth js-admin-report-box box1">
                                            <span class="js-report-box-number"><?php echo $staff->openticket; ?></span>
                                            <span class="js-report-box-title"><?php echo JText::_('New'); ?></span>
                                            <div class="js-report-box-color"></div>
                                        </div>
                                        <div class="jsreportfixwidth js-admin-report-box box2">
                                            <span class="js-report-box-number"><?php echo $staff->answeredticket; ?></span>
                                            <span class="js-report-box-title"><?php echo JText::_('Answered'); ?></span>
                                            <div class="js-report-box-color"></div>
                                        </div>
                                        <div class="jsreportfixwidth js-admin-report-box box3">
                                            <span class="js-report-box-number"><?php echo $staff->pendingticket; ?></span>
                                            <span class="js-report-box-title"><?php echo JText::_('Pending'); ?></span>
                                            <div class="js-report-box-color"></div>
                                        </div>
                                        <div class="jsreportfixwidth js-admin-report-box box4">
                                            <span class="js-report-box-number"><?php echo $staff->overdueticket; ?></span>
                                            <span class="js-report-box-title"><?php echo JText::_('Overdue'); ?></span>
                                            <div class="js-report-box-color"></div>
                                        </div>
                                        <div class="jsreportfixwidth js-admin-report-box box5">
                                            <span class="js-report-box-number"><?php echo $staff->closeticket; ?></span>
                                            <span class="js-report-box-title"><?php echo JText::_('Closed'); ?></span>
                                            <div class="js-report-box-color"></div>
                                        </div>
                                    </div>
                                </a>
                            </div>
                        <?php } ?>
                        <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=reports&layout=staffreports&Itemid='.$this->Itemid); ?>" method="post">
                            <div id="jl_pagination" class="pagination">
                                <div id="jl_pagination_pageslink">
                                    <?php echo $this->pagination->getPagesLinks(); ?>
                                </div>
                                <div id="jl_pagination_box">
                                    <?php   
                                        // echo JText::_('Display #');
                                        echo $this->pagination->getLimitBox();
                                    ?>
                                </div>
                                <div id="jl_pagination_counter">
                                    <?php echo $this->pagination->getResultsCounter(); ?>
                                </div>
                            </div>
                        </form>
                 <?php } else {   
                        messageslayout::getRecordNotFound(); //Empty Record
                    } ?>
                <?php    
                }else{
                    messageslayout::getStaffDisable(); //staff disabled
                }
            }else{
                messageslayout::getNotStaffMember(); //user not staff
            }
        }else{
            messageslayout::getUserGuest($this->layoutname,$this->Itemid); //user guest
        }
    }else{
        messageslayout::getPermissionNotAllow(); //permission not granted
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
</div>
<script type="text/javascript">
    function resetJsForm(){
        var form = jQuery('form#adminForm');
        form.find("input[type=text]").val("");
        form.find('select').prop('selectedIndex', 0);
        jQuery("<input type='hidden' value='1' />")
         .attr("id", "jsresetbutton")
         .attr("name", "jsresetbutton")
         .appendTo(form);
    }
</script>
