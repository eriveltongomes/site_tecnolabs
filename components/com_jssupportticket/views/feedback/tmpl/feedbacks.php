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
?>
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('a[href="#"]').click(function(e){
            e.preventDefault();
        });
    });
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
                
                jQuery("#js-filter-wrapper-toggle-btn").click(function () {
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
<div class="js-row js-null-margin">
    <?php 
        if($this->config['offline'] != '1'){
            require_once JPATH_COMPONENT_SITE . '/views/header.php';
            $document = JFactory::getDocument();
            $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/staff-staffprofile.css', 'text/css');
            $language = JFactory::getLanguage();
            $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
        }
    ?>
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){ ?>
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
                                            <?php echo JText::_('Feedbacks'); ?>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="js-ticket-feedback-wrapper">
                        <div class="js-ticket-top-search-wrp">
                            <?php /* <div class="js-ticket-search-heading-wrp">
                                <div class="js-ticket-heading-left">
                                    <?php echo JText::_('Feedbacks') ?>
                                </div>
                            </div> */?>
                            <div class="js-ticket-search-fields-wrp">
                                <form class="js-filter-form" action="<?php echo JRoute::_('index.php?option=com_jssupportticket&view=feedback&layout=feedbacks'); ?>" method="post" name="adminForm" id="adminForm">
                                    <div class="js-ticket-fields-wrp">
                                        <div class="js-ticket-form-field js-form-field js-ticket-feedback-fields-margin-bottom-null">
                                            <input type="text" name="subject" id="subject" placeholder="<?php echo JText::_('Subject'); ?>" value="<?php if (isset($this->lists['subject'])) echo $this->lists['subject']; ?>" class="js-ticket-field-input"/>
                                        </div>
                                        <div class="js-ticket-form-field js-ticket-feedback-fields-margin-bottom">
                                            <input type="text" name="ticketid" id="ticketid" placeholder="<?php echo JText::_('Ticket Id'); ?>" value="<?php if (isset($this->lists['ticketid'])) echo $this->lists['ticketid']; ?>" class="js-ticket-field-input"/>
                                        </div>
                                        <div id="js-filter-wrapper-toggle-area">
                                        
                                        <div class="js-ticket-form-field js-ticket-feedback-fields-margin-bottom">
                                            <input type="text" name="from" id="from" placeholder="<?php echo JText::_('User Name'); ?>" value="<?php if (isset($this->lists['from'])) echo $this->lists['from']; ?>" class="js-ticket-field-input"/>
                                        </div>
                                        <div class="js-ticket-form-field js-ticket-feedback-fields-margin-bottom js-ticket-form-field-select">
                                            <?php echo $this->lists['departments']; ?>
                                        </div>
                                        <div class="js-ticket-form-field js-ticket-form-field-select">
                                            <?php echo $this->lists['staffmembers']; ?>
                                        </div>
                                        </div>
                                    <div class="js-ticket-search-form-btn-wrp js-search-form-btn-wrp js-search-form-feedback-btn-wrp">
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
                                        <button class="js-reset-button" onclick="document.getElementById('subject').value = ''; document.getElementById('staffid').value = '';document.getElementById('departmentid').value = '';document.getElementById('from').value = '';document.getElementById('ticketid').value = ''; this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                                    </div>
                                    </div>
                                    <input type="hidden" name="boxchecked" value="0" />
                                    <input type="hidden" name="task" value="" />
                                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                                </form>
                            </div>
                        </div>
                        <div class="js-col-md-12 js-ticket-feedback-list-wrapper">
                            <div class="js-ticket-feedback-heading"><?php echo JText::_('Latest Feedback');?></div>   
                            <?php if (!(empty($this->feedbacks)) && is_array($this->feedbacks)) {  ?>
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
                                    } 
                                    $link = 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetail&id='.$feedback->ticketid.'&Itemid='.$this->Itemid;
                                ?>       
                                <div class="jsst-feedback-det-wrp">
                                    <div class="jsst-feedback-det-list">
                                        <div class="jsst-feedback-det-list-top">
                                            <div class="jsst-feedback-det-list-img-wrp">
                                                <img alt="image" title="image" src="<?php echo 'components/com_jssupportticket/include/images/'.$img_name.'.png';?>" />
                                            </div>
                                            <div class="jsst-feedback-det-list-data-wrp">
                                                <div class="jsst-feedback-det-list-data-left">
                                                <div class="jsst-feedback-det-list-data-btm">
                                                    <div class="jsst-feedback-det-list-datea-btm-rec">
                                                        <div class="jsst-feedback-det-list-data-btm-val name">
                                                            <?php echo $feedback->name; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-data-btm">
                                                    <div class="jsst-feedback-det-list-data-top-val">
                                                        <a href="<?php echo $link; ?>" class="jsst-feedback-det-list-data-top-val-txt"><?php echo $feedback->subject;?> <?php /* <img src="components/com_jssupportticket/include/images/newtab-icon.png" /> */?></a>
                                                    </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-data-btm">
                                                    <div class="jsst-feedback-det-list-datea-btm-rec">
                                                        <div class="jsst-feedback-det-list-data-btm-title">
                                                            <?php echo jText::_('Department');?>:&nbsp;
                                                        </div>
                                                        <div class="jsst-feedback-det-list-data-btm-val">
                                                            <?php echo $feedback->departmentname; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-data-right">
                                                <div class="jsst-feedback-det-list-data-btm">
                                                    <div class="jsst-feedback-det-list-datea-btm-rec">
                                                        <div class="jsst-feedback-det-list-data-btm-title">
                                                            <?php echo jText::_('Staff');?>:&nbsp;
                                                        </div>
                                                        <div class="jsst-feedback-det-list-data-btm-val staff">
                                                            <?php echo $feedback->firstname .'&nbsp;'.$feedback->lastname; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-data-btm">
                                                    <div class="jsst-feedback-det-list-datea-btm-rec">
                                                        <div class="jsst-feedback-det-list-data-btm-title">
                                                            <?php echo jText::_('Created');?>:&nbsp;
                                                        </div>
                                                        <div class="jsst-feedback-det-list-data-btm-val">
                                                            <?php echo $feedback->created; ?>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="jsst-feedback-det-list-data-btm">
                                                    <div class="jsst-feedback-det-list-datea-btm-rec">
                                                        <div class="jsst-feedback-det-list-data-btm-title">
                                                            <?php echo jText::_('Ticket Id');?>:&nbsp;
                                                        </div>
                                                        <div class="jsst-feedback-det-list-data-btm-val">
                                                            <?php echo $feedback->trackingid; ?>
                                                        </div>
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
                                        </div>
                                        <?php if($feedback->remarks !=''){ ?>
                                            <div class="jsst-feedback-det-list-btm">
                                                <div class="jsst-feedback-det-list-btm-title">
                                                    <?php echo jText::_('Feedback');?>:&nbsp;
                                                </div>
                                                <div class="jsst-feedback-det-list-btm-val">
                                                    <?php echo $feedback->remarks; ?>
                                                </div>
                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>
                            <?php } ?>
                            <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=feedback&layout=feedbacks&Itemid='.$this->Itemid); ?>" method="post">    
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
                        </div>
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
