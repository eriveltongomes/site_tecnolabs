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

class JSSupportticketModelModuleplugin extends JSSupportTicketModel
{
	function __construct() {
		parent::__construct();
	}

    function getContentForMP($title,$showtitle,$titlebackgroundcolor,$titlecolor,$listtype,$viewall,$maxrecord,$recordperrow,$textoverflow,$itemid,$mpfor,$moduleclass_sfx = null){
        $data = null;
        //Getting data
        switch ($mpfor) {
            case 'knowledgebase':
                $data = $this->getJSModelForMP('knowledgebase')->getKnowledgebaseForMP($listtype,$maxrecord);
            break;
            case 'faq':
                $data = $this->getJSModelForMP('faqs')->getFAQForMP($listtype,$maxrecord);
            break;
            case 'announcement':
                $data = $this->getJSModelForMP('announcements')->getAnnouncementForMP($listtype,$maxrecord);
            break;
            case 'download':
                $data = $this->getJSModelForMP('downloads')->getDownloadForMP($listtype,$maxrecord);
            break;
        }
        $content = '<div class="js-moduleplugin-wrapper">';
        if($showtitle == 1){ // Show title
            if(!empty($moduleclass_sfx) || $moduleclass_sfx != ''){
                $content .= '
                            <div class="'.$moduleclass_sfx.'">
                                <h3>
                                    <span>'.$title.'</span>
                                </h3>
                            </div>
                            ';
            }else{
                $content .= '<div class="js-moduleplugin-heading" style="color:'.$titlecolor.';background:'.$titlebackgroundcolor.';">'.$title.'</div>';
            }
        }
        if($data != null && !empty($data)){
            foreach($data AS $row){
                $colperrow = floor(12/$recordperrow);
                if($colperrow < 1 || $colperrow > 12){
                    $colperrow = 1;
                }
                switch ($mpfor) {
                    case 'knowledgebase':
                        $link = "index.php?option=com_jssupportticket&c=knowledgebase&layout=usercatarticledetails&id=".$row->id.'&Itemid='.$itemid;
                    break;
                    case 'faq':
                        $link = "index.php?option=com_jssupportticket&c=faqs&layout=userfaqdetail&id=".$row->id.'&Itemid='.$itemid;
                    break;
                    case 'announcement':
                        $link = "index.php?option=com_jssupportticket&c=announcements&layout=userannouncementdetail&id=".$row->id.'&Itemid='.$itemid;
                    break;
                    case 'download':
                        $link = "index.php?option=com_jssupportticket&c=downloads&layout=userdownloads&id=".$row->id.'&Itemid='.$itemid;
                    break;
                }
                $content .= '
                            <div class="js-moduleplugin-row js-col-md-'.$colperrow.' js-textclass-'.$textoverflow.'">';
                if($mpfor == 'download'){
                    $content .= '<div class="js-downloads-text">'.$row->title.'</div>
                                <div class="js-tk-download-btn">
                                    <a href="#" onclick="getDownloadById('.$row->id.');">'.JText::_('Download').'</a>
                                </div>';
                }else{
                    $content .= '<a class="js-moduleplugin-link" href="'.$link.'">'.$row->title.'</a>';
                }
                $content .= '
                            </div>';
            }
            if($viewall == 1){
                switch ($mpfor) {
                    case 'knowledgebase':
                        $content .= '
                                    <div class="js-moduleplugin-viewall">
                                        <a class="js-moduleplugin-viewalllink" href="index.php?option=com_jssupportticket&c=knowledgebase&layout=userarticles&Itemid='.$itemid.'">'.JText::_('View all').'</a>
                                    </div>';
                    break;
                    case 'faq':
                        $content .= '
                                    <div class="js-moduleplugin-viewall">
                                        <a class="js-moduleplugin-viewalllink" href="index.php?option=com_jssupportticket&c=faqs&layout=userfaqs&Itemid='.$itemid.'">'.JText::_('View all').'</a>
                                    </div>';
                    break;
                    case 'announcement':
                        $content .= '
                                    <div class="js-moduleplugin-viewall">
                                        <a class="js-moduleplugin-viewalllink" href="index.php?option=com_jssupportticket&c=announcements&layout=userannouncements&Itemid='.$itemid.'">'.JText::_('View all').'</a>
                                    </div>';
                    break;
                    case 'download':
                        $content .= '
                                    <div class="js-moduleplugin-viewall">
                                        <a class="js-moduleplugin-viewalllink" href="index.php?option=com_jssupportticket&c=downloads&layout=userdownloads&Itemid='.$itemid.'">'.JText::_('View all').'</a>
                                    </div>';
                    break;
                }
            }
        }
        $content .= '</div>';
        return $content;   
    }
} 
?>