<?php
/**
 + Created by:	Ahmad Bilal
 * Company:		Buruj Solutions
 + Contact:		www.burujsolutions.com , info@burujsolutions.com
				www.joomsky.com, ahmad@joomsky.com
 * Created on:	Dec 2, 2009
 ^
 + Project: 		JS Jobs 
 * File Name:	module/hotjsjobs.php
 ^ 
 * Description: Module for JS Jobs
 ^ 
 * History:		1.0.2 - Nov 27, 2010
 ^ 
 */
defined('_JEXEC') or die('Restricted access');
$document = JFactory::getDocument();
$version = new JVersion;
$joomla = $version->getShortVersion();
$jversion = substr($joomla,0,3);
if($jversion < 3){
	$document->addScript('components/com_jssupportticket/js/jquery.js');
	JHtml::_('behavior.mootools');
}else{
	JHtml::_('bootstrap.framework');
	JHtml::_('jquery.framework');
}
$document->addStyleSheet('components/com_jssupportticket/include/css/jssupportticketdefault.css');
$document->addStyleSheet('administrator/components/com_jssupportticket/include/css/bootstrap.min.css');
$title = $params->get('title', 'JS Support Ticket Download');
$showtitle = $params->get('showtitle', 1);
$titlebackgroundcolor = $params->get('titlebackgroundcolor', 1);
$titlecolor = $params->get('titlecolor', 1);
$viewall = $params->get('viewall', 1);
$maxrecord = $params->get('maxrecord', 10);
$recordperrow = $params->get('recordperrow', 1);
$textoverflow = $params->get('textoverflow', 2);

if($params->get('Itemid')) $itemid = $params->get('Itemid');			
else $itemid = JFactory::getApplication()->input->get('Itemid');
$lang = JFactory::getLanguage();
$lang->load('com_jssupportticket', JPATH_ADMINISTRATOR, null, true);
$moduleclass_sfx = $params->get('moduleclass_sfx');
$componentPath =  JPATH_ADMINISTRATOR.'/components/com_jssupportticket/';
require_once $componentPath.'JSApplication.php';
require_once 'components/com_jssupportticket/include/css/color.php';
$content = JSSupportTicketModel::getJSModelForMP('moduleplugin')->getContentForMP($title,$showtitle,$titlebackgroundcolor,$titlecolor,2,$viewall,$maxrecord,$recordperrow,$textoverflow,$itemid,'download',$moduleclass_sfx);
?>
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
        link = 'index.php?option=com_jssupportticket&c=downloads&task=getUserDownloadsById';
        jQuery.post(link, {downloadid: value}, function (data) {
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
        jQuery.post(link, {downloadid:value}, function (data) {
            console.log(data);
            /*          
             if(data){
             var obj = jQuery.parseJSON(data);
             alert(obj.helloworld);
             }
             */     
         });
    }
</script>
<div id="js-ticket-main-black-background" style="display:none;">
</div>
<div id="js-ticket-main-popup" style="display:none;">
    <span id="js-ticket-popup-title">abc title</span>
    <span id="js-ticket-popup-close-button"><img src="components/com_jssupportticket/include/images/close.png" /></span>
    <div id="js-ticket-main-content">
    </div>
    <div id="js-ticket-main-downloadallbtn">
    </div>

</div>
