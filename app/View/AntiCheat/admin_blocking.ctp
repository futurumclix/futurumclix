<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Blocking settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#ip"><?=__d('admin', 'IP Blocking')?></a></li>
		<li><a data-toggle="tab" href="#email"><?=__d('admin', 'Email Blocking')?></a></li>
		<li><a data-toggle="tab" href="#country"><?=__d('admin', 'Country Blocking')?></a></li>
		<li><a data-toggle="tab" href="#username"><?=__d('admin', 'Username Blocking')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="ip" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('admin', 'Add new IP to block')?></h2>
			</div>
			<?=$this->AdminForm->create('IpLock', array('class' => 'form-horizontal', 'url' => array('plugin' => null, 'controller' => 'anti_cheat', 'action' => 'blocking', '#' => 'ip')))?>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Please Enter IP to block')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('ip', array(
								'type' => 'text',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-placement' => 'top',
								'data-content' => __d('admin', 'Enter IP to block, you can use ranges like 1.1.1.1-1.1.1.255.'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Add Note To This Block')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->input('note')?>
					</div>
				</div>
				<div class="col-md-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Add')?></button>
				</div>
				<div class="clearfix"></div>
			<?=$this->AdminForm->end()?>
			<div class="title2">
				<h2><?=__d('admin', 'Currently blocked IP')?></h2>
			</div>
			<?=
				$this->AdminForm->create(false, array(
					'url' => array('action' => 'massaction', 'IpLock'),
				))
			?>
				<div class="table-horizontal">
					<table class="table table-striped table-hover">
						<tbody>
							<tr>
								<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
								<th><?=__d('admin', 'IP range')?></th>
								<th><?=__d('admin', 'Date Added')?></th>
								<th><?=__d('admin', 'Note')?></th>
								<th><?=__d('admin', 'Actions')?></th>
							</tr>
							<?php foreach($ips as $ip): ?>
								<tr>
									<td>
										<?=
											$this->AdminForm->checkbox('IpLock.'.$ip['IpLock']['id'], array(
												'value' => $ip['IpLock']['id'],
												'class' => 'ActionCheckbox'
											))
										?>
									</td>
									<td><?=__d('admin', '%s - %s', $ip['IpLock']['ip_start'], $ip['IpLock']['ip_end'])?></td>
									<td><?=$this->Time->nice($ip['IpLock']['created'])?></td>
									<td><?=h($ip['IpLock']['note'])?></td>
									<td>
									<?=
										$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Delete').'"></i>',
											array('action' => 'ip_lock_delete', $ip['IpLock']['id']),
											array('escape' => false),
											__d('admin', 'Are you sure you want to delete # %s-%s?', $ip['IpLock']['ip_start'], $ip['IpLock']['ip_end'])
										)
									?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="col-sm-5 text-left paddingten">
					<div class="input-group">
						<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
						<?=
							$this->AdminForm->input('Action', array(
								'empty' => __d('admin', '--Choose--'),
								'required' => true,
								'id'=> 'actionSelect',
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
			<?=$this->AdminForm->end()?>
		</div>
		<div id="email" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Add new email to block')?></h2>
			</div>
			<?=$this->AdminForm->create('EmailLock', array('class' => 'form-horizontal', 'url' => array('plugin' => null, 'controller' => 'anti_cheat', 'action' => 'blocking', '#' => 'email')))?>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Please Enter E-mail To Block')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('template', array(
								'type' => 'text',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-placement' => 'top',
								'data-content' => __d('admin', 'Enter email to block, you can use wildcard like *.hotmail.com.'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Add Note To This Block')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->input('note')?>
					</div>
				</div>
				<div class="col-md-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Add')?></button>
				</div>
				<div class="clearfix"></div>
			<?=$this->AdminForm->end()?>
			<div class="title2">
				<h2><?=__d('admin', 'Currently blocked emails')?></h2>
			</div>
			<?=
				$this->AdminForm->create(false, array(
					'url' => array('action' => 'massaction', 'EmailLock'),
				))
			?>
				<div class="table-horizontal">
					<table class="table table-striped table-hover">
						<tbody>
							<tr>
								<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
								<th><?=__d('admin', 'Email')?></th>
								<th><?=__d('admin', 'Date Added')?></th>
								<th><?=__d('admin', 'Note')?></th>
								<th><?=__d('admin', 'Actions')?></th>
							</tr>
							<?php foreach($emails as $email): ?>
								<tr>
									<td>
										<?=
											$this->AdminForm->checkbox('EmailLock.'.$email['EmailLock']['id'], array(
												'value' => $email['EmailLock']['id'],
												'class' => 'ActionCheckbox'
											))
										?>
									</td>
									<td><?=h($email['EmailLock']['template'])?></td>
									<td><?=$this->Time->nice($email['EmailLock']['created'])?></td>
									<td><?=h($email['EmailLock']['note'])?></td>
									<td>
									<?=
										$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltemail" data-placement="top" title="'.__d('admin', 'Delete').'"></i>',
											array('action' => 'email_lock_delete', $email['EmailLock']['id']),
											array('escape' => false),
											__d('admin', 'Are you sure you want to delete "%s"?', h($email['EmailLock']['template']))
										)
									?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="col-sm-5 text-left paddingten">
					<div class="input-group">
						<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
						<?=
							$this->AdminForm->input('Action', array(
								'empty' => __d('admin', '--Choose--'),
								'required' => true,
								'id'=> 'actionSelect',
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
			<?=$this->AdminForm->end()?>
		</div>
		<div id="country" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Add new country to block')?></h2>
			</div>
			<?=$this->AdminForm->create('CountryLock', array('class' => 'form-horizontal', 'url' => array('plugin' => null, 'controller' => 'anti_cheat', 'action' => 'blocking', '#' => 'country')))?>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Please Choose Country To Block')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->input('country_id')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Add Note To This Block')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->input('note')?>
					</div>
				</div>
				<div class="col-md-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Add')?></button>
				</div>
				<div class="clearfix"></div>
			<?=$this->AdminForm->end()?>
			<div class="title2">
				<h2><?=__d('admin', 'Currently blocked countries')?></h2>
			</div>
			<?=
				$this->AdminForm->create(false, array(
					'url' => array('action' => 'massaction', 'CountryLock'),
				))
			?>
				<div class="table-horizontal">
					<table class="table table-striped table-hover">
						<tbody>
							<tr>
								<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
								<th><?=__d('admin', 'Country')?></th>
								<th><?=__d('admin', 'Date Added')?></th>
								<th><?=__d('admin', 'Note')?></th>
								<th><?=__d('admin', 'Actions')?></th>
							</tr>
							<?php foreach($countrylocks as $country): ?>
								<tr>
									<td>
										<?=
											$this->AdminForm->checkbox('CountryLock.'.$country['CountryLock']['id'], array(
												'value' => $country['CountryLock']['id'],
												'class' => 'ActionCheckbox'
											))
										?>
									</td>
									<td><?=h($country['Country']['country'])?></td>
									<td><?=$this->Time->nice($country['CountryLock']['created'])?></td>
									<td><?=h($country['CountryLock']['note'])?></td>
									<td>
									<?=
										$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltcountry" data-placement="top" title="'.__d('admin', 'Delete').'"></i>',
											array('action' => 'country_lock_delete', $country['CountryLock']['id']),
											array('escape' => false),
											__d('admin', 'Are you sure you want to delete "%s"?', h($country['Country']['country']))
										)
									?>
									</td>
								</tr>
							<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="col-sm-5 text-left paddingten">
					<div class="input-group">
						<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
						<?=
							$this->AdminForm->input('Action', array(
								'empty' => __d('admin', '--Choose--'),
								'required' => true,
								'id'=> 'actionSelect',
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
			<?=$this->AdminForm->end()?>
		</div>
		<div id="username" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Add new username to block')?></h2>
			</div>
			<?=$this->AdminForm->create('UsernameLock', array('class' => 'form-horizontal', 'url' => array('plugin' => null, 'controller' => 'anti_cheat', 'action' => 'blocking', '#' => 'username')))?>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Enter Username To Block')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('template', array(
								'type' => 'text',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-placement' => 'top',
								'data-content' => __d('admin', 'Enter username to block, this username will be prohibited to use while registering.'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('admin', 'Add Note To This Block')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->input('note')?>
					</div>
				</div>
				<div class="col-md-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Add')?></button>
				</div>
				<div class="clearfix"></div>
			<?=$this->AdminForm->end()?>
			<div class="title2">
				<h2><?=__d('admin', 'Currently blocked usernames')?></h2>
			</div>
			<?=
				$this->AdminForm->create(false, array(
					'url' => array('action' => 'massaction', 'UsernameLock'),
				))
			?>
				<div class="table-horizontal">
					<table class="table table-striped table-hover">
						<tbody>
							<tr>
								<th><input type="checkbox" onclick="setAllCheckboxes('ActionCheckbox', this.checked);"></th>
								<th><?=__d('admin', 'Username')?></th>
								<th><?=__d('admin', 'Date Added')?></th>
								<th><?=__d('admin', 'Note')?></th>
								<th><?=__d('admin', 'Actions')?></th>
							</tr>
								<?php foreach($usernames as $username): ?>
									<tr>
										<td>
											<?=
												$this->AdminForm->checkbox('UsernameLock.'.$username['UsernameLock']['id'], array(
													'value' => $username['UsernameLock']['id'],
													'class' => 'ActionCheckbox'
												))
											?>
										</td>
										<td><?=h($username['UsernameLock']['template'])?></td>
										<td><?=$this->Time->nice($username['UsernameLock']['created'])?></td>
										<td><?=h($username['UsernameLock']['note'])?></td>
										<td>
										<?=
											$this->AdminForm->postLink('<i class="fa fa-trash fa-lg" data-toggle="tooltusername" data-placement="top" title="'.__d('admin', 'Delete').'"></i>',
												array('action' => 'username_lock_delete', $username['UsernameLock']['id']),
												array('escape' => false),
												__d('admin', 'Are you sure you want to delete "%s"?', h($username['UsernameLock']['template']))
											)
										?>
										</td>
									</tr>
								<?php endforeach; ?>
						</tbody>
					</table>
				</div>
				<div class="col-sm-5 text-left paddingten">
					<div class="input-group">
						<label for="selectMassAction" class="input-group-addon"><?=__d('admin', 'Mass Action')?></label>
						<?=
							$this->AdminForm->input('Action', array(
								'empty' => __d('admin', '--Choose--'),
								'required' => true,
								'id'=> 'actionSelect',
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
			<?=$this->AdminForm->end()?>
		</div>
	</div>
</div>
<?php $this->Js->buffer('jumpToTabByAnchor();')?>
