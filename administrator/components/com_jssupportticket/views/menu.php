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

$jinput = JFactory::getApplication()->input;
$c = $jinput->get('c');
$layout = $jinput->get('layout');
$tf = $jinput->get('tf');
$ff = $jinput->get('ff');
?>
<div id="jsstadmin-logo">
    <a id="js-tk-top-lefticon" title="JS Help Desk System" class="jsst-anchor" href="javascript:void(0)">
        <img alt="JS Help Desk System" src="components/com_jssupportticket/include/images/logo.png">
    </a>
    <img id="jsstadmin-menu-toggle" src="components/com_jssupportticket/include/images/c_p/left-icons/menu.png">
</div>
<div id="js-tk-links"  data-widget="tree">
    <div class="treeview js-divlink <?php if($c=='' || $c == 'jssupportticket'  && $layout != 'themes' || $c == 'systemerror' || $c == 'proinstaller') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/dashboard.png"/>
            <span class="text js-parent <?php if($c == 'jssupportticket' && $layout != 'themes' || ($c == 'proinstaller' && ($layout == 'step1' || $layout == 'step2'))) echo 'lastshown'; ?>"><?php echo JText::_('Home'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            
                <a class="js-child <?php if($c == 'jssupportticket' && ($layout == 'controlpanel' || $layout == '')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel"><span class="text"><?php echo JText::_('Control Panel'); ?></span></a>
            
                <a class="js-child <?php if($c == 'proinstaller' && ($layout == 'step1' || $layout == 'step2')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=proinstaller&layout=step1"><span class="text"><?php echo JText::_('Update'); ?></span></a>
            
                <a class="js-child <?php if($c == 'jssupportticket' && ($layout == 'aboutus' || $layout == '')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=aboutus"><span class="text"><?php echo JText::_('About Us'); ?></span></a>

        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'ticket' || $c == 'ticketviaemail' || $c == 'userfields' &&( $layout == 'export') || $layout == 'export' || $ff == '1' ) echo 'active'; ?> ">
        <a href="index.php?option=com_jssupportticket&c=ticket&layout=tickets" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/tickets.png"/>
            <span class="text js-parent <?php if($c == 'ticket' || $c=='ticketviaemail' || ($c=='reports' && $layout=='export') || ($c == 'userfields' && $layout=='fieldsordering' && $ff == '1')) echo 'lastshown'; ?>"><?php echo JText::_('Tickets'); ?></span>
        </a>
        <div class="treeview-menu js-innerlink">
            <a class="js-child <?php if($c == 'ticket' && ($layout == 'tickets')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=ticket&layout=tickets"><span class="text"><?php echo JText::_('Tickets'); ?></span></a>
            <a class="js-child <?php if($c == 'ticket' && ($layout == 'formticket')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=ticket&layout=formticket"><span class="text"><?php echo JText::_('Create Ticket'); ?></span></a>
            <a class="js-child <?php if($c == 'ticketviaemail' && ($layout == 'ticketviaemail')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=ticketviaemail&layout=ticketviaemail"><span class="text"><?php echo JText::_('Ticket via Email'); ?></span></a>
            <a class="js-child <?php if($c == 'reports' && ($layout == 'export')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=reports&layout=export"><span class="text"><?php echo JText::_('Export'); ?></span></a>
            <a class="js-child <?php if($ff == '1') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering&ff=1"><span class="text"><?php echo JText::_('Fields'); ?></span></a>
        </div>
    </div>
    <div class=" treeview js-divlink  <?php if($c == 'staff' || $c == 'userpermissions') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=staff&layout=staffmembers" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/users.png"/>
            <span class="text js-parent <?php if($c == 'staff') echo 'lastshown'; ?>"><?php echo JText::_('Staff members'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'staff' && ($layout == 'staffmembers') || $c == 'userpermissions') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=staff&layout=staffmembers"><span class="text"><?php echo JText::_('Staff members'); ?></span></a>
            <a class="js-child <?php if($c == 'staff' && ($layout == 'formstaff')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=staff&layout=formstaff"><span class="text"><?php echo JText::_('Add staff member'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink <?php if($c == 'config') echo 'active'; ?> <?php if($c == 'jssupportticket' && $layout == 'themes') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=config&layout=config" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/settings.png"/>
            <span class="text js-parent <?php if($c == 'config' || $layout == 'themes') echo 'lastshown'; ?>"><?php echo JText::_('Configurations'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'config' && ($layout == 'config')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=config&layout=config"><span class="text"><?php echo JText::_('Configurations'); ?></span></a>
            <a class="js-child <?php if($c == 'jssupportticket' && $layout == 'themes') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=jssupportticket&layout=themes"><span class="text"><?php echo JText::_('Themes'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink <?php if($c == 'gdpr') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=gdpr&layout=gdprfields" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/lock.png"/>
            <span class="text js-parent <?php if($c == 'gdpr' || $layout == 'erasedatarequests') echo 'lastshown'; ?>"><?php echo JText::_('GDPR'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'gdpr' && $layout == 'erasedatarequests') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=gdpr&layout=erasedatarequests"><span class="text"><?php echo JText::_('Erase Data Requests'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink <?php if($c == 'knowledgebase' && ($layout == 'categories' || $layout == 'formcategory')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=knowledgebase&layout=categories" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/category.png"/>
            <span class="text js-parent <?php if($c == 'knowledgebase' && ($layout == 'categories' || $layout == 'formcategory')) echo 'lastshown'; ?>"><?php echo JText::_('Categories'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'knowledgebase' && $layout == 'categories') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=categories"><span class="text"><?php echo JText::_('Categories'); ?></span></a>
            <a class="js-child <?php if($c == 'knowledgebase' && $layout == 'formcategory') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=formcategory"><span class="text"><?php echo JText::_('Add Category'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'knowledgebase' && ($layout == 'articles' || $layout == 'formarticle')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=knowledgebase&layout=articles" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/kb.png"/>
            <span class="text js-parent <?php if($c == 'knowledgebase' && ($layout == 'articles' || $layout == 'formarticle')) echo 'lastshown'; ?>"><?php echo JText::_('Knowledge Base'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'knowledgebase' && $layout == 'articles') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=articles"><span class="text"><?php echo JText::_('Knowledge Base'); ?></span></a>
            <a class="js-child <?php if($c == 'knowledgebase' && $layout == 'formarticle') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=formarticle"><span class="text"><?php echo JText::_('Add Knowledge Base'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'downloads' && ($layout == 'downloads' || $layout == 'formdownload')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=downloads&layout=downloads" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/download.png"/>
            <span class="text js-parent <?php if($c == 'downloads') echo 'lastshown'; ?>"><?php echo JText::_('Downloads'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'downloads' && $layout == 'downloads') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=downloads&layout=downloads"><span class="text"><?php echo JText::_('Downloads'); ?></span></a>
            <a class="js-child <?php if($c == 'downloads' && $layout == 'formdownload') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=downloads&layout=formdownload"><span class="text"><?php echo JText::_('Add Download'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'announcements' && ($layout == 'announcements' || $layout == 'formannouncement')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=announcements&layout=announcements" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/announcements.png"/>
            <span class="text js-parent <?php if($c == 'announcements') echo 'lastshown'; ?>"><?php echo JText::_('Announcements'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'announcements' && $layout == 'announcements') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=announcements&layout=announcements"><span class="text"><?php echo JText::_('Announcements'); ?></span></a>
            <a class="js-child <?php if($c == 'announcements' && $layout == 'formannouncement') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=announcements&layout=formannouncement"><span class="text"><?php echo JText::_('Add Announcement'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'faqs' && ($layout == 'faqs' || $layout == 'formfaq')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=faqs&layout=faqs" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/faq.png"/>
            <span class="text js-parent <?php if($c == 'faqs') echo 'lastshown'; ?>"><?php echo JText::_('FAQs'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'faqs' && $layout == 'faqs') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=faqs&layout=faqs"><span class="text"><?php echo JText::_('FAQs'); ?></span></a>
            <a class="js-child <?php if($c == 'faqs' && $layout == 'formfaq') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=faqs&layout=formfaq"><span class="text"><?php echo JText::_('Add FAQ'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'department' && ($layout == 'departments' || $layout == 'formdepartment')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=department&layout=departments" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/department.png"/>
            <span class="text js-parent <?php if($c == 'department') echo 'lastshown'; ?>"><?php echo JText::_('Departments'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'department' && $layout == 'departments') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=department&layout=departments"><span class="text"><?php echo JText::_('Departments'); ?></span></a>
            <a class="js-child <?php if($c == 'department' && $layout == 'formdepartment') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=department&layout=formdepartment"><span class="text"><?php echo JText::_('Add Department'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'helptopic' && ($layout == 'helptopices' || $layout == 'formhelptopic')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=helptopic&layout=helptopices" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/help-topic.png"/>
            <span class="text js-parent <?php if($c == 'helptopic') echo 'lastshown'; ?>"><?php echo JText::_('Help Topics'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'helptopic' && $layout == 'helptopices') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=helptopic&layout=helptopices"><span class="text"><?php echo JText::_('Help Topics'); ?></span></a>
            <a class="js-child <?php if($c == 'helptopic' && $layout == 'formhelptopic') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=helptopic&layout=formhelptopic"><span class="text"><?php echo JText::_('Add Help Topic'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'premade' && ($layout == 'departmentspremade' || $layout == 'formpremade')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=premade&layout=departmentspremade" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/premade-messages.png"/>
            <span class="text js-parent <?php if($c == 'premade') echo 'lastshown'; ?>"><?php echo JText::_('Premade'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'premade' && $layout == 'departmentspremade') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=premade&layout=departmentspremade"><span class="text"><?php echo JText::_('Premade'); ?></span></a>
            <a class="js-child <?php if($c == 'premade' && $layout == 'formpremade') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=premade&layout=formpremade"><span class="text"><?php echo JText::_('Add Premade Message'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'priority' && ($layout == 'priorities' || $layout == 'formpriority')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=priority&layout=priorities" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/priorities.png"/>
            <span class="text js-parent <?php if($c == 'priority') echo 'lastshown'; ?>"><?php echo JText::_('Priorities'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'priority' && $layout == 'priorities') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=priority&layout=priorities"><span class="text"><?php echo JText::_('Priorities'); ?></span></a>
            <a class="js-child <?php if($c == 'priority' && $layout == 'formpriority') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=priority&layout=formpriority"><span class="text"><?php echo JText::_('Add Priority'); ?></span></a>
        </div>
    </div>
    <div class="treeview js-divlink  <?php if($c == 'roles' && ($layout == 'roles' || $layout == 'formrole') || $c == 'rolepermissions') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=roles&layout=roles" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/tags.png"/>
            <span class="text js-parent <?php if($c == 'roles') echo 'lastshown'; ?>"><?php echo JText::_('Roles'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'roles' && $layout == 'roles' || $c == 'rolepermissions') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=roles&layout=roles"><span class="text"><?php echo JText::_('Roles'); ?></span></a>
            <a class="js-child <?php if($c == 'roles' && $layout == 'formrole') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=roles&layout=formrole"><span class="text"><?php echo JText::_('Add Role'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'feedback' && ($layout == 'feedbacks')) echo 'active'; ?><?php if($ff ==2) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=feedback&layout=feedbacks" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/feedback.png"/>
            <span class="text js-parent <?php if($c == 'feedback') echo 'lastshown'; ?>"><?php echo JText::_('Feedback'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'feedback' && $layout == 'feedbacks') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=feedback&layout=feedbacks"><span class="text"><?php echo JText::_('Feedback'); ?></span></a>
            <a class="js-child <?php if($ff == '2') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=userfields&layout=fieldsordering&ff=2"><span class="text"><?php echo JText::_('Feedback Fields'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'email' && ($layout == 'emails' || $layout == 'formemail')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=email&layout=emails" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/system-email.png"/>
            <span class="text js-parent <?php if($c == 'email') echo 'lastshown'; ?>"><?php echo JText::_('System Emails'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'email' && $layout == 'emails') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=email&layout=emails"><span class="text"><?php echo JText::_('Emails'); ?></span></a>
            <a class="js-child <?php if($c == 'email' && $layout == 'formemail') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=email&layout=formemail"><span class="text"><?php echo JText::_('Add Email'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'mail') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=mail&layout=inbox" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/message.png"/>
            <span class="text js-parent <?php if($c == 'mail') echo 'lastshown'; ?>"><?php echo JText::_('Mail'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'mail') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=mail&layout=inbox"><span class="text"><?php echo JText::_('Mail'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'emailbanlist' || $c == 'emailbanlistlog'  && ($layout == 'emailbanlists' || $layout == 'formemailbanlist' || $layout == 'emailbanlistlog')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=emailbanlist&layout=emailbanlists" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/ban.png"/>
            <span class="text js-parent <?php if($c == 'emailbanlist' || $c == 'emailbanlistlog') echo 'lastshown'; ?>"><?php echo JText::_('Banned Emails'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'emailbanlist' && $layout == 'emailbanlists') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailbanlist&layout=emailbanlists"><span class="text"><?php echo JText::_('Ban Emails'); ?></span></a>
            <a class="js-child <?php if($c == 'emailbanlist' && $layout == 'formemailbanlist') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailbanlist&layout=formemailbanlist"><span class="text"><?php echo JText::_('Add Email'); ?></span></a>
            <a class="js-child <?php if($c == 'emailbanlistlog' && $layout == 'emailbanlistlog') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailbanlistlog&layout=emailbanlistlog"><span class="text"><?php echo JText::_('Banlist log'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'emailtemplate') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=tk-ew-ad" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/email-templates.png"/>
            <span class="text js-parent <?php if($c == 'emailtemplate') echo 'lastshown'; ?>"><?php echo JText::_('Email Templates'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($tf == 'tk-ew-ad') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=tk-ew-ad"><span class="text"><?php echo JText::_('New Ticket Admin Alert'); ?></span></a>
            <a class="js-child <?php if($tf == 'ew-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=ew-tk"><span class="text"><?php echo JText::_('New Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'sntk-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=sntk-tk"><span class="text"><?php echo JText::_('Staff Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'rs-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=rs-tk"><span class="text"><?php echo JText::_('Reassign Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'cl-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=cl-tk"><span class="text"><?php echo JText::_('Close Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'dl-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=dl-tk"><span class="text"><?php echo JText::_('Delete Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'mo-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=mo-tk"><span class="text"><?php echo JText::_('Mark Overdue'); ?></span></a>
            <a class="js-child <?php if($tf == 'be-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=be-tk"><span class="text"><?php echo JText::_('Ban email'); ?></span></a>
            <a class="js-child <?php if($tf == 'dt-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=dt-tk"><span class="text"><?php echo JText::_('Department Transfer'); ?></span></a>
            <a class="js-child <?php if($tf == 'ebct-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=ebct-tk"><span class="text"><?php echo JText::_('Ban Email and Close Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'ube-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=ube-tk"><span class="text"><?php echo JText::_('Unban Email'); ?></span></a>
            <a class="js-child <?php if($tf == 'rsp-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=rsp-tk"><span class="text"><?php echo JText::_('Response Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'rpy-tk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=rpy-tk"><span class="text"><?php echo JText::_('Reply Ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'be-trtk') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=be-trtk"><span class="text"><?php echo JText::_('Ban email try to create ticket'); ?></span></a>
            <a class="js-child <?php if($tf == 'd-us-da') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=d-us-da"><span class="text"><?php echo JText::_('Erase User Data'); ?></span></a>
            <a class="js-child <?php if($tf == 'd-us-da-ad') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=d-us-da-ad"><span class="text"><?php echo JText::_('Erase User Data for admin'); ?></span></a>
            <a class="js-child <?php if($tf == 'u-da-de') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=emailtemplate&layout=emailtemplate&tf=u-da-de"><span class="text"><?php echo JText::_('User data deleted'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'systemerrors') echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=systemerrors&layout=systemerrors" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/system-error.png"/>
            <span class="text js-parent <?php if($c == 'systemerrors') echo 'lastshown'; ?>"><?php echo JText::_('System Errors'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'systemerrors' && $layout == 'systemerrors') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=systemerrors&layout=systemerrors"><span class="text"><?php echo JText::_('System Errors'); ?></span></a>
        </div>
    </div>
    <div class="js-divlink treeview <?php if($c == 'reports' && ($layout == 'overallreport' || $layout == 'staffreport'|| $layout == 'staffdetailreport'|| $layout == 'stafftimereport' || $layout == 'departmentreport' || $layout == 'departmentdetailreport' || $layout == 'userreport' || $layout == 'userdetailreport' || $layout == 'satisfactionreport')) echo 'active'; ?>">
        <a href="index.php?option=com_jssupportticket&c=reports&layout=reports" class="js-icon-left">
            <img src="components/com_jssupportticket/include/images/c_p/left-icons/report.png"/>
            <span class="text js-parent <?php if($c == 'reports') echo 'lastshown'; ?>"><?php echo JText::_('Reports'); ?></span>
        </a>
        <div class="js-innerlink treeview-menu">
            <a class="js-child <?php if($c == 'reports' && $layout == 'overallreport') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=reports&layout=overallreport"><span class="text"><?php echo JText::_('Overall Statistics'); ?></span></a>
            <a class="js-child <?php if($c == 'reports' && ($layout == 'staffreport' || $layout == 'staffdetailreport'|| $layout == 'stafftimereport')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=reports&layout=staffreport"><span class="text"><?php echo JText::_('Staff Reports'); ?></span></a>
            <a class="js-child <?php if($c == 'reports' && ($layout == 'departmentreport'|| $layout == 'departmentdetailreport')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=reports&layout=departmentreport"><span class="text"><?php echo JText::_('Department Reports'); ?></span></a>
            <a class="js-child <?php if($c == 'reports' && ($layout == 'userreport' || $layout == 'userdetailreport')) echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=reports&layout=userreport"><span class="text"><?php echo JText::_('User Reports'); ?></span></a>
            <a class="js-child <?php if($c == 'reports' && $layout == 'satisfactionreport') echo 'active'; ?>" href="index.php?option=com_jssupportticket&c=reports&layout=satisfactionreport"><span class="text"><?php echo JText::_('Satisfaction Reports'); ?></span></a>
        </div>
    </div>
</div>

<script type="text/javascript">
    var cookielist = document.cookie.split(';');
    for (var i=0; i<cookielist.length; i++) {
        if (cookielist[i].trim() == "jsst_collapse_admin_menu=1") {
            jQuery("#js-tk-admin-wrapper").addClass("menu-collasped-active");
            jQuery("#js-tk-copyright").addClass("menu-collasped-active-footer");
            break;
        }
    }
    jQuery(document).ready(function(){
        jQuery("img#js-admin-responsive-menu-link").click(function(e){
            e.preventDefault();
            if(jQuery("div#js-tk-leftmenu").css('display') == 'none'){
                jQuery("div#js-tk-leftmenu").show();
                jQuery("div#js-tk-leftmenu").find('.js-parent,a.js-parent2').show();
                jQuery('.js-parent.lastshown').next().find('a.js-child').css('display','block');
                jQuery('.js-parent.lastshown').find('img.arrow').attr("src","components/com_jssupportticket/include/images/c_p/arrow2.png");
                jQuery('.js-parent.lastshown').find('span').css('color','#ffffff');
            }else{
                jQuery("div#js-tk-leftmenu").hide();
            }
        });
        jQuery("img#jsstadmin-menu-toggle").click(function () {
            if(jQuery("div#js-tk-admin-wrapper").hasClass("menu-collasped-active")){
                jQuery('div#js-tk-admin-wrapper').removeClass('menu-collasped-active');
                jQuery('div#js-tk-copyright').removeClass('menu-collasped-active-footer');
                jQuery('.js-parent ').css('display','none');
                jQuery('a.js-child').css({'display':'none'});
                document.cookie = 'jsst_collapse_admin_menu=0; expires=Thu, 01 Jan 1970 00:00:00 UTC; path=/';
            }else{
                jQuery("div#js-tk-leftmenu").show();
                jQuery("div#js-tk-admin-wrapper").addClass('menu-collasped-active');
                jQuery("div#js-tk-copyright").addClass('menu-collasped-active-footer');
                jQuery('.js-parent ').css('display','inline-block');
                document.cookie = 'jsst_collapse_admin_menu=1; expires=Sat, 01 Jan 2050 00:00:00 UTC; path=/';
            }
        });
    });
</script>
