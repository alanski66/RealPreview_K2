<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
 
jimport( 'joomla.application.component.controller' );

class JControllerMain extends JController
{
	function download(){

		$helpers_file = JPATH_COMPONENT_ADMINISTRATOR.DS.'helpers'.DS.'helpers.php';
		$item_model_file = JPATH_COMPONENT_ADMINISTRATOR.DS.'models'.DS.'item.php';
		
		if(!file_exists($helpers_file))return;
		
		require_once($helpers_file);
		require_once($item_model_file);
		$model= new JModelItem;
		$model->download(true);
	}
	 
} ?> 