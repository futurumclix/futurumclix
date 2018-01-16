<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Paid Offers Settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#generalsettings"><?=__d('admin', 'General Settings')?></a></li>
		<li><a data-toggle="tab" href="#categories"><?=__d('admin', 'Offer\'s Categories')?></a></li>
		<li><a data-toggle="tab" href="#values"><?=__d('admin', 'Offer\'s Values')?></a></li>
		<li><a data-toggle="tab" href="#packages"><?=__d('admin', 'Offer\'s Packages')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="generalsettings" class="tab-pane fade in active">
			<?=
				$this->AdminForm->create('false', array(
					'class' => 'form-horizontal',
				))
				?>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Enable Paid Offers')?></label>
				<div class="col-sm-8">
					<?=
						$this->AdminForm->input('Settings.paidOffersActive', array(
							'type' => 'checkbox',
							'data-trigger' => 'focus',
							'data-toggle' => 'popover',
							'data-placement' => 'top',
							'data-content' => __d('admin', 'Check if you want to enable Paid Offers section'),
						))
						?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Auto Approve Ads')?></label>
				<div class="col-sm-8">
					<?=
						$this->AdminForm->input('Settings.paidOffers.autoApprove', array(
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
				<label class="col-sm-4 control-label"><?=__d('admin', 'Ad Title Length')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('Settings.paidOffers.titleLength', array(
							'type' => 'number',
							'min' => 0,
							'step' => 1,
							'max' => 512,
						))
						?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Ad Description Length')?></label>
				<div class="col-sm-4">
					<?=
						$this->AdminForm->input('Settings.paidOffers.descLength', array(
							'type' => 'number',
							'min' => 0,
							'step' => 1,
							'max' => 4096,
						))
						?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Ban Members From Paid Offers After')?></label>
				<div class="col-sm-4">
					<div class="input-group">
						<?=
							$this->AdminForm->input('Settings.paidOffers.banApplications', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
								'max' => 128,
								'data-trigger' => 'focus',
								'data-toggle' => 'popover',
								'data-placement' => 'top',
								'data-content' => __d('admin', 'If user will get more than selected number of rejects from Paid Offers, he will be excluded from that section'),
							))
							?>
						<span class="input-group-addon"><?=__d('admin', 'rejected applications')?></span>
					</div>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-4 control-label"><?=__d('admin', 'Auto Approve Pending Applications After')?></label>
				<div class="col-sm-4">
					<div class="input-group">
						<?=
							$this->AdminForm->input('Settings.paidOffers.applicationAutoApproveDays', array(
								'type' => 'number',
								'min' => 0,
								'step' => 1,
								'max' => 128,
								'data-trigger' => 'focus',
								'data-toggle' => 'popover',
								'data-placement' => 'top',
								'data-content' => __d('admin', 'If advertiser will not approve pending applications, after how many days do you want to auto approve them'),
							))
							?>
						<span class="input-group-addon"><?=__d('admin', 'days')?></span>
					</div>
				</div>
			</div>
			<div class="text-center col-sm-12 paddingten">
				<button class="btn btn-primary"><?=__d('admin', 'Save Info')?></button>
			</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="categories" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Offer\'s categories')?></h2>
			</div>
			<?=
				$this->AdminForm->create('PaidOffersCategory', array(
					'url' => array('controller' => 'paid_offers', '#' => 'categories'),
					'class' => 'form-horizontal',
				))
			?>
				<div class="form-group">
					<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
					<label class="col-sm-10 control-label"><?=__d('admin', 'Name')?></label>
					<label class="col-sm-1 control-label"><?=__d('admin', 'Actions')?></label>
				</div>
				<div id="categoriesBody">
					<?php if(isset($this->request->data['PaidOffersCategory'][0]['id'])): ?>
						<?=$this->AdminForm->input('PaidOffersCategory.0.id')?>
					<?php endif; ?>
					<div class="form-group" id="categoriesExampleRow">
						<label class="col-sm-1 control-label">1.</label>
						<div class="col-sm-10"><?=$this->AdminForm->input('PaidOffersCategory.0.name')?></div>
						<div class="col-sm-1 text-center" data-cleanup="yes">
							<?php if(isset($this->request->data['PaidOffersCategory'][0]['id'])): ?>
								<?=
									$this->AdminForm->postLink('<i title="'.__d('admin', 'Click to delete this category').'" class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>',
										array('action' => 'deleteCategory', $this->request->data['PaidOffersCategory'][0]['id']),
										array('escape' => false),
										__d('admin', 'Are you sure you want to delete category "%s"?', $this->request->data['PaidOffersCategory'][0]['name'])
									)
								?>
							<?php endif; ?>
						</div>
					</div>
					<?php for($i = 1, @$max = count($this->request->data['PaidOffersCategory']); $i < $max; $i++): ?>
						<div class="form-group">
							<?=$this->AdminForm->input("PaidOffersCategory.$i.id")?>
							<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
							<div class="col-sm-10"><?=$this->AdminForm->input("PaidOffersCategory.$i.name")?></div>
							<div class="col-sm-1 text-center">
								<?php if(isset($this->request->data['PaidOffersCategory'][$i]['id'])): ?>
									<?=
										$this->AdminForm->postLink('<i title="'.__d('admin', 'Click to delete this category').'" class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>',
											array('action' => 'deleteCategory', $this->request->data['PaidOffersCategory'][$i]['id']),
											array('escape' => false),
											__d('admin', 'Are you sure you want to delete category "%s"?', $this->request->data['PaidOffersCategory'][$i]['name'])
										)
									?>
								<?php endif; ?>
							</div>
						</div>
					<?php endfor; ?>
				</div>
				<div class="col-md-12 text-right">
					<a id="addCategoriesTableRowButton">
						<i title="<?=__d('admin', 'Click to add more categories')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
					</a>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="values" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Offer\'s values')?></h2>
			</div>
			<?=
				$this->AdminForm->create('PaidOffersValue', array(
					'url' => array('controller' => 'paid_offers', '#' => 'values'),
					'class' => 'form-horizontal',
				))
			?>
				<div class="form-group">
					<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
					<label class="col-sm-10 control-label"><?=__d('admin', 'Value')?></label>
					<label class="col-sm-1 control-label"><?=__d('admin', 'Actions')?></label>
				</div>
				<div id="valuesBody">
					<?php if(isset($this->request->data['PaidOffersValue'][0]['id'])): ?>
						<?=$this->AdminForm->input('PaidOffersValue.0.id')?>
					<?php endif; ?>
					<div class="form-group" id="valuesExampleRow">
						<label class="col-sm-1 control-label">1.</label>
						<div class="col-sm-10"><div class="input-group"><?=$this->AdminForm->input('PaidOffersValue.0.value', array('type' => 'money'))?></div></div>
						<div class="col-sm-1 text-center" data-cleanup="yes">
							<?php if(isset($this->request->data['PaidOffersValue'][0]['id'])): ?>
								<?=
									$this->AdminForm->postLink('<i title="'.__d('admin', 'Click to delete this value').'" class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>',
										array('action' => 'deleteValue', $this->request->data['PaidOffersValue'][0]['id']),
										array('escape' => false),
										__d('admin', 'Are you sure you want to delete value of %s?', $this->request->data['PaidOffersValue'][0]['value'])
									)
								?>
							<?php endif; ?>
						</div>
					</div>
					<?php for($i = 1, @$max = count($this->request->data['PaidOffersValue']); $i < $max; $i++): ?>
						<div class="form-group">
							<?=$this->AdminForm->input("PaidOffersValue.$i.id")?>
							<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
							<div class="col-sm-10"><div class="input-group"><?=$this->AdminForm->input("PaidOffersValue.$i.value")?></div></div>
							<div class="col-sm-1 text-center">
								<?php if(isset($this->request->data['PaidOffersValue'][$i]['id'])): ?>
									<?=
										$this->AdminForm->postLink('<i title="'.__d('admin', 'Click to delete this value').'" class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>',
											array('action' => 'deleteValue', $this->request->data['PaidOffersValue'][$i]['id']),
											array('escape' => false),
											__d('admin', 'Are you sure you want to delete value of %s?', $this->request->data['PaidOffersValue'][$i]['value'])
										)
									?>
								<?php endif; ?>
							</div>
						</div>
					<?php endfor; ?>
				</div>
				<div class="col-md-12 text-right">
					<a id="addValuesTableRowButton">
						<i title="<?=__d('admin', 'Click to add more categories')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
					</a>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="packages" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Offer\'s packages')?></h2>
			</div>
			<?=
				$this->AdminForm->create('PaidOffersPackage', array(
					'url' => array('controller' => 'paid_offers', '#' => 'packages'),
					'class' => 'form-horizontal',
				))
			?>
				<div class="form-group">
					<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
					<label class="col-sm-3 control-label"><?=__d('admin', 'Value')?></label>
					<label class="col-sm-3 control-label"><?=__d('admin', 'Quantity')?></label>
					<label class="col-sm-3 control-label"><?=__d('admin', 'Price')?></label>
					<label class="col-sm-2 control-label text-center"><?=__d('admin', 'Action')?></label>
				</div>
				<div id="packagesBody">
					<?php if(isset($this->request->data['PaidOffersPackage'][0]['id'])): ?>
						<?=$this->AdminForm->input('PaidOffersPackage.0.id')?>
					<?php endif; ?>
					<div class="form-group" id="packagesExampleRow">
						<label class="col-sm-1 control-label">1.</label>
						<div class="col-sm-3">
							<?=$this->AdminForm->input('PaidOffersPackage.0.value', array('type' => 'select', 'options' => $packageValues))?>
						</div>
						<div class="col-sm-3"><?=$this->AdminForm->input('PaidOffersPackage.0.quantity', array('min' => 0, 'step' => 1))?></div>
						<div class="col-sm-3"><div class="input-group"><?=$this->AdminForm->input('PaidOffersPackage.0.price', array('type' => 'money'))?></div></div>
						<div class="col-sm-2 text-center" data-cleanup="yes">
							<?php if(isset($this->request->data['PaidOffersPackage'][0]['id'])): ?>
								<?=
									$this->AdminForm->postLink('<i title="'.__d('admin', 'Click to delete this package').'" class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>',
										array('action' => 'deletePackage', $this->request->data['PaidOffersPackage'][0]['id']),
										array('escape' => false),
										__d('admin', 'Are you sure you want to delete package #1?')
									)
								?>
							<?php endif; ?>
						</div>
					</div>
					<?php for($i = 1, @$max = count($this->request->data['PaidOffersPackage']); $i < $max; $i++): ?>
						<?php if(isset($this->request->data['PaidOffersPackage'][$i]['id'])): ?>
							<?=$this->AdminForm->input('PaidOffersPackage.'.$i.'.id')?>
						<?php endif; ?>
						<div class="form-group">
							<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
							<div class="col-sm-3">
								<?=$this->AdminForm->input("PaidOffersPackage.$i.value", array('type' => 'select', 'options' => $packageValues))?>
							</div>
							<div class="col-sm-3"><?=$this->AdminForm->input("PaidOffersPackage.$i.quantity", array('min' => 0, 'step' => 1))?></div>
							<div class="col-sm-3"><div class="input-group"><?=$this->AdminForm->input("PaidOffersPackage.$i.price", array('type' => 'money'))?></div></div>
							<div class="col-sm-2 text-center" data-cleanup="yes">
								<?php if(isset($this->request->data['PaidOffersPackage'][$i]['id'])): ?>
									<?=
										$this->AdminForm->postLink('<i title="'.__d('admin', 'Click to delete this package').'" class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>',
											array('action' => 'deletePackage', $this->request->data['PaidOffersPackage'][$i]['id']),
											array('escape' => false),
											__d('admin', 'Are you sure you want to delete package #%d?', $i + 1)
										)
									?>
								<?php endif; ?>
							</div>
						</div>
					<?php endfor; ?>
				</div>
				<div class="col-md-12 text-right">
					<a id="addPackagesTableRowButton">
						<i title="<?=__d('admin', 'Click to add more categories')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
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
	$categories_lines_no = isset($this->request->data['PaidOffersCategory']) ? count($this->request->data['PaidOffersCategory']) : 1;
	$values_lines_no = isset($this->request->data['PaidOffersValue']) ? count($this->request->data['PaidOffersValue']) : 1;
	$packages_lines_no = isset($this->request->data['PaidOffersPackage']) ? count($this->request->data['PaidOffersPackage']) : 1;
	$this->Js->buffer("
		var CategoriesRowsNo = $categories_lines_no;
		$('#addCategoriesTableRowButton').click(function() {
			var newRow = $('#categoriesExampleRow').clone();
			newRow.find('input, select').each(function(idx, obj) {
				obj = $(obj);
				obj.attr('name', obj.attr('name').replace('0', CategoriesRowsNo));
				obj.attr('id', obj.attr('id').replace('0', CategoriesRowsNo));
				obj.val('');
				obj.removeClass('error');
			});
			newRow.children().first().html(CategoriesRowsNo + 1 + '.');
			newRow.find('[data-cleanup=yes]').html('');
			$('#categoriesBody').append(newRow);
			CategoriesRowsNo += 1;
		});
	");
	$this->Js->buffer("
		var ValuesRowsNo = $values_lines_no;
		$('#addValuesTableRowButton').click(function() {
			var newRow = $('#valuesExampleRow').clone();
			newRow.find('input, select').each(function(idx, obj) {
				obj = $(obj);
				obj.attr('name', obj.attr('name').replace('0', ValuesRowsNo));
				obj.attr('id', obj.attr('id').replace('0', ValuesRowsNo));
				obj.val('');
				obj.removeClass('error');
			});
			newRow.children().first().html(ValuesRowsNo + 1 + '.');
			newRow.find('[data-cleanup=yes]').html('');
			$('#valuesBody').append(newRow);
			ValuesRowsNo += 1;
		});
	");
	$this->Js->buffer("
		var PackagesRowsNo = $packages_lines_no;
		$('#addPackagesTableRowButton').click(function() {
			var newRow = $('#packagesExampleRow').clone();
			newRow.find('input, select').each(function(idx, obj) {
				obj = $(obj);
				obj.attr('name', obj.attr('name').replace('0', PackagesRowsNo));
				obj.attr('id', obj.attr('id').replace('0', PackagesRowsNo));
				obj.val('');
				obj.removeClass('error');
			});
			newRow.children().first().html(PackagesRowsNo + 1 + '.');
			newRow.find('[data-cleanup=yes]').html('');
			$('#packagesBody').append(newRow);
			PackagesRowsNo += 1;
		});
	");
	$this->Js->buffer("
		jumpToTabByAnchor();
	");
?>
