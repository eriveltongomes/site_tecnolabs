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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
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
                        <li><?php echo JText::_('Feedbacks'); ?></li>
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
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Feedbacks'); ?></h1>
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm">
            <div id="js-tk-filter">
                <div class="tk-search-value"><input type="text" name="subject" id="subject" placeholder="<?php echo JText::_('Subject'); ?>" value="<?php if (isset($this->lists['subject'])) echo $this->lists['subject']; ?>" class="text_area"/></div>
                <div class="tk-search-value"><input type="text" name="ticketid" id="ticketid" placeholder="<?php echo JText::_('Ticket Id'); ?>" value="<?php if (isset($this->lists['ticketid'])) echo $this->lists['ticketid']; ?>" class="text_area"/></div>
                <div class="tk-search-value"><input type="text" name="from" id="from" placeholder="<?php echo JText::_('Username'); ?>" value="<?php if (isset($this->lists['from'])) echo $this->lists['from']; ?>" class="text_area"/></div>
                <div id="js-filter-wrapper-toggle-area">
                <div class="tk-search-value"><?php echo $this->lists['departments']; ?></div>
                <div class="tk-search-value"><?php echo $this->lists['staffmembers']; ?></div>
                </div>
                <div class="tk-search-button tk-feedback-search-button">
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
                    <button class="js-form-search" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                    <button class="js-form-reset" onclick="document.getElementById('subject').value = ''; document.getElementById('staffid').value = '';document.getElementById('departmentid').value = '';document.getElementById('from').value = '';document.getElementById('ticketid').value = ''; this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                </div>
            </div>
            <?php
            if (!(empty($this->feedbacks)) && is_array($this->feedbacks)) {  ?>
                <div class="jsst-feedback-det-main js-ticket-box-shadow">
                        <?php foreach ($this->feedbacks AS $feedback) {
                            $img_name ='';
                            if($feedback->rating == 5){
                                $img_name = 'excelent';
                            }elseif($feedback->rating == 4){
                                $img_name = 'happy';
                            }elseif($feedback->rating == 3){
                                $img_name = 'normal';
                            }elseif($feedback->rating == 2){
                                $img_name = 'bad';
                            }elseif($feedback->rating == 1){
                                $img_name = 'angery';
                            } ?>       
                            <div class="jsst-feedback-det-wrp">
                                <div class="jsst-feedback-det-list">
                                    <div class="jsst-feedback-det-list-top">
                                        <div class="js-ticket-pic">
                                            <img alt="user image" src="components/com_jssupportticket/include/images/user.png">
                                        </div>
                                        <div class="js-ticket-data">
                                        <div class="jsst-feedback-det-list-data-wrp">
                                            <div class="jsst-feedback-det-list-data-btm">
                                                <div class="jsst-feedback-det-list-data-btm-val">
                                                    <?php echo $feedback->name; ?>
                                                </div>
                                            </div>
                                            <div class="jsst-feedback-det-list-data-btm">
                                                    <div class="jsst-feedback-det-list-data-btm-val">
                                                        <a href="?page=ticket&jstlay=ticketdetail&jssupportticketid=<?php echo $feedback->ticketid; ?>" class="jsst-feedback-det-list-data-top-val-txt"><?php echo $feedback->subject;?> </a>
                                                </div>
                                            </div>
                                            <div class="jsst-feedback-det-list-data-btm">
                                                <div class="jsst-feedback-det-list-datea-btm-rec">
                                                    <div class="jsst-feedback-det-list-data-btm-title">
                                                        <?php echo JText::_('Ticket Id');?>:&nbsp;
                                                    </div>
                                                    <div class="jsst-feedback-det-list-data-btm-val">
                                                        <?php echo $feedback->trackingid; ?>
                                                    </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-datea-btm-rec">
                                                    <div class="jsst-feedback-det-list-data-btm-title">
                                                        <?php echo JText::_('User');?>:&nbsp;
                                                    </div>
                                                    <div class="jsst-feedback-det-list-data-btm-val">
                                                        <?php echo $feedback->name; ?>
                                                    </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-datea-btm-rec">
                                                    <div class="jsst-feedback-det-list-data-btm-title">
                                                        <?php echo JText::_('Department');?>:&nbsp;
                                                    </div>
                                                    <div class="jsst-feedback-det-list-data-btm-val">
                                                        <?php echo $feedback->departmentname; ?>
                                                    </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-datea-btm-rec">
                                                    <div class="jsst-feedback-det-list-data-btm-title">
                                                        <?php echo JText::_('Staff');?>:&nbsp;
                                                    </div>
                                                    <div class="jsst-feedback-det-list-data-btm-val">
                                                        <?php echo $feedback->firstname .'&nbsp;'.$feedback->lastname; ?>
                                                    </div>
                                                </div>
                                            </div>
                                             <?php
                                                $customfields = getCustomFieldClass()->userFieldsData(2, 1);
                                                foreach ($customfields as $field) {
                                                    $f_array =  getCustomFieldClass()->showCustomFields($field,5, $feedback->params , $feedback->ticketid);
                                                    echo '
                                                    <div class="jsst-feedback-det-list-data-btm">
                                                        <div class="jsst-feedback-det-list-datea-btm-rec">
                                                            <div class="jsst-feedback-det-list-data-btm-title">
                                                                '. JText::_($f_array['title']).':&nbsp;
                                                            </div>
                                                            <div class="jsst-feedback-det-list-data-btm-val">
                                                                '. JText::_($f_array['value']).'
                                                            </div>
                                                        </div>
                                                    </div>';
                                                }
                                            ?>
                                        </div>
                                        <div class="js-ticket-right">
                                        <div class="jsst-feedback-det-list-img-wrp">
                                            <img alt="image" title="image" src="<?php echo '../components/com_jssupportticket/include/images/'.$img_name.'.png';?>" />
                                        </div>
                                        </div>
                                        </div>
                                    </div>
                                    <?php if($feedback->remarks !=''){ ?>
                                        <div class="jsst-feedback-det-list-btm">
                                            <div class="jsst-feedback-det-list-btm-title">
                                                <?php echo JText::_('Feedback');?>:&nbsp;
                                            </div>
                                            <div class="jsst-feedback-det-list-btm-val">
                                                <?php echo $feedback->remarks; ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                </div>
                            </div>
                        <?php } ?>
                </div>
                <div class="js-row js-tk-pagination js-bg-null js-ticket-pagination-shadow">
                    <?php echo $this->pagination->getListFooter(); ?>
                </div>
            <?php 
            }else{
                messagesLayout::getRecordNotFound();
            } ?>
            <input type="hidden" name="option" value="<?php echo $this->option; ?>"/>
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>"/>
            <input type="hidden" name="c" value="feedback"/>
            <input type="hidden" name="layout" value="feedbacks"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="boxchecked" value="0"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

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
        function doVisible() {
            jQuery("#js-filter-wrapper-toggle-ticketid").show();
            jQuery("#js-filter-wrapper-toggle-minus").show();
            jQuery("#js-filter-wrapper-toggle-plus").hide();
        }
    });
</script>
