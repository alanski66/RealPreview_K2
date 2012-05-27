<?php
/**
 * @version		$Id: extrafield.php 536 2010-08-04 11:56:59Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2010 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

//require_once(K2_COMPONENT_PATH_ADMIN.DS.'models'.DS.'extrafield.php');

jimport('joomla.application.component.model');

class JModelExtraField extends JModel
{

	function getData() {

		$cid = JRequest::getVar('cid');
		$row = & JTable::getInstance('K2ExtraField', 'Table');
		$row->load($cid);
		return $row;
	}

	
	function getExtraFieldsByGroup($group)
	{
		//return K2ModelExtraField::getExtraFieldsByGroup($group);		
		$db = & JFactory::getDBO();
		$group = (int)$group;
		$query = "SELECT * FROM #__k2_extra_fields WHERE `group`={$group} AND published=1 ORDER BY ordering";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		return $rows;
	}

	function renderExtraField($extraField,$draftid=NULL){

		$mainframe = &JFactory::getApplication();
		
		$script_file = RealpreviewHelper::get_k2_file('JSON.php','lib');
		if($script_file)require_once($script_file);
		//require_once(K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
		
		$json=new Services_JSON;

		if (!is_null($draftid)){
			$item = & JTable::getInstance('RealPreviewK2', 'Table');
			$item->load($draftid);
		}

		$defaultValues=$json->decode($extraField->value);

		foreach ($defaultValues as $value){
			if ($extraField->type=='textfield' || $extraField->type=='csv' || $extraField->type=='labels' || $extraField->type=='date')
				$active=$value->value;
			else if ($extraField->type=='textarea'){
				$active[0]=$value->value;
				$active[1]=$value->editor;
			}
			else if($extraField->type=='link'){
				$active[0]=$value->name;
				$active[1]=$value->value;
				$active[2]=$value->target;
			}
			else
				$active='';
		}

		if (isset($item)){
			$currentValues=$json->decode($item->extra_fields);
			if (count($currentValues)){
				foreach ($currentValues as $value){
					if ($value->id==$extraField->id){
						if($extraField->type=='textarea'){
							$active[0]=$value->value;
						}
						else if($extraField->type=='date') {
							$active = (is_array($value->value))? $value->value[0]:$value->value;
						}
						else{
							$active = $value->value;
						}
					}
				}
			}

		}

		switch ($extraField->type){

			case 'textfield':
			$output='<input type="text" name="K2ExtraField_'.$extraField->id.'" value="'.$active.'"/>';
			break;

			case 'labels':
			$output='<input type="text" name="K2ExtraField_'.$extraField->id.'" value="'.$active.'"/> '.JText::_('K2_COMMA_SEPARATED_VALUES');
			break;			
			
			case 'textarea':
			if($active[1]){
				$output='<textarea name="K2ExtraField_'.$extraField->id.'" id="K2ExtraField_'.$extraField->id.'" rows="10" cols="40" class="k2ExtraFieldEditor">'.$active[0].'</textarea>';
			}
			else{
				$output='<textarea name="K2ExtraField_'.$extraField->id.'" rows="10" cols="40">'.$active[0].'</textarea>';
			}

			break;

			case 'select':
			$output=JHTML::_('select.genericlist', $defaultValues, 'K2ExtraField_'.$extraField->id, '', 'value', 'name',$active);
			break;

			case 'multipleSelect':
			$output=JHTML::_('select.genericlist', $defaultValues, 'K2ExtraField_'.$extraField->id.'[]', 'multiple="multiple"', 'value', 'name',$active);
			break;

			case 'radio':
			$output=JHTML::_('select.radiolist', $defaultValues, 'K2ExtraField_'.$extraField->id, '', 'value', 'name',$active);
			break;

			case 'link':
			$output='<label>'.JText::_('K2_TEXT').'</label>';
			$output.='<input type="text" name="K2ExtraField_'.$extraField->id.'[]" value="'.$active[0].'"/>';
			$output.='<label>'.JText::_('K2_URL').'</label>';
			$output.='<input type="text" name="K2ExtraField_'.$extraField->id.'[]" value="'.$active[1].'"/>';
			$output.='<label for="K2ExtraField_'.$extraField->id.'">'.JText::_('K2_OPEN_IN').'</label>';
			$targetOptions[]=JHTML::_('select.option', 'same', JText::_('K2_SAME_WINDOW'));
			$targetOptions[]=JHTML::_('select.option', 'new', JText::_('K2_NEW_WINDOW'));
			$targetOptions[]=JHTML::_('select.option', 'popup', JText::_('K2_CLASSIC_JAVASCRIPT_POPUP'));
			$targetOptions[]=JHTML::_('select.option', 'lightbox', JText::_('K2_LIGHTBOX_POPUP'));
			$output.=JHTML::_('select.genericlist', $targetOptions, 'K2ExtraField_'.$extraField->id.'[]', '', 'value', 'text', $active[2]);
			break;

			case 'csv':
				$output = '<input type="file" name="K2ExtraField_'.$extraField->id.'[]"/>';

				if(is_array($active) && count($active)){
					$output.= '<input type="hidden" name="K2CSV_'.$extraField->id.'" value="'.htmlspecialchars($json->encode($active)).'"/>';
					$output.='<table class="csvTable">';
					foreach($active as $key=>$row){
						$output.='<tr>';
						foreach($row as $cell){
							$output.=($key>0)?'<td>'.$cell.'</td>':'<th>'.$cell.'</th>';
						}
						$output.='</tr>';
					}
					$output.='</table>';
					$output.='<label>'.JText::_('K2_DELETE_CSV_DATA').'</label>';
					$output.='<input type="checkbox" name="K2ResetCSV_'.$extraField->id.'"/>';
				}
			break;
			
			case 'date':
			$output = JHTML::_('calendar', $active, 'K2ExtraField_'.$extraField->id, 'K2ExtraField_'.$extraField->id);
			break;

		}

		return $output;

	}

	function getExtraFieldInfo($fieldID){

		$db = & JFactory::getDBO ();
		$fieldID = (int) $fieldID;
		$query="SELECT * FROM #__k2_extra_fields WHERE published=1 AND fieldID = ".$fieldID;
		$db->setQuery ($query,0,1);
		$row = $db->loadObject ();
		return $row;
	}

	function getSearchValue($id, $currentValue){

		$row = & JTable::getInstance('K2ExtraField', 'Table');
		$row->load($id);

		require_once(K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
		$json=new Services_JSON;
		$jsonObject=$json->decode($row->value);

		$value='';
		if ( $row->type=='textfield'|| $row->type=='textarea' || $row->type=='labels'){
			$value=$currentValue;
		}
		else if ($row->type=='multipleSelect'){
			foreach ($jsonObject as $option){
				if (in_array($option->value,$currentValue))
				$value.=$option->name.' ';
			}
		}
		else if ($row->type=='link'){
			$value.=$currentValue[0].' ';
			$value.=$currentValue[1].' ';
		}
		else {
			foreach ($jsonObject as $option){
				if ($option->value==$currentValue)
				$value.=$option->name;
			}
		}
		return $value;
	}

}
