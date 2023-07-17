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
if ($this->config['offline'] != '1') {
    require_once JPATH_COMPONENT_SITE.'/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/knowledgebase-usercatarticles.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
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
                        <?php echo JText::_('Knowledge base'); ?>
                    </li>
                </ul>
            </div>
        </div>
      <?php } ?>
    </div>
    <div class="js-ticket-categories-wrp js-ticket-kb-categories-wrp">
      <?php
      $counter = 1;
      if ($this->categories) { ?>
          <div class="js-ticket-categories-heading-wrp js-ticket-position-relative">
            <span class="js-ticket-head-text">
              <?php echo JText::_('categories') ?>
            </span>
          </div>
          <div class="js-ticket-categories-content">
            <?php foreach ($this->categories as $category) { ?>
                <div class="js-ticket-category-box">
                    <?php $link = 'index.php?option='.$this->option .'&c=knowledgebase&layout=usercatarticles&id='.$category->id.'&Itemid='.$this->Itemid; ?>
                    <a class="js-ticket-category-title" href="<?php echo $link;?>">
                        <span class="js-ticket-category-download-logo js-ticket-category-kb-logo ">
                          <?php
                            if ($this->category->logo != '') {
                                $datadirectory = $this->config['data_directory'];
                                $path = JURI::root(). $datadirectory;
                                $path .= "/attachmentdata/category/category_" . $this->category->id . "/" . $this->category->logo;
                            } else {
                                $path = 'components/com_jssupportticket/include/images/kb_default_icon.png';
                            } ?>
                            <img class="js-ticket-kb-dtl-img" src="<?php echo $path; ?>">
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
      <?php } ?>


        <div class="js-ticket-downloads-wrp">
          <div class="js-ticket-downloads-heading-wrp">
              <?php echo JText::_('Knowledgebase');?>
              <?php echo ' > ' . $this->category->name; ?>
          </div>

          <div class="js-ticket-downloads-content">
            <?php if ($this->articles) {
              $i = 1;
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
              if($i == 5)
                $i = 1;
              }?>

            <?php } else {
              messageslayout::getRecordNotFound(); // empty record
            } ?>
          </div>
          <?php if($this->subcategories && !empty($this->subcategories)){ ?>
            <div class="js-ticket-downloads-heading-wrp">
              <?php echo $this->category->name . ' > '; ?>
              <?php echo JText::_('Sub categories knowledgebase'); ?>
            </div>
            <div class="js-ticket-downloads-content">
            <?php
              $i = 1;
              foreach ($this->subcategories as $article) {  ?>
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
              if($i == 5)
                $i = 1;
              }?>
          </div>
          <?php } ?>
          <?php if($this->articles){ ?>
            <form action="<?php echo JRoute::_('index.php?option=com_jssupportticket&c=knowledgebase&layout=usercatarticles&id='.$this->id.'&Itemid=' . $this->Itemid); ?>" method="post">
              <div id="jl_pagination" class="pagination">
                <div id="jl_pagination_pageslink">
                  <?php echo $this->pagination->getPagesLinks(); ?>
                </div>
                <div id="jl_pagination_box">
                  <?php echo $this->pagination->getLimitBox(); ?>
                </div>
                <div id="jl_pagination_counter">
                    <?php echo $this->pagination->getResultsCounter(); ?>
                </div>
              </div>
            </form>
          <?php } ?>
        </div>
    <?php
} else {
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}?>
