<?php
/**
 * @version		$Id: view.html.php 549 2010-08-30 15:39:45Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2010 JoomlaWorks, a business unit of Nuevvo Webware Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JViewItem extends JView
{
	function display($tpl = null)
	{
		$mainframe = &JFactory::getApplication();
		$is_j16 = RealpreviewHelper::is_j16();
		
		$itemid = JRequest::getInt('itemid', 0);
		$option = JRequest::getVar('option');
		if(!$itemid)
		{
			JToolBarHelper::title(JText::_('RPK2_REAL_PREVIEW'),'addedit.png');
			JToolBarHelper::preferences($option, '400');
			JError::raiseNotice( 100, JText::_('RPK2_SETTINGS_MSG' ));
			return;
		}
			
		
		$db = & JFactory::getDBO();
		jimport('joomla.filesystem.file');
		jimport('joomla.html.pane');
		JHTML::_('behavior.keepalive');
		JRequest::setVar('hidemainmenu', 1);
		$document = &JFactory::getDocument();
		
		$asset_path = JURI::root(true).'/administrator/components/com_realpreview_k2/assets/';
		$script_file = $asset_path.'js/jquery-1.7.1.js';
		$document->addScript($script_file);
		$script_file = $asset_path.'js/jquery-ui-1.8.16.min.js';
		$document->addScript($script_file);
		$script_file = $asset_path.'js/item.js';
		$document->addScript($script_file);
                
                
                $document->addStyleSheet($asset_path.'/css/realpreview.css');
		
		$script_file = RealpreviewHelper::get_k2_file('nicEdit.js');
		if($script_file)$document->addScript($script_file);
		
		$js ="
		var K2SitePath = '".JURI::root(true)."/';
		var K2BasePath = '".JURI::base(true)."/';
		var K2Language = [
		'".JText::_('K2_REMOVE', true)."',
		'".JText::_('K2_LINK_TITLE_OPTIONAL',true)."',
		'".JText::_('K2_LINK_TITLE_ATTRIBUTE_OPTIONAL',true)."',
		'".JText::_('K2_ARE_YOU_SURE', true)."',
		'".JText::_('K2_YOU_ARE_NOT_ALLOWED_TO_POST_TO_THIS_CATEGORY', true)."',
		'".JText::_('K2_OR_SELECT_A_FILE_ON_THE_SERVER', true)."',
		]
		";
		
				
		$script_file = RealpreviewHelper::get_k2_file('k2.js');
		if($script_file)$document->addScript($script_file);
		//require_once(dirname(__FILE__).'/tmpl/k2_script.php');
		
		$script_file = RealpreviewHelper::get_k2_file('k2.css');
		if($script_file)$document->addStyleSheet($script_file);
		
		$document->addScriptDeclaration($js);	
		$model = & $this->getModel();
		$item = $model->getData();
		$version_list = $model->getAllVersions();
		
		JFilterOutput::objectHTMLSafe( $item, ENT_QUOTES, 'video' );
		$user = & JFactory::getUser();
/*
		if ( JTable::isCheckedOut($user->get ('id'), $item->checked_out )) {
			$message = JText::_('K2_THE_ITEM').': '.$item->title.' '.JText::_('K2_IS_CURRENTLY_BEING_EDITED_BY_ANOTHER_ADMINISTRATOR');
			$url = ($mainframe->isSite())?'index.php?option=com_k2&view=item&id='.$item->id.'&tmpl=component':'index.php?option=com_k2';
			$mainframe->redirect($url, $message);
		}
*/
		if ($item->id){
			//$item->checkout($user->get('id'));
		}
		else {
			$item->published = 1;
			$item->publish_down = $db->getNullDate();
			$item->modified = $db->getNullDate();
			$date =& JFactory::getDate();
			$now = $date->toMySQL();
			$item->created = $now;
			$item->publish_up = $item->created;
		}

		$lists = array ();
		if($is_j16) {
			//$rpk2_icon = 'article-add.png';
			$rpk2_icon = 'article.png';
			$dateFormat = JText::_('K2_J16_DATE_FORMAT_CALENDAR');
		}
		else {
			$rpk2_icon = 'addedit.png';
			$dateFormat = JText::_('K2_DATE_FORMAT_CALENDAR');
		}
		$item->publish_up = JHTML::_('date', $item->publish_up, $dateFormat);
		if($item->publish_down == $db->getNullDate()) {
			$item->publish_down = '';
		}
		else {
			$item->publish_down = JHTML::_('date', $item->publish_down, $dateFormat);
		}

		// Set up calendars
		$created = JHTML::_('date', $item->created, $dateFormat);
		$lists['createdCalendar'] = JHTML::_( 'calendar', $created, 'created', 'created');
		$lists['publish_up'] = JHTML::_( 'calendar', $item->publish_up, 'publish_up', 'publish_up');
		$lists['publish_down'] = JHTML::_( 'calendar', $item->publish_down, 'publish_down', 'publish_down');

		if($item->id){
		    $lists['created'] = JHTML::_('date', $item->created, JText::_('DATE_FORMAT_LC2'));
		}
		else {
		    $lists['created'] = JText::_('RPK2_NEW_DRAFT');
		}

		if($item->modified==$db->getNullDate() || !$item->id){
		    $lists['modified'] = JText::_('RPK2_NEVER');
		}
		else {
		    $lists['modified'] = JHTML::_('date', $item->modified, JText::_('DATE_FORMAT_LC2'));
		}

		$params = & JComponentHelper::getParams('com_k2');
		$wysiwyg = & JFactory::getEditor();

		if ($params->get("mergeEditors")){

			if (JString::strlen($item->fulltext) > 1) {
				$textValue = $item->introtext."<hr id=\"system-readmore\" />".$item->fulltext;
			}
			else {
				$textValue = $item->introtext;
			}
			$text = $wysiwyg->display('text', $textValue, '100%', '400px', '', '');
			$this->assignRef('text', $text);
		}

		else {
			$introtext = $wysiwyg->display('introtext', $item->introtext, '100%', '400px', '', '', array('readmore'));
			$this->assignRef('introtext', $introtext);
			$fulltext = $wysiwyg->display('fulltext', $item->fulltext, '100%', '400px', '', '', array('readmore'));
			$this->assignRef('fulltext', $fulltext);
		}


		$lists['published'] = JHTML::_('select.booleanlist', 'published', 'class="inputbox"', $item->published);
		$lists['featured'] = JHTML::_('select.booleanlist', 'featured', 'class="inputbox"', $item->featured);
		$lists['access'] = JHTML::_('list.accesslevel', $item);

		$query = "SELECT ordering AS value, title AS text FROM #__k2_items WHERE catid={$item->catid}";
		$lists['ordering'] = JHTML::_('list.specificordering', $item, $item->id, $query);

		if(!$item->id)
		$item->catid = $mainframe->getUserStateFromRequest('com_k2itemsfilter_category', 'catid',0, 'int');

		require_once (K2_COMPONENT_PATH_ADMIN.DS.'models'.DS.'categories.php');
		$categoriesModel = new K2ModelCategories;
		$categories = $categoriesModel->categoriesTree();
		$lists['catid'] = JHTML::_('select.genericlist', $categories, 'catid', 'class="inputbox"', 'value', 'text', $item->catid);

		if($is_j16) {
			$languages = JHTML::_('contentlanguage.existing', true, true);
			$lists['language'] = JHTML::_('select.genericlist', $languages, 'language', '', 'value', 'text', $item->language);
		}

		$lists['checkSIG']=$model->checkSIG();
		$lists['checkAllVideos']=$model->checkAllVideos();

		$remoteVideo = false;
		$providerVideo = false;
		$embedVideo = false;

		if (stristr($item->video,'remote}') !== false) {
			$remoteVideo = true;
			$options['startOffset']= 1;
		}

		$providers = $model->getVideoProviders();

		if (count($providers)){

			foreach ($providers as $provider){
				$providersOptions[] = JHTML::_('select.option', $provider, ucfirst($provider));
				if (stristr($item->video,"{{$provider}}") !== false) {
					$providerVideo = true;
					$options['startOffset']= 2;
				}
			}

		}

		if (JString::substr($item->video, 0, 1) !== '{') {
			$embedVideo = true;
			$options['startOffset']= 3;
		}

		$lists['uploadedVideo'] = (!$remoteVideo && !$providerVideo && !$embedVideo) ? true : false;

		if ($lists['uploadedVideo'] || $item->video==''){
			$options['startOffset']= 0;
		}
		
		$document->addScriptDeclaration("var K2ActiveVideoTab = ".$options['startOffset']);

		$lists['remoteVideo'] = ($remoteVideo)?preg_replace('%\{[a-z0-9-_]*\}(.*)\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['remoteVideoType'] = ($remoteVideo)?preg_replace('%\{([a-z0-9-_]*)\}.*\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['providerVideo'] = ($providerVideo)?preg_replace('%\{[a-z0-9-_]*\}(.*)\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['providerVideoType'] = ($providerVideo)?preg_replace('%\{([a-z0-9-_]*)\}.*\{/[a-z0-9-_]*\}%i', '\1', $item->video):'';
		$lists['embedVideo'] = ($embedVideo)?$item->video:'';

		if (isset($providersOptions)){
			$lists['providers'] = JHTML::_('select.genericlist', $providersOptions, 'videoProvider', '', 'value', 'text', $lists['providerVideoType']);
		}

		JPluginHelper::importPlugin ('content', 'jw_sigpro');
		JPluginHelper::importPlugin ('content', 'jw_allvideos');

		$dispatcher = &JDispatcher::getInstance ();

		// Detect gallery type
		if(JString::strpos($item->gallery, 'http://')) {
			$item->galleryType = 'flickr';
			$item->galleryValue = JString::substr($item->gallery, 9);
			$item->galleryValue = JString::substr($item->galleryValue, 0, -10);
		}
		else {
			$item->galleryType = 'server';
			$item->galleryValue = '';
		}

		//$params->set('galleries_rootfolder', 'media/k2/galleries');
		$params->set('galleries_rootfolder', 'media/realpreview/k2/galleries');
		$params->set('thb_width', '150');
		$params->set('thb_height', '120');
		$params->set('enabledownload', '0');
		$item->text=$item->gallery;
		$dispatcher->trigger ( 'onPrepareContent', array (&$item, &$params, null ) );
		$item->gallery=$item->text;

		if(!$embedVideo){
			$params->set('vfolder', 'media/k2/videos');
			$params->set('afolder', 'media/k2/audio');
			if(JString::strpos($item->video, 'remote}')){
				preg_match("#}(.*?){/#s",$item->video, $matches);
				if(JString::substr($matches[1], 0, 7)!='http://')
				$item->video = str_replace($matches[1], JURI::root().$matches[1], $item->video);
			}
			$item->text=$item->video;
			$dispatcher->trigger ( 'onPrepareContent', array (&$item, &$params, null ) );
			$item->video=$item->text;
		} else {
			// no nothing
		}

		if (isset($item->created_by)) {
			$author= & JUser::getInstance($item->created_by);
			$item->author=$author->name;
		}
		else {
			$item->author=$user->name;
		}
		if (isset($item->modified_by)) {
			$moderator = & JUser::getInstance($item->modified_by);
			$item->moderator=$moderator->name;
		}

		if($item->id){
			$active = $item->created_by;
		}
		else {
			$active = $user->id;
		}
		$lists['authors'] = JHTML::_('list.users', 'created_by', $active, false);

		$categories_option[]=JHTML::_('select.option', 0, JText::_('K2_SELECT_CATEGORY'));
		$categories = $categoriesModel->categoriesTree(NUll, true, false);
		$categories_options=@array_merge($categories_option, $categories);
		$lists['categories'] = JHTML::_('select.genericlist', $categories_options, 'catid', '', 'value', 'text', $item->catid);

		JTable::addIncludePath(K2_COMPONENT_PATH_ADMIN.DS.'tables');
		$category = & JTable::getInstance('K2Category', 'Table');
		$category->load($item->catid);

		require_once(RPREVIEW_K2_ADMIN_PATH.DS.'models'.DS.'extrafield.php');
		//require_once(K2_COMPONENT_PATH_ADMIN.DS.'models'.DS.'extrafield.php');
		$script_file = RealpreviewHelper::get_k2_file('JSON.php','lib');
		if($script_file)require_once($script_file);
				
		$extraFieldModel= new JModelExtraField;
		//$k2extraFieldModel= new K2ModelExtraField;
		if($item->id)
			$extraFields = $extraFieldModel->getExtraFieldsByGroup($category->extraFieldsGroup);
			//$extraFields = $k2extraFieldModel->getExtraFieldsByGroup($category->extraFieldsGroup);
		else $extraFields = NULL;


		for($i=0; $i<sizeof($extraFields); $i++){
			$extraFields[$i]->element=$extraFieldModel->renderExtraField($extraFields[$i],$item->id);
			//$extraFields[$i]->element=$k2extraFieldModel->renderExtraField($extraFields[$i],$item->id);
		}

	//========== attachments ==========
		if($item->id){
			$item->attachments=$model->getAttachments($item->attachments);
/*			
			$rating = $model->getRating();
			if(is_null($rating)){
				$item->ratingSum = 0;
				$item->ratingCount = 0;
			}
			else{
				$item->ratingSum = (int)$rating->rating_sum;
				$item->ratingCount = (int)$rating->rating_count;
			}
*/			
		}
		else {
			$item->attachments = NULL;
			//$item->ratingSum = 0;
			//$item->ratingCount = 0;
		}
		
		$item->ratingSum = 0;
		$item->ratingCount = 0;

	//======== end attachments ========
	//============ tags =========
        if($user->gid<24 && $params->get('lockTags'))
			$params->set('taggingSystem',0);

		//$tags=$model->getAvailableTags($item->itemid);
		$tags=$model->getAvailableTags($item->tags);
		$lists['tags'] = JHTML::_ ( 'select.genericlist', $tags, 'tags', 'multiple="multiple" size="10" ', 'id', 'name' );

		if (isset($item->id)){
			//$item->tags=$model->getCurrentTags($item->id);
			$item->tags=$model->getCurrentTags($item->tags);
			$lists['selectedTags'] = JHTML::_ ( 'select.genericlist', $item->tags, 'selectedTags[]', 'multiple="multiple" size="10" ', 'id', 'name' );
		}
		else {
			$lists['selectedTags']='<select size="10" multiple="multiple" id="selectedTags" name="selectedTags[]"></select>';
		}

		$lists['metadata']=new JParameter($item->metadata);

	//=============== end tags =========
	
		$date =& JFactory::getDate($item->modified);
		$timestamp = '?t='.$date->toUnix();
		//$timestamp=time();
		//$random_str = "?".$timestamp;
		$random_str = $timestamp;
		
		$cache_path =RPREVIEW_K2_MEDIA_PATH.'items/cache/';
		$cache_url = RPREVIEW_K2_MEDIA_URL.'items/cache/';
		$img_name = md5("Image".$item->id);
		
		if (JFile::exists($cache_path.$img_name.'_L.jpg'))
		{
			$item->image = $cache_url.$img_name.'_L.jpg';
			$item->image .= $random_str;
		}
		if (JFile::exists($cache_path.$img_name.'_S.jpg'))
		{	
			$item->thumb = $cache_url.$img_name.'_S.jpg';
			$item->thumb .= $random_str;
		}

		JPluginHelper::importPlugin ( 'k2' );
		$dispatcher = &JDispatcher::getInstance ();

		$K2PluginsItemContent=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'content' ) );
		$this->assignRef('K2PluginsItemContent', $K2PluginsItemContent);

		$K2PluginsItemImage=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'image' ) );
		$this->assignRef('K2PluginsItemImage', $K2PluginsItemImage);

		$K2PluginsItemGallery=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'gallery' ) );
		$this->assignRef('K2PluginsItemGallery', $K2PluginsItemGallery);

		$K2PluginsItemVideo=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'video' ) );
		$this->assignRef('K2PluginsItemVideo', $K2PluginsItemVideo);

		$K2PluginsItemExtraFields=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'extra-fields' ) );
		$this->assignRef('K2PluginsItemExtraFields', $K2PluginsItemExtraFields);

		$K2PluginsItemAttachments=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'attachments' ) );
		$this->assignRef('K2PluginsItemAttachments', $K2PluginsItemAttachments);

		$K2PluginsItemOther=$dispatcher->trigger('onRenderAdminForm', array (&$item, 'item', 'other' ) );
		$this->assignRef('K2PluginsItemOther', $K2PluginsItemOther);

		if($is_j16){
			jimport('joomla.form.form');
			$form = JForm::getInstance('itemForm', K2_COMPONENT_PATH_ADMIN.DS.'models'.DS.'item.xml');
			//$values = array('params'=>json_decode($item->params));
			$json_values = RealpreviewHelper::php_json_decode($item->params);
			$values = array('params'=>$json_values);
			$form->bind($values);
		}
		else {
			$form = new JParameter('', K2_COMPONENT_PATH_ADMIN.DS.'models'.DS.'item.xml');
			$form->loadINI($item->params);
		}
		$this->assignRef('form', $form);

		$nullDate = $db->getNullDate();
		$this->assignRef('nullDate', $nullDate);
		$this->assignRef('extraFields', $extraFields);
		$this->assignRef('options', $options);
		$this->assignRef('row', $item);
		$this->assignRef('lists', $lists);
		$this->assignRef('params', $params);
		$this->assignRef('user', $user);
		$this->assignRef('mainframe', $mainframe);
		
		$this->params->set('showImageTab', true);
		$this->params->set('showImageGalleryTab', true);
		$this->params->set('showVideoTab', true);
		$this->params->set('showExtraFieldsTab', true);
		$this->params->set('showAttachmentsTab', true);
		$this->params->set('showK2Plugins', true);
		
		$page_title = ( (int)$item->id ? JText::_( 'RPK2_EDIT' ) : JText::_( 'RPK2_NEW' ) );
		JToolBarHelper::title( JText::_( 'RPK2_DRAFT' ).': <small><small>[ '. $page_title.' ]</small></small>', $rpk2_icon );
		
		JToolBarHelper::custom('publishdraft','publish.png','publish_f2.png','Publish', false);
		
		$rp_params = &JComponentHelper::getParams( 'com_realpreview_k2' );
		$linktype = (int)$rp_params->get( 'targettype' , 1);
		
		$this->assignRef('linktype', $linktype);
		
		$Toolbarpreviewurl = JURI::root()."index.php?option=com_k2";
		$Toolbarpreviewurl .= "&view=item&layout=item&id={$item->itemid}";
		$Toolbarpreviewurl .= "&version={$item->version}";
		
		$bar=& JToolBar::getInstance( 'toolbar' );
		$preview_txt = JText::_('RPK2_PREVIEW');
		
		if($linktype)
			$bar->appendButton( 'Popup', 'preview', $preview_txt, $Toolbarpreviewurl, 1000, 600 );
		else
		{
			$preview_html = '<a class="toolbar" target="_blank"  href="'.$Toolbarpreviewurl.'">';
			$preview_html .= '<span title="'.$preview_txt.'" class="icon-32-preview"></span>';
			$preview_html .=$preview_txt.'</a>';
			$bar->appendButton( 'Custom', $preview_html, 'preview' );
			
		}		
		//JToolBarHelper::custom('preview','preview.png','preview_f2.png','Preview', false);
		if($is_j16)
		{
			$save_txt = JText::_('RPK2_SAVE_AND_CLOSE_DRAFT');
			$apply_txt = JText::_('RPK2_SAVE_DRAFT');
		}
		else
		{
			$save_txt = JText::_('RPK2_SAVE_DRAFT');
			$apply_txt = JText::_('RPK2_APPLY_TO_DRAFT');
		}
		
		$save_new_txt = JText::_('RPK2_SAVE_NEW_DRAFT');
		
		JToolBarHelper::save( 'saveandclose',$save_txt, 'icon-32-save.png',$save_txt, false);
		JToolBarHelper::save( 'savenewdraft',$save_new_txt, 'icon-32-save.png',$save_new_txt, false);
		
		
		if((int)$item->published)
		{
			$publish_warning = JText::_('RPK2_WARNING_SAVING_PUBLISHED_DRAFT');
			//$publish_warning = htmlspecialchars($publish_warning,ENT_QUOTES);
			$publish_warning = str_replace("'","\\'",$publish_warning);
			//$bar->appendButton( 'Confirm', $publish_warning, 'apply',$apply_txt, 'apply',false );
		}
		else
			JToolBarHelper::apply( 'apply', $apply_txt, 'icon-32-save.png', JText::_('RPK2_SAVE_DRAFT'), false);
		
		JToolBarHelper::cancel();
		
		$this->assignRef('title', $page_title);
		$this->assignRef('version_list', $version_list);

		// ACE ACL integration
		$definedConstants = get_defined_constants();
		if (!empty($definedConstants['ACEACL']) && AceaclApi::authorize('permissions', 'com_aceacl')) {
			$aceAclFlag = true;
		}
		else {
			$aceAclFlag = false;
	
	//======= end =======
		}
		$this->assignRef('aceAclFlag', $aceAclFlag);
		
		parent::display($tpl);
	}

}
