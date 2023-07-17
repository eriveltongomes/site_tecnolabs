<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project: 	JS Tickets
  ^
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
$document = JFactory::getDocument();
$document->addStyleSheet('components/com_jssupportticket/include/css/circle.css');
$document->addScript('components/com_jssupportticket/include/js/circle.js');
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
$useruid = JFactory::getApplication()->input->get('uid');
$useruid = ($useruid) != "" ? $useruid : 0;
?>

<script language=Javascript>
    function confirmdelete(deletefor) {
        msg = '';
        if(deletefor == 0){
            msg = "<?php echo JText::_('Are you sure to delete'); ?>";
        }else if(deletefor == 1){
            msg = "<?php echo JText::_('Are you sure to enforce delete'); ?>";
        }

        if (confirm(msg) == true) {
            return true;
        } else
            return false;
    }
		function getDataForDepandantField(parentf, childf, type) {
			if (type == 1) {
				var val = jQuery("select#" + parentf).val();
			} else if (type == 2) {
				var val = jQuery("input[name=" + parentf + "]:checked").val();
			}
			jQuery.post('index.php?option=com_jssupportticket&c=userfields&task=datafordepandantfield&<?php echo JSession::getFormToken(); ?>=1', {fvalue: val, child: childf}, function (data) {
				if (data) {
					console.log(data);
					var d = jQuery.parseJSON(data);
					jQuery("select#" + childf).replaceWith(d);
				}
			});
		}
</script>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        //jQuery('.custom_date').datepicker({dateFormat: 'yy-mm-dd'});
        var combinesearch = "<?php echo isset($this->filter_data['iscombinesearch']) ? $this->filter_data['iscombinesearch'] : ''; ?>";
        jQuery("#js-filter-wrapper-toggle-area").hide();
        jQuery("#js-filter-wrapper-toggle-minus").hide();
        if (combinesearch) {
            doVisible();
            jQuery("#js-filter-wrapper-toggle-area").show();
        }
        jQuery("#js-filter-wrapper-toggle-btn").click(function (e) {
            e.preventDefault();
            if (jQuery("#js-filter-wrapper-toggle-plus").is(":visible")) {
                doVisible();
            } else {
                jQuery("#js-filter-wrapper-toggle-ticketid").hide();
                jQuery("#js-filter-wrapper-toggle-minus").hide();
                jQuery("#js-filter-wrapper-toggle-plus").show();
            }
            jQuery("#js-filter-wrapper-toggle-area").toggle();
        });

        var sortby = jQuery("select.js-ticket-sorting-select").val();
        if(sortby != ""){
            jQuery("input#sortby").val(sortby);
        }

        jQuery("select.js-ticket-sorting-select").on('change',function(){
            var sortby = jQuery(this).val();
            jQuery("input#sortby").val(sortby);
            jQuery("form#adminForm").submit();
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
            jQuery("form#adminForm").submit();
        });
        function doVisible() {
            jQuery("#js-filter-wrapper-toggle-ticketid").show();
            jQuery("#js-filter-wrapper-toggle-minus").show();
            jQuery("#js-filter-wrapper-toggle-plus").hide();
        }
    });
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
                        <li><?php echo JText::_('Tickets'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Tickets'); ?></h1>
            <?php $link = 'index.php?option='.$this->option.'&c=ticket&task=addnewticket'; ?>
            <a class="tk-heading-addbutton" href="<?php echo $link; ?>">
                <img class="js-heading-addimage" src="components/com_jssupportticket/include/images/plus.png">
                <?php echo JText::_('Create Ticket'); ?>
            </a>
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <!-- tabs -->
            <div class="js-row js-ticket-top-cirlce-count-wrp">
                        <?php
                        JHTML::_('behavior.formvalidator');
                        /*
                        JHtml::_('stylesheet', 'system/calendar-jos.css', array('version' => 'auto', 'relative' => true), $attribs);
                        JHtml::_('script', $tag . '/calendar.js', array('version' => 'auto', 'relative' => true));
                        JHtml::_('script', $tag . '/calendar-setup.js', array('version' => 'auto', 'relative' => true));
                        */
                        if ($this->sortlinks['sortorder'] == 'ASC')
                            $img = "components/com_jssupportticket/include/images/sort1.png";
                        else
                            $img = "components/com_jssupportticket/include/images/sort2.png";
                        ?>
                        
                        <?php if(isset($this->ticketinfo['mytickets']) && $this->ticketinfo['mytickets'] != 0){
                            $open_percentage        =  round(($this->ticketinfo['open'] / $this->ticketinfo['mytickets']) * 100);
                            $close_percentage       =  round(($this->ticketinfo['close'] / $this->ticketinfo['mytickets']) * 100);
                            $answered_percentage    =  round(($this->ticketinfo['isanswered'] / $this->ticketinfo['mytickets']) * 100);
                            $overdue_percentage    =  round(($this->ticketinfo['isoverdue'] / $this->ticketinfo['mytickets']) * 100);}

                            if(isset($this->ticketinfo['mytickets']) && $this->ticketinfo['mytickets'] != 0){
                                $allticket_percentage = 100;
                            }else{
                                $allticket_percentage = 0;
                            } ?>
                        <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-open">
                            <a class="js-ticket-green js-myticket-link <?php if($this->listtype == 1) echo "selected";?>" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets<?php echo htmlspecialchars('&lt'); ?>=1&Itemid=<?php echo $this->Itemid."&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder']); ?>&uid=<?php echo $useruid; ?>">
                                <div class="js-ticket-cricle-wrp ">
                                    <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $open_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                        <div class="loader-bg"></div>
                                    </div>
                                </div>
                                <div class="js-ticket-circle-count-text">
                                    <?php
                                        echo JText::_('Open');
                                        if($this->config['show_count_tickets'] == 1)
                                        echo " ( " . $this->ticketinfo['open'] . " ) ";
                                    ?>
                                </div>
                            </a>
                        </div>
                        <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-close">
                            <a class="js-ticket-red js-myticket-link <?php if($this->listtype == 4) echo "selected";?>" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets<?php echo htmlspecialchars('&lt'); ?>=4&Itemid=<?php echo $this->Itemid."&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder']); ?>&uid=<?php echo $useruid; ?>">
                                <div class="js-ticket-cricle-wrp ">
                                    <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $close_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                        <div class="loader-bg"></div>
                                    </div>
                                </div>
                                <div class="js-ticket-circle-count-text js-ticket-red">
                                    <?php
                                        echo JText::_('Closed');
                                        if($this->config['show_count_tickets'] == 1)
                                        echo " ( " . $this->ticketinfo['close'] . " ) ";
                                    ?>
                                </div>
                            </a>
                        </div>
                        <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-overdue">
                            <a class="js-ticket-orange js-myticket-link <?php if($this->listtype == 3) echo "selected";?>" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets<?php echo htmlspecialchars('&lt'); ?>=3&Itemid=<?php echo $this->Itemid."&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder']); ?>&uid=<?php echo $useruid; ?>">
                                <div class="js-ticket-cricle-wrp ">
                                    <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $overdue_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                        <div class="loader-bg"></div>
                                    </div>
                                </div>
                                <div class="js-ticket-circle-count-text js-ticket-orange">
                                    <?php
                                        echo JText::_('Overdue');
                                        if($this->config['show_count_tickets'] == 1)
                                        echo " ( " . $this->ticketinfo['isoverdue'] . " ) ";
                                    ?>
                                </div>
                            </a>
                        </div>
                        <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-answer">
                            <a class="js-ticket-pink js-myticket-link <?php if($this->listtype == 2) echo "selected";?>" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets<?php echo htmlspecialchars('&lt'); ?>=2&Itemid=<?php echo $this->Itemid."&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder']); ?>&uid=<?php echo $useruid; ?>">
                                <div class="js-ticket-cricle-wrp ">
                                    <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $answered_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                        <div class="loader-bg"></div>
                                    </div>
                                </div>
                                <div class="js-ticket-circle-count-text js-ticket-pink">
                                    <?php
                                        echo JText::_('Answered');
                                        if($this->config['show_count_tickets'] == 1)
                                        echo " ( " . $this->ticketinfo['isanswered'] . " ) ";
                                    ?>
                                </div>
                            </a>
                        </div>
                        <div class="js-col-xs-12 js-col-md-2 js-myticket-link js-ticket-myticket-link-myticket js-ticket-allticket">
                            <a class="js-ticket-blue js-myticket-link <?php if($this->listtype == 5) echo "selected";?>" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets<?php echo htmlspecialchars('&lt'); ?>=5&Itemid=<?php echo $this->Itemid."&sortby=".$this->sortlinks['sorton']. strtolower($this->sortlinks['sortorder']); ?>&uid=<?php echo $useruid; ?>">
                                <div class="js-ticket-cricle-wrp ">
                                    <div class="circlebar" data-circle-startTime=0 data-circle-maxValue="<?php echo $allticket_percentage; ?>" data-circle-dialWidth=15 data-circle-size="100px" data-circle-type="progress">
                                        <div class="loader-bg"></div>
                                    </div>
                                </div>
                                <div class="js-ticket-circle-count-text js-ticket-blue">
                                    <?php
                                        echo JText::_('All Tickets');
                                        if($this->config['show_count_tickets'] == 1)
                                        echo " ( " . $this->ticketinfo['mytickets'] . " ) ";
                                    ?>
                                </div>
                            </a>
                        </div>
                    </div>
            <!-- search bar -->
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" name="filter_ticketid" id="filter_ticketid" placeholder="<?php echo JText::_('Ticket ID') ?>" size="10" value="<?php if (isset($this->lists['searchticket'])) echo $this->lists['searchticket']; ?>" class="text_area"/></div>
                <div class="tk-search-value"><input type="text" name="filter_subject" id="filter_subject" placeholder="<?php echo JText::_('Subject') ?>" size="10" value="<?php if (isset($this->lists['searchsubject'])) echo $this->lists['searchsubject']; ?>" class="text_area"/></div>
                <div class="tk-search-value"><input type="text" name="filter_from" id="filter_from" placeholder="<?php echo JText::_('From') ?>" size="10" value="<?php if (isset($this->lists['searchfrom'])) echo $this->lists['searchfrom']; ?>" class="text_area"/></div>
                <div id="js-filter-wrapper-toggle-area">
                <div class="tk-search-value"><input type="text" name="filter_fromemail" id="filter_fromemail" placeholder="<?php echo JText::_('Email') ?>" size="10" value="<?php if (isset($this->lists['searchfromemail'])) echo $this->lists['searchfromemail']; ?>" class="text_area"/></div>
                <div class="tk-search-value"><?php echo JHTML::_('calendar', isset($this->lists['datestart']) ? $this->lists['datestart'] : '', 'filter_datestart', 'filter_datestart', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19' , 'placeholder' => JText::_('Start Date'))); ?></div>
                <div class="tk-search-value"><?php echo JHTML::_('calendar', isset($this->lists['dateend']) ? $this->lists['dateend'] : '', 'filter_dateend', 'filter_dateend', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19', 'placeholder' => JText::_('End Date'))); ?></div>
                <div class="tk-search-value"><?php echo $this->lists['staffmembers']; ?></div>
                <div class="tk-search-value"><?php echo $this->lists['departments']; ?></div>
                <div class="tk-search-value"><?php echo $this->lists['priorities']; ?></div>
                <?php
                    $params = null;
                    if(isset($this->lists['params'])){
                        $params = $this->lists['params'];
                    }
                    $k = 1;
                    $customfields = getCustomFieldClass()->userFieldsForSearch(1);
                    foreach ($customfields as $field) {
                        echo '<div class="tk-search-value">';
                            getCustomFieldClass()->formCustomFieldsForSearch($field, $k, $params , 1);
                        echo '</div>';
                    }
                    ?>
                </div>
                <div class="tk-search-button">
                    <span id="js-filter-wrapper-toggle-btn">
                        <span id="js-filter-wrapper-toggle-plus">
                            <a href="#" class="js-search-filter-btn" id="js-search-filter-toggle-btn">
                                <?php echo JText::_('Show All'); ?>
                            </a>
                        </span>
                        <span id="js-filter-wrapper-toggle-minus">
                            <a href="#" class="js-search-filter-btn" id="js-search-filter-toggle-btn show-less-btn">
                                <?php echo JText::_('Show Less'); ?>
                            </a>
                        </span>
                    </span>
                    <button class="jsst-search" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                    <button class="jsst-reset" onclick="resetJsForm();this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                </div>
                <input type="hidden" name="sortby" id="sortby">
            </div>
            <!-- tickts listing -->
            <?php
            $link = 'index.php?option=com_jssupportticket&c=ticket&layout=tickets'.htmlspecialchars('&lt').'='.$this->listtype;
            if ($this->sortorder == 'ASC')
                $img = "components/com_jssupportticket/include/images/sort0.png";
            else
                $img = "components/com_jssupportticket/include/images/sort1.png";
            ?>
            <div class="js-ticket-sorting">
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
            <?php
            if (!(empty($this->result)) && is_array($this->result)) {
                $i = 0;
                foreach ($this->result AS $row) {
                    $checked = JHTML::_('grid.id', $i, $row->id);
                    $link_edit = 'index.php?option='.$this->option.'&c=ticket&task=addnewticket&cid[]='.$row->id;
                    $link_enforce_delete = 'index.php?option=' . $this->option . '&c=ticket&task=enforcedelete&cid='.$row->id.'&'. JSession::getFormToken() .'=1';
                    $link_delete = 'index.php?option=' . $this->option . '&c=ticket&task=delete&cid='.$row->id.'&'. JSession::getFormToken() .'=1';
                    $link_detail = 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetails&cid[]='.$row->id;
?>
                    <div id="js-tk-main-wrapper">
                        <div class="js-ticket-toparea">
                        <div class="js-icon">
                            <?php if ($row->staffphoto) { ?>
                                <img  src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                            <?php } else { ?>
                                <img src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                            <?php } ?>
                        </div>
                        <div class="js-ticket-data">
                            <div class="js-middle">
                            <div class="js-col-md-12 js-col-xs-12 js-wrapper"><span class="js-tk-value" style="cursor:pointer;" onClick="setFromNameFilter('<?php echo $row->email; ?>');"><?php echo $row->name; ?></span></div>
                            <div class="js-col-md-12 js-col-xs-12 js-wrapper-subject js-wrapper"><a href="<?php echo $link_detail; ?>"> <?php echo $row->subject; ?></a></div>
                            <div class="js-col-md-12 js-col-xs-12 js-tk-preletive js-wrapper">
                                <span class="js-tk-title"><?php echo JText::_('Department'); ?><font>:</font></span><span class="js-tk-value" style="cursor:pointer;" onclick="setDepartmentFilter(<?php echo $row->departmentid;?>);"><?php echo JText::_($row->departmentname); ?></span>
                            </div>

                            <?php
                                $customfields = getCustomFieldClass()->userFieldsData(1, 1);
                                foreach ($customfields as $field) {
                                    echo getCustomFieldClass()->showCustomFields($field,4, $row->params , $row->id);
                                }
                            ?>
                            </div>
                        <div class="js-right">
                            <?php if ($row->ticketviaemail == 1) { ?>
                                <span style="background-color: #0066CC;"><?php echo JText::_('Ticket via email'); ?></span>
                            <?php } ?>
                            <?php
                                $counter = 'one';
                                if ($row->lock == 1) { ?>
                                    <img class="ticketstatusimage <?php echo $counter;$counter = 'two'; ?>" src="<?php echo JURI::root(); ?>administrator/components/com_jssupportticket/include/images/lock.png" title="<?php echo JText::_('Ticket Is Locked'); ?>" />
                                <?php } ?>
                                <?php if ($row->isoverdue == 1) { ?>
                                    <img class="ticketstatusimage <?php echo $counter; ?>" src="<?php echo JURI::root(); ?>administrator/components/com_jssupportticket/include/images/over-due.png" title="<?php echo JText::_('Ticket mark overdue'); ?>" />
                                <?php } ?>
                                <?php if ($row->status == 0) { ?>
                                    <span class="js-ticket-status" style="color: #9ACC00;"><?php echo JText::_('New'); ?></span>
                                <?php } elseif ($row->status == 1) { ?>
                                    <span class="js-ticket-status" style="color: orange;"><?php echo JText::_('Waiting reply'); ?></span>
                                <?php } elseif ($row->status == 2) { ?>
                                    <span class="js-ticket-status" style="color: #FF7F50;"><?php echo JText::_('In progress'); ?></span>
                                <?php } elseif ($row->status == 3) { ?>
                                    <span class="js-ticket-status" style="color: #507DE4;"><?php echo JText::_('Replied'); ?></span>
                                <?php } elseif ($row->status == 4) { ?>
                                    <span class="js-ticket-status" style="color: #CB5355;"><?php echo JText::_('Close'); ?></span>
                                <?php } elseif ($row->status == 5){ ?>
                                    <span class="js-ticket-status" style="color: #ee1e22;"><?php echo JText::_('Close due to Merge'); ?></span>
                                <?php } ?>
                                <span class="js-ticket-priority" style="background:<?php echo $row->prioritycolour; ?>;"><?php echo JText::_($row->priority); ?></span>
                            <div class="js-ticket-data1">
                                <div class="js-wrapper"><span class="js-tk-title"><?php echo JText::_('Ticket ID').JText::_(':'); ?></span><span class="js-tk-value"> <?php echo $row->ticketid; ?></span></div>
                                <div class="js-wrapper"><span class="js-tk-title"><?php echo JText::_('Created').JText::_(':'); ?></span><span class="js-tk-value"><?php echo JHtml::_('date',$row->created,$this->config['date_format']); ?></span></div>
                                <div class="js-wrapper"><span class="js-tk-title"><?php echo JText::_('Last Reply').JText::_(':'); ?></span><span class="js-tk-value"><?php if ($row->lastreply == '' || $row->lastreply == '0000-00-00 00:00:00') echo JText::_('No last reply'); else echo JHtml::_('date',$row->lastreply,$this->config['date_format']); ?></span></div>
                            </div>
                            <?php $forlisting = $this->getJSModel('userfields')->getFieldsForListing(1);
                                if($forlisting['assignto'] == 1){   ?>
                                    <div class="js-wrapper"><span class="js-tk-title"><?php echo JText::_('Assign To').':'; ?></span><span class="js-tk-value"><?php echo $row->stafffirstname." ".$row->stafflastname ; ?></span></div>
                            <?php } ?>
                        </div>
                        </div>
                        </div>
                        <div class="js-bottom">
                                <a href="<?php echo $link_edit; ?>"><img class="js-tk-action-img" src="components/com_jssupportticket/include/images/tkedit.png">&nbsp;&nbsp;<?php echo JText::_('Edit Ticket'); ?> </a>
                                <a href="<?php echo $link_delete; ?>" onclick="return confirmdelete(0)"><img class="js-tk-action-img" src="components/com_jssupportticket/include/images/tkdelete.png">&nbsp;&nbsp;<?php echo JText::_('Delete Ticket'); ?> </a>
                                <a href="<?php echo $link_enforce_delete; ?>" onclick="return confirmdelete(1)"><img class="js-tk-action-img" src="components/com_jssupportticket/include/images/forced-delete.png">&nbsp;&nbsp;<?php echo JText::_('Enforce delete'); ?> </a>
                            </span>
                        </div>
                    </div>
                <?php
                    }
                    ?>
                    <div class="js-row js-tk-pagination js-bg-null">
                        <?php echo $this->pagination->getListFooter(); ?>
                    </div>
                    <?php
                }else {
                    messageslayout::getRecordNotFound(); //Empty Record
                } ?>
            <!-- tickets tabs and so on area -->
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="lt" value="<?php echo $this->listtype; ?>" />
            <input type="hidden" name="c" value="ticket" />
            <input type="hidden" name="layout" value="tickets" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="boxchecked" value="0" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

<script type="text/javascript">
    function resetJsForm(){
        var form = jQuery('form#adminForm');
        form.find("input[type=text], input[type=email], input[type=password], textarea").val("");
        form.find('input:checkbox').removeAttr('checked');
        form.find('select').prop('selectedIndex', 0);
        form.find('input[type="radio"]').prop('checked', false);
        jQuery("<input type='hidden' value='1' />")
         .attr("id", "jsresetbutton")
         .attr("name", "jsresetbutton")
         .appendTo(form);
    }
    
    function setDepartmentFilter( depid ){
        jQuery('#filter_department').val( depid );
        jQuery('form#adminForm').submit();
    }

    function setFromNameFilter( email ){
        jQuery('#filter_fromemail').val( email );
        jQuery('form#adminForm').submit();
    }

</script>
