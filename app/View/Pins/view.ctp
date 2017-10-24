<div class="pins view">
<h2><?php echo __('Pin'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($pin['Pin']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Location'); ?></dt>
		<dd>
			<?php echo $this->Html->link($pin['Location']['id'], array('controller' => 'locations', 'action' => 'view', $pin['Location']['id'])); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Latitude'); ?></dt>
		<dd>
			<?php echo h($pin['Pin']['latitude']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Longitude'); ?></dt>
		<dd>
			<?php echo h($pin['Pin']['longitude']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Pin'), array('action' => 'edit', $pin['Pin']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Pin'), array('action' => 'delete', $pin['Pin']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $pin['Pin']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Pins'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Pin'), array('action' => 'add')); ?> </li>
		<li><?php echo $this->Html->link(__('List Locations'), array('controller' => 'locations', 'action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Location'), array('controller' => 'locations', 'action' => 'add')); ?> </li>
	</ul>
</div>
