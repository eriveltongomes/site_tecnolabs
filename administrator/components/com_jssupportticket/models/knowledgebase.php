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
ini_set('memory_limit', '-1');

class JSSupportticketModelKnowledgebase extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }
               
    function storeKnowledgeBaseCategory($data){
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add Category' : 'Edit Category';
            $per = $user->checkUserPermission($permission);
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $data['staffid'] = $user->getStaffId();
        if(isset($_FILES['filename']['name'])) 
            $data['logo'] = $_FILES['filename']['name'];

        if($data['parentid'] == "") $data['parentid']=0;
        $row = $this->getTable('knowledgebasecategory');
        if (!$row->bind($data)){
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->check()){
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        if (!$row->store()){
            $this->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return SAVE_ERROR;
        }
        $categoryid = $row->id;
        if($_FILES['filename']['name'] != ''){
            if($_FILES['filename']['size'] > 0){
                $uploaded = $this->getJSModel('attachments')->uploadFile($categoryid, 1, 1, 'category');
                if($uploaded == false){
                    return FILE_RW_ERROR;
                }
            }
        }
        JSSupportticketMessage::$recordid = $categoryid;
        return SAVED;
    }

    function getCategoryFor($id){
        if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $result = array();
        $query = "SELECT kbcategory.* from `#__js_ticket_categories` AS kbcategory ";
                $query .=" WHERE kbcategory.id =".$id;
        $db->setQuery($query);
        $categoryfor=$db->loadObject();
        if($categoryfor->kb==1)$result['kb']=$categoryfor->kb;
        if($categoryfor->announcement==1)$result['announcement']=$categoryfor->announcement;
        if($categoryfor->faqs==1)$result['faqs']=$categoryfor->faqs;
        if($categoryfor->downloads==1)$result['downloads']=$categoryfor->downloads;
        return $result;
    }

    function getChildCategory($parentid){
        if(!is_numeric($parentid)) return false;
        $db = $this->getDBO();
        $query = "SELECT count(kbcategory.id) from `#__js_ticket_categories` AS kbcategory ";
        $query .=" WHERE kbcategory.parentid =".$parentid;
        $db->setQuery($query);
        $childcategories = $db->loadResult();
        if($childcategories > 0) return true;
        else return false;
    }

    function checkCategoryExists($name){
        if($name){
            $db = $this->getDBO();
            $query = "SELECT count(id) from `#__js_ticket_categories` WHERE name=".$db->quote($name);
            $db->setQuery($query);
            $exist = $db->loadResult();
            if($exist > 0) return true;
            else return false;
        }
    }
    
    function getCategoryForForm($id) {
        if($id) if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $categories = $this->getCategories();
        if(isset($id) && $id <> ''){
            $query = "SELECT kbcategory.*
                      FROM `#__js_ticket_categories` AS kbcategory
                      WHERE kbcategory.id = ".$id;
            $db->setQuery($query);
            $kbcategory = $db->loadObject();
        }
        $status = array(array('value' => null, 'text' => JText::_('Select Status')),array('value' => '1', 'text' => JText::_('Active')),array('value' => '0', 'text' => JText::_('Disabled')));
        if(isset($kbcategory)){
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'parentid', 'class="inputbox js-ticket-form-field-input " ' . ' onchange="getTypeForByParentId(this.value);"', 'value', 'text',$kbcategory->parentid);
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-form-field-input " ' . '', 'value', 'text',$kbcategory->status);
        }else{
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'parentid', 'class="inputbox js-ticket-form-field-input " ' . ' onchange="getTypeForByParentId(this.value);"', 'value', 'text','');
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-form-field-input " ' . '', 'value', 'text',1);
        }
        if(isset($kbcategory))
            $result[0] = $kbcategory;
        $result[1] = $lists;
        return $result;

    }
    function getArticleForForm($id) {
        if($id) if(!is_numeric($id)) return false;
        $db = $this->getDBO();

        $categories = $this->getCategories('kb');
        if(isset($id) && $id <> ''){
            $query = "SELECT kbarticle.*  FROM `#__js_ticket_articles` AS kbarticle  WHERE kbarticle.id = ".$id;
            $db->setQuery($query);
            $kbarticle = $db->loadObject();
            $query = "SELECT attach.*  FROM `#__js_ticket_articles_attachments` AS attach  WHERE attach.articleid = ".$id;
            $db->setQuery($query);
            $article_attachments = $db->loadObjectList();
        }
        $status = array(array('value' => null, 'text' => JText::_('Select Status')),array('value' => '1', 'text' => JText::_('Active')),array('value' => '0', 'text' => JText::_('Disabled')));
        if(isset($id) && $id <> ''){
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox" ' . '', 'value', 'text',$kbarticle->categoryid);
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox " ' . '', 'value', 'text',$kbarticle->status);
        }else{
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox" ' . '', 'value', 'text','');
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox " ' . '', 'value', 'text',1);
        }
        if(isset($kbarticle)){
            $result[0] = $kbarticle;
            $result[4] = $article_attachments;
		}
        $result[1] = $lists;
        return $result;
    }

    function getAllCategories($kbcattitle,$limitstart,$limit) {
        $db = $this->getDBO();
        $result = array();
        $prefix = '|-- ';
        $wherequery="";
        
        if($kbcattitle){
            $kbcattitle = trim($kbcattitle);
            $wherequery.=" AND kbcategory.name LIKE ".$db->quote('%'.$kbcattitle.'%');
        }

        $query = "SELECT COUNT(id) FROM `#__js_ticket_categories` AS kbcategory WHERE kbcategory.parentid = 0" ;
        $query .= $wherequery;
        $db->setQuery($query);
        $total = $db->loadResult();

        $query = "Select kbcategory.* from `#__js_ticket_categories` AS kbcategory WHERE kbcategory.parentid = 0";
        $query .= $wherequery;
        $db->setQuery($query);
        $knowledgebase= $db->loadObjectList();
        if(isset($knowledgebase)){
            foreach ($knowledgebase as $kb){
                $record =(Object)array();
                $record->id =$kb->id;
                $record->name =JText::_($kb->name);
                //$record->type =$kb->type;
                $record->status =$kb->status;
                $record->created =$kb->created;
                $result[] = $record;
                $result=$this->getknowledgebasecategorychild($kb->id,$prefix,$result);
            }
        }
        $finalresult=array();
        $paginationtotal=count($result); //manual paination total result in array
        $limit=$limit+$limitstart;
        if($limit >= $paginationtotal) $limit=$paginationtotal;
        for($i=$limitstart;$i<$limit;$i++){
            $finalresult[]=$result[$i];
        }
        $returnvalue['result'] = $finalresult;
        $returnvalue['total'] = count($result);
        $lists['categories'] = $kbcattitle;
        $returnvalue['lists'] = $lists;
        return $returnvalue;
    }

    function getknowledgebasecategorychild($parentid,$prefix,&$result){
        
        if(!is_numeric($parentid)) return false;
        $db = $this->getDBO();
        $q = "SELECT * FROM `#__js_ticket_categories` where parentid = ".$parentid;
        $db->setQuery($q);
        $kbcategories = $db->loadObjectList();
        foreach($kbcategories as $cat){
            $subrecord = (Object)array();
            $subrecord->id =$cat->id;
            $subrecord->name =$prefix.JText::_($cat->name);
            //$subrecord->type =$cat->type;
            $subrecord->status =$cat->status;
            $subrecord->created =$cat->created;
            $result[] = $subrecord;
            $this->getknowledgebasecategorychild($cat->id,$prefix.'|-- ',$result);
        }
        return $result;
    }

    function getAllArticles($kbarttitle,$kbcatid,$limitstart, $limit){ //$kbarttitle,$kbcatid,$typeid,$limitstart, $limit
        if($kbcatid) if(!is_numeric($kbcatid)) return false;
        //if($typeid) if(!is_numeric($typeid)) return false;
        $db = $this->getDBO();
        $result = array();
        /*$type [] =  array('value' => null,'text' => JText::_('Status'));
        $type [] =  array('value' => 0,'text' => JText::_('Public'));
        $type [] =  array('value' => 1,'text' => JText::_('Private'));
        $type [] =  array('value' => 2,'text' => JText::_('Draft'));*/

        $lists['categories'] = JHTML::_('select.genericList', $this->getCategories('kb'), 'filter_kb_categoryid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text',$kbcatid);
        //$lists['type'] = JHTML::_('select.genericList', $type, 'filter_kb_typeid', 'class="inputbox js-ticket-select-field" '. '', 'value', 'text',$typeid);

        $query = "SELECT COUNT(id) FROM `#__js_ticket_articles` AS kbarticle WHERE kbarticle.status = kbarticle.status " ;

        if ($kbarttitle) {
            $kbarttitle = trim($kbarttitle);
            $query .= " AND kbarticle.subject LIKE ".$db->quote("%".$kbarttitle."%");
        }
        if ($kbcatid) $query .= " AND kbarticle.categoryid = ".$kbcatid;
        //if (is_numeric($typeid)) $query .=" AND kbarticle.type = ".$typeid;

        $db->setQuery($query);
        $total = $db->loadResult();
        if ( $total <= $limitstart ) $limitstart = 0;

        $query = "SELECT kbarticle.*,kbcat.name AS articlecategory
            FROM `#__js_ticket_articles` AS kbarticle
            LEFT JOIN `#__js_ticket_categories` AS kbcat ON kbarticle.categoryid = kbcat.id
            WHERE kbarticle.status = kbarticle.status" ;
        if ($kbarttitle) {
            $kbarttitle = trim($kbarttitle);
            $query .= " AND kbarticle.subject LIKE ".$db->quote('%'.$kbarttitle.'%');
        }
        if ($kbcatid) $query .= " AND kbarticle.categoryid = ".$kbcatid;
        //if (is_numeric($typeid)) $query .=" AND kbarticle.type = ".$typeid;
        $db->setQuery($query,$limitstart, $limit);
        if ($kbarttitle) $lists['articletitle'] = $kbarttitle;
        $result[0] = $db->loadObjectList();
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }
        
    function getCategories($for=''){
        $result = array();
                $clause=" WHERE";
        $prefix = '|-- ';
                $wherequery="";
        $db = $this->getDBO();
        $query = "SELECT * FROM `#__js_ticket_categories` AS kbcategory ";
                switch($for){
                    case 'kb';
                    case 'downloads';
                    case 'announcement';
                    case 'faqs';
                        $wherequery.=$clause." kbcategory.".$for.'= 1 ';
                        $clause=" AND";
                    break;
                    default:
                    break;
                }
                $query .=$wherequery;
                $query .=$clause." kbcategory.parentid = 0 AND kbcategory.status = 1";
                $query .=" ORDER BY parentid ASC ";                
                //echo $query;
                
        $db->setQuery($query);
        $rows = $db->loadObjectList();
                foreach ($rows as $row){
                        $record =array();
                        $record[] =$row->id;
                        $record[] =$row->name;
                        $result[] = $record;
                        $this->getknowledgebasecategorychildCombo($row->id,$prefix,$result,$for);
                }
                
        $kbcategories = array();
        $kbcategories[] =  array('value' => '', 'text' => JText::_('Select Category'));
        foreach($result as $res){
            $kbcategories[] =  array('value' => $res[0],'text' => JText::_($res[1]));
        }
        return $kbcategories;
    }
    function getknowledgebasecategorychildCombo($parentid,$prefix,&$result,$for){

        if(!is_numeric($parentid)) return false;
        $db = $this->getDBO();
        $q = "SELECT * FROM `#__js_ticket_categories` WHERE parentid = ".$parentid;
                switch($for){
                    case 'kb';
                    case 'downloads';
                    case 'announcement';
                    case 'faqs';
                        $q.=" AND ".$for."= 1 ";
                    break;
                    default:
                    break;
                }
                //echo $q;
        $q.=" AND status = 1";
        $db->setQuery($q);
        $kbcategories = $db->loadObjectList();
                if(!empty($kbcategories)){
                    foreach($kbcategories as $cat){
                            $subrecord=array();
                            $subrecord[] =$cat->id;
                            $subrecord[] =$prefix.JText::_($cat->name);
                            $result[]= $subrecord;
                            $this->getknowledgebasecategorychildCombo($cat->id,$prefix.'|-- ',$result,$for);

                    }
                }
                //return $result;
    }
        
    function storeKnowledgeBaseArticle($data){
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add Knowledge Base' : 'Edit Knowledge Base';
            $per = $user->checkUserPermission($permission);
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $data['staffid'] = $user->getStaffId();
        $data['content'] = JFactory::getApplication()->input->get('content_article', '', 'raw');
        if($data['content'] == '')
            $data['content'] = JFactory::getApplication()->input->get('content', '', 'raw');
        
        $row = $this->getTable('knowledgebasearticles');
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
        $articleid = $row->id;
        $config = $this->getJSModel('config')->getConfigByFor('default');
        $filesize = $config['filesize'];
        $total = count($_FILES['filename']['name']);
        $attachment = $this->getJSModel('attachments');
        for($i = 0; $i < $total; $i++){
            if($_FILES['filename']['name'][$i] != ''){
                if($_FILES['filename']['size'][$i] > 0){
                    $uploadfilesize = $_FILES['filename']['size'][$i];
                    $uploadfilesize = $uploadfilesize / 1024; //kb
                    if($uploadfilesize > $filesize){
                        $row->delete($articleid);
                        return FILE_SIZE_ERROR;
                    }
                    $file_name = str_replace(' ', '_', $_FILES['filename']['name'][$i]);
                    $result = $attachment->checkExtension($file_name);
                    if($result != 'Y'){
                        $row->delete($articleid);
                        return FILE_EXTENTION_ERROR;
                    }
                    $fileext = $attachment->getExtension($file_name);                                          
                    $res = $attachment->uploadAttchments($i,$articleid, true, false, 'article');
                    if($res == true){
                        $result = $this->storeKnowledgeBaseArticleAttachment($articleid,$uploadfilesize,$file_name,$fileext,$staffid);
                    }
                }
            }
        }
        
        JSSupportticketMessage::$recordid = $articleid;
        return SAVED;
    }

    function storeKnowledgeBaseArticleAttachment($articleid,$filesize,$filename,$filetype,$staffid){
        if(!is_numeric($articleid)) return false;
        $row = $this->getTable('knowledgebasearticleattachments');
        $data['articleid'] = $articleid;
        $data['filename'] = $filename;
        $data['filesize'] = $filesize;
        $data['filetype'] = $filetype;
        $data['staffid']= $staffid;
        $data['created'] = $curdate = date('Y-m-d H:i:s');

        if (!$row->bind($data)) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->check()) {
            $this->setError($row->getError());
            return false;
        }
        if (!$row->store()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($row->getError());
            $this->setError($row->getError());
            return false;
        }
        return true;
    }

    function checkCategoryCanDelete($id){
        if(!is_numeric($id)) return false;
        $db = $this->getDbo();
        $query = "SELECT (SELECT count(id) FROM `#__js_ticket_categories` WHERE parentid=".$id.") +
                    (SELECT count(id) FROM `#__js_ticket_articles` WHERE categoryid=".$id.") +
                    (SELECT count(id) FROM `#__js_ticket_downloads` WHERE categoryid=".$id.") +
                    (SELECT count(id) FROM `#__js_ticket_faqs` WHERE categoryid=".$id.") +
                    (SELECT count(id) FROM `#__js_ticket_announcements` WHERE categoryid=".$id.") "; 
        $db->setQuery($query);
        $candelete = $db->loadResult();
        if($candelete > 0) return false; else return true;
    }
    
    function deleteKnowledgebaseCategory($id){
        if(!is_numeric($id)) return false;
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $per = $user->checkUserPermission('Delete Category');
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $db = $this->getDbo();
        $res = $this->checkCategoryCanDelete($id);
        if($res == true){
            $query="DELETE FROM `#__js_ticket_categories` WHERE id=".$id;
            $db->setQuery($query);
            if (!$db->execute()) {
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $db->setError($db->getErrorMsg());
                return DELETE_ERROR;
            }
            return DELETED;
        }else{
            return IN_USE;
        }    
    }
    
    function deleteKnowledgebaseArticle($id){
        if(!is_numeric($id)) return false;
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $per = $user->checkUserPermission('Delete Knowledge Base');
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $db = $this->getDbo();
        $query = "DELETE FROM `#__js_ticket_articles` WHERE id=".$id;
        $db->setQuery($query);
        if (!$db->execute()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            $db->setError($db->getErrorMsg());
            return DELETE_ERROR;
        }
        $query = "DELETE FROM `#__js_ticket_articles_attachments` WHERE articleid=".$id;
        $db->setQuery($query);
        if (!$db->execute()) {
            $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
            $db->setError($db->getErrorMsg());
            return DELETE_ERROR;
        }
        return DELETED;
    }

    function getKnowledgebaseCat($kbarttitle,$kbartkeyword,$limitstart, $limit) { //for user
        $db = $this->getDBO();
        $result = array();
        $inquery = '';
        if ($kbarttitle != null) {
            $kbarttitle = trim($kbarttitle);
            $inquery .=" AND category.name LIKE ".$db->quote('%'.$kbarttitle.'%');
        }
        if($kbartkeyword != null){
            $kbartkeyword = trim($kbartkeyword);
            $inquery .=" AND category.metakey LIKE ".$db->quote('%'.$kbartkeyword.'%').'';
        }
        $query = "SELECT category.name, category.id, category.logo
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.parentid = 0 AND kb = 1 AND category.status = 1" . $inquery;
        $db->setQuery($query);
        $parentcat = $db->loadObjectList();
        foreach ($parentcat as $cat) {
            $query = "SELECT category.name, category.id
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.parentid = " . $cat->id . " AND kb = 1 AND category.status = 1 LIMIT 4";
            $db->setQuery($query);
            $cat->subcategory = $db->loadObjectList();
        }
        // echo '<pre>';print_r($parentcat);echo '</pre>';exit;
        $result['categories'] = $parentcat;
        $inquery = '';
        if ($kbarttitle != null) {
            $kbarttitle = trim($kbarttitle);
            $inquery .=" AND article.subject LIKE ".$db->quote('%'.$kbarttitle.'%');
        }

        if($kbartkeyword != null){
            $kbartkeyword = trim($kbartkeyword);
            $inquery .= " AND article.metakey LIKE ".$db->quote('%'.$kbartkeyword.'%');
        }

        // Pagination
        $query = "SELECT COUNT(article.id) 
                    FROM `#__js_ticket_articles` AS article
                    WHERE article.status = 1" . $inquery;
        $db->setQuery($query);
        $total = $db->loadResult();
        $result['total'] = $total;

        $query = "SELECT article.subject,article.content, article.id AS articleid
                    FROM `#__js_ticket_articles` AS article
                    WHERE article.status = 1";
        $query .= $inquery;
        $db->setQuery($query,$limitstart, $limit);
        $result['articles'] = $db->loadObjectList();
        $result['lists']['articletitle'] = $kbarttitle;
        $result['lists']['articlekeywords'] = $kbartkeyword;
        return $result;
    }

    function getUserKnowledgebase($id,$limitstart,$limit) {
        if ($id) {
            if (!is_numeric($id))
                return false;
        } else
            $id = 0;

        $db = $this->getDBO();
        $result = array();

        if ($id != 0)
            $inquery = " AND article.categoryid = " . $id;
        else
            $inquery = '';

        $query = "SELECT category.name, category.id
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.parentid = " . $id . " AND kb = 1";
        $db->setQuery($query);
        $result['categories'] = $db->loadObjectList();

        $query = "SELECT category.name, category.logo, category.id
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.id = " . $id . " AND kb = 1";
        $db->setQuery($query);
        $result['category'] = $db->loadObject();

        // Pagination
        $query = "SELECT COUNT(article.id) 
                    FROM `#__js_ticket_articles` AS article
                    WHERE article.status = 1" . $inquery;
        $db->setQuery($query);
        $result['total'] = $db->loadResult();

        $query = "SELECT article.subject,article.content, article.id AS articleid
                    FROM `#__js_ticket_articles` AS article
                    WHERE article.status = 1" . $inquery;
        $query .=" ORDER BY article.ordering ASC";
        $db->setQuery($query,$limitstart,$limit);
        $result['articles'] = $db->loadObjectList();

        return $result;
    }

    function getUserArticleDetails($id) {

        if (!is_numeric($id))
            return;
        $db = $this->getDBO();
        $result = array();
        $query = "SELECT article.id,article.subject, article.content, category.name
                FROM `#__js_ticket_articles` AS article
                LEFT JOIN `#__js_ticket_categories` AS category on article.categoryid = category.id
                WHERE article.id = " . $id .' AND article.status = 1';
        $db->setQuery($query);
        $result['article'] = $db->loadObject();

        $query = "SELECT attachment.id AS attachmentid,attachment.articleid AS id, attachment.filename,attachment.filesize
                FROM `#__js_ticket_articles_attachments` AS attachment
                WHERE attachment.articleid = " . $id;

        $db->setQuery($query);
        $result['articleattachments'] = $db->loadObjectList();
        $this->updateHitsByID($id);
        return $result;
    }

    function updateHitsByID($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "UPDATE `#__js_ticket_articles` SET hits = hits + 1 WHERE id = $id";
        $db->setQuery($query);
        $db->execute();
        return;
    }

    function getKnowledgebaseForMP($listtype,$maxrecord){
        if( ! is_numeric($maxrecord)){
            return false;
        }
        $db = JFactory::getDbo();
        $query = "SELECT article.subject AS title,article.id AS id
                    FROM `#__js_ticket_articles` AS article WHERE article.status = 1 AND article.type = 0";
        switch($listtype){
            case 1: // Latest
                $query .= " ORDER BY article.created DESC";
            break;
            case 2: // Popular
                $query .= " ORDER BY article.hits DESC";
            break;
            case 3: // All
            break;
        }
        $query .= " LIMIT $maxrecord";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getTypeForByParentId() {
        $array = null;
        $parentid = JFactory::getApplication()->input->get('parentid');
        if ($parentid) {
            if (!is_numeric($parentid))
                return false;
            $db = JFactory::getDbo();
            $query = "SELECT kb,downloads,announcement,faqs FROM `#__js_ticket_categories` WHERE id = $parentid";
            $db->setQuery($query);
            $result = $db->loadObject();
            $array['kb'] = $result->kb;
            $array['downloads'] = $result->downloads;
            $array['announcement'] = $result->announcement;
            $array['faqs'] = $result->faqs;
        }
        return json_encode($array);
    }

    function checkParentType() {
        $message = '';
        $type = JFactory::getApplication()->input->get('type');
        $parentid = JFactory::getApplication()->input->get('parentid');
        if ($parentid) {
            if (!is_numeric($parentid))
                return false;
            $db = JFactory::getDbo();
            $query = "SELECT * FROM `#__js_ticket_categories` WHERE id = $parentid";
            $db->setQuery($query);
            $result = $db->loadObject();
            if ($result->$type != 1) {
                $message = '<div class="admin-title">' . JText::_('You') . ' parent[s] ' . JText::_('does not have this type') . '</div>';
                $message .= '<div class="admin-title">' . JText::_('Would you like to add this type to parent[s]') . '</div>';
                $message .= '<div class="admin-title"><a href="#" onclick="addTypeToParent(' . $parentid . ',\'' . $type . '\');">' . JText::_('Yes') . '</a></div>';
                $message .= '<div class="admin-title"><a href="#" onclick="closemsg(\'' . $type . '\');">' . JText::_('No') . '</a></div>';
            }
        }
        return $message;
    }

    function checkChildType() {
        $message = 0;
        $type = JFactory::getApplication()->input->get('type');
        $currentid = JFactory::getApplication()->input->get('currentid');
        if ($currentid) {
            if (!is_numeric($currentid))
                return false;
            $db = JFactory::getDbo();
            $query = "SELECT * FROM `#__js_ticket_categories` WHERE parentid = $currentid";
            $db->setQuery($query);
            $result = $db->loadObjectList();
            if(!empty($result)){
                $ans = true;
                foreach($result AS $row){
                    if($row->$type == 1){
                        $ans = false;
                        break;
                    }
                }
                if ($ans == false) {
                    $message = '<div class="admin-title">' . JText::_('Your child[s] have this type') . '</div>';
                    $message .= '<div class="admin-title">' . JText::_('You cannot unmark it') . '</div>';
                }
            }
        }
        return $message;
    }

    function makeParentOfType() {
        $parentid = JFactory::getApplication()->input->get('parentid');
        $type = JFactory::getApplication()->input->get('type');
        $this->makeParentOfTypeRecursive($parentid, $type);
        return true;
    }

    function makeParentOfTypeRecursive($parentid, $type) {
        $db = JFactory::getDbo();
        $query = "UPDATE `#__js_ticket_categories` SET $type = 1";
        $db->setQuery($query);
        $db->execute();
        $query = "SELECT parentid FROM `#__js_ticket_categories` WHERE id = $parentid";
        $db->setQuery($query);
        $isparent = $db->loadResult();
        if ($isparent != 0)
            $this->makeParentOfTypeRecursive($isparent, $type);
        else
            return;
    }

    function getDownloadAttachmentById($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "SELECT attach.filename, attach.articleid 
                    FROM `#__js_ticket_articles_attachments` AS attach 
                    WHERE attach.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $articleid = $object->articleid;
        $filename = $object->filename;
        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator    
        }  
        $path = $base.'/'.$datadirectory;
        $path = $path . '/attachmentdata';
        $path = $path . '/article/article_' . $articleid;
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
        $query = "SELECT attach.filename, attach.articleid 
                    FROM `#__js_ticket_articles_attachments` AS attach 
                    WHERE attach.id = $id";
        $db->setQuery($query);
        $object = $db->loadObject();
        $articleid = $object->articleid;
        $filename = $object->filename;
        $datadirectory = $this->getJSModel('config')->getConfigurationByName('data_directory');
        $base = JPATH_BASE;
        if(JFactory::getApplication()->isClient('administrator')){
            $base = substr($base, 0, strlen($base) - 14); //remove administrator    
        }  
        $path = $base.'/'.$datadirectory;
        $path = $path . '/attachmentdata';
        $path = $path . '/article/article_' . $articleid;
        $file = $path . '/' . $filename;
		if(unlink($file)){
			$query = "DELETE FROM `#__js_ticket_articles_attachments` WHERE id = ".$id;
			$db->setQuery($query);
			if($db->execute()){
				return DELETED;
			}else{
				return DELETE_ERROR;
			}
		}
		return DELETE_ERROR;
	}

    function getLatestKnowledgebaseForUserCP(){

        $db = $this->getDBO();

        $query = "SELECT kbarticle.*,kbcat.name AS articlecategory
            FROM `#__js_ticket_articles` AS kbarticle
            LEFT JOIN `#__js_ticket_categories` AS kbcat ON kbarticle.categoryid = kbcat.id
            WHERE kbarticle.status = kbarticle.status ORDER BY kbarticle.created" ;
        $db->setQuery($query,0,4);
        $result = $db->loadObjectList();
        return $result;
    }

    function getSubCategoryKnowledge($parentid){
        if(!is_numeric($parentid))
            return false;
        $db = $this->getDBO();
        $result = array();
        $categories = $this->getAllSubCategoriesById($parentid);
        if($categories != ''){
            foreach($categories AS $category){
                $catarray[] = $category->id;
            }
            if(!empty($catarray)){
                $catarray = implode(',', $catarray);
                $query = "SELECT id as articleid,categoryid,subject
                            FROM `#__js_ticket_articles`
                            WHERE find_in_set(categoryid, '". $catarray ."') LIMIT 5";
                $db->setQuery($query);
                $result = $db->loadObjectList();
            }
        }
        return $result;
    }

    function getAllSubCategoriesById($categoryid){
        if(!is_numeric($categoryid))
            return false;
        $db = $this->getDBO();
        $query = "SELECT id
                FROM    (SELECT id, parentid FROM `#__js_ticket_categories`
                         order by parentid, id) category_sorted,
                        (select @pv := '".$categoryid."') initialization
                WHERE   find_in_set(parentid, @pv) > 0
                AND     @pv := concat(@pv, ',', id)";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getLatestKnowledgebaseForAdminCP(){

        $db = $this->getDBO();
        $query = "SELECT article.subject,article.content, article.id AS articleid
                    FROM `#__js_ticket_articles` AS article
                    WHERE article.status = 1 ORDER BY created DESC";
        $db->setQuery($query,0, 5);
        $result = $db->loadObjectList();
        return $result;
    }
}
?>

