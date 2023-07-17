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
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/annoucement-userannouncements.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }?>

    <div class="js-ticket-download-wrapper">
        <?php if(is_numeric($this->id)) $id = '&id='.$this->id; else $id = ''; ?>
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
                                <?php echo JText::_('Announcements'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>
        <div class="js-ticket-top-search-wrp">
            <div class="js-ticket-search-fields-wrp">
               <form class="js-filter-form" action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=announcements&layout=userannouncements'.$id); ?>" method="post" name="adminForm" id="adminForm">
                    <div class="js-ticket-fields-wrp">
                        <div class="js-ticket-form-field js-ticket-form-field-download-search js-ticket-form-field-kb-search js-ticket-right-margin">
                            <input type="text" name="filter_title" id="filter_title" size="10" value="<?php if (isset($this->lists['filter_title'])) echo $this->lists['filter_title']; ?>" class="js-ticket-field-input" placeholder="<?php echo JText::_('Search Announcement'); ?>" />
                        </div>
                        <div class="js-ticket-form-field js-ticket-form-field-download-search js-ticket-form-field-kb-search">
                            <input type="text" name="filter_keyword"  id="filter_keyword" value="<?php if (isset($this->lists['filter_keyword'])) echo $this->lists['filter_keyword']; ?>" class="js-ticket-field-input" placeholder="<?php echo JText::_('Search Keywords'); ?>" />
                        </div>
                        <div class="js-ticket-search-form-btn-wrp js-ticket-search-form-btn-wrp-download ">
                            <button class="js-search-button" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                            <button class="js-reset-button" onclick="document.getElementById('filter_title').value = '';document.getElementById('filter_keyword').value = '';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                        </div>
                    </div>
                    <input type="hidden" name="boxchecked" value="0" />
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                </form>
            </div>
        </div>
        <?php
        $counter = 1;
        if ($this->categories) { ?>
            <div class="js-ticket-categories-wrp">
                <div class="js-ticket-categories-heading-wrp">
                    <?php echo JText::_('Categories') ?>
                </div>
                <div class="js-ticket-categories-content">
                    <?php foreach ($this->categories as $category) { ?>
                        <div class="js-ticket-category-box">
                            <?php $link = 'index.php?option='.$this->option .'&c=announcements&layout=userannouncements&id='.$category->id.'&Itemid='.$this->Itemid; ?>
                            <a class="js-ticket-category-title" href="<?php echo $link; ?>">
                                <?php 
                                if ($category->catlogo != '') {
                                    $datadirectory = $this->config['data_directory'];
                                    $path = JURI::root(). $datadirectory;
                                    $path .= "/attachmentdata/category/category_" . $category->id . "/" . $category->catlogo;
                                } else {
                                    $path ='components/com_jssupportticket/include/images/kb_default_icon.png';
                                }
                                ?>
                                <span class="js-ticket-category-download-logo">
                                    <img class="js-ticket-download-img" src="<?php echo $path; ?>" alt="<?php echo $category->name; ?>" />
                                </span>
                                <span class="js-ticket-category-name">
                                    <?php echo $category->name; ?>
                                </span>
                            </a>
                        </div>
                    <?php
                    $counter ++;
                    } ?>
                </div>
            </div>
        <?php }  ?>
       <div class="js-ticket-downloads-wrp">
            <div class="js-ticket-downloads-heading-wrp">
                <?php echo JText::_('Announcements');
                if ($this->categoryname) echo ' > ' . $this->categoryname; ?>
            </div>
            <?php if ($this->announcements) { ?>
                <div class="js-ticket-downloads-content">
                    <?php  $i = 1;
                    foreach ($this->announcements as $announcement) { ?>
                        <div class="js-ticket-download-box">
                            <div class="js-ticket-download-left">
                                <?php $link = 'index.php?option='.$this->option .'&c=announcements&layout=userannouncementdetail&id='.$announcement->id.'&Itemid='.$this->Itemid; ?>
                                <a class="js-ticket-download-title js-ticket-kb-title " href="<?php echo $link; ?>">
                                    <img class="js-ticket-download-icon" src="components/com_jssupportticket/include/images/announcement_icons/<?php echo $i; ?>.png" />
                                    <span class="js-ticket-download-name">
                                        <?php echo $announcement->title; ?>
                                    </span>
                                </a>
                            </div>
                        </div>
                    <?php $i++;
                        if($i == 6)
                            $i = 1;
                    } ?>
                </div>
                <?php if(is_numeric($this->id)){ $id = '&id='.$this->id; } else { $id = ''; }?>
            <?php } else {
                messageslayout::getRecordNotFound(); // empty record
            } ?>
        </div>
        <?php if($this->subcategoryannouncement){ ?>
            <div class="js-ticket-downloads-wrp">
                <div class="js-ticket-downloads-heading-wrp">
                    <?php echo JText::_('Sub Category Announcements');
                    if ($this->categoryname) echo ' > ' . $this->categoryname; ?>
                </div>
                <?php if ($this->subcategoryannouncement) { ?>
                    <div class="js-ticket-downloads-content">
                        <?php  $i = 1;
                        foreach ($this->subcategoryannouncement as $announcement) { ?>
                            <div class="js-ticket-download-box">
                                <div class="js-ticket-download-left">
                                    <?php $link = 'index.php?option='.$this->option .'&c=announcements&layout=userannouncementdetail&id='.$announcement->id.'&Itemid='.$this->Itemid; ?>
                                    <a class="js-ticket-download-title js-ticket-kb-title " href="<?php echo $link; ?>">
                                        <img class="js-ticket-download-icon" src="components/com_jssupportticket/include/images/announcement_icons/<?php echo $i; ?>.png" />
                                        <span class="js-ticket-download-name">
                                            <?php echo $announcement->title; ?>
                                        </span>
                                    </a>
                                </div>
                            </div>
                        <?php $i++;
                            if($i == 6)
                                $i = 1;
                        } ?>
                    </div>
                <?php } else {
                    messageslayout::getRecordNotFound(); // empty record
                } ?>
            </div> 
        <?php } ?>
        <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=announcements&layout=userannouncements'.$id.'&Itemid=' . $this->Itemid); ?>" method="post">
            <div id="jl_pagination" class="pagination">
                <div id="jl_pagination_pageslink">
                    <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
                <div id="jl_pagination_box">
                    <?php   
                        echo $this->pagination->getLimitBox();
                    ?>
                </div>
                <div id="jl_pagination_counter">
                    <?php echo $this->pagination->getResultsCounter(); ?>
                </div>
            </div>
        </form>
<?php }else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End
?>
</div>