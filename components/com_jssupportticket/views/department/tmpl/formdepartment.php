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
$user = JSSupportTicketCurrentUser::getInstance();
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/department-formdepartment.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$user->getIsGuest()){
            if($user->getIsStaff()){
                if(!$user->getIsStaffDisable()){ ?>

            <?php
            JHTML::_('behavior.formvalidator');?> 
            <script language="javascript">
                function validate_form(f) {
                    if (document.formvalidator.isValid(f)) {
                        f.check.value = '<?php if ((JVERSION == '1.5') || (JVERSION == '2.5')) echo JUtility::getToken();
                            else echo JSession::getFormToken(); ?>';//send token
                        }
                        else {
                            alert("<?php echo JText::_('Some values are not acceptable please retry'); ?>");
                            return false;
                        }
                        return true;
                    }

            </script>
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
                                    <a href="index.php?option=com_jssupportticket&c=department&layout=departments&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
                                        <?php echo JText::_('Departments'); ?>
                                    </a>
                                </li>
                                <li>
                                    <?php echo JText::_('Add Department'); ?>
                                </li>
                            </ul>
                        </div>
                    </div>
                <?php } ?>
            </div>
                <div class="js-ticket-add-form-wrapper">
                    <form class="js-ticket-form" action="index.php" method="POST" enctype="multipart/form-data" name="adminForm" id="adminForm" >
                        <div class="js-ticket-from-field-wrp">
                            <div class="js-ticket-from-field-title">
                                <label for="departmentname">
                                    <?php echo JText::_('Title'); ?>&nbsp;<font color="red">*</font>
                                </label>
                            </div>
                            <div class="js-ticket-from-field">
                                <input class="inputbox js-ticket-form-field-input required" type="text" id="departmentname" name="departmentname" size="40" maxlength="255" value="<?php if (isset($this->department)) echo $this->department->departmentname; ?>" />
                            </div>
                        </div>
                        <div class="js-ticket-from-field-wrp">
                            <div class="js-ticket-from-field-title">
                                <label for="emailid">
                                    <?php echo JText::_('Outgoing Email'); ?>&nbsp;<font color="red">*</font>
                                </label>
                            </div>
                            <div class="js-ticket-from-field js-ticket-form-field-select">
                               <?php echo $this->lists['emaillist']; ?>
                            </div>
                        </div>
                        <div class="js-ticket-append-signature-wrp"><!-- Append Signature -->
                            <div class="js-ticket-append-field-title">
                                <label for="appendsign">
                                    <?php echo JText::_('Append Signature'); ?>
                                </label>
                            </div>
                            <div class="js-ticket-append-field-wrp">
                                <div class="js-ticket-signature-radio-box js-ticket-signature-radio-box-full-width ">
                                    <input id="appendsign" type="checkbox" class="js-ticket-append-radio-btn" name="canappendsignature" value="1" <?php if (isset($this->department->canappendsignature)) echo "checked=''"; ?> />
                                    <label id="forcanappendsignature" for="appendsign"><?php echo JText::_('Append signature with reply'); ?></label>
                                </div>
                            </div>
                        </div>
                        <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                            <div class="js-ticket-from-field-title">
                                <label>
                                    <?php echo JText::_('Signature'); ?>
                                </label>
                            </div>
                            <div class="js-ticket-from-field">
                                <?php
                                    $editor = JFactory::getConfig()->get('editor');$editor = JEditor::getInstance($editor);
                                    if (isset($this->department->departmentsignature))
                                        echo $editor->display('departmentsignature', $this->department->departmentsignature, '550', '300', '60', '20', false);
                                    else
                                        echo $editor->display('departmentsignature', '', '550', '300', '60', '20', false);
                                ?>
                            </div>
                        </div>
                        <div class="js-ticket-from-field-wrp js-ticket-from-field-wrp-full-width">
                            <div class="js-ticket-from-field-title">
                                <label for="status">
                                    <?php echo JText::_('Status'); ?>&nbsp;<font color="red">*</font>
                                </label>
                            </div>
                            <div class="js-ticket-from-field js-ticket-form-field-select">
                                <?php echo $this->lists['status']; ?>
                            </div>
                        </div>
                        <div class="js-ticket-form-btn-wrp">
                            <input type="submit" class="js-ticket-save-button" name="submit_app" onclick="return validate_form(document.adminForm)" value="<?php echo JText::_('Save Department'); ?>" />
                            <a href="index.php?option=com_jssupportticket&c=department&layout=departments&Itemid=<?php echo $this->Itemid; ?>" class="js-ticket-cancel-button"> <?php echo JText::_('Cancel'); ?></a>
                        </div>
                        <input type="hidden" name="id" value="<?php if (isset($this->department)) echo $this->department->id; ?>" />
                        <input type="hidden" name="c" value="department" />
                        <input type="hidden" name="task" value="savedepartment" />
                        <input type="hidden" name="view" value="department" />
                        <input type="hidden" name="layout" value="formdepartment" />
                        <input type="hidden" name="check" value="" />
                        <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                        <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                        <input type="hidden" name="created" value="<?php if (!isset($this->department)) echo $curdate = date('Y-m-d H:i:s'); else echo $this->department->created; ?>"/>
                        <input type="hidden" name="update" value="<?php if (isset($this->department)) echo $update = date('Y-m-d H:i:s'); ?>"/>
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
