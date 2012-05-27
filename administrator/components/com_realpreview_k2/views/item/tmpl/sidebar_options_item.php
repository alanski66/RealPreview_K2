<?php
// no direct access
defined('_JEXEC') or die('Restricted access');
 
?>	

<h3><a href="#"><?php echo JText::_('K2_ITEM_VIEW_OPTIONS'); ?></a></h3>
<div>
<?php if($is_j16): ?>
<fieldset class="panelform">
	<ul class="adminformlist">
		<?php foreach($this->form->getFieldset('item-view-options') as $field): ?>
		<li>
			<?php if($field->type=='header'): ?>
			<div class="paramValueHeader"><?php echo $field->input; ?></div>
			<?php elseif($field->type=='Spacer'): ?>
			<div class="paramValueSpacer">&nbsp;</div>
			<div class="clr"></div>
			<?php else: ?>
			<div class="paramLabel"><?php echo $field->label; ?></div>
			<div class="paramValue"><?php echo $field->input; ?></div>
			<div class="clr"></div>
			<?php endif; ?>
		</li>
		<?php endforeach; ?>
	</ul>
</fieldset>
<?php else: ?>
<?php echo $this->form->render('params', 'item-view-options'); ?>
<?php endif; ?>
</div>