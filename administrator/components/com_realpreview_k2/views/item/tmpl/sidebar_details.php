<?php
// no direct access
defined('_JEXEC') or die('Restricted access');

?>
<h3><a href="#"><?php echo JText::_('RPK2_ITEM_INFO'); ?></a></h3>
<div>
<table class="admintable">
<tr>
	<td>
		<strong><?php echo JText::_('K2_ITEM_ID'); ?></strong>
	</td>
	<td>
		<?php echo $this->row->itemid; ?>
	</td>
</tr>
<tr>
	<td><strong><?php echo JText::_('RPK2_VERSION'); ?></strong></td>
	<td><?php echo $this->row->version; ?></td>
</tr>
<tr>
	<td><strong><?php echo JText::_('RPK2_STATE'); ?></strong></td>
	<td><?php echo ($this->row->published > 0) ? JText::_('RPK2_PUBLISHED') : JText::_('RPK2_DRAFT'); ?></td>
</tr>
<tr>
	<td>
		<strong><?php echo JText::_('K2_FEATURED'); ?></strong>
	</td>
	<td>
		<?php echo ($this->row->featured > 0) ? JText::_('K2_YES'):	JText::_('K2_NO'); ?>
	</td>
</tr>
<tr>
	<td>
		<strong><?php echo JText::_('K2_CREATED_DATE'); ?></strong>
	</td>
	<td>
		<?php echo $this->lists['created']; ?>
	</td>
</tr>
<tr>
	<td>
		<strong><?php echo JText::_('K2_CREATED_BY'); ?></strong>
	</td>
	<td>
		<?php echo $this->row->author; ?>
	</td>
</tr>
<tr>
	<td>
		<strong><?php echo JText::_('K2_MODIFIED_DATE'); ?></strong>
	</td>
	<td>
		<?php echo $this->lists['modified']; ?>
	</td>
</tr>
<tr>
	<td>
		<strong><?php echo JText::_('K2_MODIFIED_BY'); ?></strong>
	</td>
	<td>
		<?php echo $this->row->moderator; ?>
	</td>
</tr>
<tr>
	<td>
		<strong><?php echo JText::_('K2_HITS'); ?></strong>
	</td>
	<td>
		<?php echo $this->row->hits; ?>
		<?php if($this->row->hits): ?>
		<input id="resetHitsButton" type="button" value="<?php echo JText::_('K2_RESET'); ?>" class="button" name="resetHits" />
		<?php endif; ?>
	</td>
</tr>
<?php /* ?>
<?php if($this->row->id): ?>
<tr>
	<td>
		<strong><?php echo JText::_('K2_RATING'); ?></strong>
	</td>
	<td>
		<?php echo $this->row->ratingCount; ?> <?php echo JText::_('K2_VOTES'); ?>
		<?php if($this->row->ratingCount): ?>
		<br />
		(<?php echo JText::_('K2_AVERAGE_RATING'); ?>: <?php echo number_format(($this->row->ratingSum/$this->row->ratingCount),2); ?>/5.00)
		<?php endif; ?>
		<input id="resetRatingButton" type="button" value="<?php echo JText::_('K2_RESET'); ?>" class="button" name="resetRating" />
	</td>
</tr>
*/ ?>
</table>
</div>