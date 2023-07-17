<?php
/**
 * Reactflow Plugin
 * Plugin Version 1.00 - Joomla! Version 1.6
 * Author: Armin Nikdel
 * sales@reactflow.com
 * http://reactflow.com
 * Copyright (c) 2019 Reactflow. All Rights Reserved. 
 * License: GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.html
 * Nice User Info is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is included in Joomla!
defined( '_JEXEC' ) or die('Direct Access to this location is not allowed.');

jimport( 'joomla.plugin.plugin' );

class plgSystemReactflow extends JPlugin
{

	var $reactflowtrackingid;
	function plgSystemReactflow( &$subject, $params )
	{
		parent::__construct( $subject, $params );
		$this->reactflowtrackingid = $this->params->get( 'reactflowtrackingid', '' );
	}

	function onAfterRender()
	{
		$app = JFactory::getApplication();
		$input=JFactory::getApplication()->input;

		if( $app->isAdmin() || $input->getCmd('task') == 'edit' || $input->getCmd('layout') == 'edit' )
		{
			return;
		}
		$c = JResponse::getBody();
		$headpos = stripos( $c, '</head>' );
		if( $headpos !== false ){
	      $ga   = $this->getCode();
	      $head = substr( $c , 0, $headpos );
	      $body = stristr( $c, '</head>' );
	      $c    = $head.$ga.$body;
	      JResponse::setBody($c);
	    }
		
		return true;
	}
	
	function getCode()
	{
	  if ( $this->reactflowtrackingid != '' ){
	  	$this->reactflowtrackingid=str_ireplace(".js", '', $this->reactflowtrackingid);
	  	$this->reactflowtrackingid=str_ireplace("https://cdnflow.co/js/", '', $this->reactflowtrackingid);
	  	$this->reactflowtrackingid=str_ireplace("<script src=\"", '', $this->reactflowtrackingid);
	  	$this->reactflowtrackingid=str_ireplace("\"></script>", '', $this->reactflowtrackingid);
	  	$this->reactflowtrackingid=str_ireplace("<!--Reactflow-->", '', $this->reactflowtrackingid);
	  	$this->reactflowtrackingid=str_ireplace("<!--/Reactflow-->", '', $this->reactflowtrackingid);

	  	if (round($this->reactflowtrackingid)>0){
	    	return '<!--Reactflow--><script src="https://cdnflow.co/js/'.round($this->reactflowtrackingid).'.js"></script><!--/Reactflow-->';
		}else{
			return '<!--Reactflow Invalid ID provided-->';
		}
	  }
	}
	
}
?>
