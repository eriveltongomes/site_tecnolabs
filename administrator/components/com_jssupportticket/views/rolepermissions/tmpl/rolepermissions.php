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
?>

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
                        <li><?php echo JText::_('Assign Role Permissions'); ?></li>
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
        <div id="js-tk-heading"><h1 class="jsstadmin-head-text"><?php echo JText::_('Role permissions'); ?></h1></div>
        <div id="jsstadmin-data-wrp" class="js-ticket-box-shadow">
        <form action="index.php" method="post" name="adminForm" id="adminForm">
            <?php 
            $deptext = JText::_('Department Section'); 
            ?>
            <div class="js-per-subheading">
                <span class="head-text"><?php echo $deptext; ?></span>
            </div>
            <div class="js-per-wrapper">
                <?php
                foreach ($this->roledepartment AS $dep) { ?>
                   <div class="js-col-md-4 js-per-datawrapper">                
                    <div class="js-per-data">
                        <?php $dchecked_or_not = ""; ?>
                        <?php if (isset($dep->roledepartmentid)) {
                            $dchecked_or_not = ($dep->roledepartmentid == $dep->id) ? "checked='checked'" : "";
                        } ?>
                        <input type='checkbox' disabled='disabled' name='roledepdata[<?php echo $dep->name; ?>]' value="<?php echo $dep->id ?>" <?php echo $dchecked_or_not; ?> />
                        <label for="<?php echo $dep->name; ?>"><?php echo JText::_($dep->name); ?></label>
                    </div> </div> <?php 
                } ?>
            </div>
                <?php
                $pgroup = "";
                foreach ($this->rolepermission AS $per) { ?>
                    <?php
                    if ($pgroup != $per->pgroup) {
                        $pgroup = $per->pgroup;
                        switch ($pgroup) {
                            case 1:
                                $text = JText::_('Ticket section');
                                break;
                            case 2:
                                $text = JText::_('Staff section');
                                break;
                            case 3:
                                $text = JText::_('Knowledge base section');
                                break;
                            case 4:
                                $text = JText::_('FAQ section');
                                break;
                            case 5:
                                $text = JText::_('Download section');
                                break;
                            case 6:
                                $text = JText::_('Announcement section');
                                break;
                        } ?>
                        
                        <div class="js-per-subheading">
                            <span class="head-text"><?php echo $text; ?></span>
                        </div> <?php
                    } ?>
                    <div class="js-per-wrapper">
                   <div class="js-col-md-4 js-per-datawrapper">
                    <div class="js-per-data">
                        <?php $checked_or_not = ""; ?>
                        <?php if (isset($per->rolepermissionid)) {
                            $checked_or_not = ($per->rolepermissionid == $per->id) ? "checked='checked'" : "";
                        } ?>
                        <input type='checkbox' disabled='disabled'  name='roleperdata[<?php echo $per->permission; ?>]' value="<?php echo $per->id ?>" <?php echo $checked_or_not; ?> />
                        <label for="<?php echo $per->permission; ?>"><?php echo JText::_($per->permission); ?></label>
                    </div> </div> 
</div>
                    <?php 
                } ?>
            </div>
            <input type="hidden" name="c" value="rolepermissions" />
            <input type="hidden" name="layout" value="rolepermissions" />
            <input type="hidden" name="boxchecked" value="0" />
            <input type="hidden" name="task" value="" />
            <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
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
