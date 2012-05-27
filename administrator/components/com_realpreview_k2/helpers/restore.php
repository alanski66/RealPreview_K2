<?php
/**
 * @version     0.1 - Real Preview
 * @package     Real Preview component
 * @author      Joomkit Ltd <info@joomkit.com>
 * @copyright   Copyright (C) 2010 Joomkit Ltd. All rights reserved.
 * @license     GNU/GPL
 * @link	http://www.joomkit.com
 * Originally Derived from com_k2 by JoomlaWorks http://www.joomlaworks.gr
 */

/**
 * Content Component Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class RealpreviewHelper_Restore
{
	function restore_images($row)
	{
		$mainframe = &JFactory::getApplication();
	
		$draft_id = $row['id'];
		$itemid = $row['itemid'];
		
		$src_file = md5("Image".$draft_id);
        $dest_file = md5("Image".$itemid);
		
		$src =RPREVIEW_K2_MEDIA_PATH.'items/src/'.$src_file;
		$dest = K2_MEDIA_PATH.'items/src/'.$dest_file;
		
		$file_type = '.jpg';
		RealpreviewHelper::copy_file($src.$file_type,$dest.$file_type);
		
		$src =RPREVIEW_K2_MEDIA_PATH.'items/cache/'.$src_file;		
		$dest = K2_MEDIA_PATH.'items/cache/'.$dest_file;
			
		$file_type = '_XS.jpg';
		RealpreviewHelper::copy_file($src.$file_type,$dest.$file_type);
		
		$file_type = '_S.jpg';
		RealpreviewHelper::copy_file($src.$file_type,$dest.$file_type);
		
		$file_type = '_M.jpg';
		RealpreviewHelper::copy_file($src.$file_type,$dest.$file_type);
		
		$file_type = '_L.jpg';
		RealpreviewHelper::copy_file($src.$file_type,$dest.$file_type);
		
		$file_type = '_XL.jpg';
		RealpreviewHelper::copy_file($src.$file_type,$dest.$file_type);
		
		$file_type = '_Generic.jpg';
		RealpreviewHelper::copy_file($src.$file_type,$dest.$file_type);
			
	}
		
	function restore_attachments($row)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();	

		$draft_attachments = $row['attachments'];
		$itemid = $row['itemid'];
	
		//if(!$draft_attachments)return;
			
		$params = &JComponentHelper::getParams('com_k2');
		$path = $params->get('attachmentsFolder', NULL);
		
		if (is_null($path))
			$attachment_dir = K2_MEDIA_PATH.'attachments';
		else
			$attachment_dir = $path;
					
		$db = &JFactory::getDBO();
		$q = "SELECT id,filename FROM #__k2_attachments 
				WHERE itemID='$itemid'";				
		$db->setQuery($q);
		$item_attachments = $db->loadObjectList();
		
		if(is_array($item_attachments))
		{
			$delete_ids = array();
			foreach($item_attachments as $attachment)
			{
				$fileid = $attachment->id;
				$filename = $attachment->filename;
				
				$delete_file = $attachment_dir.DS.$filename;
				if(JFile::exists($delete_file))
				{
					if(JFile::delete($delete_file))
					{
						$delete_ids[] = $fileid;
						
						if($showRPreviewDebugInfo)
							$mainframe->enqueueMessage(JText::sprintf('RPK2_FILE_DELETED',$delete_file));
				
					}
					else
					{
						if($showRPreviewDebugError)
						{
							$warning_msg = JText::sprintf('RPK2_ERROR_DELETING_FILE',$delete_file);
							$mainframe->enqueueMessage($warning_msg,'error');
						}	
					}
				}
				else
					$delete_ids[] = $fileid;
			}
		
			$q = "DELETE FROM #__k2_attachments
					WHERE itemID='$itemid'";
			$db->setQuery($q);
			if(!$db->query())
			{
				if($showRPreviewDebugError)
					$mainframe->enqueueMessage(JText::_('RPK2_ERROR_DELETING_ITEM_ATTACHMENTS').' '.$db->getErrorMsg(),'error');
			}
		}

        $attachments = RealpreviewHelper::parse_attachment_field($draft_attachments);
        
//print_r($attachments);die();
			
		if(is_array($attachments) and !empty($attachments))
		{
			foreach($attachments as $attachment)
			{
				if(is_object($attachment))
				{
					$filename = $attachment->filename;
					$title = $attachment->title;
					$titleAttribute = $attachment->titleAttribute;
				}
				else
				{
					$filename = $attachment['filename'];
					$title = $attachment['title'];
					$titleAttribute = $attachment['titleAttribute'];
				}	
				
				if(!$filename)continue;
				
				$src = RPREVIEW_K2_MEDIA_PATH.'attachments/'.$filename;
				$dest = $attachment_dir;
				$dest_filename = RealpreviewHelper::auto_rename_file($attachment_dir,$filename);
				$dest .= "/".$dest_filename;

				if(!JFile::exists($src))continue;
				
				if(JFile::copy($src,$dest))
				{
					$item_attachment = &JTable::getInstance('K2Attachment', 'Table');	
					$item_attachment->itemID = $itemid;
					$item_attachment->filename = $dest_filename;
					$item_attachment->title = $title;
					$item_attachment->titleAttribute = $titleAttribute;
					$item_attachment->store();
					
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ATTACHMENT_COPIED',$dest));
				}
				else
				{
					if($showRPreviewDebugError)
					{
						$error_msg = JText::sprintf('RPK2_ERROR_RESTORING_ATTACHMENT_FROM_TO',$src,$dest);
						$mainframe->enqueueMessage($error_msg,'error');
					}	
				}
				
			}
		}
			
	}
	function restore_gallery($row)
	{
        global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
		
		$src = RPREVIEW_K2_MEDIA_PATH.'galleries/'.$row['id'];
		$dest = K2_MEDIA_PATH.'galleries/'.$row['itemid'];
		
		if(JFolder::exists($dest)) {
			if(JFolder::delete($dest))
			{
				if($showRPreviewDebugInfo)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_ITEM_GALLERY_FOLDER_DELETED',$dest));
			}
			else
			{
				if($showRPreviewDebugError)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_DELETING_GALLERY_FOLDER',$dest),'error');
			}
		}
		if (JFolder::exists($src)) {
			if(JFolder::copy($src,$dest))
			{
				if($showRPreviewDebugInfo)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_ITEM_GALLERY_FOLDER_RESTORED',$dest));
			}
			else
			{
				if($showRPreviewDebugError)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_RESTORING_GALLERY_FOLDER',$dest),'error');
			}
		}
           		
	}
	function restore_video($row)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
		
		if($row['video'])
		{
			preg_match_all("#^{(.*?)}(.*?){#", $row['video'], $matches, PREG_PATTERN_ORDER);
			$videotype = $matches[1][0];
			$videofile = $matches[2][0];
			
			//$extension_list = array('flv','swf','wmv','mov','mp4','3gp','divx');
			$videoExtensions = RealpreviewHelper::get_allowed_video_types();
			$audioExtensions = RealpreviewHelper::get_allowed_audio_types();
			$extension_list = array_merge($videoExtensions, $audioExtensions);
			
			if(in_array($videotype,$extension_list)) 
			{
				$itemid = $row['itemid'];
				$src_file = $videofile.'.'.$videotype;
				$dest_file = $itemid.'.'.$videotype;
				$item_video = '{'.$videotype.'}'.$itemid.'{/'.$videotype.'}';
				
				if(in_array($videotype,$videoExtensions))
					$subfolder='videos/';
				else	
					$subfolder='audio/';
					
				$src = RPREVIEW_K2_MEDIA_PATH.$subfolder.$src_file;
				$dest = K2_MEDIA_PATH.$subfolder.$dest_file;

				RealpreviewHelper::copy_file($src,$dest);
					
				$db = &JFactory::getDBO();
				$video_file = $db->Quote($item_video);
				$q = "UPDATE #__k2_items 
						SET `video`={$video_file}
						WHERE id='{$itemid}'";
				$db->setQuery($q);
				
				if(!$db->query())
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage($db->getErrorMsg(), 'error');
				}
				else 
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_NEW_VIDEO_FILENAME_SAVED',$video_file));
				}
			}
		}
		
	}
	
	function restore_tags($row)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
		
		$db = &JFactory::getDBO();
		$tags = $row['tags'];
		$itemid = $row['itemid'];
				
		if(!(int)$itemid)return;
			
		$q = "DELETE FROM #__k2_tags_xref where itemID='$itemid'";
		$db->setQuery($q);
		if(!$db->query())
		{
			if($showRPreviewDebugError)
			{
				$mainframe->enqueueMessage(JText::_('RPK2_ERROR_DELETING_EXISTING_TAGS'), 'error');
				$mainframe->enqueueMessage($db->getErrorMsg(), 'error');
			}
		}
		else 
		{
			if($showRPreviewDebugInfo)
				$mainframe->enqueueMessage(JText::_('RPK2_OLD_TAGS_DELETED'));
		}
		
		if(!$tags)return;
		
		$tag_ids = explode(',',$tags);

		if(is_array($tag_ids) and !empty($tag_ids))
		{
			$values=array();
			foreach($tag_ids as $tagid)
			{
				if((int)$tagid)
					$values[] = "(null,$tagid,$itemid)";
			}

			if(!empty($values))
			{
				$value_str = implode(",\n",$values);
				
				$q = "INSERT INTO #__k2_tags_xref (id,tagID,itemID)
					VALUES {$value_str}";
				$db->setQuery($q);
				if(!$db->query())
				{
					if($showRPreviewDebugError)
					{
						$mainframe->enqueueMessage(JText::_('RPK2_ERROR_SAVING_TAGS'),'error');
						$mainframe->enqueueMessage($db->getErrorMsg(), 'error');
					}	
				}
				else 
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::_('RPK2_NEW_TAGS_SAVED'));
				}	
			}
		}
		
	}

}
