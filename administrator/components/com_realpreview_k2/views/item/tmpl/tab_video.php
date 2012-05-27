<?php 
defined('_JEXEC') or die('Restricted access');
?>
<div class="simpleTabsContent" id="k2Tab4">
<?php if ($this->lists['checkAllVideos']): ?>
<table class="admintable" id="item_video_content">
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_SOURCE'); ?>
		</td>
		<td>
			<div id="k2VideoTabs" class="simpleTabs">
				<ul class="simpleTabsNavigation">
					<li><a href="#k2VideoTab1"><?php echo JText::_('K2_UPLOAD'); ?></a></li>
					<li><a href="#k2VideoTab2"><?php echo JText::_('K2_BROWSE_SERVERUSE_REMOTE_MEDIA'); ?></a></li>
					<li><a href="#k2VideoTab3"><?php echo JText::_('K2_MEDIA_USE_ONLINE_VIDEO_SERVICE'); ?></a></li>
					<li><a href="#k2VideoTab4"><?php echo JText::_('K2_EMBED'); ?></a></li>
				</ul>
				<div id="k2VideoTab1" class="simpleTabsContent">
					<div class="panel" id="Upload_video">
						<input type="file" name="video" class="fileUpload" />
						<i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i> </div>
				</div>
				<div id="k2VideoTab2" class="simpleTabsContent">
				<?php /*	<div class="panel" id="Remote_video"> <a id="k2MediaBrowseServer" href="index.php?option=com_k2&view=media&type=video&tmpl=component&fieldID=remoteVideo"><?php echo JText::_('K2_BROWSE_VIDEOS_ON_SERVER')?></a> <?php echo JText::_('K2_OR'); ?> <?php echo JText::_('K2_PASTE_REMOTE_VIDEO_URL'); ?> */ ?>
					<div class="panel" id="Remote_video"> <a id="rpk2MediaBrowseServer" href="index.php?option=<?php echo $option;?>&view=media&type=video&tmpl=component&fieldID=remoteVideo"><?php echo JText::_('K2_BROWSE_VIDEOS_ON_SERVER')?></a> <?php echo JText::_('K2_OR'); ?> <?php echo JText::_('K2_PASTE_REMOTE_VIDEO_URL'); ?>
						<br />
						<br />
						<input type="text" size="50" name="remoteVideo" id="remoteVideo" value="<?php echo $this->lists['remoteVideo'] ?>" />
					</div>
				</div>
				<div id="k2VideoTab3" class="simpleTabsContent">
					<div class="panel" id="Video_from_provider"> <?php echo JText::_('K2_SELECT_VIDEO_PROVIDER'); ?> <?php echo $this->lists['providers']; ?> <?php echo JText::_('K2_AND_ENTER_VIDEO_ID'); ?>
						<input type="text" name="videoID" value="<?php echo $this->lists['providerVideo'] ?>" />
						<br />
						<br />
						<a class="modal" rel="{handler: 'iframe', size: {x: 990, y: 600}}" href="http://www.joomlaworks.gr/allvideos-documentation"><?php echo JText::_('K2_READ_THE_ALLVIDEOS_DOCUMENTATION_FOR_MORE'); ?></a> </div>
				</div>
				<div id="k2VideoTab4" class="simpleTabsContent">
					<div class="panel" id="embedVideo">
						<?php echo JText::_('K2_PASTE_HTML_EMBED_CODE_BELOW'); ?>
						<br />
						<textarea name="embedVideo" rows="5" cols="50" class="textarea"><?php echo $this->lists['embedVideo']; ?></textarea>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_CAPTION'); ?>
		</td>
		<td>
			<input type="text" name="video_caption" size="50" class="text_area" value="<?php echo $this->row->video_caption; ?>" />
		</td>
	</tr>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_CREDITS'); ?>
		</td>
		<td>
			<input type="text" name="video_credits" size="50" class="text_area" value="<?php echo $this->row->video_credits; ?>" />
		</td>
	</tr>
	<?php if($this->row->video): ?>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_PREVIEW'); ?>
		</td>
		<td>
			<?php echo $this->row->video; ?>
			<br />
			<input type="checkbox" name="del_video" id="del_video" />
			<label for="del_video"><?php echo JText::_('K2_CHECK_THIS_BOX_TO_DELETE_CURRENT_VIDEO_OR_USE_THE_FORM_ABOVE_TO_REPLACE_THE_EXISTING_ONE'); ?></label>
		</td>
	</tr>
	<?php endif; ?>
</table>
<?php else: ?>
<dl id="system-message">
	<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
	<dd class="notice message fade">
		<ul>
			<li><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_ALLVIDEOS_PLUGIN_IF_YOU_WANT_TO_USE_THE_FULL_VIDEO_FEATURES_OF_K2'); ?></li>
		</ul>
	</dd>
</dl>
<table class="admintable" id="item_video_content">
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_SOURCE'); ?>
		</td>
		<td>
			<div id="k2VideoTabs" class="simpleTabs">
				<ul class="simpleTabsNavigation">
					<li><a href="#k2VideoTab4"><?php echo JText::_('K2_EMBED'); ?></a></li>
				</ul>
				<div class="simpleTabsContent" id="k2VideoTab4">
					<div class="panel" id="embedVideo">
						<?php echo JText::_('K2_PASTE_HTML_EMBED_CODE_BELOW'); ?>
						<br />
						<textarea name="embedVideo" rows="5" cols="50" class="textarea"><?php echo $this->lists['embedVideo']; ?></textarea>
					</div>
				</div>
			</div>
		</td>
	</tr>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_CAPTION'); ?>
		</td>
		<td>
			<input type="text" name="video_caption" size="50" class="text_area" value="<?php echo $this->row->video_caption; ?>" />
		</td>
	</tr>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_CREDITS'); ?>
		</td>
		<td>
			<input type="text" name="video_credits" size="50" class="text_area" value="<?php echo $this->row->video_credits; ?>" />
		</td>
	</tr>
	<?php if($this->row->video): ?>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_MEDIA_PREVIEW'); ?>
		</td>
		<td>
			<?php echo $this->row->video; ?>
			<br />
			<input type="checkbox" name="del_video" id="del_video" />
			<label for="del_video"><?php echo JText::_('K2_USE_THE_FORM_ABOVE_TO_REPLACE_THE_EXISTING_VIDEO_OR_CHECK_THIS_BOX_TO_DELETE_CURRENT_VIDEO'); ?></label>
		</td>
	</tr>
	<?php endif; ?>
</table>
<?php endif; ?>
<?php if (count($this->K2PluginsItemVideo)): ?>
<div class="itemPlugins">
	<?php foreach($this->K2PluginsItemVideo as $K2Plugin): ?>
	<?php if(!is_null($K2Plugin)): ?>
	<fieldset>
		<legend><?php echo $K2Plugin->name; ?></legend>
		<?php echo $K2Plugin->fields; ?>
	</fieldset>
	<?php endif; ?>
	<?php endforeach; ?>
</div>
<?php endif; ?>
</div>