<?php
if(!defined('_JEXEC'))
	
defined( '_JEXEC' ) or die( 'Restricted access' );	
	
$component_path = JPATH_COMPONENT.'/';
require_once($component_path.'controller.php');

$task = JRequest::getCmd('task', 'download');
$controller = &new JControllerMain();
$controller->execute($task);

?>