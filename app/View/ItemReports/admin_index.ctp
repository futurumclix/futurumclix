<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Reports list')?></h2>
	</div>
	<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'));?>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Reporter')?></label>
			<?=
				$this->AdminForm->input('Filter.Reporter.username', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter username to search'),
					'default' => isset($this->request->params['named']['Reporter.username']) ? $this->request->params['named']['Reporter.username'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-4">
			<label><?=__d('admin', 'Resolver')?></label>
			<?=
				$this->AdminForm->input('Filter.Resolver.email', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter admin e-mail to search'),
					'default' => isset($this->request->params['named']['Resolver.email']) ? $this->request->params['named']['Resolver.email'] : '',
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Status')?></label>
			<?=
				$this->AdminForm->input('Filter.ItemReport.status', array(
					'type' => 'select',
					'empty' => __d('admin', 'All'),
					'options' => $this->Utility->enum('ItemReport', 'status'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-1 text-right">
			<a href="#collapse1" data-toggle="collapse" style="position: relative; top: 30px;">
				<i id="collapse1Button" title="<?=__d('admin', 'Click to show more search options')?>" data-toggle="tooltip" data-placement="top" class="fa fa-plus-circle fa-lg"></i>
			</a>
		</div>
		<div id="collapse1" class="panel-collapse collapse <?=$searchCollapse?>">
			<div class="form-group searchform col-sm-4">
				<label><?=__d('admin', 'Type')?></label>
				<?=
					$this->AdminForm->input('Filter.ItemReport.type', array(
						'options' => $this->Utility->enum('ItemReport', 'type'),
						'empty' => __d('admin', 'All'),
					))
				?>
			</div>
			<div class="form-group searchform col-sm-4">
				<label><?=__d('admin', 'Reason')?></label>
				<?=
					$this->AdminForm->input('Filter.ItemReport.reason', array(
						'type' => 'text',
					))
				?>
			</div>
			<div class="form-group searchform col-sm-4">
				<label><?=__d('admin', 'Comment')?></label>
				<?=
					$this->AdminForm->input('Filter.ItemReport.comment', array(
						'type' => 'text',
					))
				?>
			</div>
		</div>
		<div class="clearfix"></div>
		<div class="text-center col-md-12 paddingten">
			<button class="btn btn-primary btn-sm"><?=_('Search')?></button>
		</div>
	<?=$this->AdminForm->end()?>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'massaction'),
		))
	?>
		<div class="table-responsive">
			<table class="table table-striped table-hover">
				<tbody>
					<tr>
						<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
						<th><?=$this->Paginator->sort('created', __d('admin', 'Date'))?></th>
						<th><?=$this->Paginator->sort('Reporter.username', __d('admin', 'Reporter'))?></th>
						<th><?=$this->Paginator->sort('Resolver.email', __d('admin', 'Resolver'))?></th>
						<th><?=$this->Paginator->sort('status')?></th>
						<th><?=$this->Paginator->sort('type')?></th>
						<th><?=$this->Paginator->sort('item')?></th>
						<th><?=$this->Paginator->sort('reason')?></th>
						<th><?=$this->Paginator->sort('comment')?></th>
						<th><?=__d('admin', 'Actions')?></th>
					</tr>
					<?php foreach($itemReports as $report): ?>
					<tr>
						<td>
							<?=
								$this->AdminForm->checkbox('ItemReport.'.$report['ItemReport']['id'], array(
									'class' => 'ActionCheckbox',
								))
							?>
						</td>
						<td><?=h($report['ItemReport']['created'])?></td>
						<td>
							<?php if($report['Reporter']['username']): ?>
								<?=$this->Html->link($report['Reporter']['username'], array('controller' => 'users', 'action' => 'edit', $report['Reporter']['id']))?>
							<?php else: ?>
								<?=__d('admin', 'User deleted')?>
							<?php endif; ?>
						</td>
						<td>
							<?php if($report['ItemReport']['resolver_id'] === null): ?>
								<?=__d('admin', '--')?>
							<?php else: ?>
								<?=$this->Html->link($report['Resolver']['email'], array('controller' => 'admins', 'action' => 'edit', $report['Resolver']['id']))?>
							<?php endif; ?>
						</td>
						<td><?=h($this->Utility->enum('ItemReport', 'status', $report['ItemReport']['status']))?></td>
						<td><?=h($this->Utility->enum('ItemReport', 'type', $report['ItemReport']['type']))?></td>
						<td>
							<?php if($report['view_url'] === null): ?>
								<?=__d('admin', 'Deleted')?>
							<?php else: ?>
								<?=$this->Html->link($report['ItemReport']['item'], $report['view_url'], array('target' => '_blank'))?>
							<?php endif; ?>
						</td>
						<td><?=h($report['ItemReport']['reason'])?></td>
						<td><?=h($report['ItemReport']['comment'])?></td>
						<td class="actions">
							<?=
								$this->AdminForm->postLink(
									'<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete report').'"></i>',
									array('action' => 'delete', $report['ItemReport']['id']), 
									array('escape' => false), 
									__d('admin', 'Are you sure you want to delete # %s?', $report['ItemReport']['id'])
								);
							?>
							<?=
								$this->Html->link('<i class="fa fa-eye fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'View report').'"></i>', array('action' => 'view',  $report['ItemReport']['id']), array('escape' => false))
							?>
							<?=
								$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit report').'"></i>', array('action' => 'edit', $report['ItemReport']['id']), array('escape' => false))
							?>
						</td>
					</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="col-sm-5 text-left">
			<div class="input-group">
				<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
				<?=
					$this->AdminForm->input('Action', array(
						'empty' => __d('admin', '--Choose--'),
						'required' => true,
						'id'=> 'selectMassAction',
						'options' => array(
							'delete' => __d('admin', 'Delete'),
						)
					))
				?>
				<div class="input-group-btn">
					<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
				</div>
			</div>
		</div>
		<div class="col-sm-7 text-right">
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
		<div class="col-sm-12 text-center paddingten">
			<p><?=$this->Paginator->numbers(array('separator' => '&nbsp;'))?></p>
		</div>
	<?=$this->AdminForm->end()?>
</div>
<?php 
	$this->Js->buffer("
		setNavToggles('collapse1Button', 'collapse1');
	") 
?>
