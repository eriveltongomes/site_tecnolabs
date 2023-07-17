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
JHTML::_('behavior.formvalidator');
$document = JFactory::getDocument();
$document->addScript('components/com_jssupportticket/include/js/jquery_idTabs.js');
$document->addScript('components/com_jssupportticket/include/js/file/file_validate.js');
JText::script('Error file size too large');
JText::script('Error file extension mismatch');
?>

<script type="text/javascript">
    function validate_form(f)
    {
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken();
                else echo JSession::getFormToken(); ?>';//send token
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        document.adminForm.submit();
    }

    // timer reply edit
            function showPopupAndFillValues(id,pfor) {
            jQuery('div.edit-time-popup').hide();
            if(pfor == 1){
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getReplyDataByID&<?php echo JSession::getFormToken(); ?>=1", {val: id}, function (data) {
                    if (data) {
                        jQuery('div.popup-header-text').html('<?php echo JText::_("Edit Reply");?>');
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
                        jQuery('div.popup-header-text').html('<?php echo JText::_("Edit Time");?>');
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
                    if (data) {
                        jQuery('div.popup-header-text').html('<?php echo JText::_("Edit Time");?>');
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
                var ticketid = id;
                jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo JSession::getFormToken(); ?>=1", {ticketid:ticketid}, function (data) {
                    if (data) {
                        var d = JSON.parse(data);
                        if(d['status'] == 1){
                            jQuery("div#popup-record-data").show();
                            jQuery("div#popup-record-data").html("");
                            jQuery("div#js-history-back").show();
                            jQuery("div#popup-record-data").html(d['data']);
                        }
                    }
                });
            }

             return false;
        }

        function updateticketlist(pagenum,ticketid){
            jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo JSession::getFormToken(); ?>=1", {ticketid:ticketid,ticketlimit:pagenum}, function (data) {
                if(data){
                    var d = JSON.parse(data);
                    if(d['status'] == 1){
                        jQuery("div#popup-record-data").show();
                        jQuery("div#popup-record-data").html("");
                        jQuery("div#popup-record-data").html(d['data']);
                    }
                }
            });
        }

    //moreDetailDiv
    jQuery(document).ready(function(){
        jQuery("a#chng-prority").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupforchangepriority").slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery("a#asgn-staff").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupforassignstaff").slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery("a#chng-dept").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupforchangedepartment").slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery("a#int-note").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupforintnote").slideDown('slow');
            jQuery('div#userpopupblack').show();
        });
        jQuery("div#userpopupblack, span.close-history").click(function (e) {
            jQuery("div#userpopupforchangepriority").slideUp('slow');
            jQuery("div#userpopupforchangedepartment").slideUp('slow');
            jQuery("div#userpopupforassignstaff").slideUp('slow');
            jQuery("div#userpopupforintnote").slideUp('slow');
            setTimeout(function () {
                jQuery('div#userpopupblack').hide();
            }, 700);
        });
        jQuery('a[href="#"]').click(function(e){
            e.preventDefault();
        });
        jQuery("a#moreactions").click(function(e){
            e.preventDefault();
            jQuery("div#js-tk-actiondiv").slideToggle();
        });

        jQuery("a#requester-showmore").click(function(e){
            e.preventDefault();
            jQuery("a#requester-showmore").find('img').toggleClass('js-hidedetail');
            jQuery("div#req-moredetail").slideToggle();
        });
        //ATTACHMENTS
        jQuery("#js-attachment-add").click(function () {
            var obj = this;
            var current_files = jQuery('input[type="file"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            var append_text = "<span class='js-value-text'><input name='filename[]' type='file' onchange=uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'); size='20' maxlenght='30' /><span  class='js-attachment-remove'></span></span>";
            if (current_files < total_allow) {
                jQuery(".js-attachment-files").append(append_text);
            } else if ((current_files === total_allow) || (current_files > total_allow)) {
                alert("<?php echo JText::_('File upload limit exceed'); ?>");
                jQuery(obj).hide();
            }
        });
        jQuery(document).delegate(".js-attachment-remove", "click", function (e) {
            var current_files = jQuery('input[type="file"]').length;
            if(current_files!=1)
                jQuery(this).parent().remove();
            var current_files = jQuery('input[type="file"]').length;
            var total_allow =<?php echo $this->config['noofattachment']; ?>;
            if (current_files < total_allow) {
                jQuery("#js-attachment-add").show();
            }
        });
        //History Popup
        jQuery(document).ready(function(){
            jQuery("a#js-tk-history").click(function(){
               jQuery('div#js-history-back').show();
               jQuery('div#js-history-popup').slideDown('slow');

            });
            jQuery('div#js-history-back,img#close-img,div#js-private-crendentials-back').click(function(){
               jQuery('div#js-history-popup').slideUp('slow');
               jQuery("div#userpopupforchangepriority").slideUp('slow');
               jQuery("div#userpopupforchangedepartment").slideUp('slow');
               jQuery("div#js-private-crendentials-popup").slideUp('slow');
               jQuery("div#userpopupforassignstaff").slideUp('slow');
               jQuery("div#userpopupforintnote").slideUp('slow');
               jQuery("div#popup-record-data").slideUp('slow');
               setTimeout(function () {
                   jQuery('div#js-history-back').hide();
                   jQuery('div#js-private-crendentials-back').hide();
                }, 700);
            });
        });
        jQuery("div.popup-header-close-img,div.jsst-popup-background,input#cancel").click(function (e) {
            jQuery("div.jsst-popup-wrapper").slideUp('slow');
            setTimeout(function () {
                jQuery('div.jsst-popup-background').hide();
            }, 700);
        });
        jQuery(document).delegate("#close-pop", "click", function (e) {
            jQuery("div#mergeticketselection").fadeOut();
            jQuery("div#popup-record-data").html("");
            jQuery('div#js-history-back').hide();
        });
        jQuery(document).delegate("#ticketpopupsearch",'submit', function (e) {
            var ticketid = jQuery("#ticketidformerge").val();
            e.preventDefault();
            var name = jQuery("input#name").val();
            var email = jQuery("input#email").val();
            jQuery.post("index.php?option=com_jssupportticket&c=ticket&task=getTicketsForMerging&<?php echo JSession::getFormToken(); ?>=1",{name: name, email: email,ticketid:ticketid}, function (data) {
                var d = JSON.parse(data);
                if (data) {
                    jQuery("div#popup-record-data").html("");
                    jQuery("div#popup-record-data").html(d['data']);
                }
            });//jquery closed
        });
        jQuery(document).delegate("#ticketidcopybtn", "click", function(){
            var temp = jQuery("<input>");
            jQuery("body").append(temp);
            temp.val(jQuery("#ticketidcopybtn").attr("data-ticket-hash-id")).select();
            document.execCommand("copy");
            temp.remove();
            jQuery("#ticketidcopybtn").text(jQuery("#ticketidcopybtn").attr('success'));
        });
    });

    function getmergeticketid(secondaryid, primaryid){
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
                var d = JSON.parse(data);
                jQuery("div#popup-record-data").html("");
                jQuery("div#popup-record-data").html(d);
            }
        });
    }

    function formField(){
        jQuery('div#jsjob_installer_waiting_div').show();
        jQuery("#name").val("");
        jQuery("#email").val("");
        jQuery("#ticketpopupsearch").submit();
    }
</script>
<script type="text/javascript">
    jQuery(document).ready(function(){
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

</script>
<div id="popup-record-data" style="display:inline-block;"></div>
<div id="js-history-back" style="display:none"> </div>
    <div id="js-history-popup" style="display:none">
        <div id="js-history-head">
            <span class="js-title"><?php echo JText::_('Ticket History'); ?></span>
            <span class="js-image"><img id="close-img" src="components/com_jssupportticket/include/images/popup-close.png"></span>
        </div>
        <div class="js-history-messagewrapper"><?php
        foreach ($this->tickethistory as $history) { ?>
            <div id="js-history-row">
                <span class="js-col-xs-12 js-col-md-2 js-data"><?php echo JHtml::_('date',$history->datetime,'Y-m-d'); ?></span>
                <span class="js-col-xs-12 js-col-md-2 js-data"><?php echo JHtml::_('date',$history->datetime,'H:i:s'); ?></span>
                <?php
                    if ($history->level == 1) //admin
                        $color = "blue";
                    elseif ($history->level == 2) //staff
                        $color = "orange";
                    else  //user
                        $color = "black";
                ?>
                <span class="js-col-xs-12 js-col-md-8 js-data" style="color:<?php echo $color; ?>"><?php echo $history->message; ?></span>
            </div> <?php
        } ?>
        </div>
    </div>
<div id="js-tk-admin-wrapper">
    <div id="js-tk-leftmenu">
        <?php include_once('components/com_jssupportticket/views/menu.php'); ?>
    </div>
    <!-- private-crendentials popup start -->
    <div id="js-private-crendentials-back" style="display:none"> </div>
    <div id="js-private-crendentials-popup" style="display:none">
        <div id="js-private-crendentials-head">
            <span class="js-title"><?php echo JText::_('Private Credentials'); ?></span>
            <span class="js-image" id="close-img">
                <img id="close-img" src="<?php echo JURI::root() ?>administrator/components/com_jssupportticket/include/images/popup-close.png">
            </span>
        </div>
        <div class="js-ticket-usercredentails-credentails-wrp">
        </div>
        <!--  Add new credentials -->
        <?php $credential_add_permission = false;
        if($this->ticketdetail->status != 4 && $this->ticketdetail->status != 5){
            $credential_add_permission = true;
        } ?>
        <?php if($credential_add_permission){ 
            $c_user = JSSupportticketCurrentUser::getInstance();?>
            <div class="private-crendentials-add-new-popup">
                <div class="js-ticket-edit-form-wrp">
                    <input type="button" class="js-ticket-priorty-cancel"  value="<?php echo JText::_('Add New credential'); ?>" onclick="addEditCredentail(<?php echo $this->ticketdetail->id;?>,<?php echo $c_user->getId();?>);" />
                </div>
            </div>
        <?php } ?>
        <div class="js-ticket-usercredentails-form-wrap" >
        </div>
    </div>
    <!-- private crediantional popup end -->
    <div class="jsst-popup-background" style="display:none;" ></div>
        <div class="jsst-popup-wrapper" style="display:none;" >
            <div class="jsst-popup-header" >
                <div class="popup-header-text" >
                    <?php echo JText::_('Edit Timer')?>
                </div>
                <div class="popup-header-close-img" >
                </div>
            </div>
            <div class="edit-time-popup" style="display:none;" >
                <div class="js-tk-tabs-wrapper-wrapper">
                    <div class="js-title"><?php echo JText::_('Time'); ?>&nbsp;<font color="red">*</font></div>
                    <div class="js-value"><input class="inputbox" type="text" name="edited_time" id="edited_time" size="40" maxlength="255" value="" /></div>
                </div>
                <div class="js-tk-tabs-wrapper-wrapper">
                    <div class="js-title"><?php echo JText::_('Reason For Editing the timer'); ?></div>
                    <div class="js-value">
                        <textarea name="ttt_desc" id="ttt_desc" cols="60" rows="20" style="height: 300px;" >  </textarea>
                    </div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="button" class="button js-button-save" name="ok" onclick="updateTimerFromPopup()" value="<?php echo JText::_('Ok'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel"  value="<?php echo JText::_('Cancel'); ?>" />
                </div>
            </div>

            <form id="jsst-reply-form" style="display:none;" method="post" >
                <div class="js-col-md-12 js-form-wrapper">
                    <div class="js-col-md-12 js-form-title"><?php echo JText::_('Reply'); ?></div>
                    <div class="js-col-md-12 js-form-value">
                        <?php
                            $editor = JFactory::getConfig()->get('editor');
                            $editor = JEditor::getInstance($editor);
                            echo $editor->display('jsticket_replytext', '', '', '100', '20', '20', false);
                        ?>
                    </div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="submit" class="button js-button-save" name="ok" value="<?php echo JText::_('Save'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel" onclick="closePopup()" value="<?php echo JText::_('Cancel'); ?>" />
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
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo JText::_('Time'); ?></div>
                    <div class="js-value"><input class="inputbox" type="text" name="edited_time" id="edited_time" size="40" maxlength="255" value="" /></div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-col-md-12 js-title"><?php echo JText::_('System Time'); ?></div>
                    <div class="js-col-md-12 js-value"><input class="inputbox" type="text" name="systemtime" id="systemtime" size="40" maxlength="255" value="" disabled="disabled" /></div>
                </div>
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo JText::_('Reason For Editing'); ?></div>
                    <div class="js-value">
                            <?php
                                $editor = JFactory::getConfig()->get('editor');
                                $editor = JEditor::getInstance($editor);
                                echo $editor->display('edit_reason', '', '', '100', '20', '20', false);
                            ?>
                    </div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-title"><?php echo JText::_('Resolve conflict'); ?></div>
                    <div class="js-value"><?php echo $time_confilct_combo; ?></div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="submit" class="button js-button-save" name="ok" value="<?php echo JText::_('Save'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel" onclick="closePopup()" value="<?php echo JText::_('Cancel'); ?>" />
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
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo JText::_('Time'); ?></div>
                    <div class="js-value"><input class="inputbox" type="text" name="edited_time" id="edited_time" size="40" maxlength="255" value="" /></div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-title"><?php echo JText::_('System Time'); ?></div>
                    <div class="js-value"><input class="inputbox" type="text" name="systemtime" id="systemtime" size="40" maxlength="255" value="" disabled="disabled" /></div>
                </div>
                <div class="js-tk-tabs-wrapper">
                    <div class="js-title"><?php echo JText::_('Reason For Editing'); ?></div>
                    <div class="js-value">
                        <?php
                            $editor = JFactory::getConfig()->get('editor');
                            $editor = JEditor::getInstance($editor);
                            echo $editor->display('t_desc', '', '', '100', '20', '20', false);
                        ?>
                    </div>
                </div>
                <div class="js-tk-tabs-wrapper system-time-div" style="display:none;" >
                    <div class="js-title"><?php echo JText::_('Resolve conflict'); ?></div>
                    <div class="js-value"><?php echo $time_confilct_combo; ?></div>
                </div>
                <div class="js-col-md-12 js-form-button-wrapper">
                    <input type="submit" class="button js-button-save" name="ok" value="<?php echo JText::_('Save'); ?>" />
                    <input type="button" class="button js-button-cancel" name="cancel" onclick="closePopup()" value="<?php echo JText::_('Cancel'); ?>" />
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
    <div id="js-tk-cparea">
        <div id="jsstadmin-wrapper-top">
            <div id="jsstadmin-wrapper-top-left">
                <div id="jsstadmin-breadcrunbs">
                    <ul>
                        <li><a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard"><?php echo JText::_('Dashboard'); ?></a></li>
                        <li><a href="index.php?option=com_jssupportticket&c=ticket&layout=tickets" title="Dashboard"><?php echo JText::_('Tickets'); ?></a></li>
                        <li><?php echo JText::_('Ticket Detail'); ?></li>
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
                    <?php echo JText::_('Version').JText::_(' :'); ?>
                    <span class="jsstadmin-ver">
                        <?php $version = str_split($this->version);
                        $version = implode('.', $version);
                        echo $version; ?>
                    </span>
                </div>
            </div>
        </div>
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo $this->ticketdetail->subject; ?></h4>
        </div>
        <form class="jsstadmin-data-wrp" action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
            <div id="js-tk-ticket-detail">
                <div class="js-tkt-det-left">
                    <div class="js-tkt-det-cnt js-tkt-det-info-wrp" id="">
                        <div class="js-tkt-det-user">
                            <?php if($this->ticketdetail->status != 5){ ?>
                            <div class="js-tkt-det-user-image">
                                <img class="requester-image" src="components/com_jssupportticket/include/images/user.png">
                            </div>
                            <div class="js-tkt-det-user-cnt">
                                <div class="js-tkt-det-user-data name">
                                    <?php echo $this->ticketdetail->name; ?>
                                </div>
                                <div class="js-tkt-det-user-data email">
                                    <?php echo $this->ticketdetail->email; ?>
                                </div>
                                <div class="js-tkt-det-user-data number">
                                    <?php if ($this->ticketdetail->phone) { ?>
                                        <?php echo $this->ticketdetail->phone;
                                         ?>
                                    <?php } ?>  
                                </div>
                            </div>
                            <?php } ?>
                        </div>
                        <div class="js-tkt-det-other-tkt">
                            <a href="index.php?option=com_jssupportticket&c=ticket&layout=tickets&uid=<?php echo $this->ticketdetail->uid ?>" class="js-tkt-det-other-tkt-btn">
                                <?php echo JText::_('View').' '.JText::_('all tickets').' '.JText::_('by').' '; ?>
                                <?php echo $this->ticketdetail->name; ?>
                            </a>
                        </div>
                        <div class="js-tkt-det-tkt-msg">
                            <p><?php echo $this->ticketdetail->message; ?></p>
                        </div>
                        <div class="js-tkt-det-actn-btn-wrp">
                            <?php if($this->ticketdetail->status != 5){ 
                                if($this->ticketdetail->status != 4){ ?>
                                    <a class="js-detal-alinks" href="#" onclick="return showPopupAndFillValues(<?php echo $this->ticketdetail->id;?>,4)">
                                        <img title="<?php echo JText::_('Merge Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/merge-ticket.png">
                                        <span>
                                            <?php echo JText::_('Merge Ticket'); ?>
                                        </span>
                                    </a>
                                <?php } ?>
                                <?php $link = 'index.php?option='.$this->option.'&c=ticket&task=addnewticket&cid[]='.$this->ticketdetail->id; ?>
                                <a class="js-detal-alinks" href="<?php echo $link; ?>">
                                    <img title="<?php echo JText::_('Edit Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/edit.png">
                                    <span>
                                        <?php echo JText::_('Edit Ticket'); ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" href="#" onclick="actioncall('<?php if ($this->ticketdetail->status == 4) echo 8; else echo 3; ?>')">
                                    <?php if ($this->ticketdetail->status != 4){?>
                                        <img title="<?php echo JText::_('Close Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/close.png">
                                        <span>
                                            <?php echo JText::_('Close Ticket'); ?>
                                        </span>
                                    <?php }else{?>
                                        <img title="<?php echo JText::_('Reopen Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/reopen.png">
                                        <span>
                                            <?php echo JText::_('Reopen Ticket'); ?>
                                        </span>
                                    <?php } ?>
                                </a>
                                <a class="js-detal-alinks" id="" href="javascript:void(0)" onclick="getCredentails(<?php echo $this->ticketdetail->id; ?>)" id="jstkprivatecrendentials">
                                    <img title="<?php echo JText::_('Private Credentials'); ?>" src="<?php echo JURI::root() ?>administrator/components/com_jssupportticket/include/images/ticket-detail/private-credentials.png">
                                    <span>
                                        <?php echo JText::_('Private Credentials'); ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" id="js-tk-history" href="#">
                                    <img title="<?php echo JText::_('Ticket History'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/history.png">
                                    <span>
                                        <?php echo JText::_('Ticket History'); ?>
                                    </span>
                                </a>
                                <?php
                                $link_print = 'index.php?option=' . $this->option . '&c=ticket&layout=print_ticket&cid[]='.$this->ticketdetail->id.'&tmpl=component&print=1';
                                ?>
                                <a class="js-detal-alinks" id="" href="<?php echo $link_print; ?>" target="_blank">
                                    <img title="<?php echo JText::_('Print Ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/print.png">
                                    <span>
                                        <?php echo JText::_('Print Ticket'); ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" href="#" onclick="actioncall('<?php if ($this->ticketdetail->lock == 1) echo 12; else echo 11; ?>')">
                                    <?php if ($this->ticketdetail->lock == 1){$image_title ='Unlock Ticket'; $icon_name='unlock.png';}else{$image_title ='Lock Ticket';$icon_name='lock.png';} ?>
                                    <img title="<?php echo JText::_($image_title); ?>" src="components/com_jssupportticket/include/images/ticket-detail/<?php echo $icon_name; ?>">
                                    <span>
                                        <?php echo JText::_($image_title); ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" href="#" onclick="actioncall('<?php if ($this->isemailban == 2) echo 4; else echo 9; ?>')">
                                    <?php if ($this->isemailban == 2){$image_title ='Ban Email'; $icon_name='ban.png';}else{$image_title ='Unban Email';$icon_name='un-ban.png';} ?>
                                    <img title="<?php echo JText::_($image_title); ?>" src="components/com_jssupportticket/include/images/ticket-detail/<?php echo $icon_name; ?>">
                                    <span>
                                        <?php echo JText::_($image_title); ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" href="#" onclick="actioncall('<?php if ($this->ticketdetail->isoverdue == 1) echo 13; else echo 6; ?>')">
                                    <?php if ($this->ticketdetail->isoverdue == 1){$image_title =JText::_('Unmark overdue'); $icon_name='un-over-due.png';}else{$image_title =JText::_('Mark overdue');$icon_name='over-due.png';} ?>
                                    <img title="<?php echo $image_title; ?>" src="components/com_jssupportticket/include/images/ticket-detail/<?php echo $icon_name; ?>">
                                    <span>
                                        <?php echo $image_title; ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" href="#" onclick="actioncall('<?php echo 10; ?>')">
                                    <img title="<?php echo JText::_('Mark In Progress'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/in-progress.png">
                                    <span>
                                        <?php echo JText::_('Mark In Progress'); ?>
                                    </span>
                                </a>
                                <a class="js-detal-alinks" href="#" onclick="actioncall('<?php echo 7; ?>')">
                                    <img title="<?php echo JText::_('Ban email and close ticket'); ?>" src="components/com_jssupportticket/include/images/ticket-detail/ban-email-close-ticket.png">
                                    <span>
                                        <?php echo JText::_('Ban email and close ticket'); ?>
                                    </span>
                                </a>
                            <?php } ?>
                        </div>
                        <div class="js-col-md-8">
                        <?php if($this->ticketdetail->ticketviaemail == 1){ ?>
                            <div class="js-wrapper">
                                <span class="js-title">
                                    <strong><?php echo JText::_('Ticket Email'); ?>:</strong>
                                </span>
                                <span class="js-value"><?php echo isset($this->ticketemail->emailaddress) ? JText::_($this->ticketemail->emailaddress) : '' ?></span>
                            </div>
                        <?php } ?>
                        </div>   
                    </div>
                     <!-- internal note -->
                    <div class="js-tk-subheading">
                        <?php echo JText::_('Internal notes'); ?>
                    </div>
                    <?php if($this->ticketnotes){ ?>
                        <?php jimport('joomla.filter.output');
                            foreach ($this->ticketnotes as $row) { ?>
                                <div id="js-ticket-threads">
                                    <div class="js-tk-pic">
                                        <?php if ($row->staffphoto) { ?>
                                            <img  src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                        <?php } else { ?>
                                            <img src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                        <?php } ?>
                                    </div>
                                    <div class="js-tk-message">
                                        <div class="js-ticket-thread-data">
                                            <span class="js-ticket-thread-person">
                                                <?php echo $row->staffname; ?>
                                            </span>
                                        </div>
                                        <?php if($row->title){ ?>
                                            <div class="js-ticket-thread-data">
                                                <span class="js-ticket-thread-note">
                                                    <?php echo $row->title; ?>
                                                </span>
                                            </div>
                                        <?php } ?>
                                        
                                        <div class="js-ticket-thread-data note-msg">
                                            <?php echo $row->note; ?>
                                            <?php  if($row->filesize > 0 && !empty($row->filename)){
                                                $notepath = 'index.php?option=com_jssupportticket&c=note&task=getdownloadbyid&id='.$row->id.'&' . JSession::getFormToken() . '=1';
                                                    echo '<div class="js_ticketattachment">'.'<span class="js_ticketattachment_fname">'
                                                    . $row->filename.'</span>' . '&nbsp;&nbsp;
                                                    <a class="button" target="_blank" href="'.$notepath.'">'.JText::_('Download').'</a>
                                                    </div>';
                                            }?>
                                        </div>
                                        <div class="js-ticket-thread-cnt-btm">
                                                <span class="js-ticket-thread-date"><?php $replyby = JHtml::_('date',strtotime($row->created),"l F d, Y, H:i:s"); echo ' ( '. $replyby.' )'; ?></span>
                                                <?php 
                                                    $hours = floor($row->usertime / 3600);
                                                    $mins = floor($row->usertime / 60 % 60);
                                                    $secs = floor($row->usertime % 60);
                                                    $time = JText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                                ?>
                                                <a class="ticket-edit-time-button" href="#" onclick="return showPopupAndFillValues(<?php echo $row->id;?>,3)" >
                                                    <img src="../components/com_jssupportticket/include/images/edit-reply-icon.png" />
                                                    <?php echo JText::_('Edit Time');?>
                                                </a>
                                                <span class="js-ticket-thread-time"><?php echo $time; ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                    <?php } ?>
                    <?php if($this->ticketdetail->status != 5){ ?>
                        <div class="js-ticket-thread-add-btn">
                            <a title="Post New Internal Note" href="#" class="js-ticket-thread-add-btn-link" id="int-note">
                                <img alt="Post New Internal Note" src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/ticket-detail/edit-time.png">
                                <span><?php echo JText::_('Post New Internal Note'); ?></span>
                            </a>
                        </div>
                    <?php } ?>
                    <div class="js-tk-subheading">
                        <?php echo JText::_('Ticket Thread'); ?>
                    </div>
                    <div id="js-ticket-threads">
                        <div class="js-tk-pic">
                            <img src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                        </div>
                        <div class="js-tk-message">
                            <div class="js-ticket-thread-data">
                                <span class="js-ticket-thread-person">
                                    <?php echo $this->ticketdetail->name; ?>
                                </span>
                            </div>
                            <div class="js-ticket-thread-data">
                                <span class="js-ticket-thread-email">
                                    <?php echo $this->ticketdetail->email; ?>                
                                </span>
                            </div>
                            <div class="js-ticket-thread-data note-msg">
                                <?php echo $this->ticketdetail->message; ?>
                                <?php
                                if (isset($this->ticketattachment[0]->filename)) {
                                    foreach ($this->ticketattachment as $attachment) {
                                        echo '<div class="js_ticketattachment">';
                                            $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&' . JSession::getFormToken() . '=1';
                                            echo "<img src='components/com_jssupportticket/include/images/clip.png'><a target='_blank' href=" . $path . ">"
                                            . $attachment->filename . "&nbsp(" . round($attachment->filesize, 2) . " KB)" . "</a>";
                                        echo "</div>";
                                    }
                                } ?>
                            </div>
                            <div class="js-ticket-thread-cnt-btm">
                                <div class="js-ticket-thread-date">
                                    <?php $replyby = JHtml::_('date',strtotime($this->ticketdetail->created),"l F d, Y, H:i:s"); echo ' ( '. $replyby.' )'; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <?php
                    jimport('joomla.filter.output');
                    foreach ($this->ticketreplies as $row) { ?>
                        <div id="js-ticket-threads">
                            <div class="js-tk-pic">
                                <?php if ($row->staffphoto) { ?>
                                    <img  src="<?php echo JURI::root(). $this->config['data_directory'] . "/staffdata/staff_" . $row->staffid . "/" . $row->staffphoto; ?>" />
                                <?php } else { ?>
                                    <img src="<?php echo JURI::root(); ?>components/com_jssupportticket/include/images/user.png" />
                                <?php } ?>
                            </div>
                            <div class="js-tk-message">
                                <div class="js-ticket-thread-data">
                                    <span class="js-ticket-thread-person">
                                        <?php echo $row->name; ?>
                                    </span>
                                </div>
                                <?php $message = $row->message;
                                if($row->mergemessage == 1){
                                    $message = str_replace("id=","cid[]=",$message);
                                    $message = str_replace("layout=ticketdetail","layout=ticketdetails",$message);
                                } ?>
                                <div class="js-ticket-thread-data note-msg">
                                    <?php echo html_entity_decode($message); ?>
                                    <?php
                                    if (isset($row->attachments)) {
                                        echo '<div class="js_ticketattachment_wrp">';
                                            foreach ($row->attachments as $attachment) {
                                                echo '<div class="js_ticketattachment">';
                                                    $path = 'index.php?option=com_jssupportticket&c=ticket&task=getdownloadbyid&id='.$attachment->attachmentid.'&' . JSession::getFormToken() . '=1';
                                                    echo "<img src='components/com_jssupportticket/include/images/clip.png'><a target='_blank' href=" . $path . ">"
                                                    . $attachment->filename . "&nbsp(" . round($attachment->filesize, 2) . " KB)" . "</a>";
                                                echo "</div>";
                                            }
                                            echo '</div>';
                                    } ?>
                                </div>
                                <div class="js-ticket-thread-cnt-btm">
                                    <span class="js-ticket-thread-time">
                                        <?php $replyby = JHtml::_('date',strtotime($row->created),"l F d, Y, H:i:s"); echo ' ( '. $replyby.' )'; ?>
                                    </span>
                                    <?php if ($row->ticketviaemail == 1) { ?>
                                        <span style="background-color: #428BCA; padding:4px 10px;border-radius:20px;color:#ffffff;">
                                            <?php echo JText::_('Ticket via email'); ?>
                                        </span>
                                    <?php } 
                                        if($row->staffid != 0){ ?>
                                            <a class="ticket-edit-reply-button" href="#" onclick="return showPopupAndFillValues(<?php echo $row->id;?>,1)" >
                                                    <img src="../components/com_jssupportticket/include/images/edit-blue.png" />
                                                <?php echo JText::_('Edit Reply');?>
                                            </a>     
                                            <?php 
                                                $hours = floor($row->usertime / 3600);
                                                $mins = floor($row->usertime / 60 % 60);
                                                $secs = floor($row->usertime % 60);
                                                $time = JText::_('Time Taken').':&nbsp;'.sprintf('%02d:%02d:%02d', $hours, $mins, $secs); 
                                                ?>
                                                <a class="ticket-edit-time-button" href="#" onclick="return showPopupAndFillValues(<?php echo $row->id;?>,2)" >
                                                    <img src="../components/com_jssupportticket/include/images/edit-reply-icon.png" />
                                                    <?php echo JText::_('Edit Time');?>
                                                </a>
                                                <span class="js-ticket-thread-time"><?php echo $time; ?></span>
                                                <?php
                                            }
                                      ?>
                                </div>
                            </div>
                        </div>
                    <?php
                    } ?>
                    <?php if($this->ticketdetail->status != 5){ ?>
                        <div id="button">
                            <div class="js-tk-subheading js-margin-bottom">
                                <?php echo JText::_('Post reply'); ?>
                            </div>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title"><?php echo JText::_('Premade'); ?>:&nbsp;</div>
                                 <div class="js-value"><?php echo $this->lists['premade']; ?></div>
                            </div>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-ticket-detail-append-signature-xs">
                                    <input class="floatnone" type="checkbox" name="append" id ="append" checked="checked"/> <?php echo JText::_('Append'); ?>
                                </div>
                            </div>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title">
                                    <?php echo JText::_('Response'); ?>:&nbsp;<font color="red">*</font></div>
                                <div class="js-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('responce', '', '', '300', '60', '20', false); ?></div>
                            </div>
                            <?php if ($this->isAttachmentPublished) { ?>
                                <div class="js-tk-tabs-wrapper js-mg-bottom">
                                    <div class="js-title"><?php echo JText::_('Attachments'); ?>:&nbsp;</div>
                                    <div class="js-value">
                                        <div id="js-attachment-files" class="js-attachment-files">
                                            <span class="js-value-text">
                                                <input type="file" class="inputbox" name="filename[]" onchange="uploadfile(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>
                                                <span class='js-attachment-remove'></span>
                                            </span>
                                        </div>
                                        <div id="js-attachment-option">
                                            <span class="js-attachment-ins">
                                                <small><?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                                            </span>
                                            <span id="js-attachment-add"><?php echo JText::_('Add Files'); ?></span>
                                        </div>
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title"><?php echo JText::_('Append signature'); ?>:&nbsp;</div>
                                <div class="js-value">
                                    <?php if ($this->isstaff == false) { ?>
                                        <div class="jsst-formfield-radio-button-wrp">
                                        <input class="setfloatoverride" type="radio" value="1" name="appendsignature" disabled /><?php echo JText::_('Own signature'); ?>
                                    </div>
                                    <div class="jsst-formfield-radio-button-wrp">
                                        <input class="setfloatoverride" type="radio" value="2" name="appendsignature" disabled /><?php echo JText::_('Department signature'); ?>
                                    </div>
                                    <div class="jsst-formfield-radio-button-wrp">
                                        <input class="setfloatoverride" type="radio" value="3" name="appendsignature" disabled /><?php echo JText::_('None'); ?>
                                        <br clear="all"/><font color="orangered">[<?php echo JText::_('To Use This Feature You Must Be Staff Memeber'); ?>]</font>
                                    </div>
                                    <?php } else { ?>
                                        <div class="jsst-formfield-radio-button-wrp">
                                        <input class="setfloatoverride" type="radio" value="1" name="appendsignature" checked=""/><?php echo JText::_('Own signature'); ?>
                                    </div>
                                    <div class="jsst-formfield-radio-button-wrp">
                                        <input class="setfloatoverride" type="radio" value="2" name="appendsignature" /><?php echo JText::_('Department signature'); ?>
                                    </div>
                                    <div class="jsst-formfield-radio-button-wrp">
                                        <input class="setfloatoverride" type="radio" value="3" name="appendsignature" /><?php echo JText::_('None'); ?>
                                    </div>
                                    <?php } ?>
                                </div>
                            </div>
                            <?php if (!$this->ticketdetail->staffid) { ?>
                                <div class="js-tk-tabs-wrapper js-mg-bottom">
                                    <div class="js-title"><?php echo JText::_('Assign ticket to staff'); ?>:&nbsp;</div>
                                    <div class="js-value">
                                        <div class="jsst-formfield-radio-button-wrp">
                                        <?php if ($this->isstaff == false) { ?>
                                            <input type="checkbox" name="assigntomyself" id ="assigntomyself" value="1" disabled /> &nbsp;<?php echo JText::_('Assign To Me'); ?> <br clear="all"/><font color="orangered">[<?php echo JText::_('To Use This Feature You Must Be Staff Memeber'); ?>]</font><?php } else { ?> <input type="checkbox" name="assigntomyself" id ="assigntomyself" value="1" checked="checked"/> &nbsp;<?php echo JText::_('Assign To Me'); ?>
                                    <?php } ?>
                                        </div> 
                                    </div>
                                </div>
                            <?php } ?>
                            <div class="js-tk-tabs-wrapper js-mg-bottom">
                                <div class="js-title"><?php echo JText::_('Ticket Status'); ?>:&nbsp;</div>
                                <div class="js-value">
                                    <div class="jsst-formfield-radio-button-wrp">
                                        <input type="checkbox" name="replystatus" id ="replystatus" value="4"/>
                                        <?php echo JText::_('Close on reply'); ?>
                                    </div>
                                </div>
                            </div>
                            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input  class="button setfloatoverride" type="button" onclick="validate_form_department(document.adminForm)" value="<?php echo JText::_('Post reply'); ?>"/></div></div>
                        </div> <!--  end div id=button -->
                    <?php } ?>
                </div><?php /* </div> */ ?>
                <div class="js-tkt-det-right">
                    <div  class="js-tkt-det-cnt js-tkt-det-tkt-info">
                        <?php if ($this->ticketdetail->lock == 1) { ?>
                            <div class="js-tkt-det-status" style="background-color: darkred;"><?php echo JText::_('Lock'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 0) { ?>
                            <div class="js-tkt-det-status" style="background-color: #9ACC00;"><?php echo JText::_('New'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 1) { ?>
                            <div class="js-tkt-det-status" style="background-color: orange;"><?php echo JText::_('Waiting reply'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 2) { ?>
                            <div class="js-tkt-det-status" style="background-color: #FF7F50;"><?php echo JText::_('In progress'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 3) { ?>
                            <div class="js-tkt-det-status" style="background-color: #507DE4;"><?php echo JText::_('Replied'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 4) { ?>
                            <div class="js-tkt-det-status" style="background-color: #CB5355;"><?php echo JText::_('Close'); ?></div>
                        <?php } elseif ($this->ticketdetail->status == 5) { ?>
                            <div class="js-tkt-det-status" style="background-color: #ee1e22;"><?php echo JText::_('Close due to merged'); ?></div>
                        <?php } ?>
                        <div class="js-tkt-det-info-cnt">
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo JText::_('Created'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo JHTML::_('date',strtotime($this->ticketdetail->created),'y-m-d H:i:s'); ?></span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo JText::_('Last Reply'); ?>&nbsp;:</span>
                                <span class="js-value"><?php if ($this->ticketdetail->lastreply == '' || $this->ticketdetail->lastreply == '0000-00-00 00:00:00') echo JText::_('Not given'); else echo JHtml::_('date',$this->ticketdetail->lastreply,$this->config['date_format']); ?></span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo JText::_('Due Date'); ?>&nbsp;:</span>
                                <span class="js-value"><?php if ($this->ticketdetail->duedate == '' || $this->ticketdetail->duedate == '0000-00-00 00:00:00') echo JText::_('Not given'); else echo JHtml::_('date',$this->ticketdetail->duedate,$this->config['date_format']); ?></span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo JText::_('Help Topic'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo JText::_($this->ticketdetail->helptopic); ?></span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo JText::_('Ticket Id'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo $this->ticketdetail->ticketid; ?>
                                    <a href="#" title="Copy" class="js-tkt-det-copy-id" id="ticketidcopybtn" data-ticket-hash-id = "<?php echo $this->ticketdetail->ticketid; ?>" success=<?php echo JText::_('Copied'); ?>><?php echo JText::_('Copy'); ?></a>
                                </span>
                            </div>
                            <div class="js-tkt-det-info-data">
                                <span class="js-title"><?php echo JText::_('Department'); ?>&nbsp;:</span>
                                <span class="js-value"><?php echo JText::_($this->ticketdetail->departmentname); ?></span>
                            </div>
                            <?php
                                $customfields = getCustomFieldClass()->userFieldsData(1);
                                foreach ($customfields as $field) {
                                    if($field->userfieldtype != 'termsandconditions'){
                                        echo getCustomFieldClass()->showCustomFields($field, 3 , $this->ticketdetail->params , $this->ticketdetail->id);   
                                    }    
                                }
                            ?>
                        </div>
                    </div>
                    <div class="js-tkt-det-cnt js-tkt-det-tkt-prty">
                        <div class="js-tkt-det-hdg">
                            <div class="js-tkt-det-hdg-txt">
                                <?php echo JText::_('Priority'); ?>
                            </div>
                            <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="chng-prority">
                                <?php echo JText::_('Change'); ?>
                            </a>
                        </div>
                        <div class="js-tkt-det-tkt-prty-txt" style="color:#FFFFF;background:<?php echo $this->ticketdetail->prioritycolour; ?>;"><?php echo JText::_($this->ticketdetail->priority); ?>
                        </div>
                    </div>
                    <div class="js-tkt-det-cnt js-tkt-det-tkt-assign">
                        <div class="js-tkt-det-hdg">
                            <div class="js-tkt-det-hdg-txt">
                                <?php echo JText::_('Ticket Assign and Transfer'); ?>
                            </div>
                        </div>
                        <div class="js-tkt-det-tkt-asgn-cnt">
                            <div class="js-tkt-det-hdg">
                                <div class="js-tkt-det-hdg-txt">
                                <?php if ($this->ticketdetail->firstname != "") { ?>
                                    <?php echo JText::_('Ticket assigned to'); ?>
                                    <?php }else{ ?>
                                        <?php echo JText::_('Not assigned to staff'); ?>
                                    <?php } ?>
                                </div>
                                <?php if($this->ticketdetail->status != 5){ ?>
                                <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="asgn-staff">
                                    <?php echo JText::_('Change'); ?>
                                </a>
                                <?php } ?>
                            </div>
                            <div class="js-tkt-det-info-wrp">
                                <?php if ($this->ticketdetail->firstname != "") { ?>
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
                                            <?php if ($this->ticketdetail->firstname != "") {
                                                echo $this->ticketdetail->firstname . " " . $this->ticketdetail->lastname;
                                            } ?>
                                        </div>
                                        <div class="js-tkt-det-user-data">
                                            <?php if ($this->ticketdetail->email != "") {
                                                echo $this->ticketdetail->email;
                                            }?>
                                        </div>
                                        <div class="js-tkt-det-user-data"></div>
                                    </div>
                                </div>
                                <?php }?>
                                <div class="js-tkt-det-trsfer-dep">
                                    <div class="js-tkt-det-trsfer-dep-txt">
                                        <?php echo JText::_('Department').JText::_(': '); ?>
                                        <?php if ($this->ticketdetail->departmentname != "") {
                                            echo $this->ticketdetail->departmentname;
                                        } ?>
                                    </div>
                                    <?php if($this->ticketdetail->status != 5){ ?>
                                    <a title="Change" href="#" class="js-tkt-det-hdg-btn" id="chng-dept">
                                        <?php echo JText::_('Change'); ?>
                                    </a>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                    </div>
                    <?php if(isset($this->usertickets) && !empty($this->usertickets)){ ?>
                        <div class="js-tkt-det-cnt js-tkt-det-user-tkts" id="usr-tkt">
                            <div class="js-tkt-det-hdg">
                                <div class="js-tkt-det-hdg-txt">
                                    <?php echo $this->ticketdetail->name . "'s "; ?>
                                    <?php echo JText::_('Tickets'); ?> 
                                </div>
                            </div>
                            <div class="js-tkt-det-usr-tkt-list">
                                <?php foreach($this->usertickets AS $userticket){ ?>
                                    <div class="js-tkt-det-user">
                                        <div class="js-tkt-det-user-image">
                                            <img src="components/com_jssupportticket/include/images/user.png" srcset="" class="avatar avatar-96 photo" height="96" width="96">
                                        </div>
                                        <div class="js-tkt-det-user-cnt">
                                            <div class="js-tkt-det-user-data name">
                                                <span id="usr-tkts">
                                                    <a title="view ticket" href="<?php echo 'index.php?option=' . $this->option . '&c=ticket&layout=ticketdetails&cid[]='.$userticket->id; ?>">
                                                        <span class="js-tkt-det-user-val">
                                                            <?php echo $userticket->subject; ?>
                                                        </span>
                                                    </a>
                                                </span>
                                            </div>
                                            <div class="js-tkt-det-user-data">
                                                <span class="js-tkt-det-user-tit"><?php echo JText::_('Department'); ?> : </span>
                                                <span class="js-tkt-det-user-val"><?php echo $userticket->departmentname; ?></span>
                                            </div>
                                            <div class="js-tkt-det-user-data">
                                                <span class="js-tkt-det-prty" style="background: <?php echo $userticket->prioritycolour; ?>;">
                                                    <?php echo JText::_($userticket->priority); ?>
                                                </span>
                                                <?php if ($userticket->status == 0) { ?>
                                                    <span class="js-tkt-det-status"><?php echo JText::_('New'); ?></span>
                                                <?php } elseif ($userticket->status == 1) { ?>
                                                    <span class="js-tkt-det-status"><?php echo JText::_('Waiting reply'); ?></span>
                                                <?php } elseif ($userticket->status == 2) { ?>
                                                    <span class="js-tkt-det-status"><?php echo JText::_('In progress'); ?></span>
                                                <?php } elseif ($userticket->status == 3) { ?>
                                                    <span class="js-tkt-det-status"><?php echo JText::_('Replied'); ?></span>
                                                <?php } elseif ($userticket->status == 4) { ?>
                                                    <span class="js-tkt-det-status"><?php echo JText::_('Close'); ?></span>
                                                <?php } elseif ($userticket->status == 5) { ?>
                                                    <span class="js-tkt-det-status"><?php echo JText::_('Close due to merged'); ?></span>
                                                <?php } ?>
                                            </div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        </div>
                    <?php } ?>  
                </div>
            </div>
            <!-- POPUP START -->
            <!-- priority popup -->
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
                    <button type="submit" class="js-ticket-priorty-save" id="changepriority"  onclick="actioncall(1)" ><?php echo JText::_('Save'); ?></button>
                </div>
                   
            </div>
            <!-- internal note popup -->
            <div id="userpopupforintnote" style="display:none;">
                <div class="js-ticket-priorty-header">
                    <?php echo JText::_('Post New Internal Note'); ?><span class="close-history"></span>
                </div>
                    <div class="js-ticket-priorty-fields-wrp">
                        <div class="js-tk-tabs-wrapper">

                            <div class="js-title"><?php echo JText::_('Note title'); ?>:&nbsp;<font color="red">*</font></div>
                            <div class="js-value"><input class="inputbox required" type="text" id="notetitle" name="notetitle" size="40" maxlength="255" value="" /></div>
                        </div>
                        <div class="js-tk-tabs-wrapper">
                            <div class="js-title"><?php echo JText::_('Internal Note'); ?>:&nbsp;<font color="red">*</font></div>
                            <div class="js-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('internalnote', '', '550', '300', '60', '20', false); ?>
                                
                            </div>
                        </div>
                        <div class="js-tk-tabs-wrapper">
                            <div class="js-title"><?php echo JText::_('Ticket Status'); ?>:&nbsp;</div>
                            <div class="js-value">
                                <div class="jsst-formfield-radio-button-wrp">
                                    <input type="checkbox" name="internalnotestatus" id ="internalnotestatus" value="4"/> <?php echo JText::_('Close on reply'); ?>
                                </div>
                            </div>
                        </div>
                        <div class="js-title"><?php echo JText::_('Attachment'); ?>:&nbsp;</div>
                        <div class="js-value">
                                <div id="js-attachment-files" class="js-attachment-files">
                                    <span class="js-value-text js-post-internal-note-input">
                                        <input type="file" class="inputbox" name="noteattachment" onchange="uploadfileNote(this, '<?php echo $this->config["filesize"]; ?>', '<?php echo $this->config["fileextension"]; ?>');" size="20" maxlenght='30'/>                                    
                                    </span>
                                </div>
                                <div id="js-attachment-option">
                                    <span class="js-attachment-ins">
                                        <small><?php echo JText::_('Maximum File Size') . ' (' . $this->config['filesize']; ?>KB)<br><?php echo JText::_('File Extension Type') . ' (' . $this->config['fileextension'] . ')'; ?></small>
                                    </span>
                                </div>
                        </div>
                        <div class="js-ticket-priorty-btn-wrp">
                            <button type="button" class="js-ticket-priorty-save" onclick="ticketinternalnote(true)" >
                                <?php echo JText::_('Post Internal Note'); ?>
                            </button>
                        </div>
                    </div>
            </div>
            <!-- change department popup -->
            <div id="userpopupforchangedepartment" style="display:none;">
                <div class="js-ticket-priorty-header">
                    <?php echo JText::_('Department Transfer'); ?><span class="close-history"></span>
                </div>
                <div class="js-ticket-priorty-fields-wrp">
                    <div class="js-tk-tabs-wrapper">
                        <div class="js-title"><?php echo JText::_('Department'); ?>:&nbsp;</div>
                        <div class="js-value"><?php echo $this->lists['departments']; ?></div>
                    </div>
                    <div class="js-tk-tabs-wrapper">
                        <div class="js-title"><?php echo JText::_('Reason for department transfer'); ?>:&nbsp;<font color="red">*</font></div>
                        <div class="js-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('departmenttranfer', '', '', '300', '60', '20', false); ?>
                        </div>
                    </div>
                    <div class="js-ticket-priorty-btn-wrp">
                        <button type="button" class="js-ticket-priorty-save"  onclick="ticketdepartmenttransfer(document.adminForm)" >
                            <?php echo JText::_('Department transfer'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <!-- assign staff popup -->
            <div id="userpopupforassignstaff" style="display:none;">
                <div class="js-ticket-priorty-header">
                    <?php echo JText::_('Assign To Staff'); ?><span class="close-history"></span>
                </div>
                <div class="js-ticket-priorty-fields-wrp">
                    <div class="js-tk-tabs-wrapper">
                        <div class="js-title"><?php echo JText::_('Staff member'); ?>:&nbsp;</div>
                        <div class="js-value"><?php echo $this->lists['staff']; ?></div>
                    </div>
                    <div class="js-tk-tabs-wrapper">
                        <div class="js-title"><?php echo JText::_('Internal Note'); ?>:&nbsp;<font color="red">*</font></div>
                        <div class="js-value"><?php $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor); echo $editor->display('assigntostaffnote', '', '', '300', '60', '20', false); ?></div>
                    </div>
                    <div class="js-ticket-priorty-btn-wrp">
                        <button type="button" class="js-ticket-priorty-save"  onclick="ticketstafftransfer(document.adminForm)" >
                            <?php echo JText::_('Assign'); ?>
                        </button>
                    </div>
                </div>
            </div>
            <!-- POPUP END -->
            <input type="hidden" name="id" value="<?php echo $this->ticketdetail->id; ?>" />
            <input type="hidden" name="ticketid" value="<?php echo $this->ticketdetail->ticketid; ?>" />
            <input type="hidden" name="hash" value="<?php echo $this->ticketdetail->hash; ?>" />
            <input type="hidden" name="email" value="<?php echo $this->ticketdetail->email; ?>" />
            <input type="hidden" name="email_ban" id="email_ban" value="<?php echo $this->isemailban; ?>" />
            <input type="hidden" name="lastreply" value="<?php echo $this->ticketdetail->lastreply; ?>" />
            <input type="hidden" id="staffid" name="staffid" value="<?php $staff = JSSupportticketCurrentUser::getInstance(); echo $staff->getStaffId(); ?>" />

            <input type="hidden" name="callaction" id="callaction" value="" />
            <input type="hidden" name="callfrom" id="callfrom" value="" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="c" value="ticket" />
            <input type="hidden" name="layout" value="tickets" />
            <input type="hidden" id="task" name="task" value="actionticket" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="created" value="<?php echo date('Y-m-d H:i:s'); ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>


<script type="text/javascript">
    function isTinyMCE(){
        is_tinyMCE_active = false;
        if (typeof(tinyMCE) != "undefined") {
            if(tinyMCE.editors.length > 0){
                is_tinyMCE_active = true;
            }
        }
        return is_tinyMCE_active;
    }
    function validate_form_department(f) {
        if(isTinyMCE()){
            var content = tinyMCE.get('responce').getContent();
        }else{
            var content = jQuery("textarea#responce").val();            
        }
        jQuery('#callfrom').val('postreply');
        if (content != '') {
            document.adminForm.submit();
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
    }

    function ticketinternalnote(f) {
        if(isTinyMCE()){
            var content = tinyMCE.get('internalnote').getContent();
        }else{
            var content = jQuery("textarea#internalnote").val();            
        }
        var title = jQuery('#notetitle').val();
        jQuery('#callfrom').val('internalnote');
        if (content != '' && title != '') {
            document.adminForm.submit();
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
    }

    function ticketdepartmenttransfer(f) {
        if(isTinyMCE()){
            var content = tinyMCE.get('departmenttranfer').getContent();
        }else{
            var content = jQuery("textarea#departmenttranfer").val();            
        }
        var depid = jQuery('#departmentid').val();
        jQuery('#callfrom').val('departmenttransfer');
        if (content != '' && depid != '') {
            document.adminForm.submit();
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
    }

    function ticketstafftransfer(f) {
        if(isTinyMCE()){
            var content = tinyMCE.get('assigntostaffnote').getContent();
        }else{
            var content = jQuery("textarea#assigntostaffnote").val();            
        }
        var staff_id = jQuery('#staff_id').find('option:selected').val();
        jQuery('#callfrom').val('stafftransfer');
        if (content != "" && staff_id != "") {
            document.adminForm.submit();
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
    }

    function getpremade(src, val, append) {
        var link = 'index.php?option=com_jssupportticket&c=ticket&task=getpremadeforinternalnote&<?php echo JSession::getFormToken(); ?>=1';
        jQuery.post(link,{val:val},function(data){
            if(data){
                if (append == true) {
                    if(isTinyMCE()){
                        var content = tinyMCE.get('responce').getContent();                        
                    }else{
                        var content = jQuery('textarea#responce').val();
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
                        jQuery('textarea#responce').val(data);
                    }
                }
            }
        });
    }

    function actioncall(value) {
        jQuery('#callfrom').val('action');
        jQuery('#callaction').val(value);
        document.adminForm.submit();
    }
    function closePopup() {
        jQuery('#popup-record-data').hide();
        jQuery("div.jsst-popup-wrapper").slideUp('slow');
        jQuery('#js-history-back').hide();
        jQuery('#js-private-crendentials-back').hide();
        setTimeout(function () {
            jQuery('div.jsst-popup-background').hide();
        }, 700);
    }
    function editResponce(id) {
        var rsrc = 'responce_' + id;
        var src = 'responce_edit_' + id;
        var esrc = 'editor_responce_' + id;
        showhide(rsrc, 'none');
        showhide(src, 'block');
        jQuery('#' + src).html("Loading...");
        jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=editresponce&id=' + id + '&<?php echo JSession::getFormToken(); ?>=1', {data: id}, function (data) {
            jQuery('#' + src).html(data); //retuen value
            if (!tinyMCE.get(esrc)) { // toggle editor
                tinyMCE.execCommand('mceToggleEditor', false, esrc);
                return false;
            }
        });
    }

    function saveResponce(id) {
        var esrc = 'editor_responce_' + id;
        if (!tinyMCE.get(esrc)) { // check toggle
            alert("Please toggle editor");
        } else {
            var contant = tinyMCE.get(esrc).getContent();
            var rsrc = 'responce_' + id;
            var src = 'responce_edit_' + id;
            showhide(rsrc, 'block');
            showhide(src, 'none');


            jQuery('#' + rsrc).html("Saving...");
            var arr = new Array();
            arr[0] = id;
            arr[1] = contant;
            jQuery.ajax({
                type: "POST",
                url: "index.php?option=com_jssupportticket&c=ticket&task=saveresponceajax&id=" + arr[0] + "&val=" + arr[1] + "&<?php echo JSession::getFormToken(); ?>=1",
                data: arr,
                success: function (data) {
                    if (data == 1) {
                        jQuery('#' + rsrc).html(contant);
                    } else if (data == 10) {
                        jQuery('#' + rsrc).html(data);
                    } else {
                        jQuery('#' + rsrc).html(data);
                    }
                    tinymce.remove(tinyMCE.get(esrc));

                }
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

    function deleteResponce(id) {
        if (confirm("<?php echo JText::_('Are you sure delete'); ?>")) {

            var rsrc = 'responce_' + id;
            jQuery('#' + rsrc).html("Deleting...");

            jQuery.post('index.php?option=com_jssupportticket&c=ticket&task=deleteresponceajax&id=' + id + '&<?php echo JSession::getFormToken(); ?>=1', {data: id}, function (data) {
                jQuery('#' + src).html(data);
            });
        }
    }
    function showhide(layer_ref, state) {
        if (state == 'none') {
            jQuery('div#' + layer_ref).hide('slow');
        } else if (state == 'block') {
            jQuery('div#' + layer_ref).show('slow');

        }
    }
</script>
