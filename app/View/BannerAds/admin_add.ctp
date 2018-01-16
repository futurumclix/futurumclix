<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add new banner ad')?></h2>
	</div>
	<?=$this->AdminForm->create('BannerAd', array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label for="advertiser" class="col-sm-3 control-label"><?=__d('admin', 'Advertiser')?></label>
			<div class="col-sm-9">
				<?=
					$this->AdminForm->input('Advertiser.username', array(
						'data-toggle' => 'tooltip',
						'data-placement' => 'top',
						'title' => __d('admin', 'Leave blank for self sponsored advertisement (admin\'s ad)'),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-3 control-label"><?=__d('admin', 'Title')?></label>
			<div class="col-sm-9">
				<div class="input-group">
					<?=
						$this->AdminForm->input('title', array(
							'data-limit' => $titleMax,
							'data-counter' => 'titleCounter'
						))
					?>
					<div id="titleCounter" class="input-group-addon"><?=isset($this->request->data['BannerAd']['title']) ? strlen($this->request->data['BannerAd']['title']) : 0?> / <?=$titleMax?></div>
				</div>
			</div>
		</div>
		<div class="form-group">
			<label for="imageUrl" class=" col-sm-3 control-label"><?=__d('admin', 'Image URL')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('image_url')?>
			</div>
		</div>
		<div class="form-group">
			<label for="url" class=" col-sm-3 control-label"><?=__d('admin', 'URL')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('url')?>
			</div>
		</div>
		<div class="form-group">
			<label for="expiry" class=" col-sm-3 control-label"><?=__d('admin', 'Expiry')?></label>
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
			<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
