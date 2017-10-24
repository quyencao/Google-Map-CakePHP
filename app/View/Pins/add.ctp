<div class="pins form">
<?php echo $this->Form->create('Pin'); ?>
	<fieldset>
		<legend><?php echo __('Add Pin'); ?></legend>
	<?php
		echo $this->Form->input('location_id');
		echo $this->Form->input('latitude');
		echo $this->Form->input('longitude');
	?>
	</fieldset>
<?php echo $this->Form->end(__('Submit')); ?>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>

		<li><?php echo $this->Html->link(__('List Pins'), array('action' => 'index')); ?></li>
		<li><?php echo $this->Html->link(__('List Locations'), array('controller' => 'locations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Location'), array('controller' => 'locations', 'action' => 'add')); ?> </li>
	</ul>
</div>
