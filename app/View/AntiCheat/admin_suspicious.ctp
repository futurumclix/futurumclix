<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Suspicious')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#ip"><?=__d('admin', 'Similar IP')?></a></li>
		<li><a data-toggle="tab" href="#usernames"><?=__d('admin', 'Similar Usernames')?></a></li>
		<li><a data-toggle="tab" href="#usernameemail"><?=__d('admin', 'Username The Same As E-mail Address')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="ip" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('admin', 'Search for similar IP')?></h2>
			</div>
			<div class="form-horizontal">
				<div class="col-md-12 text-center">
					<p><?=__d('admin', 'This option will search for similar signup and login IPs with 7 days interval time. For example it will list accounts opened with ip like 1.1.1.1 and then 1.1.1.2 within 7 days. Please note that you have to make a final decision if accounts are cheating or not.')?></p>
				</div>
				<div class="col-md-12 text-center paddingten">
					<?=$this->Html->link(__d('admin', 'Search'), array('ip', '#' => 'ip'), array('class' => 'btn btn-primary'))?>
				</div>
				<div class="clearfix"></div>
			</div>
			<?php if($mode == 'ip'): ?>
				<?php $this->Paginator->options(array('url' => array('ip', '#' => 'ip'))); ?>
				<?=
					$this->AdminForm->create(false, array(
						'url' => array('plugin' => null, 'controller' => 'users', 'action' => 'massaction'),
					))
				?>
					<div class="title2">
						<h2><?=__d('admin', 'Similar IP users')?></h2>
					</div>
					<div class="table-horizontal">
						<table class="table table-striped table-hover">
							<tbody>
								<tr>
									<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
									<th><?=$this->Paginator->sort('username', __d('admin', 'Username'))?></th>
									<th><?=$this->Paginator->sort('signup_ip', __d('admin', 'Signup IP'))?></th>
									<th><?=$this->Paginator->sort('last_ip', __d('admin', 'Login IP'))?></th>
									<th><?=$this->Paginator->sort('created', __d('admin', 'Signup Date'))?></th>
									<th><?=$this->Paginator->sort('email', __d('admin', 'E-mail'))?></th>
									<th><?=$this->Paginator->sort('location', __d('admin', 'Location'))?></th>
									<th><?=__d('admin', 'Actions')?></th>
								</tr>
									<?php foreach($data as $user): ?>
										<tr>
											<td>
												<?=
													$this->AdminForm->checkbox('Users.'.$user['User']['id'], array(
														'value' => $user['User']['id'],
														'class' => 'ActionCheckbox'
													))
												?>
											</td>
											<td>
												<?=$this->Html->link($user['User']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['User']['id']))?>
											</td>
											<td><?=h($user['User']['signup_ip'])?></td>
											<td><?=h($user['User']['last_ip'])?></td>
											<td><?=$this->Time->nice($user['User']['created'])?></td>
											<td><?=h($user['User']['email'])?></td>
											<td><?=h($user['User']['location'])?></td>
											<td>
												<?=
													$this->AdminForm->postLink('<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Suspend user').'"></i>',
														array('plugin' => null, 'controller' => 'users', 'action' => 'suspend', $user['User']['id']),
														array('escape' => false),
														__d('admin', 'Are you sure you want to suspend # %s?', $user['User']['id'])
													)
												?>
												<?=
													$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete user').'"></i>',
														array('plugin' => null, 'controller' => 'users', 'action' => 'delete', $user['User']['id']),
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
										'suspend' => __d('admin', 'Suspend users'),
										'delete' => __d('admin', 'Delete users'),
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
			<?php endif; ?>
		</div>
		<div id="usernames" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Search for users with similar usernames')?></h2>
			</div>
			<div class="form-horizontal">
				<div class="col-md-12 text-center">
					<p><?=__d('admin', 'This option will search for users with similar usernames like username, username1, username2 etc. Please note that you have to make a final decision if accounts are cheating or not.')?></p>
				</div>
				<div class="col-md-12 text-center paddingten">
					<?=$this->Html->link(__d('admin', 'Search'), array('username', '#' => 'usernames'), array('class' => 'btn btn-primary'))?>
				</div>
				<div class="clearfix"></div>
			</div>
			<?php if($mode == 'username'): ?>
				<?php $this->Paginator->options(array('url' => array('username', '#' => 'usernames'))); ?>
				<div class="title2">
					<h2><?=__d('admin', 'Similar usernames')?></h2>
				</div>
				<?=
					$this->AdminForm->create(false, array(
						'url' => array('plugin' => null, 'controller' => 'users', 'action' => 'massaction'),
					))
				?>
					<div class="table-horizontal">
						<table class="table table-striped table-hover">
							<tbody>
								<tr>
									<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
									<th><?=$this->Paginator->sort('username', __d('admin', 'Username'))?></th>
									<th><?=$this->Paginator->sort('signup_ip', __d('admin', 'Signup IP'))?></th>
									<th><?=$this->Paginator->sort('last_ip', __d('admin', 'Login IP'))?></th>
									<th><?=$this->Paginator->sort('created', __d('admin', 'Signup Date'))?></th>
									<th><?=$this->Paginator->sort('email', __d('admin', 'E-mail'))?></th>
									<th><?=$this->Paginator->sort('location', __d('admin', 'Location'))?></th>
									<th><?=__d('admin', 'Actions')?></th>
								</tr>
									<?php foreach($data as $user): ?>
										<tr>
											<td>
												<?=
													$this->AdminForm->checkbox('Users.'.$user['User']['id'], array(
														'value' => $user['User']['id'],
														'class' => 'ActionCheckbox'
													))
												?>
											</td>
											<td>
												<?=$this->Html->link($user['User']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['User']['id']))?>
											</td>
											<td><?=h($user['User']['signup_ip'])?></td>
											<td><?=h($user['User']['last_ip'])?></td>
											<td><?=$this->Time->nice($user['User']['created'])?></td>
											<td><?=h($user['User']['email'])?></td>
											<td><?=h($user['User']['location'])?></td>
											<td>
												<?=
													$this->AdminForm->postLink('<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Suspend user').'"></i>',
														array('plugin' => null, 'controller' => 'users', 'action' => 'suspend', $user['User']['id']),
														array('escape' => false),
														__d('admin', 'Are you sure you want to suspend # %s?', $user['User']['id'])
													)
												?>
												<?=
													$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete user').'"></i>',
														array('plugin' => null, 'controller' => 'users', 'action' => 'delete', $user['User']['id']),
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
										'suspend' => __d('admin', 'Suspend users'),
										'delete' => __d('admin', 'Delete users'),
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
			<?php endif; ?>
		</div>
		<div id="usernameemail" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Search for users with usernames the same as e-mail addresses')?></h2>
			</div>
			<div class="form-horizontal">
				<div class="col-md-12 text-center">
					<p><?=__d('admin', 'This option will search for users with usernames the same as email addresses, for example user1-user1@hotmail.com. This is very common way of bots and cheaters to open multiple accounts. Please note that you have to make a final decision if accounts are cheating or not.')?></p>
				</div>
				<div class="col-md-12 text-center paddingten">
					<?=$this->Html->link(__d('admin', 'Search'), array('usernameemail', '#' => 'usernameemail'), array('class' => 'btn btn-primary'))?>
				</div>
				<div class="clearfix"></div>
			</div>
			<?php if($mode == 'usernameemail'): ?>
				<?php $this->Paginator->options(array('url' => array('usernameemail', '#' => 'usernameemail'))); ?>
				<div class="title2">
					<h2><?=__d('admin', 'Similar usernames')?></h2>
				</div>
				<?=
					$this->AdminForm->create(false, array(
						'url' => array('plugin' => null, 'controller' => 'users', 'action' => 'massaction'),
					))
				?>
					<div class="table-horizontal">
						<table class="table table-striped table-hover">
							<tbody>
								<tr>
									<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
									<th><?=$this->Paginator->sort('username', __d('admin', 'Username'))?></th>
									<th><?=$this->Paginator->sort('signup_ip', __d('admin', 'Signup IP'))?></th>
									<th><?=$this->Paginator->sort('last_ip', __d('admin', 'Login IP'))?></th>
									<th><?=$this->Paginator->sort('created', __d('admin', 'Signup Date'))?></th>
									<th><?=$this->Paginator->sort('email', __d('admin', 'E-mail'))?></th>
									<th><?=$this->Paginator->sort('location', __d('admin', 'Location'))?></th>
									<th><?=__d('admin', 'Actions')?></th>
								</tr>
									<?php foreach($data as $user): ?>
										<tr>
											<td>
												<?=
													$this->AdminForm->checkbox('Users.'.$user['User']['id'], array(
														'value' => $user['User']['id'],
														'class' => 'ActionCheckbox'
													))
												?>
											</td>
											<td>
												<?=$this->Html->link($user['User']['username'], array('plugin' => null, 'controller' => 'users', 'action' => 'edit', $user['User']['id']))?>
											</td>
											<td><?=h($user['User']['signup_ip'])?></td>
											<td><?=h($user['User']['last_ip'])?></td>
											<td><?=$this->Time->nice($user['User']['created'])?></td>
											<td><?=h($user['User']['email'])?></td>
											<td><?=h($user['User']['location'])?></td>
											<td>
												<?=
													$this->AdminForm->postLink('<i class="fa fa-minus-square fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Suspend user').'"></i>',
														array('plugin' => null, 'controller' => 'users', 'action' => 'suspend', $user['User']['id']),
														array('escape' => false),
														__d('admin', 'Are you sure you want to suspend # %s?', $user['User']['id'])
													)
												?>
												<?=
													$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete user').'"></i>',
														array('plugin' => null, 'controller' => 'users', 'action' => 'delete', $user['User']['id']),
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
										'suspend' => __d('admin', 'Suspend users'),
										'delete' => __d('admin', 'Delete users'),
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
			<?php endif; ?>
		</div>
	</div>
</div>
<?php $this->Js->buffer('jumpToTabByAnchor()'); ?>
