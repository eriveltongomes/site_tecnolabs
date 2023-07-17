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

class JSSupportticketModelFaqs extends JSSupportTicketModel {

    function __construct() {
        parent::__construct();
    }

    function getFaqForForm($id) {
        if($id) if(!is_numeric($id)) return false;
        $db = $this->getDBO();
        $configs = $this->getJSModel('config')->getConfigByFor('default');
        $title = "";
        $categories = $this->getJSModel('knowledgebase')->getCategories('faqs');
        if(isset($id) && $id <> ''){
            $query = "SELECT faq.*  FROM `#__js_ticket_faqs` AS faq  WHERE faq.id = ".$id;
            $db->setQuery($query);
            $faqs= $db->loadObject();
        }
        $status = array(array('value' => null, 'text' => JText::_('Select Status')),array('value' => '1', 'text' => JText::_('Active')),array('value' => '0', 'text' => JText::_('Disabled')));
        if(isset($id) && $id <> ''){
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text',$faqs->categoryid);
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-select-field " ' . '', 'value', 'text',$faqs->status);
        }else{
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text','');
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox  js-ticket-select-field" ' . '', 'value', 'text',1);
        }
        if (isset($faqs))
            $result[0] = $faqs;
        $result[1] = $lists;
        return $result;
    }

    function storeFaq($data){
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add FAQ' : 'Edit FAQ';
            $per = $user->checkUserPermission($permission);
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $data['staffid'] = $user->getStaffId();
        $row = $this->getTable('faqs');
        $data['content'] = JFactory::getApplication()->input->get('faq_content', '', 'raw');
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

    function getAllFaqs($subject, $catid, $limitstart, $limit) {  // get all staff members

        if ($catid)
            if (!is_numeric($catid))
                return False;
        $db = $this->getDBO();
        $result = array();

        $lists['categories'] = JHTML::_('select.genericList', $this->getJSModel('knowledgebase')->getCategories('faqs'), 'filter_categoryid', 'class="inputbox js-ticket-select-field " ' . '', 'value', 'text', $catid);

        $wherequery = "";
        $clause = " Where ";
        if ($subject) {
            $subject = trim($subject);
            $wherequery .= $clause . " faq.subject LIKE " . $db->quote("%" . $subject . "%");
            $clause = " AND ";
        }
        if ($catid) {
            $wherequery .= $clause . " faq.categoryid = " . $catid;
            $clause = " AND ";
        }
        $query = "SELECT COUNT(faq.id) FROM `#__js_ticket_faqs` AS faq ";
        $query.=$wherequery;

        $db->setQuery($query);
        $total = $db->loadResult();
        if ($total <= $limitstart)
            $limitstart = 0;

        $query = "SELECT faq.*,kbcat.name AS faqcategory
            FROM `#__js_ticket_faqs` AS faq
            LEFT JOIN `#__js_ticket_categories` AS kbcat ON faq.categoryid = kbcat.id ";
        $query.=$wherequery;
        $db->setQuery($query, $limitstart, $limit);
        if ($subject)
            $lists['filter_subject'] = $subject;
        $result[0] = $db->loadObjectList();
        $result[1] = $total;
        $result[2] = $lists;
        return $result;
    }

    function getUserCatAndFaqs($id ,$subject,$keyword,$limitstart, $limit) {
        $db = $this->getDBO();
        $inquery = '';
        $inquerykey = '';
        $inquerycat = '';
        if ($subject != null) {
            $subject = trim($subject);
            $inquery .=" AND faq.subject LIKE " . $db->quote("%" . $subject . "%");
            $inquerycat .=" AND category.name LIKE " . $db->quote("%" . $subject . "%");
            $lists['filter_subject'] = $subject;
        }
        // keyword search
        if($keyword != null){
            $inquerykey .= " AND category.metakey LIKE " . $db->quote("%" . $keyword . "%");
        }
        // copid form wp
        if ($id) {
            if (!is_numeric($id))
                return false;
        } else
            $id = 0;
        $result = array();
        $query = "SELECT category.name, category.id, category.logo AS catlogo
                 FROM `#__js_ticket_categories` AS category 
                    WHERE category.parentid = " . $id . " AND category.faqs = 1 AND category.status = 1";
        $query .= $inquerycat.$inquerykey;
        $db->setQuery($query);
        $result['categories'] = $db->loadObjectList();

        $query = "SELECT category.name
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.id = " . $id . " AND category.faqs = 1 AND category.status = 1";
        $db->setQuery($query);
        $result['categoryname'] = $db->loadResult();

        if ($id != 0)
            $inquery .= " AND faq.categoryid = " . $id;

        if ($id > 0) {
            $query = "SELECT category.name, category.logo, category.id
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.id = " . $id . " AND kb = 1 AND category.status = 1";
            $db->setQuery($query);
            $result['categoryname'] = $db->loadResult();
        }

        $inquery .= $inquerykey;
        // Pagination
        $query = "SELECT COUNT(faq.id)
                   FROM `#__js_ticket_faqs` AS faq
                   JOIN `#__js_ticket_categories` AS category ON category.id = faq.categoryid
                WHERE faq.status = 1 " . $inquery;
        $db->setQuery($query);
        $total = $db->loadResult();
        if ( $total <= $limitstart ) $limitstart = 0;
        $result['total'] = $total;

        $query = "SELECT faq.id, faq.subject
                FROM `#__js_ticket_faqs` AS faq
                JOIN `#__js_ticket_categories` AS category ON category.id = faq.categoryid
                WHERE faq.status = 1 " . $inquery;
        $db->setQuery($query,$limitstart,$limit);
        $result['faqs'] = $db->loadObjectList();
        $result['lists']['filter_subject'] = $subject;
        $result['lists']['filter_faq_keyword'] = $keyword;

        return $result;
    }

    function getUserFaqDetail($id){
        if($id){
            $db = $this->getDBO();
            $query = "SELECT faq.subject,faq.content AS detail,category.name AS categoryname
                    FROM `#__js_ticket_faqs` AS faq
                    LEFT JOIN `#__js_ticket_categories` AS category ON faq.categoryid = category.id
                    WHERE faq.id=".$id." AND faq.status = 1";
            $db->setQuery($query);
            $result = $db->loadObject();
            $this->updateHitsByID($id);
            return $result;
        }
    }

    function updateHitsByID($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "UPDATE `#__js_ticket_faqs` SET hits = hits + 1 WHERE id = $id";
        $db->setQuery($query);
        $db->execute();
        return;
    }

    function deleteFaq(){
        $cids = JFactory::getApplication()->input->get('cid', array(0), null, 'array');
        if(!empty($cids)){
            foreach($cids AS $id){
                if(is_numeric($id)){
                    $user = JSSupportticketCurrentUser::getInstance();
                    if(!$user->getIsAdmin()){
                        $per = $user->checkUserPermission('Delete FAQ');
                        if ($per == false) 
                            return PERMISSION_ERROR;
                    }
                    $db =$this->getDbo();
                    $query="DELETE FROM `#__js_ticket_faqs` WHERE id=".$id;
                    $db->setQuery($query);
                    if(!$db->execute()){
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

    function deleteUserFaq($id){
        if(is_numeric($id)){
            $user = JSSupportticketCurrentUser::getInstance();
            $per = $user->checkUserPermission('Delete FAQ');
            if ($per == false) 
                return PERMISSION_ERROR;
            $db =$this->getDbo();
            $query="DELETE FROM `#__js_ticket_faqs` WHERE id=".$id;
            $db->setQuery($query);
            if(!$db->execute()){
                $this->getJSModel('systemerrors')->updateSystemErrors($db->getErrorMsg());
                $db->setError($db->getErrorMsg());
                return DELETE_ERROR;
            }
        }else{
            return DELETE_ERROR;
        }
        return DELETED;
    }

    function getFAQForMP($listtype,$maxrecord){
        if(! is_numeric($maxrecord))
            return false;
        $db = JFactory::getDbo();
        $query = "SELECT faq.subject AS title,faq.id AS id
                    FROM `#__js_ticket_faqs` AS faq WHERE faq.status = 1 ";
        switch($listtype){
            case 1: // Latest
                $query .= " ORDER BY faq.created DESC";
            break;
            case 2: // Popular
                $query .= " ORDER BY faq.hits DESC";
            break;
            case 3: // All
            break;
        }
        $query .= " LIMIT $maxrecord";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getSubCategoryFaqsByParentCat($parentid){
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
                $query = "SELECT id,categoryid,subject
                            FROM `#__js_ticket_faqs`
                            WHERE find_in_set(categoryid, '". $catarray ."') LIMIT 5";
                $db->setQuery($query);
                $result = $db->loadObjectList();
            }
        }
        return $result;
    }
}

?>
