<!-- Begin of editing paid to click category -->
<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Edit PTC category:')?> <?=h($this->request->data['AdsCategory']['name'])?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#details"><?=__d('admin', 'Category Details')?></a></li>
		<li><a data-toggle="tab" href="#values"><?=__d('admin', 'Click Values')?></a></li>
		<li><a data-toggle="tab" href="#packages"><?=__d('admin', 'Click Packages')?></a></li>
	</ul>
	<!-- Category details -->
	<div class="tab-content">
		<div id="details" class="tab-pane fade in active">
			<?=
				$this->AdminForm->create('AdsCategory', array(
					'url' => array('#' => 'details'),
					'class' => 'form-horizontal',
				))
				?>
			<?=$this->AdminForm->input('AdsCategory.id')?>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Category Name')?></label>
				<div class="col-sm-4">
						<?=$this->AdminForm->input('name')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Ad Timer In Seconds')?></label>
				<div class="col-sm-4">
						<?=$this->AdminForm->input('time')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Display Description')?></label>
				<div class="col-sm-4">
						<?=$this->AdminForm->booleanRadio('allow_description')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Allow Geo-targeting')?></label>
				<div class="col-sm-4">
						<?=$this->AdminForm->booleanRadio('geo_targetting')?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Allow Earnings From Referral Clicks')?></label>
				<div class="col-sm-4">
						<?=$this->AdminForm->booleanRadio('referrals_earnings')?>
				</div>
			</div>
			<div class="text-center col-sm-12 paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<!-- End of category details -->
		<!-- Click values -->
		<div id="values" class="tab-pane fade in">
			<?=
				$this->AdminForm->create('AdsCategory', array(
					'url' => array('#' => 'values'),
					'class' => 'form-horizontal',
				))
				?>
			<?=$this->AdminForm->input('AdsCategory.id')?>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=__d('admin', 'Membership')?></label>
				<label class="col-sm-3 control-label"><?=__d('admin', 'User Click')?></label>
				<label class="col-sm-3 control-label"><?=__d('admin', 'Direct Referral Click')?></label>
				<label class="col-sm-3 control-label"><?=__d('admin', 'Rented Referral Click')?></label>
			</div>
			<div class="form-group">
			<?php foreach($this->request->data['ClickValue'] as $i => $clickValue): ?>
					<?=$this->AdminForm->input("ClickValue.$i.id")?>
						<?=$this->AdminForm->input("ClickValue.$i.membership_id", ['type' => 'hidden'])?>
					<label class="col-sm-3 control-label"><?=$memberships[$clickValue['membership_id']]?></label>
					<div class="col-sm-3">
						<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.user_click_value")?></div>
						<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.user_click_points")?></div>
					</div>
					<div class="col-sm-3">
						<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.direct_referral_click_value")?></div>
						<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.direct_referral_click_points")?></div>
					</div>
					<div class="col-sm-3">
						<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.rented_referral_click_value")?></div>
						<div class="input-group"><?=$this->AdminForm->input("ClickValue.$i.rented_referral_click_points")?></div>
					</div><br /><br />
				<?php endforeach; ?>
			</div>
			<div class="text-center col-sm-12 paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button></td>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<!-- Click packages -->
		<div id="packages" class="tab-pane fade in">
			<?=
				$this->AdminForm->create('AdsCategory', array(
					'url' => array('#' => 'packages'),
					'class' => 'form-horizontal',
				))
			?>
			<?=$this->AdminForm->input('AdsCategory.id')?>
			<div class="form-group">
				<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
				<label class="col-sm-3 control-label"><?=__d('admin', 'Type')?></label>
				<label class="col-sm-4 control-label"><?=__d('admin', 'Amount')?></label>
				<label class="col-sm-4 control-label"><?=__d('admin', 'Price')?></label>
			</div>
			<div id="packagesBody">
				<?=$this->AdminForm->input('AdsCategoryPackage.0.id')?>
				<div class="form-group" id="exampleRow">
					<label class="col-sm-1 control-label">1.</label>
					<div class="col-sm-3">
						<?=$this->AdminForm->input('AdsCategoryPackage.0.type', array('options' => $packagesTypes))?>
					</div>
					<div class="col-sm-4">
						<?=$this->AdminForm->input('AdsCategoryPackage.0.amount')?>
					</div>
					<div class="col-sm-3">
						<div class="input-group"><?=$this->AdminForm->input('AdsCategoryPackage.0.price')?></div>
					</div>
					<div class="col-sm-1">
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
								array('controller' => 'ads_category_packages', 'action' => 'delete', $this->request->data['AdsCategoryPackage'][0]['id']),
								array('escape' => false),
								__d('admin', 'Are You sure you want to delete # %s', $this->request->data['AdsCategoryPackage'][0]['id'])
							)
						?>
					</div>
				</div>
				<?php for($i = 1; $i < $packetsNo; $i++): ?>
				<div class="form-group">
					<?=$this->AdminForm->input("AdsCategoryPackage.$i.id")?>
					<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
					<div class="col-sm-3"><?=$this->AdminForm->input("AdsCategoryPackage.$i.type", array('options' => $packagesTypes))?></div>
					<div class="col-sm-4"><?=$this->AdminForm->input("AdsCategoryPackage.$i.amount")?></div>
					<div class="col-sm-3">
						<div class="input-group"><?=$this->AdminForm->input("AdsCategoryPackage.$i.price")?></div>
					</div>
					<div class="col-sm-1">
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
								array('controller' => 'ads_category_packages', 'action' => 'delete', $this->request->data['AdsCategoryPackage'][$i]['id']),
								array('escape' => false),
								__d('admin', 'Are You sure you want to delete # %s?', $this->request->data['AdsCategoryPackage'][$i]['id'])
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
	");
	$this->Js->buffer("
		jumpToTabByAnchor();
	");
?>
