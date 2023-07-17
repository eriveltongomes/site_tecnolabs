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
?>

<div id="jsst-main-wrapper" >
    <div id="jsst-upper-wrapper">
        <span class="jsst-title"><?php echo JText::_('JS Support Ticket Pro Installer'); ?></span>
        <span class="jsst-logo">
            <?php /*<img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/jsticketpro.png" />*/ ?>
            <img src="components/com_jssupportticket/include/images/jsticketpro.png" />
        </span>
    </div>
    <div id="jsst-middle-wrapper">
        <div class="js-col-md-4 active"><span class="jsst-number">1</span><span class="jsst-text"><?php echo JText::_('Configuration'); ?></span></div>
        <div class="js-col-md-4 active"><span class="jsst-number">2</span><span class="jsst-text"><?php echo JText::_('Permissions'); ?></span></div>
        <div class="js-col-md-4 active"><span class="jsst-number">3</span><span class="jsst-text"><?php echo JText::_('Installation'); ?></span></div>
        <div class="js-col-md-4 active"><span class="jsst-number">4</span><span class="jsst-text"><?php echo JText::_('Finish'); ?></span></div>        
    </div>
    <div id="jsst-finish-message">
        <?php /* <img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/fulltick.png" />*/ ?>
        <img src="components/com_jssupportticket/include/images/fulltick.png" />
        <?php echo JText::_('JS Ticket Pro Installed Successfully'); ?>
    </div>
    <div id="jsst-finish-message-1"><?php echo JText::_('Thanks For Installing JS Ticket Pro'); ?></div>
    <div id="jsst-finish-last-message">
        <?php /*<img src="<?php echo jssupportticket::$_pluginpath; ?>includes/images/image_1.png" />*/ ?>
        <img src="components/com_jssupportticket/include/images/image_1.png" />
    </div>
    <div class="js-row" id="jsst-finish-button">        
        <a class="nextbutton" href="index.php?option=com_jssupportticket"><?php echo JText::_('Start using'); ?></a>
    </div>    
</div>
