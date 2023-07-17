<?php
/**
 * @Copyright Copyright (C) 2012 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
 + Contact:     www.burujsolutions.com , info@burujsolutions.com
 * Created on:  May 03, 2012
 ^
 + Project:     JS Tickets
 ^
*/

defined('_JEXEC') or die('Restricted access');

class JSSupportticketModelTicketviaemail extends JSSupportTicketModel
{
    private $plainmsg;
    private $htmlmsg;
    private $subject;
    private $charset;
    private $attachments;
    private $headers;

    function __construct() {
        parent::__construct();
    }

    function getTicketViaEmailforFormbyId($id){
        $ticketve = '';
        $db = $this->getDbo();
        $result = array();
        if($id){
            $query = "SELECT * FROM `#__js_ticket_ticketsemail` WHERE id =".$id;
            $db->setQuery($query);
            $ticketve = $db->loadObject();
        }
        if($ticketve)
            $result[0] = $ticketve;
        return $result;
    }

    function getTicketEmailById($id){
        $ticketve = '';
        $db = $this->getDbo();
        $result = array();
        if($id){
            $query = "SELECT * FROM `#__js_ticket_ticketsemail` WHERE id =".$id;
            $db->setQuery($query);
            $ticketve = $db->loadObject();
        }
        if($ticketve)
            $result = $ticketve;
        return $result;   
    }
    
    function readEmailsAjax(){
        $config = $this->getJSModel('config')->getConfigs();
        $adminEmail = $this->getJSModel('email')->getEmailById($config['admin_email']);
        $ticketviaemailaddress = JFactory::getApplication()->input->getString('emailaddress');
        if($adminEmail == $ticketviaemailaddress){
            $array['type'] = 2;
            $array['msg'] = JText::_('Admin email address and ticket via email (email address) cannot be same, your ticket via email will not be work.');
            $arraystring = json_encode($array);
            return $arraystring;
        }

        $hosttype = JFactory::getApplication()->input->get('hosttype');
        $hostname = JFactory::getApplication()->input->getString('hostname');
        $emailaddress = JFactory::getApplication()->input->getString('emailaddress');
        $ssl = JFactory::getApplication()->input->get('ssl');
        $hostportnumber = JFactory::getApplication()->input->get('hostportnumber');
        $username = $emailaddress;
        $password = JFactory::getApplication()->input->getString('password');
        if(function_exists('imap_open')){
            //check Imap is enable or not
            switch ($hosttype) {
                case 1:
                    $hostname = "{imap.gmail.com:993/imap/ssl}INBOX";
                    break;
                case 2:
                    $hostname = "{imap.mail.yahoo.com:993/imap/ssl}INBOX";
                    break;
                case 3:
                    $hostname = "{imap.aol.com:993/imap/ssl}INBOX";
                    break;
                case 4:
                    if($ssl == 1){
                        $hostname = "{" . $hostname;
                        if(!empty($hostportnumber)){
                            $hostname .= ":".$hostportnumber;
                        }
                        $hostname .= "/imap/ssl}INBOX";
                    }else{
                        $hostname = "{" . $hostname . "/notls}";
                    }
                    break;
                case 5:
                    $hostname = "{imap-mail.outlook.com/imap/ssl}INBOX";
                    break;
            }
            set_time_limit(300);
            $config = $this->getJSModel('config')->getConfiguration();
            $imap = imap_open($hostname, $username, $password) or die(imap_last_error()) or die("can't connect: " . imap_last_error());

            $emails = imap_search($imap, 'ALL'); // Grabs any e-mail that is not read
            if($emails){
                $array['type'] = 0;
                $array['msg'] = JText::_('Email reads sucessfully');
            }else{
                $array['type'] = 2;
                $array['msg'] = JText::_('Connection Established But We Cannot Ready Any Email');
            }

        }else{
            $array['type'] = 1;
            $array['msg'] = JText::_('IMAP is either not installed or not enable in your server');
        }
        $arraystring = json_encode($array);
        return $arraystring;
    }

    function storeTicketviaEmail($data){
        $db = JFactory::getDbo();
        $notsave = false;
        $alreadyexist = $this->isAlreadyExist($data);
        if($alreadyexist)
            return ALREADY_EXIST;
        // check if in staff or department email
        
        
        $config = $this->getJSModel('config')->getConfigs();
        $adminEmail = $this->getJSModel('email')->getEmailById($config['admin_email']);
        $ticketviaemailaddress = $data['emailaddress'];
        if($adminEmail == $ticketviaemailaddress){
            return 2;
        }

        $alreadyexist = $this->checkEmail($data['emailaddress']);
        if($alreadyexist)
            return 2;//

        $row = $this->getTable('ticketviaemail');
        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        JSSupportticketMessage::$recordid = $row->id;
        return SAVED;
    }


    function isAlreadyExist($data) {
        $db = JFactory::getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_ticketsemail` WHERE emailaddress = '" . $data['emailaddress'] . "' AND id != '".$data['id']."'";
        $db->setQuery($query);
        $result = $db->loadResult();
        if ($result > 0)
            return true;
        else
            return false;
    }

    function checkEmail($emailaddress) {
        $db = JFactory::getDbo();
        $query = "SELECT email
                    FROM `#__js_ticket_staff`";
        $db->setQuery($query);
        $emailaddresses = $db->loadObjectList();
        foreach ($emailaddresses as $data) {
            if($data->email == $emailaddress){
                return true;
            }
        }

        $query = "SELECT email.email
                    FROM `#__js_ticket_departments` AS dept
                    JOIN `#__js_ticket_email` AS email On email.id = dept.emailid
                    ";
        $db->setQuery($query);
        $emailaddresses = $db->loadObjectList();
        foreach ($emailaddresses as $data) {
            if($data->email == $emailaddress){
                return true;
            }
        }
        return false;
    }

    function storeConfiguration($data) {
        $db = JFactory::getDbo();
        $notsave = false;
        foreach ($data AS $key => $value) {
            $query = "UPDATE `#__js_ticket_config` SET configvalue = ".$db->quote($value)." WHERE configname = ".$db->quote($key);
            $db->setQuery($query);
            if (!$db->execute()) {
                $this->getJSModel('systemerror')->addSystemError();
                $notsave = true;
            }
        }
        if ($notsave == false) {
            if($data['status'] == 1){
                //$this->getJSController('ticketviaemail')->registerReadEmails();
            }
            return true;
        } else {
            return false;
        }
    }

    function getAllEmailsforticket(){
        $db = JFactory::getDbo();
        
        $query = "SELECT * FROM `#__js_ticket_ticketsemail`";
        $db->setQuery($query);
        $ticketsemail = $db->loadObjectList();
        $result = array();
        if(!empty($ticketsemail)){
            foreach($ticketsemail as $key){
                $this->readEmails($key);
            }
        }
    }

    function readEmails($result) {
        //echo "Fetching emails ...";
        $config = $this->getJSModel('config')->getConfigs();
        $adminEmail = $this->getJSModel('email')->getEmailById($config['admin_email']);
        $ticketviaemailaddress = $result->emailaddress;
        if($ticketviaemailaddress == ""){
            return;
        }

        if($adminEmail == $ticketviaemailaddress){
            return;
        }

        if($result->status != 1){ // if ticket via email disable
            return;
        }
        $imap = $this->getImap($result);
        if(is_null($imap)){
            return;
        }
        /* grab emails */
        //$emails = imap_search($imap,'ALL'); // get all emails
        //$emails = imap_search($inbox,'NEW'); // get new emails
        $emails = imap_search($imap, 'UNSEEN'); // Grabs any e-mail that is not read

        if ($emails) {
            // put the newest emails on top
            rsort($emails);
            foreach ($emails as $email) {
                $this->attachments = array();
                // get information specific to this email
                $overview = imap_fetch_overview($imap, $email, 0);
                $this->getHeaders($imap, $email);
                if (!empty($this->headers->subject)){
                    $this->subject = $this->headers->subject->text;
                }
                $this->getMessage($imap, $email);
                $message = $this->htmlmsg;
                $structure = imap_fetchstructure($imap, $email);
                imap_setflag_full($imap, $email, "\\Seen \\Flagged");
                $validate = $this->validateEmail($overview[0]->from);
                if ($validate) {
                    $message = $this->htmlmsg;
                    if($message == 'EMPTY') $message = $this->plainmsg;

                    $message = $this->removeTagInlineText($message, "style");
                    $message = $this->removeTagInlineText($message, "script");
					$message = $this->removeTagInlineText($message, "base target");

                    //$message = trim(utf8_encode(quoted_printable_decode($message)));
                    $idsarray = $this->manageNewEmail($overview, $message, $structure,$result);
                    $this->getAttachments($idsarray);
                } else {// not validate
                } // validate end
            }
        }
    }

    function getAttachments($idsarray) {
        if(isset($this->attachments))
        foreach ($this->attachments as $key => $value) {
            $name = $key;
            $contents = $value;
            $this->getJSModel('ticket')->storeTicketAttachment($idsarray[0], '', $name, $idsarray[1]);
            $config = $this->getJSModel('config')->getConfigs();
            $datadirectory = $config['data_directory'];
            $base = JPATH_BASE;
            if(JFactory::getApplication()->isClient('administrator')){
                $base = substr($base, 0, strlen($base) - 14); //remove administrator
            }
            $path = $base . '/' . $datadirectory;
            if (!file_exists($path)) { // create user directory
                $this->getJSModel('attachments')->makeDir($path);
            }
            $isupload = false;
            $path = $path . '/attachmentdata';
            if (!file_exists($path)) { // create user directory
                $this->getJSModel('attachments')->makeDir($path);
            }
            $path = $path . '/ticket';
            if (!file_exists($path)) { // create user directory
                $this->getJSModel('attachments')->makeDir($path);
            }
            $a_dir = $this->getJSModel('ticket')->getTicketAttachmentDir($idsarray[0]);
			$userpath = $path . '/'. $a_dir;
            if (!file_exists($userpath)) { // create user directory
                $this->getJSModel('attachments')->makeDir($userpath);
            }
            file_put_contents($userpath . '/' . $name, $contents); // save the file
        }
    }

    function checkNewTicketOrReply($overview, $message) {
        $db = JFactory::getDbo();
        $ticketnumber = "";
        $return = array();
        $return[0] = 1; // new ticket
        $replyInSubject = 0;
        $messagefrom = "";
        // check Re: in subject
        $subject = $overview[0]->subject;
        $subjectpos = strpos($subject, 'Re:', 0);
        if ($subjectpos < 5)
            $replyInSubject = 1;
        // check ticket number in message

        $messagepos1 = strpos($message, 'ticketid:', 0);
        if ($messagepos1) {
            $messagepos2 = strpos($message, '###', $messagepos1);
            $ticketnumber = substr($message, $messagepos1 + 9, ($messagepos2 - ($messagepos1 + 9)));

            $frompos = strpos($message, '####', $messagepos2);
            $messagefrom = substr($message, $messagepos2 + 3, ($frompos - ($messagepos2 + 3)));

            $find = '<input type="hidden" name="ticketid:' . $ticketnumber . '###admin####">';
            $message = str_replace($find, "", $message);

            $find = '<input type="hidden" name="ticketid:' . $ticketnumber . '###staff####">';
            $message = str_replace($find, "", $message);

            $find = '<input type="hidden" name="ticketid:' . $ticketnumber . '###user####">';
            $message = str_replace($find, "", $message);
            
            $find = '<span style="display:none;" ticketid:' . $ticketnumber . '###admin#### ></span>';
            $message = str_replace($find, "", $message);

            $find = '<span style="display:none;" ticketid:' . $ticketnumber . '###staff#### ></span>';
            $message = str_replace($find, "", $message);

            $find = '<span style="display:none;" ticketid:' . $ticketnumber . '###user#### ></span>';
            $message = str_replace($find, "", $message);

        }
        if($ticketnumber == ""){
            $messagepos1 = strpos($message, 'jssupportticketid=', 0);
            if ($messagepos1) {
                $messagepos2 = strpos($message, '>', $messagepos1);
                $ticketnumber = substr($message, $messagepos1 + 9, ($messagepos2 - ($messagepos1 + 9)));
            }
        }

        if($ticketnumber != ""){
            $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid = " . $db->quote($ticketnumber);
            $db->setQuery($query);
            $ticketid = $db->loadResult();
            if ($ticketid) { // confirm reply
                $return[0] = 2; // reply
                $return[1] = $ticketid;
                $return[2] = $messagefrom;
            }
        }

        // find in subject
        if($return[0] != 2){
            $messagepos = substr($subject, strpos($subject, "[") + 1);
            $ticketnumber = substr($messagepos, 0, strpos($messagepos, "]"));
            if ($ticketnumber) {
                $query = "SELECT id FROM `#__js_ticket_tickets` WHERE ticketid = " . $db->quote($ticketnumber);
                $db->setQuery($query);
                $ticketid = $db->loadResult();
                if ($ticketid) { // confirm reply
                    $return[0] = 2; // reply
                    $return[1] = $ticketid;
                    $return[2] = $messagefrom;
                }
            }
        }
        return $return;
    }

    function manageNewEmail($overview, $message, $structure,$maildata) {
        $mailreadtype = $maildata->mailreadtype;
        // check is new ticket or reply
        $result = $this->checkNewTicketOrReply($overview, $message);
        $ticketid = 0;
        $replyid = 0;
        if ($result[0] == 1) { // new ticket
            if ($mailreadtype == 1 || $mailreadtype == 3) {
                $ticketid = $this->addNewTicket($overview, $message,$maildata);
            }
        } elseif ($result[0] == 2) { // reply
            if ($mailreadtype == 2 || $mailreadtype == 3) {
                $ticketid = $result[1];
                $messagefrom = $result[2];
                $replyid = $this->addNewReply($overview, $message, $ticketid, $messagefrom,$maildata);
            }
        }
        $array[0] = $ticketid;
        $array[1] = $replyid;
        return $array;
    }

    function addNewTicket($overview, $message,$maildata) {
        $path1 = strpos($overview[0]->from, '<', 0);
        $path2 = strpos($overview[0]->from, '>', 0);
        $email = substr($overview[0]->from, $path1 + 1, ($path2 - $path1) - 1);
		//$email = $this->headers->from[0]->mailbox . "@" . $this->headers->from[0]->host; // this may good
		//$name = $this->headers->from[0]->personal;
        $name = substr($overview[0]->from,0,$path1 - 1);
        $defaultdepartmentid = $this->getJSModel('department')->getDefaultDepartmentID();

        $subject = $this->subject;
        // special treat for other langauges i.e russian
        if($this->getJSModel('config')->getConfigurationByName('read_utf_ticket_via_email') == 1){
            $subject = iconv_mime_decode($this->subject,0,"UTF-8");
            $name = iconv_mime_decode($name,0,"UTF-8");
		}else{
            $subject = $this->subject;
		}
		if($subject == "") $subject = JText::_("No Subject");
        $uid = $this->getUidFromEmail($email);

        $data = array();
        $data['id'] = '';
        $data['email'] = $email;
        $data['name'] = $name;
        $data['uid'] = $uid;
        $data['phone'] = '';
        $data['phoneext'] = '';
        $data['departmentid'] = $defaultdepartmentid;
        $data['priorityid'] = $this->getJSModel('priority')->getDefaultPriorityID();
        $data['subject'] = $subject;
        $data['staffid'] = '';
        $data['lastreply'] = '';
        $data['userfields_total'] = 0;
        $data['helptopicid'] = '';
        $data['message'] = $message;
        $data['status'] = 0;
        $data['ticketviaemail'] = 1;
        $data['ticketviaemail_id'] = $maildata->id;
        $data['duedate'] = '';
        $data['created'] = date('Y-m-d H:i:s');
        $ticketid = $this->getJSModel('ticket')->storeTicket($data);
        return $ticketid;
    }

    function addNewReply($overview, $message, $ticketid, $messagefrom) {
        if($messagefrom == "") $ticketstatus = 1; // reply from user
        elseif($messagefrom == "user") $ticketstatus = 1; // reply from user
        elseif($messagefrom == "admin") $ticketstatus = 3; // reply from admin
        elseif($messagefrom == "staff") $ticketstatus = 3; // reply from staff

        $path1 = strpos($overview[0]->from, '<', 0);
        $path2 = strpos($overview[0]->from, '>', 0);
        $email = substr($overview[0]->from, $path1 + 1, ($path2 - $path1) - 1);
        $uid = $this->getUidFromEmail($email);
        $staffid = $this->getStaffMemberIdFromEmail($email);

        $data = array();
        $created = date('Y-m-d H:i:s');
        $data['id'] = '';
        $data['nonesignature'] = '';
        $data['ownsignature'] = '';
        $data['departmentsignature'] = '';
        $data['closeonreply'] = '';
        $data['uid'] = $uid;
        $data['ticketid'] = $ticketid;
        $data['message'] = $message;
        $data['ticketviaemail'] = 1;
        $data['ticketviaemail_id'] = $maildata->id;
        $data['status'] = $ticketstatus;
        $data['created'] = $created;
        $data['staffid'] = $staffid;
        $result = $this->getJSModel('ticket')->storeTicketReplies($ticketid, $message, $created, $data);
    }

    function validateEmail($emailaddress) {
        $db = JFactory::getDbo();
        // validate in ban email, this already validate in ticket model
        // validate in max open ticket, this already validate in ticket model
        // validate in max open ticke in specific time from this address
        $maxticket_peremail = 3; // get from configurations
        $maxticket_peremail_time = 5; // time in minutes .. get from configurations

        $check_time = date('Y-m-d H:i:s', strtotime("-$maxticket_peremail_time min"));

        // check only ticket
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE email = " . $db->quote($emailaddress) . " AND created >= " . $db->quote($check_time);
        $db->setQuery($query);
        $maxticketsperemail = $db->loadResult();
        if ($maxticketsperemail > $maxticket_peremail) {
            //validate fail
            return 2;
        }

        // validate max ticket in specific time
        $maxticket_pertime = 25; // get from configurations
        $maxticket_time = 5; // time in minutes .. get from configurations

        $check_time = date('Y-m-d H:i:s', strtotime("-$maxticket_time min"));

        // check only ticket
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE created >= " . $db->quote($check_time);
        $db->setQuery($query);
        $maxtickets = $db->loadResult();
        if ($maxtickets > $maxticket_pertime) {
            //validate fail
            return 3;
        }

        return true;
    }

    function getImap($result) {
        $hosttype = $result->hosttype;
        switch ($hosttype) {
            case 1:
                $hostname = "{imap.gmail.com:993/imap/ssl}INBOX";
                break;
            case 2:
                $hostname = "{imap.mail.yahoo.com:993/imap/ssl}INBOX";
                break;
            case 3:
                $hostname = "{imap.aol.com:993/imap/ssl}INBOX";
                break;
            case 4:
                if($result->enabled_ssl == 1){
                    $hostname = "{" . $result->hostname;
                    if(!empty($result->hostportnumber)){
                        $hostname .= ":".$result->hostportnumber;
                    }
                    $hostname .= "/imap/ssl}INBOX";
                }else{
                    $hostname = "{" . $result->hostname . "/notls}";
                }
                break;
            case 5:
                $hostname = "{imap-mail.outlook.com/imap/ssl}INBOX";
                break;
        }
        set_time_limit(300);
        $username = $result->emailaddress;
        $password = $result->emailpassword;
        //$imap = imap_open($hostname, $username, $password) or die(imap_last_error()) or die("can't connect: " . imap_last_error());
        $imap = imap_open($hostname, $username, $password) or null;
        //the following two lines just make sure error and warnings cought
        imap_errors();
        if(is_null($imap)){
            if(imap_last_error()){
                echo "IMAP Error: ".imap_last_error();
            }
        }
        $this->imap = $imap;
        return $imap;
    }

    function getMessage($imap, $email) {
        $this->plainmsg = "";
        $this->htmlmsg = "";

        $structure = imap_fetchstructure($imap, $email);

        if (empty($structure->parts)){
            $this->getPart($imap, $email, $structure, 0);
        } else {
            foreach($structure->parts as $partno => $part){
                $this->getPart($imap, $email, $part, $partno+1);
            }
        }

        if (empty($this->plainmsg))
            $this->plainmsg = "EMPTY";

        if (empty($this->htmlmsg))
            $this->htmlmsg = "EMPTY";

        if ($this->charset != 'UTF-8'){
            $this->plainmsg = iconv($this->charset, 'UTF-8', $this->plainmsg);
            $this->htmlmsg = iconv($this->charset, 'UTF-8', $this->htmlmsg);
        }

        if (strlen($this->plainmsg) < 10 && strlen($this->htmlmsg) > 20){ // htmll message
            require_once(JPATH_ROOT. '/administrator/components/com_jssupportticket/models/html2text.php');
            $h2t = new html2text($this->htmlmsg);
            $this->plainmsg = $h2t->get_text();
        }
    }
    function getPart($imap, $email, $part, $partno) {
        // decode data
        $data = ($partno)?
            imap_fetchbody($imap,$email,$partno):  // multipart
            imap_body($imap,$email);  // not multipart
        if ($part->encoding==4){
            $data = quoted_printable_decode($data);
        }elseif ($part->encoding==3){
            $data = base64_decode($data);
        }

        $aparams = array();
        if ($part->parameters){
            foreach ($part->parameters as $x){
                $aparams[ strtolower( $x->attribute ) ] = $x->value;
            }
        }

        if (!empty($part->dparameters)){
            foreach ($part->dparameters as $x){
                $aparams[ strtolower( $x->attribute ) ] = $x->value;
            }
        }

        if ( (array_key_exists("filename",$aparams) && $aparams['filename']) || (array_key_exists("name",$aparams) &&  $aparams['name'])) {
            $filename = ($aparams['filename'])? $aparams['filename'] : $aparams['name'];
            if (empty($this->attachments)){
                $this->attachments = array();
            }

            while (array_key_exists($filename,$this->attachments)){
                $filename = "-".$filename;
            }
            $this->attachments[$filename] = $data;  // problem if two files have same name
        }elseif ($part->type==0 && $data) { // text
            if (strtolower($part->subtype)== 'plain'){
                $this->plainmsg .= trim($data) ."\n\n";
            }else{
                $this->htmlmsg .= $data ."<br><br>";
            }
            $this->charset = $aparams['charset'];
        }elseif ($part->type==2 && $data) { // embedded message
            $this->plainmsg .= trim($data) ."\n\n";
        }

        // sub parts
        if (!empty($part->parts)) {
            foreach ($part->parts as $partno0=>$part2)
                $this->getPart($imap, $email, $part2, $partno.'.'.($partno0+1));  // 1.2, 1.2.1, etc.
        }
    }

    function getHeaders($imap, $email){
        // get and parse headers
        $headers = imap_headerinfo($imap,$email);
        $this->headers = null;

        if (empty($headers)){
            return false;
        }

        foreach ($headers as $header => $value){
            if (is_string($value)){
                $obj = new stdClass();
                $obj->text = imap_utf8($value);
                $obj->charset = 'UTF-8';

                $headers->$header = $obj;

            }elseif (is_array($value)) {
                foreach ($value as $offset => $values){
                    foreach ($values as $key => $text){
                        if (is_string($text)){
                            $headers->{$header}[$offset]->$key = iconv_mime_decode($text);
                        }
                    }
                }

            }
        }

        $this->headers = $headers;
        return true;
    }

    function getAllTicketsviaEmail($searchemail,$limitstart,$limit){//$searchemail, $searchtype,$limitstart,$limit
        $lists = array();
        $db = $this->getDbo();
        //For Total Record
        $wherequery="";
        if(isset($searchemail) && $searchemail != ''){
            $searchemail = trim($searchemail);
            $wherequery .= " WHERE emailaddress LIKE ".$db->quote('%'.$searchemail.'%');
        }
        

        $query = "SELECT COUNT(id) From `#__js_ticket_ticketsemail`";
        $query.=$wherequery;
        $db = JFactory::getDbo();
        $db->setQuery($query);
        $total = $db->loadResult();

        if ($total <= $limitstart)
            $limitstart = 0;
        // ,dep.ispublic
        $query = "SELECT *
                    FROM `#__js_ticket_ticketsemail`";
        $query .= $wherequery;

        $db->setQuery($query,$limitstart,$limit);
        $ticketsemail = $db->loadObjectList();
        if($searchemail) $lists['searchemail'] = $searchemail;
        $result[0] = $ticketsemail;
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function getUidFromEmail($email) {
        $db = JFactory::getDbo();
        $return = array();
        $query = "SELECT id FROM `#__users` WHERE email = " . $db->quote($email);
        $db->setQuery($query);
        $uid = $db->loadResult();
        if ($uid) { // confirm reply
            return $uid;
        }
        return 0;
    }

    function getStaffMemberIdFromEmail($email) {
        $db = JFactory::getDbo();
		$query = "SELECT id FROM `#__js_ticket_staff` WHERE email = '" . $email . "'";
        $db->setQuery($query);
        $id = $db->loadResult();
        if ($id) { 
            return $id;
        }
        return 0;
    }

    function removeTagInlineText($text, $tag){
        while (stripos($text, "<" . $tag) !== false){
            $spos = stripos($text, "<" . $tag);
            $epos = stripos($text, "</" . $tag . ">");
                
            if ($spos && $epos){
                $text = substr($text,0, $spos) . substr($text, $epos + strlen($tag) + 3);       
            } else {
                break;  
            }
        }
        
        return $text;
    }

    function delete_TicketviaEmail() {
        $id = JFactory::getApplication()->input->get('cid');
        if (!is_numeric($id))
            return false;
        $user = JSSupportticketCurrentUser::getInstance();
        $eventtype = JText::_('Delete ticket via email');
        if($user->getIsAdmin()){
            $msg1 = JText::_('Admin');
        }
    
        $row = $this->getTable('ticketviaemail');
        if($this->canRemoveEmail($id)){
            if (!$row->delete($id)) {
                $message = $row->getError();
                $this->activity_log->storeActivityLog($id,1,$eventtype,$message,'Error');
                return DELETE_ERROR;
            }
        }else{
            return DELETE_ERROR;
        }
        return DELETED;
    }

    function canRemoveEmail($id){
        if(!is_numeric($id))
            return false;
        $db = JFactory::getDbo();
        $query = "SELECT COUNT(id) FROM `#__js_ticket_tickets` WHERE ticketviaemail = 1 AND ticketviaemail_id =" .$id;
        $db->setQuery($query);
        $email = $db->loadResult();
        if($email > 0){
            return false;
        }else{
            return true;
        }

    }

}

?>
