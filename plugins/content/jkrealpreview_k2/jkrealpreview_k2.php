<?php
/*
 // "YouTube Videos" Plugin by JoomlaWorks for K2 v2.x - Version 1.0
 // Copyright (c) 2006 - 2009 JoomlaWorks Ltd. All rights reserved.
 // Released under the GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 // More info at http://www.joomlaworks.gr
 // Designed and developed by the JoomlaWorks team
 // *** Last update: June 20th, 2009 ***
 */

// no direct access
defined('_JEXEC') or die ('Restricted access');

class plgContentJkrealpreview_k2 extends JPlugin {

	function plgContentJkrealpreview_k2( & $subject, $params) {
	
		parent::__construct($subject, $params);
	}

	function onContentBeforeDisplay($context, &$item, &$params, $page=0)
	{
		if($context !== 'com_k2.item')return;
		$this->onBeforeDisplay( $item, $params, $page);
		return;
	}
	function onBeforeDisplay( &$item, &$params, $limitstart) 
	{	
		//global $mainframe;
		$mainframe = &JFactory::getApplication();
		if(!$mainframe->isSite())return '';
		
		$option = JRequest::getVar('option');
		$view = JRequest::getVar('view');
		$itemid=(int)$item->id;	
		
		if($option!='com_k2' or $view!='item' or !$itemid)
			return '';
		
		$versionid = JRequest::getInt('version');
	
		if(!$versionid)return'';
		
		$rpreview_admin_dir = JPATH_ADMINISTRATOR.'/components/com_realpreview_k2/';
		
		if(!file_exists($rpreview_admin_dir.'admin.realpreview_k2.php'))
			return'';
			
		require_once($rpreview_admin_dir.'/helpers/helpers.php');
		
		$table = RealpreviewHelper::get_table_name();
		
		$db = &JFactory::getDBO();
		$q = "SELECT * FROM `$table`
				WHERE `itemid`='$itemid' 
				AND `version`='$versionid'";
			
		$db->setQuery($q);
/*		
		if (!$db->query()) {
			JError::raiseError( 500, $db->stderr() );
			return false;
		}
*/		
		$draft = $db->loadObject();
		$draft->draft_id=$draft->id;
		$draft->id=$itemid;
		$draft->published=1;
		
		if(!empty($draft))
		{
			$item = $this->prepareItem($draft,$params,$limitstart);			
		}
		return '';
	}
	
	function prepareItem($item,&$params,$limitstart)
	{		
		jimport('joomla.filesystem.file');
		JTable::addIncludePath(JPATH_COMPONENT_ADMINISTRATOR.DS.'tables');
		$limitstart=JRequest::getInt('limitstart');
	
		//Category
		$db = & JFactory::getDBO();
		$query = "SELECT * FROM #__k2_categories WHERE id=".(int)$item->catid;
		$db->setQuery($query, 0, 1);
		$category = $db->loadObject();

		$item->category=$category;
		$item->category->link=urldecode(JRoute::_(K2HelperRoute::getCategoryRoute($category->id.':'.urlencode($category->alias))));

		//Read more link
		$link = K2HelperRoute::getItemRoute($item->id.':'.urlencode($item->alias),$item->catid.':'.urlencode($item->category->alias));
		$link .= '&version='.$item->version;
		$item->link=urldecode(JRoute::_($link));

		//Print link
		$item->printLink = urldecode(JRoute::_($link.'&tmpl=component&print=1'));

		//Params
		$cparams = new JParameter( $category->params );
		$iparams = new JParameter( $item->params );
		$params = & JComponentHelper::getParams('com_k2');
		$item->params= $params;
		if ($cparams->get('inheritFrom')){
			$masterCategoryID = $cparams->get('inheritFrom');
			$query = "SELECT * FROM #__k2_categories WHERE id=".(int)$masterCategoryID;
			$db->setQuery($query, 0, 1);
			$masterCategory = $db->loadObject();
			$cparams = new JParameter( $masterCategory->params );
		}
		$item->params->merge($cparams);
		$item->params->merge($iparams);

		//Edit link
		$item->editLink ='';
		//if (K2HelperPermissions::canEditItem($item->created_by,$item->catid))
		//$item->editLink = JRoute::_('index.php?option=com_k2&view=item&task=edit&cid='.$item->itemid.'&tmpl=component');

		//image
		$this->prepare_image(&$item,&$params);
		
		//Tags
		if(($item->params->get('itemTags') or $item->params->get('itemRelated')))
			$this->prepare_tags(&$item,&$params);
		
		//Attachments
		if($item->params->get('itemAttachments'))
			$this->prepare_attachment(&$item,&$params);
			
		//Author
		if($item->params->get('itemAuthorBlock'))
			$this->prepare_author(&$item,&$params);
		
		//gallery		
		if($item->params->get('itemImageGallery'))
			$this->prepare_gallery(&$item,&$params,$limitstart);

		if($item->params->get('itemVideo'))
			$this->prepare_video(&$item,&$params,$limitstart);
		
		//text
		$this->prepare_text(&$item,&$params);
		
		
		//Extra fields
		if($item->params->get('itemExtraFields'))
		{
			$item->extra_fields=K2ModelItem::getItemExtraFields($item->extra_fields);
		}
		//Rating
		if( $item->params->get('itemRating'))
		{
			$item->votingPercentage = K2ModelItem::getVotesPercentage($item->id);
			$item->numOfvotes = K2ModelItem::getVotesNum($item->id);
		}
		//Num of comments
		$item->numOfComments = K2ModelItem::countItemComments($item->id);

		$this->load_script();
		
		return $item;
	}
	
	function prepare_tags(&$item,&$params,$limitstart=0)
	{
		$tags=array();
		if(trim($item->tags)!='')
		{			
			$query = "SELECT * FROM #__k2_tags as tags 
						WHERE tags.id IN ({$item->tags})";
			$db = & JFactory::getDBO();
			$db->setQuery($query);
			$tmp_tags = $db->loadObjectList();				
			if(is_array($tmp_tags))
				$tags = $tmp_tags;
		}
		for ($i=0; $i<sizeof($tags); $i++) {
			$tags[$i]->link = JRoute::_(K2HelperRoute::getTagRoute($tags[$i]->name));
		}
		$item->tags=$tags;
	}
	function prepare_image(&$item,&$params,$limitstart=0)
	{
		$item->imageXSmall='';
		$item->imageSmall='';
		$item->imageMedium='';
		$item->imageLarge='';
		$item->imageXLarge='';
		
		$media_path = JPATH_SITE.'/media/realpreview/k2/';
		$media_url = JURI::root(true).'/media/realpreview/k2/';
		
		$img_filename = md5("Image".$item->draft_id);
		$img_file = $media_path.'items/cache/'.$img_filename;
		$img_src = $media_url.'items/cache/'.$img_filename;

		$timestamp=time();
		$random_str = "?".$timestamp;
		
		if (JFile::exists($img_file.'_XS.jpg'))
		{
			//$item->imageXSmall = $img_src.'_XS.jpg';
			$item->imageXSmall = $img_src.'_XS.jpg'.$random_str;
		}
		if (JFile::exists($img_file.'_S.jpg'))
		{
			//$item->imageSmall = $img_src.'_S.jpg';
			$item->imageSmall = $img_src.'_S.jpg'.$random_str;
		}
		if (JFile::exists($img_file.'_M.jpg'))
		{
			//$item->imageMedium = $img_src.'_M.jpg';
			$item->imageMedium = $img_src.'_M.jpg'.$random_str;
		}
		if (JFile::exists($img_file.'_L.jpg'))
		{
			//$item->imageLarge = $img_src.'_L.jpg';
			$item->imageLarge = $img_src.'_L.jpg'.$random_str;
		}
		if (JFile::exists($img_file.'_XL.jpg'))
		{
			//$item->imageXLarge = $img_src.'_XL.jpg';
			$item->imageXLarge = $img_src.'_XL.jpg'.$random_str;
		}
		if (JFile::exists($img_file.'_Generic.jpg'))
		{
			//$item->imageGeneric = $img_src.'_Generic.jpg';
			$item->imageGeneric = $img_src.'_Generic.jpg'.$random_str;
		}
	}
	
	function prepare_attachment(&$item,&$params,$limitstart=0)
	{
		$attachments = trim($item->attachments);
		$list = array();
		if($attachments=='')
		{
			$item->attachments = array();
		}
		else
		{
			$list = RealpreviewHelper::parse_attachment_field($attachments);
			
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
						
					$obj->id .= '&draft_id='.$item->draft_id;	
					$obj->id .= "&version={$item->draft_id}&itemid={$item->id}";	
					$obj->id .= "&fileid={$array->id}";	
					
					$obj->link = "index.php?option=com_realpreview_k2&tmpl=component&id=".$obj->id;
					
					$object_list[] = $obj;
				}	
			
				$item->attachments= $object_list;
			}
		}
	}
	function prepare_text(&$item,&$params,$limitstart=0)
	{
		if ($params->get('introTextCleanup'))
		{
			$filterTags	= preg_split( '#[,\s]+#', trim( $params->get( 'introTextCleanupExcludeTags' ) ) );
			$filterAttrs = preg_split( '#[,\s]+#', trim( $params->get( 'introTextCleanupTagAttr' ) ) );
			$filter	= new JFilterInput( $filterTags, $filterAttrs, 0, 1 );
			$item->introtext= $filter->clean( $item->introtext );
		}

		if ($params->get('fullTextCleanup'))
		{
			$filterTags	= preg_split( '#[,\s]+#', trim( $params->get( 'fullTextCleanupExcludeTags' ) ) );
			$filterAttrs = preg_split( '#[,\s]+#', trim( $params->get( 'fullTextCleanupTagAttr' ) ) );
			$filter	= new JFilterInput( $filterTags, $filterAttrs, 0, 1 );
			$item->fulltext= $filter->clean( $item->fulltext );
		}

		if ($item->params->get('catItemIntroTextWordLimit') && $task=='category'){
			$item->introtext = K2HelperUtilities::wordLimit($item->introtext, $item->params->get('catItemIntroTextWordLimit'));
		}

		$item->cleanTitle = $item->title;
		$item->title = htmlspecialchars($item->title, ENT_QUOTES);
		$item->image_caption = htmlspecialchars($item->image_caption, ENT_QUOTES);

		$item->text='';
		$params->set('vfolder', NULL);
		$params->set('vwidth', NULL);
		$params->set('vheight', NULL);
		$params->set('autoplay', NULL);
		$params->set('galleries_rootfolder', NULL);
		$params->set('popup_engine', NULL);
		$params->set('enabledownload', NULL);

		
		if ($item->params->get('itemIntroText'))
			$item->text.= $item->introtext;
		if ($item->params->get('itemFullText'))
			$item->text.= '{K2Splitter}'.$item->fulltext;
		
	}
	function prepare_author(&$item,$params,$limitstart=0)
	{
		if (!empty($item->created_by_alias))
		{
			$item->author->name = $item->created_by_alias;
			$item->author->avatar = K2HelperUtilities::getAvatar('alias');
			$item->author->link = JURI::root();
		}
		else 
		{
			$author=&JFactory::getUser($item->created_by);
			$item->author = $author;
			$item->author->link = JRoute::_(K2HelperRoute::getUserRoute($item->created_by));
			$item->author->profile = K2ModelItem::getUserProfile($item->created_by);
			$item->author->avatar = K2HelperUtilities::getAvatar($author->id, $author->email, $params->get('userImageWidth'));
		}

		if(!isset($item->author->profile) || is_null($item->author->profile))
		{

			$item->author->profile = new JObject;
			$item->author->profile->gender = NULL;
		}
	}
	function prepare_gallery(&$item,&$params,$limitstart=0)
	{
		$params->set('galleries_rootfolder', 'media/realpreview/k2/galleries');
		$params->set('popup_engine', 'mootools_slimbox');
		$params->set('enabledownload', '0');
		$item->text=$item->gallery;
		
		$dispatcher = &JDispatcher::getInstance();
		JPluginHelper::importPlugin ('content');
		$dispatcher->trigger ( 'onPrepareContent', array (&$item, &$params, $limitstart ) );
		$item->gallery=$item->text;
	}
	function prepare_video(&$item,&$params,$limitstart=0)
	{	
		if (!empty($item->video) && JString::substr($item->video, 0, 1) !== '{') {
			$item->video=$item->video;
			$item->videoType='embedded';
		}
		else {
			$item->videoType='allvideos';
			$params->set('vfolder', 'media/realpreview/k2/videos');
			$params->set('afolder', 'media/realpreview/k2/audio');

			if(JString::strpos($item->video, 'remote}')){
				preg_match("#}(.*?){/#s",$item->video, $matches);
				if(JString::substr($matches[1], 0, 7)!='http://')
					$item->video = JString::str_ireplace($matches[1], JURI::root().$matches[1], $item->video);
			}

			$params->set('vwidth', $item->params->get('itemVideoWidth'));
			$params->set('vheight', $item->params->get('itemVideoHeight'));
			$params->set('autoplay', $item->params->get('itemVideoAutoPlay'));
			
			$dispatcher = &JDispatcher::getInstance();
			$item->text=$item->video;
			$dispatcher->trigger ( 'onPrepareContent', array (&$item, &$params, $limitstart ) );
			$item->video=$item->text;
		}
	}
	function load_script()
	{
		global $jkrealpreview_k2_script_loaded;
		
		if($jkrealpreview_k2_script_loaded)return;
		
		$jkrealpreview_k2_script_loaded = true;
		
		$document = &JFactory::getDocument();
		$css = "#submitCommentButton{display:none !important;}";
		$css .= "\n.itemEditLink{display:none !important;}";
		$document->addStyleDeclaration($css);
	}
	
	

} // END CLASS

