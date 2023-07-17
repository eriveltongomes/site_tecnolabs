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

class JSSupportticketModelAnnouncements extends JSSupportTicketModel {
    function __construct() {
        parent::__construct();    
    }

    function getAnnouncementForForm($id){
        if($id) if (!is_numeric($id)) return false;
        $db = $this->getDBO();
        $categories = $this->getJSModel('knowledgebase')->getCategories('announcement');
        //$type = array(array('value' => null, 'text' => JText::_('Type')), array('value' => 2, 'text' => JText::_('Private')), array('value' => 1, 'text' => JText::_('Public')));
        $status = array(array('value' => null, 'text' => JText::_('Select Status')),array('value' => '1', 'text' => JText::_('Active')),array('value' => '0', 'text' => JText::_('Disabled')));
        if (isset($id) && $id <> '') {
            $query = "SELECT ann.*  FROM `#__js_ticket_announcements` AS ann  WHERE ann.id = " . $id;
            $db->setQuery($query);
            $announcements = $db->loadObject();
        }
        if (isset($announcements)) {
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', $announcements->categoryid);
            //$lists['type'] = JHTML::_('select.genericList', $type, 'type', 'class="inputbox required" ' . '', 'value', 'text', $announcements->type);
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-select-field " ' . '', 'value', 'text',$announcements->status);
        } else {
            $lists['categories'] = JHTML::_('select.genericList', $categories, 'categoryid', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text', '');
            //$lists['type'] = JHTML::_('select.genericList', $type, 'type', 'class="inputbox required" ' . '', 'value', 'text', '');
            $lists['status'] = JHTML::_('select.genericList', $status, 'status', 'class="inputbox js-ticket-select-field" ' . '', 'value', 'text',1);
        }
        if(isset($announcements))
            $result[0] = $announcements;
        $result[1] = $lists;
        return $result;
    }

    function storeAnnouncement($data) {
        $user = JSSupportticketCurrentUser::getInstance();
        if(!$user->getIsAdmin()){
            $permission = ($data['id'] == '') ? 'Add Announcement' : 'Edit Announcement';
            $per = $user->checkUserPermission($permission);
            if ($per == false) 
                return PERMISSION_ERROR;
        }
        $data['description'] = JFactory::getApplication()->input->getString('description', '', 'raw');
        $row = $this->getTable('announcements');
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

    function deleteAnnouncement() {
        $cids = JFactory::getApplication()->input->get('cid', array(0), null, 'array');
        if(!empty($cids)){
            foreach($cids AS $id){
                if(is_numeric($id)){
                    $user = JSSupportticketCurrentUser::getInstance();
                    if(!$user->getIsAdmin()){
                        $per = $user->checkUserPermission('Delete Announcement');
                        if ($per == false)
                            return PERMISSION_ERROR;
                    }
                    $db = $this->getDbo();
                    $query = "DELETE FROM `#__js_ticket_announcements` WHERE id=" . $id;
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
        }else
            return DELETE_ERROR;
        return DELETED;
    }

    function deleteUserAnnouncement($id) {
        if(is_numeric($id)){
            $user = JSSupportticketCurrentUser::getInstance();
            $per = $user->checkUserPermission('Delete Announcement');
            if ($per == false)
                return PERMISSION_ERROR;
            $db = $this->getDbo();
            $query = "DELETE FROM `#__js_ticket_announcements` WHERE id=" . $id;
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

    function getAllAnnouncements($title, $catid, $limitstart, $limit) {//$title, $catid, $anntype, $limitstart, $limit
        if ($catid)
            if (!is_numeric($catid))
                return false;
        $db = $this->getDBO();
        $result = array();
        //$type = array(array('value' => null, 'text' => JText::_('Type')), array('value' => 2, 'text' => JText::_('Private')), array('value' => 1, 'text' => JText::_('Public')));

        $lists['categories'] = JHTML::_('select.genericList', $this->getJSModel('knowledgebase')->getCategories('announcement'), 'filter_categoryid', 'class="inputbox js-ticket-select-field " ' . '', 'value', 'text', $catid);
        //$lists['type'] = JHTML::_('select.genericList', $type, 'filter_type', 'class="inputbox " ' . '', 'value', 'text', $anntype);

        $wherequery = "";
        $clause = " Where ";
        if ($title) {
            $title = trim($title);
            $wherequery .= $clause . " ann.title LIKE " . $db->quote("%" . $title . "%");
            $clause = " AND ";
        }
        if ($catid) {
            $wherequery .= $clause . " ann.categoryid = " . $catid;
            $clause = " AND ";
        }
        /*if ($anntype) {
            if(is_numeric($anntype))
                $wherequery .= $clause . " ann.type = " . $anntype;
        }*/
        $query = "SELECT COUNT(ann.id) FROM `#__js_ticket_announcements` AS ann " ;
        $query.=$wherequery;

		$db->setQuery($query);
		$total = $db->loadResult();
		if ( $total <= $limitstart ) $limitstart = 0;

		$query = "SELECT ann.*,kbcat.name AS anncategory
			FROM `#__js_ticket_announcements` AS ann
			LEFT JOIN `#__js_ticket_categories` AS kbcat ON ann.categoryid = kbcat.id " ;
                $query.=$wherequery;
		
        $db->setQuery($query,$limitstart, $limit);
		if ($title) $lists['filter_title'] = $title;
		$result[0] = $db->loadObjectList();
		$result[1] = $total;
		$result[2] = $lists;
		return $result;
	}

    function getUserAnnouncementDetail($id){
        if($id){
            if(!is_numeric($id)) return false;
            $db = $this->getDBO();
            $query = "SELECT ann.title AS subject,ann.description AS detail,category.name AS categoryname
                    FROM `#__js_ticket_announcements` AS ann
                    LEFT JOIN `#__js_ticket_categories` AS category ON ann.categoryid = category.id
                    WHERE ann.id=".$id." AND ann.status = 1";
            $db->setQuery($query);
            $result = $db->loadObject();
            $this->updateHitsByID($id);
            return $result;
        }
    }

    function updateHitsByID($id){
        if(!is_numeric($id)) return false;
        $db = JFactory::getDbo();
        $query = "UPDATE `#__js_ticket_announcements` SET hits = hits + 1 WHERE id = $id";
        $db->setQuery($query);
        $db->execute();
        return;
    }

    function getUserCatAndAnnouncements($id ,$title,$keyword,$limitstart, $limit) {
        $db = $this->getDBO();
        $inquery = '';
        $inquerycat = '';
        $inquerykey = '';
        if ($title != null) {
            $title = trim($title);
            $inquery .=" AND announcement.title LIKE " . $db->quote("%" . $title . "%");
            // $inquerycat .=" AND category.name LIKE " . $db->quote("%" . $title . "%");
            $lists['filter_title'] = $title;
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
        $query = "SELECT category.name, category.id,category.logo as catlogo
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.parentid = " . $id . " AND category.announcement = 1 AND category.status = 1";
        // $query .= $inquerycat . $inquerykey;
        $query .= $inquerykey;
        $db->setQuery($query);
        $result['categories'] = $db->loadObjectList();

        $query = "SELECT category.name
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.id = " . $id . " AND announcement = 1 AND category.status = 1";
        $db->setQuery($query);
        $result['categoryname'] = $db->loadResult();

        if ($id != 0)
            $inquery .= " AND announcement.categoryid = " . $id;

        if ($id > 0) {
            $query = "SELECT category.name, category.logo, category.id
                    FROM `#__js_ticket_categories` AS category
                    WHERE category.id = " . $id . " AND kb = 1 AND category.status = 1";
            $db->setQuery($query);
            $result['categoryname'] = $db->loadResult();
        }

        // Pagination
        $query = "SELECT COUNT(announcement.id)
                   FROM `#__js_ticket_announcements` AS announcement
                   LEFT JOIN `#__js_ticket_categories` AS category ON category.id = announcement.categoryid
                WHERE announcement.status = 1 " . $inquery.$inquerykey;
        $db->setQuery($query);
        $total = $db->loadResult();
        if ( $total <= $limitstart ) $limitstart = 0;
        $result['total'] = $total;

        $query = "SELECT announcement.id, announcement.title
                FROM `#__js_ticket_announcements` AS announcement
                LEFT JOIN `#__js_ticket_categories` AS category ON category.id = announcement.categoryid
                WHERE announcement.status = 1 " . $inquery.$inquerykey;
        $db->setQuery($query,$limitstart,$limit);
        $result['announcements'] = $db->loadObjectList();
        $result['lists']['filter_title'] = $title;
        $result['lists']['filter_keyword'] = $keyword;

        return $result;
    }

    function getAnnouncementForMP($listtype,$maxrecord){
        $db = JFactory::getDbo();
        $query = "SELECT ann.title AS title,ann.id AS id
                    FROM `#__js_ticket_announcements` AS ann WHERE ann.status = 1 AND ann.type = 1";
        switch($listtype){
            case 1: // Latest
                $query .= " ORDER BY ann.created DESC";
            break;
            case 2: // Popular
                $query .= " ORDER BY ann.hits DESC";
            break;
            case 3: // All
            break;
        }
        $query .= " LIMIT $maxrecord";
        $db->setQuery($query);
        $result = $db->loadObjectList();
        return $result;
    }

    function getLatestAnnouncementsForUserCP(){
        $db = $this->getDBO();
        $query = "SELECT ann.*,kbcat.name AS anncategory
            FROM `#__js_ticket_announcements` AS ann
            LEFT JOIN `#__js_ticket_categories` AS kbcat ON ann.categoryid = kbcat.id ORDER BY ann.created DESC" ;
        $db->setQuery($query,0,4);
        $result = $db->loadObjectList();
        return $result;
    }

    function getAnnouncementSubCategoryByParentCat($parentid){
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
                            FROM `#__js_ticket_announcements`
                            WHERE find_in_set(categoryid, '". $catarray ."') LIMIT 5";
                $db->setQuery($query);
                $result = $db->loadObjectList();
            }
        }
        return $result;
    }

    function getLatestAnnouncementsForAdminCP(){

        $db = $this->getDBO();
        $query = "SELECT title,description,id
                    FROM `#__js_ticket_announcements`
                    ORDER BY created DESC";
        $db->setQuery($query,0, 5);
        $result = $db->loadObjectList();
        return $result;
    }
}
?>
