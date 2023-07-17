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
$colperrow = 4;
$colwidth = round(100 / $colperrow, 1);
$colwidth = $colwidth . '%';
$td = array('row0', 'row1');
$k = 0;


$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
if (JVERSION < 3) {
    JHtml::_('behavior.mootools');
    $document->addScript('components/com_jssupportticket/include/js/jquery.js');
} else {
    JHtml::_('bootstrap.framework');
    JHtml::_('jquery.framework');
}
global $mainframe;
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
        } else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        return true;
    }
</script>
<table width="100%" >
    <tr>
        <td align="left" width="175"  valign="top">
            <table width="100%" ><tr><td style="vertical-align:top;">
                    <?php
                    include_once('components/com_jssupportticket/views/menu.php');
                    ?>
                    </td>
                </tr></table>
        </td>
        <td width="100%" valign="top" align="left">
            <form action="index.php" method="POST" name="adminForm" id="adminForm" >
                <div id="button">
                    <table cellpadding="0" cellspacing="0" border="0" width="100%" class="adminlist">
                        <tr class="<?php echo $td[$k];
$k = 1 - $k; ?>">
                            <td valign="top" align="right"><label id="namemsg" for="name"><?php echo JText::_('Name'); ?>:&nbsp;<font color="red">*</font></label></td>
                            <td>
                                <input  class="inputbox required" type="text" name="name" id="name" value="<?php if (isset($this->role->name)) {
    echo $this->role->name;
} else {
    echo "";
} ?>" />
                            </td>
                        </tr>
                        <tr>
                            <td valign="top" align="right">
                            </td>
                            <td>
                                <div style="border: 1px solid #002a80;margin-bottom: 2px;">
                                    <table cellpadding="3" cellspacing="0" border="0" width="100%">
<?php
$deptext = JText::_('Department Section');
$depid = "rad_alldepartmentaccess";
$depclass = "rad_departmentaccess";
?>
                                        <tr style="text-align: center;font-weight: bold;font-size: medium;border-bottom: 1px solid #002a80">
                                            <td colspan="<?php echo $colperrow; ?>" valign="top" ><b><?php echo $deptext; ?>
                                                    <span style="float: right;text-align: right;font-size: small;font-weight: normal;">
                                                        <input  type="checkbox" id="<?php echo $depid; ?>" <?php if (!$this->roleid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $depid; ?>', '<?php echo $depclass; ?>');" /><?php echo JText::_('Select / Deselect All'); ?>
                                                    </span>
                                            </td>
                                        </tr>
                                        <tr>
                                                <?php $colcount = 0;
                                                foreach ($this->roledepartment AS $dep) {
                                                    ?>
                                                <?php if ($colcount == $colperrow) {
                                                    echo '</tr><tr>';
                                                    $colcount = 0;
                                                } $colcount++; ?>
                                                <td width="<?php echo $colwidth; ?>">
                                        <?php $dchecked_or_not = ""; ?>
                                            <?php if ($this->roleid) {  //edit case  ?>
                                                <?php if (isset($dep->roledepartmentid)) {
                                                    $dchecked_or_not = ($dep->roledepartmentid == $dep->id) ? "checked='checked'" : "";
                                                }; ?>
                                            <?php } else { //add case ?>
                                                <?php $dchecked_or_not = "checked='checked'" ?>
                                            <?php } ?>
                                                    <input type='checkbox' class="<?php echo $depclass; ?>" name='roledepdata[<?php echo $dep->name; ?>]' value="<?php echo $dep->id ?>" <?php echo $dchecked_or_not; ?> />
                                                    <label for="<?php echo $dep->name; ?>"><?php echo JText::_($dep->name); ?></label>
                                                </td>
                                        <?php } ?>
                                    </table>   
                                </div>
                                <div style="border: 1px solid #002a80;">
                                        <?php $pgroup = "";
                                        $colcount = 0; ?>
                                    <table cellpadding="3" cellspacing="0" border="0" width="100%">
                                        <?php foreach ($this->rolepermission AS $per) { ?>
                                            <?php
                                            if ($pgroup != $per->pgroup) {
                                                $pgroup = $per->pgroup;
                                                switch ($pgroup) {
                                                    case 1:
                                                        $text = JText::_('Ticket section');
                                                        $id = "t_s_allrolepermision";
                                                        $class = "t_s_rolepermission";
                                                        break;
                                                    case 2:
                                                        $text = JText::_('Staff section');
                                                        $id = "s_s_allrolepermision";
                                                        $class = "s_s_rolepermission";
                                                        break;
                                                    case 3:
                                                        $text = JText::_('Knowledge base section');
                                                        $id = "kb_s_allrolepermision";
                                                        $class = "kb_s_rolepermission";
                                                        break;
                                                    case 4:
                                                        $text = JText::_('FAQ section');
                                                        $id = "f_s_allrolepermision";
                                                        $class = "f_s_rolepermission";
                                                        break;
                                                    case 5:
                                                        $text = JText::_('Download section');
                                                        $id = "d_s_allrolepermision";
                                                        $class = "d_s_rolepermission";
                                                        break;
                                                    case 6:
                                                        $text = JText::_('Announcement section');
                                                        $id = "a_s_allrolepermision";
                                                        $class = "a_s_rolepermission";
                                                        break;
                                                }
                                                ?>
                                                <tr style="text-align: center;font-weight: bold;font-size: medium;border-bottom: 1px solid #002a80">
                                                    <td colspan="<?php echo $colperrow; ?>" valign="top" ><b><?php echo $text; ?>
                                                            <span style="float: right;text-align: right;font-size: small;font-weight: normal;">
                                                                <input  type="checkbox" id="<?php echo $id; ?>" <?php if (!$this->roleid) echo 'checked="checked"'; ?> onclick="selectdeseletsection('<?php echo $id; ?>', '<?php echo $class; ?>');" /><?php echo JText::_('Select / Deselect All'); ?>
                                                            </span>
                                                    </td>
                                                </tr>
                                                <tr>
                                                        <?php
                                                        switch ($pgroup) {
                                                            case 1:
                                                            case 2:
                                                            case 3:
                                                            case 4:
                                                            case 5:
                                                            case 6:
                                                                if ($colcount < $colperrow) {
                                                                    echo '</tr><tr>';
                                                                    $colcount = 0;
                                                                };
                                                                break;
                                                        }
                                                        ?>

    <?php } ?>
    <?php if ($colcount == $colperrow) {
        echo '</tr><tr>';
        $colcount = 0;
    } $colcount++; ?>
                                                <td width="<?php echo $colwidth; ?>">
                            <?php $checked_or_not = ""; ?>
                            <?php if ($this->roleid) {  //edit case ?>
                                <?php if (isset($per->rolepermissionid)) {
                                    $checked_or_not = ($per->rolepermissionid == $per->id) ? "checked='checked'" : "";
                                }; ?>
    <?php } else { //add case  ?>
        <?php $checked_or_not = "checked='checked'" ?>
    <?php } ?>
                                                    <input type='checkbox' class="<?php echo $class; ?>" name='roleperdata[<?php echo $per->permission; ?>]' value="<?php echo $per->id ?>" <?php echo $checked_or_not; ?> />
                                                    <label for="<?php echo $per->permission; ?>"><?php echo JText::_($per->permission); ?></label>
                                                </td>
<?php } ?>
                                    </table>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td  align="center" colspan="2">
                                <input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Role Permission'); ?>" />
                            </td>
                        </tr>
<?php if (!isset($this->role->id)) {
    $created = date('Y-m-d H:i:s')
    ?>
                            <input type="hidden" name="created" value="<?php echo $created; ?>" />
<?php } ?>   

                        <input type="hidden" name="id" value="<?php if (isset($this->role->id)) {
    echo $this->role->id;
} ?>" />
                        <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                        <input type="hidden" name="status" value="1" />
                        <input type="hidden" name="c" value="roles" />
                        <input type="hidden" name="layout" value="formrole" />
                        <input type="hidden" name="check" value="" />
                        <input type="hidden" name="task" value="saverole" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                    </table>
                </div>
                <?php echo JHtml::_('form.token'); ?>
            </form>
        </td>
    </tr>      
</table>				
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>

<script type="text/javascript">
    function selectdeseletsection(sectionid, sectionclass) {
        var obj = jQuery('#' + sectionid);
        if (obj.is(":checked")) {
            jQuery('.' + sectionclass).each(function () { //loop through each checkbox
                this.checked = true;  //select all checkboxes with class "rolepermission"              
            });
        } else {
            jQuery('.' + sectionclass).each(function () { //loop through each checkbox
                this.checked = false; //deselect all checkboxes with class "rolepermission"                      
            });
        }
    }
</script>
