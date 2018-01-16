<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Admins list')?></h2>
	</div>
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
					<th><?=$this->Paginator->sort('email')?></th>
					<th><?=$this->Paginator->sort('verify_token', __d('admin', 'Verified'))?></th>
					<th><?=$this->Paginator->sort('allowed_ips')?></th>
					<th><?=$this->Paginator->sort('last_log_in')?></th>
					<th><?=__d('admin', 'Actions')?></th>
				</tr>
				<?php foreach($admins as $admin): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('Admins.'.$admin['Admin']['id'], array(
								'value' => $admin['Admin']['id'],
								'class' => 'ActionCheckbox'
							))
						?>
					</td>
					<td><?=$admin['Admin']['email']?></td>
					<td><?=empty($admin['Admin']['verify_token']) ? __d('admin', 'Yes') : __d('admin', 'No')?></td>
					<td><?=empty($admin['Admin']['allowed_ips']) ? __d('admin', 'Any') : $admin['Admin']['allowed_ips']?></td>
					<td><?=$admin['Admin']['last_log_in']?></td>
					<td>
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" title="Edit Admin" data-toggle="tooltip" data-placement="top"></i>',
								array('action' => 'edit', $admin['Admin']['id']),
								array('escape' => false)
							)
						?>
						<?php if($admin['Admin']['id'] != 1): ?>
							<?=
								$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" title="Delete Admin" data-toggle="tooltip" data-placement="top"></i>',
									array('action' => 'delete', $admin['Admin']['id']),
									array('escape' => false),
									__d('admin', 'Are you sure you want to delete # %s', $admin['Admin']['id'])
								)
							?>
						<?php endif; ?>
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
					'id'=> 'actionSelect',
					'options' => array(
						'delete' => __d('admin', 'Delete Admins'),
					)
				))
			?>
			<div class="input-group-btn">
				<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
			</div>
		</div>
	</div>
	<div class="col-sm-7 text-right">
		<?=$this->Paginator->counter(array('format' => __d('admin', 'Page {:page} of {:pages}')))?>
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
	<div class="clearfix"></div>
	<?=$this->AdminForm->end()?>
	<div class="title">
		<h2><?=__d('admin', 'Add admin')?></h2>
	</div>
	<?=
		$this->AdminForm->create('Admin', array(
			'class' => 'form-horizontal'
		));
	?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'E-mail Address')?></label>
		<div class="col-sm-4">
			<?=$this->AdminForm->input('email')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Password')?></label>
		<div class="col-sm-4">
			<?=$this->AdminForm->input('password')?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Allowed IPs')?></label>
		<div class="col-sm-4">
			<?=
				$this->AdminForm->input('allowed_ips', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'You can enter more than one IP, separated with comma with no spaces, for example: 1.1.1.1,2.2.2.2. Leave this field empty if you want to allow connections from any IP.'),
				))
			?>
		</div>
	</div>
	<div class="text-center">
		<button class="btn btn-primary"><?=__d('admin', 'Add Admin')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
<?php $this->Js->buffer('setNavToggles();'); ?>
