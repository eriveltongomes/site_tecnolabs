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
    function validate_form(f){
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken();else echo JSession::getFormToken(); ?>';//send token
        } else {
            alert("<?php echo JText::_('Please provide a rating'); ?>");
            return false;
        }
        return true;
    }
</script>
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    if($this->successflag == 0){
        JHTML::_('behavior.formvalidator');
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
                                <?php echo JText::_('Add Feedback'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="js-ticket-add-form-wrapper ">
            <form class="js-ticket-form" action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm" class="form-validate" >
                <?php foreach ($this->fieldordering AS $field){
                    switch ($field->field) {
                        case 'rating': ?>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                <div class="js-ticket-from-field-title">
                                    <?php echo jText::_($field->fieldtitle); ?>&nbsp;<font color="red">*</font>
                                </div>
                                <div class="js-ticket-from-field">
                                    <div class="jsst-rating-div">
                                        <img class="rating_image" data-value="1" src="components/com_jssupportticket/include/images/rating/angery.png"/>
                                        <img class="rating_image" data-value="2" src="components/com_jssupportticket/include/images/rating/bad.png"/>
                                        <img class="rating_image" data-value="3" src="components/com_jssupportticket/include/images/rating/normal.png"/>
                                        <img class="rating_image" data-value="4" src="components/com_jssupportticket/include/images/rating/happy.png"/>
                                        <img class="rating_image" data-value="5" src="components/com_jssupportticket/include/images/rating/excelent.png"/>
                                    </div>
                                    <input type="hidden" name="rating" id="rating" value="" class="required"/>
                                </div>
                            </div>
                        <?php break;
                        case 'remarks':
                            ?>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                <div class="js-ticket-from-field-title">
                                    <?php echo JText::_($field->fieldtitle); ?>
                                </div>
                                <div class="js-ticket-from-field">
                                    <?php
                                        $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                                        echo $editor->display('remarks', '', '550', '300', '60', '20', false);
                                    ?>
                                </div>
                            </div>
                        <?php break;
                            default:
                                echo getCustomFieldClass()->formCustomFields($field,'','',false);
                            break;
                    }
                }?>
                <div class="js-ticket-form-btn-wrp">
                    <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Feedback'); ?>" />
                </div>
                <input type="hidden" name="created" value="<?php echo date('Y-m-d H:i:s'); ?>" />
                <input type="hidden" name="id" value="" />
                <input type="hidden" name="c" value="feedback" />
                <input type="hidden" name="view" value="feedback" />
                <input type="hidden" name="layout" value="formfeedback" />
                <input type="hidden" name="check" value="" />
                <input type="hidden" name="task" value="savefeedback" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="ticketid" value="<?php echo $this->ticketid; ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </form>
        </div>
           
        <?php }else{
        messageslayout::getFeedbackMessage($this->successflag,$this->config['feedback_thanks_message']); //feedback stored message
    }
}else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>
</div>
 <script type="text/javascript">
    jQuery(document).ready(function () {
        jQuery('img.rating_image').on('mouseover', function(){
            if(jQuery(this).hasClass('selected')){
            }else{
                src = jQuery(this).attr('src').replace('.png', '-1.png');
                jQuery(this).attr('src', src);
            }
        })
        jQuery('img.rating_image').on('mouseout', function(){
            if(jQuery(this).hasClass('selected')){
            }else{
                src = jQuery(this).attr('src').replace('-1.png', '.png');
                jQuery(this).attr('src', src);
            }
        }); 
        jQuery('img.rating_image').on('click', function(){
            jQuery("img.rating_image").each(function(index) {
                if(jQuery(this).hasClass('selected')){
                    jQuery(this).removeClass('selected');
                    src = jQuery(this).attr('src').replace('-1.png', '.png');
                    jQuery(this).attr('src', src);
                }
            });
            jQuery(this).addClass('selected');
            val = jQuery(this).attr('data-value');
            jQuery('input#rating').val(val);
        }); 
    });
</script>
