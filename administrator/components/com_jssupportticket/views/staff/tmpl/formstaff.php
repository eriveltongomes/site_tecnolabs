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
JHTML::_('behavior.formvalidator');
JHTML::_('bootstrap.renderModal');
$document = JFactory::getDocument();
$document->addScript('components/com_jssupportticket/include/js/permission/permission.js');
global $mainframe;
?>

<script type="text/javascript">
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
        jQuery("span.close, div#userpopupblack").click(function (e) {
            jQuery("div#userpopup").slideUp('slow', function () {
                jQuery("div#userpopupblack").hide();
            });

        });
    });
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'savestaffmember' || task == 'savestaffmemberandnew' || task == 'savestaffmembersave') {
                returnvalue = validate_form(document.adminForm);
            } else
                returnvalue = true;
            if (returnvalue) {
                Joomla.submitform(task);
                return true;
            } else
                return false;
        }
    }
    function validate_form(f)
    {
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
<div id="userpopup" style="display:none;">
    <div>
        <form id="userpopupsearch">
            <div class="search-center">
                <div class="search-center-heading"><?php echo JText::_('Select user'); ?><span class="close"></span></div>
                <div class="js-col-md-12">
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="username" id="username" placeholder="<?php echo JText::_('Username'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="name" id="name" placeholder="<?php echo JText::_('Name'); ?>" />
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value">
                        <input type="text" name="emailaddress" id="emailaddress" placeholder="<?php echo JText::_('Email Address'); ?>"/>
                    </div>
                    <div class="js-col-xs-12 js-col-md-3 js-search-value-button">
                        <div class="js-button">
                            <input type="submit"class="js-button-search" value="<?php echo JText::_('Search'); ?>" />
                        </div>
                        <div class="js-button">
                            <input type="submit" class="js-button-reset" onclick="document.getElementById('name').value = '';document.getElementById('username').value = ''; document.getElementById('emailaddress').value = '';" value="<?php echo JText::_('Reset'); ?>" />
                        </div>
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
                        <li><?php echo JText::_('Add Staff Member'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text">
            <?php /* <img id="js-admin-responsive-menu-link" src="components/com_jssupportticket/include/images/c_p/left-icons/menu.png" /> */ ?>
            <?php echo JText::_('Add Staff Member'); ?></h1>
        </div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
                <div class="js-form-wrapper">
                    <div class="js-title">
                        <label for="username">
                            <?php echo JText::_('Username'); ?>:&nbsp;
                            <font color="red">*</font>
                        </label>
                    </div>
                    <div class="js-value">
                        <input  class="inputbox js-form-diabled-field required" type="text" name="username" id="username" value="<?php if (isset($this->user)) {echo $this->user->username; } else {echo ""; } ?>" />
                        <a id="userpopup" href="#"><?php echo JText::_('Select user') ?></a>
                    </div>
                </div>
                <div class="js-form-wrapper">
                    <div class="js-title"><label for="roleid"><?php echo JText::_('Role'); ?>:&nbsp;<font color="red">*</font></label></div>
                    <div class="js-value">
                        <?php echo $this->lists['roles'] ?>
                    </div>
                </div>
                <?php
                if ($this->staffid != "" && $this->staffid != 0 && is_numeric($this->staffid) == true) { ?>
                    <div class="js-col-md-12" id="rolepermissionedit">
                                                <?php
                        $deptext = JText::_('Department section');
                        $depid = "uad_alldepartmentaccess";
                        $depclass = "uad_departmentaccess";
                        ?>
                            <div class="js-per-subheading">
                                <span class="head-text"><?php echo $deptext; ?></span>
                                <span class="head-checkbox"><input  type="checkbox" id="<?php echo $depid; ?>" <?php if (!$this->staffid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $depid; ?>', '<?php echo $depclass; ?>');" /> <label for="<?php echo $depid; ?>"><?php echo JText::_('Select / Deselect All'); ?></label></span>
                            </div>
                        <div class="js-per-wrapper">
                            <?php
                            foreach ($this->userdepartments AS $dep) { ?>
                                <div class="js-col-md-4 js-per-datawrapper">
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
                                        <input type='checkbox' id="<?php echo 'roledepdata_' . $dep->name; ?>" class="<?php echo $depclass; ?>" name='roledepdata[<?php echo $dep->name; ?>]' value="<?php echo $dep->id ?>" <?php echo $dchecked_or_not; ?> />
                                        <label for="<?php echo 'roledepdata_' . $dep->name; ?>"><?php echo JText::_($dep->name); ?></label>
                                    </div>
                                </div>  <?php
                            } ?>
                        </div> <?php

                        $pgroup = "";
                        foreach ($this->userpermissions AS $per) {
                            if ($pgroup != $per->pgroup) {
                                $pgroup = $per->pgroup;
                                switch ($pgroup) {
                                    case 1:
                                        $text = JText::_('Ticket section');
                                        $id = "t_s_allrolepermision";
                                        $class = "t_s_rolepermission";
                                        $section = 'ticke';
                                        break;
                                    case 2:
                                        $text = JText::_('Staff section');
                                        $id = "s_s_allrolepermision";
                                        $class = "s_s_rolepermission";
                                        $section = 'staff';
                                        break;
                                    case 3:
                                        $text = JText::_('Knowledge base section');
                                        $id = "kb_s_allrolepermision";
                                        $class = "kb_s_rolepermission";
                                        $section = 'kb';
                                        break;
                                    case 4:
                                        $text = JText::_('FAQ section');
                                        $id = "f_s_allrolepermision";
                                        $class = "f_s_rolepermission";
                                        $section = 'faqs';
                                        break;
                                    case 5:
                                        $text = JText::_('Download section');
                                        $id = "d_s_allrolepermision";
                                        $class = "d_s_rolepermission";
                                        $section = 'downloads';
                                        break;
                                    case 6:
                                        $text = JText::_('Announcement section');
                                        $id = "a_s_allrolepermision";
                                        $class = "a_s_rolepermission";
                                        $section = 'announcement';
                                        break;
                                    case 7:
                                        $text = JText::_('Mail section');
                                        $id = "ms_s_allrolepermision";
                                        $class = "ms_s_rolepermission";
                                        $section = 'mail';
                                        break;
                                } ?>


                                <div class="js-per-subheading">
                                    <span class="head-text"><?php echo $text; ?></span>
                                    <span class="head-checkbox"><input  type="checkbox" id="<?php echo $id; ?>" <?php if (!$this->staffid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $id; ?>', '<?php echo $class; ?>');" /><label for="<?php echo $id; ?>" ><?php echo JText::_('Select / Deselect All'); ?></label></span>
                                </div>
                                <?php
                            } ?>
                            <div class="js-per-wrapper">
                                <div class="js-col-md-4 js-per-datawrapper">
                                    <div class="js-per-data">
                                        <?php
                                        $checked_or_not = "";
                                        if ($this->staffid) {  //edit case
                                            if (isset($per->userpermissionid)) {
                                                $checked_or_not = ($per->userpermissionid == $per->id) ? "checked='checked'" : "";
                                            }
                                        } else { //add case
                                            $checked_or_not = "checked='checked'";
                                        } ?>
                                        <input type='checkbox' id="<?php echo $section . '_' . $per->permission; ?>" class="<?php echo $class; ?>" name='roleperdata[<?php echo $per->permission; ?>]' value="<?php echo $per->id ?>" <?php echo $checked_or_not; ?> />
                                        <label for="<?php echo $section . '_' . $per->permission; ?>"><?php echo JText::_($per->permission); ?></label>
                                    </div>
                                </div>
                            </div>  <?php
                        } ?>
                        <div id='js-tk-per-ajax-bottom-border'></div>
                    </div><?php
                }//End staff edit ?>
            <div id="rolepermission"></div>
            <div class="js-form-wrapper">
                <div class="js-title">
                    <label for="firstname">
                        <?php echo JText::_('First name'); ?>:&nbsp;
                        <font color="red">*</font>
                    </label>
                </div>
                <div class="js-value">
                    <input class="inputbox required" type="text" id="firstname" name="firstname" value="<?php if (isset($this->staff)) echo $this->staff->firstname; ?>"/>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title">
                    <label for="lastname">
                        <?php echo JText::_('Last name'); ?>:&nbsp;
                        <font color="red">*</font>
                    </label>
                </div>
                <div class="js-value"><input class="inputbox required" type="text" id="lastname" name="lastname" value="<?php if (isset($this->staff)) echo $this->staff->lastname; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><label for="email"><?php echo JText::_('Email address'); ?>:&nbsp;<font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required validate-email" type="text" size="40" id="email" name="email" value="<?php if (isset($this->staff)) echo $this->staff->email; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Office phone'); ?>:&nbsp;</div>
                <div class="js-value"><input class="inputbox" type="text" id="phone" name="phone" value="<?php if (isset($this->staff)) echo $this->staff->phone; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Phone Ext'); ?>:&nbsp;</div>
                <div class="js-value"><input class="inputbox" type="text" id="phoneext" name="phoneext" maxlength="6" value="<?php if (isset($this->staff)) echo $this->staff->phoneext; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Mobile No'); ?>:&nbsp;</div>
                <div class="js-value"><input class="inputbox" type="text" id="mobile" name="mobile" value="<?php if (isset($this->staff)) echo $this->staff->mobile; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Append signature'); ?>:&nbsp;</div>
                <div class=" jsst-formfield-radio-button-wrap">
                <input class="floatnone" type="checkbox" name="appendsignature" id ="appendsignature" value="1" <?php if (isset($this->staff)) if ($this->staff->appendsignature == 1) echo "checked=''"; ?>/> <label for="appendsignature"><?php echo JText::_('Append'); ?></label>
                </div>
            </div>
            <div class="js-form-wrapper fullwidth">
                <div class="js-title"><?php echo JText::_('Signature'); ?>:&nbsp;</div>
                <div class="js-value"><textarea cols="30" rows="5" id="signature" name="signature"><?php if (isset($this->staff)) echo $this->staff->signature; ?></textarea></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Account Status'); ?>:&nbsp;</div>
                <div class="js-value-radio-btn">
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="status" id="active" value="1" <?php if (isset($this->staff)) {if ($this->staff->status == 1) echo "checked="; } else echo "checked="; ?>/><label for="active"><?php echo JText::_('Active'); ?></label>
                    </div>
                    <div class="jsst-formfield-status-radio-button-wrap">
                        <input type="radio" name="status" id="disable" value="-1" <?php if (isset($this->staff)) if ($this->staff->status == -1) echo "checked="; ?>/><label for="disable"><?php echo JText::_('Disabled'); ?></label>
                    </div>
                </div>
            </div>
            <div class=""><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Staff Member'); ?>" /></div></div>

            <input type="hidden" name="created" value="<?php if (isset($this->staff)) {echo $this->staff->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
            <input type="hidden" name="update" value="<?php if (isset($this->staff)) {$update = date('Y-m-d H:i:s'); echo $update; } ?>" />
            <input type="hidden" id="staffid" name="id" value="<?php if (isset($this->staff->id)) {echo $this->staff->id; } ?>" />
            <input type="hidden" name="uid" id="uid" value="<?php if (isset($this->staff)) {echo $this->staff->uid; } ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="c" value="staff" />
            <input type="hidden" name="layout" value="formstaff" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="task" value="savestaffmember" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <?php echo JHtml::_('form.token'); ?>
            </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

<script type="text/javascript"  language=Javascript>

    function getrolepermission(roleid) {
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_jssupportticket&c=rolepermissions&task=getRolePermissionForStaff&roleid=" + roleid + "&<?php echo JSession::getFormToken(); ?>=1",
            data: roleid,
            success: function (data) {
                var isedit = jQuery('#staffid').val();
                if (isedit != '' && isedit != 0) {
                    jQuery('#rolepermissionedit').remove();
                }
                jQuery('#rolepermission').slideUp();
                jQuery('#rolepermission').slideDown();
                jQuery('#rolepermission').html(data);
            }
        });
    }

    function getdepartmentsgroup(val) {
        var pagesrc = 'sf_dept';
        jQuery('#' + pagesrc).html("Loading ...");
        jQuery.ajax({
            type: "POST",
            url: "index.php?option=com_jssupportticket&c=staff&task=listdepartmentsbygroup&val=" + val + "&<?php echo JSession::getFormToken(); ?>=1",
            data: val,
            success: function (data) {
                jQuery('#' + pagesrc).html(data);
            }
        });
    }
    function setuser(username, userid) {
        var isexist;
        jQuery.post("index.php?option=com_jssupportticket&c=staff&task=checkuserexist&<?php echo JSession::getFormToken(); ?>=1",{val:userid},function(data){
            if(data == 0){
                document.getElementById('uid').value = userid;
                document.getElementById('username').value = username;
                window.setTimeout('closeme();', 300);
            }else {
                alert("<?php echo JText::_('User already staff member'); ?>");
            }
        });
    }
    function closeme() {
        parent.SqueezeBox.close();
    }

</script>
