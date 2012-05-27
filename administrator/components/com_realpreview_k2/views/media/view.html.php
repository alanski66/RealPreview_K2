<?php
/**
 * @version		$Id: view.html.php 1113 2011-10-11 14:39:02Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JViewMedia extends JView {

	function display($tpl = null) 
	{
		$mainframe = &JFactory::getApplication();
		$user = &JFactory::getUser();
		$document = &JFactory::getDocument();		
		
		$assets_dir = JURI::root(true);
		$assets_dir .= '/administrator/components/com_realpreview_k2/assets/';
		$js_path = $assets_dir.'js/';
		$script_file = $js_path.'jquery-1.7.1.js';
		$document->addScript($script_file);
		$script_file = $js_path.'jquery-ui-1.8.16.min.js';
		$document->addScript($script_file);
		$script_file = RealpreviewHelper::get_k2_file('k2.js');
		if($script_file)$document->addScript($script_file);
		
		$script_file = $assets_dir.'css/jqueryui/smoothness/jquery-ui-1.8.16.css';
		$document->addStyleSheet($script_file);
		
		if($script_file)$document->addStyleSheet($script_file);
		$script_file = RealpreviewHelper::get_k2_file('elfinder.full.css');
		if($script_file)$document->addStyleSheet($script_file);
		$script_file = RealpreviewHelper::get_k2_file('elfinder.min.js');
		if($script_file)$document->addScript($script_file);
		$script_file = RealpreviewHelper::get_k2_file('theme.css');
		if($script_file)$document->addScript($script_file);
		
		$type = JRequest::getCmd('type');
		$fieldID = JRequest::getCmd('fieldID');
		if($type=='video'){
			$mimes = "'video','audio'";
		}
		elseif ($type == 'image'){
			$mimes = "'image'";
		}
		else {
			$mimes = '';
		}
		$this->assignRef('mimes', $mimes);
		$this->assignRef('type', $type);
		$this->assignRef('fieldID', $fieldID);
		
		$this->loadHelper('html');
		
		parent::display($tpl);

	}

}
