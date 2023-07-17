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
<?php
if ($this->config['offline'] != '1') {
        require_once JPATH_COMPONENT_SITE . '/views/header.php'; 
        $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/knowledgebase-userarticles.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }?>

    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("div#js-ticket-border-box-kb").mouseover(function () {
                $(this).find("div#js-ticket-subcat-data").show();
                $(this).addClass("js-ticket-border-box-kb-jsenabled");
                $(this).css('box-shadow','0 0 12px 1px #909090');
                $(this).css('border','1px solid #418AC9');
                $(this).css('color','#418AC9');
         });
            $("div#js-ticket-border-box-kb").mouseout(function () {
                $(this).find("div#js-ticket-subcat-data").hide();
                $(this).removeClass("js-ticket-border-box-kb-jsenabled");
                $(this).css('box-shadow','none');
                $(this).css('border','1px solid #dadada');
                $(this).css('color','#666666');
            });
        });
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
                            <?php echo JText::_('Knowledge base'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
    <div class="js-ticket-knowledgebase-wrapper">
        <div class="js-ticket-top-search-wrp">
            <div class="js-ticket-search-fields-wrp">
               <form class="js-filter-form" action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=knowledgebase&layout=userarticles'); ?>" method="post" name="adminForm" id="adminForm">
                    <div class="js-ticket-fields-wrp">
                        <div class="js-ticket-form-field js-ticket-form-field-download-search js-ticket-form-field-kb-search js-ticket-right-margin">
                            <input type="text" name="filter_kb_articletitle"  id="filter_kb_articletitle" size="10" value="<?php if (isset($this->lists['articletitle'])) echo $this->lists['articletitle']; ?>" class="js-ticket-field-input" placeholder="<?php echo JText::_('Search knowledge Base'); ?>" />
                        </div>
                        <div class="js-ticket-form-field js-ticket-form-field-download-search js-ticket-form-field-kb-search">
                            <input type="text" name="filter_kb_articlekeyword"  id="filter_kb_articlekeyword" value="<?php if (isset($this->lists['articlekeywords'])) echo $this->lists['articlekeywords']; ?>" class="js-ticket-field-input" placeholder="<?php echo JText::_('Search Keywords'); ?>" />
                        </div>
                        <div class="js-ticket-search-form-btn-wrp js-ticket-search-form-btn-wrp-download ">
                            <button class="js-search-button" onclick="this.form.submit();"><?php echo JText::_('Search'); ?></button>
                            <button class="js-reset-button" onclick="document.getElementById('filter_kb_articletitle').value = '';document.getElementById('filter_kb_articlekeyword').value = '';this.form.submit();"><?php echo JText::_('Reset'); ?></button>
                        </div>
                    </div>
                    <input type="hidden" name="boxchecked" value="0" />
                    <input type="hidden" name="task" value="" />
                    <input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
                    <input type="hidden" name="option" value="<?php echo $this->option; ?>" />
                </form>
            </div>
        </div>

        <?php if ($this->categories) { ?>
            <div class="js-ticket-categories-wrp">
                <div class="js-ticket-categories-heading-wrp">
                    <?php echo JText::_('Categories') ?>
                </div>
                <div class="js-ticket-categories-content">
                    <?php foreach ($this->categories as $category) { ?>
                        <div class="js-ticket-category-box">
                            <?php $link = 'index.php?option='.$this->option .'&c=knowledgebase&layout=usercatarticles&id='.$category->id.'&Itemid='.$this->Itemid; ?>
                            <a class="js-ticket-category-title" href="<?php echo $link; ?>">
                                <span class="js-ticket-category-download-logo js-ticket-category-kb-logo ">
                                    <?php
                                        if ($category->logo != '') {
                                            $datadirectory = $this->config['data_directory'];
                                            $path = JURI::root(). $datadirectory;
                                            $path .= "/attachmentdata/category/category_" . $category->id . "/" . $category->logo;
                                        } else {
                                            $path ='components/com_jssupportticket/include/images/kb_default_icon.png';
                                        }
                                    ?>
                                        <img class="js-ticket-kb-img" src="<?php echo $path; ?>">
                                </span> 
                                <span class="js-ticket-category-name">
                                    <?php echo $category->name; ?>
                                </span>
                            </a>
                        </div>
                        <?php if (!empty($category->subcategory)) { ?>
                            <div id="js-ticket-subcat-data" class="js-ticket-subcat-data" style="display:none;">
                                <?php $counter = 1;
                                foreach ($category->subcategory as $subcategory) { ?>
                                    <?php $link = 'index.php?option='.$this->option .'&c=knowledgebase&layout=usercatarticles&id='.$subcategory->id.'&Itemid='.$this->Itemid; ?>
                                    <div class="js-col-md-6 js-ticket-body-data-kb-text js-ticket-body-data-elipses"><a href="<?php echo $link; ?>"> <?php echo $counter . '. ' . $subcategory->name; ?>   </a> </div>
                                    <?php
                                    $counter ++;
                                }
                                ?>
                            </div>      
                        <?php } ?>
                    <?php 
                    } ?>
                </div>
            </div>
        <?php } ?>

        <div class="js-ticket-downloads-wrp">
            <div class="js-ticket-downloads-heading-wrp">
                <?php echo JText::_('Knowledge Base') ?>
            </div>
            <div class="js-ticket-downloads-content">
                <?php $per_id = null;?>
                <?php if ($this->articles) { ?>
                    <?php  $i = 1;
                    foreach ($this->articles as $article) {  ?>
                        <div class="js-ticket-download-box">
                            <div class="js-ticket-download-left">
                                <?php $link = 'index.php?option='.$this->option .'&c=knowledgebase&layout=usercatarticledetails&id='.$article->articleid.'&Itemid='.$this->Itemid; ?>
                                <a class="js-ticket-download-title js-ticket-kb-title " href="<?php echo $link; ?>">
                                    <img class="js-ticket-download-icon" src="components/com_jssupportticket/include/images/knowledgebase_icons/<?php echo $i; ?>.png" />
                                    <span class="js-ticket-download-name">
                                        <?php echo $article->subject; ?>
                                    </span>
                                </a>
                            </div>
                        </div>
                    <?php $i++;
                        if($i == 6)
                            $i = 1;
                    } ?>
                    <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=knowledgebase&layout=userarticles&Itemid=' . $this->Itemid); ?>" method="post">
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
                <?php } else {
                    messageslayout::getRecordNotFound(); // empty record
                    } ?>
            </div>
        </div>
    <?php
} else {
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}?>
</div>
