<?php 
defined('_JEXEC') or die('Restricted access');
?>
<div class="simpleTabsContent" id="k2Tab5">
<div id="extraFieldsContainer">
	<?php if (count($this->extraFields)): ?>
	<table class="admintable" id="extraFields">
		<?php foreach($this->extraFields as $extraField): ?>
		<tr>
			<td align="right" class="key">
				<?php echo $extraField->name; ?>
			</td>
			<td>
				<?php echo $extraField->element; ?>
			</td>
		</tr>
		<?php endforeach; ?>
	</table>
	<?php else: ?>
	<dl id="system-message">
		<dt class="notice"><?php echo JText::_('K2_NOTICE'); ?></dt>
		<dd class="notice message fade">
			<ul>
				<li><?php echo JText::_('K2_PLEASE_SELECT_A_CATEGORY_FIRST_TO_RETRIEVE_ITS_RELATED_EXTRA_FIELDS'); ?></li>
			</ul>
		</dd>
	</dl>
	<?php endif; ?>
</div>
<?php if (count($this->K2PluginsItemExtraFields)): ?>
<div class="itemPlugins">
	<?php foreach($this->K2PluginsItemExtraFields as $K2Plugin): ?>
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