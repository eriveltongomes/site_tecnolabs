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
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'saverole' || task == 'saveroleandnew' || task == 'saverolesave') {
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
                        <li><?php echo JText::_('Add Role'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Role'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label id="name" for="name" class="js-control-label js-col-sm-4"><?php echo JText::_('Name'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input  class="inputbox required" type="text" name="name" id="name" value="<?php if (isset($this->role->name)) {echo $this->role->name; } else {echo ""; } ?>" /></div>
            </div>
            <?php
            $deptext = JText::_('Department Section');
            $depid = "rad_alldepartmentaccess";
            $depclass = "rad_departmentaccess";
            ?>
            <div class="js-per-subheading">
                <span class="head-text"><?php echo $deptext; ?></span>
                <span class="head-checkbox"><input  type="checkbox" id="<?php echo $depid; ?>" <?php if (!$this->roleid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $depid; ?>', '<?php echo $depclass; ?>');" /><label for='<?php echo $depid; ?>'><?php echo JText::_('Select / Deselect All'); ?></label></span>
            </div>
            <div class="js-per-wrapper">
                <?php
                foreach ($this->roledepartment AS $dep) { ?>
                   <div class="js-col-md-4 js-per-datawrapper">
                   <div class="js-per-data">
                        <?php $dchecked_or_not = "";
                        if ($this->roleid) {  //edit case
                            if (isset($dep->roledepartmentid)) {
                                $dchecked_or_not = ($dep->roledepartmentid == $dep->id) ? "checked='checked'" : "";
                            }
                        }else{ //add case
                            $dchecked_or_not = "checked='checked'";
                        } ?>
                        <input type='checkbox' id='<?php echo "roledepdata_" . $dep->name; ?>' class="<?php echo $depclass; ?>" name='roledepdata[<?php echo $dep->name; ?>]' value="<?php echo $dep->id ?>" <?php echo $dchecked_or_not; ?> />
                        <label for='<?php echo "roledepdata_" . $dep->name; ?>'><?php echo JText::_($dep->name); ?></label>
                    </div></div> <?php 
                } ?>
            </div>
                    <?php 
                $pgroup = "";
                foreach ($this->rolepermission AS $per) {
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
                                $id = "m_s_allrolepermision";
                                $class = "m_s_rolepermission";
                                $section = 'mail';
                                break;
                        }
                        ?>
                        <div class="js-per-subheading">
                            <span class="head-text"><?php echo $text; ?></span>
                            <span class="head-checkbox"><input  type="checkbox" id="<?php echo $id; ?>" <?php if (!$this->roleid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $id; ?>', '<?php echo $class; ?>');" /><label for="<?php echo $id; ?>"> <?php echo JText::_('Select / Deselect All'); ?></label></span>
                        </div>
                        <?php 
                    } ?>
                    <div class="js-per-wrapper">
                        <div class="js-col-md-4 js-per-datawrapper">
                            <div class="js-per-data">
                                <?php $checked_or_not = "";
                                if ($this->roleid) {  //edit case
                                    if (isset($per->rolepermissionid)) {
                                        $checked_or_not = ($per->rolepermissionid == $per->id) ? "checked='checked'" : "";
                                    }
                                }else{ //add case
                                    $checked_or_not = "checked='checked'";
                                } ?>
                                <input type='checkbox' id="<?php echo $per->permission . '_' . $section; ?>" class="<?php echo $class; ?>" name='roleperdata[<?php echo $per->permission; ?>]' value="<?php echo $per->id ?>" <?php echo $checked_or_not; ?> />
                                <label for="<?php echo $per->permission . '_' . $section; ?>"><?php echo JText::_($per->permission); ?></label>
                         </div> </div>   </div> <?php 
                } ?>

                <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Role'); ?>" /></div></div>

                <?php if (!isset($this->role->id)) {$created = date('Y-m-d H:i:s') ?> <input type="hidden" name="created" value="<?php echo $created; ?>" /> <?php } ?>
                <input type="hidden" name="id" value="<?php if (isset($this->role->id)) {echo $this->role->id; } ?>" />
                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                <input type="hidden" name="status" value="1" />
                <input type="hidden" name="c" value="roles" />
                <input type="hidden" name="layout" value="formrole" />
                <input type="hidden" name="check" value="" />
                <input type="hidden" name="task" value="saverole" />
                <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                <?php echo JHtml::_('form.token'); ?>
            </div>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
