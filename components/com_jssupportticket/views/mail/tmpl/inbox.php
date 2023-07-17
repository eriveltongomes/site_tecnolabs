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
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/mail-inbox.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){
                        $read = array(
                            '0' => array('value' => JText::_(''),
                                'text' => JText::_('Select type')),
                            '1' => array('value' => JText::_('1'),
                                'text' => JText::_('Read')),
                            '2' => array('value' => JText::_('2'),
                                'text' => JText::_('Unread')),);
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
                                                <?php echo JText::_('Inbox'); ?>
                                            </li>
                                        </ul>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                    <div class="js-ticket-mail-wrapper">
                        <div class="js-ticket-top-search-wrp">
                            
                            <div class="js-ticket-search-fields-wrp">
                               <form class="js-filter-form" action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=mail&layout=inbox'); ?>" method="post" name="adminForm" id="adminForm">
                                    <div class="js-ticket-fields-wrp">
                                        <div class="js-ticket-form-field" id="filtersubject">
                                            <input type="text" name="filter_subject" id="filter_subject" size="15" value="<?php if (isset($this->lists['subject'])) echo $this->lists['subject']; ?>" class="js-ticket-field-input" placeholder="<?php echo JText::_('Search'); ?>"/>
                                        </div>
                                        <div class="js-ticket-form-field" id="selecttype">
                                            <?php echo JHtml::_('select.genericList', $read, 'read', 'class="inputbox js-ticket-select-field"' . '', 'value', 'text', isset($this->lists['read'])?$this->lists['read']:''); ?>
                                        </div>
                                        <div id="js-filter-wrapper-toggle-area">
                                        <div class="js-ticket-form-field js-ticket-form-field-select js-ticket-margin-top-select" id="startdate">
                                            <?php if (isset($this->lists['start_date'])) echo JHTML::_('calendar', $this->lists['start_date'], 'filter_start_date', 'startdate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19')); else echo JHTML::_('calendar', '', 'filter_start_date', 'startdate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19','placeholder' => JText::_('Start date'))); ?>
                                        </div>
                                        <div class="js-ticket-form-field js-ticket-form-field-select js-ticket-margin-top-select" id="enddate">
                                            <?php if (isset($this->lists['end_date'])) echo JHTML::_('calendar', $this->lists['end_date'], 'filter_end_date', 'enddate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19')); else echo JHTML::_('calendar', '', 'filter_end_date', 'enddate', $js_dateformat, array('class' => 'inputbox', 'size' => '10', 'maxlength' => '19' ,'placeholder' => JText::_('End date'))); ?>
                                        </div>
                                        </div>
                                        <div class="js-ticket-search-form-btn-wrp js-ticket-staff-search-btn-wrp js-ticket-mail-search-btn-wrp">
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
                                            <button class="js-search-button" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                                            <button class="js-reset-button" onclick="resetJsForm();this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="boxchecked" value="0" />
                                    <input type="hidden" name="task" value="" />
                                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                                </form>
                            </div>
                        </div>
                        <!-- Button Wrapper -->
                        <?php if ($this->unreadmessages >= 1) {$inbox = $this->unreadmessages; } else {$inbox = $this->totalinboxmessages; } ?>   
                        <div class="js-ticket-mails-btn-wrp ac">
                            <div class="js-ticket-mail-btn">
                                <a class="js-add-link button active" href="index.php?option=com_jssupportticket&c=mail&layout=inbox&Itemid=<?php echo $this->Itemid; ?>">
                                    <img id="inbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/inbox-black.png" />
                                    <img id="inbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/inbox-black.png" />
                                    <?php echo JText::_('inbox') . "&nbsp;(" . $inbox . ")"; ?>
                                </a>
                            </div>
                            <div class="js-ticket-mail-btn">
                                <a class="js-add-link button" href="index.php?option=com_jssupportticket&c=mail&layout=outbox&Itemid=<?php echo $this->Itemid; ?>">
                                    <img id="outbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/outbox-black.png" />
                                    <img id="outbox-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/outbox-black.png" />
                                    <?php echo JText::_('Outbox') . "&nbsp;(" . $this->outboxmessages . ")"; ?>
                                </a>
                            </div>
                            <div class="js-ticket-mail-btn">
                                <a class="js-add-link button" href="index.php?option=com_jssupportticket&c=mail&layout=formmessage&Itemid=<?php echo $this->Itemid; ?>">
                                    <img id="compose-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/compose-black.png" />
                                    <img id="compose-img" class="js-ticket-mail-img" src="components/com_jssupportticket/include/images/compose-black.png" />
                                    <?php echo JText::_('Compose'); ?>
                                </a>
                            </div>
                        </div>
                        
                        <?php  if ((!empty($this->messages)) && is_array($this->messages)) { ?>
                            <div class="js-ticket-download-content-wrp">
                                <div class="js-ticket-search-heading-wrp">
                                <div class="js-ticket-heading-left">
                                    <?php echo JText::_('Emails') ?>
                                </div>
                                <div class="js-ticket-heading-right">
                                    <a class="js-ticket-add-download-btn" href="index.php?option=com_jssupportticket&c=mail&layout=formmessage&Itemid=<?php echo $this->Itemid; ?>"><span class="js-ticket-add-img-wrp"><img src="components/com_jssupportticket/include/images/add.png" alt="Add-image"></span><?php echo JText::_('Compose') ?></a> 
                                </div>
                            </div>
                                <div class="js-ticket-table-wrp js-col-md-12">
                                    <div class="js-ticket-table-header">
                                        <div class="js-ticket-table-header-col js-col-md-4 js-col-xs-4"><?php echo JText::_('Subject'); ?></div>
                                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2"><?php echo JText::_('From'); ?></div>
                                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2"><?php echo JText::_('Status'); ?></div>
                                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2"><?php echo JText::_('Created'); ?></div>
                                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2"><?php echo JText::_('Action'); ?></div>
                                    </div>
                                    <div class="js-ticket-table-body">
                                        <?php foreach ($this->messages AS $message) { ?>
                                            <?php $link = 'index.php?option=' . $this->option . '&c=mail&layout=message&id=' . $message->id . '&Itemid=' . $this->Itemid ?>
                                            <?php if($message->isread == 1) $icon_status = 'mark_read.png'; else $icon_status = 'mark_unread.png'; ?>
                                            <div class="js-ticket-data-row" id="tk-row-<?php echo $message->id; ?>">
                                                <div id="js-suredelete"><img id="warning-icon" src="components/com_jssupportticket/include/images/warning_icon.png" />
                                                    <?php echo JText::_('Are you sure to delete'); ?> ?
                                                    <a class="js-tk-suredelete-btn" onclick="yesdeletethisrecord('<?php echo $message->id; ?>','mail','removemessage','<?php echo JSession::getFormToken(); ?>');">
                                                        <?php echo JText::_('Delete'); ?>
                                                    </a>
                                                    <a class="js-tk-canceldelete-btn" onclick="canceldelete('<?php echo $message->id; ?>');">
                                                        <?php echo JText::_('No'); ?>
                                                    </a>  
                                                </div>

                                                <div class="js-ticket-table-body-col js-ticket-first-child js-col-md-4 js-col-xs-4">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Subject:'); ?></span>
                                                    <span class="js-ticket-title"><?php if ($message->isread == 2) echo '<b>'; ?><a  href="<?php echo $link; ?>"> <?php if ($message->count != 0) echo $message->subject . ' ' . JText::_('Re'); else echo $message->subject; ?></a><?php if ($message->isread == 2) echo '</b>'; ?></span>
                                                </div>
                                                <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('From:'); ?></span>
                                                    <?php echo $message->staffname; ?>
                                                </div>
                                                <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Status:'); ?></span>
                                                    <img src="components/com_jssupportticket/include/images/<?php echo $icon_status; ?>">
                                                </div>
                                                <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Created:'); ?></span>
                                                    <?php echo JHtml::_('date',$message->created,$this->config['date_format']); ?>
                                                </div>
                                                <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Action:'); ?></span>
                                                    <a class="js-tk-button" onclick="suretodelete('<?php echo $message->id; ?>');">
                                                        <img src="components/com_jssupportticket/include/images/roles_icons/delete.png">
                                                    </a>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=mail&layout=outbox&Itemid=' . $this->Itemid); ?>" method="post">    
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
                                    </div>
                                </div>
                            </div>
                        <?php } else {   
                            messageslayout::getRecordNotFound(); //Empty Record
                        }?>
                    </div>
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
                        jQuery("#js-filter-wrapper-toggle-minus").hide();
                        jQuery("#js-filter-wrapper-toggle-plus").show();
                    }
                    jQuery("#js-filter-wrapper-toggle-area").toggle();
                });
                function doVisible() {
                    jQuery("#js-filter-wrapper-toggle-minus").show();
                    jQuery("#js-filter-wrapper-toggle-plus").hide();
                }
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
