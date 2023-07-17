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
<script type="text/javascript">
    jQuery(document).ready(function(){
        jQuery('a[href="#"]').click(function(e){
            e.preventDefault();
        });
    });
</script>
<div class="js-row js-null-margin">
<?php
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/knowledgebase-categories.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
    if($this->per_granted){
        if(!$this->user->getIsGuest()){
            if($this->user->getIsStaff()){
                if(!$this->user->getIsStaffDisable()){
                    //require_once JPATH_COMPONENT_SITE . '/views/ticket_header_bottom.php'; ?>
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
				            <?php echo JText::_('Categories'); ?>
				        </li>
				    </ul>
				</div>
    			    </div>
                        <?php } ?>
        			</div>
                    <div class="js-ticket-department-wrapper">
                        <div class="js-ticket-top-search-wrp">
                            <div class="js-ticket-search-fields-wrp">
                               <form class="js-filter-form" action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=knowledgebase&layout=categories'); ?>" method="post" name="adminForm" id="adminForm">
                                    <div class="js-ticket-fields-wrp">
                                        <div class="js-ticket-form-field js-ticket-form-field-download-search">
                                            <input type="text" name="filter_kb_categories" class="js-ticket-field-input" id="filter_kb_categories" size="10" value="<?php if (isset($this->lists['categories'])) echo $this->lists['categories']; ?>" class="text_area" placeholder="<?php echo JText::_('Search'); ?>" />
                                        </div>
                                        <div class="js-ticket-search-form-btn-wrp js-ticket-search-form-btn-wrp-download ">
                                            <button class="js-search-button" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                                            <button class="js-reset-button" onclick="document.getElementById('filter_kb_categories').value = '';"><?php echo JText::_('Reset'); ?></button>
                                        </div>
                                    </div>
                                    <input type="hidden" name="boxchecked" value="0" />
                                    <input type="hidden" name="task" value="" />
                                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                                </form>
                            </div>
                        </div>
                        <?php if(!(empty($this->knowledgebase)) && is_array($this->knowledgebase)){ ?>
                            <div class="js-ticket-download-content-wrp">
                                <div class="js-ticket-search-heading-wrp">
                                <div class="js-ticket-heading-left">
                                    <?php echo JText::_('Categories') ?>
                                </div>
                                <div class="js-ticket-heading-right">
                                    <?php $link = 'index.php?option='.$this->option .'&c=knowledgebase&layout=formcategory&Itemid='.$this->Itemid; ?>
                                    <a class="js-ticket-add-download-btn" href="<?php echo $link; ?>"><span class="js-ticket-add-img-wrp"><img src="components/com_jssupportticket/include/images/add.png" alt="Add-image"></span><?php echo JText::_('Add Category') ?></a> 
                                </div>
                            </div>
                                <div class="js-ticket-table-wrp js-col-md-12">
                                    <div class="js-ticket-table-header">
                                        <div class="js-ticket-table-header-col js-col-md-7 js-col-xs-7"><?php echo JText::_('Category Title'); ?></div>
                                        <?php /* <div class="js-ticket-table-header-col js-col-md-3 js-col-xs-3"><?php echo JText::_('Type'); ?></div> */?>
                                        <div class="js-ticket-table-header-col js-col-md-1 js-col-xs-1"><?php echo JText::_('Status'); ?></div>
                                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2"><?php echo JText::_('Created'); ?></div>
                                        <div class="js-ticket-table-header-col js-col-md-2 js-col-xs-2"><?php echo JText::_('Action'); ?></div>
                                    </div>
                                    <div class="js-ticket-table-body">
                                        <?php foreach ($this->knowledgebase AS $row) { ?>
                                            <?php if($row->status == 1) $icon_status = 'tick-icon.png'; else $icon_status = 'close-icon.png'; ?>
                                            <div class="js-ticket-data-row" id="tk-row-<?php echo $row->id; ?>">
                                                <div class="js-ticket-table-body-col js-col-md-7 js-col-xs-7 js-ticket-first-child">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Title:'); ?></span>
                                                    <span class="js-ticket-title"><?php $link = 'index.php?option=' . $this->option . '&c=knowledgebase&layout=formcategory&id=' . $row->id . '&Itemid=' . $this->Itemid; ?><a class="js-ticket-title-anchor" href="<?php echo $link; ?>"><?php echo $row->name; ?></a></span>
                                                </div>
                                                <div class="js-ticket-table-body-col js-col-md-1 js-col-xs-1">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Status:'); ?></span>
                                                    <img src="components/com_jssupportticket/include/images/<?php echo $icon_status; ?>">
                                                </div>
                                                <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2 created-padding-top">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Created:'); ?></span>
                                                    <?php echo JHtml::_('date',$row->created,$this->config['date_format']); ?>
                                                </div>
                                                <div class="js-ticket-table-body-col js-col-md-2 js-col-xs-2">
                                                    <span class="js-ticket-display-block"><?php echo JText::_('Action:'); ?></span>
                                                    <?php $link = 'index.php?option=' . $this->option . '&c=knowledgebase&layout=formcategory&id=' . $row->id . '&Itemid=' . $this->Itemid; ?>
                                                    <a class="js-tk-button tk_set_edit_text" href="<?php echo $link; ?>">
                                                        <img src="components/com_jssupportticket/include/images/roles_icons/edit.png">                     
                                                    </a>&nbsp;
                                                    <a class="js-tk-button" href="#" onclick="suretodelete('<?php echo $row->id; ?>');">
                                                        <img src="components/com_jssupportticket/include/images/roles_icons/delete.png">
                                                    </a>
                                                </div>
                                                <div id="js-suredelete"><img id="warning-icon" src="components/com_jssupportticket/include/images/warning_icon.png" />
                                                    <?php echo JText::_('Are you sure to delete'); ?> ?
                                                    <a class="js-tk-suredelete-btn" onclick="yesdeletethisrecord('<?php echo $row->id; ?>','knowledgebase','removekbcategory','<?php echo JSession::getFormToken(); ?>');">
                                                        <?php echo JText::_('Yes'); ?>
                                                    </a>
                                                    <a class="js-tk-canceldelete-btn" onclick="canceldelete('<?php echo $row->id; ?>');">
                                                        <?php echo JText::_('No'); ?>
                                                    </a>  
                                                </div>
                                            </div>
                                        <?php } ?>
                                        <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=knowledgebase&layout=categories&Itemid=' . $this->Itemid); ?>" method="post">
                                            <div id="jl_pagination" class="pagination">
                                                <div id="jl_pagination_pageslink">
                                                    <?php echo $this->pagination->getPagesLinks(); ?>
                                                </div>
                                                <div id="jl_pagination_box">
                                                    <?php   
                                                        // echo JText::_('Display #');
                                                        echo $this->pagination->getLimitBox();
                                                    ?>
                                                </div>
                                                <div id="jl_pagination_counter">
                                                    <?php echo $this->pagination->getResultsCounter(); ?>
                                                </div>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php }else{ 
                            messageslayout::getRecordNotFound(); // empty record
                        }?>
                    </div>
                <?php }else{
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
