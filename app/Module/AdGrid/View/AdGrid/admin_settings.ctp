<div class="col-md-12">
	<div class="title">
		<h2><?=__d('ad_grid_admin', 'AdGrid settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#generalsettings"><?=__d('ad_grid_admin', 'General settings')?></a></li>
		<li><a data-toggle="tab" href="#packages"><?=__d('ad_grid_admin', 'Price & Packages')?></a></li>
		<?php foreach($memberships as $membership): ?>
			<li><a data-toggle="tab" href="#<?=Inflector::slug($membership)?>"><?=__d('ad_grid_admin', '"%s" settings', $membership)?></a></li>
		<?php endforeach;?>
	</ul>
	<div class="tab-content">
		<div id="generalsettings" class="tab-pane fade in active">
			<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Auto Approve Ads')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('AdGridSettings.adGrid.autoApprove', array(
								'type' => 'checkbox',
								'data-trigger' => 'focus',
								'data-toggle' => 'popover',
								'data-content' => __d('ad_grid_admin', 'Check if you want to auto approve ads, otherwise admin will have to approve them manually'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'AdGrid Size')?></label>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('ad_grid_admin', 'Width')?></div>
							<?=
								$this->AdminForm->input('AdGridSettings.adGrid.size.width', array(
									'type' => 'number',
									'min' => 1,
									'step' => 1,
								))
							?>
						</div>
					</div>
					<div class="col-sm-4">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('ad_grid_admin', 'Height')?></div>
							<?=
								$this->AdminForm->input('AdGridSettings.adGrid.size.height', array(
									'type' => 'number',
									'min' => 1,
									'step' => 1,
								))
							?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Ad timer')?></label>
					<div class="col-sm-3">
						<div class="input-group">
							<div class="input-group-addon"><?=__d('ad_grid_admin', 'Seconds')?></div>
							<?=
								$this->AdminForm->input('AdGridSettings.adGrid.time', array(
									'type' => 'number',
									'min' => 0,
									'step' => 1,
								))
							?>
						</div>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Focus Setting')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('AdGridSettings.adGrid.focus', array(
								'type' => 'checkbox',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-content' => __d('ad_grid_admin', 'Check if you want to auto approve ads, otherwise admin will have to approve them manually'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Start Ad Timer Settings')?></label>
					<div class="col-sm-6">
						<?php
							$timeInput = $this->AdminForm->input('AdGridSettings.adGrid.delay', array(
								'type' => 'number',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-placement' => 'top',
								'data-content' => __d('ad_grid_admin', 'If advertised page does not load or load slow you can set after how many seconds timer has to start if site is not loaded fully yet. Set 0 to start timer immediately.'),
								'style' => 'display: inherit; width: 70px; margin: 0 5px;',
							));
						?>
						<?=
							$this->AdminForm->radio('AdGridSettings.adGrid.timeMode', array(
								'dual' => '<div class="radio">'.__d('ad_grid_admin', 'Start ad timer after') . $timeInput . _('seconds or after page fully loaded').'</div>',
								'immediately' => '<div class="radio">'.__d('ad_grid_admin', 'Start timer immediately').'</div>',
								'afterLoad' => '<div class="radio">'.__d('ad_grid_admin', 'Start timer only if site is fully loaded').'</div>',
							), array(
								'separator' => '<br/>',
								'legend' => false,
								'label' => false,
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Where To Credit Prizes')?></label>
					<div class="col-sm-4">
						<?=
							$this->AdminForm->input('AdGridSettings.adGrid.payMode', array(
								'options' => array(
									'account' => __d('ad_grid_admin', 'Account balance'),
									'purchase' => __d('ad_grid_admin', 'Purchase balance'),
								),
							))
						?>
					</div>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('ad_grid_admin', 'Save changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="packages" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('ad_grid_admin', 'Price & Packages')?></h2>
			</div>
			<?=
				$this->AdminForm->create('AdGridAdsPackage', array(
					'url' => array('controller' => 'adGrid', '#' => 'packages'),
					'class' => 'form-horizontal',
				))
			?>
				<div class="form-group">
					<label class="col-sm-1 control-label"><?=__d('ad_grid_admin', 'No.')?></label>
					<label class="col-sm-3 control-label"><?=__d('ad_grid_admin', 'Type')?></label>
					<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Amount')?></label>
					<label class="col-sm-3 control-label"><?=__d('ad_grid_admin', 'Price')?></label>
				</div>
				<div id="packagesBody">
					<?php if(isset($this->request->data['AdGridAdsPackage'][0]['id'])): ?>
						<?=$this->AdminForm->input('AdGridAdsPackage.0.id')?>
					<?php endif; ?>
					<div class="form-group" id="exampleRow">
						<label class="col-sm-1 control-label">1.</label>
						<div class="col-sm-3">
							<?=$this->AdminForm->input('AdGridAdsPackage.0.type', array('options' => $packagesTypes))?>
						</div>
						<div class="col-sm-4">
							<?=$this->AdminForm->input('AdGridAdsPackage.0.amount')?>
						</div>
						<div class="col-sm-3">
							<div class="input-group"><?=$this->AdminForm->input('AdGridAdsPackage.0.price')?></div>
						</div>
						<?php if(isset($this->request->data['AdGridAdsPackage'][0]['id'])): ?>
							<div class="col-sm-1">
								<?=
									$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
										array('action' => 'delete_package', $this->request->data['AdGridAdsPackage'][0]['id']),
										array('escape' => false),
										__d('admin', 'Are You sure you want to delete # %s?', $this->request->data['AdGridAdsPackage'][0]['id'])
									)
								?>
							</div>
						<?php endif; ?>
					</div>
					<?php for($i = 1; $i < $packetsNo; $i++): ?>
						<div class="form-group">
							<?=$this->AdminForm->input("AdGridAdsPackage.$i.id")?>
							<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
							<div class="col-sm-3"><?=$this->AdminForm->input("AdGridAdsPackage.$i.type", array('options' => $packagesTypes))?></div>
							<div class="col-sm-4"><?=$this->AdminForm->input("AdGridAdsPackage.$i.amount")?></div>
							<div class="col-sm-3">
								<div class="input-group"><?=$this->AdminForm->input("AdGridAdsPackage.$i.price")?></div>
							</div>
							<div class="col-sm-1">
								<?=
									$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click To Remove This Package').'"></i>',
										array('action' => 'delete_package', $this->request->data['AdGridAdsPackage'][$i]['id']),
										array('escape' => false),
										__d('admin', 'Are You sure you want to delete # %s?', $this->request->data['AdGridAdsPackage'][$i]['id'])
									)
								?>
							</div>
						</div>
					<?php endfor; ?>
				</div>
				<div class="col-md-12 text-right">
					<a id="addTableRowButton">
						<i title="<?=__d('ad_grid_admin', 'Click to add more packages')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
					</a>
				</div>
				<div class="text-center">
					<button class="btn btn-primary"><?=__d('ad_grid_admin', 'Save changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<?php foreach($memberships as $id => $membership): ?>
			<div id="<?=Inflector::slug($membership)?>" class="tab-pane fade in">
				<?=$this->AdminForm->create(false, array('class' => 'form-horizontal'))?>
					<?php if(isset($this->request->data['AdGridMembershipsOption'][$id]['id'])): ?>
						<?=$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.id')?>
					<?php endif; ?>
					<?=$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.membership_id', array('default' => $id, 'type' => 'hidden'))?>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Max Clicks Per Day')?></label>
						<div class="col-sm-2">
							<?=
								$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.clicks_per_day', array(
									'min' => 0,
									'step' => 1,
								))
							?>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Winning Probability')?></label>
						<div class="col-sm-2">
							<div class="input-group">
								<?=
									$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.win_probability', array(
										'min' => 0,
										'step' => 1,
										'max' => 1000,
									))
								?>
							</div>
						</div>
					</div>
					<div class="form-group">
						<label class="col-sm-4 control-label"><?=__d('ad_grid_admin', 'Points per click')?></label>
						<div class="col-sm-2">
							<div class="input-group">
								<?=$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.points_per_click')?>
							</div>
						</div>
					</div>
					<div class="title2">
						<h2><?=__d('ad_grid_admin', 'Prizes')?></h2>
					</div>
					<div class="form-group" id="prizesGroup<?=$id?>">
						<span id="line<?=$id.'0'?>" data-line-number="0">
							<div class="col-sm-4">
								<div class="input-group">
									<div class="input-group-addon"><?=__d('ad_grid_admin', 'Prize')?></div>
									<?=
										$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.prizes.0.prize', array(
											'type' => 'money',
										))
									?>
								</div>
							</div>
							<div class="col-sm-4">
								<div class="input-group">
									<?=
										$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.prizes.0.points', array(
											'type' => 'points',
										))
									?>
								</div>
							</div>
							<div class="col-sm-3">
								<div class="input-group">
									<div class="input-group-addon"><?=__d('ad_grid_admin', 'Probability')?></div>
									<?=
										$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.prizes.0.probability', array(
											'type' => 'number',
											'min' => 0,
											'step' => 1,
											'max' => 100,
										))
									?>
									<div class="input-group-addon">%</div>
								</div>
							</div>
							<div class="col-sm-1">
								<div class="input-group">
									<a data-lineremove="#line<?=$id.'0'?>">
										<i class="fa fa-minus-circle fa-lg" title="<?=__d('ad_grid_admin', 'Delete this prize')?>" data-placement="top" data-toggle="tooltip"></i>
									</a>
								</div>
							</div>
							<br/><br/>
						</span>
						<?php for($i = 1, @$max = count($this->request->data['AdGridMembershipsOption'][$id]['prizes']); $i < $max; $i++):?>
							<span id="line<?=$id.$i?>" data-line-number="<?=$i?>">
								<div class="col-sm-4">
									<div class="input-group">
										<div class="input-group-addon"><?=__d('ad_grid_admin', 'Prize')?></div>
										<?=
											$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.prizes.'.$i.'.prize', array(
												'type' => 'money',
											))
										?>
									</div>
								</div>
								<div class="col-sm-4">
									<div class="input-group">
										<?=
											$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.prizes.'.$i.'.points', array(
												'type' => 'points',
											))
										?>
									</div>
								</div>
								<div class="col-sm-3">
									<div class="input-group">
										<div class="input-group-addon"><?=__d('ad_grid_admin', 'Probability')?></div>
										<?=
											$this->AdminForm->input('AdGridMembershipsOption.'.$id.'.prizes.'.$i.'.probability', array(
												'type' => 'number',
												'min' => 0,
												'step' => 1,
												'max' => 100,
											))
										?>
										<div class="input-group-addon">%</div>
									</div>
								</div>
								<div class="col-sm-1">
									<div class="input-group">
										<a data-lineremove="#line<?=$id.$i?>">
											<i class="fa fa-minus-circle fa-lg" title="<?=__d('ad_grid_admin', 'Delete this prize')?>" data-placement="top" data-toggle="tooltip"></i>
										</a>
									</div>
								</div>
								<br/><br/>
							</span>
						<?php endfor;?>
					</div>
					<div class="col-sm-12 input-group text-right">
						<a class="addPrizesLine" data-group="prizesGroup<?=$id?>">
							<i class="fa fa-plus-circle fa-lg" title="<?=__d('ad_grid_admin', 'Click to add more prizes')?>" data-placement="top" data-toggle="tooltip"></i>
						</a>
					</div>
					<div class="text-center">
						<button class="btn btn-primary"><?=__d('ad_grid_admin', 'Save changes')?></button>
					</div>
				<?=$this->AdminForm->end()?>
			</div>
		<?php endforeach; ?>
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

		function removeLine() {
			var line = $($(this).data('lineremove'));

			if(line.parent().children().length <= 1) {
				line.find('input').each(function(idx, obj) {
					$(obj).val('').removeClass('error');
				});
			} else {
				line.remove();
			}
		}

		var LinesNo = 1000;
		$('.addPrizesLine').click(function() {
			var newGroup = $('span:first', $('#' + $(this).data('group'))).clone();
			var num = newGroup.data('line-number');

			newGroup.prop('id', 'line' + LinesNo);

			newGroup.find('input').each(function(idx, obj) {
				obj = $(obj);
				obj.attr('name', obj.attr('name').replace('prizes][' + num, 'prizes][' + LinesNo));
				obj.attr('id', obj.attr('id').replace('Prizes' + num, 'Prizes' + LinesNo));
				obj.val('');
				obj.removeClass('error');
			});

			newGroup.find('[data-lineremove]').each(function(idx, obj) {
				$(obj).on('click', removeLine).data('lineremove', '#line' + LinesNo);
			});

			$('#' + $(this).data('group')).append(newGroup);
			LinesNo += 1;
		});
		$('[data-lineremove]').on('click', removeLine);
		jumpToTabByAnchor();
	");
?>
