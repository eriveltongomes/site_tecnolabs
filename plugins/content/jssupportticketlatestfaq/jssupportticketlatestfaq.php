<?php

/**
  + Created by:	Ahmad Bilal
 * Company:		Buruj Solutions
  + Contact:		www.burujsolutions.com , info@burujsolutions.com
  www.joomsky.com, ahmad@joomsky.com
 * Created on:	Aug 25, 2010
  ^
  + Project: 		JS Jobs
 * File Name:	Pplugin/jssupportticketfaq.php
  ^
 * Description: Plugin for JS Jobs
  ^
 * History:		NONE
  ^
 */
defined('_JEXEC') or die('Restricted access');

// Import Joomla! Plugin library file
jimport('joomla.plugin.plugin');
if (!defined('DS')) {
    define('DS', DIRECTORY_SEPARATOR);
}

//The Content plugin Loadmodule
class plgContentjssupportticketlatestfaq extends JPlugin {

    /**
     * onContentPrepare is for Joomla 1.6
     */
    public function onContentPrepare($context, &$row, &$params, $page = 0) {
        if (JString::strpos($row->text, 'jssupportticketfaq') === false) {
            return true;
        }

        // expression to search for
        $regex = '/{jssupportticketfaq\s*.*?}/i';
        if (!$this->params->get('enabled', 1)) {
            $row->text = preg_replace($regex, '', $row->text);
            return true;
        }
        preg_match_all($regex, $row->text, $matches);
        $count = count($matches[0]);
        if ($count) {
            // Get plugin parameters
            $style = $this->params->def('style', -2);
            $this->_process($row, $matches, $count, $regex, $style);
        }
    }

    protected function _process(&$row, &$matches, $count, $regex, $style) {
        for ($i = 0; $i < $count; $i++) {
            $load = str_replace('jssupportticketfaq', '', $matches[0][$i]);
            $load = str_replace('{', '', $load);
            $load = str_replace('}', '', $load);
            $load = trim($load);

            $modules = $this->_load($load, $style);
            $row->text = preg_replace('{' . $matches[0][$i] . '}', $modules, $row->text);
        }
        $row->text = preg_replace($regex, '', $row->text);
    }

    protected function _load($position, $style = -2) {
        $inline_params = array();
        if(strpos($position,'=')){
            $posstart = 0;
            $posend = 0;
            $notfound = 0;
            do{
                $lastpoststart = $posstart;
                $lastposend = $posend;
                $posstart = strpos($position, '="', $posstart);
                if (is_numeric($posstart)){
                    $posend = strpos($position, '"', $posstart+2);
                    $stringFound = substr($position, ($posstart+2), ($posend-$posstart)-2);
                    if($lastpoststart == 0){
                        $lastpoststart = $posstart;
                    }else{
                        $lastpoststart = strpos($position, '="', $lastpoststart);
                        $lastposend += 1;
                        $lastpoststart = $lastpoststart - $lastposend;
                    }
                    $stringFoundTitle = substr($position, $lastposend, $lastpoststart);
                    $stringFoundTitle = trim($stringFoundTitle);
                    $inline_params[$stringFoundTitle] = $stringFound;
                    $posstart = $posend;
                }else{
                    $notfound = 1;
                }
            }while($notfound != 1);
        }
        
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
        if(isset($inline_params['title'])){
            $title = $inline_params['title'];
        }else{
            $title = $this->params->get('title');
        }
        if(isset($inline_params['showtitle'])){
            switch ($inline_params['showtitle']) {
                case 'show':
                case 'yes':
                    $showtitle = 1;
                break;
                default:
                    $showtitle = 0;
                break;
            }
        }else{
            $showtitle = $this->params->get('showtitle', 1);    
        }
        if(isset($inline_params['titlebackgroundcolor'])){
            $titlebackgroundcolor = $inline_params['titlebackgroundcolor'];
        }else{
            $titlebackgroundcolor = $this->params->get('titlebackgroundcolor');
        }
        if(isset($inline_params['titletextcolor'])){
            $titlecolor = $inline_params['titletextcolor'];
        }else{
            $titlecolor = $this->params->get('titlecolor');
        }
        if(isset($inline_params['viewall'])){
            switch ($inline_params['viewall']) {
                case 'show':
                case 'yes':
                    $viewall = 1;
                break;
                default:
                    $viewall = 0;
                break;
            }
        }else{
            $viewall = $this->params->get('viewall', 1);
        }
        if(isset($inline_params['maxrecord'])){
            $maxrecord = $inline_params['maxrecord'];
        }else{
            $maxrecord = $this->params->get('maxrecord', 10);    
        }
        if(isset($inline_params['recordperrow'])){
            $recordperrow = $inline_params['recordperrow'];
        }else{
            $recordperrow = $this->params->get('recordperrow', 1);    
        }
        if(isset($inline_params['textoverflow'])){
            switch ($inline_params['textoverflow']) {
                case 'ellipsis':
                    $textoverflow = 2;
                    break;
                default:
                    $textoverflow = 1;
                    break;
            }
        }else{
            $textoverflow = $this->params->get('textoverflow', 2);
        }
        if(isset($inline_params['Itemid'])){
            $itemid = $inline_params['Itemid'];
        }else{
            if($this->params->get('Itemid')) $itemid = $this->params->get('Itemid');            
            else $itemid = JFactory::getApplication()->input->get('Itemid');
        }
        $lang = JFactory::getLanguage();
        $lang->load('com_jssupportticket', JPATH_ADMINISTRATOR, null, true);
        $moduleclass_sfx = $this->params->get('moduleclass_sfx');
        $componentPath =  JPATH_ADMINISTRATOR.'/components/com_jssupportticket/';
        require_once $componentPath.'JSApplication.php';
        require_once 'components/com_jssupportticket/include/css/color.php';
        $content = JSSupportTicketModel::getJSModelForMP('moduleplugin')->getContentForMP($title,$showtitle,$titlebackgroundcolor,$titlecolor,1,$viewall,$maxrecord,$recordperrow,$textoverflow,$itemid,'faq',null);
        return $content;
    }

}

?>
