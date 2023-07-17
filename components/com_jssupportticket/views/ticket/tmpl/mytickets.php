<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:        www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
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
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
    $document->addScript('components/com_jssupportticket/include/js/circle.js');
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/ticket-myticket.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
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
                            <?php echo JText::_('My Tickets'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php //require_once JPATH_COMPONENT_SITE . '/views/ticket_header_bottom.php';
    if(!$this->user->getIsGuest()){?>
        <script type="text/javascript">
            jQuery(document).ready(function ($) {
                //jQuery('.custom_date').datepicker({dateFormat: 'yy-mm-dd'});
                var combinesearch = "<?php echo isset($this->filter_data['iscombinesearch']) ? $this->filter_data['iscombinesearch'] : ''; ?>";
                jQuery("#js-filter-wrapper-toggle-area").hide();
                jQuery("a.js-search-filter-btn").text('<?php echo JText::_('Show All'); ?>');
                if (combinesearch) {
                    doVisible();
                    jQuery("#js-filter-wrapper-toggle-area").show();
                }

                jQuery("#js-filter-wrapper-toggle-btn").click(function (e) {
                    e.preventDefault();
                    if (jQuery("#js-filter-wrapper-toggle-area").is(":visible")) {
                        doVisible();
                    } else {
                        jQuery("a.js-search-filter-btn").text('<?php echo JText::_('Show Less'); ?>');
                    }
                    jQuery("#js-filter-wrapper-toggle-area").toggle();
                });
                function doVisible(){
                    jQuery("a.js-search-filter-btn").text('<?php echo JText::_('Show All'); ?>');
                }

                var sortby = jQuery("select.js-ticket-sorting-select").val();
                if(sortby != ""){
                    jQuery("input#sortby").val(sortby);
                }

                jQuery("select.js-ticket-sorting-select").on('change',function(){
                    var sortby = jQuery(this).val();
                    jQuery("input#sortby").val(sortby);
                    jQuery("form#jssupportticketform").submit();
                });

                jQuery("#jssortbtn").on('click',function(){
                    var sortby = jQuery("select.js-ticket-sorting-select").val();
                    switch(sortby){
                        case "subjectdesc": sortby = "subjectasc"; break;
                        case "subjectasc": sortby = "subjectdesc"; break;
                        case "prioritydesc": sortby = "priorityasc"; break;
                        case "priorityasc": sortby = "prioritydesc"; break;
                        case "ticketiddesc": sortby = "ticketidasc"; break;
                        case "ticketidasc": sortby = "ticketiddesc"; break;
                        case "answereddesc": sortby = "answeredasc"; break;
                        case "answeredasc": sortby = "answereddesc"; break;
                        case "createddesc": sortby = "createdasc"; break;
                        case "createdasc": sortby = "createddesc"; break;
                        case "statusdesc": sortby = "statusasc"; break;
                        case "statusasc": sortby = "statusdesc"; break;
                    }
                    jQuery("input#sortby").val(sortby);
                    jQuery("form#jssupportticketform").submit();
                });
            });
            function getDataForDepandantField(parentf, childf, type) {
                if (type == 1) {
                    var val = jQuery("select#" + parentf).val();
                } else if (type == 2) {
                    var val = jQuery("input[name=" + parentf + "]:checked").val();
                    if(val === undefined){
                        var val = jQuery("input[name=\"" + parentf + "[]\"]:checked").val();
                    }
                }
                jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=datafordepandantfield&<?php echo JSession::getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
                    if (data) {
                        console.log(data);
                        var d = jQuery.parseJSON(data);
                        jQuery("select#" + childf).replaceWith(d);
                    }
                });
            }
        </script>
        <!-- Top Circle Count Boxes -->
        <div class="js-row js-ticket-top-cirlce-count-wrp js-ticket-count">
            <?php 
            $open = ($this->lt == 1) ? 'active' : '';
            $answered = ($this->lt == 2) ? 'active' : '';
            $overdue = ($this->lt == 3) ? 'active' : '';
            $myticket = ($this->lt == 4) ? 'active' : '';
            if(isset($this->ticketinfo['allticket']) && $this->ticketinfo['allticket'] != 0){
                $open_percentage        =  round(($this->ticketinfo['open'] / $this->ticketinfo['allticket']) * 100);
                $close_percentage       =  round(($this->ticketinfo['close'] / $this->ticketinfo['allticket']) * 100);
                $answered_percentage    =  round(($this->ticketinfo['answered'] / $this->ticketinfo['allticket']) * 100);
            }
    
                if(isset($this->ticketinfo['allticket']) && $this->ticketinfo['allticket'] != 0){
                    $allticket_percentage = 100;
                }else{
                    $allticket_percentage = 0;
                } ?>
            <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-link js-ticket-myticket-link-myticket js-ticket-open">
                <a class="js-ticket-green js-myticket-link js-ticket-link <?php if ($this->lt == '1') echo 'active'; ?>"  href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets<?php if($this->email) echo '&email='.$this->email; ?><?php echo htmlspecialchars('&lt'); ?>=1<?php echo "&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder'])."&Itemid=".$this->Itemid; ?>">
                    <div class="js-ticket-cricle-wrp ">
                        <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $open_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                            <div class="loader-bg"></div>
                        </div>
                    </div>
                    <div class="js-ticket-link-text">
                        <?php
                            echo JText::_('Open');
                            if($this->config['show_count_tickets'] == 1)
                            echo " ( " . $this->ticketinfo['open'] . " ) ";
                        ?>
                    </div>
                </a>
            </div>
            <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-link js-ticket-myticket-link-myticket js-ticket-close">
                <a class="js-ticket-red js-myticket-link js-ticket-link <?php if ($this->lt == '4') echo 'active'; ?>"  href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets<?php if($this->email) echo '&email='.$this->email; ?><?php echo htmlspecialchars('&lt'); ?>=4<?php echo "&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder'])."&Itemid=".$this->Itemid; ?>">
                    <div class="js-ticket-cricle-wrp ">
                        <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $close_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                            <div class="loader-bg"></div>
                        </div>
                    </div>
                    <div class="js-ticket-link-text">
                        <?php
                            echo JText::_('Closed');
                            if($this->config['show_count_tickets'] == 1)
                            echo " ( " . $this->ticketinfo['close'] . " ) ";
                        ?>
                    </div>
                </a>
            </div>
            <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-link js-ticket-myticket-link-myticket js-ticket-answer">
                <a class="js-ticket-pink js-myticket-link js-ticket-link <?php if ($this->lt == '2') echo 'active'; ?>"  href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets<?php if($this->email) echo '&email='.$this->email; ?><?php echo htmlspecialchars('&lt'); ?>=2<?php echo "&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder'])."&Itemid=".$this->Itemid; ?>">
                    <div class="js-ticket-cricle-wrp ">
                        <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $answered_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                            <div class="loader-bg"></div>
                        </div>
                    </div>
                    <div class="js-ticket-link-text">
                        <?php
                            echo JText::_('Answered');
                            if($this->config['show_count_tickets'] == 1)
                            echo " ( " . $this->ticketinfo['answered'] . " ) ";
                        ?>
                    </div>
                </a>
            </div>
            <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-link js-ticket-myticket-link-myticket js-ticket-allticket">
                <a class="js-ticket-blue js-myticket-link js-ticket-link <?php if ($this->lt == '5') echo 'active'; ?>"  href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets<?php if($this->email) echo '&email='.$this->email; ?><?php echo htmlspecialchars('&lt'); ?>=5<?php echo "&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder'])."&Itemid=".$this->Itemid; ?>">
                    <div class="js-ticket-cricle-wrp ">
                        <div class="circlebar" data-circle-startTime="0" data-circle-maxValue="<?php echo $allticket_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                            <div class="loader-bg"></div>
                        </div>
                    </div>
                    <div class="js-ticket-link-text js-ticket-allticket">
                        <?php
                            echo JText::_('All Tickets');
                            if($this->config['show_count_tickets'] == 1)
                            echo " ( " . $this->ticketinfo['allticket'] . " ) ";
                        ?>
                    </div>
                </a>
            </div>
        </div>
        <!-- Search Portion -->
        <div class="js-ticket-search-wrp">
            <div class="js-heading-wrp"></div>
            <div class="js-ticket-form-wrp">
                <form class="js-tk-combinesearch" method="post" name="adminForm" id="jssupportticketform">
                    <div class="js-filter-wrapper">
                        <div class="js-col-md-3 js-filter-field-wrp
                        js-ticket-margin-bottom-null">
                                <input type="text" name="filter_ticketid" id="filter_ticketid" value="<?php if (isset($this->filter_data['ticketid'])) echo $this->filter_data['ticketid']; ?>" class="js-ticket-input-field" placeholder="<?php echo JText::_('Ticket ID'); ?>" />

                            </div>
                            <div class="js-col-md-3 js-filter-field-wrp js-ticket-margin-bottom-null">
                                <input type="text" name="filter_from" id="filter_from" class="js-ticket-input-field" value="<?php if (isset($this->filter_data['from'])) echo $this->filter_data['from']; ?>" placeholder="<?php echo JText::_('From'); ?>" />
                            </div>
                            <div class="js-col-md-3 js-filter-field-wrp js-ticket-margin-bottom-null">
                                <input type="text" name="filter_email" id="filter_email" class="js-ticket-input-field" value="<?php if (isset($this->filter_data['email'])) echo $this->filter_data['email']; ?>" placeholder="<?php echo JText::_('Email'); ?>" />
                            </div>

                        <div id="js-filter-wrapper-toggle-area">
                            <div class="js-col-md-3 js-filter-field-wrp">
                                <?php echo $this->lists['departments']; ?>
                            </div>
                            <div class="js-col-md-3 js-filter-field-wrp">
                                <?php echo $this->lists['priorities']; ?>
                            </div>
                            <div class="js-col-md-3 js-filter-field-wrp">
                                <input type="text" name="filter_subject" id="filter_subject" class="js-ticket-input-field" value="<?php if (isset($this->filter_data['subject'])) echo $this->filter_data['subject']; ?>" placeholder="<?php echo JText::_('Subject'); ?>" />
                            </div>
                            <div class="js-col-md-3 js-filter-field-wrp">
                                <?php echo JHTML::_('calendar', isset($this->filter_data['datestart']) ? $this->filter_data['datestart'] : '', 'filter_datestart', 'filter_datestart', $js_dateformat, array('class' => 'js-ticket-input-field', 'size' => '10', 'maxlength' => '19' , 'placeholder' => JText::_('Start Date'))); ?>
                            </div>
                            <div class="js-col-md-3 js-filter-field-wrp">
                                <?php echo JHTML::_('calendar', isset($this->filter_data['dateend']) ? $this->filter_data['dateend'] : '', 'filter_dateend', 'filter_dateend', $js_dateformat, array('class' => 'js-ticket-input-field', 'size' => '10', 'maxlength' => '19' , 'placeholder' => JText::_('End Date'))); ?>
                            </div>
                            <?php $params = null;
                                if(isset($this->filter_data['params'])){
                                    $params = $this->filter_data['params'];
                                }
                                $k = 1;
                                $customfields = getCustomFieldClass()->userFieldsForSearch(1);
                                if(!empty($customfields)){
                                    foreach ($customfields as $field) {
                                        getCustomFieldClass()->formCustomFieldsForSearch($field, $k, $params);
                                    }
                                    if(sizeof($customfields) == 1 && $customfields[0]->userfieldtype == 'termsandconditions' ){ 
                                        
                                    }
                                    else{
                                        echo '</div>'; // last div close on the user fields
                                    }
                                }?>
                                
                        </div>
                        <div class="js-col-md-3 js-filter-button-wrp">
                            <span id="js-filter-wrapper-toggle-btn">
                                <?php /* <span id="js-filter-wrapper-toggle-plus"> */?>
                                    <a href="#" class="js-search-filter-btn" id="js-search-filter-toggle-btn"><?php echo JText::_('Show All'); ?></a>
                            </span>
                            <span class="js-filter-button-wrp">
                                <button class="js-ticket-filter-button js-ticket-search-btn" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                                <button id="jsresetbutton" name="jsresetbutton" class="js-ticket-filter-button js-ticket-reset-btn" onclick="resetJsForm();"><?php echo JText::_('Reset'); ?></button>

                            </span>
                        </div>
                        <input type="hidden" name="sortby" id="sortby">
                        <input type="hidden" name="sortorder" id="sortorder">
                    </div>
                </form> 
            </div>
        </div>
        <!-- Sorting Portion -->
        <?php
            $link = 'index.php?option=com_jssupportticket&c=ticket&layout=mytickets&email=' . $this->email . htmlspecialchars("&lt").'=' . $this->lt . '&Itemid=' . $this->Itemid;
            if ($this->sortlinks['sortorder'] == 'ASC')
                $img = "components/com_jssupportticket/include/images/sort1.png";
            else
                $img = "components/com_jssupportticket/include/images/sort2.png";
        ?>
        <div class="js-ticket-sorting js-col-md-12">
            <div class="js-ticket-sorting-left">
                <div class="js-ticket-sorting-heading">
                    <?php echo JText::_('All Tickets'); ?>
                </div>
            </div>
            <div class="js-ticket-sorting-right">
                <div class="js-ticket-sort">
                    <select class="js-ticket-sorting-select">
                        <option value="<?php if($this->sortlinks['sortorder'] == 'ASC') echo 'subjectasc'; else echo 'subjectdesc'; ?>" <?php if($this->sortlinks['sorton'] == 'subject') echo 'selected'; ?>><?php echo JText::_('Subject'); ?></option>
                        <option value="<?php if($this->sortlinks['sortorder'] == 'ASC') echo 'priorityasc'; else echo 'prioritydesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'priority') echo 'selected'; ?>><?php echo JText::_('Priority'); ?></option>

                        <option value="<?php if($this->sortlinks['sortorder'] == 'ASC') echo 'ticketidasc'; else echo 'ticketiddesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'ticketid') echo 'selected'; ?>><?php echo JText::_('Ticket ID'); ?></option>
                        <option value="<?php if($this->sortlinks['sortorder'] == 'ASC') echo 'answeredasc'; else echo 'answereddesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'answered') echo 'selected'; ?>><?php echo JText::_('Answered'); ?></option>
                        <option value="<?php if($this->sortlinks['sortorder'] == 'ASC') echo 'statusasc'; else echo 'statusdesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'status') echo 'selected'; ?>><?php echo JText::_('Status'); ?></option>
                        <option value="<?php if($this->sortlinks['sortorder'] == 'ASC') echo 'createdasc'; else echo 'createddesc'; ?>" <?php if ($this->sortlinks['sorton'] == 'created') echo 'selected'; ?>><?php echo JText::_('Created'); ?></option>
                    </select>
                    <a href="javascript:void(0)" id="jssortbtn" class="js-admin-sort-btn" title="sort">
                        <img src="<?php echo $img; ?>">
                    </a>
                </div>
            </div>
        </div>
        
        <!-- My Ticket List -->
        <?php
        if (!(empty($this->result)) && is_array($this->result)) {
            foreach ($this->result as $row) {
                $link = 'index.php?option=com_jssupportticket&c=ticket&layout=ticketdetail&id=' . $row->id . '&Itemid=' . $this->Itemid; ?>
                <div class="js-col-xs-12 js-col-md-12 js-ticket-wrapper">
                    <div class="js-col-xs-12 js-col-md-12 js-ticket-toparea">
                    <div class="js-ticket-pic">
						<?php 
						if ($row->staffphoto) { ?>
							<img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
						<?php } else { ?>
							<img class="js-ticket-icon-img" src="components/com_jssupportticket/include/images/user.png">
						<?php } ?>
						
                    </div>
                    <div class="js-ticket-data js-nullpadding">

                        <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses name"><span class="js-ticket-value"><?php echo $row->name; ?></span></div>
                        <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses subject">
                            <a class="js-ticket-title-anchor" href="<?php echo $link; ?>"> <?php echo $row->subject; ?></a>
                        </div>
                        <div class="js-col-xs-12 js-col-md-12 js-ticket-padding-xs js-ticket-body-data-elipses">

                            <span class="js-ticket-field-title"><?php echo JText::_('Department'); ?><font> : </font></span>
                            <span class="js-ticket-value"><?php echo $row->departmentname; ?></span>
                        </div>
                        <?php
                            $customfields = getCustomFieldClass()->userFieldsData(1, 1);
                            if(!empty($customfields)){
                                foreach ($customfields as $field) {
                                    echo getCustomFieldClass()->showCustomFields($field,1, $row->params , $row->id);
                                }
                            }
                        ?>

                    </div>
                    <div class="js-ticket-data1 js-ticket-padding-left-xs">
                        <!-- code change start -->
                            <span class="js-ticket-status-img">
                                <?php
                                $counter = 'one';
                                 if ($row->ticketviaemail == 1) { ?>
                                    <span class="ticketstatusimage <?php echo $counter;$counter = 'two'; ?>" style="background-color: #0066CC;"><?php echo JText::_('Ticket via email'); ?></span>
                                <?php } ?>
                                <?php
                                if ($row->lock == 1) { ?>
                                    <img class="ticketstatusimage <?php echo $counter; ?>" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/lock.png" title="<?php echo JText::_('Ticket Is Locked'); ?>" />
                                    <?php if($counter == 'one')
                                        {
                                            $counter = 'two';
                                        }
                                        else if($counter == 'two')
                                        {
                                            $counter = 'three';
                                        }
                                    ?>
                                <?php } ?>
                                <?php if ($row->isoverdue == 1) { ?>
                                    <img class="ticketstatusimage <?php echo $counter; ?>" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/over-due.png" title="<?php echo JText::_('Ticket mark overdue'); ?>" />
                                <?php } ?>
                            </span>
                            <span class="js-ticket-status" style="color:#5bb12f;">
                               <?php /*
                                <?php if ($row->ticketviaemail == 1) { ?>
                                    <span style="background-color: #0066CC;"><?php echo JText::_('Ticket via email'); ?></span>
                                <?php } ?>
                                <?php
                                $counter = 'one';
                                if ($row->lock == 1) { ?>
                                    <img class="ticketstatusimage <?php echo $counter;$counter = 'two'; ?>" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/lockstatus.png" title="<?php echo JText::_('Ticket Is Locked'); ?>" />
                                <?php } ?>
                                <?php if ($row->isoverdue == 1) { ?>
                                    <img class="ticketstatusimage <?php echo $counter; ?>" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/mark_over_due.png" title="<?php echo JText::_('Ticket mark overdue'); ?>" />
                                <?php } ?> */?>
                                <?php if ($row->status == 0) { ?>
                                    <span style="color: #9ACC00;"><?php echo JText::_('New'); ?></span>
                                <?php } elseif ($row->status == 1) { ?>
                                    <span style="color: orange;"><?php echo JText::_('Waiting reply'); ?></span>
                                <?php } elseif ($row->status == 2) { ?>
                                    <span style="color: #FF7F50;"><?php echo JText::_('In progress'); ?></span>
                                <?php } elseif ($row->status == 3) { ?>
                                    <span style="color: #507DE4;"><?php echo JText::_('Replied'); ?></span>
                                <?php } elseif ($row->status == 4) { ?>
                                    <span style="color: #CB5355;"><?php echo JText::_('Close'); ?></span>
                                <?php } elseif ($row->status == 5){ ?>
                                    <span style="color: #ee1e22;"><?php echo JText::_('Close due to Merge'); ?></span>
                                <?php } ?>
                            </span>
                            <span class="js-ticket-wrapper-textcolor" style="background:<?php echo $row->prioritycolour; ?>;color:#fff;"><?php echo JText::_($row->priority); ?></span>
                        <!-- code change end -->
                        <div class="js-ticket-data2">
                        <div class="js-ticket-data-row"><div class="js-ticket-data-tit"><?php echo JText::_('Ticket ID').JText::_(' : '); ?></div><div class="js-ticket-data-val"> <?php echo $row->ticketid; ?></div></div>
                        <div class="js-ticket-data-row"><div class="js-ticket-data-tit"><?php echo JText::_('Last Reply').JText::_(' : '); ?></div><div class="js-ticket-data-val"><?php if ($row->lastreply == '' || $row->lastreply == '0000-00-00 00:00:00') echo JText::_('No last reply'); else echo JHtml::_('date',$row->lastreply,$this->config['date_format']); ?></div></div>
                        <?php /*
                        <div class="js-ticket-data-row"><div class="js-ticket-data-tit"><?php echo JText::_('Last Reply By : '); ?></div><div class="js-ticket-data-val"><?php echo $row->lastreplyby; ?></div></div>*/ ?>
                        <?php $forlisting = $this->getJSModel('userfields')->getFieldsForListing(1);
                            if($forlisting['assignto'] == 1){   ?>
                                <div class="js-col-md-12 js-wrapper">
                                    <span class="js-col-xs-6 js-col-md-6 js-tk-title"><?php echo JText::_('Assign To').JText::_(' : '); ?></span><span class="js-col-xs-6 js-col-md-6 js-tk-value"><?php echo JText::_($row->staffname); ?></span></div>
                        <?php } ?>
                        </div>
                    </div>
                  </div>
                </div> 
            <?php
            } //End foreach rows as row ?>

            <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=ticket&layout=mytickets'.htmlspecialchars('&lt').'='.$this->lt.'&Itemid=' . $this->Itemid); ?>" method="post">
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
            </form>  <?php
        }else{
            messageslayout::getRecordNotFound(); //Empty Record
        }
    }else{
        messageslayout::getUserGuest($this->layoutname,$this->Itemid); //user guest
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
</div>
<script type="text/javascript">
    function resetJsForm(){
        var form = jQuery('form#jssupportticketform');
        form.find("input[type=text], input[type=email], input[type=password], textarea").val("");
        form.find('input:checkbox').removeAttr('checked');
        form.find('select').prop('selectedIndex', 0);
        form.find('input[type="radio"]').prop('checked', false);
        jQuery("<input type='hidden' value='1' />")
         .attr("id", "jsresetbutton")
         .attr("name", "jsresetbutton")
         .appendTo(form);
    }
</script>
