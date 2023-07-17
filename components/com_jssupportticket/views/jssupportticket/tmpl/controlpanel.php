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
<script>
    <?php
                   if($this->user->getIsStaff()){
                             if($this->config['cplink_ticketstats_staff'] == 1){ ?>
                google.charts.load('current', {'packages':['corechart']});
                google.setOnLoadCallback(drawStackChartHorizontal);
                function drawStackChartHorizontal() {
                    var data = google.visualization.arrayToDataTable([
                        <?php
                        echo $this->result_graph['stack_chart_horizontal']['title'].',';
                        echo $this->result_graph['stack_chart_horizontal']['data'];
                        ?>
                    ]);

                    var view = new google.visualization.DataView(data);

                  var options = {
                    height:570,
                    legend: { position: 'top', maxLines: 3 },
                    bar: { groupWidth: '75%' },
                    isStacked: true,
                    colors:<?php echo $this->result_graph['stack_chart_horizontal']['colors']; ?>
                  };
                  var chart = new google.visualization.BarChart(document.getElementById("stack_chart_horizontal"));
                  chart.draw(view, options);
                }
    <?php }
        } ?>
</script>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            jQuery('a[href="#"]').click(function(e){
                e.preventDefault();
            });
            jQuery("div#js-ticket-main-black-background,span#js-ticket-popup-close-button").click(function () {
                jQuery("div#js-ticket-main-popup").slideUp();
                setTimeout(function () {
                    jQuery("div#js-ticket-main-black-background").hide();
                }, 600);

            });
        });
    </script>
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE.'/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
    $document->addScript('components/com_jssupportticket/include/js/circle.js');
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/ticket-myticket.css', 'text/css');

    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    $c_count = $this->config['controlpanel_column_count'];
    if($c_count < 1 || $c_count > 12){
        $c_count = 3;
    }else{
        $c_count = ceil(12/$c_count);
    }
    if($this->user->getIsStaff()){ ?>
        <div id="jsst-wrapper-top">
            <?php if($this->config['cur_location'] == 1){ ?>
                <div id="jsst-wrapper-top-left">
                    <div id="jsst-breadcrunbs">
                        <ul>
                            <li>
                                <?php echo JText::_('Dashboard'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <!-- Dashboard Links -->
        <div class="js-cp-left" >
            <div id="js-dash-menu-link-wrp">
            <div class="js-section-heading"><?php echo JText::_('Dashboard Links'); ?></div>
            <div class="js-menu-links-wrp">
            <div class="js-ticket-menu-links-row">
                <?php if($this->config['cplink_openticket_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/add-ticket-icon.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Submit Ticket'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_myticket_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/tickets.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('My Tickets'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_roles_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=roles&c=roles&layout=roles&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/role.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Roles'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_staff_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=staff&layout=staff&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/staff.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Staff Members'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_department_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=department&layout=departments&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/department.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Departments'); ?></span>
                    </a>
                <?php } ?>
            </div>
            <div class="js-ticket-menu-links-row">
                <?php if($this->config['cplink_category_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=categories&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/category.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Categories'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_kb_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=articles&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/kb.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Knowledge Base'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_download_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=downloads&layout=downloads&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/download.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Downloads'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_announcement_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=announcements&layout=announcements&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/announcements.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Announcements'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_faq_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=faqs&layout=faqs&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/faq.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('FAQs'); ?></span>
                    </a>
                <?php } ?>
            </div>
            <div class="js-ticket-menu-links-row">
                <?php if($this->config['cplink_mail_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=mail&layout=inbox&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/mails.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Mail'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_staff_report_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=reports&layout=staffreports&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/staff-report.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Staff reports'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_department_report_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=reports&layout=departmentreports&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/department-report.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Department Reports'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_feedback_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=feedback&layout=feedbacks&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/feedback.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('Feedbacks'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_profile_staff'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=staff&layout=staffprofile&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/priorities.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('My Profile'); ?></span>
                    </a>
                <?php } ?>
                <?php if($this->config['cplink_userdata_user'] == 1){ ?>
                    <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=gdpr&layout=adderasedatarequest&Itemid=<?php echo $this->Itemid; ?>">
                        <div class="js-ticket-dash-menu-icon">
                            <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/user-data.png" />
                        </div>
                        <span class="js-ticket-dash-menu-text"><?php echo JText::_('User Data'); ?></span>
                    </a>
                <?php } ?>

                <?php $redirect = JRoute::_("index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=" . $this->Itemid , false);
                $link = "index.php?option=com_jssupportticket&c=jssupportticket&task=logout&return=".$redirect."&Itemid=" . $this->Itemid;?>
                <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="<?php echo $link; ?>">
                    <div class="js-ticket-dash-menu-icon">
                        <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/login.png" />
                    </div>
                    <span class="js-ticket-dash-menu-text"><?php echo JText::_('Log Out'); ?></span>
                </a>
            </div>
            </div>
            </div>
        </div>
        <div class="js-cp-right">
            <?php
            {
                $openticket = 0;
                $closedticket = 0;
                $answeredticket = 0;
                if(isset($this->userticketstats)){
                    if($this->userticketstats['allticket'] > 0){
                        $openticket = round($this->userticketstats['openticket'] / $this->userticketstats['allticket'] * 100);
                        $closedticket = round($this->userticketstats['closedticket'] / $this->userticketstats['allticket'] * 100);
                        $answeredticket = round($this->userticketstats['answeredticket'] / $this->userticketstats['allticket'] * 100);
                    }
                }
                if(isset($this->userticketstats) && $this->userticketstats['allticket'] != 0){
                    $allticket = 100;
                }else{
                    $allticket = 0;
                }
                ?>
                <div class="js-ticket-count">
                    <div class="js-ticket-link js-ticket-open">
                        <a class="js-ticket-link" href="#" data-tab-number="1" title="<?php echo JText::_("Open Ticket"); ?>">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $openticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text">
                              <?php 
                                if(isset($this->userticketstats['allticket'])){ 
                                    echo JText::_("Open") . " ( " . $this->userticketstats['openticket'] . " )";
                                }else{
                                    echo JText::_("Open");
                                } 
                              ?>
                            </div>
                        </a>
                    </div>
                    <div class="js-ticket-link js-ticket-close">
                        <a class="js-ticket-link" href="#" data-tab-number="2" title="<?php echo ("closed ticket"); ?>">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $closedticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text">
                              <?php 
                                if(isset($this->userticketstats['allticket'])){ 
                                    echo JText::_("Closed") . " ( " . $this->userticketstats['closedticket'] . " )";
                                }else{
                                    echo JText::_("Closed");
                                } 
                              ?>
                            </div>
                        </a>
                    </div>
                    <div class="js-ticket-link js-ticket-answer">
                        <a class="js-ticket-link" href="#" data-tab-number="3" title="<?php  echo JText::_("answered ticket"); ?>">
                            <div class="js-ticket-cricle-wrp">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $answeredticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text">
                              <?php 
                                if(isset($this->userticketstats['allticket'])){ 
                                    echo JText::_("Answered") . " ( " . $this->userticketstats['answeredticket'] . " )";
                                }else{
                                    echo JText::_("Answered");
                                } 
                              ?>
                            </div>
                        </a>
                    </div>
                    <div class="js-ticket-link js-ticket-allticket">
                        <a class="js-ticket-link" href="#" data-tab-number="4" title="<?php echo JText::_("All ticket"); ?>">
                            <div class="js-ticket-cricle-wrp">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $allticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text">
                              <?php 
                                if(isset($this->userticketstats['allticket'])){ 
                                    echo JText::_("All Tickets") . " ( " . $this->userticketstats['allticket'] . " )";
                                }else{
                                    echo JText::_("All Tickets");
                                } 
                              ?>
                            </div>
                        </a>
                    </div>
                </div>
            <?php } ?>
            <?php if($this->config['cplink_ticketstats_staff'] == 1){ ?>
            <!-- Ticket statictis -->
            <div id="js-pm-graphtitle-wrp">
                <div id="js-pm-graphtitle">
                    <?php echo JText::_('Ticket Statistics'); ?>
                </div>
                <div id="js-pm-grapharea">
                    <div id="stack_chart_horizontal" style="width:100%;"></div>
                </div>
            </div>
        <?php } ?>
        </div>
        <?php if($this->config['cplink_latesttickets_staff'] == 1){ ?>
            <!-- Latest Tickets -->
            <div id="js-pm-graphtitle-wrp">
                <?php if (!empty($this->latest_tickets)) { ?>
                <div class="js-ticket-latest-ticket-wrapper">
                    <div class="js-ticket-haeder">
                        <div class="js-ticket-header-txt"><?php echo JText::_("Latest Tickets"); ?></div>
                        <a class="js-ticket-header-link" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>"><?php echo JText::_("View All Tickets"); ?></a>
                    </div>
                    <div class="js-ticket-latest-tickets-wrp">
                        <?php foreach ($this->latest_tickets AS $ticket): ?>
                            <div class="js-ticket-row">
                                <div class="js-ticket-first-left">
                                    <div class="js-ticket-user-img-wrp">
                                        <img alt="<?php echo JText::_("User image"); ?>" src="components/com_jssupportticket/include/images/user.png" class="avatar avatar-96 photo" height="96" width="96">
                                    </div>
                                    <div class="js-ticket-ticket-subject">
                                        <div class="js-ticket-data-row">
                                            <?php echo JText::_($ticket->name); ?>
                                        </div>
                                        <div class="js-ticket-data-row name">
                                            <?php $link = 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetail&id='.$ticket->ticketid.'&Itemid='.$this->Itemid; ?>
                                            <a class="js-ticket-data-link" href="<?php echo $link; ?>"><?php echo JText::_($ticket->subject); ?></a>
                                        </div>
                                        <div class="js-ticket-data-row">
                                            <span class="js-ticket-title"><?php echo JText::_("Department"); ?> : </span>
                                            <?php echo JText::_($ticket->departmentname); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="js-ticket-second-left">
                                    <?php if ($ticket->status == 0) { ?>
                                        <span class="js-ticket-status" style="color: #9ACC00;"><?php echo JText::_('New'); ?></span>
                                    <?php } elseif ($ticket->status == 1) { ?>
                                        <span class="js-ticket-status" style="color: orange;"><?php echo JText::_('Waiting reply'); ?></span>
                                    <?php } elseif ($ticket->status == 2) { ?>
                                        <span class="js-ticket-status" style="color: #FF7F50;"><?php echo JText::_('In progress'); ?></span>
                                    <?php } elseif ($ticket->status == 3) { ?>
                                        <span class="js-ticket-status" style="color: #507DE4;"><?php echo JText::_('Replied'); ?></span>
                                    <?php } elseif ($ticket->status == 4) { ?>
                                        <span class="js-ticket-status" style="color: #CB5355;"><?php echo JText::_('Close'); ?></span>
                                    <?php } elseif ($ticket->status == 5){ ?>
                                        <span class="js-ticket-status" style="color: #ee1e22;"><?php echo JText::_('Close due to Merge'); ?></span>
                                    <?php } ?>
                                    </span>
                                </div>
                                <div class="js-ticket-third-left"><?php echo JHtml::_('date',$ticket->created,"d F, Y"); ?></div>
                                <div class="js-ticket-fourth-left">
                                    <span class="js-tk-priorty" style="background:<?php echo $ticket->prioritycolour; ?>;color:#fff;"><?php echo JText::_($ticket->priority); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php } ?>
                <!-- Latest Tickets -->
            </div>
        <?php } ?>
    <?php }else{ ?>
        <div id="jsst-wrapper-top">
            <?php if($this->config['cur_location'] == 1){ ?>
                <div id="jsst-wrapper-top-left">
                    <div id="jsst-breadcrunbs">
                        <ul>
                            <li>
                                <?php echo JText::_('Dashboard'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="js-cp-left" >
            <div id="js-dash-menu-link-wrp">
                <!-- Dashboard Links -->
                <div class="js-section-heading"><?php echo JText::_('Dashboard Links'); ?></div>
                <div class="js-menu-links-wrp">
                    <div class="js-ticket-menu-links-row">
                        <?php if($this->config['cplink_openticket_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/add-ticket-icon.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('Submit Ticket'); ?></span>
                            </a>
                        <?php } ?>
                        <?php if($this->config['cplink_myticket_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/tickets.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('My Tickets'); ?></span>
                            </a>
                        <?php } ?>
                        <?php if($this->config['cplink_checkstatus_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/report.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('Ticket Status'); ?></span>
                            </a>
                        <?php } ?>
                        <?php if($this->config['cplink_download_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=downloads&layout=userdownloads&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/download.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('Downloads'); ?></span>
                            </a>
                        <?php } ?>
                        <?php if($this->config['cplink_announcement_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=announcements&layout=userannouncements&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/announcements.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('Announcements'); ?></span>
                            </a>
                        <?php } ?>
                    </div>
                    <div class="js-ticket-menu-links-row">
                        <?php if($this->config['cplink_faq_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=faqs&layout=userfaqs&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/faq.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('FAQs'); ?></span>
                            </a>
                        <?php } ?>
                        <?php if($this->config['cplink_kb_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=userarticles&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/kb.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('Knowledge Base'); ?></span>
                            </a>
                        <?php } ?>
                        <?php if($this->config['cplink_userdata_user'] == 1){ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="index.php?option=com_jssupportticket&c=gdpr&layout=adderasedatarequest&Itemid=<?php echo $this->Itemid; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/user-data.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('User Data'); ?></span>
                            </a>
                        <?php } ?>
                        <?php $redirect = JRoute::_("index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=" . $this->Itemid , false);
                        $redirect = '&amp;return=' . base64_encode($redirect);
                        if(isset($this->userticketstats) && $this->userticketstats){ ?>
                            <?php $link = "index.php?option=com_jssupportticket&c=jssupportticket&task=logout&return=".$redirect."&Itemid=" . $this->Itemid;?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="<?php echo $link; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/logout.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('Log Out'); ?></span>
                            </a>
                        <?php }else{ ?>
                            <a class="js-col-xs-12 js-col-sm-6 js-col-md-4 js-ticket-dash-menu" href="<?php echo 'index.php?option=com_users&view=login' . $redirect; ?>">
                                <div class="js-ticket-dash-menu-icon">
                                    <img class="js-ticket-dash-menu-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/login.png" />
                                </div>
                                <span class="js-ticket-dash-menu-text"><?php echo JText::_('Log In'); ?></span>
                            </a>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="js-cp-right">
            <!-- if user loged in -->
            <?php if(isset($this->userticketstats) && $this->userticketstats){
                $openticket = 0;
                $closedticket = 0;
                $answeredticket = 0;
                if($this->userticketstats['allticket'] > 0){
                    $openticket = round($this->userticketstats['openticket'] / $this->userticketstats['allticket'] * 100);
                    $closedticket = round($this->userticketstats['closedticket'] / $this->userticketstats['allticket'] * 100);
                    $answeredticket = round($this->userticketstats['answeredticket'] / $this->userticketstats['allticket'] * 100);
                }
                if($this->userticketstats['allticket'] != 0){
                    $allticket = 100;
                }else{
                    $allticket = 0;
                }
                ?>
                <div class="js-ticket-count">
                    <div class="js-ticket-link js-ticket-open">
                        <a class="js-ticket-link" href="#" data-tab-number="1" title="<?php echo JText::_("Open Ticket"); ?>">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $openticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text">
                                <?php echo JText::_("Open") . " ( " . $this->userticketstats['openticket'] . " )"; ?>
                            </div>
                        </a>
                    </div>
                    <div class="js-ticket-link js-ticket-close">
                        <a class="js-ticket-link" href="#" data-tab-number="2" title="<?php echo ("closed ticket"); ?>">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $closedticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text">
                                <?php echo JText::_("Closed") . " ( " . $this->userticketstats['closedticket'] . " )"; ?>
                            </div>
                        </a>
                    </div>
                    <div class="js-ticket-link js-ticket-answer">
                        <a class="js-ticket-link" href="#" data-tab-number="3" title="<?php  echo JText::_("answered ticket"); ?>">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $answeredticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text js-ticket-brown">
                                <?php echo JText::_("Answered") . " ( " . $this->userticketstats['answeredticket'] . " )"; ?>
                            </div>
                        </a>
                    </div>
                    <div class="js-ticket-link js-ticket-allticket">
                        <a class="js-ticket-link" href="#" data-tab-number="4" title="<?php echo JText::_("All ticket"); ?>">
                            <div class="js-ticket-cricle-wrp ">
                                <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $allticket; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                    <div class="loader-bg"></div>
                                </div>
                            </div>
                            <div class="js-ticket-link-text">
                                <?php echo JText::_("All Tickets") . " ( " . $this->userticketstats['allticket'] . " )"; ?>
                            </div>
                        </a>
                    </div>
                </div>
            <?php }else{ ?>
                <!-- if user not loged in -->
                <div class="js-support-ticket-cont">
                    <div class="js-support-ticket-box">
                        <img src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/add-ticket.png" alt="Create Ticket">
                        <div class="js-support-ticket-title">
                            <?php echo JText::_("Submit Ticket"); ?>
                        </div>
                        <div class="js-support-ticket-desc">
                            <?php echo JText::_("Create Ticket"); ?>
                        </div>
                        <a href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>" class="js-support-ticket-btn">
                            <?php echo JText::_("Submit Ticket"); ?>
                        </a>
                    </div>
                    <div class="js-support-ticket-box">
                        <img src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/my-tickets.png" alt="my ticket">
                        <div class="js-support-ticket-title">
                            <?php echo JText::_("My Tickets"); ?>
                        </div>
                        <div class="js-support-ticket-desc">
                            <?php echo JText::_("View all the created tickets"); ?>
                        </div>
                        <a href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>" class="js-support-ticket-btn">
                            <?php echo JText::_("My Tickets"); ?>
                        </a>
                    </div>
                    <div class="js-support-ticket-box">
                        <img src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/dashboard-icon/ticket-status.png" alt="Ticket Status" />
                        <div class="js-support-ticket-title">
                            <?php echo JText::_("Ticket Status"); ?>
                        </div>
                        <div class="js-support-ticket-desc">
                            <?php echo JText::_("Check Status"); ?>
                        </div>
                        <a href="index.php?option=com_jssupportticket&c=ticket&layout=ticketstatus&Itemid=<?php echo $this->Itemid; ?>" class="js-support-ticket-btn">
                            <?php echo JText::_("View").' '.JText::_("Status"); ?>
                        </a>
                    </div>
                </div>
            <?php } ?>
            <!-- latest user tickets -->
            <?php if (!empty($this->latest_tickets)) { ?>
                <div class="js-ticket-latest-ticket-wrapper">
                    <div class="js-ticket-haeder">
                        <div class="js-ticket-header-txt"><?php echo JText::_("Latest Tickets"); ?></div>
                        <a class="js-ticket-header-link" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>"><?php echo JText::_("View All Tickets"); ?></a>
                    </div>
                    <div class="js-ticket-latest-tickets-wrp">
                        <?php foreach ($this->latest_tickets AS $ticket):?>
                            <div class="js-ticket-row">
                                <div class="js-ticket-first-left">
                                    <div class="js-ticket-user-img-wrp">
                                        <img alt="<?php echo JText::_("User image"); ?>" src="components/com_jssupportticket/include/images/user.png" class="avatar avatar-96 photo" height="96" width="96">
                                    </div>
                                    <div class="js-ticket-ticket-subject">
                                        <div class="js-ticket-data-row">
                                            <?php echo JText::_($ticket->name); ?>
                                        </div>
                                        <div class="js-ticket-data-row name">
                                            <?php $link = 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetail&id='.$ticket->ticketid.'&Itemid='.$this->Itemid; ?>
                                            <a class="js-ticket-data-link" href="<?php echo $link; ?>"><?php echo JText::_($ticket->subject); ?></a>
                                        </div>
                                        <div class="js-ticket-data-row">
                                            <span class="js-ticket-title"><?php echo JText::_("Department"); ?> : </span>
                                            <?php echo JText::_($ticket->departmentname); ?>
                                        </div>
                                    </div>
                                </div>
                                <div class="js-ticket-second-left">
                                    <?php if ($ticket->status == 0) { ?>
                                        <span class="js-ticket-status" style="color: #9ACC00;"><?php echo JText::_('New'); ?></span>
                                    <?php } elseif ($ticket->status == 1) { ?>
                                        <span class="js-ticket-status" style="color: orange;"><?php echo JText::_('Waiting reply'); ?></span>
                                    <?php } elseif ($ticket->status == 2) { ?>
                                        <span class="js-ticket-status" style="color: #FF7F50;"><?php echo JText::_('In progress'); ?></span>
                                    <?php } elseif ($ticket->status == 3) { ?>
                                        <span class="js-ticket-status" style="color: #507DE4;"><?php echo JText::_('Replied'); ?></span>
                                    <?php } elseif ($ticket->status == 4) { ?>
                                        <span class="js-ticket-status" style="color: #CB5355;"><?php echo JText::_('Close'); ?></span>
                                    <?php } elseif ($ticket->status == 5){ ?>
                                        <span class="js-ticket-status" style="color: #ee1e22;"><?php echo JText::_('Close due to Merge'); ?></span>
                                    <?php } ?>
                                    </span>
                                </div>
                                <div class="js-ticket-third-left"><?php echo JHtml::_('date',$ticket->created,"d F, Y"); ?></div>
                                <div class="js-ticket-fourth-left">
                                    <span class="js-tk-priorty" style="background:<?php echo $ticket->prioritycolour; ?>;color:#fff;"><?php echo JText::_($ticket->priority); ?></span>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            <?php } ?>
            <!-- latest announcement -->
            <?php if(!empty($this->latest_announcements)){ ?>
                <div class="js-ticket-data-list-wrp latst-ancmts">
                    <div class="js-ticket-haeder">
                        <div class="js-ticket-header-txt">
                            <?php echo JText::_("Latest Announcements"); ?>
                        </div>
                        <a class="js-ticket-header-link" href="index.php?option=com_jssupportticket&c=announcements&layout=userannouncements&Itemid=<?php echo $this->Itemid; ?>">
                            <?php echo JText::_("View All Announcements"); ?>
                        </a>
                    </div>
                    <div class="js-ticket-data-list">
                        <?php $i = 1;
                        foreach($this->latest_announcements AS $announcement): ?>
                            <div class="js-ticket-data">
                                <div class="js-ticket-data-image">
                                    <img alt="img" class="js-ticket-data-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/announcement_icons/<?php echo $i; ?>.png">
                                </div>
                                <?php $link = 'index.php?option='.$this->option .'&c=announcements&layout=userannouncementdetail&id='.$announcement->id.'&Itemid='.$this->Itemid; ?>
                                <a class="js-ticket-data-tit" href="<?php echo $link; ?>">
                                    <?php echo JText::_($announcement->title); ?>
                                </a>
                            </div>
                        <?php $i++;
                        endforeach; ?>
                    </div>
                </div>
            <?php } ?>
            <!-- latest knowledgebase -->
            <?php if(!empty($this->latest_knowledgebase)){ ?>
                <div class="js-ticket-data-list-wrp latst-kb">
                    <div class="js-ticket-haeder">
                        <div class="js-ticket-header-txt">
                            <?php echo JText::_("Latest Knowledge Base"); ?>
                        </div>
                        <a class="js-ticket-header-link" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=userarticles&Itemid=<?php echo $this->Itemid; ?>">
                            <?php echo JText::_("View All Knowledge Base"); ?>
                        </a>
                    </div>
                    <div class="js-ticket-data-list">
                        <?php $i = 1;
                        foreach($this->latest_knowledgebase AS $knowledge): ?>
                            <div class="js-ticket-data">
                                <div class="js-ticket-data-image">
                                    <img alt="image" class="js-ticket-data-img" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/knowledgebase_icons/<?php echo $i; ?>.png">
                                </div>
                                <?php $link = 'index.php?option='.$this->option .'&c=knowledgebase&layout=usercatarticledetails&id='.$knowledge->id.'&Itemid='.$this->Itemid; ?>
                                <a class="js-ticket-data-tit" href="<?php echo $link; ?>">
                                    <?php echo JText::_($knowledge->subject); ?>
                                </a>
                            </div>
                        <?php $i++;
                        endforeach; ?>
                    </div>
                </div>
            <?php } ?>

            <!-- download popup -->
            <div id="js-ticket-main-black-background" style="display:none;">
    </div>
    <div id="js-ticket-main-popup" style="display:none;">
        <span id="js-ticket-popup-title">abc title</span>
        <span id="js-ticket-popup-close-button"><img src="components/com_jssupportticket/include/images/popup-close.png" /></span>
        <div id="js-ticket-main-content">
        </div>
        <div id="js-ticket-main-downloadallbtn">
        </div>
    </div>

        </div>
    <?php }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
