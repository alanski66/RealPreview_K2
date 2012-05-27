<?php 
defined('_JEXEC') or die('Restricted access'); 

JPlugin::loadLanguage('com_k2',JPATH_ADMINISTRATOR);
JTable::addIncludePath(JPATH_COMPONENT.DS.'tables');

require_once(JPATH_COMPONENT.DS.'controller.php');
require_once(JPATH_COMPONENT.DS.'helpers/helpers.php');

RealpreviewHelper::set_debug_variables();

$task = JRequest::getCmd('task', 'display');
$controller = JRequest::getCmd('view', 'item');

$controllerPath = JPATH_COMPONENT.DS.'controllers'.DS.$controller.'.php';
if(file_exists($controllerPath))
{
	require_once($controllerPath);
}
else
{
	JError::raiseError(500, 'Invalid Controller');
}

$controllerClass = 'JController'.ucfirst($controller);

if(class_exists($controllerClass))
{
	$controller = &new $controllerClass();
}
else
{
	JError::raiseError(500, 'Invalid Controller Class');
}

$controller->execute($task);

if($task != 'display')
{
	$controller->redirect();
}	

?>