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
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/roles-formrole.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){        
            if(!$this->user->getIsGuest()){
                if($this->user->getIsStaff()){
                    if(!$this->user->getIsStaffDisable()){
                        JHTML::_('behavior.formvalidator');
                        $document = JFactory::getDocument();
                        $document->addScript('administrator/components/com_jssupportticket/include/js/permission/permission.js');
                        ?>
                        <script language="javascript">
                            function validate_form(f) {
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
                        <?php
                            JHTML::_('behavior.formvalidator');
                            //require_once JPATH_COMPONENT_SITE . '/views/ticket_header_bottom.php';
                            $deptext = JText::_('Department Section');
                            $depid = "rad_alldepartmentaccess";
                            $depclass = "rad_departmentaccess";     
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
            				            <a href="index.php?option=com_jssupportticket&c=roles&c=roles&layout=roles&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
            				                <?php echo JText::_('Roles'); ?>
            				            </a>
            				        </li>
            				        <li>
            				            <?php echo JText::_('Add Role'); ?>
            				        </li>
            				    </ul>
            				</div>
        			    </div>
                            <?php } ?>
            			</div>
                        <div id="js-tk-formwrapper">
                            <form action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm" >
                                <div class="js-col-md-12 js-col-xs-12 js-ticket-zero-padding">
                                    <div class="js-form-title"><label for="name"><?php echo JText::_('Name'); ?>&nbsp;<font color="red">*</font></label></div>
                                    <div class="js-form-value"><input class="inputbox js-ticket-form-input required" type="text" name="name" id="name" value="<?php if (isset($this->role->name)) {echo $this->role->name; } else {echo ""; } ?>" /></div>
                                </div>
                                <div class="js-per-wrapper">
                                    <div class="js-per-subheading">
                                        <span class="head-text"><?php echo $deptext; ?></span>
                                        <span class="head-checkbox"><input class="js-ticket-form-checkbox"  type="checkbox" class="js-ticket-form-checkbox" id="<?php echo $depid; ?>" <?php if (!$this->roleid) echo 'checked="checked"'; ?> <?php if ($this->per_assign_role == true){ ?>onclick="selectdeseletsection('<?php echo $depid; ?>', '<?php echo $depclass; ?>');" <?php } ?> /><label for='<?php echo $depid; ?>'><?php echo JText::_('Select / Deselect All'); ?></label></span>
                                    </div>
                                    <?php foreach ($this->roledepartment AS $dep) { ?>
                                        <div class="js-per-data">
                                            <?php $dchecked_or_not = "";
                                            if ($this->roleid) {  //edit case
                                                if (isset($dep->roledepartmentid)) {
                                                    if ($this->per_assign_role == true){
                                                        $dchecked_or_not = ($dep->roledepartmentid == $dep->id) ? "checked='checked'" : "";
                                                    }else{
                                                        $dchecked_or_not = ($dep->roledepartmentid == $dep->id) ? "checked='checked' disabled='disabled'" : "disabled='disabled'";  
                                                    }    
                                                }
                                            }else{ //add case
                                                if ($this->per_assign_role == true){
                                                    $dchecked_or_not = "checked='checked'";
                                                }else{
                                                    $dchecked_or_not = 'disabled="disabled"';
                                                }
                                                
                                            } ?>
                                            <input type='checkbox' id='<?php echo "roledepdata_" . $dep->name; ?>' class="<?php echo $depclass; ?> js-ticket-form-checkbox" name='roledepdata[<?php echo $dep->name; ?>]' value="<?php echo $dep->id ?>" <?php echo $dchecked_or_not; ?> />
                                            <label for='<?php echo "roledepdata_" . $dep->name; ?>'><?php echo JText::_($dep->name); ?></label>
                                        </div> 
                                    <?php } ?>
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
                                                <span class="head-checkbox"><input  type="checkbox" class="js-ticket-form-checkbox" id="<?php echo $id; ?>" <?php if (!$this->roleid) echo 'checked="checked"'; ?> <?php if ($this->per_assign_role == true){ ?>onclick="selectdeseletsection('<?php echo $id; ?>', '<?php echo $class; ?>');" <?php } ?> /><label for="<?php echo $id; ?>"> <?php echo JText::_('Select / Deselect All'); ?></label></span>
                                            </div>
                                            <?php 
                                        } ?>
                                        <div class="js-per-data">
                                            <?php $checked_or_not = "";
                                            if ($this->roleid) {  //edit case
                                                if (isset($per->rolepermissionid)) {
                                                    if ($this->per_assign_role == true){
                                                        $checked_or_not = ($per->rolepermissionid == $per->id) ? "checked='checked'" : "";
                                                    }else{
                                                        $checked_or_not = ($per->rolepermissionid == $per->id) ? "checked='checked' disabled='disabled'" : "disabled='disabled'";
                                                    }
                                                    
                                                }
                                            }else{ //add case
                                                if ($this->per_assign_role == true){
                                                    $checked_or_not = "checked='checked'";
                                                }else{
                                                    $checked_or_not = "disabled='disabled'";
                                                }
                                                
                                            } ?>
                                            <input type='checkbox' id="<?php echo $per->permission.'_'.$section; ?>" class="<?php echo $class; ?> js-ticket-form-checkbox" name='roleperdata[<?php echo $per->permission; ?>]' value="<?php echo $per->id ?>" <?php echo $checked_or_not; ?> />
                                            <label for="<?php echo $per->permission.'_'.$section; ?>"><?php echo JText::_($per->permission); ?></label>
                                        </div>
                                     <?php 
                                    } ?>
                                </div>
                                <div class="js-ticket-form-btn-wrp">
                                    <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Role'); ?>" />
                                    <a href="index.php?option=com_jssupportticket&c=roles&c=roles&layout=roles&Itemid=<?php echo $this->Itemid;?>" class="js-ticket-cancel-button">
                                        <?php echo JText::_('Cancel'); ?>
                                    </a>
                                </div>
                             <?php 
                                if (!isset($this->role->id)) { $created = date('Y-m-d H:i:s') ?> 
                                <input type="hidden" name="created" value="<?php echo $created; ?>" /> <?php } ?> <input type="hidden" name="id" value="<?php if (isset($this->role->id)) {echo $this->role->id; } ?>" />
                                <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                                <input type="hidden" name="status" value="1" />
                                <input type="hidden" name="c" value="roles" />
                                <input type="hidden" name="view" value="roles" />

                                <input type="hidden" name="layout" value="formrole" />
                                <input type="hidden" name="check" value="" />
                                <input type="hidden" name="task" value="saverole" />
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
</div>
