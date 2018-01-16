<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Exchange your points')?></h5>
						</div>
						<div class="col-sm-3 col-md-offset-2 moneypanel">
							<h6><?=__('Points balance')?></h6>
							<h3><?=h($user['User']['points'])?></h3>
						</div>
						<div class="col-sm-3 moneypanel">
							<h6><?=__('Current exchange rate per 1 point')?></h6>
							<h3><?=$this->Currency->format($user['ActiveMembership']['Membership']['points_value'])?></h3>
						</div>
						<div class="col-sm-3 moneypanel">
							<h6><?=__('Current value of your points')?></h6>
							<h3><?=$this->Currency->format($total_value)?></h3>
						</div>
						<?=$this->UserForm->create(false)?>
							<fieldset class="form-group col-md-offset-3 col-sm-6 margin30-top">
								<label id="maximumAmountLabel"><?=__('Enter points amount to exchange')?></label>
								<div class="input-group">
									<?=
										$this->UserForm->input('points', array(
											'type' => 'points',
											'value' => $user['User']['points'],
											'class' => 'form-control col-sm-12',
											'symbol' => 'input-group-addon',
										))
									?>
								</div>
							</fieldset>
							<div class="text-xs-center col-md-12">
								<button type="submit" class="btn btn-primary"><?=__('Exchange')?></button>
							</div>
						<?=$this->UserForm->end()?>
						<div class="clearfix"></div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
