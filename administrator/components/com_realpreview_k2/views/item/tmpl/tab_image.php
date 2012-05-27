<?php 
defined('_JEXEC') or die('Restricted access');
?>
<div class="simpleTabsContent" id="k2Tab2">
<table class="admintable">
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_ITEM_IMAGE'); ?>
		</td>
		<td>
			<input type="file" name="image" class="fileUpload" />
			<i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i>
			<br />
			<br />
			<input type="text" name="existingImage" id="existingImageValue" class="text_area" readonly />
		<?php /*	<input type="button" value="<?php echo JText::_('K2_BROWSE_SERVER'); ?>" id="k2ImageBrowseServer"  /> */ ?>
			<input type="button" value="<?php echo JText::_('K2_BROWSE_SERVER'); ?>" id="rpk2ImageBrowseServer"  />
			<br />
			<br />
		</td>
	</tr>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_ITEM_IMAGE_CAPTION'); ?>
		</td>
		<td>
			<input type="text" name="image_caption" size="30" class="text_area" value="<?php echo $this->row->image_caption; ?>" />
		</td>
	</tr>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_ITEM_IMAGE_CREDITS'); ?>
		</td>
		<td>
			<input type="text" name="image_credits" size="30" class="text_area" value="<?php echo $this->row->image_credits; ?>" />
		</td>
	</tr>
	<?php if (!empty($this->row->image)): ?>
	<tr>
		<td align="right" class="key">
			<?php echo JText::_('K2_ITEM_IMAGE_PREVIEW'); ?>
		</td>
		<td>
			<a class="modal" rel="{handler: 'image'}" href="<?php echo $this->row->image; ?>" title="<?php echo JText::_('K2_CLICK_ON_IMAGE_TO_PREVIEW_IN_ORIGINAL_SIZE'); ?>"> <img alt="<?php echo $this->row->title; ?>" src="<?php echo $this->row->thumb; ?>" class="k2AdminImage"/> </a>
			<input type="checkbox" name="del_image" id="del_image" />
			<label for="del_image"><?php echo JText::_('K2_CHECK_THIS_BOX_TO_DELETE_CURRENT_IMAGE_OR_JUST_UPLOAD_A_NEW_IMAGE_TO_REPLACE_THE_EXISTING_ONE'); ?></label>
		</td>
	</tr>
	<?php endif; ?>
</table>
<?php if (count($this->K2PluginsItemImage)): ?>
<div class="itemPlugins">
	<?php foreach($this->K2PluginsItemImage as $K2Plugin): ?>
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