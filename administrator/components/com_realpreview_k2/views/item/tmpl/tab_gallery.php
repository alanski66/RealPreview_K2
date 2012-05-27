<?php 
defined('_JEXEC') or die('Restricted access');
?>
<div class="simpleTabsContent" id="k2Tab3">
<?php if ($this->lists['checkSIG']): ?>
<table class="admintable" id="item_gallery_content">
	<tr>
		<td align="right" valign="top" class="key">
			<?php echo JText::_('K2_UPLOAD_A_ZIP_FILE_WITH_IMAGES'); ?>
		</td>
		<td valign="top">
			<input type="file" name="gallery" class="fileUpload" />
			<i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i>
			<br />
			<br />
			<?php echo JText::_('K2_OR_ENTER_A_FLICKR_SET_URL'); ?>
			<input type="text" name="flickrGallery" size="50" value="<?php echo ($this->row->galleryType == 'flickr')? $this->row->galleryValue : ''; ?>" />
			<?php if (!empty($this->row->gallery)): ?>
			<div id="itemGallery"> <?php echo $this->row->gallery; ?>
				<input type="checkbox" name="del_gallery" id="del_gallery"/>
				<label for="del_gallery"><?php echo JText::_('K2_CHECK_THIS_BOX_TO_DELETE_CURRENT_IMAGE_GALLERY_OR_JUST_UPLOAD_A_NEW_IMAGE_GALLERY_TO_REPLACE_THE_EXISTING_ONE'); ?></label>
			</div>
			<?php endif; ?>
		</td>
	</tr>
</table>
<?php else: ?>
<dl id="system-message">
	<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
	<dd class="notice message fade">
		<ul>
			<li><?php echo JText::_('K2_NOTICE_PLEASE_INSTALL_JOOMLAWORKS_SIMPLE_IMAGE_GALLERY_PRO_PLUGIN_IF_YOU_WANT_TO_USE_THE_IMAGE_GALLERY_FEATURES_OF_K2'); ?></li>
		</ul>
	</dd>
</dl>
<?php endif; ?>
<?php if (count($this->K2PluginsItemGallery)): ?>
<div class="itemPlugins">
	<?php foreach($this->K2PluginsItemGallery as $K2Plugin): ?>
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
