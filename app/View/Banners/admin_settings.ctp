<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add new banner')?></h2>
	</div>
	<?=$this->AdminForm->create('Banner', array('type' => 'file', 'class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Upload New Banner')?></label>
		<div class="col-sm-8">
			<?=
				$this->AdminForm->input('file', array(
					'type' => 'file', 
					'class' => 'inputfile',
					'id' => 'bannerbuttonupload',
				))
			?>
			<label class="form-control" for="bannerbuttonupload"><i class="fa fa-upload"></i> <?=__d('admin', 'Choose a file')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Add New Banner From The Web')?></label>
		<div class="col-sm-8">
			<?=
				$this->AdminForm->input('remote', array(
					'class' => 'form-control',
					'data-toggle' => 'tooltip',
					'data-placement' => 'top',
					'title' => __d('admin', 'Please include protocol name, for example: http://example.com/image.png'),
				))
			?>
		</div>
	</div>
	<div class="text-center">
		<button class="btn btn-primary"><?=__d('admin', 'Add')?></button>
	</div>
	<?=$this->AdminForm->end()?>
	<?php foreach($banners as $banner): ?>
	<div class="title2">
		<h2>
			<?=__d('admin', 'Banner #%s (%s)', $banner['Banner']['id'], $banner['Banner']['filename'])?>
			<div style="float: right;">
				<?=
					$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-placement="left" data-toggle="tooltip" title="" data-original-title="'.__d('admin', 'Delete this banner').'"></i>',
						array('action' => 'delete', $banner['Banner']['id']),
						array('escape' => false),
						__d('admin', 'Are you sure you want to delete # %s?', $banner['Banner']['id'])
					)
				?>
			</div>
		</h2>
	</div>
	<?=$this->AdminForm->create('Banner', array('class' => 'form-horizontal'))?>
	<?=$this->AdminForm->input('id', array(
		'type' => 'hidden',
		'value' => $banner['Banner']['id'],
	))?>
	<div class="text-center paddingten">
		<?=$this->Html->image(Router::url(array('action' => 'image', $banner['Banner']['id'])))?>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Make This Banner Statistical')?></label>
		<div class="col-sm-8">
			<?=$this->AdminForm->input('statistical', array(
				'checked' => $banner['Banner']['statistical'],
				'data-toggleid' => 'statisticalOptions-'.$banner['Banner']['id'],
				'class' => 'toggleCheckbox',
			))?>
		</div>
	</div>
	<div id="statisticalOptions-<?=$banner['Banner']['id']?>" <?php if(!$banner['Banner']['statistical']): ?> style="display: none;"<?php endif;?>>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'What Data Do You Want To Show On This Banner')?></label>
			<div class="col-sm-8">
				<div class="checkbox">
					<label>
						<?=
							$this->AdminForm->input('user_paid', array(
								'checked' => $banner['Banner']['user_paid'],
								'class' => 'toggleCheckbox',
								'data-toggleid' => 'userPaid-'.$banner['Banner']['id'],
							))
						?>
						<?=__d('admin', 'How Much User Got Paid')?>
					</label>
				</div>
				<div class="checkbox">
					<label>
						<?=
							$this->AdminForm->input('user_earned', array(
								'checked' => $banner['Banner']['user_earned'],
								'class' => 'toggleCheckbox',
								'data-toggleid' => 'userEarned-'.$banner['Banner']['id'],
							))
						?>
					<?=__d('admin', 'How Much User Earned')?>
					</label>
				</div>
				<div class="checkbox">
					<label>
					<?=
							$this->AdminForm->input('site_paid', array(
								'checked' => $banner['Banner']['site_paid'],
								'class' => 'toggleCheckbox',
								'data-toggleid' => 'sitePaid-'.$banner['Banner']['id'],
							))
						?>
					<?=__d('admin', 'How Much Site Already Paid')?>
					</label>
				</div>
			</div>
		</div>
		<div id="font">
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=__d('admin', 'Font')?></label>
				<div class="col-sm-9">
					<div class="form-group">
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'Font')?>
							</div>
							<?=
								$this->AdminForm->input('font_name', array(
									'class' => 'form-control',
									'options' => $fonts,
									'value' => $banner['Banner']['font_name'],
								))
							?>
						</div><br />
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'Font Size')?>
							</div>
							<?=
								$this->AdminForm->input('font_size', array(
									'value' => $banner['Banner']['font_size'],
									'class' => 'form-control',
								))
							?>
						</div><br />
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'Font Color')?>
							</div>
							<?=
								$this->AdminForm->input('font_color', array(
									'value' => $banner['Banner']['font_color'],
									'class' => 'form-control',
								))
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="userPaid-<?=$banner['Banner']['id']?>"<?php if(!$banner['Banner']['user_paid']): ?> style="display: none;"<?php endif;?>>
			<div class="form-group">
				<label class="col-sm-3 control-label"><i><?=__d('admin', 'I Already Got Paid')?></i></label>
				<div class="col-sm-9 form-inline">
					<div class="form-group">
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'X position')?>
							</div>
							<?=
								$this->AdminForm->input('user_paid_x', array(
									'value' => $banner['Banner']['user_paid_x'],
									'class' => 'form-control',
								))
							?>
						</div>
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'Y position')?>
							</div>
							<?=
								$this->AdminForm->input('user_paid_y', array(
									'value' => $banner['Banner']['user_paid_y'],
									'class' => 'form-control',
								))
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="userEarned-<?=$banner['Banner']['id']?>"<?php if(!$banner['Banner']['user_earned']): ?> style="display: none;"<?php endif;?>>
			<div class="form-group">
				<label class="col-sm-3 control-label"><i><?=__d('admin', 'I Already Earned')?></i></label>
				<div class="col-sm-9 form-inline">
					<div class="form-group">
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'X position')?>
							</div>
							<?=
								$this->AdminForm->input('user_earned_x', array(
									'value' => $banner['Banner']['user_earned_x'],
									'class' => 'form-control',
								))
							?>
						</div>
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'Y position')?>
							</div>
							<?=
								$this->AdminForm->input('user_earned_y', array(
									'value' => $banner['Banner']['user_earned_y'],
									'class' => 'form-control',
								))
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div id="sitePaid-<?=$banner['Banner']['id']?>"<?php if(!$banner['Banner']['site_paid']): ?> style="display: none;"<?php endif;?>>
			<div class="form-group">
				<label class="col-sm-3 control-label"><i><?=__d('admin', 'Site Paid')?></i></label>
				<div class="col-sm-9 form-inline">
					<div class="form-group">
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'X position')?>
							</div>
							<?=
								$this->AdminForm->input('site_paid_x', array(
									'value' => $banner['Banner']['site_paid_x'],
									'class' => 'form-control',
								))
							?>
						</div>
						<div class="input-group col-sm-4">
							<div class="input-group-addon">
								<?=__d('admin', 'Y position')?>
							</div>
							<?=
								$this->AdminForm->input('site_paid_y', array(
									'value' => $banner['Banner']['site_paid_y'],
									'class' => 'form-control',
								))
							?>
						</div>
					</div>
				</div>
			</div>
		</div>
		
	</div>
	<div class="text-center">
		<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
	<?php endforeach; ?>
</div>
<?php
	$this->Js->buffer("
		$('.toggleCheckbox').change(function() {
			$('#' + $(this).data('toggleid')).toggle($(this).is(':checked'));
		});
	");
?>
