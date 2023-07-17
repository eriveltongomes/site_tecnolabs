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
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/role-rolepermissions.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){
                $deptext = JText::_('Department Section');
                $depclass = "rad_departmentaccess";   ?>
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
            		                    <?php echo JText::_('Roles'); ?>
            		                </li>
            		            </ul>
            		        </div>
            		    </div>
                    <?php } ?>
        		</div>
                <div id="js-tk-formwrapper">
                    <div class="js-per-wrapper">
                        <div class="js-per-subheading">
                            <span class="head-text"><?php echo $deptext; ?></span>
                        </div>
                            <?php
                        foreach ($this->roledepartment AS $dep) {  ?>
                                <div class="js-per-data">
                                    <?php
                                    $dchecked_or_not = "";
                                    if (isset($dep->roledepartmentid)) {
                                        $dchecked_or_not = ($dep->roledepartmentid == $dep->id) ? "checked='checked'" : "";
                                    } ?>
                                    <input class="<?php echo $depclass; ?>" type='checkbox' disabled='disabled' name='roledepdata[<?php echo $dep->name; ?>]' value="<?php echo $dep->id ?>" <?php echo $dchecked_or_not; ?> />
                                    <label class="<?php echo $depclass; ?>" for="<?php echo $dep->name; ?>"><?php echo JText::_($dep->name); ?></label>
                                </div>
                            <?php 
                        } ?>
                    </div>
                    
                    <?php
                    $permission_keys = array_keys($this->permissionbysection);
                    foreach ($permission_keys AS $permissin_by_section) {
                        switch ($permissin_by_section) {
                            case 'ticket_section';
                                $text = JText::_('Ticket section');
                                $class = "t_s_rolepermission";
                                break;
                            case 'staff_section';
                                $text = JText::_('Staff section');
                                $class = "s_s_rolepermission";
                                break;
                            case 'kb_section';
                                $text = JText::_('Knowledge base section');
                                $class = "kb_s_rolepermission";
                                break;
                            case 'faq_section';
                                $text = JText::_('FAQ section');
                                $class = "f_s_rolepermission";
                                break;
                            case 'download_section';
                                $text = JText::_('Download section');
                                $class = "d_s_rolepermission";
                                break;
                            case 'announcement_section';
                                $text = JText::_('Announcement section');
                                $class = "a_s_rolepermission";
                                break;
                            case 'mail_section';
                                $text = JText::_('Mail section');
                                $class = "m_s_rolepermission";
                                break;
                        } ?>
                        <div class="js-per-wrapper">
                            <div class="js-per-subheading">
                                <span class="head-text"><?php echo $text; ?></span>
                            </div>
                            <?php
                            foreach ($this->permissionbysection[$permissin_by_section] AS $per) { ?>
                                    <div class="js-per-data">
                                        <?php $checked_or_not = "";
                                        if (isset($per->rolepermissionid)) {
                                            $checked_or_not = ($per->rolepermissionid == $per->id) ? "checked='checked'" : "";
                                        } ?>
                                        <input class="<?php echo $class; ?>" type='checkbox' disabled='disabled'  name='roleperdata[<?php echo $per->permission; ?>]' value="<?php echo $per->id ?>" <?php echo $checked_or_not; ?> />
                                        <label class="<?php echo $class; ?>" for="<?php echo $per->permission; ?>"><?php echo JText::_($per->permission); ?></label>
                                    </div>
                                <?php
                            } ?>
                        </div>
                        <?php
                    } ?>
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
</div>
