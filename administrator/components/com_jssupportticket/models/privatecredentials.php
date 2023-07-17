<?php
/**
 * @Copyright Copyright (C) 2015 ... Ahmad Bilal
 * @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
 * Company:     Buruj Solutions
  + Contact:    www.burujsolutions.com , info@burujsolutions.com
 * Created on:	May 22, 2015
  ^
  + Project:    JS Tickets
  ^
 */
defined('_JEXEC') or die('Not Allowed');

jimport('joomla.application.component.model');
jimport('joomla.html.html');

class JSSupportticketModelPrivatecredentials extends JSSupportTicketModel {

    var $activity_log;
    var $_jinput = null;

    function __construct() {
        parent::__construct();

        $this->activity_log = $this->getJSModel('activitylog');
        $this->_jinput = JFactory::getApplication()->input;
    }

	function getFormForPrivateCredentials(){
		$ticketid = $this->_jinput->get('ticketid',0);
		$cred_id = $this->_jinput->get('cred_id',0);
		$uid = $this->_jinput->get('uid',0);
		// manage data for form (mainly to handle edit case`)

		$c_user = JSSupportticketCurrentUser::getInstance();

		$cred_data_array = array();
		if(!is_numeric($ticketid) || $ticketid ==  0){
			return false;
		}
		if(is_numeric($cred_id) && $cred_id > 0){
			$cred_data = $this->_jinput->get('cred_data','');
			if($cred_data != ''){
				$cred_json_string = base64_decode($cred_data);
				$cred_data_array = json_decode($cred_json_string, true);
				$cred_data_array['uid'] = $uid;
				$cred_data_array['id'] = $cred_id;
			}
		}
		if(empty($cred_data_array)){
			$cred_data_array['credentialtype'] = '';
			$cred_data_array['username'] = '';
			$cred_data_array['password'] = '';
			$cred_data_array['info'] = '';
			$cred_data_array['id'] = '';
			$cred_data_array['uid'] = $c_user->getId();
		}
		$cred_data_array['ticketid'] = $ticketid;

		$html = $this->generateFormHTML($cred_data_array);

		return json_encode($html);
	}

	function generateFormHTML($cred_data_array){
		$html ='';
		$html .='
				<form id="js-ticket-usercredentails-form" method="POST" action="#">
					<div class="private-crendentials-form-popup">
		                <div class="js-ticket-edit-form-wrp">
		                    <div class="js-ticket-edit-field-title">
		                        ' . JText::_('Credential Type') . '
		                    </div>
		                    <div class="js-ticket-edit-field-wrp">
		                        <input class="inputbox js-ticket-edit-field-input" type="text" name="credentialtype" id="credentialtype" value="'. $cred_data_array['credentialtype'] .'" required />
		                    </div>
		                    <div class="js-ticket-edit-field-title">
		                        '.JText::_('Username').'
		                    </div>
		                    <div class="js-ticket-edit-field-wrp">
		                        <input class="inputbox js-ticket-edit-field-input" type="text" name="username" id="username" value="'.$cred_data_array['username'] .'"  required />
		                    </div>
		                    <div class="js-ticket-edit-field-title">
		                        '.JText::_('Password').'
		                    </div>
		                    <div class="js-ticket-edit-field-wrp">
		                        <input class="inputbox js-ticket-edit-field-input" type="password" name="password" id="password" value="'. $cred_data_array['password'] .'" required />
		                    </div>
		                    <div class="js-ticket-edit-field-title">
		                        ' . JText::_('Additional Info') . '
		                    </div>
		                    <div class="js-ticket-edit-field-wrp">';
                                    $editor = JFactory::getConfig()->get('editor');
                                    $editor = JEditor::getInstance($editor);
                            $html .= $editor->display('info', $cred_data_array['info'], '', '300', '60', '20', false) . '
		                    </div>
		                    <div class="js-ticket-priorty-btn-wrp">
		                        <input type="submit" class="js-ticket-priorty-save" name="savecredentials"  value="' . JText::_('Save') . '" />
		                        <input type="button" class="js-ticket-priorty-cancel" name="cancelcredentials" id="cancelcredentials"  value="' . JText::_('Cancel') . '" onclick=closeCredentailsForm('. $cred_data_array['ticketid'] . ') />
		                    </div>
		                    <input type="hidden" name="pc_ticketid" value="'. $cred_data_array['ticketid'] .'"/>
		                    <input type="hidden" name="id" value="'. $cred_data_array['id'] .'"/>
		                    <input type="hidden" name="uid" value="'. $cred_data_array['uid'] .'"/>
		            	</div>
	            	</div>
				</form>
				';
		return $html;
	}

	function getPrivateCredentials($ticketid){
		if(!is_numeric($ticketid) || $ticketid ==  0){
			return false;
		}

		$db = $this->getDbo();

		$user = JSSupportticketCurrentUser::getInstance();
		if($user->getIsStaff()){
            $credentialpermission = $user->checkUserPermission('View Credentials');
            if ($credentialpermission == false)
                return PERMISSION_ERROR;
        }

		$query = "SELECT id, data, ticketid,uid,status
					FROM `#__js_ticket_privatecredentials`
					WHERE ticketid = ".$ticketid;

		$query .= " ORDER BY created DESC";
		$db->setQuery($query);
		$cred_data = $db->loadObjectList();
		$html = '';
		if($cred_data != '' ){
			foreach ($cred_data as $cred) {
				$html .= $this->generatePrivateCredentialsHTML($ticketid, $cred->data, $cred->id, $cred->uid, $cred->status);
			}
		}
		if($html != ''){
			$return_array['status'] = 1;
			$return_array['content'] = $html;
			return json_encode($return_array);
		}
		$return_array['status'] = 1;
		$return_array['content'] = '';
		return json_encode($return_array);
	}

	function removePrivateCredential(){
		$cred_id = $this->_jinput->get('cred_id' ,0);

		if(!is_numeric($cred_id) || $cred_id ==  0){
			return false;
		}

		$del_allowed = false;
		$user = JSSupportticketCurrentUser::getInstance();
		$current_u_id = $user->getId();

		$row = $this->getTable('privatecredentials');
		if(is_numeric($current_u_id) && $current_u_id > 0){
			// if(current_user_can('manage_options')){
			if(false){
				$del_allowed = true;
			}elseif( $user->getIsStaff() && $user->checkUserPermission('Delete Credentials') ) {
				// checks if is staff memeber and check whehter he is allowed to delete.
				$del_allowed = true;
			}else{
				$row->load($cred_id);
				if($row->uid == $current_u_id){
					$del_allowed = true;
				}
			}
		}else{
			$db = $this->getDbo();
			$row->load($cred_id);
			$email = $this->_jinput->get('email',null);
			$ticketid = $this->_jinput->get('ticketrandomid',null);
			$query = "SELECT ticketid FROM `#__js_ticket_tickets`
			WHERE `email` = '".$email."' AND `ticketid` = '".$ticketid."' ";
			$db->setQuery($query);
			$ticketid = $db->loadResult($query);
			if($ticketid == $row->ticketid){
				$del_allowed = true;
			}
		}

		if($del_allowed === true && $row->delete($cred_id)){
			return true;
		}else{
			return false;
		}

	}

	function storePrivateCredentials(){
		$data = array();
		$result_array = array();
		if($this->_jinput->post->getString('formdata_string' , '') != '' ){
			parse_str($this->_jinput->post->getString('formdata_string'),$result_array);
		}
		$data['id'] = '';
		$result_string = '';
		if(!empty($result_array)){
			$data['ticketid'] = $result_array['pc_ticketid'];
			$data['id'] = $result_array['id'];
			$data['uid'] = $result_array['uid'];
			unset($result_array['pc_ticketid']);
			unset($result_array['uid']);
			unset($result_array['id']);
			$result_array = array_filter($result_array);
			if(!empty($result_array)){
				$result_string = json_encode($result_array);
				$result_string = base64_encode($result_string);
				include_once JPATH_COMPONENT_ADMINISTRATOR.'/include/classes/privatecredentials.php';
	    		$privatecredentialsclass = new privatecredentials();
				$result_string = $privatecredentialsclass->encrypt($result_string);
			}
		}

		if($result_string == ''){
			$return_array = array();
			$return_array['status'] = 0;
			$return_array['content'] = '';
			$return_array['error_message'] = JText::_('Please insert values').'.';
			return json_encode($return_array);
		}

		$data['data'] = $result_string;
		$data['status'] = 1;// status 0 is for deleted credentials
		//if( $data['id'] != ''){
			$data['created'] = date('Y-m-d H:i:s');
		//}

		$row = $this->getTable('privatecredentials');

		$error = 0;

		if(isset($data['ticketid']) && is_numeric($data['ticketid']) && $data['ticketid'] > 0){
			if (!$row->bind($data)) {
			    $error = 1;
			}
			if (!$row->store()) {
			    $error = 1;
			}
		}else{
			$error = 1;
		}

		if($error == 1){
			return false;// save error or data error.
		}else{ // everything ok

			// return html of stored credntail
			$html = $this->generatePrivateCredentialsHTML($data['ticketid'], $data['data'], $row->id, $row->uid, $row->status);
			if($html){
				$return_array = array();
				$return_array['status'] = 1;
				$return_array['content'] = $html;
				return json_encode($return_array);
			}
			return FALSE;
		}
	}

	function generatePrivateCredentialsHTML($ticketid,$cred_data_string,$cred_id,$uid,$status){
		if(!is_numeric($ticketid)){
			return false;
		}

		$user = JSSupportticketCurrentUser::getInstance();

		include_once JPATH_COMPONENT_ADMINISTRATOR.'/include/classes/privatecredentials.php';
	    $privatecredentialsclass = new privatecredentials();
	    $cred_data_string = $privatecredentialsclass->decrypt($cred_data_string);
	    $cred_json = base64_decode($cred_data_string);
		$cred_array = json_decode($cred_json,true);

		if($status == 1 && $cred_array && is_array($cred_array) && !empty($cred_array)){
			$html = '
			<div class="private-crendentials-detail-popup" id="js-ticket-usercredentails-single-id-'.$cred_id.'">
                <div class="js-ticket-edit-form-wrp">
                    <div class="js-ticket-crendential-detail-wrp">
                        <div class="js-ticket-crendential-detail-head">
                            <div class="js-ticket-title">';
                                    if(isset($cred_array['credentialtype']) && $cred_array['credentialtype'] != ''){
								    	$html .= JText::_($cred_array['credentialtype']);
								    }else{
								    	$html .= '---------';
									}
                                $html .= '</div>
                                <div class="js-ticket-value">';
                                    $user_name =  JText::_('Visitor');
								    if(is_numeric($uid) && $uid > 0){
								    	if ($userdata = JFactory::getUser($uid)){
										    $user_name =  $userdata->name;
										}
								    }

								$html .= '&nbsp;('. JText::_('By') .':&nbsp;'. $user_name .')
                                </div>
                        </div>
                        <div class="js-ticket-crendential-detail-body">
                            <div class="js-ticket-rows-wrp">
                                <div class="js-ticket-title">
                                    ' . JText::_('Username').' :' . '
                                </div>
                                <div class="js-ticket-value">';
                                    if(isset($cred_array['username']) && $cred_array['username'] != ''){
								    	$html .= $cred_array['username'];
								    }else{
								    	$html .= '---------';
									}
                                $html .= '</div>
                            </div>
                            <div class="js-ticket-rows-wrp">
                                <div class="js-ticket-title">
                                    ' . JText::_('Password')." :" . '
                                </div>
                                <div class="js-ticket-value">';
                                    if(isset($cred_array['password']) && $cred_array['password'] != ''){
			        			    	$html .= $cred_array['password'];
			        			    }else{
			        			    	$html .= '---------';
			        				}
                                $html .= '</div>
                            </div>
                            <div class="js-ticket-rows-wrp">
                                <div class="js-ticket-title">
                                    ' . JText::_('Additional Info')." :" . '
                                </div>
                                <div class="js-ticket-value">';
                             		if(isset($cred_array['info']) && $cred_array['info'] != ''){
			        			    	$html .= $cred_array['info'];
			        			    }else{
			        			    	$html .= '---------';
			        				}
                                $html .= '</div>
                            </div>
                        </div>';
                        $credential_edit_permission = false;
						$credential_delete_permission = false;
					    if($user->getIsStaff()){
					    	$credential_edit_permission = $user->checkUserPermission('Edit Credentials');
							$credential_delete_permission = $user->checkUserPermission('Delete Credentials');
						}
						// elseif(current_user_can('manage_options')){
						// 	$credential_edit_permission = true;
						// 	$credential_delete_permission = true;
						// }
						elseif($user->getId() == $uid){
							$credential_edit_permission = true;
							$credential_delete_permission = true;
						}

						if($credential_edit_permission || $credential_delete_permission){
	                        $html .= '<div class="js-ticket-crendential-detail-footer">';
	                        	if($credential_edit_permission){
	                            	$html .= '<input type="button" class="js-ticket-priorty-save" name="detail_edit"  value="' . JText::_('Edit') . '" onclick="addEditCredentail('.$ticketid.','.$uid.','.$cred_id.',\''.$cred_data_string.'\');" />';
                            	}
                            	if($credential_delete_permission){
	                            	$html .= '<input type="button" class="js-ticket-priorty-cancel" name="detail_delete" id="detail_delete"  value="' . JText::_('Delete') . '" onclick="removeCredentail('.$cred_id.');" />';
	                        	}
	                        $html .= '</div>';
                        }
                    $html .= '</div>
                </div>
            </div>';
		}else{
			if($status == 0){
				$html = '
				<div class="private-crendentials-detail-popup" id="js-ticket-usercredentails-single-id-'.$cred_id.'">
		            <div class="js-ticket-edit-form-wrp">
		                <div class="js-ticket-crendential-detail-wrp">
		                    <div class="js-ticket-crendential-detail-head">
		                        <div class="js-ticket-title">
		                        	' . JText::_('Credential removed on ticket close') . '
		                        </div>
		                    </div>
		                </div>
		            </div>
	            </div>';
			}else{
				$html = '
				<div class="private-crendentials-detail-popup" id="js-ticket-usercredentails-single-id-'.$cred_id.'">
		            <div class="js-ticket-edit-form-wrp">
		                <div class="js-ticket-crendential-detail-wrp">
		                    <div class="js-ticket-crendential-detail-head">
		                        <div class="js-ticket-title">
		                        	' . JText::_('Failed to retrieve data') . '
		                        </div>
		                    </div>';
		                    $credential_delete_permission = false;
						    if($user->getIsStaff()){
						    	$credential_delete_permission = $user->checkUserPermission('Delete Credentials');
							}
							// elseif(current_user_can('manage_options')){
							// 	$credential_edit_permission = true;
							// 	$credential_delete_permission = true;
							// }
							elseif($user->getId() == $uid){
								$credential_delete_permission = true;
							}

							if($credential_delete_permission){
		                        $html .= '<div class="js-ticket-crendential-detail-footer">';
		                        	if($credential_delete_permission){
		                            	$html .= '<input type="button" class="js-ticket-priorty-cancel" name="detail_delete" id="detail_delete"  value="' . JText::_('Delete') . '" onclick="removeCredentail('.$cred_id.');" />';
		                        	}
		                        $html .= '</div>';
		                    }
		                $html .= '</div>
		            </div>
	            </div>';
			}
		}
		return $html;
	}

	function deleteCredentialsOnCloseTicket($ticketid){
		$db = $this->getDbo();
		// to mark as deleted on closing ticket.
		// set empty array as value.
		$empty_array = array();
		$json_string = json_encode($empty_array);
		$base64_string = base64_encode($json_string);
		include_once JPATH_COMPONENT_ADMINISTRATOR.'/include/classes/privatecredentials.php';
		$privatecredentialsclass = new privatecredentials();
		$empty_array_string = $privatecredentialsclass->encrypt($base64_string);
		// set values in table (empty array in data to remove data, status 0 to show message.)
		$query = "UPDATE `#__js_ticket_privatecredentials` SET status = 0, data = '".$empty_array_string."' WHERE ticketid = " . $ticketid;
		$db->setQuery($query);
		$db->execute();
    }
}
?>
