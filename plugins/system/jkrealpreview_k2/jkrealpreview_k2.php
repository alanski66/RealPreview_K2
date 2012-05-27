<?php
/**
 * @version     0.1 - Content Preview
 * @package     Real Preview component
 * @author      Joomkit Ltd <info@joomkit.com>
 * @copyright   Copyright (C) 2010 Joomkit Ltd. All rights reserved.
 * @license     GNU/GPL
 * @link	http://www.joomkit.com
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );


class plgSystemJKRealpreview_k2 extends JPlugin
{
	function plgSystemJKRealpreview_k2(& $subject, $config) {
		parent::__construct($subject, $config);
	}
	
	function onAfterInitialise()
	{      
		$mainframe = &JFactory::getApplication();
		
		$option = JRequest::getVar('option');
		$view = JRequest::getVar('view');
		$task = JRequest::getVar('task','');
		
		if($option != 'com_k2' or $view != 'item')
			return;
	
		if($mainframe->isAdmin())
		{		
			$skip_plg = JRequest::getInt( 'skip_plg',0);
			if($skip_plg)return;
			//return;
			$db = JFactory::getDBO();
			$cid = JRequest::getVar( 'cid', array(0), '', 'array' );
			JArrayHelper::toInteger($cid, array(0));
			$id	= JRequest::getInt( 'id', $cid[0]);

			if(($task=='' or $task=='edit') and $id)
			{
				$query = 'SELECT version FROM #__realpreview_k2 
				WHERE `published` = "1" AND  `itemid` = '.$id.'';
				
				$query = "SELECT version FROM #__realpreview_k2 
							WHERE `itemid`={$id}
							ORDER BY `published` DESC,`version` DESC
							LIMIT 1";
				$db->setQuery($query);
				$v = (int)$db->loadResult();
				
				if (!$v) 
				{
					require_once(JPATH_ADMINISTRATOR.'/components/com_realpreview_k2/helpers/helpers.php');
					RealpreviewHelper::initialize_draft($id);
					$db->setQuery($query);
					$v = (int)$db->loadResult();
					if(!$v)$v =1;
				}	
			
				$mainframe->redirect("index.php?option=com_realpreview_k2&itemid={$id}&version={$v}");
			}
        }
		else
		{
			$draft_id = JRequest::getInt('draft_id');
			$id = JRequest::getInt('id');
			$version = JRequest::getInt('version');
			$itemid = JRequest::getInt('itemid');
			
			if($task !='download' or !$draft_id or !$id)
					return;
					
			$link = "index.php?option=com_realpreview_k2&view=item&fileid={$id}&id={$draft_id}";
			$link .= "&itemid={$itemid}&version={$version}";
			$redirect = JRoute::_($link,false);

			$mainframe->redirect($redirect);
			
		}
        return;
	}
	
    
} 
