<div class="col-md-12">
	<div class="title">
		<h2><?=__d('ad_grid_admin', 'Add new AdGrid ad')?></h2>
	</div>
	<?=$this->AdminForm->create('AdGridAd', array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label for="advertiser" class="col-sm-3 control-label"><?=__d('ad_grid_admin', 'Advertiser')?></label>
			<div class="col-sm-9">
				<?=
					$this->AdminForm->input('Advertiser.username', array(
						'data-toggle' => 'tooltip',
						'data-placement' => 'top',
						'title' => __d('ad_grid_admin', 'Leave blank for self sponsored advertisement (admin\'s ad)'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="url" class=" col-sm-3 control-label"><?=__d('ad_grid_admin', 'URL')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('url')?>
			</div>
		</div>
		<div class="form-group">
			<label for="expiry" class=" col-sm-3 control-label"><?=__d('ad_grid_admin', 'Expiry')?></label>
			<div class="col-sm-9">
				<div class="form-inline">
					<?=
						$this->AdminForm->input('expiry', array(
							'min' => 0,
							'step' => 1,
						))
					?>
					<?=$this->AdminForm->input('package_type')?>
				</div>
			</div>
		</div>
		<div class="text-center">
			<button class="btn btn-primary"><?=__d('ad_grid_admin', 'Save changes')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
