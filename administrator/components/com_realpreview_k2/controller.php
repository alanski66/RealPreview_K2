<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
 
jimport( 'joomla.application.component.controller' );

class JControllerBase extends JController
{

    function getViewName() { JError::raiseError(500,"getViewName() not implemented"); } /* abstract */
	function getModelName() { JError::raiseError(500,"getModelName() not implemented"); } /* abstract */
	function getLayoutName() { return 'default'; }
	
    function display()
    {     
		
		$doc = &JFactory::getDocument();
		$viewType = $doc->getType();
        $view = &$this->getView( ucfirst($this->getViewName()), $viewType);
 
        if ($model = &$this->getModel($this->getModelName()))
        {
            $view->setModel($model, true);
        }
 
        $view->setLayout($this->getLayoutName());
        $view->display();
    }
	
} ?> 