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


if(!defined('K2_COMPONENT_PATH_ADMIN'))
	define('K2_COMPONENT_PATH_ADMIN',JPATH_ADMINISTRATOR.'/components/com_k2');
	
if(!defined('RPREVIEW_K2_MEDIA_PATH'))
	define('RPREVIEW_K2_MEDIA_PATH',JPATH_SITE.'/media/realpreview/k2/');
	
if(!defined('RPREVIEW_K2_MEDIA_URL'))
	define('RPREVIEW_K2_MEDIA_URL',JURI::root(true).'/media/realpreview/k2/');
	
if(!defined('RPREVIEW_K2_ADMIN_PATH'))
	define('RPREVIEW_K2_ADMIN_PATH',JPATH_ADMINISTRATOR.'/components/com_realpreview_k2/');

if(!defined('K2_MEDIA_PATH'))
	define('K2_MEDIA_PATH',JPATH_SITE.'/media/k2/');
	
if(!defined('K2_MEDIA_URL'))
	define('K2_MEDIA_URL',JURI::root(true).'/media/k2/');
		
class RealpreviewHelper
{
//administrator/index.php?option=com_realpreview_k2&itemid=1&version=1	
		
	function initialize_draft($itemid='',$is_frontend=false)
	{		
		if(!$itemid)
			$itemid = JRequest::getInt('itemid');
			
		$version = JRequest::getInt('version');
		$table = RealpreviewHelper::get_table_name();
		
		if($itemid)
		{
			$db = &JFactory::getDBO();
			if($is_frontend)
				$count=0;
			else	
			{
				$q = "SELECT COUNT(id) FROM $table WHERE itemid='$itemid'";
				$db->setQuery($q);
				$count = (int)$db->loadResult();
			}
			if(!$count)
			{
				$q = "SELECT * FROM #__k2_items WHERE id='$itemid'";
				$db->setQuery($q);
				$item = $db->loadAssoc();
				
//print_r($item);
//die();
				RealpreviewHelper::copy_k2_item($item,$is_frontend);
					
			}
			
		}	
	}
	
	function copy_k2_item($item,$is_frontend=false)
	{
	
		require_once(RPREVIEW_K2_ADMIN_PATH.'helpers/upload.php');
		require_once(RPREVIEW_K2_ADMIN_PATH.'helpers/copy_files.php');
		
		if($item)
		{	
			$itemid = $item['id'];
			$item['itemid'] = $itemid;
			
			if(!$itemid)
				return;
			
			unset($item['id']);
			$table = RealpreviewHelper::get_table_name();
			
			JTable::addIncludePath(RPREVIEW_K2_ADMIN_PATH.DS.'tables');
			
			$draft = &JTable::getInstance('RealPreviewK2', 'Table');
			$draft->bind($item);
			
			$draft->version=1;
			$draft->published=1;
			$draft->itemid=$itemid;
			$draft->flowstatus='draft';
			
			if($is_frontend)
			{
				$draft->version=RealpreviewHelper::get_next_item_vesion($itemid);
			}
			
			$db = &JFactory::getDBO();
			$q = "SELECT tagID FROM #__k2_tags_xref WHERE itemID='$itemid'";
			$db->setQuery($q);
			$tags = $db->loadResultArray();
			if(is_array($tags))
			{
				$draft->tags = implode(',',$tags);
			}
			
			$q = "SELECT * FROM #__k2_attachments WHERE itemID='$itemid'";
			$db->setQuery($q);
			//$attachments = $db->loadAssocList();
			$attachments = $db->loadObjectList();
			
			$json_str='';
			if(is_array($attachments))
			{
				$json_str = RealpreviewHelper::json_encode($attachments);
			}	
			$draft->attachments = $json_str;
/*					
print_r($attachments);
echo '<br><br>'.$json_str.'<br><br>';
print_r($draft);
die();
//*/
			$draft->store();
			$draft->checkin();
		
			//copy files/images
			$draft_id = $draft->id;
			RealpreviewHelper_Copy_Files::copy_images($draft_id,$itemid,false);					
			RealpreviewHelper_Copy_Files::copy_gallery($draft_id,$itemid,false);
			RealpreviewHelper_Copy_Files::copy_video($draft_id,$itemid,false);
			$orig_attachments = RealpreviewHelper_Copy_Files::copy_attachments($current_draft_id,$itemid,false);
			
			if(is_array($orig_attachments) and !empty($orig_attachments))
			{							
				//$json_str = @json_encode($orig_attachments);
				$json_str = RealpreviewHelper::json_encode($orig_attachments);
				$json_str = $db->Quote($json_str);
				$q = "UPDATE  $table 
						SET `attachments`= $json_str
						WHERE id='$draft_id'";
				$db->setQuery($q);
				$db->query();
			
			}
			
			if($is_frontend)
			{
				$q = "UPDATE  $table 
						SET `published`= 0
						WHERE itemid='$itemid'
						AND id !='$draft_id'";
				$db->setQuery($q);
				$db->query();
			}
		}
	}
	
	function get_last_item_version($itemid)
	{
		if(!(int)$itemid) return 0;
		
		$table = RealpreviewHelper::get_table_name();
		
		$db = &JFactory::getDBO();
		$q = "SELECT `version` FROM $table 
				WHERE itemid='$itemid'
				ORDER BY version DESC
				LIMIT 1";
		$db->setQuery($q);
		$version = $db->loadResult();
		
		return (int)$version;
	}
	
	function json_encode($var)
	{
		$json_str = '';
		if(is_array($var) and !empty($var))
		{				
			if(RealpreviewHelper::use_k2_json_api())
			{
				//require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
				$json = new Services_JSON;
				$json_str = $json->encode($var);
			}
			else
				$json_str = @json_encode($var);
		}
		return $json_str;
	}
	function json_decode_v1($json_str)
	{
		$var=array();
		if(trim($json_str)!='')
		{		
			if(RealpreviewHelper::use_k2_json_api())
			{
				//require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
				$json = new Services_JSON;
				$var = $json->decode($json_str);
				if(empty($var) and $json_str !='')
				{	
					$tmp_var = RealpreviewHelper::php_json_decode($json_str);
					if($tmp_var != '')
						$var = $tmp_var;
				}		
			}
			else
				$var = RealpreviewHelper::php_json_decode($json_str);
				
			//if(!is_array($var))$var=array();
		}
		return $var;
	}
	function json_decode($json_str)
	{
		$var=array();
		if(trim($json_str)!='')
		{		
			if(RealpreviewHelper::use_k2_json_api())
			{
				//require_once (K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php');
				$json = new Services_JSON;
				$var = $json->decode($json_str);
			}
			else
				$var = RealpreviewHelper::php_json_decode($json_str);
				
			//if(!is_array($var))$var=array();
		}
		return $var;
	}
	function parse_attachment_field($json_str)
	{		
		//return RealpreviewHelper::json_decode($json_str);	
		$json_str = htmlspecialchars_decode($json_str);	
		$attchments =  RealpreviewHelper::json_decode($json_str);		
		if(!is_array($attchments))
			$attchments=array();
			
		return $attchments;	
	}
	function use_k2_json_api()
	{
		$use_lib = false;
		$lib_file = K2_COMPONENT_PATH_ADMIN.DS.'lib'.DS.'JSON.php';
		if(file_exists($lib_file))
		{
			require_once($lib_file);
			$use_lib=true;
		}
		return $use_lib;
	}
	function php_json_decode($json_str)
	{
		$attachments=array();
		if($json_str)
		{				
			$json_start_tags = array('[','{');
			$json_end_tags = array(']','}');
			$start_tag =substr($json_str, 0, 1);
			$end_tag = substr($json_str, -1, 1);
			
			if ((in_array($start_tag,$json_start_tags)) && (in_array($end_tag,$json_end_tags)))
			{
				try{
					$attachments = @json_decode(htmlspecialchars_decode($json_str),true);
				}catch(Exception $e){
					$attachments = array();
				}
			}
		}
		return $attachments;
	}
	
	function get_next_item_vesion($itemid)
	{
		$next_version = RealpreviewHelper::get_last_item_version($itemid);
		$next_version++;
		return $next_version;
	}
	
	function auto_rename_file_v1($filename)
	{
		//if(!JFile::exists($filename))
			//return $filename;
		if(trim($filename)!='')
		{
			$cpt = 1;
			$ext = JFile::getExt($filename);
			$file = JFile::stripExt($filename);			
			while (JFile::exists($filename)) 
			{
				$filename = $file . '_' . $cpt.'.'.$ext;
				$cpt++;
			}
		}
		
		return $filename;
	}
	function auto_rename_file($path,$filename)
	{
		if(trim($filename)!='' and trim($path)!='')
		{
			$cpt = 1;
			$ext = JFile::getExt($filename);
			$file = JFile::stripExt($filename);			
			while (JFile::exists($path.'/'.$filename)) 
			{
				$filename = $file . '_' . $cpt.'.'.$ext;
				$cpt++;
			}
		}
		
		return $filename;
	}
	
	function get_item_attachments($itemid)
	{
        $attachments = array();
		if((int)$itemid)
		{		
			$db = &JFactory::getDBO();
			$table = RealpreviewHelper::get_table_name();
			$query = "SELECT attachments FROM $table 
						WHERE id={$itemid}";
						
			$db->setQuery($query);
			$result = $db->loadResult();
		
			if ($result) {
				$attachments = RealpreviewHelper::parse_attachment_field($result);
			}
		}
		
		return $attachments;
		
	}
	
	function copy_file($src,$dest,$delete_old=true)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
		
		$status = true;
		
		if (JFile::exists($src)) {
			if(JFile::copy($src,$dest))
			{
				if($showRPreviewDebugInfo)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_COPIED_FILE_FROM_TO',$src,$dest));
					//$mainframe->enqueueMessage('copied file : '.$src.'<br> to :'.$dest);
			}
			else
			{
				$status = false;
				if($showRPreviewDebugError)
				{
					$mainframe->enqueueMessage(JText::sprintf('RPK2_ERROR_DELETING_FILE',$src,$dest), 'error');
				}	
			}
		}
		elseif($delete_old)
		{

			if (JFile::exists($dest)) {
				if(JFile::delete($dest))
				{
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage(JText::sprintf('RPK2_FILE_DELETED',$src));
				}
				else
				{
					$warning_msg = JText::sprintf('RPK2_ERROR_DELETING_FILE',$dest);
					if($showRPreviewDebugInfo)
						$mainframe->enqueueMessage($warning_msg, 'error');
				}
			}
		}
		return $status;
	}
	
	function delete_file($file)
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;
		
		$mainframe = &JFactory::getApplication();
		
		$status = true;
		
		if (JFile::exists($file)) 
		{
			if(JFile::delete($file))
			{
				if($showRPreviewDebugInfo)
					$mainframe->enqueueMessage(JText::sprintf('RPK2_FILE_DELETED',$file));
			}
			else
			{
				$status=false;
				if($showRPreviewDebugInfo)
				{
					$warning_msg = JText::sprintf('RPK2_ERROR_DELETING_FILE',$file);
					$mainframe->enqueueMessage($warning_msg, 'error');
				}	
			}
		}
		else
		{
			if($showRPreviewDebugInfo)
				$mainframe->enqueueMessage(JText::sprintf('RPK2_DELETE_ERROR_MISSING_FILE',$file),'error');
		}
	
		return $status;
	}
	
	function set_debug_variables()
	{
		global $showRPreviewDebugInfo,$showRPreviewDebugError;

		//$showRPreviewDebugInfo=true;
		//$showRPreviewDebugError=true;	
		
		$params = &JComponentHelper::getParams( 'com_realpreview_k2' );
		$showRPreviewDebugInfo = (int)$params->get( 'show_debug_info' , 0);
		$showRPreviewDebugError = (int)$params->get( 'show_debug_error' , 1);
		
	}
	
	function get_table_name(){return "#__realpreview_k2";}
	
	
	function is_j16()
	{
		global $RPk2IsJM17;
		
		if(!($RPk2IsJM17 === true or $RPk2IsJM17 === false))
		{					
			$version = new JVersion;
			$jversion = floatval($version->getShortVersion());
			if($jversion >= 1.6)
				$RPk2IsJM17 = true;
			else	
				$RPk2IsJM17 = false;
				
			//$RPk2IsJM17 = version_compare( JVERSION, '1.6.0', 'ge' );
		}
		
		return $RPk2IsJM17;
	}
	
	function get_k2_file($file,$folder='assets')
	{
		jimport('joomla.filesystem.file');
		
		$k2_assets_path = '/media/k2/assets/';
		$k2_lib_path = '/administrator/components/com_k2/lib/';
		$rp_k2_assets_path = '/administrator/components/com_realpreview_k2/assets/k2/';
		
		$url_path=false;
		$ext = JFile::getExt($file);
		if($ext=='css' or $ext=='js')
			$url_path = true;
			
		if($folder=='assets')
		{
			$k2_dir = $k2_assets_path . $ext .'/';
			$rp_dir = $rp_k2_assets_path.$ext.'/';
		}
		elseif($folder=='lib')
		{	
			$k2_dir = $k2_lib_path;
			$rp_dir = $rp_k2_assets_path.'lib/';
		}	
		else
			return false;
		
		
		
		$k2_file = $k2_dir.$file;
		$rp_k2_file = $rp_dir.$file;
		
		if(JFile::exists(JPATH_SITE.$k2_file))
			$file_path = $k2_file;
		elseif(JFile::exists(JPATH_SITE.$rp_k2_file))
			$file_path = $rp_k2_file;
		else
			return false;
			
		if($url_path)
			$file_path = JURI::root(true).$file_path;
			//$file_path = JURI::root().$file_path;
		else
			$file_path = JPATH_SITE.$file_path;
			
		return $file_path;
	}
	
	function get_allowed_video_types()
	{		
		$extensions = array("flv", "mp4", "ogv", "webm", "f4v", 
								"m4v", "3gp", "3g2", "mov", "mpeg", 
								"mpg", "avi", "wmv", "divx"
							);
		return $extensions;
	}
	function get_allowed_audio_types()
	{
		$extensions = array("mp3", "aac", "m4a", "ogg", "wma");
		return $extensions;
	}

}
