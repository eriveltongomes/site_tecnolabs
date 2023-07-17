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
jimport('joomla.html.pane');
JHTML::_('behavior.formvalidator');
JHTML::_('bootstrap.renderModal');
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if ((task == 'saveknowledgebasecategorysavenew') || (task == 'saveknowledgebasecategory') || (task == 'saveknowledgebasecategorysave')) {
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
        var result = checkCategoryForSelected();
        if(result == false)
            return false;
        if (document.formvalidator.isValid(f)) {
            f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken(); else echo JSession::getFormToken(); ?>';//send token
        }
        else {
            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
            return false;
        }
        return true;
    }

    function checkCategoriesParent(val, type) {
        var parentid = jQuery("select#parentid").val();
        if (val == true || parentid != '') {
            jQuery.post('index.php?option=com_jssupportticket&c=knowledgebase&task=checkparenttype&<?php echo JSession::getFormToken(); ?>=1', {parentid: parentid, type: type}, function (data) {
                if (data) {
                    jQuery("div#msgshowcategory").html(data);
                }
            });
        } else {
            var currentid = jQuery("input#id").val();
            jQuery.post('index.php?option=com_jssupportticket&c=knowledgebase&task=checkchildtype&<?php echo JSession::getFormToken(); ?>=1', {currentid: currentid, type: type}, function (data) {
                if (data != 0) {
                    jQuery("div#msgshowcategory").html(data);
                    jQuery("input#" + type).attr('checked', 'true');
                }
            });
        }
    }
    function addTypeToParent(parentid, type) {
        jQuery.post('index.php?option=com_jssupportticket&c=knowledgebase&task=makeparentoftype&<?php echo JSession::getFormToken(); ?>=1', {parentid: parentid, type: type}, function (data) {
            if (data) {
                //jQuery("input#" + type).attr('readonly', 'readonly');
                jQuery("div#msgshowcategory").html('');
                jQuery("div#msgshowcategory").hide();
            }
        });
    }
    function getTypeForByParentId(parentid) {
        jQuery.post('index.php?option=com_jssupportticket&c=knowledgebase&task=gettypeforbyparentid&<?php echo JSession::getFormToken(); ?>=1', {parentid: parentid}, function (data) {
            if (data) {
                var array = jQuery.parseJSON(data);
                //reset the previous selection
                jQuery("input#kb").removeAttr('checked');
                jQuery("input#kb").removeAttr('readonly');
                jQuery("input#downloads").removeAttr('checked');
                jQuery("input#downloads").removeAttr('readonly');
                jQuery("input#announcement").removeAttr('checked');
                jQuery("input#announcement").removeAttr('readonly');
                jQuery("input#faqs").removeAttr('checked');
                jQuery("input#faqs").removeAttr('readonly');
                if (array['kb'] == 1) {
                    jQuery("input#kb").attr({'checked': 'true'});
                }
                if (array['downloads'] == 1) {
                    jQuery("input#downloads").attr({'checked': 'true'});
                }
                if (array['announcement'] == 1) {
                    jQuery("input#announcement").attr({'checked': 'true'});
                }
                if (array['faqs'] == 1) {
                    jQuery("input#faqs").attr({'checked': 'true'});
                }
            }
        });
    }
    function closemsg(type) {
        type = type;
        jQuery("input#" + type).attr('checked', false);
        jQuery("div#msgshowcategory").html('');
    }
    function checkCategoryForSelected(){
        var cat_for = jQuery('input[type="checkbox"]:checked').length;
        if (cat_for == 0) {
            alert("<?php echo JText::_('Please select atleast one category for'); ?>");
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
                        <li><?php echo JText::_('Add category'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Category'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="name"><?php echo JText::_('Category name'); ?>:&nbsp;<font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required" type="text" id="name" name="name" value="<?php if (isset($this->category)) echo $this->category->name; ?>"/></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Parent Category'); ?>:&nbsp;</div>
                <div class="js-value"><?php echo $this->lists['categories'] ?></div>
            </div>
            <?php /*<div class="js-col-xs-12 js-col-md-2 js-title"><?php echo JText::_('Type'); ?>:&nbsp;</div>
            <div class="js-col-xs-12 js-col-md-10 js-value">
                <input type="radio" name="type" id="publish" value="1" <?php if (isset($this->category)) {if ($this->category->type == 1) echo "checked="; } else echo "checked="; ?>/><label for="publish"><?php echo JText::_('Public'); ?></label>
                <input type="radio" name="type" id="private" value="0" <?php if (isset($this->category)) if ($this->category->type == 0) echo "checked="; ?>/><label for="private"><?php echo JText::_('Private'); ?></label>
            </div>*/ ?>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Logo'); ?>:&nbsp;</div>
                <div class="js-value"><input type="file" class="inputbox" id="filename" name="filename" size="20" maxlenght='30'/> 
                <?php
                    if(isset($this->category) AND $this->category->logo){
                        $logolink = JURI::root().$this->config['data_directory']."/attachmentdata/category/category_".$this->category->id."/".$this->category->logo;
                    ?>
                        <img style="width:70px;height:auto;" src="<?php echo $logolink;?>">           
                    <?php
                    }
                 ?> 
                </div>
            </div>
            <div id="msgshowcategory"></div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Category for'); ?>:&nbsp;</div>
                <div class="jsst-formfield-radio-button-wrap">
                    <input onchange="checkCategoriesParent(this.checked,'kb');" type="checkbox" name="kb" id="kb" value="1" <?php if (isset($this->category)) {if ($this->category->kb == 1) echo "checked="; } else echo "checked="; ?>/> <label for='kb'><?php echo JText::_('Knowledge base'); ?></label>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Category for'); ?>:&nbsp;</div>
                <div class="jsst-formfield-radio-button-wrap">
                    <input onchange="checkCategoriesParent(this.checked,'downloads');" type="checkbox" name="downloads" id="downloads" value="1" <?php if (isset($this->category)) {if ($this->category->downloads == 1) echo "checked="; } else echo "checked="; ?>/> <label for='downloads'><?php echo JText::_('Downloads'); ?></label>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Category for'); ?>:&nbsp;</div>
                <div class="jsst-formfield-radio-button-wrap">
                    <input onchange="checkCategoriesParent(this.checked,'faqs');" type="checkbox" name="faqs" id="faqs" value="1" <?php if (isset($this->category)) {if ($this->category->faqs == 1) echo "checked="; } else echo "checked="; ?>/> <label for='faqs'><?php echo JText::_('FAQs'); ?></label>
                </div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Category for'); ?>:&nbsp;</div>
                <div class="jsst-formfield-radio-button-wrap">
                    <input onchange="checkCategoriesParent(this.checked,'announcement');" type="checkbox" name="announcement" id="announcement" value="1" <?php if (isset($this->category)) {if ($this->category->announcement == 1) echo "checked="; } else echo "checked="; ?>/> <label for='announcement'><?php echo JText::_('Announcement'); ?></label>
                </div>
            </div>
            
            <div class="js-subheading"><?php echo JText::_('Meta Data Options'); ?></div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Meta description'); ?>:&nbsp;</div>
                <div class="js-value"><textarea class="js-form-textarea-field" id="metadesc" rows ="3" cols="40" name="metadesc"><?php if (isset($this->category)) echo $this->category->metadesc; ?></textarea></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Meta keywords'); ?>:&nbsp;</div>
                <div class="js-value"><textarea class="js-form-textarea-field" id="metakey" rows ="3" cols="40" name="metakey"><?php if (isset($this->category)) echo $this->category->metakey; ?></textarea></div>
            </div>
            <div class="js-form-wrapper">
                <div class="js-title"><?php echo JText::_('Status'); ?>:&nbsp;</div>
                <div class="js-value"><?php echo $this->lists['status']; ?></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Category'); ?>" /></div></div>

            <?php if (isset($this->category->created)) $created = $this->category->created; else $created = date('Y-m-d H:i:s'); ?>
            <input type="hidden" name="created" id="created" value="<?php if (isset($this->category)) {echo $this->category->created; } else {$curdate = date('Y-m-d H:i:s'); echo $curdate; } ?>" />
            <input type="hidden" name="id" id="id" value="<?php echo $this->categoryid; ?>" />
            <input type="hidden" name="Itemid" id="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="c" id="c" value="knowledgebase" />
            <input type="hidden" name="layout" id="layout" value="formcategory" />
            <input type="hidden" name="check" id="check" value="" />
            <input type="hidden" name="task" id="task" value="saveknowledgebasecategory" />
            <input type="hidden" name="option" id="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="created" id="created" value="<?php echo $created; ?>" />
            <?php echo JHtml::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
