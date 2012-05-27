<?php 
defined('_JEXEC') or die('Restricted access');


//index.php?option={$option}&amp;view=item&amp;task=deleteAttachment&amp;id=<?php echo $attachment->id&amp;cid=$this->row->itemid;
?>
<div class="simpleTabsContent" id="k2Tab6">
<div class="itemAttachments">
	<?php if (count($this->row->attachments)): ?>
	<table class="adminlist">
		<tr>
			<th><?php echo JText::_('K2_FILENAME'); ?></th>
			<th><?php echo JText::_('K2_TITLE'); ?></th>
			<th><?php echo JText::_('K2_TITLE_ATTRIBUTE'); ?></th>
			<th><?php echo JText::_('K2_DOWNLOADS'); ?></th>
			<th><?php echo JText::_('K2_OPERATIONS'); ?></th>
		</tr>
	<?php 
		foreach($this->row->attachments as $attachment):
			$download_attachment_link =$page_link."&amp;task=download&amp;fileid={$attachment->id}";
			$delete_attachment_link = $page_link."&amp;task=deleteAttachment&amp;fileid={$attachment->id}";
	
		if(!$attachment->id and $attachment->title)
		{
			$append_to_link = '&amp;file_title='.urlencode($attachment->title);
			$download_attachment_link .= $append_to_link;
			$delete_attachment_link .= $append_to_link;
		}
	?>
		<tr>
			<td class="attachment_entry"><?php echo $attachment->filename; ?></td>
			<td><?php echo $attachment->title; ?></td>
			<td><?php echo $attachment->titleAttribute; ?></td>
			<td><?php echo $attachment->hits; ?></td>
			<td>
				<a href="<?php echo $download_attachment_link;//$attachment->link; ?>">
					<?php echo JText::_('K2_DOWNLOAD'); ?>
				</a> 
				<a class="deleteAttachmentButton" href="<?php echo $delete_attachment_link; ?>">
					<?php echo JText::_('K2_DELETE'); ?>
				</a>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php endif; ?>
</div>
<div id="addAttachment">
<?php /*	<input type="button" id="addAttachmentButton" value="<?php echo JText::_('K2_ADD_ATTACHMENT_FIELD'); ?>" /> */ ?>
	<input type="button" id="rpk2addAttachmentButton" value="<?php echo JText::_('K2_ADD_ATTACHMENT_FIELD'); ?>" />
	<i>(<?php echo JText::_('K2_MAX_UPLOAD_SIZE'); ?>: <?php echo ini_get('upload_max_filesize'); ?>)</i> </div>
<div id="itemAttachments"></div>
<?php if (count($this->K2PluginsItemAttachments)): ?>
<div class="itemPlugins">
	<?php foreach($this->K2PluginsItemAttachments as $K2Plugin): ?>
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
