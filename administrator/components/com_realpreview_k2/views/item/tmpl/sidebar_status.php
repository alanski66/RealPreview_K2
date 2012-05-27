<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<h3><a href="#"><?php echo JText::_('K2_AUTHOR_PUBLISHING_STATUS'); ?></a></h3>
<div>
	<table class="admintable">
		<?php if(isset($this->lists['language'])): ?>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_LANGUAGE'); ?>
			</td>
			<td>
				<?php echo $this->lists['language']; ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_AUTHOR'); ?>
			</td>
			<td id="k2AuthorOptions">
				<span id="k2Author"><?php echo $this->row->author; ?></span>
				<?php if($this->mainframe->isAdmin() || ($this->mainframe->isSite() && $this->permissions->get('editAll'))): ?>
				<a class="modal" rel="{handler:'iframe', size: {x: 800, y: 460}}" href="index.php?option=com_k2&amp;view=users&amp;task=element&amp;tmpl=component">
					<?php echo JText::_('K2_CHANGE'); ?>
				</a>
				<input type="hidden" name="created_by" value="<?php echo $this->row->created_by; ?>" />
				<?php endif; ?>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_AUTHOR_ALIAS'); ?>
			</td>
			<td>
				<input class="text_area" type="text" name="created_by_alias" maxlength="250" value="<?php echo $this->row->created_by_alias; ?>" />
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_ACCESS_LEVEL'); ?>
			</td>
			<td>
				<?php echo $this->lists['access']; ?>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_CREATION_DATE'); ?>
			</td>
			<td class="k2ItemFormDateField">
				<?php echo $this->lists['createdCalendar']; ?>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_START_PUBLISHING'); ?>
			</td>
			<td class="k2ItemFormDateField">
				<?php echo $this->lists['publish_up']; ?>
			</td>
		</tr>
		<tr>
			<td align="right" class="key">
				<?php echo JText::_('K2_FINISH_PUBLISHING'); ?>
			</td>
			<td class="k2ItemFormDateField">
				<?php echo $this->lists['publish_down']; ?>
			</td>
		</tr>
	</table>
</div>