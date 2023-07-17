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
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php'; 
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/download-userdownloads.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            jQuery('a[href="#"]').click(function(e){
                e.preventDefault();
            });
            jQuery("div#js-ticket-main-black-background,span#js-ticket-popup-close-button").click(function () {
                jQuery("div#js-ticket-main-popup").slideUp();
                setTimeout(function () {
                    jQuery("div#js-ticket-main-black-background").hide();
                }, 600);

            });
        });
        function getDownloadById(value) {
            link = 'index.php?option=com_jssupportticket&c=downloads&task=getUserDownloadsById&<?php echo JSession::getFormToken(); ?>=1';
            jQuery.post(link, {id: value}, function (data) {
                if (data) {
                    var obj = jQuery.parseJSON(data);
                    jQuery("div#js-ticket-main-content").html(obj.data);
                    jQuery("span#js-ticket-popup-title").html(obj.title);
                    jQuery("div#js-ticket-main-downloadallbtn").html(obj.downloadallbtn);
                    jQuery("div#js-ticket-main-black-background").show();
                    jQuery("div#js-ticket-main-popup").slideDown("slow");
                }
            });
        }
        function getAllDownloads(value) {
            link = 'index.php?option=com_jssupportticket&c=downloads&task=getUserAllDownloads';
            jQuery.post(link, {id:value}, function (data) {
                console.log(data);
            });
        }
    </script>
    <div id="js-ticket-main-black-background" style="display:none;">
    </div>
    <div id="js-ticket-main-popup" style="display:none;">
        <span id="js-ticket-popup-title">abc title</span>
        <span id="js-ticket-popup-close-button"><img src="components/com_jssupportticket/include/images/popup-close.png" /></span>
        <div id="js-ticket-main-content">
        </div>
        <div id="js-ticket-main-downloadallbtn">
        </div>
    </div>

    <?php if(is_numeric($this->id)){
        $id = '&id='.$this->id;
    }else{
        $id = '';
    } ?>
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
                            <?php echo JText::_('Downloads'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="js-ticket-download-wrapper">
        <div class="js-ticket-top-search-wrp">
            <div class="js-ticket-search-fields-wrp">
               <form class="js-filter-form" action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=downloads&layout=userdownloads'.$id); ?>" method="post" name="adminForm" id="adminForm">
                    <div class="js-ticket-fields-wrp">
                        <div class="js-ticket-form-field js-ticket-form-field-download-search js-ticket-form-field-kb-search js-ticket-right-margin">
                            <input type="text" name="filter_title" id="filter_title" size="10" value="<?php if (isset($this->lists['filter_title'])) echo $this->lists['filter_title']; ?>" placeholder="<?php echo JText::_('Search download'); ?>" class="js-ticket-field-input" />
                        </div>
                        <div class="js-ticket-form-field js-ticket-form-field-download-search js-ticket-form-field-kb-search">
                            <input type="text" name="filter_keyword"  id="filter_keyword" value="<?php if (isset($this->lists['filter_keyword'])) echo $this->lists['filter_keyword']; ?>" class="js-ticket-field-input" placeholder="<?php echo JText::_('Search Category Keywords'); ?>" />
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
        <?php if($this->categories){ ?>
            <div class="js-ticket-categories-wrp">
                <div class="js-ticket-categories-heading-wrp">
                    <?php echo JText::_('Categories'); ?>
                </div>
                <div class="js-ticket-categories-content">
                    <?php foreach ($this->categories as $category) { ?>
                        <?php $link = 'index.php?option='.$this->option .'&c=downloads&layout=userdownloads&id='.$category->id.'&Itemid='.$this->Itemid; ?>
                        <div class="js-ticket-category-box">
                            <a class="js-ticket-category-title" href="<?php echo $link; ?>">
                                <span class="js-ticket-category-download-logo">
                                    <?php
                                    if ($category->catlogo != '') {
                                        $datadirectory = $this->config['data_directory'];
                                        $path = JURI::root(). $datadirectory;
                                        $path .= "/attachmentdata/category/category_" . $category->id . "/" . $category->catlogo;
                                    } else {
                                        $path ='components/com_jssupportticket/include/images/kb_default_icon.png';
                                    }
                                    ?>
                                    <img alt="<?php echo $category->name; ?>" class="js-ticket-download-img" src="<?php echo $path; ?>">
                                </span>
                                <span class="js-ticket-category-name">
                                    <?php echo  $category->name; ?>
                                </span>
                            </a>
                        </div>
                    <?php } ?>
                </div>
            </div>
        <?php } ?>

        <div class="js-ticket-downloads-wrp">
            <div class="js-ticket-downloads-heading-wrp">
                <?php echo JText::_('Downloads');
                if ($this->categoryname) echo ' > ' . $this->categoryname; ?>
            </div>
            <div class="js-ticket-downloads-content">
                <?php if($this->downloads){ ?>
                    <?php $i = 1;
                    foreach ($this->downloads as $download) { ?>
                        <div class="js-ticket-download-box">
                            <div class="js-ticket-download-left">
                                <a class="js-ticket-download-title" onclick="getDownloadById(<?php echo $download->id ?>)">
                                    <img class="js-ticket-download-icon" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/download_icons/<?php echo $i; ?>.png" />
                                    <span class="js-ticket-download-name">
                                        <?php echo $download->title; ?>
                                    </span>
                                </a>
                            </div>
                            <div class="js-ticket-download-right">
                                <div class="js-ticket-download-btn">
                                    <button type="button" class="js-ticket-download-btn-style" onclick="getDownloadById(<?php echo $download->id ?>)">
                                        <?php echo JText::_('Download'); ?>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <?php $i++;
                        if($i == 6)
                            $i = 1;
                     ?>
                    <?php }
                    if(is_numeric($this->id)){
                        $id = '&id='.$this->id;
                    }else{
                        $id = '';
                    }?>
                <?php $i++;
                if($i == 6)
                    $i = 1;
                } else{
                    messageslayout::getRecordNotFound(); // empty record
                }?>
                <?php if($this->subcategorydownloads){ ?>
                    <div class="js-ticket-downloads-heading-wrp">
                        <?php echo JText::_('Sub Category Downloads'); ?>
                    </div>
                    <div class="js-ticket-downloads-content">
                        <?php $i = 1;
                        foreach ($this->subcategorydownloads as $download) { ?>
                            <div class="js-ticket-download-box">
                                <div class="js-ticket-download-left">
                                    <a class="js-ticket-download-title" onclick="getDownloadById(<?php echo $download->id ?>)">
                                        <img class="js-ticket-download-icon" src="<?php echo JURI::root() ?>components/com_jssupportticket/include/images/download_icons/<?php echo $i; ?>.png" />
                                        <span class="js-ticket-download-name">
                                            <?php echo $download->title; ?>
                                        </span>
                                    </a>
                                </div>
                                <div class="js-ticket-download-right">
                                    <div class="js-ticket-download-btn">
                                        <button type="button" class="js-ticket-download-btn-style" onclick="getDownloadById(<?php echo $download->id ?>)">
                                            <?php echo JText::_('Download'); ?>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <?php $i++;
                            if($i == 6)
                                $i = 1;
                         ?>
                        <?php }
                        if(is_numeric($this->id)){
                            $id = '&id='.$this->id;
                        }else{
                            $id = '';
                        }?>
                        <?php $i++;
                        if($i == 6)
                            $i = 1;
                        ?>
                    </div>
                <?php } ?>
                <?php if($this->downloads){ ?>
                    <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=downloads&layout=userdownloads'.$id.'&Itemid=' . $this->Itemid); ?>" method="post">
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
                <?php } else{
                    messageslayout::getRecordNotFound(); // empty record
                }?>
            </div>
        <?php }else{
        messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
    } ?>
</div>
</div>
