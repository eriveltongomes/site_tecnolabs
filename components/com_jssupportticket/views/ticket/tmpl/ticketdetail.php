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
JText::script('Error file size too large');
JText::script('Error file extension mismatch');
$document = JFactory::getDocument();
$document->addScript('components/com_jssupportticket/include/js/timer.jquery.js');
?>
<div class="js-row js-null-margin">
<?php
if(isset($this->perm_not_allowed) && $this->perm_not_allowed == 2 ){
    messageslayout::getPermissionNotAllow(); //permission not granted
}elseif(isset($this->perm_not_allowed) && $this->perm_not_allowed == 3 ){
    messageslayout::getUserGuest($this->layoutname,$this->Itemid); //visitor trying to view ticket that belongs to logged in user.
}elseif(isset($this->perm_not_allowed) && $this->perm_not_allowed == 4 ){
    messageslayout::getUserNotAllowedToViewTicket(); //permission not granted
}elseif(!isset($this->ticketdetail) && empty($this->ticketdetail)){
    messageslayout::getUserNotAllowedToViewTicket(); //permission not granted
}else{
$isstaff = $this->user->getIsStaff();
$per_viewticket = true;
$isstaffdisable = true;
$per_ticketmerge = false;
if($isstaff){
    $per_viewticket = ($this->ticket_permissions['View Ticket'] == 1) ? true : false;
    $isstaffdisable = !($this->user->getIsStaffDisable());
    $per_ticketmerge = ($this->ticket_permissions['Ticket Merge'] == 1) ? true : false;
    $post_reply_permission = ($this->ticket_permissions['Reply Ticket'] == 1) ? 1 : 0;
    $post_internal_note_permission = ($this->ticket_permissions['Post Internal Note'] == 1) ? 1 : 0;
    $dep_transfer_permission = ($this->ticket_permissions['Ticket Department Transfer'] == 1) ? 1 : 0;
    $assign_staff_permission = ($this->ticket_permissions['Assign Ticket To Staff'] == 1) ? 1 : 0;
    $edit_own_time = ($this->ticket_permissions['Edit Own Time'] == 1) ? 1 : 0;
}
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/ticket-ticketdetail.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($per_viewticket){
        if($isstaffdisable){
            if($this->ticketdetail){
            $document = JFactory::getDocument();
            $document->addScript('administrator/components/com_jssupportticket/include/js/jquery_idTabs.js');
            $document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
            // $document->addScript('components/com_jssupportticket/include/js/timer.jquery.js');
            JText::script('JS_ERROR_FILE_SIZE_TO_LARGE');
            JText::script('JS_ERROR_FILE_EXT_MISMATCH'); ?>

        <script language="javascript">
            function validate_form(f) {
                if (document.formvalidator.isValid(f)) {
                    f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo JSession::getFormToken(); ?>';//send token
                }
                else {
                    alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                    return false;
                }
                return true;
            }
        </script>
        <script type="text/javascript">
            //
            var timer_flag = 0;
            var seconds = 0;
            function changeTimerStatus(val) {
                if(timer_flag == 2){// to handle stopped timer
                        return;
                }
                if(!jQuery('span.timer-button.cls_'+val).hasClass('selected')){
                    jQuery('span.timer-button').removeClass('selected');
                    jQuery('span.timer-button.cls_'+val).addClass('selected');
                    if(val == 1){
                        if(timer_flag == 0){
                            jQuery('div.timer').timer({format: '%H:%M:%S'});
                        }
                        timer_flag = 1;
                        jQuery('div.timer').timer('resume');
                    }else if(val == 2) {
                         jQuery('div.timer').timer('pause');
                    }else{
                         jQuery('div.timer').timer('remove');
                        timer_flag = 2;
                    }
                }
            }

            function showEditTimerPopup(){
                jQuery('form#jsst-time-edit-form').hide();
                jQuery('form#jsst-reply-form').hide();
                jQuery('form#jsst-note-edit-form').hide();
                jQuery('div.edit-time-popup').show();
                jQuery('span.timer-button').removeClass('selected');
                if(timer_flag != 0){
                    jQuery('div.timer').timer('pause');
                }
                ex_val = jQuery('div.timer').html();
                jQuery('input#edited_time').val('');
                jQuery('input#edited_time').val(ex_val.trim());
                jQuery('div.jsst-popup-background').show();
                jQuery('div.jsst-popup-wrapper').slideDown('slow');
            }

            function updateTimerFromPopup(){
                val = jQuery('input#edited_time').val();
                arr = val.split(':', 3);
                jQuery('div.timer').html(val);
                jQuery('div.jsst-popup-background').hide();
                jQuery('div.jsst-popup-wrapper').slideUp('slow');
                seconds = parseInt(arr[0])*3600 + parseInt(arr[1])*60 + parseInt(arr[2]);
                if(seconds < 0){
                    seconds = 0;
                }
                jQuery('div.timer').timer('remove');
                jQuery('div.timer').timer({
                                            format: '%H:%M:%S',
                                            seconds: seconds,
                                            });
                jQuery('div.timer').timer('pause');
                timer_flag = 1;
                var desc = jQuery('#ttt_desc').val();
                jQuery('input#timer_edit_desc').val(desc);
            }

            jQuery(document).ready(function ($) {
                jQuery.noConflict();
                jQuery( "form#adminForm" ).submit(function(e) {
                    if(timer_flag != 0){
                        jQuery('input#timer_time_in_seconds').val(jQuery('div.timer').data('seconds'));
                    }
                });
               jQuery("div#userpopupblack, span.close-history").click(function (e) {
                    jQuery("div#userpopup").slideUp('slow');
                    jQuery("div#userpopupforchangepriority").slideUp('slow');
                    setTimeout(function () {
                        jQuery('div#userpopupblack').hide();
                    }, 700);
                });
               jQuery("div#agenttransferblack, span.close-history").click(function (e) {
                    jQuery("div#popupforagenttransfer").slideUp('slow');
                    setTimeout(function () {
                        jQuery('div#agenttransferblack').hide();
                    }, 700);
                });
               jQuery("div#departmenttransferblack, span.close-history").click(function (e) {
                    jQuery("div#popupfordepartmenttransfer").slideUp('slow');
                    setTimeout(function () {
                        jQuery('div#departmenttransferblack').hide();
                    }, 700);
                });
               jQuery("div#internalnoteblack, span.close-history").click(function (e) {
                    jQuery("div#popupforinternalnote").slideUp('slow');
                    setTimeout(function () {
                        jQuery('div#internalnoteblack').hide();
                    }, 700);
                });

                jQuery("div.popup-header-close-img,div.jsst-popup-background,input#cancel,input#close-pop").click(function (e) {
                    jQuery("div.jsst-popup-wrapper").slideUp('slow');
                    jQuery("div.jsst-merge-popup-wrapper").slideUp('slow');
                    setTimeout(function () {
                        jQuery('div.jsst-popup-background').hide();
                    }, 700);
                });

                // private credentials
                jQuery(document).on('submit','#js-ticket-usercredentails-form',function(e){
                    e.preventDefault(); // avoid to execute the actual submit of the form.
                    var fdata = jQuery(this).serialize(); // serializes the form's elements.
                    jQuery.post("index.php?option=com_jssupportticket&c=privatecredentials&task=storeprivatecredentials&<?php echo JSession::getFormToken(); ?>=1", {formdata_string:fdata}, function (data) {
                        if(data){ // ajax executed
                            var return_data = jQuery.parseJSON(data);
                            if(return_data.status == 1){
                                jQuery('.private-crendentials-add-new-popup').show();
                                jQuery('.js-ticket-usercredentails-form-wrap').hide();
                                jQuery('.js-ticket-usercredentails-credentails-wrp').append(return_data.content);
                                jQuery('.js-ticket-usercredentails-credentails-wrp').show();
                            }else{
                                alert(return_data.error_message);
                            }
                        }
                    });
                });

                jQuery(document).delegate("#ticketidcopybtn", "click", function(){
                    var temp = jQuery("<input>");
                    jQuery("body").append(temp);
                    temp.val(jQuery("#ticketid").val()).select();
                    document.execCommand("copy");
                    temp.remove();
                    jQuery("#ticketidcopybtn").text(jQuery("#ticketidcopybtn").attr('success'));
                });
            });

            // private crredentials
            function addEditCredentail(ticketid, uid, cred_id = 0, cred_data = ''){
                jQuery.post("index.php?option=com_jssupportticket&c=privatecredentials&task=getformforprivatecredentials&<?php echo JSession::getFormToken(); ?>=1", {ticketid: ticketid, cred_id: cred_id, cred_data: cred_data, uid: uid}, function (data) {
                    if(data){ // ajax executed
                        var return_data = jQuery.parseJSON(data);
                        jQuery('.private-crendentials-add-new-popup').hide();
                        jQuery('.js-ticket-usercredentails-credentails-wrp').hide();
                        jQuery('.js-ticket-usercredentails-form-wrap').show();
                        jQuery('.js-ticket-usercredentails-form-wrap').html(return_data);
                        if(cred_id != 0){
                            jQuery('#js-ticket-usercredentails-single-id-'+cred_id).remove();
                        }
                    }
                });
            }

            function getCredentails(ticketid){
                jQuery.post("index.php?option=com_jssupportticket&c=privatecredentials&task=getprivatecredentials&<?php echo JSession::getFormToken(); ?>=1", {ticketid : ticketid}, function (data) {
                    if(data){ // ajax executed
                        var return_data = jQuery.parseJSON(data);
                        if(return_data.status == 1){
                            jQuery('div#js-private-crendentials-back').show();
                            jQuery('div#js-private-crendentials-popup').slideDown('slow');
                            jQuery('.js-ticket-usercredentails-form-wrap').hide();
                            if(return_data.content != ''){
                                jQuery('.js-ticket-usercredentails-credentails-wrp').html('');
                                jQuery('.js-ticket-usercredentails-credentails-wrp').append(return_data.content);
                                jQuery('.js-ticket-usercredentails-credentails-wrp').show();
                                jQuery('.private-crendentials-add-new-popup').show();
                            }
                        }
                    }
                });
            }

            function removeCredentail(cred_id){
                var params = {cred_id:cred_id};
                <?php
                if(!$this->user && isset($this->ticketdetail->id)){
                    ?>
                    params.email = "<?php echo $this->ticketdetail->email; ?>";
                    params.ticketrandomid = "<?php echo $this->ticketdetail->ticketid; ?>";
                    <?php
                }
                ?>

                jQuery.post("index.php?option=com_jssupportticket&c=privatecredentials&task=removePrivateCredential&<?php echo JSession::getFormToken(); ?>=1", params, function (data) {
                    if(data){ // ajax executed
                        if(cred_id != 0){
                            jQuery('#js-ticket-usercredentails-single-id-'+cred_id).remove();
                        }
                    }
                });
                return false;
            }
            function closeCredentailsForm(ticketid){
                getCredentails(ticketid);
            }

            // end private credentials

            function showPopupAndFillValues(id,pfor) {
                jQuery('div.edit-time-popup').hide();
                if(pfor == 1){
                    jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getReplyDataByID&<?php echo JSession::getFormToken(); ?>=1", {val: id}, function (data) {
                        if (data) {
                            jQuery('div.popup-header-text').html("<?php echo jText::_('Edit Reply');?>");
                            d = jQuery.parseJSON(data);
                            tinyMCE.get('jsticket_replytext').execCommand('mceSetContent', false, d.message);
                            jQuery('div.edit-time-popup').hide();
                            jQuery('form#jsst-time-edit-form').hide();
                            jQuery('form#jsst-note-edit-form').hide();
                            jQuery('form#jsst-reply-form').show();
                            jQuery('input#reply-replyid').val(id);
                            jQuery('div.jsst-popup-background').show();
                            jQuery('div.jsst-popup-wrapper').slideDown('slow');
                        }
                    });
                }else if(pfor == 2){
                    jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTimeByReplyID&<?php echo JSession::getFormToken(); ?>=1", {val: id}, function (data) {
                        if (data) {
                            jQuery('div.popup-header-text').html("<?php echo jText::_('Edit Time');?>");
                            d = jQuery.parseJSON(data);
                            jQuery('div.edit-time-popup').hide();
                            jQuery('form#jsst-reply-form').hide();
                            jQuery('form#jsst-note-edit-form').hide();
                            jQuery('div.system-time-div').hide();
                            jQuery('form#jsst-time-edit-form').show();
                            jQuery('input#reply-replyid').val(id);
                            jQuery('div.jsst-popup-background').show();
                            jQuery('div.jsst-popup-wrapper').slideDown('slow');
                            jQuery('input#edited_time').val(d.time);
                            tinyMCE.get('edit_reason').execCommand('mceSetContent', false, d.desc);
                            if(d.conflict == 1){
                                jQuery('div.system-time-div').show();
                                jQuery('input#time-confilct').val(d.conflict);
                                jQuery('input#systemtime').val(d.systemtime);
                                jQuery('select#time-confilct-combo').val(0);
                            }
                        }
                    });
                }else if(pfor == 3){
                    jQuery.post("index.php?option=com_jssupportticket&c=note&task=getTimeByNoteID&<?php echo JSession::getFormToken(); ?>=1", {val: id}, function (data) {
                        console.log(data);
                        if (data) {
                            jQuery('div.popup-header-text').html("<?php echo jText::_('Edit Time');?>");
                            d = jQuery.parseJSON(data);
                            jQuery('div.edit-time-popup').hide();
                            jQuery('form#jsst-reply-form').hide();
                            jQuery('form#jsst-note-edit-form').show();
                            jQuery('form#jsst-time-edit-form').hide();
                            jQuery('div.system-time-div').hide();
                            jQuery('input#note-noteid').val(id);
                            jQuery('div.jsst-popup-background').show();
                            jQuery('div.jsst-popup-wrapper').slideDown('slow');
                            jQuery('input#edited_time').val(d.time);
                            tinyMCE.get('t_desc').execCommand('mceSetContent', false, d.desc);
                            if(d.conflict == 1){
                                jQuery('div.system-time-div').show();
                                jQuery('input#time-confilct').val(d.conflict);
                                jQuery('input#systemtime').val(d.systemtime);
                                jQuery('select#time-confilct-combo').val(0);
                            }
                        }
                    });
                }else if(pfor == 4){
                    jQuery('div#jsjob_installer_waiting_div').show();
                    var ticketid = id;
                    jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo JSession::getFormToken(); ?>=1", {ticketid:ticketid}, function (data) {
                        if (data) {
                            jQuery('div#jsjob_installer_waiting_div').hide();
                            var d = JSON.parse(data);
                            if(d['status'] == 1){
                                jQuery('div.jsst-popup-background').show();
                                jQuery('div.jsst-merge-popup-wrapper').slideDown('slow');
                                jQuery("div#js-ticket-merge-ticket-wrp").html(d['data']);
                            }
                        }
                    });
                }

                return false;
            }
            function updateticketlist(pagenum,ticketid){
                jQuery('div#jsjob_installer_waiting_div').show();
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo JSession::getFormToken(); ?>=1", {ticketid:ticketid,ticketlimit:pagenum}, function (data) {
                    if(data){
                        var d = JSON.parse(data);
                        jQuery('div#jsjob_installer_waiting_div').hide();
                        if(d['status'] == 1){
                            jQuery("div#js-ticket-merge-ticket-wrp").html("");
                            jQuery("div#js-ticket-merge-ticket-wrp").html(d['data']);
                        }
                    }
                });
            }
            // ////////////////////////////////////////////////////////////////////////////
            //more actions
            jQuery(document).ready(function() {
                jQuery('a[href="#"]').click( function(e) {
                    e.preventDefault();
                });
                //more actions
                jQuery("a#jstkmoreactions").click(function(e){
                    jQuery("div#tk-more-actions").slideToggle();
                });
                //more detail
                jQuery("a#tk-show-moredetail").click(function(e){
                    jQuery("div#tk-moredetail-data").slideToggle();
                    jQuery("img.js-showdetail").toggleClass("js-hidedetail");
                });
                //History Popup
                jQuery("a#jstkhistory").click(function(e){
                    jQuery('div#js-history-back').show();
                    jQuery('div#js-history-popup').slideDown('slow');
                });
                jQuery('div#js-history-back,span#close-img,span.close-history').click(function(){
                   jQuery('div#js-history-popup').slideUp('slow');
                   jQuery('div#js-private-crendentials-popup').slideUp('slow');
                   jQuery("div#userpopupforchangepriority").slideUp('slow');
                   jQuery("div#popupfordepartmenttransfer").slideUp('slow');
                   jQuery("div#popupforagenttransfer").slideUp('slow');
                   jQuery("div#popupforinternalnote").slideUp('slow');
                   setTimeout(function () {
                       jQuery('div#js-history-back').hide();
                       jQuery('div#js-private-crendentials-back').hide();
                    }, 700);
                });
                
                jQuery(document).delegate("#close-pop", "click", function (e) {
                    jQuery("div#mergeticketselection").fadeOut();
                    jQuery("div#js-ticket-merge-ticket-wrp").html("");
                    jQuery("div#popup-record-data").slideUp('slow');
                    jQuery("div.jsst-popup-background").hide();

                });
                
                jQuery("a#changepriority").click(function (e) {
                    e.preventDefault();
                    jQuery("div#userpopupforchangepriority").slideDown('slow');
                    jQuery('div#userpopupblack').show();
                });
                jQuery("a#assigntostaff").click(function (e) {
                    e.preventDefault();
                    jQuery("div#popupforagenttransfer").slideDown('slow');
                    jQuery('div#agenttransferblack').show();
                });
                jQuery("a#departmenttransfer").click(function (e) {
                    e.preventDefault();
                    jQuery("div#popupfordepartmenttransfer").slideDown('slow');
                    jQuery('div#departmenttransferblack').show();
                });
                jQuery("a#postinternalnote").click(function (e) {
                    e.preventDefault();
                    jQuery("div#popupforinternalnote").slideDown('slow');
                    jQuery('div#internalnoteblack').show();
                });

                jQuery(document).delegate("#ticketpopupsearch",'submit', function (e) {
                    jQuery('div#jsjob_installer_waiting_div').show();
                    var ticketid = jQuery("#ticketidformerge").val();
                    e.preventDefault();
                    var name = jQuery("input#name").val();
                    var email = jQuery("input#email").val();
                    jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo JSession::getFormToken(); ?>=1",{name: name, email: email,ticketid:ticketid}, function (data) {
                        var d = JSON.parse(data);
                        jQuery('div#jsjob_installer_waiting_div').hide();
                        if (d['data']) {
                            jQuery("div#js-ticket-merge-ticket-wrp").html(d['data']);
                        }
                    });//jquery closed
                });
            });
                
            function getmergeticketid(secondaryid, primaryid){
                jQuery('div#jsjob_installer_waiting_div').show();
                if(primaryid == 0){
                    primaryid =  jQuery("#mergeticketid").val();
                }else{
                    jQuery("#mergeticketid").val(primaryid);
                }
                if(secondaryid == primaryid){
                    alert("Primary id must be differ from merge ticket id");
                    return false;
                }
                jQuery("#mergeticketselection").hide();
                getTicketdataForMerging(secondaryid,primaryid);
            }
            
            function getTicketdataForMerging(secondaryid,primaryid){
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getLatestReplyForMerging&<?php echo JSession::getFormToken(); ?>=1",{secondaryid:secondaryid,primaryid:primaryid},function(data){
                    if(data){
                        jQuery('div#jsjob_installer_waiting_div').hide();
                        var d = JSON.parse(data);
                        jQuery('div.jsst-merge-popup-wrapper').slideDown('slow');
                        jQuery('div#js-ticket-merge-ticket-wrp').html("");
                        jQuery("div#js-ticket-merge-ticket-wrp").html(d);
                    }
                });
            }
            function closePopup(){
                setTimeout(function () {
                    jQuery('div#js-private-crendentials-back,div#js-private-crendentials-popup').hide();
                    jQuery('div.jsst-popup-background,div#js-history-popup').hide();
                    jQuery('div#userpopupblack,div#js-history-back').hide();
                    jQuery('div#agenttransferblack,div#js-history-back').hide();
                    }, 700);
                
                jQuery('div.jsst-popup-wrapper').slideUp('slow');
                jQuery('div#userpopupforchangepriority').slideUp('slow');
                jQuery("div#popupfordepartmenttransfer").slideUp('slow');
                jQuery("div#popupforinternalnote").slideUp('slow');
                jQuery("div#popupforagenttransfer").slideUp('slow');
                jQuery('div#userpopup,div#js-history-popup').slideUp('slow');
                jQuery('div#js-private-crendentials-popup').slideUp('slow');

            }

            function formField(){
                jQuery('div#jsjob_installer_waiting_div').show();
                jQuery("#name").val("");
                jQuery("#email").val("");
                jQuery("#ticketpopupsearch").submit();
            }  
        </script>
        <?php 
            $yesno = array(
            '0' => array('value' => '1',
                'text' => JText::_('Yes')),
            '1' => array('value' => '0',
                'text' => JText::_('No')),);
             $time_confilct_combo = JHTML::_('select.genericList', $yesno, 'time-confilct-combo', 'class="inputbox" ' . '', 'value', 'text', '');
        ?>
        <div id="js-history-back" style="display:none"> </div>
        <div id="js-history-popup" style="display:none">
            <div id="js-history-head">
                <span class="js-title"><?php echo JText::_('Ticket History'); ?></span>
                <span class="js-image" id="close-img"></span>
            </div>
            <div class="js-ticket-history-table-wrp">
                <table class="table js-table-striped">
                    <thead>
                      <tr>
                        <th class="js-ticket-textalign-center"><?php echo JText::_('Date'); ?></th>
                        <th class="js-ticket-textalign-center"><?php echo JText::_('Time'); ?></th>
                        <th class=""><?php echo JText::_('Message Logs'); ?></th>
                      </tr>
                    </thead>
                    <tbody class="js-ticket-ticket-history-body">
                        <?php if(isset($this->tickethistory))
                            foreach ($this->tickethistory as $history) { ?>
                              <tr>
                                <td class="js-ticket-textalign-center"><?php echo JHtml::_('date',$history->datetime,'Y-m-d'); ?></td>
                                <td class="js-ticket-textalign-center"><?php echo JHtml::_('date',$history->datetime,'H:i:s'); ?></td>
                                <?php
                                    if ($history->level == 1) //admin
                                        $color = "blue";
                                    elseif ($history->level == 2) //staff
                                        $color = "orange";
                                    else  //user
                                        $color = "black";
                                ?>
                                <td class="" style="color:<?php echo $color; ?>"><?php echo $history->message; ?></td>
                              </tr>
                            <?php } ?>
                    </tbody>
                </table>
                <div class="js-ticket-priorty-btn-wrp">
                    <button type="button" role="button" class="js-ticket-priorty-cancel" onclick="closePopup();"><?php echo JText::_('Cancel');?></button>
                </div>
            </div>
        </div>
        <!-- private-crendentials popup start -->
        <div id="js-private-crendentials-back" style="display:none"> </div>
        <div id="js-private-crendentials-popup" style="display:none">
            <div id="js-private-crendentials-head">
                <span class="js-title"><?php echo JText::_('Private Credentials'); ?></span>
                <span class="js-image" id="close-img"></span>
            </div>
            <div class="js-ticket-usercredentails-credentails-wrp">
            </div>
            <!--  Add new credentials -->
            <?php $credential_add_permission = false;
            if($this->ticketdetail->status != 4 && $this->ticketdetail->status != 5){
                if($this->user->getStaffId()){
                    $credential_add_permission = $this->ticket_permissions['Add Credentials'];
                }
                // elseif(current_user_can('manage_options')){
                //     $credential_add_permission = true;
                // }
                elseif($this->user->getId() == $this->ticketdetail->uid){
                    $credential_add_permission = true;
                }
            } ?>
            <?php if($credential_add_permission){ ?>
                <div class="private-crendentials-add-new-popup">
                    <div class="js-ticket-edit-form-wrp">
                        <input type="button" class="js-ticket-priorty-cancel"  value="<?php echo JText::_('Add New Credential'); ?>" onclick="addEditCredentail(<?php echo $this->ticketdetail->id;?>,<?php echo $this->user->getId();?>);" />
                    </div>
                </div>
            <?php } ?>
            <div class="js-ticket-usercredentails-form-wrap" >
            </div>
        </div>
        <!-- private crediantional popup end -->
        <div class="jsst-popup-background" style="display:none;" ></div>
        <div class="jsst-merge-popup-wrapper" style="display:none;" id="mergeticketselection">
            <?php /* <div class="jsst-popup-header" >
                <div class="popup-header-text" >
                   <?php echo jText::_('Merge Ticket')?>
                </div>
                <div class="popup-header-close-img" id="close-pop"></div>
            </div> */?>
            <div id="js-ticket-merge-ticket-wrp"></div>
        </div>
        <div class="jsst-popup-wrapper" style="display:none;" >
            <div class="jsst-popup-header" >
                <div class="popup-header-text" >
                    <?php echo jText::_('Edit Timer')?>
                </div>
                <div class="popup-header-close-img" >
                </div>
            </div>
            <div class="edit-time-popup" style="display:none;" >
                <div class="js-ticket-edit-form-wrp">
                    <div class="js-ticket-edit-field-title">
                        <?php echo jText::_('Time'); ?>&nbsp;<font color="red">*</font>
                    </div>
                    <div class="js-ticket-edit-field-wrp">
                        <input class="inputbox js-ticket-edit-field-input" type="text" name="edited_time" id="edited_time" size="40" maxlength="255" value="" />
                    </div>
                    <div class="js-ticket-edit-field-title">
                        <?php echo jText::_('Reason For Editing the timer'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp">
                        <textarea name="ttt_desc" id="ttt_desc" cols="60" rows="20" style="height: 300px;" >  </textarea>
                    </div>
                    <div class="js-ticket-priorty-btn-wrp">
                        <input type="button" class="js-ticket-priorty-save" name="ok" onclick="updateTimerFromPopup()" value="<?php echo JText::_('Ok'); ?>" />
                        <input type="button" class="js-ticket-priorty-cancel" name="cancel" id="cancel"  value="<?php echo JText::_('Cancel'); ?>" />
                    </div>
                </div>
            </div>

            <form id="jsst-reply-form" style="display:none;" method="post" >
                <div class="js-ticket-edit-form-wrp">
                    <div class="js-ticket-form-field-wrp">
                        <?php
                            $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                            echo $editor->display('jsticket_replytext', '', '', '50', '20', '20', false);
                        ?>
                    </div>        
                </div>
                <div class="js-ticket-priorty-btn-wrp">
                    <input type="submit" class="js-ticket-priorty-save" name="ok" value="<?php echo JText::_('Save'); ?>" />
                    <input type="button" class="js-ticket-priorty-cancel" name="cancel" id="cancel" onclick="closePopup()" value="<?php echo JText::_('Cancel'); ?>" />
                </div>
                <input type="hidden" name="reply-replyid" id="reply-replyid" value="" />
                <input type="hidden" name="reply-tikcetid" id="reply-tikcetid" value="<?php echo $this->ticketdetail->id; ?>" />
                 <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="layout" value="ticketdetail" />
                <input type="hidden" id="task" name="task" value="saveeditedreply" />
                <input type="hidden" name="c" value="ticket" />
                <?php echo JHtml::_('form.token'); ?>
            </form>

            <form id="jsst-time-edit-form" style="display:none" method="post" >
                <div class="js-ticket-edit-form-wrp">
                    <div class="js-ticket-edit-field-title">
                        <?php echo jText::_('Time'); ?>&nbsp;<font color="red">*</font>
                    </div>
                    <div class="js-ticket-edit-field-wrp">
                        <input class="inputbox js-ticket-edit-field-input" type="text" name="edited_time" id="edited_time" size="40" maxlength="255" value="" />
                    </div>
                    <div class="js-ticket-edit-field-title" style="display:none;">
                        <?php echo jText::_('System Time'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp" style="display:none;">
                        <input class="inputbox js-ticket-edit-field-input" type="text" name="systemtime" id="systemtime" size="40" maxlength="255" value="" disabled="disabled" />
                    </div>
                    <div class="js-ticket-edit-field-title" style="display:none;">
                        <?php echo jText::_('Resolve conflict'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp" style="display:none;">
                        <?php echo $time_confilct_combo; ?>
                    </div>
                    <div class="js-ticket-edit-field-title">
                        <?php echo jText::_('Reason For Editing the timer'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp">
                        <?php
                            $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                            echo $editor->display('edit_reason', '', '', '100', '20', '20', false);
                        ?>
                    </div>
                    <div class="js-ticket-priorty-btn-wrp">
                        <input type="submit" class="js-ticket-priorty-save" name="ok" value="<?php echo JText::_('Save'); ?>" />
                        <input type="button" class="js-ticket-priorty-cancel" name="cancel" id="cancel" onclick="closePopup()" value="<?php echo JText::_('Cancel'); ?>" />
                    </div>
                </div> 
                <input type="hidden" name="reply-replyid" id="reply-replyid" value="" />
                <input type="hidden" name="reply-tikcetid" id="reply-tikcetid" value="<?php echo $this->ticketdetail->id; ?>" />
                <input type="hidden" name="time-confilct" id="time-confilct" value="" />
                
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="layout" value="ticketdetail" />
                <input type="hidden" id="task" name="task" value="saveeditedtime" />
                <input type="hidden" name="c" value="ticket" />
                <?php echo JHtml::_('form.token'); ?>
            </form>

            <form id="jsst-note-edit-form" style="display:none" method="post" >
                <div class="js-ticket-edit-form-wrp">
                    <div class="js-ticket-edit-field-title">
                        <?php echo jText::_('Time'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp">
                        <input class="inputbox js-ticket-edit-field-input" type="text" name="edited_time" id="edited_time" size="40" maxlength="255" value="" />
                    </div>
                    <div class="js-ticket-edit-field-title" style="display: none;">
                        <?php echo jText::_('System Time'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp" style="display: none;">
                        <input class="inputbox js-ticket-edit-field-input" type="text" name="systemtime" id="systemtime" size="40" maxlength="255" value="" disabled="disabled" />
                    </div>
                    <div class="js-ticket-edit-field-title">
                        <?php echo jText::_('Reason For Editing the timer'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp">
                        <?php
                            $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                            echo $editor->display('t_desc', '', '', '100', '20', '20', false);
                        ?>
                    </div>
                    <div class="js-ticket-edit-field-title" style="display: none;">
                        <?php echo jText::_('Resolve Conflict'); ?>
                    </div>
                    <div class="js-ticket-edit-field-wrp" style="display: none;">
                        <?php echo $time_confilct_combo; ?>
                    </div>

                    <div class="js-ticket-priorty-btn-wrp">
                        <input type="submit" class="js-ticket-priorty-save" name="ok" value="<?php echo JText::_('Save'); ?>" />
                        <input type="button" class="js-ticket-priorty-cancel" name="cancel" id="cancel" onclick="closePopup()"  value="<?php echo JText::_('Cancel'); ?>" />
                    </div>
                </div>
                <input type="hidden" name="note-tikcetid" id="note-tikcetid" value="<?php echo $this->ticketdetail->id; ?>" />
                <input type="hidden" name="note-noteid" id="note-noteid" value="" />
                <input type="hidden" name="time-confilct" id="time-confilct" value="" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="layout" value="ticketdetail" />
                <input type="hidden" id="task" name="task" value="saveeditedtimenote" />
                <input type="hidden" name="c" value="ticket" />
                <?php echo JHtml::_('form.token'); ?>
            </form>
        </div>
        <div id="jsjob_installer_waiting_div" style="display:none;"></div>
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
                                <?php
                                    $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=".$this->Itemid;
                                    if($isstaff)
                                        $link = "index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff&Itemid=".$this->Itemid;
                                ?>
                                <a href="<?php echo $link; ?>" title="Dashboard">
                                    <?php echo JText::_('My Tickets'); ?>
                                </a>
                            </li>
                            <li>
                                <?php echo JText::_('Ticket Detail')?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div id="tk-detail-wraper">
            <form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
                <div id="message"></div>
                <div id="tk_detail_content_wraper">
                    <div class="js-col-md-12 js-ticket-detail-wrapper"> <!-- Ticket Detail Data Top -->
                        <div class="js-ticket-detail-box"><!-- Ticket Detail Box -->
                            <div class="js-ticket-detail-left">
                                <div class="js-tkt-det-cnt js-tkt-det-info-wrp">
                                    <div class="js-tkt-det-user">
                                        <div class="js-ticket-user-img-wrp">
                                            <img class="js-ticket-staff-img" src="components/com_jssupportticket/include/images/user.png" alt="<?php echo JText::_('New Ticket'); ?>" />
                                        </div>
                                        <div class="js-tkt-det-user-cnt">
                                            <div class="js-ticket-user-name-wrp">
                                                <?php echo $this->ticketdetail->name; ?>
                                            </div>
                                            <div class="js-ticket-user-subject-wrp">
                                                <?php echo $this->ticketdetail->subject; ?>
                                                <div class="js-ticket-user-email-wrp">
                                                    <?php echo $this->ticketdetail->email; ?>
                                                </div>
                                                <div class="js-ticket-user-email-wrp">
                                                    <?php echo $this->ticketdetail->phone; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="js-tkt-go-to-all-wrp">
                                        <a class="js-tkt-go-to-all" href="index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=<?php echo $this->Itemid; ?>"><?php echo JText::_('Show All').' '.JText::_('Tickets'); ?>
                                        </a>
                                    </div>
                                    <div class="js-tkt-det-tkt-msg">
                                        <?php echo $this->ticketdetail->message; ?>
                                    </div>
                                <div class="js-ticket-btn-box">
                                    <?php if($this->ticketdetail->status != 5){ ?>
                                        <?php if($isstaff && $per_ticketmerge && $this->ticketdetail->status != 4){ ?>
                                            <a class="js-button" href="#" onclick="return showPopupAndFillValues(<?php echo $this->ticketdetail->id;?>,4)" alt="<?php echo JText::_('Merge Ticket'); ?>">
                                                <img class="js-button-icon" title="<?php echo JText::_('Merge Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/merge-ticket.png">
                                                <span><?php echo JText::_('Merge Ticket'); ?></span>
                                            </a>
                                        <?php } ?>
                                    <?php if($isstaff){ ?>
                                        <?php if($this->ticketdetail->status != 5 && $this->ticketdetail->status != 4){
                                            $link = 'index.php?option='.$this->option.'&c=ticket&layout=formticket&id='.$this->ticketdetail->id.'&Itemid='.$this->Itemid; ?>
                                                <a class="js-button" href="<?php echo $link; ?>">
                                                    <img class="js-button-icon" title="<?php echo JText::_('Edit Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/edit.png">
                                                    <span><?php echo JText::_('Edit Ticket'); ?></span>
                                                </a>
                                        <?php  }
                                    }?>
                                    <a class="js-button" href="#" onclick="actioncall('<?php if ($this->ticketdetail->status == 4) echo 8; else echo 3; ?>')">
                                        <?php if ($this->ticketdetail->status == 4){  ?>
                                            <img class="js-button-icon" title="<?php echo JText::_('Reopen Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/reopen.png">
                                            <span><?php echo JText::_('Reopen Ticket'); ?></span>
                                        <?php }else{ ?>
                                            <img class="js-button-icon" title="<?php echo JText::_('Close Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/close.png">
                                            <span><?php echo JText::_('Close Ticket'); ?></span>
                                        <?php } ?>
                                    </a>
                                <?php } ?>
                                <!-- Print Ticket -->
                                <?php $link_print = 'index.php?option=' . $this->option . '&c=ticket&layout=print_ticket&id='.$this->ticketdetail->id.'&tmpl=component&print=1'; ?>
                                    <?php if($isstaff){
                                    $print_permission = ($this->ticket_permissions['Print Ticket'] == 1) ? 1 : 0;
                                    if($print_permission){ ?>
                                        <a class="js-button" id="" href="<?php echo $link_print; ?>" target="_blank">
                                                <img class="js-button-icon" title="<?php echo JText::_('Print Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/print.png">
                                                <span><?php echo JText::_('Print'); ?></span>
                                            </a>
                                    <?php }
                                    }elseif($this->config['print_ticket_user'] == 1){ ?>
                                    <a class="js-button" id="" href="<?php echo $link_print; ?>" target="_blank">
                                        <img class="js-button-icon" title="<?php echo JText::_('Print Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/print.png">
                                        <span><?php echo JText::_('Print Ticket'); ?></span>
                                    </a>
                            <?php } ?>
                                <a class="js-button" id="jstkhistory" href="#">
                                    <img class="js-button-icon" title="<?php echo JText::_('Ticket History'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/history.png">
                                    <span><?php echo JText::_('Ticket History'); ?></span>
                                </a>
                                <?php if(!$isstaff && $this->ticketdetail->status != 4 && $this->ticketdetail->status != 5){ ?>
                                    <a class="js-button" href="javascript:void(0);" onclick="getCredentails(<?php echo $this->ticketdetail->id; ?>)" id="jstkprivatecrendentials">
                                        <img class="js-button-icon" title="<?php echo JText::_('Private Credentials'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/private-credentials.png">
                                       <span> <?php echo JText::_('Private Credentials'); ?></span>
                                    </a>
                                <?php } ?>
                            <?php if($isstaff){ ?>
                                <?php if($this->ticketdetail->status != 5){ ?>
                                        <a class="js-button" href="#" onclick="actioncall('<?php if ($this->ticketdetail->lock == 1) echo 12; else echo 11; ?>')">
                                            <?php if ($this->ticketdetail->lock == 1){
                                                $image_title = JText::_('Unlock ticket'); $icon_name='unlock.png';
                                                $text = JText::_('Unlock Ticket');
                                            }else{
                                                $image_title =JText::_('Lock Ticket');$icon_name='lock.png';
                                                $text = JText::_('Lock Ticket');
                                            } ?>
                                            <img class="js-button-icon" title="<?php echo $image_title; ?>" src="components/com_jssupportticket/include/images/ticket-detail/<?php echo $icon_name; ?>">
                                            <span><?php echo $text; ?></span>
                                        </a>

                                        <a class="js-button" href="#" onclick="actioncall('<?php echo 7; ?>')">
                                            <img class="js-button-icon" title="<?php echo JText::_('Ban email and close ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/ban-email-close-ticket.png">
                                            <span><?php echo JText::_('Ban Email and close ticket'); ?></span>
                                        </a>
                                        <a class="js-button" onclick="actioncall('<?php if ($this->isemailban == 2) echo 4; else echo 9; ?>')">
                                            <?php if ($this->isemailban == 2){
                                                $image_title ='Ban Email'; $icon_name='ban.png';
                                                $text = JText::_('Ban Email');
                                            }else{
                                                $image_title ='Unban Email';$icon_name='un-ban.png';
                                                $text = JText::_('Unban Email');
                                            } ?>
                                            <img class="js-button-icon" title="<?php echo JText::_($image_title); ?>" src="components/com_jssupportticket/include/images/ticket-detail/<?php echo $icon_name; ?>">
                                            <span><?php echo $text; ?></span>
                                        </a>
                                        <a class="js-button" onclick="actioncall('<?php if ($this->ticketdetail->isoverdue == 1) echo 13; else echo 6; ?>')">
                                            <?php if ($this->ticketdetail->isoverdue == 1){
                                                $image_title =JText::_('Unmark overdue'); $icon_name='un-over-due.png';
                                                $text = JText::_('Unmark Overdue');
                                            }else{
                                                $image_title =JText::_('Mark overdue');$icon_name='over-due.png';
                                                $text = JText::_('Overdue');
                                            } ?>
                                            <img class="js-button-icon" title="<?php echo $image_title; ?>" src="components/com_jssupportticket/include/images/ticket-detail/<?php echo $icon_name; ?>">
                                            <span><?php echo $text; ?></span>
                                        </a>
                                        <a class="js-button" onclick="actioncall('<?php echo 10; ?>')">
                                            <img class="js-button-icon" title="<?php echo JText::_('Mark In Progress'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/in-progress.png">
                                            <span><?php echo JText::_('Mark in Progress'); ?></span>
                                        </a>
                                        <?php if($this->ticket_permissions['View Credentials']){ ?>
                                            <a class="js-button" href="javascript:void(0);" onclick="getCredentails(<?php echo $this->ticketdetail->id; ?>)" id="jstkprivatecrendentials">
                                                <img class="js-button-icon" title="<?php echo JText::_('Private Credentials'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/private-credentials.png">
                                                <span><?php echo JText::_('Private Credentials'); ?></span>
                                            </a>
                                        <?php } ?>
                                     <div id="userpopupblack" style="display:none;" ></div>
                                    <div id="userpopupforchangepriority" style="display:none;">
                                        <div class="js-ticket-priorty-header">
                                           <?php echo JText::_('Change Priority'); ?><span class="close-history"></span>
                                        </div>
                                        <div class="js-ticket-priorty-fields-wrp">
                                            <div class="js-ticket-select-priorty">
                                                <?php echo $this->lists['priorities']; ?>
                                            </div>
                                        </div>
                                        <div class="js-ticket-priorty-btn-wrp">
                                            <button type="button" class="js-ticket-priorty-save" id="changepriority" onclick="actioncall(1)" ><?php echo JText::_('Save Priority'); ?></button>
                                        </div>
                                    </div>
                                    <!-- assign to staff popup -->
                                    <div id="agenttransferblack" style="display:none;" ></div>
                                    <div id="popupforagenttransfer" style="display:none;">
                                        <div class="jsst-popup-header">
                                            <?php echo JText::_('Assign To Staff'); ?>
                                            <span class="close-history"></span>
                                        </div>
                                        <div class="js-ticket-assign-fields-wrp">
                                            <div class="js-ticket-assign-to-staff-wrp">
                                                <div class="js-ticket-premade-msg-wrp"><!-- Select Department Wrapper -->
                                                    <div class="js-ticket-premade-field-title">
                                                        <?php echo JText::_('Assign to Staff'); ?>
                                                    </div>
                                                    <div class="js-ticket-premade-field-wrp">
                                                        <?php echo $this->lists['staff']; ?>
                                                    </div>
                                                </div>
                                                <div class="js-ticket-text-editor-wrp">
                                                    <div class="js-ticket-text-editor-field-title">
                                                        <?php echo JText::_('Assigning Note'); ?>
                                                    </div>
                                                    <div class="js-ticket-text-editor-field">
                                                        <?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('assigntostaffnote', '', '550', '300', '60', '20', false); ?>
                                                    </div>
                                                </div>

                                        <div class="js-ticket-reply-form-button-wrp">
                                            <input type="button" class="button js-ticket-save-button" onclick="ticketstafftransfer(document.adminForm)" value="<?php echo JText::_('Assign'); ?>" />
                                        </div>
                                    </div>
                                </div>
                                            <!-- end of assigntostaff div -->
                            </div>
                            <!-- assign to department popup -->
                            <div id="departmenttransferblack" style="display:none;" >
                            </div>
                            <div id="popupfordepartmenttransfer" style="display: none;">
                                <div class="jsst-popup-header">
                                    <?php echo JText::_('Change').' '.JText::_('Department'); ?>
                                    <span class="close-history">
                                    </span>
                                </div>
                                <div class="js-ticket-department-fields-wrp">
                                    <div class="js-ticket-department-wrp">
                                        <div class="js-ticket-premade-msg-wrp"><!-- Select Department Wrapper -->
                                            <div class="js-ticket-premade-field-title"><?php echo JText::_('Department'); ?></div>
                                            <div class="js-ticket-premade-field-wrp">
                                                <?php echo $this->lists['departments']; ?>
                                            </div>
                                        </div>
                                        <div class="js-ticket-text-editor-wrp">
                                            <div class="js-ticket-text-editor-field-title">
                                                <?php echo JText::_('Reason for department transfer'); ?>
                                            </div>
                                            <div class="js-ticket-text-editor-field">
                                                <?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('departmenttranfer', '', '550', '300', '60', '20', false);?>

                                            </div>
                                        </div>
                                        <div class="js-ticket-reply-form-button-wrp">
                                            <input type="button" class="button js-ticket-save-button" onclick="ticketdepartmenttransfer(document.adminForm)" value="<?php echo JText::_('Save Department'); ?>" />
                                        </div>
                                    </div>
                                </div> <!-- end of departmenttransfer div -->
                            </div>
                            <!-- internal note popup -->
                            <div id="internalnoteblack" style="display:none;" ></div>
                            <div id="popupforinternalnote" style="display:none;">
                                <div class="jsst-popup-header">
                                    <div class="popup-header-text">
                                        <?php echo JText::_('Internal Note'); ?>
                                    </div>
                                    <span class="close-history">
                                    </span>
                                </div>
                                <div class="js-ticket-internalnote-fields-wrp">  <!--  postinternalnote Area   -->
                                    <div id="postinternalnote" class="js-ticket-post-internal-note-wrp">
                                        <div class="jsst-ticket-detail-timer-wrapper">
                                            <div class="timer-left" >
                                                <?php /* <?php echo jText::_('Time Track'); ?> */?>
                                                <div class="timer-total-time" >
                                                    <?php
                                                        $time = $this->time_taken;
                                                        $hours = floor($time / 3600);
                                                        $mins = floor($time / 60 % 60);
                                                        $secs = floor($time % 60);
                                                        echo jText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                                    ?>
                                                </div>
                                            </div>
                                            <div class="timer-right" >

                                                <div class="timer" >
                                                    00:00:00
                                                </div>
                                                <div class="timer-buttons" >
                                                    <?php { ?>
                                                        <span class="timer-button" onclick="showEditTimerPopup()" >
                                                            <img src="components/com_jssupportticket/include/images/ticket-detail/edit-time-1.png"/>
                                                        </span>
                                                    <?php } ?>
                                                    <span class="timer-button cls_1" onclick="changeTimerStatus(1)" >
                                                        <img src="components/com_jssupportticket/include/images/ticket-detail/play-time-1.png"/>
                                                    </span>
                                                    <span class="timer-button cls_2" onclick="changeTimerStatus(2)" >
                                                        <img src="components/com_jssupportticket/include/images/ticket-detail/pause-time-1.png"/>
                                                    </span>
                                                    <span class="timer-button cls_3" onclick="changeTimerStatus(3)" >
                                                        <img src="components/com_jssupportticket/include/images/ticket-detail/stop-time-1.png"/>
                                                    </span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="timer_time_in_seconds" id="timer_time_in_seconds" value=""  />
                                            <input type="hidden" name="timer_edit_desc" id="timer_edit_desc" value=""  />
                                        </div>
                                        <div class="js-ticket-internalnote-wrp"><!-- Ticket Tittle -->
                                            <div class="js-ticket-internalnote-field-title"><?php echo JText::_('Note title'); ?>:&nbsp;<font color="red">*</font></div>
                                            <div class="js-ticket-internalnote-field-wrp">
                                                <input class="inputbox required js-ticket-internalnote-input" type="text" id="notetitle" name="notetitle" size="40" maxlength="255" value="" />
                                            </div>
                                        </div>
                                        <div class="js-ticket-text-editor-wrp">
                                            <div class="js-ticket-text-editor-field-title"><?php echo JText::_('Type Internal Note'); ?> </div>
                                            <div class="js-ticket-text-editor-field"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);echo $editor->display('internalnote', '', '550', '300', '60', '20', false);?></div>
                                        </div>
                                        <div class="js-attachment-wrp"><!-- Attachments -->
                                            <div class="js-form-title"><?php echo JText::_('Attachments'); ?></div>
                                            <div class="js-form-value js-attachment-files-wrp">
                                                <div id="js_attachment_files_internalnote" class="js-attachment-files">
                                                    <span class="js-attachment-file-box">
                                                        <input type="file" class="inputbox js-attachment-inputbox js-form-input-field-attachment" name="noteattachment" onchange="uploadfileNote(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                            <span class='js-attachment-remove'></span>
                                                    </span>
                                                </div>
                                                <div id="js-attachment-option">
                                                    <?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="js-ticket-reply-form-button-wrp"><!-- Close On Reply -->
                                            <input type="button" class="js-ticket-save-button"  onclick="ticketinternalnote(true)" value="<?php echo JText::_('Post Internal Note'); ?>" />
                                        </div>
                                    </div>
                                    </div> <!-- end of postinternalnote div -->
                                </div>
                            <?php }
                            } ?>
                        </div>
                            <?php } ?>
                        <!-- data edit -->
                        </div>
                        <!-- Internal Notes Portion -->
                        <div class="js-ticket-post-reply-wrapper"><!-- Ticket Post Replay -->
                            <?php
                                if ($isstaff && $this->ticketdetail->status != 5 && empty($this->ticketnotes)) { ?>
                                    <div class="js-ticket-thread-heading"><?php echo JText::_('Internal Note'); ?></div>
                            <?php } ?>
                            <?php
                            if (!empty($this->ticketnotes)) { ?>
                                <div class="js-ticket-thread-heading"><?php echo JText::_('Internal Note'); ?></div>
                                    <?php foreach ($this->ticketnotes AS $row) {  ?>
                                        <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                            <div class="js-ticket-detail-left js-ticket-white-background"><!-- Left Side Image -->
                                                <div class="js-ticket-user-img-wrp">
                                                    <?php if ($row->staffphoto) { ?>
                                                        <img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                                    <?php } else { ?>
                                                        <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                                    <?php } ?>
                                                </div>
                                            </div>
                                            <div class="js-ticket-detail-right js-ticket-background"><!-- Right Side Ticket Data -->
                                                <div class="js-ticket-rows-wrapper">
                                                    <div class="js-ticket-rows-wrp" >
                                                        <?php $name=$row->staffname;
                                                        if($row->staffid == 0){
                                                            $name = $row->from;
                                                        } ?>
                                                        <div class="js-ticket-field-value name">
                                                            <?php echo $name; ?>
                                                        </div>
                                                    </div>
                                                    <div class="js-ticket-rows-wrp">
                                                        <div class="js-ticket-row">
                                                            <div class="js-ticket-field-value">
                                                               <?php echo $row->title; ?>
                                                            </div>
                                                        </div>
                                                        <div class="js-ticket-row">
                                                            <div class="js-ticket-field-value">
                                                               <?php echo $row->note; ?>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <?php
                                                    if($row->filesize > 0 && !empty($row->filename)){
                                                        $notepath = 'index.php?option=com_jssupportticket&c=note&task=getdownloadbyid&id='.$row->id.'&'. JSession::getFormToken() . '=1'; ?>
                                                        <div class="js-ticket-attachments-wrp">
                                                            <div class="js_ticketattachment">
                                                                <span class="js-ticket-download-file-title">
                                                                    <?php echo $row->filename;?>
                                                                </span>
                                                                <a class="js-download-button" target="_blank" href="<?php echo $notepath;?>">
                                                                    <img class="js-ticket-download-img" src="components/com_jssupportticket/include/images/download.png">
                                                                </a>
                                                            </div>
                                                        </div>
                                                    <?php } ?>
                                                </div>
                                                <div class="js-ticket-time-stamp-wrp">
                                                    <span class="js-ticket-ticket-created-date">
                                                        <?php echo JHtml::_('date',$row->created,"l F d, Y");?>
                                                    </span>

                                                    <div class="js-ticket-edit-options-wrp">
                                                        <?php
                                                            $hours = floor($row->usertime / 3600);
                                                            $mins = floor($row->usertime / 60 % 60);
                                                            $secs = floor($row->usertime % 60);
                                                            $time = jText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                                            ?>
                                                            <?php if($this->staff_permissions['Edit Time'] == 1){ ?>
                                                                <a class="js-button" href="#" onclick="return showPopupAndFillValues(<?php echo $row->id;?>,3)" >
                                                                    <img src="components/com_jssupportticket/include/images/edit-time.png" />
                                                                    <?php echo jText::_('Edit Time');?>
                                                                </a>
                                                        <?php } ?>
                                                        <span class="js-ticket-thread-time"><?php echo $time; ?></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    <?php } ?>

                            <?php } ?>
                        </div>

                    <!-- internal note btn -->
                    <?php if($isstaff && $this->ticketdetail->status != 5){ ?>
                        <?php if ($post_internal_note_permission == 1) { ?>
                            <span class="js-ticket-thread-add-btn">
                                <a class="js-ticket-thread-add-btn-link" href="#" id="postinternalnote">
                                    <img class="js-ticket-tab-img" id="internal-note_b" src="components/com_jssupportticket/include/images/ticket-detail/edit-time.png"/>
                                    <?php echo JText::_('Post Internal Note'); ?>
                                </a>
                            </span>
                            <?php } ?>
                            <?php } ?>
                        <div class="js-ticket-thread-heading">
                            <?php echo JText::_('Ticket Thread'); ?>
                        </div>
                        <div class="js-ticket-thread internal-note"><!-- Left Side Image -->
                                <div class="js-ticket-user-img-wrp">
                                     <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                </div>
                                <div class="js-ticket-thread-cnt">
                                    <div class="js-ticket-user-name-wrp">
                                        <span><?php echo $this->ticketdetail->name; ?></span>
                                    </div>
                                    <div class="js-ticket-user-email-wrp">
                                        <?php echo $this->ticketdetail->email; ?>
                                    </div>
                                    <div class="js-ticket-user-email-wrp">
                                        <?php echo $this->ticketdetail->message; ?>
                                    </div>
                                    <?php
                                    if (isset($this->ticketattachment[0]->filename)) { ?>
                                        <div class="js-ticket-attachments-wrp">
                                            <?php foreach ($this->ticketattachment as $attachment) {
                                                echo '
                                                    <div class="js_ticketattachment">
                                                        <span class="js-ticket-download-file-title">
                                                            ' . $attachment->filename  . '
                                                        </span>
                                                        <a class="js-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&'. JSession::getFormToken() .'=1">'.
                                                            JText::_("Download").'
                                                        </a>
                                                    </div>';
                                            }
                                            echo'
                                                <a class="js-all-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=downloadall&id='.$attachment->id.'&'. JSession::getFormToken() .'=1">'.JText::_("Download All").'</a>';?>
                                           </div>
                                    <?php
                                    } ?>
                                    <div class="js-ticket-time-stamp-wrp">
                                        <span class="js-ticket-ticket-created-date">
                                            <?php echo JHtml::_('date',$this->ticketdetail->created,"l F d, Y");?>
                                        </span>
                                    </div>
                                </div>
                        </div>
                         <!--replay a message  -->
                        <div class="js-ticket-post-reply-wrapper">
                            <!-- Ticket Replies -->
                            <?php  if (!empty($this->ticketreplies)) { ?>
                            <?php $i = 0;
                            foreach ($this->ticketreplies AS $row) {
                                $i++;
                                if($row->staffid <> 0) {
                                    $staffname = $row->staffname;
                                }else{
                                    $staffname = $row->name;
                                } ?>
                                <div class="js-ticket-detail-box js-ticket-post-reply-box"><!-- Ticket Detail Box -->
                                    <!-- Left Side Image -->
                                    <div class="js-ticket-user-img-wrp">
                                            <?php if ($row->staffphoto) { ?>
                                                <img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                            <?php } else { ?>
                                                <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                            <?php } ?>
                                    </div>
                                    <div class="js-ticket-thread-cnt">
                                        <div class="js-ticket-user-name-wrp">
                                           <?php echo $staffname; ?>
                                        </div>
                                        <div class="js-ticket-user-email-wrp">
                                            <?php if ($row->ticketviaemail == 1) { ?>
                                                <?php echo JText::_('Created via email'); ?>
                                            <?php } ?>
                                        </div>
                                        <div class="js-ticket-rows-wrapper">
                                            <div >
                                                <div class="js-ticket-row">
                                                    <div class="js-ticket-field-value">
                                                        <?php $message = $row->message;
                                                        if($row->mergemessage == 1){
                                                            $message = str_replace("cid[]=","id=",$message);
                                                            $message = str_replace("layout=ticketdetails","layout=ticketdetail",$message);
                                                        } ?>
                                                        <?php echo html_entity_decode($message); ?>
                                                    </div>
                                                </div>
                                            </div>
                                            <?php if (!empty($row->attachments)) { ?>
                                                 <?php if (isset($row->attachments)) { ?>
                                                    <div class="js-ticket-attachments-wrp">
                                                        <?php foreach ($row->attachments as $attachment) {
                                                            $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid . '&'. JSession::getFormToken() . '=1';
                                                            echo ' <div class="js_ticketattachment">
                                                                        <span class="js-ticket-download-file-title">'
                                                                            . $attachment->filename . "&nbsp(" . round($attachment->filesize, 2) . " KB)";
                                                                    echo '</span>
                                                                        <a class="js-download-button" target="_blank" href="' . $path . '">
                                                                            '.JText::_('Download').'
                                                                        </a>
                                                                </div>';
                                                        }
                                                            echo'
                                                                <a class="js-all-download-button" target="_blank" href="index.php?option=com_jssupportticket&c=ticket&task=downloadallforreply&id='.$row->id.'&' . JSession::getFormToken() . '=1">'.JText::_('Download All').'
                                                                     </a>';?>
                                                    </div>
                                                <?php } ?>
                                            <?php } ?>
                                        </div>
                                        <div class="js-ticket-time-stamp-wrp">
                                            <span class="js-ticket-ticket-created-date">
                                                 <?php echo JHtml::_('date',$row->created,"l F d, Y");?>
                                            </span>
                                            <?php if($isstaff){ ?>
                                                <div class="js-ticket-edit-options-wrp">
                                                    <?php if($row->staffid != 0){ ?> 
                                                        <?php if($this->staff_permissions['Edit Reply'] == 1 && $row->status != 5 ){  ?>
                                                            <a class="js-button" href="#" onclick="return showPopupAndFillValues(<?php echo $row->id;?>,1)" >
                                                                <img src="components/com_jssupportticket/include/images/edit.png" />
                                                               <?php echo jText::_('Edit Reply');?>
                                                            </a>
                                                        <?php }
                                                    } ?>
                                                    <?php if($row->staffid != 0){ 
                                                        $hours = floor($row->usertime / 3600);
                                                        $mins = floor($row->usertime / 60 % 60);
                                                        $secs = floor($row->usertime % 60);
                                                        $time = jText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                                        ?>
                                                        <?php if($this->staff_permissions['Edit Time'] == 1 && $row->status != 5){ ?>
                                                            <a class="js-button" href="#" onclick="return showPopupAndFillValues(<?php echo $row->id;?>,2)" >
                                                                <img src="components/com_jssupportticket/include/images/edit-time.png" />
                                                                 <?php echo jText::_('Edit Time');?>
                                                            </a>
                                                        <?php } ?>
                                                        <span class="js-ticket-thread-time"><?php echo $time; ?></span>  
                                                    <?php }?>
                                                    
                                                </div>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>

                            <?php }
                        } ?>
                    </div>

                    <div class="js-ticket-tabs-wrapper">
                        <?php if($isstaff && $this->ticketdetail->status != 5){ ?>
                            <!-- Tabs Header -->
                            <!-- Tabs body -->
                            <div class="js-ticket-tabs-body">
                                <?php if ($post_reply_permission == 1){ ?>
                                    <div class="js-ticket-thread-heading">
                                        <?php echo JText::_('Post Reply'); ?>
                                    </div>
                                    <div id="postreply" class="js-ticket-post-reply-wrp selected">
                                        <div class="jsst-ticket-detail-timer-wrapper">
                                            <?php /* <div class="timer-left" >
                                                <?php echo jText::_('Time Track'); ?>
                                            </div> */ ?>
                                            <div class="timer-right" >
                                                <div class="timer-total-time" >
                                                    <?php 
                                                    $time = $this->time_taken;
                                                        $hours = floor($time / 3600);
                                                        $mins = floor($time / 60 % 60);
                                                        $secs = floor($time % 60);
                                                        echo jText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                                    ?>
                                                </div>
                                                <div class="timer" >
                                                    00:00:00
                                                </div>
                                                <div class="timer-buttons" >
                                                    <?php if($edit_own_time == 1){ ?>
                                                        <span class="timer-button" onclick="showEditTimerPopup()" >
                                                            <img src="components/com_jssupportticket/include/images/timer-edit.png"/>
                                                        </span>
                                                    <?php } ?>
                                                    <span class="timer-button cls_1" onclick="changeTimerStatus(1)" >
                                                        <img src="components/com_jssupportticket/include/images/play.png"/>
                                                    </span>
                                                    <span class="timer-button cls_2" onclick="changeTimerStatus(2)" >
                                                        <img src="components/com_jssupportticket/include/images/pause.png"/>
                                                    </span>
                                                    <span class="timer-button cls_3" onclick="changeTimerStatus(3)" >
                                                        <img src="components/com_jssupportticket/include/images/stop.png"/>
                                                    </span>
                                                </div>
                                            </div>
                                            <input type="hidden" name="timer_time_in_seconds" id="timer_time_in_seconds" value=""  />
                                            <input type="hidden" name="timer_edit_desc" id="timer_edit_desc" value=""  />
                                        </div>
                                        <div class="js-ticket-premade-msg-wrp"><!-- Premade Message Wrapper -->
                                            <div class="js-ticket-premade-field-title"><?php echo JText::_('Premade Message'); ?></div>
                                            <div class="js-ticket-premade-field-wrp">
                                                <?php echo $this->lists['premade']; ?>
                                                <span class="js-ticket-apend-radio-btn">
                                                     <input type="checkbox" id="append" class="radiobutton js-ticket-premade-radiobtn" name="append"/>
                                                     <?php echo JText::_('Append'); ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="js-ticket-text-editor-wrp"><!-- Premade Message Editor -->
                                            <div class="js-ticket-text-editor-field-title"><?php echo JText::_('Type Message'); ?></div>
                                            <div class="js-ticket-text-editor-field"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('responce', '', '550', '300', '60', '20', false); ?></div>
                                        </div>
                                        <?php if ($this->isAttachmentPublished) { ?>
                                            <div class="js-attachment-wrp"><!-- Attachments -->
                                                <div class="js-form-title"><?php echo JText::_('Attachments'); ?></div>
                                                <div class="js-form-value js-attachment-files-wrp">
                                                    <div id="js-attachment-files" class="js-attachment-files">
                                                        <span class="js-attachment-file-box">
                                                            <input type="file" class="inputbox js-attachment-inputbox js-form-input-field-attachment" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                            <span class='js-attachment-remove'></span>
                                                        </span>
                                                    </div>
                                                    <div id="js-attachment-option">
                                                        <?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?>
                                                    </div>
                                                    <span id="js-attachment-add"><?php echo JText::_('Add Files'); ?></span>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <?php if (!$this->ticketdetail->staffid) { ?><!-- Assign to me -->
                                            <div class="js-ticket-assigntome-wrp">
                                                <div class="js-ticket-assigntome-field-title"><?php echo JText::_('Assign ticket to staff'); ?></div>
                                                <div class="js-ticket-assigntome-field-wrp">
                                                    <?php 
                                                        if($this->ticketdetail->staffid){
                                                            $checked = '';
                                                        }else{
                                                            $checked = 1;
                                                        }?>
                                                        <input type="checkbox" class="js-ticket-assigntome-checkbox" name="assigntomyself" id ="assigntomyself" value="1" checked="checked"/> &nbsp;
                                                        <label id="forassigntome"><?php echo JText::_('Assign to me'); ?></label>
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <div class="js-ticket-closeonreply-wrp"><!-- Close On Reply -->
                                            <div class="js-ticket-closeonreply-title"><?php echo JText::_('Ticket Status'); ?></div>
                                            <div class="replyFormStatus js-form-title-position-reletive-left">
                                                <input type="checkbox" id="replystatus" class="radiobutton js-ticket-closeonreply-checkbox" name="replystatus" value="4" />
                                                <label id="forcloseonreply" for="replystatus"><?php echo JText::_('Close on reply'); ?></label>
                                            </div>
                                        </div>
                                        <div class="js-ticket-append-signature-wrp js-ticket-append-signature-wrp-full-width"><!-- Append Signature -->
                                            <div class="js-ticket-append-field-title"><?php echo JText::_('Append signature'); ?></div>
                                            <div class="js-ticket-append-field-wrp">
                                        <div class="js-ticket-signature-radio-box">
                                                    <input type="radio" id="appendsignature1" class="radiobutton js-ticket-append-radio-btn" name="appendsignature" value="1" checked />
                                                    <label for="appendsignature1" data-signature="appendsignature1" class="cb-enable selected forownsignature"><?php echo JText::_('Own signature'); ?></label>
                                                </div>
                                                <div class="js-ticket-signature-radio-box">
                                                    <input type="radio" id="appendsignature2" class="radiobutton js-ticket-append-radio-btn" name="appendsignature" value="2" />
                                                    <label for="appendsignature2" data-signature="appendsignature2" class="cb-disable fordepartmentsignature"><?php echo JText::_('Dept.signature'); ?></label>
                                                </div>
                                                <div class="js-ticket-signature-radio-box">
                                                    <input type="radio" id="appendsignature3" class="radiobutton js-ticket-append-radio-btn" name="appendsignature" value="3" />
                                                    <label for="appendsignature3" data-signature="appendsignature3" class="cb-disable fornonesignature"><?php echo JText::_('None'); ?></label>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="js-ticket-reply-form-button-wrp"><!-- Close On Reply -->
                                            <input class="js-ticket-save-button" type="button" onclick="validate_form_department(document.adminForm)" value="<?php echo JText::_('Post reply'); ?>"/>
                                        </div>
                                    </div>
                                <?php } ?>
                                <!-- Post Internal-Note Wrp -->
                                <!-- Department Transfer Wrp -->
                                <!-- Assign To staff Wrp -->
                            </div>
                        <?php }else{ //isnotstaff 
                            if ($this->ticketdetail->lock != 1 && $this->ticketdetail->status != 4 && $this->ticketdetail->status != 5) { ?>
                                <div class="js-ticket-reply-forms-heading"><?php echo JText::_('Reply a Message'); ?></div>
                                <div class="js-ticket-reply-field-wrp">
                                    <div class="js-ticket-reply-field">
                                        <?php
                                            $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                                            echo $editor->display('responce', '', '550', '300', '60', '20', false);
                                        ?>
                                    </div>
                                </div>
                                <?php
                                $isguest = $this->user->getIsGuest();
                                if ($isguest == 0) {
                                    $publisheCheck =  $this->isAttachmentPublished;
                                } else {
                                    $publisheCheck =  $this->isAttachmentVisitorPublished;
                                }
                                if ($publisheCheck) { ?>
                                    <div class="js-attachment-wrp">
                                        <div class="js-form-title"><?php echo JText::_('Attachments'); ?></div>
                                        <div class="js-form-value js-attachment-files-wrp">
                                            <div id="js-attachment-files" class="js-attachment-files">
                                                <span class="js-attachment-file-box">
                                                    <input type="file" class="inputbox js-attachment-inputbox js-form-input-field-attachment" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                    <span class='js-attachment-remove'></span>
                                                </span>
                                            </div>
                                            <div id="js-attachment-option">
                                                <?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?>
                                            </div>
                                            <span id="js-attachment-add"><?php echo JText::_('Add More File'); ?></span>
                                        </div>
                                    </div>
                                <?php } ?>
                                <div class="js-ticket-reply-form-button-wrp">
                                    <input  class="js-ticket-save-button" type="button" onclick="validate_form_department(document.adminForm)" value="<?php echo JText::_('Post reply'); ?>"/>
                                </div> 
                            <?php } 
                        }?>
                    </div>
                        </div>
                            <div class="js-ticket-detail-right"><!-- Right Side Ticket Data -->
                                <div class="js-ticket-rows-wrp js-tkt-detail-cnt" >
                                    <?php
                                        $color = "#ed1c24;";
                                        if ($this->ticketdetail->lock == 1) {
                                            $color = "#5bb12f;";
                                        } elseif ($this->ticketdetail->status == 0) {
                                            $color = "#5bb12f;";
                                        } elseif ($this->ticketdetail->status == 1) {
                                            $color = "#28abe3;";
                                        } elseif ($this->ticketdetail->status == 2) {
                                            $color = "#69d2e7;";
                                        } elseif ($this->ticketdetail->status == 3) {
                                            $color = "#FFB613;";
                                        } elseif ($this->ticketdetail->status == 4) {
                                            $color = "#ed1c24;";
                                        } elseif ($this->ticketdetail->status == 5) {
                                            $color = "#dc2742;";
                                        }
                                    ?>
                                    <div class="js-tkt-det-status" style="background-color:<?php echo $color;?>;">
                                        <?php
                                        $printstatus = 1;
                                        $ticketmessage = '';
                                        if ($this->ticketdetail->status == 4 || $this->ticketdetail->status == 5 )
                                            $ticketmessage = JText::_('Closed');
                                        elseif ($this->ticketdetail->status == 2)
                                            $ticketmessage = JText::_('In Progress');
                                        else
                                            $ticketmessage = JText::_('Open');
                                        $printstatus = 1;
                                        if ($this->ticketdetail->lock == 1) {
                                            echo '<div class="js-ticket-status-note">' . JText::_('Locked').'</div>';
                                            $printstatus = 0;
                                        }
                                        if ($this->ticketdetail->isoverdue == 1) {
                                            echo '<div class="js-ticket-status-note">' . JText::_('Overdue') . '</div>';
                                            $printstatus = 0;
                                        }
                                        if ($printstatus == 1) {
                                            echo $ticketmessage;
                                        }
                                        ?>
                                    </div>
                                    <div class="js-tkt-det-info-cnt">
                                    <div class="js-ticket-row">
                                        <div class="js-ticket-field-title">
                                           <?php echo JText::_('Created'); ?>&nbsp;:
                                        </div>
                                        <div class="js-ticket-field-value">
                                            <?php
                                                $startTimeStamp = strtotime($this->ticketdetail->created);
                                                $endTimeStamp = strtotime("now");
                                                $timeDiff = abs($endTimeStamp - $startTimeStamp);
                                                $numberDays = $timeDiff / 86400;  // 86400 seconds in one day
                                                // and you might want to convert to integer
                                                $numberDays = intval($numberDays);
                                                if ($numberDays != 0 && $numberDays == 1) {
                                                    $day_text = JText::_('Day');
                                                } elseif ($numberDays > 1) {
                                                    $day_text = JText::_('Days');
                                                } elseif ($numberDays == 0) {
                                                    $day_text = JText::_('Today');
                                                }
                                            ?>
                                            <?php
                                                if ($numberDays == 0) {
                                                    echo $day_text;
                                                } else {
                                                    echo $numberDays . ' ' . $day_text . ' ';
                                                    echo JText::_('Ago');
                                                }
                                            ?>
                                            <?php //echo JHtml::_('date',$this->ticketdetail->created,"d F, Y");
                                            ?>
                                        </div>
                                    </div>

                                    <div class="js-ticket-row">
                                        <div class="js-ticket-field-title">
                                           <?php echo JText::_('Last Reply'); ?>&nbsp;:
                                        </div>
                                        <div class="js-ticket-field-value">
                                           <?php if ($this->ticketdetail->lastreply == '' || $this->ticketdetail->lastreply == '0000-00-00 00:00:00') {echo JText::_('No Last Reply'); } else {echo JHtml::_('date',$this->ticketdetail->lastreply,"d F, Y"); } ?>
                                        </div>
                                    </div>
                                    <div class="js-ticket-row">
                                        <div class="js-ticket-field-title">
                                            <?php echo JText::_('Department'); ?>&nbsp;:
                                        </div>
                                        <div class="js-ticket-field-value">
                                            <?php echo $this->ticketdetail->departmentname; ?>
                                        </div>
                                    </div>
                                    <div class="js-ticket-row">
                                        <div class="js-ticket-field-title">
                                           <?php echo JText::_('Ticket ID'); ?>&nbsp;:
                                        </div>
                                        <div class="js-ticket-field-value">
                                           <?php echo $this->ticketdetail->ticketid; ?>
                                           <a href="javascript:void(0)" title="Copy" class="js-tkt-det-copy-id" id="ticketidcopybtn" success=<?php echo JText::_('Copied'); ?>><?php echo JText::_('Copy'); ?></a>
                                        </div>
                                    </div>
                                    <?php if($this->ticketdetail->ticketviaemail == 1 && $isstaff){ ?>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                                <?php echo JText::_('Ticket Email'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                                <?php echo $this->ticketdetail->departmentname; ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <div class="js-ticket-row">
                                        <div class="js-ticket-field-title">
                                            <?php echo JText::_('Due Date'); ?>&nbsp;:
                                        </div>
                                        <div class="js-ticket-field-value">
                                           <?php if ($this->ticketdetail->duedate == '' || $this->ticketdetail->duedate == '0000-00-00 00:00:00') echo JText::_('None'); else echo JHtml::_('date',$this->ticketdetail->duedate,"d F, Y"); ?>
                                        </div>
                                    </div>

                                    <div class="js-ticket-row">
                                        <div class="js-ticket-field-title">
                                            <?php echo JText::_('Status'); ?>&nbsp;:
                                        </div>
                                        <div class="js-ticket-field-value">
                                           <?php
                                        if ($this->ticketdetail->lock == 1) {
                                            $msg = JText::_('Lock');
                                        } elseif ($this->ticketdetail->status == 0) {
                                            $msg = JText::_('Open');
                                        } elseif ($this->ticketdetail->status == 1) {
                                            $msg = JText::_('On Waiting');
                                        } elseif ($this->ticketdetail->status == 2) {
                                            $msg = JText::_('In Progress');
                                        } elseif ($this->ticketdetail->status == 3) {
                                            $msg = JText::_('Replied');
                                        } elseif ($this->ticketdetail->status == 4) {
                                            $msg = JText::_('Closed');
                                        } elseif ($this->ticketdetail->status == 5) {
                                            $msg = JText::_('Closed and Merged');
                                        }
                                        ?>
                                        <?php echo $msg; ?>

                                        </div>
                                    </div>
                                    <div class="js-ticket-row">
                                        <div class="js-ticket-field-title">
                                            <?php echo JText::_('Help Topic'); ?>&nbsp;:
                                        </div>
                                        <div class="js-ticket-field-value">
                                            <?php echo $this->ticketdetail->helptopic; ?>
                                        </div>
                                    </div>
                                    <?php if(isset($this->time_taken)){ ?>
                                        <div class="js-ticket-row">
                                            <div class="js-ticket-field-title">
                                                 <?php echo JText::_('Total Time Taken'); ?>&nbsp;:
                                            </div>
                                            <div class="js-ticket-field-value">
                                                <?php
                                                $time = $this->time_taken;
                                                    $hours = floor($time / 3600);
                                                    $mins = floor($time / 60 % 60);
                                                    $secs = floor($time % 60);
                                                    echo jText::_(''). sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                                ?>
                                            </div>
                                        </div>
                                    <?php } ?>
                                    <?php
                                    $customfields = getCustomFieldClass()->userFieldsData(1);
                                    if(!empty($customfields)){ ?>
                                        <div class="js-ticket-row">
                                            <?php
                                                foreach ($customfields as $field) {
                                                    if($field->userfieldtype != 'termsandconditions'){
                                                        $array =  getCustomFieldClass()->showCustomFields($field, 5, $this->ticketdetail->params , $this->ticketdetail->id);
                                                        if(!empty($array)){ ?>
                                                            <div class="js-ticket-row">
                                                                <div class="js-ticket-field-title">
                                                                    <?php echo JText::_($array['title']); ?>&nbsp;:
                                                                </div>
                                                                <div class="js-ticket-field-value">
                                                                    <?php echo JText::_($array['value']); ?>
                                                                </div>
                                                            </div>

                                                <?php
                                                        }
                                                    }
                                                }
                                            ?>
                                        </div>
                                    <?php } ?>
                                    <!-- Status box -->
                                    <?php
                                        if ($this->ticketdetail->lock == 1) {
                                            $color = "#5bb12f;";
                                            $ticketmessage = JText::_('Lock');
                                        } elseif ($this->ticketdetail->status == 0) {
                                            $color = "#5bb12f;";
                                            $ticketmessage = JText::_('Open');
                                        } elseif ($this->ticketdetail->status == 1) {
                                            $color = "#28abe3;";
                                            $ticketmessage = JText::_('On Waiting');
                                        } elseif ($this->ticketdetail->status == 2) {
                                            $color = "#69d2e7;";
                                            $ticketmessage = JText::_('In Progress');
                                        } elseif ($this->ticketdetail->status == 3) {
                                            $color = "#FFB613;";
                                            $ticketmessage = JText::_('Replied');
                                        } elseif ($this->ticketdetail->status == 4) {
                                            $color = "#ed1c24;";
                                            $ticketmessage = JText::_('Closed');
                                        } elseif ($this->ticketdetail->status == 5) {
                                            $color = "#dc2742;";
                                            $ticketmessage = JText::_('Closed and Merged');
                                        }
                                    ?>

                                </div>

                            </div>

                            <div class="js-ticket-rows-wrp  js-tkt-detail-cnt" >
                                <div class="js-tkt-det-hdg">
                                    <div class="js-tkt-det-hdg-txt">
                                        <?php echo JText::_('Priority'); ?>
                                    </div>
                                    <?php if($isstaff && $this->ticketdetail->status != 5){ ?>
                                        <a class="js-tkt-det-hdg-btn" href="#" id="changepriority">
                                            <?php echo JText::_('Change'); ?>
                                        </a>
                                    <?php } ?>
                                </div>
                                <div class="js-ticket-field-value js-ticket-priorty" style="background:<?php echo $this->ticketdetail->prioritycolour; ?>; color:#ffffff;">
                                   <?php echo JText::_($this->ticketdetail->priority); ?>
                                </div>
                            </div>
                            <?php /*<?php if($this->ticketdetail->status != 5){ ?>*/?>
                            <div class="js-ticket-rows-wrp  js-tkt-detail-cnt" >
                                <div class="js-tkt-det-hdg">
                                    <div class="js-tkt-det-hdg-txt">
                                        <?php echo JText::_('Assigned To'); ?>
                                    </div>
                                </div>
                                <div class="js-ticket-field-value">
                                    <div class="js-tkt-det-hdg">
                                        <div class="js-tkt-det-hdg-txt">
                                            <?php echo JText::_('Ticket assigned to'); ?>
                                        </div>
                                        <?php if($isstaff &&  $assign_staff_permission == 1){ ?>
                                            <a class="js-tkt-det-hdg-btn" href="#assigntostaff" id="assigntostaff">
                                                <?php echo JText::_('Change'); ?>
                                            </a>
                                        <?php } ?>
                                    </div>
                                    <div class="js-tkt-det-info-wrp">
                                        <div class="js-tkt-det-user">
                                            <div class="js-tkt-det-user-image">
                                               <?php if ($this->ticketdetail->staffphoto != "") { ?>
                                                    <img  class="js-ticket-staff-img" src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                                <?php } else { ?>
                                                    <img class="js-ticket-staff-img" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                                <?php } ?>
                                            </div>
                                            <div class="js-tkt-det-user-cnt">
                                                <div class="js-tkt-det-user-data">
                                                    <?php
                                                    if ($this->ticketdetail->firstname != "") {
                                                        echo $this->ticketdetail->firstname . " " . $this->ticketdetail->lastname;
                                                    } ?>
                                                </div>
                                                <div class="js-tkt-det-user-data agent-email">
                                                    <?php
                                                    if ($this->ticketdetail->email != "")
                                                    {
                                                        echo $this->ticketdetail->email;
                                                    }?>
                                                </div>
                                                <div class="js-tkt-det-user-data"></div>
                                            </div>
                                        </div>
                                        <div class="js-tkt-det-trsfer-dep">
                                            <div class="js-tkt-det-trsfer-dep-txt">
                                                <span class="js-tkt-det-trsfer-dep-txt-tit">
                                                    <?php echo JText::_('Department').JText::_(' :'); ?>
                                                </span>
                                                <?php
                                                    if ($this->ticketdetail->departmentname != "") {
                                                            echo $this->ticketdetail->departmentname;
                                                        } ?>
                                            </div>
                                            <?php if($isstaff && $dep_transfer_permission == 1){ ?>
                                                <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="departmenttransfer">
                                                    <?php echo JText::_('Change'); ?>
                                                </a>
                                            <?php } ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <!-- timer start -->
                            <?php if($isstaff && $this->ticketdetail->status != 5){ ?>
                                <div class="js-ticket-rows-wrp  js-tkt-detail-cnt timer-wrp" >
                                        <div class="js-tkt-det-hdg">
                                            <div class="js-tkt-det-hdg-txt">
                                                <?php echo jText::_('Time Track'); ?>
                                            </div>
                                            <?php if($isstaff){ ?>
                                                <?php /*if($edit_own_time == 1){
                                                }
                                                <a href="javascript:void(0);" class="js-tkt-det-hdg-btn" onclick="showEditTimerPopup()" >
                                                    <?php echo jText::_('Edit Time'); ?>
                                                </a>
                                                */ ?>
                                            <?php } ?>
                                        </div>
                                        <div class="js-ticket-field-value">
                                            <div class="js-tkt-det-info-wrp">
                                                <div class="js-tkt-det-user">
                                                    <?php /* <div> */?>
                                                        <div class="timer_1" >
                                                            <?php
                                                            $time = $this->time_taken;
                                                            $hours = floor($time / 3600);
                                                            $mins = floor($time / 60 % 60);
                                                            $secs = floor($time % 60);
                                                            ?>
                                                            <span><?php echo sprintf('%02d', $hours); ?></span>:
                                                            <span><?php echo sprintf('%02d', $mins); ?></span>:
                                                            <span><?php echo sprintf('%02d', $secs); ?></span>
                                                        </div>
                                                        <?php /*
                                                        <div class="timer-buttons" >

                                                            <span class="timer-button cls_1" onclick="changeTimerStatus(1)" >
                                                                <?php echo jText::_('Play') ?>
                                                            </span>
                                                            <span class="timer-button cls_2" onclick="changeTimerStatus(2)" >
                                                                <?php echo jText::_('Pause') ?>
                                                            </span>
                                                            <span class="timer-button cls_3" onclick="changeTimerStatus(3)" >
                                                                <?php echo jText::_('Stop') ?>
                                                            </span>
                                                        </div>
                                                        */ ?>
                                                    <?php /* </div> */?>
                                                    <input type="hidden" name="timer_time_in_seconds" id="timer_time_in_seconds" value=""  />
                                                    <input type="hidden" name="timer_edit_desc" id="timer_edit_desc" value=""  />
                                                </div>
                                                <?php /*
                                                <div class="js-tkt-det-trsfer-dep">
                                                    <div class="js-tkt-det-trsfer-dep-txt">
                                                        <span class="js-tkt-det-trsfer-dep-txt-tit">
                                                            <?php echo jText::_('Time Taken').':&nbsp;' ?>
                                                        </span>
                                                        <?php
                                                            echo sprintf('%02d:%02d:%02d', $hours, $mins, $secs);
                                                        ?>
                                                    </div>
                                                </div>
                                                */ ?>
                                            </div>
                                        </div>
                                </div>
                            <?php } ?>
                            <!-- timer end -->
                            </div>
                            <!-- comment start -->
                            <!-- Print Ticket -->

                        </div>
                        <!-- comment end -->
                    </div>
                    <!-- Ticket Post Replay -->

                </div>

                <input type="hidden" name="email" value="<?php echo $this->ticketdetail->email; ?>" />
                <input type="hidden" name="email_ban" id="email_ban" value="<?php echo (isset($this->isemailban)) ? $this->isemailban : ''; ?>" />
                <input type="hidden" id="staffid" name="staffid" value="<?php echo ($isstaff) ? $this->user->getStaffId() : '' ;?>" />

                <input type="hidden" name="callaction" id="callaction" value="" />
                <input type="hidden" name="callfrom" id="callfrom" value="" />
                <input type="hidden" name="view" value="ticket" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="lastreply" value="<?php echo $this->ticketdetail->lastreply; ?>" />
                <input type="hidden" name="id" value="<?php echo $this->ticketdetail->id; ?>" />
                <input type="hidden" name="ticketid" id="ticketid" value="<?php echo $this->ticketdetail->ticketid; ?>" />
                <input type="hidden" name="hash" value="<?php echo $this->ticketdetail->hash; ?>" />
                <input type="hidden" name="layout" value="tickets" />
                <input type="hidden" id="task" name="task" value="actionticket" />
                <input type="hidden" name="c" value="ticket" />
                <input type="hidden" name="created" value="<?php echo $curdate = date('Y-m-d H:i:s'); ?>"/>
                <?php echo JHtml::_('form.token'); ?>
            </form>
            <div id="popup-record-data" style="display:inline-block;width:100%;"></div>
        </div>
<?php
            }else{
                messageslayout::getRecordNotFound(); //No Record
            }
        }else{
            messageslayout::getStaffDisable(); //staff disabled
        }
    }else{
        messageslayout::getPermissionNotAllow(); //permission not granted
    }
}/*else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}*///End ?>
    <script language="Javascript">
        jQuery(document).ready(function () {
            jQuery(".cb-enable").click(function () {
                var append_sig = jQuery(this).attr('for');
                var parent = jQuery(this).parents('.switch');
                jQuery('.cb-disable', parent).removeClass('selected');
                if (typeof append_sig !== 'undefined' && append_sig !== null) {
                    if (append_sig == 'appendsignature1') {
                        jQuery('label[data-signature="appendsignature2"]').removeClass('cb-enable').addClass('cb-disable');
                        jQuery('label[data-signature="appendsignature3"]').removeClass('cb-enable').addClass('cb-disable');
                        jQuery(this).addClass('selected');
                    }
                } else {
                    jQuery(this).addClass('selected');
                }

                jQuery('.checkbox', parent).attr('checked', true);
            });

            jQuery(".cb-disable").click(function () {
                var append_sig = jQuery(this).attr('for');
                var parent = jQuery(this).parents('.switch');
                jQuery('.cb-enable', parent).removeClass('selected');
                if (typeof append_sig !== 'undefined' && append_sig !== null) {
                    if ((append_sig == 'appendsignature2') || (append_sig == 'appendsignature3') || (append_sig == 'appendsignature1')) {
                        jQuery(this).removeClass('cb-disable').addClass('cb-enable');
                        jQuery(this).addClass('selected');
                    }
                } else {
                    jQuery(this).addClass('selected');
                }
                jQuery('.checkbox', parent).attr('checked', false);
            });


        }); //end .readyFunction

        function lockticket(f) {
            if (f == 0) {
                jQuery('#task').val('lockticket');
            } else if (f == 1) {
                jQuery('#task').val('unlockticket');
            }
            document.adminForm.submit();
        }

        jQuery("#js-attachment-add").click(function () {
            var obj = this;
            var current_files = jQuery('input[name="filename[]"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            var append_text = "<span class='js-attachment-file-box'><input name='filename[]' class=' js-attachment-inputbox js-form-input-field-attachment' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
            if (current_files < total_allow) { 
                jQuery("#js-attachment-files").append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert("<?php echo JText::_('File upload limit exceed'); ?>");
                jQuery(obj).hide();
            }
        });
        jQuery(document).delegate(".js-attachment-remove", "click", function (e) {
            var current_files = jQuery('input[name="filename[]"]').length;
            if(current_files!=1)
                jQuery(this).parent().remove();
            var current_files = jQuery('input[name="filename[]"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            if (current_files < total_allow) {
                jQuery("#js-attachment-add").show();
            }
        });

        function validate_form_department(f) {
            var content = jQuery('textarea#responce').val();
            if(content == ''){
                if(isTinyMCE()){
                    content = tinyMCE.get('responce').getContent();
                }else{
                    content = true;
                }
            }
            jQuery('#callfrom').val('postreply');
            if (content !== '') {
                if(timer_flag != 0){
                    jQuery('input#timer_time_in_seconds').val(jQuery('div.timer').data('seconds'));
                }
                document.adminForm.submit();
            } else {
                alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                return false;
            }
        }
        function isTinyMCE(){
            is_tinyMCE_active = false;
            if (typeof(tinyMCE) != "undefined") {
                if(tinyMCE.editors.length > 0){
                    is_tinyMCE_active = true;
                }
            }
            return is_tinyMCE_active;
        }
        function ticketinternalnote(f) {

            var content = jQuery('textarea#internalnote').val();
            if(content == ''){
                if(isTinyMCE()){
                    content = tinyMCE.get('internalnote').getContent();                    
                }else{
                    content = true;
                }
            }
            var title = jQuery('#notetitle').val();
            jQuery('#callfrom').val('internalnote');
            if (content !== '' && title !== '') {
                if(timer_flag != 0){
                    jQuery('input#timer_time_in_seconds').val(jQuery('div.timer').data('seconds'));
                }
                document.adminForm.submit();
            } else {
                alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                return false;

            }

        }
        function ticketdepartmenttransfer(f) {
            var content = jQuery("textarea#departmenttranfer").val();
            if(content == ''){
                if(isTinyMCE()){
                    content = tinyMCE.get('departmenttranfer').getContent();
                }else{
                    content = true;
                }
            }
            var depid = jQuery('#departmentid').val();
            jQuery('#callfrom').val('departmenttransfer');
            if (content !== '' && depid !== '') {
                document.adminForm.submit();
            } else {
                alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                return false;
            }
        }
        function ticketstafftransfer(f) {
            var content = jQuery('textarea#assigntostaffnote').val();
            if(content == ''){
                if(isTinyMCE()){
                    content = tinyMCE.get('assigntostaffnote').getContent();
                }else{
                    content = true;
                }
            }
            var staff_id = jQuery('#assigntostaff').find('option:selected').val();
            jQuery('#staffid').val(staff_id);
            jQuery('#callfrom').val('stafftransfer');
            if (content !== "" && staff_id !== "") {
                document.adminForm.submit();
            } else {
                alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                return false;
            }
        }

        function getpremade(src, premadeid, append) {
            var link = 'index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo JSession::getFormToken(); ?>=1';
            jQuery.post(link,{val:premadeid},function(data){
                if(data){
                    if (append == true) {
                        var content = jQuery('textarea#responce').val();
                        if(content == ''){
                            if(isTinyMCE()){
                                content = tinyMCE.get('responce').getContent();
                            }
                        }
                        content = content + data;
                        if(isTinyMCE()){
                            tinyMCE.get('responce').execCommand('mceSetContent', false, content);
                        }else{
                            jQuery('textarea#responce').val(content);
                        }

                    } else {
                        if(isTinyMCE()){
                            tinyMCE.get('responce').execCommand('mceSetContent', false, data);
                        }else{
                            jQuery('textarea#responce').val(content);
                        }
                    }
                }
            });
        }

        function actioncall(value) {
            if(value == 3){
                var yesclose = confirm('<?php echo JText::_('Are you sure to close ticket'); ?>');
                if(yesclose != true){
                    return;
                }
            }
            jQuery('#callfrom').val('action');
            jQuery('#callaction').val(value);
            document.adminForm.submit();
        }

        function combo(value) {
            var ele = jQuery('#priorityid');
            if (value == 1) {
                ele.prop('disabled', false);
            } else {
                ele.prop('disabled', true);
            }
        }

        function editreply(id,jsession) {
            var rsrc = 'responce_' + id;
            var src = 'responce_edit_' + id;
            var esrc = 'editor_responce_' + id;
            showhide(rsrc, 'none');
            showhide(src, 'block');
            jQuery('#' + src).html("<?php echo JText::_('Loading'); ?> ...");
            jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=editresponce&"+jsession, {id: id}, function (data, status) {
                jQuery('#' + src).html(data);  //retuen value
                if (!tinyMCE.get(esrc)) {  //toggle editor
                    tinyMCE.execCommand('mceToggleEditor', false, esrc);
                    return false;
                }
            });
        }

        function saveResponce(id,jsession) {
            var esrc = 'editor_responce_' + id;
            if (!tinyMCE.get(esrc)) { // check toggle
                alert("Please toggle editor");
            } else {
                if(isTinyMCE()){
                    var content = tinyMCE.get(esrc).getContent();
                }
                var rsrc = 'responce_' + id;
                var src = 'responce_edit_' + id;
                showhide(rsrc, 'block');
                showhide(src, 'none');

                jQuery('#' + rsrc).html("Saving...");
                var arr = new Array();
                arr[0] = id;
                arr[1] = content;
                var link = 'index.php?option=com_jssupportticket&c=ticket&task=saveresponceajax&'+jsession;
                jQuery.post(link,{val:JSON.stringify(arr)}, function(data){
                    if(data){
                        if(data == 1){
                            jQuery('#' + rsrc).html(content);
                        }else{
                            jQuery('#' + rsrc).html(data);
                        }
                        tinymce.remove(tinyMCE.get(esrc));
                    }
                });
            }
        }
        function deletereply(id,jsession) {
            if(confirm("<?php echo JText::_('Are you sure delete'); ?>")){
                var rsrc = 'responce_' + id;
                jQuery('#' + rsrc).html("Deleting...");

               jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=deleteresponceajax&"+jsession, {id : id}, function(data){
                    jQuery('#' + rsrc).html(data);
               });
            }
        }
        function closeResponce(id) {
            var rsrc = 'responce_' + id;
            var src = 'responce_edit_' + id;
            var esrc = 'editor_responce_' + id;
            showhide(rsrc, 'block');
            showhide(src, 'none');
            tinymce.remove(tinyMCE.get(esrc));
        }
        function showhide(layer_ref, state) {
            if (state == 'none') {
                jQuery('#' + layer_ref).hide('slow');
            } else if (state == 'block') {
                jQuery('#' + layer_ref).show('slow');

            }
        }
    </script>
</div>
<?php /*} */?>
