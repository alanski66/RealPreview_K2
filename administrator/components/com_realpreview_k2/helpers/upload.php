<?php
/**
 * @version     0.1 - Real Preview
 * @package     Real Preview component
 * @author      Joomkit Ltd <info@joomkit.com>
 * @copyright   Copyright (C) 2010 Joomkit Ltd. All rights reserved.
 * @license     GNU/GPL
 * @link	http://www.joomkit.com
 *
 * Originally Derived from JoomlaWorks k2 project
 */

/**
 * Content Component Helper
 *
 * @static
 * @package		Joomla
 * @subpackage	Content
 * @since 1.5
 */
class RealpreviewHelper_Upload
{
	function upload_images(&$row,$files,$redirect)
	{
		$mainframe = &JFactory::getApplication();
		
		$existingImage = JRequest::getVar('existingImage');
		$is_uploaded = true;
        if ( ($files['image']['error'] === 0 || $existingImage) && !JRequest::getBool('del_image')) {

            if($files['image']['error'] === 0){
                $image = $files['image'];
            }
            else{
                $image = JPATH_SITE.DS.JPath::clean($existingImage);
				$is_uploaded = false;
            }
			
            $db = &JFactory::getDBO();
			$handle = new Upload($image);
            $handle->allowed = array('image/*');

            if ($handle->uploaded) 
			{
                //Image params
                $category = &JTable::getInstance('K2Category', 'Table');
                $category->load($row->catid);
                $cparams = new JParameter($category->params);

                if ($cparams->get('inheritFrom')) {
                    $masterCategoryID = $cparams->get('inheritFrom');
                    $query = "SELECT * FROM #__k2_categories WHERE id=".(int)$masterCategoryID;
                    $db->setQuery($query, 0, 1);
                    $masterCategory = $db->loadObject();
                    $cparams = new JParameter($masterCategory->params);
                }

				$params = &JComponentHelper::getParams('com_k2');
                $params->merge($cparams);

                //Original image
                $savepath = RPREVIEW_K2_MEDIA_PATH.'items'.DS.'src';
                $handle->image_convert = 'jpg';
                $handle->jpeg_quality = 100;
                $handle->file_auto_rename = false;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = md5("Image".$row->id);
                $handle->Process($savepath);

                $filename = $handle->file_dst_name_body;
                $savepath = RPREVIEW_K2_MEDIA_PATH.'items/cache';

                //XLarge image
                $handle->image_resize = true;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->jpeg_quality = $params->get('imagesQuality');
                $handle->file_auto_rename = false;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = $filename.'_XL';
                if (JRequest::getInt('itemImageXL')) {
                    $imageWidth = JRequest::getInt('itemImageXL');
                } else {
                    $imageWidth = $params->get('itemImageXL', '800');
                }
                $handle->image_x = $imageWidth;
                $handle->Process($savepath);

                //Large image
                $handle->image_resize = true;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->jpeg_quality = $params->get('imagesQuality');
                $handle->file_auto_rename = false;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = $filename.'_L';
                if (JRequest::getInt('itemImageL')) {
                    $imageWidth = JRequest::getInt('itemImageL');
                } else {
                    $imageWidth = $params->get('itemImageL', '600');
                }
                $handle->image_x = $imageWidth;
                $handle->Process($savepath);

                //Medium image
                $handle->image_resize = true;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->jpeg_quality = $params->get('imagesQuality');
                $handle->file_auto_rename = false;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = $filename.'_M';
                if (JRequest::getInt('itemImageM')) {
                    $imageWidth = JRequest::getInt('itemImageM');
                } else {
                    $imageWidth = $params->get('itemImageM', '400');
                }
                $handle->image_x = $imageWidth;
                $handle->Process($savepath);

                //Small image
                $handle->image_resize = true;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->jpeg_quality = $params->get('imagesQuality');
                $handle->file_auto_rename = false;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = $filename.'_S';
                if (JRequest::getInt('itemImageS')) {
                    $imageWidth = JRequest::getInt('itemImageS');
                } else {
                    $imageWidth = $params->get('itemImageS', '200');
                }
                $handle->image_x = $imageWidth;
                $handle->Process($savepath);

                //XSmall image
                $handle->image_resize = true;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->jpeg_quality = $params->get('imagesQuality');
                $handle->file_auto_rename = false;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = $filename.'_XS';
                if (JRequest::getInt('itemImageXS')) {
                    $imageWidth = JRequest::getInt('itemImageXS');
                } else {
                    $imageWidth = $params->get('itemImageXS', '100');
                }
                $handle->image_x = $imageWidth;
                $handle->Process($savepath);

                //Generic image
                $handle->image_resize = true;
                $handle->image_ratio_y = true;
                $handle->image_convert = 'jpg';
                $handle->jpeg_quality = $params->get('imagesQuality');
                $handle->file_auto_rename = false;
                $handle->file_overwrite = true;
                $handle->file_new_name_body = $filename.'_Generic';
                $imageWidth = $params->get('itemImageGeneric', '300');
                $handle->image_x = $imageWidth;
                $handle->Process($savepath);

                if($files['image']['error'] === 0 and $is_uploaded)
					$handle->Clean();

            } else {
                //$mainframe->redirect($redirect, $handle->error, 'error');
				$mainframe->enqueueMessage($handle->error,'error');
            }

        }

        if (JRequest::getBool('del_image')) 
		{
            $current = &JTable::getInstance('RealPreviewK2', 'Table');
            $current->load($row->id);
            $filename = md5("Image".$current->id);
            
			$img_src_path =RPREVIEW_K2_MEDIA_PATH.'items/src/';
			$img_cache_path =RPREVIEW_K2_MEDIA_PATH.'items/cache/';
			
			if (JFile::exists($img_src_path.$filename.'.jpg')) {
                JFile::delete($img_src_path.$filename.'.jpg');
            }

            if (JFile::exists($img_cache_path.$filename.'_XS.jpg')) {
                JFile::delete($img_cache_path.$filename.'_XS.jpg');
            }

            if (JFile::exists($img_cache_path.$filename.'_S.jpg')) {
                JFile::delete($img_cache_path.$filename.'_S.jpg');
            }

            if (JFile::exists($img_cache_path.$filename.'_M.jpg')) {
                JFile::delete($img_cache_path.$filename.'_M.jpg');
            }

            if (JFile::exists($img_cache_path.$filename.'_L.jpg')) {
                JFile::delete($img_cache_path.$filename.'_L.jpg');
            }

            if (JFile::exists($img_cache_path.$filename.'_XL.jpg')) {
                JFile::delete($img_cache_path.$filename.'_XL.jpg');
            }

            if (JFile::exists($img_cache_path.$filename.'_Generic.jpg')) {
                JFile::delete($img_cache_path.$filename.'_Generic.jpg');
            }

            $row->image_caption = '';
            $row->image_credits = '';

        }
	}
	function upload_attachments(&$row,$files,$redirect)
	{
		$mainframe = &JFactory::getApplication();
		
		$attachments = JRequest::getVar('attachment_file', NULL, 'FILES', 'array');
        $attachments_names = JRequest::getVar('attachment_name', '', 'POST', 'array');
        $attachments_titles = JRequest::getVar('attachment_title', '', 'POST', 'array');
        $attachments_title_attributes = JRequest::getVar('attachment_title_attribute', '', 'POST', 'array');
		$attachments_existing_files = JRequest::getVar('attachment_existing_file', '', 'POST', 'array');

        $attachmentFiles = array();
        $attachment_list = array();

        if (count($attachments)) {

            foreach ($attachments as $k=>$l) {
                foreach ($l as $i=>$v) {
                    if (!array_key_exists($i, $attachmentFiles))
                    $attachmentFiles[$i] = array();
                    $attachmentFiles[$i][$k] = $v;
                }

            }

            $savepath = RPREVIEW_K2_MEDIA_PATH.'attachments';
            $counter = 0;

			$attachment_list=array();

			foreach ($attachmentFiles as $key=>$file) 
			{				
				if($file["tmp_name"] || $attachments_existing_files[$key])
				{					
					$is_uploaded = true;
					if($attachments_existing_files[$key])
					{
						$is_uploaded = false;
						$file = JPATH_SITE.DS.JPath::clean($attachments_existing_files[$key]);
					}
		
                    $handle = new Upload($file);

                    if ($handle->uploaded) {
                        $handle->file_auto_rename = true;
                        $handle->allowed[] = 'application/x-zip';						
						$handle->allowed[] = 'application/download';
                        $handle->Process($savepath);
                        $filename = $handle->file_dst_name;
						
						//avoid deleting local files
						if($is_uploaded)
							$handle->Clean();
                        
						$attachment=new stdClass();
						$attachment->id = $counter + 1;
						$attachment->itemID = $row->itemid;
                        $attachment->filename = $filename;
                        $attachment->title = ( empty($attachments_titles[$counter])) ? $filename : $attachments_titles[$counter];
                        $attachment->titleAttribute = ( empty($attachments_title_attributes[$counter])) ? $filename : $attachments_title_attributes[$counter];
/*                        
						$attachment=array();
						$attachment['itemID'] = $row->itemid;
                        $attachment['filename'] = $filename;
                        $attachment['title'] = ( empty($attachments_titles[$counter])) ? $filename : $attachments_titles[$counter];
                        $attachment['titleAttribute'] = ( empty($attachments_title_attributes[$counter])) ? $filename : $attachments_title_attributes[$counter];
*/                        
						
						$attachment_list[]=$attachment;
						
                    } else {
                        $mainframe->enqueueMessage($handle->error,'error');
                    }
                }
                $counter++;
            }
			//$row->attachments = @json_encode($attachment_list);
			$row->attachments = RealpreviewHelper::json_encode($attachment_list);

        }
		
		return $attachment_list;
	
	}
	function upload_gallery(&$row,$files,$redirect)
	{
        $mainframe = &JFactory::getApplication();
	
		$flickrGallery = JRequest::getVar('flickrGallery');
		if($flickrGallery) {
			$row->gallery = '{gallery}'.$flickrGallery.'{/gallery}';
		}
		
        if (isset($files['gallery']) && $files['gallery']['error'] == 0 && !JRequest::getBool('del_gallery')) {
            $handle = new Upload($files['gallery']);
            $handle->file_auto_rename = true;
            $savepath = RPREVIEW_K2_MEDIA_PATH.'galleries';
            $handle->allowed = array("application/download", "application/rar", "application/x-rar-compressed", "application/arj", "application/gnutar", "application/x-bzip", "application/x-bzip2", "application/x-compressed", "application/x-gzip", "application/x-zip-compressed", "application/zip", "multipart/x-zip", "multipart/x-gzip", "application/x-unknown", "application/x-zip");

            if ($handle->uploaded) {

                $handle->Process($savepath);
                $handle->Clean();

                if (JFolder::exists($savepath.DS.$row->id)) {
                    JFolder::delete($savepath.DS.$row->id);
                }

                if (!JArchive::extract($savepath.DS.$handle->file_dst_name, $savepath.DS.$row->id)) 
				{
                    $mainframe->enqueueMessage(JText::_('RPK2_GALLERY_UPLOAD_ERROR_CANNOT_EXTRACT_ARCHIVE'),'error');
                }
				else 
				{
                    $row->gallery = '{gallery}'.$row->itemid.'{/gallery}';
                }
                JFile::delete($savepath.DS.$handle->file_dst_name);
                $handle->Clean();

            } else {
                //$mainframe->redirect($redirect, $handle->error, 'error');
				$mainframe->enqueueMessage($handle->error,'error');
            }
        }

        if (JRequest::getBool('del_gallery')) {

            $current = &JTable::getInstance('RealPreviewK2', 'Table');
            $current->load($row->id);

            if (JFolder::exists(RPREVIEW_K2_MEDIA_PATH.'galleries'.DS.$current->id)) {
                JFolder::delete(RPREVIEW_K2_MEDIA_PATH.'galleries'.DS.$current->id);
            }
            $row->gallery = '';
        }
		
	}
	function upload_video(&$row,$files,$redirect)
	{
		$mainframe = &JFactory::getApplication();
		
		$videoExtensions = RealpreviewHelper::get_allowed_video_types();
		$audioExtensions = RealpreviewHelper::get_allowed_audio_types();
		
        if (!JRequest::getBool('del_video')) 
		{
            if (isset($files['video']) && $files['video']['error'] == 0) {

                $validExtensions = array('flv', 'swf', 'wmv', 'mov', 
											'mp4', '3gp', 'avi', 'divx'
									);
                $savepath = RPREVIEW_K2_MEDIA_PATH.'videos';
                $filetype = JFile::getExt($files['video']['name']);
			
				//if(JFolder::exists(K2_MEDIA_PATH.'audio/')){
				$validExtensions = array_merge($videoExtensions, $audioExtensions);
				if (in_array($filetype, $audioExtensions))
					$savepath = RPREVIEW_K2_MEDIA_PATH.DS.'audio';
				//}
				
                if (!in_array($filetype, $validExtensions)) {
                    $mainframe->enqueueMessage(JText::_('RPK2_INVALID_VIDEO_FILE'),'error');
                }
	
				
                $filename = JFile::stripExt($files['video']['name']);
                JFile::upload($files['video']['tmp_name'], $savepath.DS.$row->id.'.'.$filetype);
                $filetype = JFile::getExt($files['video']['name']);
                $row->video = '{'.$filetype.'}'.$row->id.'{/'.$filetype.'}';

            } 
			else 
			{
                if (JRequest::getVar('remoteVideo')) 
				{
                    $fileurl = JRequest::getVar('remoteVideo');
                    $filetype = JFile::getExt($fileurl);
                    $row->video = '{'.$filetype.'remote}'.$fileurl.'{/'.$filetype.'remote}';
                }

                if (JRequest::getVar('videoID')) 
				{
                    $provider = JRequest::getWord('videoProvider');
                    $videoID = JRequest::getVar('videoID');
                    $row->video = '{'.$provider.'}'.$videoID.'{/'.$provider.'}';
                }
                if (JRequest::getVar('embedVideo', '', 'post', 'string', JREQUEST_ALLOWRAW)) 
				{
                    $row->video = JRequest::getVar('embedVideo', '', 'post', 'string', JREQUEST_ALLOWRAW);
                }
            }
        } 
		else 
		{
            $current = &JTable::getInstance('RealPreviewK2', 'Table');
            $current->load($row->id);

            preg_match_all("#^{(.*?)}(.*?){#", $current->video, $matches, PREG_PATTERN_ORDER);
            $videotype = $matches[1][0];
            $videofile = $matches[2][0];

            //if ($videotype == 'flv' || $videotype == 'swf' || $videotype == 'wmv' || $videotype == 'mov' || $videotype == 'mp4' || $videotype == '3gp' || $videotype == 'divx') 
			if (in_array($videotype, $videoExtensions))
			{
                if (JFile::exists(RPREVIEW_K2_MEDIA_PATH.'videos'.DS.$videofile.'.'.$videotype))
                JFile::delete(RPREVIEW_K2_MEDIA_PATH.'videos'.DS.$videofile.'.'.$videotype);
            }
			
			if(in_array($videotype, $audioExtensions)) 
			{
				if (JFile::exists(RPREVIEW_K2_MEDIA_PATH.'audio'.DS.$videofile.'.'.$videotype))
				JFile::delete(RPREVIEW_K2_MEDIA_PATH.'audio'.DS.$videofile.'.'.$videotype);
			}

            $row->video = '';
            $row->video_caption = '';
            $row->video_credits = '';
        }
	}

}
