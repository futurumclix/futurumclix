<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Setup Cron')?></h2>
	</div>
	<div class="col-sm-6 text-left">
		<?=__d('admin', 'Please set your crontab with commands below:')?>
	</div>
	<div class="col-sm-12 paddingten">
		<?php foreach($jobs as $job): ?>
			<input readonly type="text" class="form-control" value="<?=h($job)?>" /></td>
		<?php endforeach; ?>
	</div>
	<?php if($this->request->data['Settings']['allowHttpCron']): ?>
		<div class="col-sm-6 text-left">
			<?=__d('admin', 'or use following URLs if you need to run cron jobs via HTTP(S):')?>
		</div>
		<div class="col-sm-12 paddingten">
			<?php foreach($httpJobs as $job): ?>
				<input readonly type="text" class="form-control" value="<?=h($job)?>" /></td>
			<?php endforeach; ?>
		</div>
	<?php endif; ?>
	<div class="clearfix"></div>
	<div class="title2">
		<h2><?=__d('admin', 'Cron Job Settings')?></h2>
	</div>
	<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Delete PTC ads if they are expired/inactive for at least')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('PTCDeleteAfter', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 20%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' days since they were expired/added.')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Delete Banner ads if they are expired/inactive for at least')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('bannerAdsDeleteAfter', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 20%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' days since they were expired/added.')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Delete Featured ads if they are expired/inactive for at least')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('featuredAdsDeleteAfter', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 20%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' days since they were expired/added.')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Delete Login ads if they are expired/inactive for at least')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('loginAdsDeleteAfter', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 20%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' days since they were expired/added.')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Delete Paid Offers if they are expired/inactive for at least')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('paidOffersDeleteAfter', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 20%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' days since they were expired/added.')?></label>
		</div>
	</div>
	<?php if(Module::installed('AdGrid')): ?>
		<div class="form-group">
			<label class="col-sm-5 control-label"><?=__d('admin', 'Delete AdGrid ads if they are expired/inactive for at least')?></label>
			<div class="col-sm-7">
				<?=
					$this->AdminForm->input('AdGridDeleteAfter', array(
						'type' => 'number',
						'min' => 0,
						'step' => 1,
						'class' => 'form-control',
						'style' => 'width: 20%; float: left; margin-right: 10px;',
					))
				?>
				<label class="control-label"><?=__d('admin', ' days since they were expired/added.')?></label>
			</div>
		</div>
	<?php endif; ?>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Send')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('emailsPerShell', array(
					'type' => 'number',
					'min' => 1,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 15%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' e-mails in one e-mail cron run.')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Remove PTC ads statistics older than')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('PTCStatsDays', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 15%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' days (0 = never).')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Remove unverified users after')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('unverifiedDeleteDays', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 15%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' days (0 = never).')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Set Pending Deposits to Failed after')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('depositsPendingPurgeHours', array(
					'type' => 'number',
					'min' => 0,
					'step' => 1,
					'class' => 'form-control',
					'style' => 'width: 15%; float: left; margin-right: 10px;',
				))
			?>
			<label class="control-label"><?=__d('admin', ' hours (0 = never).')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Remove overfloated referrals when degrade user.')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('removeReferralsOverflow', array(
					'type' => 'checkbox',
				))
			?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Cron via HTTP(S) Settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'Allow running cron jobs via HTTP(S).')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('allowHttpCron', array(
					'type' => 'checkbox',
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-5 control-label"><?=__d('admin', 'HTTP(S) Allowed IPs')?></label>
		<div class="col-sm-7">
			<?=
				$this->AdminForm->input('httpCronIPs', array(
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'You can enter more than one IP, separated with comma with no spaces, for example: 1.1.1.1,2.2.2.2. Leave this field empty if you want to allow connections from any IP.'),
				))
			?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save changes')?></button></td>
	</div>
	<?=$this->AdminForm->end()?>
</div>
