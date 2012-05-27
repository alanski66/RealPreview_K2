<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

?>

<h3><a href="#"><?php echo JText::_('K2_METADATA_INFORMATION'); ?></a></h3>
<div>
	<table class="admintable">
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_DESCRIPTION'); ?>
			</td>
			<td>
				<textarea name="metadesc" rows="5" cols="20"><?php echo $this->row->metadesc; ?></textarea>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_KEYWORDS'); ?>
			</td>
			<td>
				<textarea name="metakey" rows="5" cols="20"><?php echo $this->row->metakey; ?></textarea>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_ROBOTS'); ?>
			</td>
			<td>
				<input type="text" name="meta[robots]" value="<?php echo $this->lists['metadata']->get('robots'); ?>" />
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_AUTHOR'); ?>
			</td>
			<td>
				<input type="text" name="meta[author]" value="<?php echo $this->lists['metadata']->get('author'); ?>" />
			</td>
		</tr>
	</table>
</div>