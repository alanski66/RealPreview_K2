<?php
/**
 * @version		$Id: item.php 567 2010-09-23 11:50:09Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2010 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');

//JTable::addIncludePath(K2_COMPONENT_PATH_ADMIN.DS.'tables');

class JModelItem extends JModel 
{
    function getData() {

        $itemid = JRequest::getVar('itemid');
        $version = JRequest::getVar('version');
        $draft = $tmp = &JTable::getInstance('RealPreviewK2', 'Table');
        //$row = $draft->loadDraft($itemid,$version);
        if(!$draft->loadDraft($itemid,$version))
		{
			//JError::raiseError( 500, $draft->getError() );
			JError::raiseWarning( 500, $draft->getError() );
			$draft = $tmp;
		}
		
		return $draft;
		
    }

    function getAllVersions(){
        $itemid = JRequest::getInt('itemid');
        $db = &JFactory::getDBO();
		$table = RealpreviewHelper::get_table_name();
		
        $query = "SELECT id, itemid, version, flowstatus, flowstatusId, 
				published, created, created_by, checked_out,
				checked_out_time, modified, modified_by, featured				
				FROM $table 
				WHERE itemid='{$itemid}'
				ORDER BY `version` DESC";
        $db->setQuery($query);
        $rows = $db->loadObjectList();

        return $rows;
    }

    function save($front = false,$new_draft=false,$publish_draft=false) 
	{

        
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.archive');
        require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'class.upload.php');
		
		require_once(RPREVIEW_K2_ADMIN_PATH.'helpers/upload.php');
		require_once(RPREVIEW_K2_ADMIN_PATH.'helpers/copy_files.php');

//$orig_attachments = RealpreviewHelper_Copy_Files::copy_attachments(2,1);
//die('rrr');
		
		JTable::addIncludePath(K2_COMPONENT_PATH_ADMIN.DS.'tables');
		
        $mainframe = &JFactory::getApplication();
		$option = JRequest::getVar('option');
		$itemid = JRequest::getVar('itemid');
		$version = JRequest::getVar('version');
		$draft_id = JRequest::getVar('id');
		$redirect = "index.php?option={$option}&itemid={$itemid}&version={$version}";
						
        $db = &JFactory::getDBO();
        $user = &JFactory::getUser();
        $row = &JTable::getInstance('RealPreviewK2', 'Table');
        $params = &JComponentHelper::getParams('com_k2');
        $nullDate = $db->getNullDate();
		
        if (!$row->bind(JRequest::get('post'))) {
            $mainframe->redirect($redirect, $row->getError(), 'error');
        }
		
		if($new_draft)
		{
			$row->id=null;
			$row->published=0;
			$next_version = RealpreviewHelper::get_next_item_vesion($itemid);
			$row->version = $next_version;	
		}	
		
		$show_published_warning = false;
		if(!$publish_draft)
		{	
			if(!$front and (int)$row->published)
			{
				$row->published = 0;
				$show_published_warning = true;
			}
		}
		
/*
        if ($front && $row->id == NULL) {
            if (!$user->authorize('com_k2', 'add', 'category', $row->catid) && !$user->authorize('com_k2', 'add', 'category', 'all')) {
                $mainframe->redirect('index.php?option=com_k2&view=item&task=add&tmpl=component', JText::_('RPK2_CATEGORY_POST_ERROR'), 'error');
            }
        }
*/

        ($row->id) ? $isNew = false : $isNew = true;


        if ($params->get('mergeEditors')) {
            $text = JRequest::getVar('text', '', 'post', 'string', 2);
            if($params->get('xssFiltering')){
                $filter = new JFilterInput(array(), array(), 1, 1, 0);
                $text = $filter->clean( $text );
            }
            $pattern = '#<hr\s+id=("|\')system-readmore("|\')\s*\/*>#i';
            $tagPos = preg_match($pattern, $text);
            if ($tagPos == 0) {
                $row->introtext = $text;
                $row->fulltext = '';
            } else
            list($row->introtext, $row->fulltext) = preg_split($pattern, $text, 2);
        } else {
            $row->introtext = JRequest::getVar('introtext', '', 'post', 'string', 2);
            $row->fulltext = JRequest::getVar('fulltext', '', 'post', 'string', 2);
            if($params->get('xssFiltering')){
                $filter = new JFilterInput(array(), array(), 1, 1, 0);
                $row->introtext = $filter->clean( $row->introtext );
                $row->fulltext = $filter->clean( $row->fulltext );
            }
        }

//        if(!$row->id){
//           //echo'no id';
//            $row->created_by = $user->get('id');
//
//        }elseif ($row->id) {
//            $datenow = &JFactory::getDate();
//            $row->modified = $datenow->toMySQL();
//            $row->modified_by = $user->get('id');
//            //joomkit alan 2012-05-11
//            $row->created_by = $user->get('id');
//            //end
//        } 
        
        if ($row->id) {
            $datenow = &JFactory::getDate();
            $row->modified = $datenow->toMySQL();
            
            $row->modified_by = $user->get('id');
            //joomkit alan 2012-05-11
            //$row->created_by = $user->get('id');
            //end
        } 
        //var_dump($row->id);die();
		
		$row->ordering = JRequest::getInt('ordering');
        $row->featured_ordering = JRequest::getInt('featured_ordering');
        $row->featured = JRequest::getInt('featured');
        $row->flowstatus = 'draft';		

/*		
        if ($front) {
            if (!$row->id)
            $row->created_by = $user->get('id');
        } else {
            $row->created_by = $row->created_by ? $row->created_by : $user->get('id');
        }
*/
        
        
        if ($row->created && strlen(trim($row->created)) <= 10) {
            $row->created .= ' 00:00:00';
        }

        $config = &JFactory::getConfig();
        $tzoffset = $config->getValue('config.offset');
        $date = &JFactory::getDate($row->created, $tzoffset);
        $row->created = $date->toMySQL();
        
        if (strlen(trim($row->publish_up)) <= 10) {
            $row->publish_up .= ' 00:00:00';
        }

        $date = &JFactory::getDate($row->publish_up, $tzoffset);
        $row->publish_up = $date->toMySQL();

        if (trim($row->publish_down) == JText::_('RPK2_NEVER') || trim($row->publish_down) == '') {
            $row->publish_down = $nullDate;
        } else {
            if (strlen(trim($row->publish_down)) <= 10) {
                $row->publish_down .= ' 00:00:00';
            }
            $date = &JFactory::getDate($row->publish_down, $tzoffset);
            $row->publish_down = $date->toMySQL();
        }

        $metadata = JRequest::getVar('meta', null, 'post', 'array');
        if (is_array($metadata)) {
            $txt = array();
            foreach ($metadata as $k=>$v) {
                if ($k == 'description') {
                    $row->metadesc = $v;
                } elseif ($k == 'keywords') {
                    $row->metakey = $v;
                } else {
                    $txt[] = "$k=$v";
                }
            }
            $row->metadata = implode("\n", $txt);
        }


        if (!$row->check()) {
            $mainframe->redirect($redirect, $row->getError(), 'error');
        }

        $dispatcher = &JDispatcher::getInstance();
/*		
        JPluginHelper::importPlugin('k2');
        $result = $dispatcher->trigger('onBeforeK2Save', array(&$row, $isNew));
        if (in_array(false, $result, true)) {
            JError::raiseError(500, $row->getError());
            return false;
        }
*/
        if (version_compare(phpversion(), '5.0') < 0) {
            $tmpRow = $row;
        }
        else {
            $tmpRow = clone($row);
        }

        if (!$row->store()) {
            $mainframe->redirect($redirect, $row->getError(), 'error');
        }
		
		if($show_published_warning)
		{
			$publish_warning = JText::_('RPK2_PUBLISHED_ITEM_SAVED_WARNING');
			$mainframe->enqueueMessage($publish_warning,'notice' );
		}

        $files = JRequest::get('files');
		
		$orig_attachments = array();
		if($new_draft)
		{
			$current_draft_id = $row->id;
			$old_draft_id = $draft_id;
						
			//RealpreviewHelper_Copy_Files::copy_images($current_draft_id,$old_draft_id);
			//RealpreviewHelper_Copy_Files::copy_gallery($current_draft_id,$old_draft_id);
			//RealpreviewHelper_Copy_Files::copy_video($current_draft_id,$old_draft_id);

			$existingImage = JRequest::getVar('existingImage');
			if(!(($files['image']['error'] === 0 || $existingImage) || JRequest::getBool('del_image')))
				RealpreviewHelper_Copy_Files::copy_images($current_draft_id,$old_draft_id);
			
			if(!((isset($files['video']) && $files['video']['error'] == 0) || JRequest::getBool('del_video')))
				RealpreviewHelper_Copy_Files::copy_video($current_draft_id,$old_draft_id);
				
			if(!((isset($files['gallery']) && $files['gallery']['error'] == 0) || JRequest::getBool('del_gallery'))) 
				RealpreviewHelper_Copy_Files::copy_gallery($current_draft_id,$old_draft_id);

			//Attachments
			$orig_attachments = RealpreviewHelper_Copy_Files::copy_attachments($current_draft_id,$old_draft_id);
			$redirect = "index.php?option={$option}&itemid={$itemid}&version={$row->version}";
		}
		else
		{
			$orig_attachments = RealpreviewHelper::get_item_attachments($draft_id);
		}
		
/*
echo "<br>orig_attachments-";
print_r($orig_attachments);
die('end attachments');
//*/	

        RealpreviewHelper_Upload::upload_images(&$row,$files,$redirect);
        RealpreviewHelper_Upload::upload_gallery(&$row,$files,$redirect);
		RealpreviewHelper_Upload::upload_video(&$row,$files,$redirect);

		//Attachments
        $new_attachments = RealpreviewHelper_Upload::upload_attachments(&$row,$files,$redirect);
/*		
echo "<br>new_attachments-";
print_r($new_attachments);
//*/	
		$tmp_attachment = array();
		if(is_array($orig_attachments) and !empty($orig_attachments))
		{
			if(is_array($new_attachments) and !empty($new_attachments))
			{
				$new_attachments = array_merge($new_attachments,$orig_attachments);
				
				$counter=1;				
				foreach($new_attachments as $attachment)
				{
					if(is_object($attachment))
					{
						$attachment->id = $counter++;
					}
					else	
					{	
						$attachment['id'] = $counter++;
					}	
					$tmp_attachment[] = $attachment;
				}
			}
			else
				$tmp_attachment = $orig_attachments;
				
			$row->attachments = RealpreviewHelper::json_encode($tmp_attachment);
			
/*
echo "<br>tmp_attachment-";
print_r($tmp_attachment);			
echo "<br>attachments-";
print_r($row->attachments);					
//die();
//*/				
		}
		
		//Extra fields
        $objects = array();
        $variables = JRequest::get('post', 4);
        foreach ($variables as $key=>$value) {
            if (( bool )JString::stristr($key, 'K2ExtraField_')) {
                $object = new JObject;
                $object->set('id', JString::substr($key, 13));
                $object->set('value', $value);
                unset($object->_errors);
                $objects[] = $object;
            }
        }

        $csvFiles = JRequest::get('files');
        foreach ($csvFiles as $key=>$file) {
            if (( bool )JString::stristr($key, 'K2ExtraField_')) {
                $object = new JObject;
                $object->set('id', JString::substr($key, 13));
                $csvFile = $file['tmp_name'][0];
                if(!empty($csvFile) && JFile::getExt($file['name'][0])=='csv'){
                    $handle = @fopen($csvFile, 'r');
                    $csvData=array();
                    while (($data = fgetcsv($handle, 1000)) !== FALSE) {
                        $csvData[]=$data;
                    }
                    fclose($handle);
                    $object->set('value', $csvData);
                }
                else {
                    //require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
                    
					$script_file = RealpreviewHelper::get_k2_file('JSON.php','lib');
					if($script_file)require_once($script_file);
					else
						require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
					
					$json = new Services_JSON;
                    $object->set('value', $json->decode(JRequest::getVar('K2CSV_'.$object->id)));
                    if(JRequest::getBool('K2ResetCSV_'.$object->id))
                    $object->set('value', null);
                }
                unset($object->_errors);
                $objects[] = $object;
            }
        }

        //require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
		$script_file = RealpreviewHelper::get_k2_file('JSON.php','lib');
		if($script_file)require_once($script_file);
		else
			require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
			
        $json = new Services_JSON;
        $row->extra_fields = $json->encode($objects);

        require_once (K2_COMPONENT_PATH_ADMIN.DS.'models'.DS.'extrafield.php');
        $extraFieldModel = new K2ModelExtraField;
        $row->extra_fields_search = '';

        foreach ($objects as $object) {
            $row->extra_fields_search .= $extraFieldModel->getSearchValue($object->id, $object->value);
            $row->extra_fields_search .= ' ';
        }

        //Tags
        if($user->gid<24 && $params->get('lockTags'))
			$params->set('taggingSystem',0);
        
		$tag_ids = array();
        if($params->get('taggingSystem')){

            if($user->gid<24 && $params->get('lockTags'))
            JError::raiseError(403, JText::_("RPK2_ALERTNOTAUTH"));

            $tags = JRequest::getVar('tags', NULL, 'POST', 'array');
            if (count($tags)) {
                $tags = array_unique($tags);
                foreach ($tags as $tag) 
				{
                    $tag = str_replace('-','',$tag);
                    $query = "SELECT id FROM #__k2_tags WHERE name=".$db->Quote($tag);
                    $db->setQuery($query);
                    $tagID = $db->loadResult();
					
					if(!(int)$tagID)
					{
                        $K2Tag = &JTable::getInstance('K2Tag', 'Table');
                        $K2Tag->name = $tag;
                        $K2Tag->published = 1;
                        $K2Tag->check();
                        $K2Tag->store();
						
						$tag_ids[] = $K2Tag->id;
					}	
					else
						$tag_ids[] = $tagID;
                }
            }

        }
        else 
		{
            $tags = JRequest::getVar('selectedTags', NULL, 'POST', 'array');
            if (count($tags)) {
                foreach ($tags as $tagID) {
					if(intval($tagID))
						$tag_ids[] = intval($tagID);
                }
            }

        }		
		if(is_array($tag_ids))
		{
			$tag_ids = array_filter($tag_ids);
			$row->tags = implode(',',$tag_ids);
		}
		
        if ($front) {
            if (!K2HelperPermissions::canPublishItem($row->catid) && $row->published == 1) {
                $row->published = 0;
                $mainframe->enqueueMessage(JText::_("RPK2_PUBLISH_PERMISSION_ERROR"), 'notice');
            }
        }

        if (!$row->store()) {
            $mainframe->redirect($redirect, $row->getError(), 'error');
        }

        $row->checkin();

        $cache = &JFactory::getCache('com_k2');
        $cache->clean();
	
        
       
		if($publish_draft)
			return true;
			
                $msg = JText::_('RPK2_DRAFT_SAVED');
		
		if($new_draft)
			$msg = JText::_('RPK2_DRAFT_CREATED');
		
		if(JRequest::getVar('task') == 'saveandclose')
		{			
			$redirect = "index.php?option=com_k2&view=items";
			//$redirect .= "&filter_featured=-1&filter_trash=0";
		}
                      
			              
			
		$mainframe->redirect($redirect, $msg);
    }
	
	function publishdraft()
	{
        $this->save(false,false,true);
		
		//require_once(JPATH_COMPONENT.DS.'helpers/restore.php');
        require_once(RPREVIEW_K2_ADMIN_PATH.'helpers/restore.php');
		
		JTable::addIncludePath(K2_COMPONENT_PATH_ADMIN.DS.'tables');
		
		$mainframe = &JFactory::getApplication();
		$option = JRequest::getVar('option');
		$itemid = JRequest::getVar('itemid');
		$version = JRequest::getVar('version');
		$draft_id = JRequest::getVar('id');
		$redirect = "index.php?option={$option}&itemid={$itemid}&version={$version}";
		
                $table = RealpreviewHelper::get_table_name();
		
		$db	= & JFactory::getDBO();
		$query = "SELECT * FROM $table WHERE id='$draft_id'";
		$db->setQuery($query);
		$draft = $db->loadAssoc();
		
		if(!is_array($draft))
		{
			$mainframe->redirect($redirect,$draft->stderr(),'error');
		}	
		elseif(empty($draft))
			$mainframe->redirect($redirect,JText::_('RPK2_ERROR_DRAFT_NOT_FOUND'),'error');
			
		//remove draft publish state
		unset($draft['published']);
		unset($draft['created']);
		unset($draft['id']);
		
		$item =& JTable::getInstance('K2Item', 'Table');
		if (!$item->bind($draft)) {
			$mainframe->redirect($redirect,$item->getError(),'error');
		}
		
		$draft['id']=$draft_id;
		$item->id=$draft['itemid'];
		$item->published=1;
		
		$item->check();
		
		if(!$item->store())
		{
			$mainframe->redirect($redirect,$item->getError(),'error');
		}
		
		if((int)$draft['featured'])
		{
			$params = &JComponentHelper::getParams('com_k2');
			if(!$params->get('disableCompactOrdering'))
				$item->reorder("featured = 1 AND trash = 0", 'featured_ordering');
		}
		
		$query = "UPDATE $table SET `published`=0 WHERE itemid='$itemid'";
		$db->setQuery($query);
		$db->query();
		$query = "UPDATE $table SET `published` = 1 WHERE id='$draft_id'";
		$db->setQuery($query);
		$db->query();
		
		RealpreviewHelper_Restore::restore_images($draft);
		RealpreviewHelper_Restore::restore_attachments($draft);
		RealpreviewHelper_Restore::restore_gallery($draft);
		RealpreviewHelper_Restore::restore_video($draft);
		RealpreviewHelper_Restore::restore_tags($draft);
                
                //joomkit alan
				
                     //get config for return to items on publish
                      $RPparams = &JComponentHelper::getParams( 'com_realpreview_k2' );
                      var_dump($RPparams->get('publishreturn'));
                      
                      if($RPparams->get('publishreturn') == "1"):
                        //joomkit
                            $redirect = "index.php?option=com_k2&view=items";
  
                      elseif($RPparams->get('publishreturn') == "0"):
                        
                          $redirect = "index.php?option={$option}&itemid={$itemid}&version={$version}";
                            $msg = JText::_('RPK2_DRAFT_PUBLISHED');
                             
                      endif;
                     
     
		$mainframe->redirect($redirect,JText::_('RPK2_DRAFT_PUBLISHED'));
		
	}

	function deletedraft()
	{
        //require_once(JPATH_COMPONENT.DS.'helpers/delete.php');
        require_once(RPREVIEW_K2_ADMIN_PATH.'helpers/delete.php');
		
		JTable::addIncludePath(K2_COMPONENT_PATH_ADMIN.DS.'tables');
		
		$mainframe = &JFactory::getApplication();
		$option = JRequest::getVar('option');
		$itemid = JRequest::getVar('itemid');
		$version = JRequest::getVar('version');
		$return_version = JRequest::getVar('return_version');
		$draft_id = JRequest::getVar('id');
		//$redirect = "index.php?option={$option}&itemid={$itemid}&version={$version}";
		$redirect = "index.php?option={$option}&itemid={$itemid}&version={$return_version}";
		$table = RealpreviewHelper::get_table_name();
		
		$db	= & JFactory::getDBO();
		$query = "SELECT * FROM $table WHERE id='$draft_id'";
		$db->setQuery($query);
		$draft = $db->loadAssoc();
		
		if(!is_array($draft))
		{
			$mainframe->redirect($redirect,$draft->stderr(),'error');
		}		
		elseif(empty($draft))
			$mainframe->redirect($redirect,JText::_('RPK2_ERROR_DRAFT_NOT_FOUND'),'error');
			
		RealpreviewHelper_Delete::delete_images($draft);
		RealpreviewHelper_Delete::delete_attachments($draft);
		RealpreviewHelper_Delete::delete_gallery($draft);
		RealpreviewHelper_Delete::delete_video($draft);
		
		$query = "DELETE FROM $table WHERE id='$draft_id'";
		$db->setQuery($query);
		if($db->query())
		{
			$msg = JText::_('RPK2_DRAFT_DELETED');
		}
		else
		{
			$msg = JText::_('RPK2_ERROR_DELETING_DRAFT').$db->getErrorMsg();
			$mainframe->redirect($redirect,$msg,'error');
		}
		
		$mainframe->redirect($redirect,$msg);
		
	}


    function cancel() {

        $mainframe = &JFactory::getApplication();
        $cid = JRequest::getInt('id');
        $row = &JTable::getInstance('RealPreviewK2', 'Table');
        $row->load($cid);
        $row->checkin();
        $mainframe->redirect('index.php?option=com_k2&view=items');
    }

    function getVideoProviders() {

        jimport('joomla.filesystem.file');
        if (JFile::exists(JPATH_PLUGINS.DS.'content'.DS.'jw_allvideos'.DS.'includes'.DS.'sources.php')) {
            require JPATH_PLUGINS.DS.'content'.DS.'jw_allvideos'.DS.'includes'.DS.'sources.php';
            $thirdPartyProviders = array_slice($tagReplace, 18);
            $providersTmp = array_keys($thirdPartyProviders);
            $providers = array();
            foreach ($providersTmp as $providerTmp) {

                if (stristr($providerTmp, 'google|google.co.uk|google.com.au|google.de|google.es|google.fr|google.it|google.nl|google.pl') !== false) {
                    $provider = 'google';
                } elseif (stristr($providerTmp, 'spike|ifilm') !== false) {
                    $provider = 'spike';
                } else {
                    $provider = $providerTmp;
                }
                $providers[] = $provider;
            }
            return $providers;
        } else {
            return;
        }

    }
    
    function getAttachments($attachments) 
	{
		$attachments = trim($attachments);
        if($attachments!='')
		{
			$list = RealpreviewHelper::parse_attachment_field($attachments);
		}
		else
			$list = array();

/*
echo "<br>attachments=$attachments<br>list=";
print_r($list);
die();
//*/			
		//return $list;
		
		if(!empty($list))
		{
			$object_list = array();
			foreach($list as $array)
			{
				$obj = new stdClass();
				if(is_array($array))
				{
					foreach ($array as $key => $val) {
						$obj->$key = is_array($val) ? toObject($val) : $val;
					}
				}
				else
					$obj = $array;
				$object_list[] = $obj;
			}	
			
			return $object_list;

		}
		return $list;
    }
	
	function download($front = false) 
	{
        $mainframe = &JFactory::getApplication();
		
        jimport('joomla.filesystem.file');
        $params = &JComponentHelper::getParams('com_k2');
        $id = JRequest::getInt('id');
        $fileid = JRequest::getVar('fileid');
		
		if($front)
		{
			//$fileid = $id;
			$draft_id = JRequest::getInt('draft_id');
			if($draft_id)
			{	
				$item_id=$id;
				$id=$draft_id;	
			}	
		}	
		
		$filename='';

		if(!($id or $fileid))
		{
			if($front)
			{
				exit();
				//index.php?option=com_k2&view=item&layout=item&id=4&version=2
				$version = JRequest::getVar('version');
				$Itemid = JRequest::getVar('Itemid');
				$redirect = 'index.php?option=com_k2&view=item&layout=item';
				$redirect .="&id=$item_id&version=$version&Itemid=$Itemid";
				$mainframe->redirect($redirect);
				return;
			}
			$mainframe->close();
		}
		
        JPluginHelper::importPlugin('k2');
        $dispatcher = &JDispatcher::getInstance();

		$attachments = RealpreviewHelper::get_item_attachments($id);
		
//print_r($attachments);
//die();
		
		if(is_array($attachments) and !empty($attachments))
		{
			$attachment_path = RPREVIEW_K2_MEDIA_PATH.'attachments';
			$warning_msg = JText::_('RPK2_ERROR_DELETING_ATTACHMENT');
			$show_delete_error=$show_delete_info=true;
		
			foreach($attachments as $k=>$attachment)
			{
				if(is_object($attachment))
				{
					$attachment_id = $attachment->id;
					$attachment_file = $attachment->filename;
				}
				else
				{
					$attachment_id = $attachment['id'];
					$attachment_file = $attachment['filename'];
				}	
				if($attachment_id==$fileid)
				{
					$filename = $attachment_file;
					break;
				}
			}
			
			
		}
		
		if(!$filename)
		{
			$mainframe->close();
		}
		
        //$dispatcher->trigger('onK2BeforeDownload',  array(&$attachment, &$params));
		
		$savepath = RPREVIEW_K2_MEDIA_PATH.'attachments/';
        $file = $savepath.$filename;

        if (JFile::exists($file)) {
            require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'class.upload.php');
            $handle = new Upload($file);
            
            $len = filesize($file);
            $filename = basename($file);
//*			
            ob_clean();	
			JResponse::clearHeaders();
            JResponse::setHeader('Pragma', 'public', true);
            JResponse::setHeader('Expires', '0', true);
            JResponse::setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true);
            JResponse::setHeader('Content-Type', $handle->file_src_mime, true);
            JResponse::setHeader('Content-Disposition', 'attachment; filename='.$filename.';', true);
            JResponse::setHeader('Content-Transfer-Encoding', 'binary', true);
            JResponse::setHeader('Content-Length', $len, true);
            JResponse::sendHeaders();
           	echo JFile::read($file);
					
	
        } else {
            echo JText::_('RPK2_FILE_DOES_NOT_EXIST');
        }
        $mainframe->close();
    }
	
    function deleteAttachment() 
	{
//index.php?option=com_realpreview_k2&id=3&itemid=1&version=3&task=deleteAttachment&fileid=1

        $mainframe = &JFactory::getApplication();
        $params = &JComponentHelper::getParams('com_k2');
        jimport('joomla.filesystem.file');
        $id = JRequest::getInt('id');
        $fileid = JRequest::getVar('fileid');
        $file_title = JRequest::getVar('file_title');
        $itemid = JRequest::getInt('cid');
		$table = RealpreviewHelper::get_table_name();
		$reset_attachment = false;
		
		if(!$file_title and !$fileid)
		{
			$mainframe->close();
			return;
		}
		
		if($file_title)$file_title = urldecode($file_title);
		        
		$attachments = RealpreviewHelper::get_item_attachments($id);
		
		if(is_array($attachments) and !empty($attachments))
		{
			$attachment_path = RPREVIEW_K2_MEDIA_PATH.'attachments';
			$warning_msg = JText::_('RPK2_ERROR_DELETING_ATTACHMENT');
			$show_delete_error=$show_delete_info=true;
		
			foreach($attachments as $k=>$attachment)
			{
				if(is_object($attachment))
				{
					$attachment_id = $attachment->id;
					$attachment_file = $attachment->filename;
				}
				else
				{
					$attachment_id = $attachment['id'];
					$attachment_file = $attachment['filename'];
				}
				
				$is_file = false;
				if($fileid)
				{
					if($attachment_id==$fileid)
						$is_file=true;
				}
				elseif($file_title)
				{
					if($file_title == $attachment_file)
						$is_file=true;
				}
				if($is_file)
				{
					$filename = $attachment_file;
					$file_path = $attachment_path.DS.$filename;
					if (JFile::exists($file_path)) 
					{	
						if(!JFile::delete($file_path))
						{
							if($show_delete_error)
							{
								$mainframe->enqueueMessage($warning_msg.$file_path,'notice');
							}	
						}
						else
						{
							if($show_delete_info)
							{
								$mainframe->enqueueMessage('deleted attachment-'.$file_path);
							}
						}
						
					}
					
					$reset_attachment = true;
					unset($attachments[$k]);
					
					break;
				}
			}
			
			if($reset_attachment)
			{
				@reset($attachments);
				$counter=1;
/*
				foreach($attachments as $k=>$attachment)
				{
					if(is_object($attachment))
						$attachments[$k]->id = $counter++;
					else	
						$attachments[$k]['id'] = $counter++;
				}
*/				
				
				$db = &JFactory::getDBO();
				$attachments = array_merge(array(),$attachments);
				//$json_str = @json_encode($attachments);
				$json_str = RealpreviewHelper::json_encode($attachments);
				$json_str = $db->Quote($json_str);
				
				$db = &JFactory::getDBO();
				$q = "UPDATE  $table 
						SET `attachments`= $json_str
						WHERE id='$id'";
				$db->setQuery($q);
				$db->query();
			}	
		}
		
        $mainframe->close();
    }
	
    function getAvailableTags($tags = NULL) {

        $db = &JFactory::getDBO();
        $query = "SELECT * FROM #__k2_tags as tags";
        if (trim($tags)!='')
			$query .= " WHERE tags.id NOT IN ($tags)";
        $db->setQuery($query);
        $rows = $db->loadObjectList();
		
		if(!is_array($rows))$rows=array();
        return $rows;
    }

    function getCurrentTags($tags='') 
	{
		if(trim($tags)!='')
		{			
			$db = &JFactory::getDBO();
			$query = "SELECT * FROM #__k2_tags as tags 
						WHERE tags.id IN ($tags)";
			$db->setQuery($query);
			$rows = $db->loadObjectList();
			
			if(!is_array($rows))$rows=array();
		}
		else
			$rows = array();
			
        return $rows;
    }

    function resetHits()
	{
        $mainframe = &JFactory::getApplication();
        $id = JRequest::getInt('id');
        $itemid = JRequest::getInt('itemid');
        $version = JRequest::getInt('version');
		$option = JRequest::getVar('option');
        $db = &JFactory::getDBO();
		$table = RealpreviewHelper::get_table_name();
        $query = "UPDATE $table SET hits=0 WHERE id={$id}";
        $db->setQuery($query);
        $db->query();
/*        
		if($mainframe->isAdmin())
			$url = "index.php?option={$option}&view=item&cid={$id}";
        else
			$url = 'index.php?option={$option}&view=item&task=edit&cid='.$id.'&tmpl=component';
*/        
		$url = "index.php?option={$option}&view=item&itemid={$itemid}&version={$version}";
		$mainframe->redirect($url, JText::_('RPK2_SUCCESSFULLY_RESET_ITEM_HITS' ));
    }

    function resetRating(){
        $mainframe = &JFactory::getApplication();
        $id = JRequest::getInt('id');
        $itemid = JRequest::getInt('itemid');
        $version = JRequest::getInt('version');
        $db = &JFactory::getDBO();
        $query = "DELETE FROM #__k2_rating WHERE itemID={$id}";
        $db->setQuery($query);
        //$db->query();
/*        
		if($mainframe->isAdmin())
        $url = 'index.php?option=com_k2&view=item&cid='.$id;
        else
        $url = 'index.php?option=com_k2&view=item&task=edit&cid='.$id.'&tmpl=component';
*/        
		$url = "index.php?option={$option}&view=item&itemid={$itemid}&version={$version}";
		$mainframe->redirect($url, JText::_('RPK2_SUCCESSFULLY_RESET_ITEM_RATING'));
    }

    function getRating(){
        $id = JRequest::getInt('cid');
        $db = &JFactory::getDBO();
        $query = "SELECT * FROM #__k2_rating WHERE itemID={$id}";
        $db->setQuery($query, 0, 1);
        $row = $db->loadObject();
        return $row;
    }
	
    function checkSIG_v1() {
        $mainframe = &JFactory::getApplication();
        if ( JFile::exists(JPATH_PLUGINS.DS.'content'.DS.'jw_sigpro.php') ) {
            return true;
        } else {
            return false;
        }
    }

    function checkAllVideos_v1() {
        $mainframe = &JFactory::getApplication();
        if (JFile::exists(JPATH_PLUGINS.DS.'content'.DS.'jw_allvideos.php')) {
            return true;
        } else {
            return false;
        }
    }
	
	function checkSIG() {
		$mainframe = &JFactory::getApplication();
		//if(K2_JVERSION == '16') {
		if(RealpreviewHelper::is_j16()) {
			$check = JPATH_PLUGINS.DS.'content'.DS.'jw_sigpro'.DS.'jw_sigpro.php';
		}
		else {
			$check = JPATH_PLUGINS.DS.'content'.DS.'jw_sigpro.php';
		}
		if (JFile::exists($check)) {
			return true;
		} else {
			return false;
		}
	}

	function checkAllVideos() {
		$mainframe = &JFactory::getApplication();
		//if(K2_JVERSION == '16') {
		if(RealpreviewHelper::is_j16()) {
			$check = JPATH_PLUGINS.DS.'content'.DS.'jw_allvideos'.DS.'jw_allvideos.php';
		}
		else {
			$check = JPATH_PLUGINS.DS.'content'.DS.'jw_allvideos.php';
		}
		if (JFile::exists($check)) {
			return true;
		} else {
			return false;
		}
	}

	function publish() 
	{
        jimport('joomla.filesystem.file');
        jimport('joomla.filesystem.archive');
		JTable::addIncludePath(K2_COMPONENT_PATH_ADMIN.DS.'tables');
		
        require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'class.upload.php');
		//require_once(JPATH_COMPONENT.DS.'helpers/restore.php');
		require_once(RPREVIEW_K2_ADMIN_PATH.'helpers/restore.php');
				
        $mainframe = &JFactory::getApplication();
		$option = JRequest::getVar('option');
		$itemid = JRequest::getVar('itemid');
		$version = JRequest::getVar('version');
		$draft_id = JRequest::getVar('id');
		$redirect = "index.php?option={$option}&itemid={$itemid}&version={$version}";
		
        $db = &JFactory::getDBO();
        $user = &JFactory::getUser();
        $row = &JTable::getInstance('RealPreviewK2', 'Table');
        $params = &JComponentHelper::getParams('com_k2');
        $nullDate = $db->getNullDate();
	}

}
