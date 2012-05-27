<?php
/**
 * @version		$Id: media.php 1113 2011-10-11 14:39:02Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.controller');

class JControllerMedia extends JControllerBase 
{
	function getViewName(){return 'media';} 
	function getModelName(){return 'item';}	
	function getParentView(){return 'item';} 	
	function getChildView(){return 'media';} 
	function getTableName(){return 'items';}
	
	function display() {
		JRequest::setVar('view', 'media');
		parent::display();
	}

	function connector(){
		$mainframe = &JFactory::getApplication();
		$params = &JComponentHelper::getParams('com_media');
		$root = $params->get('file_path', 'media');
		$folder = JRequest::getVar( 'folder', $root, 'default', 'path');
		$type = JRequest::getCmd('type', 'video');
		if(JString::trim($folder)=="") {
			$folder = $root;
		}
		$url = JURI::root(true).'/'.$folder;
		$path=JPATH_SITE.DS.JPath::clean($folder);
		JPath::check($path);
	
/*	
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'elfinder'.DS.'elFinderConnector.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'elfinder'.DS.'elFinder.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'elfinder'.DS.'elFinderVolumeDriver.class.php';
		include_once JPATH_COMPONENT_ADMINISTRATOR.DS.'lib'.DS.'elfinder'.DS.'elFinderVolumeLocalFileSystem.class.php';
*/
	
		$script_file = RealpreviewHelper::get_k2_file('elfinder/elFinderConnector.class.php','lib');
		if($script_file)include_once($script_file);
		$script_file = RealpreviewHelper::get_k2_file('elfinder/elFinder.class.php','lib');
		if($script_file)include_once($script_file);
		$script_file = RealpreviewHelper::get_k2_file('elfinder/elFinderVolumeDriver.class.php','lib');
		if($script_file)include_once($script_file);
		$script_file = RealpreviewHelper::get_k2_file('elfinder/elFinderVolumeLocalFileSystem.class.php','lib');
		if($script_file)include_once($script_file);
		
		function access($attr, $path, $data, $volume) {
			$mainframe = &JFactory::getApplication();
			// Hide files and folders starting with .
			if(strpos(basename($path), '.') === 0 && $attr == 'hidden') {
				return true;
			}   
			// Read only access for front-end. Full access for administration section.
			switch($attr) {
				case 'read':
					return true;
					break;
				case 'write':
					return ($mainframe->isSite())? false: true;
					break;
				case 'locked':
					return ($mainframe->isSite())? true: false;
					break;
				case 'hidden':
					return false;
					break;
			}
			
		}
		$permissions =  array('read'  => true, 'write' => true);
		$options = array('roots' => array( array('driver' => 'LocalFileSystem', 'path' => $path, 'URL' => $url, 'accessControl' => 'access', 'defaults' => $permissions)));
		$connector = new elFinderConnector(new elFinder($options));
		$connector->run();
	}

}