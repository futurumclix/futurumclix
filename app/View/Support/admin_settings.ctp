<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Tickets Settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<li class="active"><a data-toggle="tab" href="#details"><?=__d('admin', 'Details')?></a></li>
		<li><a data-toggle="tab" href="#departments"><?=__d('admin', 'Departments')?></a></li>
	</ul>
	<div class="tab-content">
		<div id="details" class="tab-pane fade in active">
			<div class="title2">
				<h2><?=__d('admin', 'Details')?></h2>
			</div>
			<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Enable Support System')?></label>
					<div class="col-sm-8">
						<?=$this->AdminForm->input('supportEnabled', array('type' => 'checkbox'))?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'For Members Only')?></label>
					<div class="col-sm-8">
						<?=
							$this->AdminForm->input('supportRequireLogin', array(
								'type' => 'checkbox',
								'data-trigger' => 'focus',
								'data-toggle' => 'popover',
								'data-content' => __d('admin', 'Check if you want to enable support system only for logged in users.'),
							))
						?>
					</div>
				</div>
				<div class="form-group">
					<label class="col-sm-4 control-label"><?=__d('admin', 'Minimum Characters')?></label>
					<div class="col-sm-2">
						<div class="input-group">
							<?=
								$this->AdminForm->input('supportMinMsgLen', array(
									'type' => 'number',
									'min' => 0,
									'max' => 65535,
									'step' => 1,
									'data-trigger' => 'focus',
									'data-toggle' => 'popover',
									'data-placement' => 'top',
									'data-content' => __d('admin', 'Minimum amount of characters a tickets needs to contain, to prevent spam and nonsense tickets.'),
								))
							?>
							<span class="input-group-addon"><?=__d('admin', 'characters')?></span>
						</div>
					</div>
				</div>
				<div class="text-center col-md-12 paddingten">
					<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
				</div>
			<?=$this->AdminForm->end()?>
		</div>
		<div id="departments" class="tab-pane fade in">
			<div class="title2">
				<h2><?=__d('admin', 'Departments')?></h2>
			</div>
			<?=
				$this->AdminForm->create('SupportDepartment', array(
					'url' => array('controller' => 'support', '#' => 'departments'),
					'class' => 'form-horizontal',
				))
			?>
				<div class="form-group">
					<label class="col-sm-1 control-label"><?=__d('admin', 'No.')?></label>
					<label class="col-sm-11 control-label"><?=__d('admin', 'Name')?></label>
				</div>
				<div id="packagesBody">
					<?php if(isset($this->request->data['SupportDepartment'][0]['id'])): ?>
						<?=$this->AdminForm->input('SupportDepartment.0.id')?>
					<?php endif; ?>
					<div class="form-group" id="exampleRow">
						<label class="col-sm-1 control-label">1.</label>
						<div class="col-sm-11"><?=$this->AdminForm->input('SupportDepartment.0.name')?></div>
					</div>
					<?php for($i = 1; $i < $departmentsNo; $i++): ?>
						<div class="form-group">
							<?=$this->AdminForm->input("SupportDepartment.$i.id")?>
							<label class="col-sm-1 control-label"><?=($i+1).'.'?></label>
							<div class="col-sm-11"><?=$this->AdminForm->input("SupportDepartment.$i.name")?></div>
						</div>
					<?php endfor; ?>
				</div>
				<div class="col-md-12 text-right">
					<a id="addTableRowButton">
						<i title="<?=__d('admin', 'Click to add more departments')?>" class="fa fa-plus-circle fa-lg" data-toggle="tooltip" data-placement="top"></i>
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
		var RowsNo = $departmentsNo == 0 ? 1 : $departmentsNo;
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
