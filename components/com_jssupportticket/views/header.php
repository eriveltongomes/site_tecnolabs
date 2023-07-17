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

$id = JFactory::getApplication()->input->getVar('id');
$isstaff=$this->user->getIsStaff();
$isguest=$this->user->getIsGuest();
$layout=$this->layoutname;
$config_prefix = $isstaff == 1 ? 'staff' : 'user';
$commonpath="index.php?option=com_jssupportticket";
$showhearderbottom = false;
$obj=[];
$array[]=array('text'=> JText::_('Dashboard'));?>
<?php
     if ($layout != null) {
        switch ($layout) {
            /*Control Panel*/
            case 'controlpanel':
                $array[] = array('text' => JText::_('Dashboard'));
            break;
            /*User Persmissions*/
            case 'userpermissions':
                $array[] = array('text' => JText::_('Staff Persmissions'));
                break;
            /*Tickets*/
            case 'formticket':
                $text = ($id) ? JText::_('Edit Ticket') : JText::_('Add Ticket');
                $array[] = array('text' => $text);
                break;
            case 'mytickets':
            case 'myticketsstaff':
                $array[] = array('text' => JText::_('My Tickets'));
                break;
            case 'ticketdetail':
                $array[] = array('text' => JText::_('Ticket Details'));
            break;
            case 'ticketstatus':
                $array[] = array('text' => JText::_('Ticket Status'));
            break;
            /*Staffs*/
            case 'formstaff':
                $text = ($id) ? JText::_('Edit Staff') : JText::_('Add Staff');
                $array[] = array('text' => $text);
                break;
            case 'staff':
                $array[] = array('text' => JText::_('Staff Members'));
                break;
            case 'staffprofile':
                $array[] = array('text' => JText::_('My Profile'));
                break;
            /*Roles*/
            case 'formrole':
                $text = ($id) ? JText::_('Edit Role') : JText::_('Add Role');
                $array[] = array('text' => $text);
                break;
            case 'roles':
                $array[] = array('text' => JText::_('Roles'));
                break;
            /*Roles Permission*/
            case 'rolepermissions':
                $array[] = array('text' => JText::_('Role Permissions'));
            break;
            /*Reports*/
            case 'departmentreports':
                $array[] = array('text' => JText::_('Department Reports'));
                break;
            case 'staffreports':
                $array[] = array('text' => JText::_('Staff Reports'));
                break;
            case 'staffdetailreport':
                $array[] = array('text' => JText::_('Staff Reports Det'));
                break;
            /*Mail*/
            case 'formmessage':
                $array[] = array('text' => JText::_('Send Message'));
                break;
            case 'inbox':
                $array[] = array('text' => JText::_('Inbox'));
                break;
            case 'outbox':
                $array[] = array('text' => JText::_('Outbox'));
                break;
            case 'message':
                $array[] = array('text' => JText::_('Messages'));
                break;
            /*Knowledgebase*/
            case 'formcategory':
                $text = ($id) ? JText::_('Edit Category') : JText::_('Add Category');
                $array[] = array('text' => $text);
                break;
            case 'formarticle':
                $text = ($id) ? JText::_('Edit Knowledgebase') : JText::_('Add Knowledgebase');
                $array[] = array('text' => $text);
                break;
            case 'categories':
                $array[] = array('text' => JText::_('Categories'));
                break;
            case 'articles':
                $array[] = array('text' => JText::_('Knowledgebase'));
                break;
            case 'userarticles':
            case 'usercatarticles':
                $array[] = array('text' => JText::_('Knowledgebase'));
                break;
            case 'usercatarticledetails':
                $array[] = array('text' => JText::_('Knowledgebase Det'));
                break;
            /*Faqs*/
            case 'formfaq':
                $text = ($id) ? JText::_('Edit Faq') : JText::_('Add Faq');
                $array[] = array('text' => $text);
                break;
            case 'faqs':
                $array[] = array('text' => JText::_('FAQs'));
                break;
            case 'userfaqs':
                $array[] = array('text' => JText::_('FAQs'));
                break;
            case 'userfaqdetail':
                $array[] = array('text' => JText::_('FAQs Details'));
                break;
            /*Download*/
            case 'formdownload':
                $text = ($id) ? JText::_('Edit Download') : JText::_('Add Download');
                $array[] = array('text' => $text);
                break;
            case 'downloads':
                $array[] = array('text' => JText::_('Downloads'));
                break;
            case 'userdownloads':
                $array[] = array('text' => JText::_('Downloads'));
                break;
            /*Department*/
            case 'formdepartment':
                $text = ($id) ? JText::_('Edit Department') : JText::_('Add Department');
                $array[] = array('text' => $text);
                break;
            case 'departments':
                $array[] = array('text' => JText::_('Departments'));
                break;
            /*FeedBack*/
            case 'feedbacks':
                $array[] = array('text' => JText::_('FeedBacks'));
                break;
            case 'formfeedback':
                $array[] = array('text' => JText::_('Add FeedBack'));
                break;
            /*Visitor Message Layout*/
            case 'visitorsuccessmessage':
                $array[] = array('text' => JText::_('Visitor Message'));
            break;
            /*Announcement*/
            case 'formannouncement':
                $text = ($id) ? JText::_('Edit Announcement') : JText::_('Add Announcement');
                $array[] = array('text' => $text);
            break;
            case 'announcements':
                $array[] = array('text' => JText::_('Announcements'));
            break;
            case 'userannouncements':
                $array[] = array('text' => JText::_('Announcements'));
            break;
            case 'userannouncementdetail':
                $array[] = array('text' => JText::_('Announcement Det'));
            break;
            case 'adderasedatarequest':
                $array[] = array('text' => JText::_('Erase Data Request'));
            break;
        }
    }
?>
<?php if (isset($array)) {
    foreach ($array AS $obj);
} ?>

<div id="jsst-header-main-wrapper">
    <div id="jsst-header">
        <?php /*
        <div id="jsst-header-heading" class="" >
            <a class="js-ticket-header-links"><?php echo $obj['text']; ?></a>
        </div> */ ?>
        <div id="jsst-tabs-wrp" class="" >
            <?php if($this->config['tplink_home_'.$config_prefix] == 1){?>
                <span class="jsst-header-tab">
                    <a class="js-cp-menu-link <?php if($layout=='controlpanel') echo ' selected'; ?> " href="index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=<?php echo $this->Itemid; ?>">
                        <?php echo JText::_('Dashboard'); ?>
                    </a>
                </span>
            <?php } ?>
            <?php if($this->config['tplink_ticket_'.$config_prefix] == 1){?>
                <span class="jsst-header-tab">
                    <a class="js-cp-menu-link <?php if($layout=='formticket') echo ' selected'; ?> " href="index.php?option=com_jssupportticket&c=ticket&layout=formticket&Itemid=<?php echo $this->Itemid; ?>" >
                        <?php echo JText::_('Submit Ticket'); ?>
                    </a>
                </span>
            <?php } ?>
            <?php  if($this->config['tplink_ticket_'.$config_prefix] == 1){ ?>
                <span class="jsst-header-tab">
                    <?php
                        $link = "index.php?option=com_jssupportticket&c=ticket&layout=mytickets&Itemid=".$this->Itemid;
                        if($isstaff)
                            $link = "index.php?option=com_jssupportticket&c=ticket&layout=myticketsstaff&Itemid=".$this->Itemid;
                    ?>
                    <a class="js-cp-menu-link" href="<?php echo $link; ?>">
                        <?php echo JText::_('My Tickets'); ?>
                    </a>
                </span>
            <?php } ?>
            <?php  $redirect = JRoute::_("index.php?option=com_jssupportticket&c=jssupportticket&layout=controlpanel&Itemid=" . $this->Itemid , false);
            $redirect = '&amp;return=' . base64_encode($redirect);
            if($isguest){ ?>
                <span class="jsst-header-tab jsst-header-tab-right">
                    <a class="js-cp-menu-link" href="<?php echo 'index.php?option=com_users&view=login' . $redirect; ?>">
                        <?php echo JText::_('Login'); ?>
                    </a>
                </span>
            <?php }else{
                $link = "index.php?option=com_jssupportticket&c=jssupportticket&task=logout&return=".$redirect."&Itemid=" . $this->Itemid; ?>
                <span class="jsst-header-tab jsst-header-tab-right">
                    <a class="js-cp-menu-link" href="<?php echo $link; ?>">
                        <?php echo JText::_('Log Out'); ?>
                    </a>
                </span>
            <?php } ?>
        </div>
    </div>
</div>

