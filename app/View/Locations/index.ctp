<?= $this->Html->css('https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css'); ?>
<?= $this->Html->script("http://ajax.googleapis.com/ajax/libs/jquery/1.10.2/jquery.min.js", false); ?>
<?= $this->Html->script('http://maps.google.com/maps/api/js?key=AIzaSyALRT24edC7GaVGvOa8jABOvq4g1I20JZQ&libraries=places&sensor=true', false); ?>
<?php if(count($locations) > 0): ?>
<div class="locations index container">
	<h2><?php echo __('Danh sách các địa điểm'); ?></h2>
	<table cellpadding="0" cellspacing="0" class="table table-hover" style="min-height: 300px">
	<thead>
	<tr>
			<th><?php echo $this->Paginator->sort('id'); ?></th>
			<th><?php echo $this->Paginator->sort('Địa chỉ'); ?></th>
			<th><?php echo $this->Paginator->sort('Vĩ độ'); ?></th>
			<th><?php echo $this->Paginator->sort('Kinh độ'); ?></th>
			<th class="actions"><?php echo __('Hành động'); ?></th>
	</tr>
	</thead>
	<tbody>
	<?php foreach ($locations as $location): ?>
        <tr>
            <td><?php echo h($location['Location']['id']); ?>&nbsp;</td>
            <td><?php echo h($location['Location']['address']); ?>&nbsp;</td>
            <td><?php echo h($location['Location']['latitude']); ?>&nbsp;</td>
            <td><?php echo h($location['Location']['longitude']); ?>&nbsp;</td>
            <td class="actions">
                <?php echo $this->Html->link(__('Xem'), array('action' => 'view', $location['Location']['id'])); ?>
                <?php echo $this->Html->link(__('Sửa'), array('action' => 'edit', $location['Location']['id'])); ?>
                <?php echo $this->Form->postLink(__('Xóa'), array('action' => 'delete', $location['Location']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $location['Location']['id']))); ?>
            </td>
        </tr>
    <?php endforeach; ?>
	</tbody>
	</table>
    <?php if($this->Paginator->params()['count'] > $this->Paginator->params()['limit']): ?>
        <nav aria-label="Page navigation example">
            <ul class="pagination d-flex flex-row justify-content-center">
                <?php if($this->Paginator->hasPrev('Location')): ?>
                    <li class="page-item"><a class="page-link" href="/locations/index/page:<?php echo $this->Paginator->current('Location') - 1; ?>">Trang trước</a></li>
                <?php else: ?>
                    <li class="page-item disabled"><a class="page-link" href="#">Trang trước</a></li>
                <?php endif; ?>
                <?php for($i = 1; $i <= $this->Paginator->params()['pageCount']; $i++): ?>
                    <li class="page-item<?php echo $this->Paginator->current('Location') == $i ? ' active' : ''; ?>"><a class="page-link" href="/locations/index/page:<?php echo $i; ?>"><?php echo $i; ?></a></li>
                <?php endfor; ?>
                <?php if($this->Paginator->hasNext('Location')): ?>
                    <li class="page-item"><a class="page-link" href="/locations/index/page:<?php echo $this->Paginator->current('Location') + 1; ?>">Trang sau</a></li>
                <?php else: ?>
                    <li class="page-item disabled"><a class="page-link" href="#">Trang sau</a></li>
                <?php endif; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
<?php else: ?>
    <h1 class="text-center">Chưa có địa điểm nào</h1>
<?php endif; ?>
