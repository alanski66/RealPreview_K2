<?php
/*
 // "YouTube Videos" Plugin by JoomlaWorks for K2 v2.x - Version 1.0
 // Copyright (c) 2006 - 2009 JoomlaWorks Ltd. All rights reserved.
 // Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 // More info at http://www.joomlaworks.gr
 // Designed and developed by the JoomlaWorks team
 // *** Last update: June 20th, 2009 ***
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');
JLoader::register('K2Plugin', JPATH_ADMINISTRATOR.DS.'components'.DS.'com_k2'.DS.'lib'.DS.'k2plugin.php');

class plgK2Jkrealpreview extends K2Plugin {

	// Some params
	var $pluginName = 'jkrealpreview';
	var $pluginNameHumanReadable = 'Real Preview Plugin';

	function plgK2Jkrealpreview( & $subject, $params) {
	
		parent::__construct($subject, $params);
	}
	
	function onAfterK2Save(&$item, $isNew)
	{		
		$rpreview_helpers = JPATH_ADMINISTRATOR.DS.'components'.DS.'com_realpreview_k2'.DS.'helpers'.DS.'helpers.php';
		
		if(file_exists($rpreview_helpers))
		{
			require_once($rpreview_helpers);
			
			$mainframe = &JFactory::getApplication();
			
			if($mainframe->isAdmin())
			{
				RealpreviewHelper::initialize_draft($item->id);
			}
			else
			{
				RealpreviewHelper::initialize_draft($item->id,true);
			}
		}
	}

} // END CLASS

