<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Canned Responses')?></h2>
	</div>
	<div class="table-responsive">
		<table class="table table-striped table-hover">
			<tbody>
				<tr>
					<th><?=$this->Paginator->sort('id')?></th>
					<th><?=$this->Paginator->sort('name')?></th>
					<th class="text-right"><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach($supportCannedAnswers as $answer): ?>
					<tr>
						<td><?=h($answer['SupportCannedAnswer']['id'])?></td>
						<td><?=h($answer['SupportCannedAnswer']['name'])?></td>
						<td class="text-right">
							<?=
								$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit').'"></i>', array('action' => 'edit',  $answer['SupportCannedAnswer']['id']), array('escape' => false))
							?>
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete').'"></i>',
									array('action' => 'delete', $answer['SupportCannedAnswer']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to delete "%s"?', $answer['SupportCannedAnswer']['name'])
								);
							?>
						</td>
					</tr>
				<?php endforeach;?>
			</tbody>
		</table>
	</div>
	<div class="col-sm-12 text-right">
		<?=
			$this->Paginator->counter(array(
				'format' => __d('admin', 'Page {:page} of {:pages}')
			))
		?>
	</div>
	<div class="col-sm-12 text-center paddingten">
		<nav>
			<ul class="pagination pagination-sm">
				<?php
					echo $this->Paginator->first('&laquo;', array('tag' => 'li', 'escape' => false));
					echo $this->Paginator->numbers(array('separator' => '', 'tag' => 'li', 'currentClass' => 'active', 'currentTag' => 'a'));
					echo $this->Paginator->last('&raquo;', array('tag' => 'li', 'escape' => false));
				?>
			</ul>
		</nav>
	</div>
	<div class="clearfix"></div>
	<div class="text-center col-md-12 paddingten">
		<?=
			$this->Html->link(__d('admin', 'Add New Canned Response'), array('action' => 'add'), array(
				'class' => 'btn btn-primary',
			))
		?>
	</div>
</div>
