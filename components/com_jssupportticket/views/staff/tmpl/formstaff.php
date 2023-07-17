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
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/staff-formstaff.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){ ?>
                    <script language="javascript">
                        function validate_form(f) {
                            var c_p = jQuery('#changepermissions').val();
                            if (c_p == 0) {
                                jQuery("input[type=checkbox]").removeAttr("disabled");
                            }
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
                    <div id="userpopupblack" style="display:none;"></div>
                    <div id="userpopup" class="" style="display:none;"><!-- Select User Popup --> 
                        <div class="jsst-popup-header">
                            <div class="popup-header-text"><?php echo JText::_('Select user'); ?></div><div class="popup-header-close-img"></div>
                        </div>
                        <div class="js-ticket-popup-search-wrp">
                            <form id="userpopupsearch">
                                <div class="js-ticket-search-top">
                                    <div class="js-ticket-search-left">
                                        <div class="js-ticket-search-fields-wrp">
                                            <input class="js-ticket-search-input-fields" type="text" name="username" id="username" placeholder="<?php echo JText::_('Username'); ?>" />
                                            <input class="js-ticket-search-input-fields" type="text" name="name" id="name" placeholder="<?php echo JText::_('Name'); ?>" />
                                            <input class="js-ticket-search-input-fields" type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo JText::_('Email Address'); ?>"/>
                                        </div>
                                    </div>
                                    <div class="js-ticket-search-right">
                                        <div class="js-ticket-search-btn-wrp">
                                            <input value="<?php echo JText::_('Search'); ?>" type="submit" class="js-ticket-search-btn">
                                            <input type="submit" class="js-ticket-reset-btn" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo JText::_('Reset'); ?>" />
                                        </div>
                                    </div>
                                </div>    
                            </form>
                        </div>
                        <div id="records">
                            <div id="records-inner">
                                <div class="js-staff-searc-desc">
                                    <?php echo JText::_('Use Search Feature To Select The User'); ?>
                                </div>
                            </div>    
                        </div>          
                    </div>
                    <?php
                    JHTML::_('behavior.formvalidator');
                    JHTML::_('bootstrap.renderModal');
                    $document = JFactory::getDocument();
                    $document->addScript('administrator/components/com_jssupportticket/include/js/permission/permission.js');
                    //require_once JPATH_COMPONENT_SITE . '/views/ticket_header_bottom.php';
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
				            <a href="index.php?option=com_jssupportticket&c=staff&layout=staff&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
				                <?php echo JText::_('Staff Members'); ?>
				            </a>
				        </li>
				        <li>
				            <?php echo JText::_('Add Staff Member'); ?>
				        </li>
				    </ul>
				</div>
    			    </div>
                        <?php } ?>
        			</div>
                    <div class="js-ticket-add-form-wrapper">
                        <?php $userlink = 'index.php?option=com_jssupportticket&c=staff&layout=users&tmpl=component&task=preview'; ?>
                        <form class="js-ticket-form" action="index.php" method="POST" name="adminForm" id="adminForm" >
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="username">
                                        <?php echo JText::_('Username'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <div class="js-ticket-select-user-field">
                                        <input  class="js-ticket-form-field-input required" disabled="disabled" type="text" name="username" id="username" value="<?php if (isset($this->username)) {echo $this->username->username;} else {echo "";} ?>" />
                                    </div>
                                    <div class="js-ticket-select-user-btn">
                                        <a href="#" id="userpopup"><?php echo JText::_('Select User'); ?></a>
                                    </div>
                                </div>
                            </div>
                            <!-- Assign roles And Persmissions -->
                            <?php if ($this->assign_role == true) { ?>
                                <div class="js-ticket-from-field-wrp ">
                                    <div class="js-ticket-from-field-title">
                                        <label for="roleid">
                                            <?php echo JText::_('Roles'); ?>&nbsp;<font color="red">*</font>
                                        </label>
                                    </div>
                                    <div class="js-ticket-from-field js-ticket-form-field-select">
                                        <?php echo $this->lists['roles'] ?>
                                    </div>
                                </div>
                                <?php $change_permission_grant = ($this->assign_role == true) ? '' : 'disabled=disabled';
                                if ($this->staffid != "" && $this->staffid != 0 && is_numeric($this->staffid) == true) {  ?>
                                    <?php $deptext = JText::_('Department Section');
                                        $depid = "uad_alldepartmentaccess";
                                        $depclass = "uad_departmentaccess"; ?>

                                        <div id="rolepermissionedit">
                                            <?php if (isset($this->userdepartments) && is_array($this->userdepartments)) { ?>
                                                <div class="js-per-wrapper">
                                                    <div class="js-per-subheading">
                                                        <span class="head-text"><?php echo $deptext; ?></span>
                                                        <span class="head-checkbox"><input class="js-ticket-checkbox" type="checkbox" <?php echo $change_permission_grant; ?> id="<?php echo $depid; ?>" <?php if (!$this->staffid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $depid; ?>', '<?php echo $depclass; ?>');" /><label for="<?php echo $depid; ?>"><?php echo JText::_('Select / Deselect All'); ?></label> </span>
                                                    </div>
                                                    <?php
                                                    foreach ($this->userdepartments AS $dep) { ?>
                                                        <div class="js-per-data">
                                                            <?php
                                                            $dchecked_or_not = "";
                                                            if ($this->staffid) {  //edit case
                                                                if (isset($dep->userdepartmentid)) {
                                                                    $dchecked_or_not = ($dep->userdepartmentid == $dep->id) ? "checked='checked'" : "";
                                                                }
                                                            } else { //add case
                                                                $dchecked_or_not = "checked='checked'";
                                                            } ?>
                                                            <input type='checkbox' <?php echo $change_permission_grant; ?> class="<?php echo $depclass; ?> js-ticket-checkbox" id="<?php echo 'roledepdata_' . $dep->name; ?>" name='roledepdata[<?php echo $dep->name; ?>]' value="<?php echo $dep->id ?>" <?php echo $dchecked_or_not; ?> />
                                                            <label for="<?php echo 'roledepdata_' . $dep->name; ?>"><?php echo JText::_($dep->name); ?></label>
                                                        </div>
                                                    <?php } ?>
                                                </div> 
                                            <?php } ?>
                                            <div class="js-per-wrapper">
                                                <?php if (isset($this->userpermissions) && is_array($this->userpermissions)) {
                                                    $permission_keys = array_keys($this->permissionbysection);
                                                    foreach ($permission_keys AS $permissin_by_section) {
                                                        switch ($permissin_by_section) {
                                                            case 'ticket_section';
                                                                $text = JText::_('Ticket section');
                                                                $id = "t_s_allrolepermision";
                                                                $class = "t_s_rolepermission";
                                                                $section = 'ticke';
                                                                break;
                                                            case 'staff_section';
                                                                $text = JText::_('Staff section');
                                                                $id = "s_s_allrolepermision";
                                                                $class = "s_s_rolepermission";
                                                                $section = 'staff';

                                                                break;
                                                            case 'kb_section';
                                                                $text = JText::_('Knowledge base section');
                                                                $id = "kb_s_allrolepermision";
                                                                $class = "kb_s_rolepermission";
                                                                $section = 'kb';

                                                                break;
                                                            case 'faq_section';
                                                                $text = JText::_('FAQ section');
                                                                $id = "f_s_allrolepermision";
                                                                $class = "f_s_rolepermission";
                                                                $section = 'faqs';

                                                                break;
                                                            case 'download_section';
                                                                $text = JText::_('Download section');
                                                                $id = "d_s_allrolepermision";
                                                                $class = "d_s_rolepermission";
                                                                $section = 'downloads';

                                                                break;
                                                            case 'announcement_section';
                                                                $text = JText::_('Announcement section');
                                                                $id = "a_s_allrolepermision";
                                                                $class = "a_s_rolepermission";
                                                                $section = 'announcement';
                                                                break;
                                                            case 'mail_section';
                                                                $text = JText::_('Mail section');
                                                                $id = "m_s_allrolepermision";
                                                                $class = "m_s_rolepermission";
                                                                $section = 'mail';
                                                                break;
                                                        } ?>

                                                        <div class="js-per-subheading">
                                                            <span class="head-text"> <?php echo $text; ?> </span>
                                                            <span class="head-checkbox"> <input class="js-ticket-checkbox" type="checkbox" <?php echo $change_permission_grant; ?> id="<?php echo $id; ?>" <?php if (!$this->staffid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $id; ?>', '<?php echo $class; ?>');" /><label for="<?php echo $id; ?>"><?php echo JText::_('Select / Deselect All'); ?></label> </span>
                                                        </div>
                                                        <?php foreach ($this->permissionbysection[$permissin_by_section] AS $per) { ?>
                                                            <div class="js-per-data"> 
                                                                <?php $checked_or_not = "";
                                                                if ($this->staffid) {  //edit case
                                                                    if (isset($per->userpermissionid)) {
                                                                        $checked_or_not = ($per->userpermissionid == $per->id) ? "checked='checked'" : "";
                                                                    }
                                                                }else{ //add case
                                                                    $checked_or_not = "checked='checked'";
                                                                } ?>
                                                                <input type='checkbox' <?php echo $change_permission_grant; ?> id="<?php echo $per->permission. '_' . $section; ?>" class="<?php echo $class; ?> js-ticket-checkbox" name='roleperdata[<?php echo $per->permission; ?>]' value="<?php echo $per->id ?>" <?php echo $checked_or_not; ?> />
                                                                <label for="<?php echo $per->permission. '_' . $section ; ?>"><?php echo JText::_($per->permission); ?></label>
                                                            </div>
                                                        <?php }
                                                    }?>
                                                   
                                                    <?php
                                                }//end if case ?>
                                            </div>
                                            <div id='js-tk-per-ajax-bottom-border'></div>
                                        </div>
                                        <?php
                                    } ?>
                                    <div id="rolepermission"></div>
                            <?php } ?>

                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="firstname">
                                        <?php echo JText::_('First name'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="js-ticket-form-field-input required" type="text" id="firstname" name="firstname" value="<?php if (isset($this->staff)) echo $this->staff->firstname; ?>"/>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="lastname">
                                        <?php echo JText::_('Last name'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="js-ticket-form-field-input required" type="text" id="lastname" name="lastname" value="<?php if (isset($this->staff)) echo $this->staff->lastname; ?>"/>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="email">
                                        <?php echo JText::_('Email Address'); ?>&nbsp;<font color="red">*</font>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="js-ticket-form-field-input required validate-email" type="text" size="40" id="email" name="email" value="<?php if (isset($this->staff)) echo $this->staff->email; ?>"/>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="phone">
                                        <?php echo JText::_('Office Phone'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="js-ticket-form-field-input" type="text" id="phone" name="phone" value="<?php if (isset($this->staff)) echo $this->staff->phone; ?>"/>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="phoneext">
                                        <?php echo JText::_('Phone Ext'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="js-ticket-form-field-input" type="text" id="phoneext" name="phoneext" size="13" value="<?php if (isset($this->staff)) echo $this->staff->phoneext; ?>"/>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp">
                                <div class="js-ticket-from-field-title">
                                    <label for="mobile">
                                        <?php echo JText::_('Mobile no'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <input class="js-ticket-form-field-input" type="text" id="mobile" name="mobile" value="<?php if (isset($this->staff)) echo $this->staff->mobile; ?>"/>
                                </div>
                            </div>
                            <!-- Append Signature -->
                            <div class="js-ticket-append-signature-wrp js-ticket-append-signature-wrp-full-width">
                                <div class="js-ticket-append-field-title">
                                    <label for="appendsignature">
                                        <?php echo JText::_('Append Signature'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-append-field-wrp">
                                    <div class="js-ticket-signature-radio-box js-ticket-signature-radio-box-full-width ">
                                        <input class="radiobutton js-ticket-append-radio-btn" type="checkbox" name="appendsignature" id ="appendsignature" value="1" <?php if (isset($this->staff)) if ($this->staff->appendsignature == 1) echo "checked=''"; ?>/>
                                        <label for="appendsignature" class="tk_form_chkbox_label"><?php echo JText::_('Append Signature With Reply'); ?></label>
                                    </div>
                                    
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width ">
                                <div class="js-ticket-from-field-title">
                                    <label for="signature">
                                        <?php echo JText::_('Signature'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field">
                                    <textarea id="signature" name="signature"><?php if (isset($this->staff)) echo $this->staff->signature; ?></textarea>
                                </div>
                            </div>
                            <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                                <div class="js-ticket-from-field-title">
                                    <label for="active">
                                        <?php echo JText::_('Account Status'); ?>
                                    </label>
                                </div>
                                <div class="js-ticket-from-field js-ticket-form-field-select">
                                    <div class="js-ticket-radio-btn-wrp">
                                        <div class="js-form-radio-btn">
                                            <input class="js-ticket-radio-btn-status" type="radio" name="status" id="active" value="1" <?php if (isset($this->staff)) {if ($this->staff->status == 1) echo "checked=";} else echo "checked="; ?>/>
                                            <label class="radio-status" for="active"><?php echo JText::_('Active'); ?></label>
                                        </div>
                                        <div class="js-form-radio-btn">
                                            <input class="js-ticket-radio-btn-status" type="radio" name="status" id="lock" value="-1" <?php if (isset($this->staff)) if ($this->staff->status == -1) echo "checked="; ?>/>
                                            <label class="radio-status" for="lock"><?php echo JText::_('Disabled'); ?></label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="js-ticket-form-btn-wrp">
                                <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save'); ?>" />
                                <a href="index.php?option=com_jssupportticket&c=staff&layout=staff&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"><?php echo JText::_('Cancel'); ?></a>
                            </div>

                            <input type="hidden" name="created" value="<?php if (isset($this->staff)) { echo $this->staff->created; } else { $curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
                            <input type="hidden" name="update" value="<?php if (isset($this->staff)) {$update = date('Y-m-d H:i:s');echo $update;} ?>" />
                            <input type="hidden" id="staffid" name="id" value="<?php if (isset($this->staff->id)) {echo $this->staff->id;} ?>" />
                            <input type="hidden" name="uid" id="uid" class="js-ticket-form-field-input required" value="<?php if (isset($this->staff->uid)) { echo $this->staff->uid; } ?>" />
                            <input type="hidden" name="changepermissions" id="changepermissions" value="<?php echo $this->assign_permissions; ?>" />

                            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                            <input type="hidden" name="c" value="staff" />
                            <input type="hidden" name="view" value="staff" />
                            <input type="hidden" name="layout" value="formstaff" />
                            <input type="hidden" name="check" value="" />
                            <input type="hidden" name="task" value="savestaffmember" />
                            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                            <?php echo JHtml::_('form.token'); ?>
                        </form>
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
<script language="javascript">
    function updateuserlist(pagenum){
        var username = jQuery("input#username").val();
        var name = jQuery("input#name").val();
        var emailaddress = jQuery("input#emailaddress").val();
        jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1", {username:username,name:name,emailaddress:emailaddress,userlimit:pagenum}, function (data) {
            if(data){
                jQuery("div#records").html("");
                jQuery("div#records").html(data);
                setUserLink();
            }
        });
    }
    function setUserLink() {
        jQuery("a.js-userpopup-link").each(function () {
            var anchor = jQuery(this);
            jQuery(anchor).click(function (e) {
                var id = jQuery(this).attr('data-id');
                var name = jQuery(this).html();
                var email = jQuery(this).attr('data-email');
                var displayname = jQuery(this).attr('data-name');
                jQuery("input#username").val(name);
                if(jQuery('input#firstname').val() == ''){
                    jQuery('input#firstname').val(displayname);
                }
                if(jQuery('input#email').val() == ''){
                    jQuery('input#email').val(email);
                }
                jQuery("input#uid").val(id);
                jQuery("div#userpopup").slideUp('slow', function () {
                    jQuery("div#userpopupblack").hide();
                });
            });
        });
    }
    jQuery(document).ready(function ($) {
        jQuery("a#userpopup").click(function (e) {
            e.preventDefault();
            jQuery("div#userpopupblack").show();
            jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1",{},function(data){
              if(data){
                jQuery('div#records').html("");
                jQuery('div#records').html(data);
                setUserLink();
              }
            });
            jQuery("div#userpopup").slideDown('slow');
        });
        jQuery("form#userpopupsearch").submit(function (e) {
            e.preventDefault();
            var username = jQuery("input#username").val();
            var name = jQuery("input#name").val();
            var emailaddress = jQuery("input#emailaddress").val();
            jQuery.post("index.php?option=com_jssupportticket&c=staff&task=getusersearchajax&<?php echo JSession::getFormToken(); ?>=1", {name: name, emailaddress: emailaddress, username: username}, function (data) {
                if (data) {
                    jQuery("div#records").html(data);
                    setUserLink();
                }
            });//jquery closed
        });
        jQuery("div.popup-header-close-img, div#userpopupblack").click(function (e) {
            jQuery("div#userpopup").slideUp('slow', function () {
                jQuery("div#userpopupblack").hide();
            });

        });
    });
    function getrolepermission(roleid) {
        var c_p = jQuery('#changepermissions').val();
        var link = 'index.php?option=com_jssupportticket&c=rolepermissions&task=getRolePermissionForStaff&<?php echo JSession::getFormToken(); ?>=1';
        jQuery.post(link,{roleid : roleid, cp: c_p}, function(data){
            if (data) {
                result = JSON.parse(data);
                var isedit = jQuery('#staffid').val();
                if (isedit != '' && isedit != 0) {
                    jQuery('#rolepermissionedit').remove();
                }
                jQuery('#rolepermission').slideUp('slow');
                jQuery('#rolepermission').slideDown('slow');
                jQuery('#rolepermission').html(result);
            }
        });
    }

    function setuser(username, userid) {
        var isexist;
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_jssupportticket&c=staff&task=checkuserexist&val=" + userid + "&<?php echo JSession::getFormToken(); ?>=1",
            data: userid,
            success: function (data) {
                isexist = JSON.parse(data);
                if (isexist == 0) {
                    jQuery('#uid').val(userid);
                    jQuery('#username').val(username);
                    window.setTimeout('closeme();', 300);
                }
                else {
                    alert("<?php echo JText::_('User already staff member'); ?>");
                }
            }
        });

    }
    function closeme() {
        parent.SqueezeBox.close();
    }
</script>
</div>
