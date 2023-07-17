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
JHtml::_('bootstrap.tooltip');
JHtml::_('behavior.multiselect');
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
                        <li>
                            <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" title="Dashboard">
                                <?php echo JText::_('Dashboard'); ?>
                            </a>
                        </li>
                        <li>
                            <?php echo JText::_('Reports'); ?>
                        </li>
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
        <div id="js-tk-heading">
            <h1 class="jsstadmin-head-text"><?php echo JText::_('Reports'); ?></h4>
        </div>
        <div id="jsstadmin-data-wrp" class=" ">
            <a class="js-admin-report-wrapper" href="index.php?option=com_jssupportticket&c=reports&layout=overallreport" >
                <div class="js-admin-overall-report-type-wrapper">
                    <img src="components/com_jssupportticket/include/images/report/overall_icon.png" />
                    <span class="js-admin-staff-report-type-label"><?php echo JText::_('Overall Statistics'); ?></span>
                </div>
            </a>
            <a class="js-admin-report-wrapper" href="index.php?option=com_jssupportticket&c=reports&layout=staffreport" >
                <div class="js-admin-staff-report-type-wrapper">
                    <img src="components/com_jssupportticket/include/images/report/staff.png" />
                    <span class="js-admin-staff-report-type-label"><?php echo JText::_('Staff Reports'); ?></span>
                </div>
            </a>
            <a class="js-admin-report-wrapper" href="index.php?option=com_jssupportticket&c=reports&layout=userreport" >
                <div class="js-admin-user-report-type-wrapper">
                    <img src="components/com_jssupportticket/include/images/report/user.png" />
                    <span class="js-admin-user-report-type-label"><?php echo JText::_('User Reports'); ?></span>
                </div>
            </a> 
            <a class="js-admin-report-wrapper" href="index.php?option=com_jssupportticket&c=reports&layout=departmentreport" >
                <div class="js-admin-department-report-type-wrapper">
                    <img src="components/com_jssupportticket/include/images/report/department.png" />
                    <span class="js-admin-department-report-type-label"><?php echo JText::_('Department Reports'); ?></span>
                </div>
            </a>          
        </div>
    </div>
</div>
<div id="js-tk-copyright">
    <img width="85" src="https://www.joomsky.com/logo/jssupportticket_logo_small.png">&nbsp;Powered by <a target="_blank" href="https://www.joomsky.com">Joom Sky</a><br/>
    &copy;Copyright 2008 - <?php echo date('Y'); ?>, <a target="_blank" href="https://www.burujsolutions.com">Buruj Solutions</a>
</div>
