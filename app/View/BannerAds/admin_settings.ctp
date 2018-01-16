<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Banner Ads settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#details"><?=__d('admin', 'Details')?></a></li>
		<li><a data-toggle="tab" href="#packages"><?=__d('admin', 'Click packages')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="details" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('admin', 'Details')?></h2>
			</div>
			<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Enable Banner Ads')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('bannerAdsActive', array(
								'type' => 'checkbox',
								'data-trigger' => 'focus',
								'data-toggle' => 'popover',
								'data-placement' => 'top',
								'data-content' => __d('admin', 'Check if you want to turn on Banner Ads'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Auto Approve Ads')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('bannerAdsAutoApprove', array(
								'type' => 'checkbox',
								'data-trigger' => 'focus',
								'data-toggle' => 'popover',
								'data-placement' => 'top',
								'data-content' => __d('admin', 'Check if you want to auto approve ads, otherwise admin will have to approve them manually'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Maximum Amount Of Chars For Ad Title')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('bannerAdsTitleMaxLen', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
								'max' => 128,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Default Banner Size')?></label>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('admin', 'Width')?></div>
							<?=
								$this->AdminForm->input('Settings.bannerAdsSize.width', array(
									'type' => 'number',
									'min' => 1,
									'step' => 1,
								))
							?>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('admin', 'Height')?></div>
							<?=
								$this->AdminForm->input('Settings.bannerAdsSize.height', array(
									'type' => 'number',
									'min' => 1,
									'step' => 1,
								))
							?>
						</div>
					</div>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="packages" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Price & Packages')?></h2>
			</div>
			<?=
				$this->AdminForm->create('BannerAdsPackage', array(
					'url' => array('controller' => 'banner_ads', '#' => 'packages'),
					'class' => 'form-horizontal',
				))
			?>
				<div class="form-group">
					<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
					<label class="col-sm-3 control-label"><?=__d('admin', 'Type')?></label>
					<label class="col-sm-4 control-label"><?=__d('admin', 'Amount')?></label>
					<label class="col-sm-3 control-label"><?=__d('admin', 'Price')?></label>
				</div>
				<div id="packagesBody">
					<?php if(isset($this->request->data['BannerAdsPackage'][0]['id'])): ?>
						<?=$this->AdminForm->input('BannerAdsPackage.0.id')?>
					<?php endif; ?>
					<div class="form-group" id="exampleRow">
						<label class="col-sm-1 control-label">1.</label>
						<div class="col-sm-3">
							<?=$this->AdminForm->input('BannerAdsPackage.0.type', array('options' => $packagesTypes))?>
						</div>
						<div class="col-sm-4">
							<?=$this->AdminForm->input('BannerAdsPackage.0.amount')?>
						</div>
						<div class="col-sm-3">
							<div class="input-group"><?=$this->AdminForm->input('BannerAdsPackage.0.price')?></div>
						</div>
						<div class="col-sm-1">
							<?=
								$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
									array('action' => 'delete_package', $this->request->data['BannerAdsPackage'][0]['id']),
									array('escape' => false),
									__d('admin', 'Are You sure you want to delete # %s?', $this->request->data['BannerAdsPackage'][0]['id'])
								)
							?>
						</div>
					</div>
					<?php for($i = 1; $i < $packetsNo; $i++): ?>
						<div class="form-group">
							<?=$this->AdminForm->input("BannerAdsPackage.$i.id")?>
							<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
							<div class="col-sm-3"><?=$this->AdminForm->input("BannerAdsPackage.$i.type", array('options' => $packagesTypes))?></div>
							<div class="col-sm-4"><?=$this->AdminForm->input("BannerAdsPackage.$i.amount")?></div>
							<div class="col-sm-3">
								<div class="input-group"><?=$this->AdminForm->input("BannerAdsPackage.$i.price")?></div>
							</div>
							<div class="col-sm-1">
								<?=
									$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
										array('action' => 'delete_package', $this->request->data['BannerAdsPackage'][$i]['id']),
										array('escape' => false),
										__d('admin', 'Are You sure you want to delete # %s?', $this->request->data['BannerAdsPackage'][$i]['id'])
									)
								?>
							</div>
						</div>
					<?php endfor; ?>
				</div>
				<div class="col-md-12 text-right">
					<a id="addTableRowButton">
						<i title="<?=__d('admin', 'Click to add more packages')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
					</a>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
	</div>
</div>
<?php
	$this->Js->buffer("
		var RowsNo = $packetsNo == 0 ? 1 : $packetsNo;
		var exampleRow = $('#exampleRow');
		$('#addTableRowButton').click(function() {
			var newRow = exampleRow.clone();
			newRow.find('input, select').each(function(idx, obj) {
				obj = $(obj);
				obj.attr('name', obj.attr('name').replace('0', RowsNo));
				obj.attr('id', obj.attr('id').replace('0', RowsNo));
				obj.val('');
				obj.removeClass('error');
			});
			newRow.children().first().html(RowsNo + 1 + '.');
			$('#packagesBody').append(newRow);
			RowsNo += 1;
		});
	");
	$this->Js->buffer("
		jumpToTabByAnchor();
	");
?>
