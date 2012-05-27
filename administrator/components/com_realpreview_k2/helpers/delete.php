<?php
/**
 * @version     0.1 - Real Preview
 * @package     Real Preview component
 * @author      Joomkit Ltd <info@joomkit.com>
 * @copyright   Copyright (C) 2010 Joomkit Ltd. All rights reserved.
 * @license     GNU/GPL
 * @link	http://www.joomkit.com
 * Originally Derived from JoomlaWorks k2 project
 */

class RealpreviewHelper_Delete
{
	function delete_images($row)
	{
		$mainframe = &JFactory::getApplication();
	
		$draft_id = $row['id'];
		$src_file = md5("Image".$draft_id);
		
		$src =RPREVIEW_K2_MEDIA_PATH.'items/src/'.$src_file;
		
		$file_type = '.jpg';
		RealpreviewHelper::delete_file($src.$file_type);
		
		$src =RPREVIEW_K2_MEDIA_PATH.'items/cache/'.$src_file;		
			
		$file_type = '_XS.jpg';
		RealpreviewHelper::delete_file($src.$file_type);
		
		$file_type = '_S.jpg';
		RealpreviewHelper::delete_file($src.$file_type);
		
		$file_type = '_M.jpg';
		RealpreviewHelper::delete_file($src.$file_type);
		
		$file_type = '_L.jpg';
		RealpreviewHelper::delete_file($src.$file_type);
		
		$file_type = '_XL.jpg';
		RealpreviewHelper::delete_file($src.$file_type);
		
		$file_type = '_Generic.jpg';
		RealpreviewHelper::delete_file($src.$file_type);
			
	}
		
	function delete_attachments($row)
	{
		$mainframe = &JFactory::getApplication();	

		$draft_attachments = $row['attachments'];
		$itemid = $row['itemid'];
	
		if(!$draft_attachments)return;
		
		$attachments = RealpreviewHelper::parse_attachment_field($draft_attachments);
        
//print_r($attachments);die();
			
		if(is_array($attachments) and !empty($attachments))
		{
			foreach($attachments as $attachment)
			{
				if(is_object($attachment))
					$filename = $attachment->filename;
				else	
					$filename = $attachment['filename'];
				
				if($filename)
				{
					$file = RPREVIEW_K2_MEDIA_PATH.'attachments/'.$filename;				
					RealpreviewHelper::delete_file($file);				
				}	
			}
		}
			
	}
	function delete_gallery($row)
	{
        global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
				
		$scr = RPREVIEW_K2_MEDIA_PATH.'galleries/'.$row['id'];
		
		if (JFolder::exists($scr)) 
		{
			if(JFolder::delete($scr))
			{
				if($showRPreviewDebugInfo)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_DRAFT_GALLERY_FOLDER_DELETED',$src));
			}
			else
			{
				if($showRPreviewDebugError)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_DELETING_DRAFT_GALLERY',$src),'error');
			}
		}
           		
	}
	function delete_video($row)
	{
		$mainframe = &JFactory::getApplication();
		
		if($row['video'])
		{
			preg_match_all("#^{(.*?)}(.*?){#", $row['video'], $matches, PREG_PATTERN_ORDER);
			$videotype = $matches[1][0];
			$videofile = $matches[2][0];
			
			$videoExtensions = RealpreviewHelper::get_allowed_video_types();
			$audioExtensions = RealpreviewHelper::get_allowed_audio_types();
			$extension_list = array_merge($videoExtensions, $audioExtensions);
			//$extension_list = array('flv','swf','wmv','mov','mp4','3gp','divx');
			
			if($videotype and in_array($videotype,$extension_list)) 
			{
				if(in_array($videotype,$videoExtensions))
					$subfolder = 'videos/';
				else
					$subfolder = 'audio/';
					
				$scr_file = $videofile.'.'.$videotype;
				$src = RPREVIEW_K2_MEDIA_PATH.$subfolder.$src_file;

				RealpreviewHelper::delete_file($src);
						
			}
		}
		
	}
}
