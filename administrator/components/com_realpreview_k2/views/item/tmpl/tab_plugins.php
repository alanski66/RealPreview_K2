<?php 
defined('_JEXEC') or die('Restricted access');
?>
<div class="simpleTabsContent" id="k2Tab7">
	<div class="itemPlugins">
		<?php foreach($this->K2PluginsItemOther as $K2Plugin): ?>
		<?php if(!is_null($K2Plugin)): ?>
		<fieldset>
			<legend><?php echo $K2Plugin->name; ?></legend>
			<?php echo $K2Plugin->fields; ?>
		</fieldset>
		<?php endif; ?>
		<?php endforeach; ?>
	</div>
</div>