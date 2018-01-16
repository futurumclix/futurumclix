<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Edit "%s"', $this->request->data['News']['title'])?></h2>
	</div>
	<?=$this->AdminForm->create('News', array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label for="title" class="col-sm-3 control-label"><?=__d('admin', 'Include In Login Ads')?></label>
			<div class="col-sm-2">
				<?=$this->AdminForm->input('show_in_login_ads')?>
			</div>
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-3 control-label"><?=__d('admin', 'Display For How Long In Login Ads')?></label>
			<div class="col-sm-2">
				<?=$this->AdminForm->input('show_in_login_ads_until')?>
			</div>
		</div>
		<div class="form-group">
			<label for="title" class="col-sm-3 control-label"><?=__d('admin', 'Title')?></label>
			<div class="col-sm-9">
				<?=$this->AdminForm->input('title')?>
			</div>
		</div>
		<div class="title2">
			<h2><?=__d('admin', 'News Content')?></h2>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<?=$this->AdminForm->input('content')?>
			</div>
		</div>
		<div class="text-center">
			<button class="btn btn-primary"><?=__d('admin', 'Save')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
<?=$this->TinyMCE->editor()?>
