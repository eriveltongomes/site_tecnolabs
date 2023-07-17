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
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/inc.css/faq-userfaqdetails.css', 'text/css');
    $language = JFactory::getLanguage();
    $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketresponsive.css');
    if($language->isRTL()){
        $document->addStyleSheet(JURI::root().'components/com_jssupportticket/include/css/jssupportticketdefaultrtl.css');
    }
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
                                <a href="index.php?option=com_jssupportticket&c=faqs&layout=userfaqs&Itemid=<?php echo $this->Itemid; ?>" title="Dashboard">
                                    <?php echo JText::_('Faqs'); ?>
                                </a>
                            </li>
                            <li>
                                <?php echo JText::_('Faq detail'); ?>
                            </li>
                        </ul>
                    </div>
                </div>
            <?php } ?>
        </div>

        <div class="js-ticket-categories-wrp js-ticket-margin-bottom">
            <div class="js-ticket-knowledgebase-categories-heading-wrp">
                <?php echo JText::_('Category Name');
                echo ' > ' . $this->categoryname; ?>
            </div>
        </div>
    <div class="js-ticket-knowledgebase-wrapper">
        <?php  if ($this->subject) { ?>
           <div class="js-ticket-faqsdetail-top-search-wrp">
                <div class="js-ticket-heading-wrp">
                    <div class="js-ticket-heading-left">
                        <?php echo $this->subject; ?>
                    </div>
                </div>
                <div class="js-ticket-knowledgebase-details">
                    <?php echo $this->detail; ?>
                </div>
            </div>
        <?php } else {
            messageslayout::getRecordNotFound(); // empty record
        }?>
    </div>
<?php
    } else {
        messageslayout::getSystemOffline($this->config['title'],$this->config['offline_text']); //offline
    }//End
?>
