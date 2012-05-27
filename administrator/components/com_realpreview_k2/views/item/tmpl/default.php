<?php
/**
 * @version		$Id: default.php 1377 2011-12-02 10:43:01Z lefteris.kavadas $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');


$document = & JFactory::getDocument();
$document->addScriptDeclaration("
	Joomla.submitbutton = function(pressbutton){
		if (pressbutton == 'cancel') {
			submitform( pressbutton );
			return;
		}
		if (\$K2.trim(\$K2('#title').val()) == '') {
			alert( '".JText::_('K2_ITEM_MUST_HAVE_A_TITLE', true)."' );
		}
		else if (\$K2.trim(\$K2('#catid').val()) == '0') {
			alert( '".JText::_('K2_PLEASE_SELECT_A_CATEGORY', true)."' );
		}
		else {
			syncExtraFieldsEditor();
			\$K2('#selectedTags option').attr('selected', 'selected');
			submitform( pressbutton );
		}
	}
");

$is_j16 = RealpreviewHelper::is_j16();
$tpl_dir = dirname(__FILE__).'/';
$option = JRequest::getVar('option');
$page_link = "index.php?option={$option}&amp;id={$this->row->id}&amp;itemid={$this->row->itemid}&amp;version={$this->row->version}";
	
?>

<form action="index.php" enctype="multipart/form-data" method="post" name="adminForm" id="adminForm">
<div id="k2AdminContainer">
<div id="k2ToggleSidebarContainer"> <a href="#" id="k2ToggleSidebar"><?php echo JText::_('K2_TOGGLE_SIDEBAR'); ?></a> </div>
<table cellspacing="0" cellpadding="0" border="0" class="adminFormK2Container">
	<tbody>
		<tr>
			<td>
				<table class="adminFormK2">
					<tr>
						<td class="adminK2LeftCol">
							<label for="title"><?php echo JText::_('K2_TITLE'); ?></label>
						</td>
						<td class="adminK2RightCol">
							<input class="text_area k2TitleBox" type="text" name="title" id="title" maxlength="250" value="<?php echo $this->row->title; ?>" />
						</td>
					</tr>
					<tr>
						<td class="adminK2LeftCol">
							<label for="alias"><?php echo JText::_('K2_TITLE_ALIAS'); ?></label>
						</td>
						<td class="adminK2RightCol">
							<input class="text_area k2TitleAliasBox" type="text" name="alias" id="alias" maxlength="250" value="<?php echo $this->row->alias; ?>" />
						</td>
					</tr>
					<tr>
						<td class="adminK2LeftCol">
							<label><?php echo JText::_('K2_CATEGORY'); ?></label>
						</td>
						<td class="adminK2RightCol">
							<?php echo $this->lists['categories']; ?>
						</td>
					</tr>
					<tr>
						<td class="adminK2LeftCol">
							<label><?php echo JText::_('K2_TAGS'); ?></label>
						</td>
						<td class="adminK2RightCol">
							<?php if($this->params->get('taggingSystem')): ?>
							<!-- Free tagging -->
							<ul class="tags">
								<?php if(isset($this->row->tags) && count($this->row->tags)): ?>
								<?php foreach($this->row->tags as $tag): ?>
								<li class="tagAdded">
									<?php echo $tag->name; ?>
									<span title="<?php echo JText::_('K2_CLICK_TO_REMOVE_TAG'); ?>" class="tagRemove">x</span>
									<input type="hidden" name="tags[]" value="<?php echo $tag->name; ?>" />
								</li>
								<?php endforeach; ?>
								<?php endif; ?>
								<li class="tagAdd">
									<input type="text" id="search-field" />
								</li>
								<li class="clr"></li>
							</ul>
							<span class="k2Note"> <?php echo JText::_('K2_WRITE_A_TAG_AND_PRESS_RETURN_OR_COMMA_TO_ADD_IT'); ?> </span>
							<?php else: ?>
							<!-- Selection based tagging -->
							<?php if( !$this->params->get('lockTags') || $this->user->gid>23): ?>
							<div style="float:left;">
								<input type="text" name="tag" id="tag" />
								<input type="button" id="newTagButton" value="<?php echo JText::_('K2_ADD'); ?>" />
							</div>
							<div id="tagsLog"></div>
							<div class="clr"></div>
							<span class="k2Note"> <?php echo JText::_('K2_WRITE_A_TAG_AND_PRESS_ADD_TO_INSERT_IT_TO_THE_AVAILABLE_TAGS_LISTNEW_TAGS_ARE_APPENDED_AT_THE_BOTTOM_OF_THE_AVAILABLE_TAGS_LIST_LEFT'); ?> </span>
							<?php endif; ?>
							<table cellspacing="0" cellpadding="0" border="0" id="tagLists">
								<tr>
									<td id="tagListsLeft">
										<span><?php echo JText::_('K2_AVAILABLE_TAGS'); ?></span> <?php echo $this->lists['tags'];	?>
									</td>
									<td id="tagListsButtons">
										<input type="button" id="addTagButton" value="<?php echo JText::_('K2_ADD'); ?> &raquo;" />
										<br />
										<br />
										<input type="button" id="removeTagButton" value="&laquo; <?php echo JText::_('K2_REMOVE'); ?>" />
									</td>
									<td id="tagListsRight">
										<span><?php echo JText::_('K2_SELECTED_TAGS'); ?></span> <?php echo $this->lists['selectedTags']; ?>
									</td>
								</tr>
							</table>
							<?php endif; ?>
						</td>
					</tr>
					<?php if($this->mainframe->isAdmin() || ($this->mainframe->isSite() && $this->permissions->get('publish'))): ?>
					<tr>
						<td class="adminK2LeftCol">
							<label for="featured"><?php echo JText::_('K2_IS_IT_FEATURED'); ?></label>
						</td>
						<td class="adminK2RightCol">
						<?php echo $this->lists['featured']; ?>
						</td>
					</tr>
					<tr>
						<td class="adminK2LeftCol">
							<label><?php echo JText::_('RPK2_STATE'); ?></label>
						</td>
						<td class="adminK2RightCol">
						<?php 
							//echo $this->lists['published']; 
							if((int)$this->row->published)
							echo '<span style="color:green;">'.JText::_('RPK2_PUBLISHED').'</span>';
							else
								echo '<span style="color:orange;">'.JText::_('RPK2_DRAFT').'</span>';
						 ?>
						</td>
					</tr>
					<?php endif; ?>
				</table>
				
				<!-- Tabs start here -->
				<div class="simpleTabs" id="k2Tabs">
					<ul class="simpleTabsNavigation">
						<li id="tabContent"><a href="#k2Tab1"><?php echo JText::_('K2_CONTENT'); ?></a></li>
						<?php if ($this->params->get('showImageTab')): ?>
						<li id="tabImage"><a href="#k2Tab2"><?php echo JText::_('K2_IMAGE'); ?></a></li>
						<?php endif; ?>
						<?php if ($this->params->get('showImageGalleryTab')): ?>
						<li id="tabImageGallery"><a href="#k2Tab3"><?php echo JText::_('K2_IMAGE_GALLERY'); ?></a></li>
						<?php endif; ?>
						<?php if ($this->params->get('showVideoTab')): ?>
						<li id="tabVideo"><a href="#k2Tab4"><?php echo JText::_('K2_MEDIA'); ?></a></li>
						<?php endif; ?>
						<?php if ($this->params->get('showExtraFieldsTab')): ?>
						<li id="tabExtraFields"><a href="#k2Tab5"><?php echo JText::_('K2_EXTRA_FIELDS'); ?></a></li>
						<?php endif; ?>
						<?php if ($this->params->get('showAttachmentsTab')): ?>
						<li id="tabAttachments"><a href="#k2Tab6"><?php echo JText::_('K2_ATTACHMENTS'); ?></a></li>
						<?php endif; ?>
						<?php if(count(array_filter($this->K2PluginsItemOther)) && $this->params->get('showK2Plugins')): ?>
						<li id="tabPlugins"><a href="#k2Tab7"><?php echo JText::_('K2_PLUGINS'); ?></a></li>
						<?php endif; ?>
					</ul>
					
<?php
	//Tab content
	require_once($tpl_dir.'tab_content.php');
	//Tab image
	if ($this->params->get('showImageTab')):
		require_once($tpl_dir.'tab_image.php');
	endif;
	//Tab image gallery
	if ($this->params->get('showImageGalleryTab')):
		require_once($tpl_dir.'tab_gallery.php');
	endif;
	//Tab video
	if ($this->params->get('showVideoTab')):
		require_once($tpl_dir.'tab_video.php');
	endif;
	//Tab extra fields
	if ($this->params->get('showExtraFieldsTab')):
		require_once($tpl_dir.'tab_extrafields.php'); 								
	endif;
	//Tab attachements
	if ($this->params->get('showAttachmentsTab')):
		require_once($tpl_dir.'tab_attachments.php'); 
	endif;
	//Tab other plugins
	if(count(array_filter($this->K2PluginsItemOther)) && $this->params->get('showK2Plugins')):
		require_once($tpl_dir.'tab_plugins.php');
	endif;
?>		
				</div>
				<!-- Tabs end here -->
				
				<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
				<input type="hidden" name="itemid" value="<?php echo $this->row->itemid; ?>" />
				<input type="hidden" name="version" value="<?php echo $this->row->version; ?>" />
				<input type="hidden" name="published" value="<?php echo $this->row->published; ?>" />
				<input type="hidden" name="ordering" value="<?php echo $this->row->ordering; ?>" />
				<input type="hidden" name="featured_ordering" value="<?php echo $this->row->featured_ordering; ?>" />

				<input type="hidden" name="option" value="<?php echo $option; ?>" />
				<input type="hidden" name="view" value="item" />
				<input type="hidden" name="task" value="<?php echo JRequest::getVar('task'); ?>" />
				<?php echo JHTML::_('form.token'); ?>
			</td>
			<td id="adminFormK2Sidebar" class="xmlParamsFields">
				
				<div id="k2Accordion">
					
				<?php 
				require_once($tpl_dir.'sidebar_version.php');
				if($this->row->itemid)
				require_once($tpl_dir.'sidebar_details.php');
				
				require_once($tpl_dir.'sidebar_status.php');
				require_once($tpl_dir.'sidebar_metadata.php');
				
				require_once($tpl_dir.'sidebar_options_category.php');
				require_once($tpl_dir.'sidebar_options_item.php');
				
				?>
					
					<?php if($this->aceAclFlag): ?>
					<h3><a href="#"><?php echo JText::_('AceACL') . ' ' . JText::_('COM_ACEACL_COMMON_PERMISSIONS'); ?></a></h3>
					<div><?php AceaclApi::getWidget('com_k2.item.'.$this->row->itemid, true); ?></div>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</tbody>
</table>
<div class="clr"></div>
</div>
</form>
