<?php 
defined('_JEXEC') or die('Restricted access');
?>
<div class="simpleTabsContent" id="k2Tab1">
<?php if($this->params->get('mergeEditors')): ?>
	<div class="k2ItemFormEditor"> <?php echo $this->text; ?>
		<div class="dummyHeight"></div>
		<div class="clr"></div>
	</div>
<?php else: ?>
	<div class="k2ItemFormEditor"> <span class="k2ItemFormEditorTitle"> <?php echo JText::_('K2_INTROTEXT_TEASER_CONTENTEXCERPT'); ?> </span> <?php echo $this->introtext; ?>
		<div class="dummyHeight"></div>
		<div class="clr"></div>
	</div>
	<div class="k2ItemFormEditor"> <span class="k2ItemFormEditorTitle"> <?php echo JText::_('K2_FULLTEXT_MAIN_CONTENT'); ?> </span> <?php echo $this->fulltext; ?>
		<div class="dummyHeight"></div>
		<div class="clr"></div>
	</div>
<?php endif; ?>
<?php if (count($this->K2PluginsItemContent)): ?>
	<div class="itemPlugins">
		<?php foreach($this->K2PluginsItemContent as $K2Plugin): ?>
			<?php if(!is_null($K2Plugin)): ?>
			<fieldset>
				<legend><?php echo $K2Plugin->name; ?></legend>
				<?php echo $K2Plugin->fields; ?>
			</fieldset>
			<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>
	<div class="clr"></div>
</div>