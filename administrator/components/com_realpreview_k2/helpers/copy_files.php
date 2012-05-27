<?php
/**
 * @version     0.1 - Real Preview
 * @package     Real Preview component
 * @author      Joomkit Ltd <info@joomkit.com>
 * @copyright   Copyright (C) 2010 Joomkit Ltd. All rights reserved.
 * @license     GNU/GPL
 * @link	http://www.joomkit.com
 * Originally Derived from com_content Joomla Project
 */

/**
 * Content Component Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class RealpreviewHelper_Copy_Files
{
	function copy_images($current_draft_id,$old_draft_id,$is_draft=true)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
		
		$existingImage = JRequest::getVar('existingImage');
		$files = JRequest::get('files');
        
        if ( $is_draft and ($files['image']['error'] === 0 || $existingImage || JRequest::getBool('del_image')) ) 
			return true;
						
        if ((int)$old_draft_id and (int)$current_draft_id) 
		{			
			$src = md5("Image".$old_draft_id);
            $dest = md5("Image".$current_draft_id);
            
			$img_dest_path =RPREVIEW_K2_MEDIA_PATH.'items/src/';
			$cache_dest_path =RPREVIEW_K2_MEDIA_PATH.'items/cache/';
			
			if($is_draft)
			{
				$img_src_path = $img_dest_path;
				$cache_src_path = $cache_dest_path;
			}
			else
			{
				$img_src_path = K2_MEDIA_PATH.'items/src/';
				$cache_src_path = K2_MEDIA_PATH.'items/cache/';
			}	
			
			$src_file = $img_src_path.$src.'.jpg';
			$dest_file = $img_dest_path.$dest.'.jpg';
			if (JFile::exists($src_file))
			{
                if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_IMAGE_FILE_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
				}				
			}
			
			$src_file = $cache_src_path.$src.'_XS.jpg';
			$dest_file = $cache_dest_path.$dest.'_XS.jpg';
			if (JFile::exists($src_file))
			{
                if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_IMAGE_FILE_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
				}				
			}
			$src_file = $cache_src_path.$src.'_S.jpg';
			$dest_file = $cache_dest_path.$dest.'_S.jpg';
			if (JFile::exists($src_file)) 
			{
                if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_IMAGE_FILE_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
				}				
			}
			$src_file = $cache_src_path.$src.'_M.jpg';
			$dest_file = $cache_dest_path.$dest.'_M.jpg';
			if (JFile::exists($src_file))
			{
                if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_IMAGE_FILE_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
				}				
			}
			$src_file = $cache_src_path.$src.'_L.jpg';
			$dest_file = $cache_dest_path.$dest.'_L.jpg';
			if (JFile::exists($src_file))
			{
                if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_IMAGE_FILE_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
						//$mainframe->enqueueMessage('copied file : '.$src_file.'<br> to :'.$dest_file);
				}				
			}
			$src_file = $cache_src_path.$src.'_XL.jpg';
			$dest_file = $cache_dest_path.$dest.'_XL.jpg';
			if (JFile::exists($src_file))
			{
                if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_IMAGE_FILE_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
						//$mainframe->enqueueMessage('copied file : '.$src_file.'<br>'.JText::_('to').' - '.$dest_file);
				}			
			}
			$src_file = $cache_src_path.$src.'_Generic.jpg';
			$dest_file = $cache_dest_path.$dest.'_Generic.jpg';
			if (JFile::exists($src_file)) 
			{
                if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_IMAGE_FILE_FROM_TO',$src_file,$dest_file), 'error');
						//$mainframe->enqueueMessage($warning_msg.$src_file.'<br>'.JText::_('to').' - '.$dest_file, 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
						//$mainframe->enqueueMessage(JText::_('copied file').$src_file.'<br>'.JText::_('to').' - '.$dest_file);
				}				
			}

        }
	}
	function copy_attachments($current_draft_id,$old_draft_id,$is_draft=true)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
		
		$src = K2_MEDIA_PATH.'attachments/';
		$dest = RPREVIEW_K2_MEDIA_PATH.'attachments/';
		$db = &JFactory::getDBO();
		$attachments = array();
		
		if($is_draft)
		{
			$src = $dest; 
			$table = RealpreviewHelper::get_table_name();
			$q = "SELECT attachments FROM $table 
					WHERE id='$old_draft_id'";					
			$db->setQuery($q);
			$json_str = $db->loadResult();
			
			if($json_str)
			{				
				//$valid_tags = array('{','{');
				if ((substr($json_str, 0, 1) == '[') && (substr($json_str, -1, 1) == ']'))
				{
					try{
						$attachments = @json_decode(htmlspecialchars_decode($json_str),true);
					}catch(Exception $e){
						$attachments = array();
					}
				}
			}
		}
		else
		{	
			
			$q = "SELECT * FROM #__k2_attachments 
					WHERE itemID='$old_draft_id'";
			$db->setQuery($q);
			$attachments = $db->loadAssocList();			
		}
//print_r($attachments);die();		
		if(is_array($attachments))
		{
			foreach($attachments as $k=>$attachment)
			{
				if(is_object($attachment))	
					$filename = $attachment->filename;
				else	
					$filename = $attachment['filename'];
				
				if(!$filename)continue;
				
				$src_file =$src.$filename;
				$dest_file =$dest.$filename;
			
				if(JFile::exists($src.$filename))
				{
					//$dest_file = RealpreviewHelper::auto_rename_file($dest_file);
					$dest_filename = RealpreviewHelper::auto_rename_file($dest,$filename);
					$dest_file = $dest.$dest_filename;
					
					if(!JFile::copy($src_file,$dest_file))
					{
						if($showRPreviewDebugError)
							$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_ATTACHMENT_FROM_TO',$src_file,$dest_file),'error');
							//$mainframe->enqueueMessage($warning_msg.$src_file.'<br>'.JText::_('to').' - '.$dest_file,'error');
					}
					else
					{
						if($showRPreviewDebugInfo)
							$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
							//$mainframe->enqueueMessage(JText::_('copied file').' - '.$src_file.'<br>'.JText::_('to').' - '.$dest_file);
					}
					
					
					//$new_filename = JFile::getName($dest_file);
					//$dest = str_replace("\\","/",$dest);
					$dest_file =str_replace("\\","/",$dest_file);
					$new_filename = basename($dest_file);
					if($filename != $new_filename)
					{
						$attachments[$k]['filename']=$new_filename;
					}

				}
				
			}
		}
        return $attachments;
	
	}
	function copy_gallery($current_draft_id,$old_draft_id,$is_draft=true)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
				
		if($is_draft)
		{
			$gallery_dir = RPREVIEW_K2_MEDIA_PATH.'galleries/';
			if (JFolder::exists($gallery_dir.$old_draft_id)) 
			{
				$src_file =$gallery_dir.$old_draft_id;
				$dest_file =$gallery_dir.$current_draft_id;
				if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_GALLERY_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
				}
			}       
		}
		else
		{
			$gallery_dir = K2_MEDIA_PATH.'galleries/';
			$rpreview_gallery_dir = RPREVIEW_K2_MEDIA_PATH.'galleries/';
			if(JFolder::exists($gallery_dir.$old_draft_id)) 
			{
				$src_file =$gallery_dir.$old_draft_id;
				$dest_file =$rpreview_gallery_dir.$current_draft_id;
				if(!JFile::copy($src_file,$dest_file))
				{
					if($showRPreviewDebugError)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_COPYING_GALLERY_FROM_TO',$src_file,$dest_file), 'error');
				}
				else
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src_file,$dest_file));
				}
			} 
		
		}
		
	}
	function copy_video($current_draft_id,$old_draft_id,$is_draft=true)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
				
		$dest_dir = RPREVIEW_K2_MEDIA_PATH.'videos/';
		$audio_dest_dir = RPREVIEW_K2_MEDIA_PATH.'audio/';
		$rpreview_table = RealpreviewHelper::get_table_name();
		if($is_draft)
		{
			$table = $rpreview_table;
			$video_dir = $dest_dir;
			$audio_dir = $audio_dest_dir;
		}
		else
		{
			$table = '#__k2_items';
			$video_dir = K2_MEDIA_PATH.'videos/';
			$audio_dir = K2_MEDIA_PATH.'audio/';
		}
		
		$db = &JFactory::getDBO();
		$q = "SELECT video FROM $table WHERE id='$old_draft_id'";	
		$db->setQuery($q);
		$video = $db->loadResult();
		
		if($video)
		{
			preg_match_all("#^{(.*?)}(.*?){#", $video, $matches, PREG_PATTERN_ORDER);
			$videotype = $matches[1][0];
			$videofile = $matches[2][0];
			
			//$extension_list = array('flv','swf','wmv','mov','mp4','3gp','divx');
			$videoExtensions = RealpreviewHelper::get_allowed_video_types();
			$audioExtensions = RealpreviewHelper::get_allowed_audio_types();
			$extension_list = array_merge($videoExtensions, $audioExtensions);
			
			if(in_array($videotype,$extension_list)) 
			{
				$src_file = $videofile.'.'.$videotype;
				$dest_file = $current_draft_id.'.'.$videotype;
				
				if(in_array($videotype,$videoExtensions))
				{
					$src = $video_dir.$src_file;
					$dest = $dest_dir.$dest_file;
				}
				else
				{
					$src = $audio_dir.$src_file;
					$dest = $audio_dest_dir.$dest_file;
				}
				
				RealpreviewHelper::copy_file($src,$dest,false);
				
				$item_video = '{'.$videotype.'}'.$current_draft_id.'{/'.$videotype.'}';
				$video_file = $db->Quote($item_video);
				$q = "UPDATE `{$rpreview_table}` 
						SET `video`={$video_file}
						WHERE id='{$current_draft_id}'";
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
		return true;
	}

}
