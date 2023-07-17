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
$editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
jimport('joomla.html.pane');
JHTML::_('behavior.formvalidator');
$document = JFactory::getDocument();
$document->addStyleSheet(JUri::root() . 'administrator/components/com_jssupportticket/include/css/jsticketadmin.css');
?>

<script type="text/javascript">
// for joomla 1.6
    Joomla.submitbutton = function (task) {
        if (task == '') {
            return false;
        } else {
            if (task == 'banemail' || task == 'banemailandnew' || task == 'banemailsave') {
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
                        <li><?php echo JText::_('Add Email'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Add Email'); ?></h1></div> 
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm">
            <div class="js-form-wrapper">
                <div class="js-title"><label for="email"><?php echo JText::_('Email'); ?><font color="red">*</font></label></div>
                <div class="js-value"><input class="inputbox required validate-email" type="text" name="email" id="email" size="40" maxlength="255" value="<?php if (isset($this->email)) echo $this->email->email; ?>" /></div>
            </div>
            <div class="js-col-xs-12 js-col-md-12"><div id="js-submit-btn"><input type="submit" class="button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Ban Email'); ?>" /></div></div>

            <input type="hidden" name="id" value="<?php if (isset($this->email)) echo $this->email->id; ?>" />
            <input type="hidden" name="c" value="emailbanlist" />
            <input type="hidden" name="task" value="banemail" />
            <input type="hidden" name="layout" value="formemail" />
            <input type="hidden" name="check" value="" />
            <input type="hidden" name="submitter" value="<?php echo JSSupportticketCurrentUser::getInstance()->getId(); ?>" />
            <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
            <input type="hidden" name="created" value="<?php if (!isset($this->email)) echo $curdate = date('Y-m-d H:i:s'); else echo $this->email->created; ?>"/>
            <input type="hidden" name="update" value="<?php if (isset($this->email)) echo $update = date('Y-m-d H:i:s'); ?>"/>
            <?php echo JHtml::_('form.token'); ?>
        </form>
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
