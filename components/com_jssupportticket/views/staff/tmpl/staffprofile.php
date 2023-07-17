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

<?php
$document = JFactory::getDocument();
$document->addScript('components/com_jssupportticket/include/js/jquery.form.js');
$document->addScript('administrator/components/com_jssupportticket/include/js/file/file_validate.js');
JText::script('Error file size too large');
JText::script('Error file extension mismatch');

if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/staff-staffprofile.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if(!$this->user->getIsGuest()){
        if($this->user->getIsStaff()){
            if(!$this->user->getIsStaffDisable()){?>
                <script type="text/javascript">
                        jQuery(document).ready(function ($) {
                            jQuery("a#userpopup").click(function (e) {
                                e.preventDefault();
                                jQuery("div#userpopup").slideDown('slow');
                            });
                            jQuery("span.close").click(function (e) {
                                jQuery("div#userpopup").slideUp('slow');
                            });
                            setUserLink();
                            function setUserLink() {
                                jQuery("a.js-userpopup-link").each(function () {
                                    var anchor = jQuery(this);
                                    jQuery(anchor).click(function (e) {
                                        var id = jQuery(this).attr('data-id');
                                        var name = jQuery(this).html();
                                        jQuery("div#username-div").html(name);
                                        jQuery("input#uid").val(id);
                                    });
                                });
                            }
                            jQuery("form#userpopupsearch").submit(function (e) {
                                e.preventDefault();
                                var name = jQuery("input#name").val();
                                var emailaddress = jQuery("input#emailaddress").val();
                                var link = 'index.php?option=/com_jssupportticket/c=staff&task=searchstaffprofileajax&<?php echo JSession::getFormToken(); ?>=1';
                                jQuery.post(link, {name: name, emailaddress: emailaddress}, function (data) {
                                    if (data) {
                                        jQuery("div#records").html(data);
                                    }
                                });//jquery closed
                            });
                            $('div.editable').each(function () {
                                var maindiv = $(this);
                                $(maindiv).mouseover(function () {
                                    var datafor = $(maindiv).attr('data-for');
                                    if (!($(maindiv).find('img#one').length > 0) && ($(maindiv).find('img#two').length <= 0)) {
                                        $(maindiv).append('<img id="one" class="js-ticket-profile-form-img" src="components/com_jssupportticket/include/images/roles_icons/edit.png" />');
                                        var img = $(maindiv).find('img');
                                        $(img).click(function (e) {
                                            var value = $(maindiv).find('input').val();
                                            var data = setEditOption(img, datafor, value);
                                            $(maindiv).html(data);
                                            $(maindiv).append('<img id="two" class="js-ticket-profile-form-img" src="components/com_jssupportticket/include/images/save.png" />');
                                            var save = $(maindiv).find('img#two');
                                            $(save).click(function (e) {
                                                var value = $('#' + datafor).val();
                                                var link = 'index.php?option=com_jssupportticket&c=staff&task=savestaffprofileajax&<?php echo JSession::getFormToken(); ?>=1';
                                                jQuery.post(link, {value: value, datafor: datafor}, function (data) {
                                                    if (data == '1') {
                                                        $(maindiv).html('<input type="text" class="js-ticket-form-field-input" value="'+value+'" > ');
                                                    } else {
                                                        alert("<?php echo JText::_("Some thing wrong try again later"); ?>");
                                                    }
                                                });
                                            });
                                        });
                                    }
                                });
                                $(this).mouseout(function () {
                                    // if (!$(this).find('img#one').is(':hover')) {
                                    //     //$(this).find('img#one').remove();
                                    // }
                                });
                            });
                            function setEditOption(img, datafor, value) {
                                switch (datafor) {
                                    case 'firstname':
                                        data = '<input type="text" name="firstname" id="firstname" class="js-ticket-form-field-input" value="' + value + '"/>';
                                        break;
                                    case 'lastname':
                                        data = '<input type="text" name="lastname" id="lastname" class="js-ticket-form-field-input" value="' + value + '"/>';
                                        break;
                                    case 'phone':
                                        data = '<input type="text" name="phone" id="phone" class="js-ticket-form-field-input" value="' + value + '"/>';
                                        break;
                                    case 'signature':
                                        data = '<textarea name="signature" id="signature" class="js-ticket-form-field-input">'  + value + '</textarea>';
                                        break;
                                }
                                return data;
                            }
                            
                            var options = { 
                                beforeSend: function(){
                                    jQuery("#progress").show();
                                    //clear everything
                                    jQuery("#bar").width('0%');
                                    jQuery("#message").html("");
                                    jQuery("#percent").html("0%");
                                },
                                uploadProgress: function(event, position, total, percentComplete){
                                    jQuery("#bar").width(percentComplete+'%');
                                    jQuery("#percent").html(percentComplete+'%');
                                },
                                success: function() {
                                    jQuery("#bar").width('100%');
                                    jQuery("#percent").html('100%');
                                },
                                complete: function(response){
                                    alert(response.responseText);
                                    var object = jQuery.parseJSON(response.responseText);
                                    var defualtimageid = object.defaultimageid;

                                    if(object.resultcode == 1){
                                        jQuery("#message").html("<font style='color:#fff'>"+object.msg+"</font>");
                                    }else if(object.resultcode == 2){
                                        jQuery("#message").html("<font style='color:#fff'>"+object.msg+"</font>");
                                    }
                                },
                                error: function(){
                                    jQuery("#message").html("<font style='color:#f6010d'> ERROR: unable to upload files</font>");
                                }
                            };
                             
                            jQuery("#jsst-myprofileimageform").ajaxForm(options);
                        
                            var options = {
                                //target:        '#percent',      // target element(s) to be updated with server response 
                                beforeSubmit: showRequest, // pre-submit callback 
                                success: showResponse, // post-submit callback 
                                url: 'index.php?option=com_jssupportticket&c=staff&task=uploadStaffImageajax'                 // since 2.8 ajaxurl is always defined in the admin header and points to admin-ajax.php     
                            };

                            // bind form using 'ajaxForm' 
                            $('#jsst-myprofileimageform').ajaxForm(options);
                            $("img.profile-image").mouseover(function () {
                                $('div#showhidemouseover').show();
                                if($("div#showhidemouseover").css('margin-top') == '0px' && $("span#submit_btn").css('display') == 'none'){   
                                    $('div#showhidemouseover').css("margin-top","30px");
                                    $('div#showhidemouseover').css("min-width","170px");
                                }
                                else{
                                    
                                }
                            }).mouseout(function () {
                                if (!$('div#showhidemouseover').is(':hover')) {
                                    //$('div#showhidemouseover').hide();
                                }
                                //$('div#showhidemouseover').hide();
                            });
                            $("#upload_field").click(function (e) {
                                $('#submit_btn').show();
                                $('#uploadbutton').hide();
                                $('#upload_field').hide();
                                $('div#showhidemouseover').css("margin-top","0px");
                                $('div#showhidemouseover').css("min-width","0px");
                            });

                        });
                        function showRequest(formData, jqForm, options) {
                            //do extra stuff before submit like disable the submit button
                            //jQuery('#percent').html('Sending...');
                            jQuery('#upload_btn').attr("disabled", "true");
                        }
                        function showResponse(responseText, statusText, xhr, $form) {
                            //do extra stuff after submit
                            var object = jQuery.parseJSON(responseText);
                            if (object.errorcode == true) {
                                jQuery('img.profile-image').attr('src', object.imagepath);
                            }
                            jQuery('#upload_btn').removeAttr("disabled");
                            jQuery('div#showhidemouseover').hide();
                            jQuery('div#showhidemouseover').css("margin-top","0px");
                            $('div#showhidemouseover').css("min-width","0px");
                        }
                        /*function uploadfile(fileobj, fileextensionallow) {
                            var file = fileobj.files[0];
                            var name = file.name;
                            var type = file.type;
                            var fileext = getExtension(name);
                            replace_txt = "<input type='file' class='inputbox' name='filename' onchange='uploadfile(this," + '"' + fileextensionallow + '"' + ");' size='20' maxlenght='30'/>";
                            var f_e_a = fileextensionallow.split(','); // file extension allow array
                            var isfileextensionallow = checkExtension(f_e_a, fileext);
                            if (isfileextensionallow == 'N') {
                                jQuery(fileobj).replaceWith(replace_txt);
                                alert(jQuery('span#fileext').html());
                                return false;
                            }
                            return true;
                        }*/
                        function newfunction1(){
                            jQuery('div#showhidemouseover').css("margin-top","0px");
                            jQuery('div#showhidemouseover').css("min-width","0px");
                        }
                        function  checkExtension(f_e_a, fileext) {
                            var match = 'N';
                            for (var i = 0; i < f_e_a.length; i++) {
                                if (f_e_a[i].toLowerCase() === fileext.toLowerCase()) {
                                    match = 'Y';
                                    return match;
                                }
                            }
                            return match;
                        }
                        function getExtension(filename) {
                            return filename.split('.').pop().toLowerCase();
                        }
                </script>
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
            		                    <?php echo JText::_('My Profile'); ?>
            		                </li>
            		            </ul>
            		        </div>
            		    </div>
                    <?php } ?>
        		</div>
                <div class="js-ticket-downloads-wrp">
                    <div class="js-ticket-downloads-heading-wrp">
                        <?php echo JText::_('My Profile') ?>
                    </div>
                    <div class="js-ticket-profile-wrp">
                        <form action="index.php"  id="jsst-myprofileimageform"  method="POST" name="adminForm" id="adminForm" enctype="multipart/form-data">
                            <div class="js-ticket-profile-left">
                                <div class="js-ticket-user-img-wrp">
                                    <?php $staff = $this->profiledata;
                                    if (!empty($staff->photo)) { ?>
                                        <img class="profile-image" src="<?php echo JURI::root().$this->config['data_directory'].'/staffdata/staff_' . $staff->id . '/'.$staff->photo; ?>">
                                    <?php } else { ?>
                                        <img class="profile-image" src="components/com_jssupportticket/include/images/defaultprofile.png">
                                    <?php } ?>
                                </div>
                                <div id="showhidemouseover" style="display:none;">
                                    <input type="hidden" name="c" value="staff" />
                                    <input type="hidden" name="task" value="uploadstaffimageajax" />
                                    <label for='upload' id="uploadbutton" class="js-ticket-file-upload-label"><?php echo JText::_('Select Image'); ?></label>
                                    <input type="file" id="upload_field" name="filename[]" class='inputbox js-ticket-upload-input' onchange="uploadfile(this,'<?php echo $this->config['filesize']; ?>','<?php echo $this->config['fileextension']; ?>'),newfunction1()" />
                                </div>
                                <span id="submit_btn" class="js-ticket-submit-btn" style="display:none;"><input type="submit" id="upload_btn" class="js-ticket-file-upload-label" value="Upload Image"></span>
                            </div>
                            <div class="js-ticket-profile-right">
                                <div class="js-ticket-add-form-wrapper">
                                    <div class="js-ticket-from-field-wrp  js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('Username'); ?>
                                        </div>
                                        <div class="js-ticket-from-field">
                                            <input type="text" name="title" class="js-ticket-form-field-input" value="<?php echo $staff->username; ?>" disabled >
                                        </div>
                                    </div>
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('Role'); ?>
                                        </div>
                                        <div class="js-ticket-from-field">
                                            <input type="text" name="title" class="js-ticket-form-field-input" value="<?php echo JText::_($staff->rolename); ?>" disabled >
                                        </div>
                                    </div>
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('First Name'); ?>
                                        </div>
                                        <div class="js-ticket-from-field editable" data-for="firstname">
                                            <input type="text" name="firstname" class="js-ticket-form-field-input" value="<?php echo $staff->firstname; ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('Last Name'); ?>
                                        </div>
                                        <div class="js-ticket-from-field editable" data-for="lastname">
                                            <input type="text" name="lastname" class="js-ticket-form-field-input" value="<?php echo $staff->lastname; ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('Email'); ?>
                                        </div>
                                        <div class="js-ticket-from-field">
                                            <input type="text" name="title" class="js-ticket-form-field-input" value="<?php echo $staff->email; ?>" disabled >
                                        </div>
                                    </div>
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('Phone No'); ?>
                                        </div>
                                        <div class="js-ticket-from-field editable" data-for="phone">
                                            <input type="text" name="phone" class="js-ticket-form-field-input" value="<?php echo $staff->phone; ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('Signature'); ?>
                                        </div>
                                        <div class="js-ticket-from-field editable" data-for="signature">
                                            <input type="text" name="signature" class="js-ticket-form-field-input" value="<?php echo $staff->signature; ?>" disabled>
                                        </div>
                                    </div>
                                    <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                        <div class="js-ticket-from-field-title">
                                            <?php echo JText::_('Account Status'); ?>
                                        </div>
                                        <div class="js-ticket-from-field">
                                            <input type="text" name="status" class="js-ticket-form-field-input" value="<?php echo ($staff->status == 1) ? JText::_('Active') : JText::_('Disabled'); ?>" disabled>
                                        </div>
                                    </div>
                                 </div>
                            </div>
                            <input type="hidden" name="id" value="<?php echo isset($staff->id) ? $staff->id : ''; ?>">
                            <input type="hidden" name="uid" value="<?php echo isset($staff->uid) ? $staff->uid : ''; ?>">
                            <input type="hidden" name="created" value="<?php echo isset($staff->created) ? $staff->created : ''; ?>">
                            <input type="hidden" name="updated" value="<?php echo isset($staff->updated) ? $staff->updated : ''; ?>">
                        </form>
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
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End ?>

