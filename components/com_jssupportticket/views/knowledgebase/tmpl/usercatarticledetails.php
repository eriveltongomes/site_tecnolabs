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
if($this->config['offline'] != '1'){
    require_once JPATH_COMPONENT_SITE . '/views/header.php';
    $document = JFactory::getDocument();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/knowledgebase-usercatarticledetails.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }?>
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
                            <a href="index.php?option=com_jssupportticket&c=knowledgebase&layout=userarticles&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
                                <?php echo JText::_('knowledgebase'); ?>
                            </a>
                        </li>
                        <li>
                            <?php echo JText::_('knowledge base detail'); ?>
                        </li>
                    </ul>
                </div>
            </div>
        <?php } ?>
    </div>
    <?php if (isset($this->subject)) { ?>
        <div class="js-ticket-knowledgebase-wrapper">
            <div class="js-ticket-categories-wrp js-ticket-margin-bottom">
                <div class="js-ticket-knowledgebase-categories-heading-wrp">
                    <?php echo JText::_('Category Name');
                    echo ' > ' . $this->name; ?>
                </div>
            </div>
           <div class="js-ticket-knowledgebase-top-search-wrp">
                <div class="js-ticket-heading-wrp">
                    <div class="js-ticket-heading-left">
                        <?php echo $this->subject; ?>
                    </div>
                </div>
                <div class="js-ticket-knowledgebase-details">
                    <?php echo $this->detail; ?>
                </div>
            </div>
        </div>
        <div class="js-ticket-downloads-wrp">
            <div class="js-ticket-downloads-heading-wrp">
                <?php echo JText::_('Article Attachment') ?>
            </div>
            <?php if($this->articleattachments){ ?>
                <div class="js-ticket-downloads-content">
                    <?php $i = 1;
                    foreach ($this->articleattachments as $download) { ?>
                        <div class="js-ticket-download-box">
                            <div class="js-ticket-knowledgebase-download-left">
                                <a class="js-ticket-download-title" href="#">
                                    <img class="js-ticket-download-icon" src="components/com_jssupportticket/include/images/knowledgebase_icons/<?php echo $i; ?>.png" />
                                    <span class="js-ticket-download-name">
                                        <?php echo $download->filename; ?>
                                    </span>
                                </a>
                            </div>
                            <div class="js-ticket-download-right">
                                <?php $link = "index.php?option=com_jssupportticket&c=knowledgebase&task=getdownloadbyid&id=".$download->attachmentid.'&' . JSession::getFormToken() . '=1'; ?>                                    
                                <div class="js-ticket-download-btn">
                                    <a target="_blank" href="<?php echo $link; ?>" class="js-ticket-download-btn-style">
                                        
                                        <?php echo JText::_('Download'); ?>
                                    </a> 
                                </div>
                            </div>
                        </div>
                    <?php
                        $i++;
                        if($i == 6)
                            $i = 1; 
                    } ?>
                </div>
            <?php }?>
        </div>
    <?php } else {
      messageslayout::getRecordNotFound(); // empty record
    } ?>
<?php } else{
    messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
}//End
?>
