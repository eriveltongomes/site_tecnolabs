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
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelJSSupportticket extends JSSupportTicketModel{
    function __construct() {
        parent::__construct();
    }

    function getTicketsSummaryForAdminModule($month_back = 1){
        if(!is_numeric($month_back)){
            $month_back = 1;
        }

        $db = JFactory::getDbo();
        $result = array();
        $curdate = date('Y-m-d');
        $fromdate = date('Y-m-d', strtotime("now -".$month_back." month"));

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE status = 0 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $openticket = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $answeredticket = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isoverdue = 1 AND status != 4 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $overdueticket = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate);
        $db->setQuery($query);
        $pendingticket = $db->loadResult();

        $result = array();
        $result['new'] = $openticket;
        $result['answered'] = $answeredticket;
        $result['overdue'] = $overdueticket;
        $result['pending'] = $pendingticket;

        return $result;
    }

    function getLatestTicketsAdminModule(){
        $db = JFactory::getDbo();
        $query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status
            FROM `#__js_ticket_tickets` AS ticket
            JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
            ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        $result['date_format'] = $this->getJSModelForAdminMP('config')->getConfigurationByName('date_format');
        return $result;
    }

    function getControlPanelData(){
    	$db = JFactory::getDbo();
    	$result = array();
        $curdate = date('Y-m-d');
        $fromdate = date('Y-m-d', strtotime("now -1 month"));

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND status = 0 AND (lastreply = '0000-00-00 00:00:00' OR lastreply IS NULL) AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate)." ) AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $openticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isanswered = 1 AND status != 4 AND status != 0 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $answeredticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND isoverdue = 1 AND status != 4 AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $overdueticket_pr = $db->loadObjectList();
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id  AND isanswered != 1 AND status != 4 AND (lastreply != '0000-00-00 00:00:00') AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $pendingticket_pr = $db->loadObjectList();

        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id  AND date(created) >= ".$db->quote($fromdate)." AND date(created) <= ".$db->quote($curdate).") AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $totalticket_pr = $db->loadObjectList();


        $result['stack_chart_horizontal']['title'] = "['".JText::_("Tickets")."',";
        $result['stack_chart_horizontal']['data'] = "['".JText::_("Overdue")."',";
        foreach($overdueticket_pr AS $pr){
            $result['stack_chart_horizontal']['title'] .= "'".JText::_($pr->priority)."',";
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }
        $result['stack_chart_horizontal']['title'] .= "]";
        $result['stack_chart_horizontal']['data'] .= "],['".JText::_("Pending")."',";

        foreach($pendingticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".JText::_("Answered")."',";

        foreach($answeredticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "],['".JText::_("New")."',";

        foreach($openticket_pr AS $pr){
            $result['stack_chart_horizontal']['data'] .= $pr->totalticket.",";
        }

        $result['stack_chart_horizontal']['data'] .= "]";

        //To show priority colors on chart
        $jsonColorList = "[";

        $query = "SELECT prioritycolour FROM `#__js_ticket_priorities` ORDER BY priority ";
        $db->setQuery($query);
        foreach($db->loadObjectList() as $priority){
            $jsonColorList.= "'".$priority->prioritycolour."',";
        }
        $jsonColorList .= "]";
        $result['stack_chart_horizontal']['colors'] = $jsonColorList;
        //end priority colors

        $result['ticket_total']['openticket'] = 0;
        $result['ticket_total']['overdueticket'] = 0;
        $result['ticket_total']['pendingticket'] = 0;
        $result['ticket_total']['answeredticket'] = 0;
        $result['ticket_total']['totalticket'] = 0;

        $count = count($openticket_pr);
        for($i = 0;$i < $count; $i++){
            $result['ticket_total']['openticket'] += $openticket_pr[$i]->totalticket;
            $result['ticket_total']['overdueticket'] += $overdueticket_pr[$i]->totalticket;
            $result['ticket_total']['pendingticket'] += $pendingticket_pr[$i]->totalticket;
            $result['ticket_total']['answeredticket'] += $answeredticket_pr[$i]->totalticket;
            $result['ticket_total']['totalticket'] += $totalticket_pr[$i]->totalticket;
        }

        //today tickets for chart
        $query = "SELECT priority.priority,(SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE priorityid = priority.id AND date(created) = '".$curdate."')  AS totalticket
                    FROM `#__js_ticket_priorities` AS priority ORDER BY priority.priority";
        $db->setQuery($query);
        $priorities = $db->loadObjectList();
        $result['today_ticket_chart']['title'] = "['".JText::_('Priority')."',";
        $result['today_ticket_chart']['data'] = "['',";
        foreach($priorities AS $pr){
            $result['today_ticket_chart']['title'] .= "'".JText::_($pr->priority)."',";
            $result['today_ticket_chart']['data'] .= $pr->totalticket.",";
        }
        $result['today_ticket_chart']['title'] .= "]";
        $result['today_ticket_chart']['data'] .= "]";

        $query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status
        		FROM `#__js_ticket_tickets` AS ticket
        		JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
        		ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        $db->setQuery($query);
        $result['tickets'] = $db->loadObjectList();
        return $result;
    }
    function getStaffLatestTickets() {
        $db = JFactory::getDbo();
        $result = array();
        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest()){
            return false;
        }
        $staffid = $user->getStaffid();

        $query = "SELECT ticket.id,ticket.ticketid,ticket.subject,ticket.name,ticket.created,priority.priority,priority.prioritycolour,ticket.status,department.departmentname,CONCAT(staff.firstname ,'  ' ,staff.lastname) AS staffname,staff.id AS staffid, staff.photo AS staffphoto
                    FROM `#__js_ticket_tickets` AS ticket
                    JOIN `#__js_ticket_priorities` AS priority ON priority.id = ticket.priorityid
                    LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                    LEFT JOIN `#__js_ticket_staff` AS staff ON ticket.staffid = staff.id
                    WHERE ticket.departmentid IN (SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $staffid)
                    ORDER BY ticket.status ASC, ticket.created DESC LIMIT 0, 5";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getStaffControlPanelData() {
        $db = JFactory::getDbo();
        $result = array();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets`";
        $db->setQuery($query);
        $total_ticket = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_departments`";
        $db->setQuery($query);
        $total_department = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_staff`";
        $db->setQuery($query);
        $total_staff = $db->loadResult();

        $query = "SELECT COUNT(id) FROM `#__js_ticket_feedbacks`";
        $db->setQuery($query);
        $total_feedback = $db->loadResult();

        $result = array();
        $result['totalticket'] = $total_ticket;
        $result['totaldepartment'] = $total_department;
        $result['totalstaff'] = $total_staff;
        $result['totalfeedback'] = $total_feedback;
        return $result;
    }

    function storeTheme($data) {
        $base = substr(JPATH_BASE,0,(strlen(JPATH_BASE)-14));
        $filepath = $base.'/components/com_jssupportticket/include/css/color.php';
        $filestring = file_get_contents($filepath);
        $this->replaceString($filestring, 1, $data);
        $this->replaceString($filestring, 2, $data);
        $this->replaceString($filestring, 3, $data);
        $this->replaceString($filestring, 4, $data);
        $this->replaceString($filestring, 5, $data);
        $this->replaceString($filestring, 6, $data);
        $this->replaceString($filestring, 7, $data);
        if (file_put_contents($filepath, $filestring)) {
            return true;
        } else {
            return false;
        }
    }

    function replaceString(&$filestring, $colorNo, $data) {
        if (strstr($filestring, '$color' . $colorNo)) {
            $path1 = strpos($filestring, '$color' . $colorNo);
            $path2 = strpos($filestring, ';', $path1);
            $filestring = substr_replace($filestring, '$color' . $colorNo . ' = "' . $data['color' . $colorNo] . '";', $path1, $path2 - $path1 + 1);
        }
    }

    function getColorCode($filestring, $colorNo) {
        if (strstr($filestring, '$color' . $colorNo)) {
            $path1 = strpos($filestring, '$color' . $colorNo);
            $path1 = strpos($filestring, '#', $path1);
            $path2 = strpos($filestring, ';', $path1);
            $colorcode = substr($filestring, $path1, $path2 - $path1 - 1);
            return $colorcode;
        }
    }

    function getCurrentTheme() {
        $filepath = '../components/com_jssupportticket/include/css/color.php';
        $filestring = file_get_contents($filepath);
        $theme['color1'] = $this->getColorCode($filestring, 1);
        $theme['color2'] = $this->getColorCode($filestring, 2);
        $theme['color3'] = $this->getColorCode($filestring, 3);
        $theme['color4'] = $this->getColorCode($filestring, 4);
        $theme['color5'] = $this->getColorCode($filestring, 5);
        $theme['color6'] = $this->getColorCode($filestring, 6);
        $theme['color7'] = $this->getColorCode($filestring, 7);
        $result[0] = $theme;
        return $result;
    }

    function getListTranslations() {

        $result = array();
        $result['error'] = false;

        $path = JPATH_ADMINISTRATOR.'/language';

        if( ! is_writeable($path)){
            $result['error'] = JText::_('Dir is not writeable').' '.$path;

        }else{

            if($this->isConnected()){

                $version = $this->getJSModel('config')->getConfigByFor('version');
                if(!isset($version['versiontype'])){
                    $version['versiontype'] = $this->getJSModel('config')->getConfigurationByName('versiontype');
                }

                $url = "https://www.joomsky.com/translations/api/1.0/index.php";
                $post_data['product'] ='js-support-ticket-joomla';
                $post_data['domain'] = JURI::root();
                $post_data['producttype'] = $version['versiontype'];
                $post_data['productcode'] = 'jssupportticket';
                $post_data['productversion'] = $version['version'];
                $post_data['JVERSION'] = JVERSION;
                $post_data['method'] = 'getTranslations';

                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                $response = curl_exec($ch);
                curl_close($ch);

                $result['data'] = $response;
            }else{
                $result['error'] = JText::_('Unable to connect to server');
            }
        }

        $result = json_encode($result);

        return $result;
    }

    function makeLanguageCode($lang_name , $path){

        if( strpos($lang_name, '_') !== false ) {
            $lang_name = str_replace('_', '-', $lang_name);
        }else{
            if($lang_name == 'en'){
                $lang_name = $lang_name.'-'.strtoupper('gb');
            }elseif($lang_name == 'sv'){
                $lang_name = $lang_name.'-'.strtoupper('se');
            }elseif($lang_name == 'ar'){
                $lang_folders = scandir($path);
                $n = count($lang_folders);
                for ($i = 0; $i < $n; $i++) {
                    if($lang_folders[$i] == 'ar-SA'){
                        $lang_name = $lang_folders[$i];
                        $i = $n;
                    }elseif ($lang_folders[$i] == 'ar-EG'){
                        $lang_name = $lang_folders[$i];
                        $i = $n;
                    }elseif ($lang_folders[$i] == 'ar-AA'){
                        $lang_name = $lang_folders[$i];
                        $i = $n;
                    }
                }
            }else{
                $lang_name = $lang_name.'-'.strtoupper($lang_name);
            }
        }
        return $lang_name;
    }

    function validateAndShowDownloadFileName( $lang_name ){

        if($lang_name == '')
            return '';
        $result = array();
        $path = JPATH_ADMINISTRATOR.'/language';

        $final_name = $this->makeLanguageCode($lang_name , $path);

        $result['error'] = false;
        if(!file_exists($path)){
            $result['error'] = JText::_('Dir not exist').': '.$path;
        }elseif(!is_writeable($path)){
            $result['error'] = JText::_('Dir is not writeable').': '.$path;
        }else{
            $result['input'] = '<input id="languagecode" class="text_area" type="text" value="'.$final_name.'" name="languagecode">';
            $result['path'] = $path;
        }
        $result = json_encode($result);
        return $result;
    }

    function getLanguageTranslation($lang_name , $language_code){

        $result = array();
        $result['error'] = false;
        $path = JPATH_ADMINISTRATOR.'/language';

        if($lang_name == '' || $language_code == ''){
            $result['error'] = JText::_('Empty values are not allowed');
            return json_encode($result);
        }

        $path = $path.'/'.$language_code;
        $final_path = $path.'/'.$language_code.'.com_jssupportticket.ini';

        if(!file_exists($path)){
            $result['error'] = JText::_('Required language is not installed').': '.$language_code;
            return json_encode($result);
        }

        if(!is_writeable($path)){
            $result['error'] = JText::_('Dir is not writeable').': '.$path;
            return json_encode($result);
        }

        if(!file_exists($final_path)){
            touch($final_path);
        }

        if(!is_writeable($final_path)){
            $result['error'] = JText::_('File is not writeable').': '.$final_path;
        }else{

            if($this->isConnected()){

                $version = $this->getJSModel('config')->getConfigByFor('version');

                $url = "https://www.joomsky.com/translations/api/1.0/index.php";
                $post_data['product'] ='js-support-ticket-joomla';
                $post_data['domain'] = JURI::root();
                $post_data['producttype'] = $version['versiontype'];
                $post_data['productcode'] = 'jssupportticket';
                $post_data['productversion'] = $version['version'];
                $post_data['JVERSION'] = JVERSION;
                $post_data['translationcode'] = $lang_name;
                $post_data['method'] = 'getTranslationFile';
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                $response = curl_exec($ch);
                curl_close($ch);
                $array = json_decode($response, true);

                $ret = $this->writeLanguageFile( $final_path , $array['file']);

                if($ret != false){
                    $url = "https://www.joomsky.com/translations/api/1.0/index.php";
                    $post_data['product'] ='js-support-ticket-joomla';
                    $post_data['domain'] = JURI::root();
                    $post_data['producttype'] = $version['versiontype'];
                    $post_data['productcode'] = 'jssupportticket';
                    $post_data['productversion'] = $version['version'];
                    $post_data['JVERSION'] = JVERSION;
                    $post_data['folder'] = $array['foldername'];
                    $ch = curl_init();
                    curl_setopt($ch, CURLOPT_URL, $url);
                    curl_setopt($ch, CURLOPT_POSTFIELDS, $post_data);
                    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
                    curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 2);
                    $response = curl_exec($ch);
                    curl_close($ch);
                }
                $result['data'] = JText::_('File Downloaded Successfully');
            }else{
                $result['error'] = JText::_('Unable to connect to server');
            }
        }

        $result = json_encode($result);

        return $result;

    }

    function writeLanguageFile( $path , $url ){
        $result = file_put_contents($path, fopen($url, 'r'));
        return $result;
    }

    function isConnected(){

        $connected = @fsockopen("www.google.com", 80);
        if ($connected){
            $is_conn = true; //action when connected
            fclose($connected);
        }else{
            $is_conn = false; //action in connection failure
        }
        return $is_conn;
    }

    function stripslashesFull($input){// testing this function/.
        if (is_array($input)) {
            $input = array_map(array($this,'stripslashesFull'), $input);
        } elseif (is_object($input)) {
            $vars = get_object_vars($input);
            foreach ($vars as $k=>$v) {
                $input->{$k} = stripslashesFull($v);
            }
        } else {
            $input = stripslashes($input);
        }
        return $input;
    }

    function getUserTicketStatsForCP(){
        $db = JFactory::getDbo();

        $user = JSSupportticketCurrentUser::getInstance();
        if($user->getIsGuest())
            return false;

        $staffid = $user->getStaffid();
        $all_ticket = $user->checkUserPermission('All Tickets');
        if($staffid){
            if($all_ticket){
                $allticket_query = ' 1 = 1 ';
            }else{
                $allticket_query = " (ticket.staffid = $staffid OR ticket.departmentid IN (SELECT dept.departmentid FROM `#__js_ticket_acl_user_access_departments` AS dept WHERE dept.staffid = $staffid)) ";
            }
        }else{
            $allticket_query = "ticket.uid = ". $user->getId();
        }


        $result = array();

        $query = "SELECT COUNT(ticket.id)
                FROM `#__js_ticket_tickets` AS ticket
                JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                WHERE $allticket_query AND (ticket.status != 4 AND ticket.status != 5)";
        $db->setQuery($query);
        $result['openticket'] = $db->loadResult();

        $query = "SELECT COUNT(ticket.id)
                FROM `#__js_ticket_tickets` AS ticket
                LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                WHERE $allticket_query AND ticket.status = 3 ";
        $db->setQuery($query);
        $result['answeredticket'] = $db->loadResult();

        $query = "SELECT COUNT(ticket.id)
                FROM `#__js_ticket_tickets` AS ticket
                LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                WHERE $allticket_query AND (ticket.status = 4 OR ticket.status = 5)";
        $db->setQuery($query);
        $result['closedticket'] = $db->loadResult();

        $query = "SELECT COUNT(ticket.id)
                FROM `#__js_ticket_tickets` AS ticket
                LEFT JOIN `#__js_ticket_departments` AS department ON ticket.departmentid = department.id
                JOIN `#__js_ticket_priorities` AS priority ON ticket.priorityid = priority.id
                WHERE $allticket_query";
        $db->setQuery($query);
        $result['allticket'] = $db->loadResult();

        return $result;
    }

    function joomlaContentArticles(){
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('id AS value, title AS text');
        $query->from('#__content');

        $db->setQuery((string)$query);
        $res = $db->loadObjectList();
        return $res;
    }
    function getHtmlInput($htmlText){
        $app = JFactory::getApplication();
        $text = JComponentHelper::filterText($app->input->get($htmlText, '', 'raw'));
        return $text;    
    }

}
?>
