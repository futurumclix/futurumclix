<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Explorer Ads Settings:')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#details"><?=__d('admin', 'General Settings')?></a></li>
		<li><a data-toggle="tab" href="#values"><?=__d('admin', 'Click Values')?></a></li>
		<li><a data-toggle="tab" href="#packages"><?=__d('admin', 'Click Packages')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="details" class="tab-pane fade in active">
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Enable')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->booleanRadio('Settings.explorerAdsActive')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Ad Title Length')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('Settings.explorerAds.titleLen', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
								'max' => 128,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Ad Description Length')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('Settings.explorerAds.descLen', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
								'max' => 1024,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Amount of subpages packages')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('Settings.explorerAds.maxSubpages', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
								'max' => 255,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Preview subpages amount')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('Settings.explorerAds.previewSubpages', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
								'max' => 255,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Preview time (one subpage)')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('Settings.explorerAds.previewTime', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Auto Approve Ads')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('Settings.explorerAds.autoApprove', array(
								'type' => 'checkbox',
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Display Description')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->booleanRadio('Settings.explorerAds.descShow')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Allow Geo-targeting')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->booleanRadio('Settings.explorerAds.geo_targetting')?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Allow Earnings From Referral Clicks')?></label>
					<div class="col-sm-4">
						<?=$this->AdminForm->booleanRadio('Settings.explorerAds.referrals_earnings')?>
					</div>
				</div>
				<?php for($i = 1; $i <= $this->request->data['Settings']['explorerAds']['maxSubpages']; $i++): ?>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('admin', 'One SubPageTimer in Seconds for %d SubPage package', $i)?></label>
						<div class="col-sm-4">
							<?=
								$this->AdminForm->input('Settings.explorerAds.timers.'.$i, array(
									'type' => 'number',
									'min' => 0,
									'step' => 1,
									'max' => 255,
									'value' => isset($this->request->data['Settings']['explorerAds']['timers'][$i]) ? $this->request->data['Settings']['explorerAds']['timers'][$i] : 0,
								))
							?>
						</div>
					</div>
				<?php endfor; ?>
				<div class="text-center col-sm-12 paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<!-- Click values -->
		<div id="values" class="tab-pane fade in">
			<?=
				$this->AdminForm->create('ClickValue', array(
					'url' => array('controller' => 'explorer_ads', '#' => 'values'),
					'class' => 'form-horizontal',
				))
			?>
				<?php $i = 0; foreach($this->request->data['ClickValue'] as $subpages => $clickValues): ?>
					<div class="title2">
						<h2><?=__d('admin', '%d SubPages Package', $subpages)?></h2>
					</div>
					<div class="form-group">
						<label class="col-sm-3 control-label"><?=__d('admin', 'Membership')?></label>
						<label class="col-sm-3 control-label"><?=__d('admin', 'User Click')?></label>
						<label class="col-sm-3 control-label"><?=__d('admin', 'Direct Referral Click')?></label>
						<label class="col-sm-3 control-label"><?=__d('admin', 'Rented Referral Click')?></label>
					</div>
					<?php foreach($clickValues as $membership_id => $clickValue): ?>
						<div class="form-group">
							<?php if(isset($clickValue['id'])): ?>
								<?=$this->AdminForm->input("ClickValue.$i.id", array('value' => $clickValue['id']))?>
							<?php endif; ?>
							<?=$this->AdminForm->input("ClickValue.$i.membership_id", array('type' => 'hidden', 'value' => $clickValue['membership_id']))?>
							<?=$this->AdminForm->input("ClickValue.$i.subpages", array('type' => 'hidden', 'value' => $clickValue['subpages']))?>
							<label class="col-sm-3 control-label"><?=$memberships[$membership_id]?></label>
							<div class="col-sm-3">
								<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.user_click_value", array('value' => $clickValue['user_click_value']))?></div>
								<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.user_click_points", array('value' => $clickValue['user_click_points']))?></div>
							</div>
							<div class="col-sm-3">
								<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.direct_referral_click_value", array('value' => $clickValue['direct_referral_click_value']))?></div>
								<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.direct_referral_click_points", array('value' => $clickValue['direct_referral_click_points']))?></div>
							</div>
							<div class="col-sm-3">
								<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.rented_referral_click_value", array('value' => $clickValue['rented_referral_click_value']))?></div>
								<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.rented_referral_click_points", array('value' => $clickValue['rented_referral_click_points']))?></div>
							</div><br /><br />
						</div>
					<?php $i++; endforeach; ?>
				<?php endforeach; ?>
				<div class="text-center col-sm-12 paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button></td>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<!-- Click packages -->
		<div id="packages" class="tab-pane fade in">
			<?=
				$this->AdminForm->create('ExplorerAdsPackage', array(
					'url' => array('controller' => 'explorer_ads', '#' => 'packages'),
					'class' => 'form-horizontal',
				))
			?>
			<div class="form-group">
				<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
				<label class="col-sm-2 control-label"><?=__d('admin', 'Type')?></label>
				<label class="col-sm-3 control-label"><?=__d('admin', 'Amount')?></label>
				<label class="col-sm-3 control-label"><?=__d('admin', 'Price')?></label>
				<label class="col-sm-2 control-label"><?=__d('admin', 'SubPages')?></label>
			</div>
			<div id="packagesBody">
				<?=$this->AdminForm->input('ExplorerAdsPackage.0.id')?>
				<div class="form-group" id="exampleRow">
					<label class="col-sm-1 control-label">1.</label>
					<div class="col-sm-2">
						<?=$this->AdminForm->input('ExplorerAdsPackage.0.type', array('options' => $packagesTypes))?>
					</div>
					<div class="col-sm-3">
						<?=$this->AdminForm->input('ExplorerAdsPackage.0.amount')?>
					</div>
					<div class="col-sm-3">
						<div class="input-group"><?=$this->AdminForm->input('ExplorerAdsPackage.0.price')?></div>
					</div>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input('ExplorerAdsPackage.0.subpages', array(
								'min' => 1,
								'max' => $this->request->data['Settings']['explorerAds']['maxSubpages'],
								'step' => 1,
							))
						?>
					</div>
					<div class="col-sm-1">
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
								array('action' => 'delete_package', $this->request->data['ExplorerAdsPackage'][0]['id']),
								array('escape' => false),
								__d('admin', 'Are You sure you want to delete # %s?', $this->request->data['ExplorerAdsPackage'][0]['id'])
							)
						?>
					</div>
				</div>
				<?php for($i = 1; $i < $packetsNo; $i++): ?>
				<div class="form-group">
					<?=$this->AdminForm->input("ExplorerAdsPackage.$i.id")?>
					<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
					<div class="col-sm-2"><?=$this->AdminForm->input("ExplorerAdsPackage.$i.type", array('options' => $packagesTypes))?></div>
					<div class="col-sm-3"><?=$this->AdminForm->input("ExplorerAdsPackage.$i.amount")?></div>
					<div class="col-sm-3">
						<div class="input-group"><?=$this->AdminForm->input("ExplorerAdsPackage.$i.price")?></div>
					</div>
					<div class="col-sm-2">
						<?=
							$this->AdminForm->input("ExplorerAdsPackage.$i.subpages", array(
								'min' => 1,
								'max' => $this->request->data['Settings']['explorerAds']['maxSubpages'],
								'step' => 1,
							))
						?>
					</div>
					<div class="col-sm-1">
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
								array('action' => 'delete_package', $this->request->data['ExplorerAdsPackage'][$i]['id']),
								array('escape' => false),
								__d('admin', 'Are You sure you want to delete # %s?', $this->request->data['ExplorerAdsPackage'][$i]['id'])
							)
						?>
					</div>
				</div>
				<?php endfor; ?>
			</div>
			<div class="col-md-12 text-right">
				<a id="addTableRowButton">
					<i title="<?=__d('admin', 'Click To Add More Packages')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
				</a>
			</div>
			<div class="clearfix"></div>
			<div class="text-center col-md-12 paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
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
		jumpToTabByAnchor();
	");
?>
