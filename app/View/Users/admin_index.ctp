<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Search Members')?></h2>
	</div>
	<?=
		$this->AdminForm->create(false, array(
			'url' => array('action' => 'index'),
			'class' => 'form-horizontal',
		))
	?>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'Username')?></label>
		<?=
			$this->AdminForm->input('Filter.User.username', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter username to search'),
			))
		?>
	</div>
	<div class="form-group searchform col-sm-4">
		<label><?=__d('admin', 'E-mail')?></label>
		<?=
			$this->AdminForm->input('Filter.User.email', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter email to search'),
				'required' => false,
				'type' => 'text',
			))
		?>
	</div>
	<div class="form-group searchform col-sm-3">
		<label><?=__d('admin', 'Last IP')?></label>
		<?=
			$this->AdminForm->input('Filter.User.last_ip', array(
				'data-toggle' => 'tooltip',
				'data-placement' => 'top',
				'title' => __d('admin', 'Enter last IP to search'),
			))
		?>
	</div>
	<div class="form-group searchform col-sm-1 text-right">
		<a href="#collapse1" data-toggle="collapse" style="position: relative; top: 30px;">
			<i id="collapse1Button" title="<?=__d('admin', 'Click to show more search options')?>" data-toggle="tooltip" data-placement="top" class="fa fa-plus-circle fa-lg"></i>
		</a>
	</div>	
	<div id="collapse1" class="panel-collapse collapse <?=$searchCollapse?>">
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Upline')?></label>
			<?=
				$this->AdminForm->input('Filter.Upline.username', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter upline username to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Rented Upline')?></label>
			<?=
				$this->AdminForm->input('Filter.RentedUpline.username', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter rented upline username to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Payment Account')?></label>
			<?=
				$this->AdminForm->input('Filter.User.payment', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter payment email to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Signup IP')?></label>
			<?=
				$this->AdminForm->input('Filter.User.signup_ip', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter singup IP to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Location')?></label>
			<?=
				$this->AdminForm->input('Filter.User.location', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Enter location to search'),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Membership')?></label>
			<?=
				$this->AdminForm->input('Filter.ActiveMembership.membership_id', array(
					'type' => 'select',
					'empty' => '----',
					'options' => $memberships,
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Status')?></label>
			<?=
				$this->AdminForm->input('Filter.User.role', array(
					'type' => 'select',
					'empty' => '----',
					'options' => $userRoles,
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Upgraded')?></label>
			<?=
				$this->AdminForm->input('Filter.ActiveMembership.period', array(
					'type' => 'select',
					'empty' => '----',
					'options' => array('Default' => __d('admin', 'No'), 'Upgraded' => __d('admin', 'Only')),
				))
			?>
		</div>
		<div class="form-group searchform col-sm-3">
			<label><?=__d('admin', 'Forum Status')?></label>
			<?=
				$this->AdminForm->input('Filter.User.forum_status', array(
					'type' => 'select',
					'empty' => '----',
					'options' => array(1 => __d('admin', 'Active'), 2 => __d('admin', 'Banned')),
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
					<th><?=$this->Paginator->sort('username')?></th>
					<th><?=$this->Paginator->sort('email')?></th>
					<th><?=$this->Paginator->sort('location', __d('admin', 'Location'))?></th>
					<th><?=$this->Paginator->sort('account_balance')?></th>
					<th><?=$this->Paginator->sort('purchase_balance')?></th>
					<th><?=$this->Paginator->sort('refs_count','Referrals')?></th>
					<th><?=$this->Paginator->sort('rented_refs_count','Rented Referrals')?></th>
					<th><?=$this->Paginator->sort('ActiveMembership.membership_id', __d('admin', 'Account'))?></th>
					<th><?=$this->Paginator->sort('role', 'Status')?></th>
					<th><?=$this->Paginator->sort('last_login')?></th>
					<th class="actions"><?= __d('admin', 'Actions')?></th>
				</tr>
				<?php foreach ($users as $user): ?>
				<tr>
					<td>
						<?=
							$this->AdminForm->checkbox('Users.'.$user['User']['id'], array(
								'value' => $user['User']['id'],
								'class' => 'ActionCheckbox'
							))
						?>
					</td>
					<td><?=h($user['User']['username'])?>&nbsp;</td>
					<td><?=h($user['User']['email'])?>&nbsp;</td>
					<td><?=h($user['User']['location'])?>&nbsp;</td>
					<td><?=h($this->Currency->format($user['User']['account_balance']))?>&nbsp;</td>
					<td><?=h($this->Currency->format($user['User']['purchase_balance']))?>&nbsp;</td>
					<?php if($user['User']['refs_count'] > 0): ?>
					<td><?= $this->AdminForm->postLink(h($user['User']['refs_count']), array('action' => 'index', 'Upline.username' => $user['User']['username']))?></td>
					<?php else: ?>
					<td><?=h($user['User']['refs_count'])?>&nbsp;</td>
					<?php endif; ?>
					<?php if($user['User']['rented_refs_count'] > 0): ?>
					<td><?=$this->AdminForm->postLink(h($user['User']['rented_refs_count']), array('action' => 'index', 'RentedUpline.username' => $user['User']['username']))?></td>
					<?php else: ?>
					<td><?=h($user['User']['rented_refs_count'])?>&nbsp;</td>
					<?php endif; ?>
					<td><?=h($user['ActiveMembership']['Membership']['name'])?></td>
					<td><?=h($user['User']['role'])?>&nbsp;</td>
					<td><?=h($user['User']['last_log_in'] ? $user['User']['last_log_in'] : __d('admin', 'Never'))?>&nbsp;</td>
					<td class="actions">
						<?=
							$this->Html->link('<i class="fa fa-pencil fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Edit user').'"></i>',
								array('action' => 'edit', $user['User']['id']),
								array('escape' => false)
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Suspend user').'"></i>',
								array('action' => 'suspend', $user['User']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to suspend # %s?', $user['User']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-check-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Activate user').'"></i>',
								array('action' => 'activate', $user['User']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to activate # %s?', $user['User']['id'])
							)
						?>
						<?=
							$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete user').'"></i>',
								array('action' => 'delete', $user['User']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to delete # %s?', $user['User']['id'])
							)
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
					'id'=> 'actionSelect',
					'options' => array(
						'activate' => __d('admin', 'Activate users'),
						'suspend' => __d('admin', 'Suspend users'),
						'delete' => __d('admin', 'Delete users'),
						'ban' => __d('admin', 'Ban from forum'),
						'unban' => __d('admin', 'Unban from froum'),
					)
				))
			?>
			<div class="input-group-btn">
				<button class="btn btn-danger"><?=__d('admin', 'Perform Action')?></button>
			</div>
		</div>
	</div>
	<?=$this->AdminForm->end()?>
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
</div>
<?php $this->Js->buffer("setNavToggles('collapse1Button', 'collapse1');") ?>
