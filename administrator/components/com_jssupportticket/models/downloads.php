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
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelDownloads extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getDownloadForForm($id) {
        if ($id)
            if (!is_numeric($id))
                return false;
        $db = $this->getDBO();
        $title = "";
        $categories = $this->getJSModel('knowledgebase')->getCategories('downloads');
        if (isset($id) && $id <> '') {
            $query = "SELECT down.*  FROM `#__js_ticket_downloads` AS down  WHERE down.id = " . $id;
            $db->setQuery($query);
            $downloads = $db->loadObject();
            $query = "SELECT attach.*  FROM `#__js_ticket_downloads_attachments` AS attach  WHERE attach.downloadid = " . $id;
            $db->setQuery($query);
            $downlaodattachments = $db->loadObjectList();
        }
        $status = array(array('value' => null, 'text' => JText::_('Select Status')),array('value' => '1', 'text' => JText::_('Active')),array('value' => '0', 'text' => JText::_('Disabled')));
        if (isset($downloads)) {
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox js-ticket-form-field-input" ' . '', 'value', 'text', $downloads->categoryid);
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-form-field-input " ' . '', 'value', 'text',$downloads->status);
        } else {
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox js-ticket-form-field-input" ' . '', 'value', 'text', '');
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-form-field-input " ' . '', 'value', 'text',1);
        }
        If (isset($downloads)){
            $result[0] = $downloads;
            $result[4] = $downlaodattachments;
		}
        $result[1] = $lists;
        return $result;
    }

    function storeDownload($data) {
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add Download' : 'Edit Download';
            $per = $user->checkUserPermission($permission);
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $data['description'] = JFactory::getApplication()->input->get('description', '', 'raw');
        $row = $this->getTable('downloads');
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
        $staffid = $user->getStaffId();
        $downloadid = $row->id;
        $config = $this->getJSModel('config')->getConfigByFor('default');
        $filesize = $config['filesize'];
        $total = count($_FILES['filename']['name']);
        if($total > 0){
            $attachment = $this->getJSModel('attachments');
            for($i = 0; $i < $total; $i++){
                if($_FILES['filename']['name'][$i] != ''){
                    if($_FILES['filename']['size'][$i] > 0){
                        $uploadfilesize = $_FILES['filename']['size'][$i];
                        $uploadfilesize = $uploadfilesize / 1024; //kb
                        if($uploadfilesize > $filesize){
                            //$row->delete($downloadid);
                            //return FILE_SIZE_ERROR;
                            continue;
                        }
                        $file_name = str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                        $result = $attachment->checkExtension($file_name);
                        $fileext = $attachment->getExtension($file_name);
                        if($result != 'Y'){
                            //$row->delete($downloadid);
                            //return FILE_EXTENTION_ERROR;
                            continue;
                        }                                             
                        $res = $attachment->uploadAttchments($i,$downloadid, true, false, 'download');
                        if($res == true){
                                $result = $this->storeDownloadAttachment($downloadid,$uploadfilesize,$file_name,$fileext,$staffid);
                        }else{ // delete row as well
                            //$row->delete($downloadid);
                            //return FILE_RW_ERROR;
                            continue;
                        }
                    }
                }
            }
        }
        JSSupportticketMessage::$recordid = $downloadid;
        return SAVED;
    }

    function deleteDownload() {
        $user = JSSupportticketCurrentUser::getInstance();
        $cids = JFactory::getApplication()->input->get('cid', array(0), null, 'array');
        if(!empty($cids)){
            foreach($cids AS $id){
                if(is_numeric($id)){
                    if(!$user->getIsAdmin()){
                        $per = $user->checkUserPermission('Delete Download');
                        if ($per == false) 
                            return PERMISSION_ERROR;
                    }
                    $db = $this->getDbo();
                    $query = "DELETE FROM `#__js_ticket_downloads` WHERE id=".$id;
                    $db->setQuery($query);
                    if (!$db->execute()) {
                        $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                        $db->setError($db->getErrorMsg());
                        return DELETE_ERROR;
                    }
                    $query = "DELETE FROM `#__js_ticket_downloads_attachments` WHERE downloadid=" . $id;
                    $db->setQuery($query);
                    if (!$db->execute()) {
                        $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                        $db->setError($db->getErrorMsg());
                        return DELETE_ERROR;
                    }
                }else{
                    return DELETE_ERROR;
                }
            }
        }else{
            return DELETE_ERROR;
        }

        return DELETED;
    }

    function deleteUserDownload($id) {
        $user = JSSupportticketCurrentUser::getInstance();
        if(is_numeric($id)){
            $per = $user->checkUserPermission('Delete Download');
            if ($per == false) 
                return PERMISSION_ERROR;
            $db = $this->getDbo();
            $query = "DELETE FROM `#__js_ticket_downloads` WHERE id=".$id;
            $db->setQuery($query);
            if (!$db->execute()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $db->setError($db->getErrorMsg());
                return DELETE_ERROR;
            }
            $query = "DELETE FROM `#__js_ticket_downloads_attachments` WHERE downloadid=" . $id;
            $db->setQuery($query);
            if (!$db->execute()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $db->setError($db->getErrorMsg());
                return DELETE_ERROR;
            }
        }else{
            return DELETE_ERROR;
        }
        return DELETED;
    }

    function getAllDownloads($title, $catid, $limitstart, $limit) {
        if ($catid)
            if (!is_numeric($catid))
                return false;
        $db = $this->getDBO();
        $result = array();

        $lists['categories'] = JHTML::_('select.genericList', $this->getJSModel('knowledgebase')->getCategories('downloads'), 'filter_categoryid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', $catid);

        $wherequery = "";
        $clause = " Where ";
        if ($title) {
            $title = trim($title);
            $wherequery .= $clause . " down.title LIKE " . $db->quote("%" . $title . "%");
            $clause = " AND ";
        }
        if ($catid) {
            $wherequery .= $clause . " down.categoryid = " . $catid;
        }
        $query = "SELECT COUNT(down.id) FROM `#__js_ticket_downloads` AS down ";
        $query.=$wherequery;

        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;

        $query = "SELECT down.*,kbcat.name AS downcategory,(SELECT count(da.id) FROM `#__js_ticket_downloads_attachments` AS da WHERE down.id=da.downloadid ) AS totalattachment
			FROM `#__js_ticket_downloads` AS down
			LEFT JOIN `#__js_ticket_categories` AS kbcat ON down.categoryid = kbcat.id ";
        $query.=$wherequery;
        $db->setQuery($query, $limitstart, $limit);
        if ($title)
            $lists['filter_title'] = $title;
        $result[0] = $db->loadObjectList();
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function storedownloadAttachment($downloadid,$filesize,$filename,$filetype,$staffid){
        if(!is_numeric($downloadid)) return false;
        $row = $this->getTable('downloadsattachments');
        $data['downloadid'] = $downloadid;
        $data['filename'] = $filename;
        $data['filesize'] = $filesize;
        $data['filetype'] = $filetype;
        $data['staffid'] = $staffid;
        $data['created'] = $curdate = date('Y-m-d H:i:s');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        try
        {
            $row->store();
        }
        catch (RuntimeException $e)
        {
            $this->getJSModel('systemerrors')->updateSystemErrors($e);
            $this->setError($e);
            return false;
        }
        return true;
    }

    function getUserCatAndDownloads($id ,$title,$keyword,$limitstart, $limit) {
        $db = $this->getDBO();
        $inquery = '';
        $inquerykey = '';
        if ($title != null) {
            $title = trim($title);
            $inquery .=" AND download.title LIKE " . $db->quote("%" . $title . "%");
            $lists['filter_title'] = $title;
        }
        if($keyword){
            $inquerykey .= " AND category.metakey LIKE " . $db->quote("%" . $keyword . "%");
        }
        // copid form wp
        if ($id) {
            if (!is_numeric($id))
                return false;
        } else
            $id = 0;
        $result = array();
        $query = "SELECT category.name, category.id , category.logo as catlogo
                    FROM `#__js_ticket_categories` AS category 
                    WHERE category.parentid = " . $id . " AND category.downloads = 1 AND category.status = 1";
        $query .= $inquerykey;
        $db->setQuery($query);
        $result['categories'] = $db->loadObjectList();

        $query = "SELECT category.name
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.id = " . $id . " AND category.downloads = 1 AND category.status = 1";
        $db->setQuery($query);
        $result['categoryname'] = $db->loadResult();

        if ($id != 0)
            $inquery .= " AND download.categoryid = " . $id;

        // if ($id > 0) {
        //     $query = "SELECT category.name, category.logo, category.id
        //             FROM `#__js_ticket_categories` AS category
        //             WHERE category.id = " . $id . " AND kb = 1";
        //     $db->setQuery($query);
        //     $result['categoryname'] = $db->loadResult();
        // }

        // Pagination
        $query = "SELECT COUNT(download.id)
                   FROM `#__js_ticket_downloads` AS download
                   LEFT JOIN `#__js_ticket_categories` AS category ON category.id = download.categoryid
                WHERE download.status = 1 " . $inquery.$inquerykey;
        $db->setQuery($query);
        $total = $db->loadResult();
        if ( $total <= $limitstart ) $limitstart = 0;        
        $result['total'] = $total;

        $query = "SELECT download.id, download.title  
                FROM `#__js_ticket_downloads` AS download
                    LEFT JOIN `#__js_ticket_categories` AS category ON category.id = download.categoryid
                    WHERE download.status = 1 " . $inquery.$inquerykey;
        // $query .=" ORDER BY download.ordering ASC";
        $db->setQuery($query,$limitstart,$limit);
        $result['downloads'] = $db->loadObjectList();
        $result['lists']['filter_title'] = $title;
        $result['lists']['filter_keyword'] = $keyword;
        
        return $result;
    }

    function getUserDownloadById($id) {
        if (!is_numeric($id))
            return false;
        
        $db = $this->getDBO();
        $config = $this->getJSModel('config')->getConfigs();
        
        $query = "SELECT download.title, download.description, attachment.id AS attachmentid, attachment.filename,attachment.filesize, attachment.filetype
                FROM `#__js_ticket_downloads` AS download
                JOIN `#__js_ticket_downloads_attachments` AS attachment
                ON download.id = attachment.downloadid
                WHERE download.status = 1 AND download.id = " . $id;
        
        $query .=" ORDER BY downloadid";
        $db->setQuery($query);
        $downloads = $db->loadObjectList();
        if(!empty($downloads)){
            $result['data'] = '
                <div class="js-ticket-downloads-content">
                    <div class="js-ticket-download-description">'
                        . $downloads[0]->description.'                    
                    </div>';
                    $i = 1;
                    foreach ($downloads as $download) {
                        $link = JRoute::_('index.php?option=com_jssupportticket&c=downloads&task=getdownloadbyid&id=' . $download->attachmentid .'&'. JSession::getFormToken() . '=1');
                        $result['data'] .='
                        <div class="js-ticket-download-box">
                            <div class="js-ticket-download-left">
                                <a class="js-ticket-download-title" href="#">
                                    <img class="js-ticket-download-icon" src="'.JURI::root().'components/com_jssupportticket/include/images/download_icons/'. $i .'.png" />
                                    <span class="js-ticket-download-name">
                                        ' . $download->filename . '
                                    </span>
                                    
                                    
                                </a>
                            </div>
                            <div class="js-ticket-download-right">
                                <div class="js-ticket-download-btn">
                                    <a class="js-ticket-download-btn-style" href="' . $link . '" target="_blank">
                                        '.JText::_('Download').'
                                    </a>
                                </div>
                            </div>
                        </div>';
                        $i++;
                        if($i == 6)
                            $i = 1;
                    }
                    $result['downloadallbtn'] = '
                        <div class="js-ticket-download-btn">
                            <a class="js-ticket-download-btn-style" href="'.JRoute::_('index.php?option=com_jssupportticket&c=downloads&task=downloadall&downloadid=' . $id .'&' . JSession::getFormToken() . '=1'). '" onclick="" target="_blank">
                                <img class="js-ticket-download-btn-icon" src="'.JURI::root().'components/com_jssupportticket/include/images/downloadall.png" />
                                '.JText::_('Download All').'
                            </a>
                        </div> ';

                    $result['title'] = $downloads[0]->title;
            }else{
                $result['data'] = '
                                   <div class="js-ticket-error-message-wrapper">
                                        <div class="js-ticket-message-image-wrapper">
                                            <img class="js-ticket-message-image" src="'.JURI::root().'components/com_jssupportticket/include/images/error/no-record-icon.png"/>
                                        </div>
                                        <div class="js-ticket-messages-data-wrapper">
                                            <span class="js-ticket-messages-main-text">
                                                ' . JText::_('Sorry') . '!
                                            </span>
                                            <span class="js-ticket-messages-block_text">
                                                ' . JText::_('No record found') . '...!
                                            </span>
                                        </div>
                                    </div>
                                ';
                $result['downloadallbtn'] = '';
                $result['title'] = JText::_('Message');
            }
        $this->updateHitsByID($id);
        return $result;
    }

    function updateHitsByID($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "UPDATE `#__js_ticket_downloads` SET hits = hits + 1 WHERE id = $id";
        $db->setQuery($query);
        $db->execute();
        return;
    }

    function getAllDownloadFiles() {
        $downloadid = JFactory::getApplication()->input->get('downloadid');
        require_once('administrator/components/com_jssupportticket/include/lib/pclzip.lib.php');
        $config = $this->getJSModel('config')->getConfigs();        
        $path = JPATH_BASE.'/'.$config['data_directory'];
        $path .= '/zipdownloads';
        $this->getJSModel('attachments')->makeDir($path);
        $randomfolder = $this->getRandomFolderName($path);
        $path .= '/' . $randomfolder;
        $this->getJSModel('attachments')->makeDir($path);
        $archive = new PclZip($path . '/alldownloads.zip');
        $directory = JPATH_BASE .'/'. $config['data_directory'] . '/attachmentdata/download/download_' . $downloadid . '/';
        $scanned_directory = array_diff(scandir($directory), array('..', '.'));
        $scanned_directory = array_diff(scandir($directory), array('..', '.','index.html'));
        $filelist = '';
        foreach ($scanned_directory AS $file) {
            $filelist .= $directory . '/' . $file . ',';
        }
        $filelist = substr($filelist, 0, strlen($filelist) - 1);
        $v_list = $archive->create($filelist, PCLZIP_OPT_REMOVE_PATH, $directory);
        if ($v_list == 0) {
            die("Error : '" . $archive->errorInfo() . "'");
        }
        $file = $path . '/alldownloads.zip';
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        @unlink($file);
        /*$path = jssupportticket::$_path;
        $path .= 'zipdownloads';
        $path .= '/' . $randomfolder;*/
        @unlink($path . '/index.html');
        rmdir($path);
        exit();
    }

    function getRandomFolderName($path) {
        $match = '';
        do {
            $rndfoldername = "";
            $length = 5;
            $possible = "2346789bcdfghjkmnpqrtvwxyzBCDFGHJKLMNPQRTVWXYZ";
            $maxlength = strlen($possible);
            if ($length > $maxlength) {
                $length = $maxlength;
            }
            $i = 0;
            while ($i < $length) {
                $char = substr($possible, mt_rand(0, $maxlength - 1), 1);
                if (!strstr($rndfoldername, $char)) {
                    if ($i == 0) {
                        if (ctype_alpha($char)) {
                            $rndfoldername .= $char;
                            $i++;
                        }
                    } else {
                        $rndfoldername .= $char;
                        $i++;
                    }
                }
            }
            $folderexist = $path . '/' . $rndfoldername;
            if (file_exists($folderexist))
                $match = 'Y';
            else
                $match = 'N';
        }while ($match == 'Y');

        return $rndfoldername;
    }

    function getDownloadForMP($listtype,$maxrecord){
        if( ! is_numeric($maxrecord))
            return false;
        $db = JFactory::getDbo();
        $query = "SELECT download.title AS title,download.id AS id
                    FROM `#__js_ticket_downloads` AS download WHERE download.status = 1";
        switch($listtype){
            case 1: // Latest
                $query .= " ORDER BY download.created DESC";
            break;
            case 2: // Popular
                $query .= " ORDER BY download.hits DESC";
            break;
            case 3: // All
            break;
        }
        $query .= " LIMIT $maxrecord";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getDownloadAttachmentById($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "SELECT attach.filename, attach.downloadid 
                    FROM `#__js_ticket_downloads_attachments` AS attach 
                    WHERE attach.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $downloadid = $object->downloadid;
        $filename = $object->filename;
        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator    
        }  
        $path = $base.'/'.$datadirectory;
        $path = $path . '/attachmentdata';
        $path = $path . '/download/download_' . $downloadid;
        $file = $path . '/' . $filename;
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($file));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        ob_clean();
        flush();
        readfile($file);
        exit();
    }
    
    function deleteAttachmentById($id){
		if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "SELECT attach.filename, attach.downloadid 
                    FROM `#__js_ticket_downloads_attachments` AS attach 
                    WHERE attach.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $downloadid = $object->downloadid;
        $filename = $object->filename;
        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator    
        }  
        $path = $base.'/'.$datadirectory;
        $path = $path . '/attachmentdata';
        $path = $path . '/download/download_' . $downloadid;
        $file = $path . '/' . $filename;
		if(unlink($file)){
			$query = "DELETE FROM `#__js_ticket_downloads_attachments` WHERE id = ".$id;
			$db->setQuery($query);
			if($db->execute()){
				return DELETED;
			}else{
				return DELETE_ERROR;
			}
		}
		return DELETE_ERROR;
	}

    function getLatestDownloadsForUserCP(){
        
        $db = $this->getDBO();        
        $query = "SELECT down.*,kbcat.name AS downcategory,(SELECT count(da.id) FROM `#__js_ticket_downloads_attachments` AS da WHERE down.id=da.downloadid ) AS totalattachment
            FROM `#__js_ticket_downloads` AS down
            LEFT JOIN `#__js_ticket_categories` AS kbcat ON down.categoryid = kbcat.id 
            ORDER BY down.created DESC";
        $db->setQuery($query, 0, 4);
        $result = $db->loadObjectList();
        return $result;
    }

    function getSubCategoryDownloadsByParentCat($parentid){
        if(!is_numeric($parentid))
            return false;
        $db = JFactory::getDbo();
        $result = array();
        $categories = $this->getJSModel('knowledgebase')->getAllSubCategoriesById($parentid);
        if($categories != ''){
            foreach($categories AS $category){
                $catarray[] = $category->id;
            }
            if(!empty($catarray)){
                $catarray = implode(',', $catarray);
                $query = "SELECT id,categoryid,title
                            FROM `#__js_ticket_downloads`
                            WHERE find_in_set(categoryid, '". $catarray ."') LIMIT 5";
                $db->setQuery($query);
                $result = $db->loadObjectList();
            }
        }
        return $result;
    }

    function getLatestDownloadsForAdminCP() {

        $db = $this->getDBO();
        
        $query = "SELECT title,id,description
            FROM `#__js_ticket_downloads`
            ORDER BY created DESC";
        $db->setQuery($query, 0, 5);
        $result = $db->loadObjectList();
        return $result;
    }
}
?>
