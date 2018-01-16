<div class="col-md-12">
	<div class="title">
		<h2><?=__d('offerwalls_admin', 'Offerwalls Settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#settings"><?=__d('offerwalls_admin', 'General Settings')?></a></li>
		<?php foreach($active as $k => $offerwall): ?>
			<li><a data-toggle="tab" href="#<?=$k?>"><?=h($offerwall)?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<div id="settings" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('offerwalls_admin', 'General settings')?></h2>
			</div>
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('offerwalls_admin', 'Please Choose Which Oferwalls Do You Want To Use')?></label>
					<div class="col-sm-4">
						<?php foreach($available as $k => $offerwall): ?>
							<div class="checkbox">
								<label>
									<?=$this->AdminForm->checkbox("Offerwall.$k.enabled")?>
									<?=h($offerwall)?>
								</label>
							</div>
						<?php endforeach; ?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4"><?=__d('offerwalls_admin', 'Where To Credit Money Made On Offerwalls')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('OfferwallsSettings.offerwalls.credit', array(
								'options' => array(
									'account' => __d('offerwalls_admin', 'Account Balance'),
									'purchase' => __d('offerwalls_admin', 'Purchase Balance'),
								)
							))
						?>
					</div>
				</div>
				<?php foreach($memberships as $membership_id => $membership): ?>
					<?php if(isset($this->request->data['OfferwallsMembership'][$membership_id]['id'])): ?>
						<?=$this->AdminForm->input('OfferwallsMembership.'.$membership_id.'.id', array('type' => 'hidden'))?>
					<?php endif; ?>
					<?=$this->AdminForm->input('OfferwallsMembership.'.$membership_id.'.membership_id', array('value' => $membership_id, 'type' => 'hidden'))?>
						<div class="title2">
							<h2><?=__d('offerwalls_admin', '"%s" settings', $membership)?></h2>
						</div>
						<div class="form-group">
							<label class="col-sm-4"><?=__d('offerwalls_admin', 'Point Ratio')?></label>
							<div class="col-sm-4">
								<?=
									$this->AdminForm->input('OfferwallsMembership.'.$membership_id.'.point_ratio', array(
										'type' => 'number',
										'min' => 0,
										'max' => '999.9999',
										'step' => '0.0001',
										'data-placement' => 'top',
										'data-toggle' => 'popover',
										'data-trigger' => 'focus',
										'data-content' => __d('offerwalls_admin', 'Enter the value of 1 point. For example if you will set 1 point for $0.01 and user will make an offer worth 100 points, he will earn $1.'),
									))
								?>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4"><?=__d('offerwalls_admin', 'Add Money Made On Offerwalls After')?></label>
							<div class="col-sm-2">
								<div class="input-group">
									<?=
										$this->AdminForm->input('OfferwallsMembership.'.$membership_id.'.delay', array(
											'type' => 'number',
											'min' => 0,
											'step' => 1,
											'max' => 255,
											'data-placement' => 'top',
											'data-toggle' => 'popover',
											'data-trigger' => 'focus',
											'data-content' => __d('offerwalls_admin', 'After how many days add money earned on offers to user\'s account (put 0 for instant crediting)'),
										))
									?>
									<div class="input-group-addon"><?=__d('offerwalls_admin', 'Days')?></div>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4"><?=__d('offerwalls_admin', 'Instant Credit Offers Worth Less or Equal')?></label>
							<div class="col-sm-2">
								<div class="input-group">
									<?=
										$this->AdminForm->input('OfferwallsMembership.'.$membership_id.'.instant_limit', array(
											'type' => 'money',
											'data-placement' => 'top',
											'data-toggle' => 'popover',
											'data-trigger' => 'focus',
											'data-content' => __d('offerwalls_admin', 'Amount less or equal will be credited without delay (put 0 for disable)'),
										))
									?>
								</div>
							</div>
						</div>
						<div class="form-group">
							<label class="col-sm-4"><?=__d('offerwalls_admin', 'Points per completed offer')?></label>
							<div class="col-sm-2">
								<div class="input-group">
									<?=
										$this->AdminForm->input('OfferwallsMembership.'.$membership_id.'.points_per_offer', array(
											'data-placement' => 'top',
											'data-toggle' => 'popover',
											'data-trigger' => 'focus',
											'data-content' => __d('offerwalls_admin', 'Amount less or equal will be credited without delay (put 0 for disable)'),
										))
									?>
								</div>
							</div>
						</div>
				<?php endforeach; ?>
				<div class="col-sm-12 text-center paddingten">
					<button class="btn btn-primary"><?=__d('offerwalls_admin', 'Save changes')?></button>
				</div>
				<div class="clearfix"></div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php
			foreach($active as $k => $v) {
				include(App::pluginPath('Offerwalls').'Lib'.DS.'Offerwalls'.DS.$k.'Settings.ctp');
			}
		?>
	</div>
</div>
<?php $this->Js->buffer("jumpToTabByAnchor();"); ?>
