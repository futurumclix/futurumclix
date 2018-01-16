<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Exchange your points')?></h2>
			<div class="uk-child-width-1-3@m uk-grid-small uk-grid-match" uk-grid>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Points balance')?></h6>
						<h3><?=h($user['User']['points'])?></h3>
					</div>
				</div>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Current exchange rate per 1 point')?></h6>
						<h3><?=$this->Currency->format($user['ActiveMembership']['Membership']['points_value'])?></h3>
					</div>
				</div>
				<div>
					<div class="uk-card uk-card-body">
						<h6><?=__('Current value of your points')?></h6>
						<h3><?=$this->Currency->format($total_value)?></h3>
					</div>
				</div>
			</div>
			<?=$this->UserForm->create(false, array('class' => 'uk-form-stacked'))?>
			<div class="uk-margin uk-flex-center">
				<label class="uk-form-label" id="maximumAmountLabel"><?=__('Enter points amount to exchange')?></label>
				<?=
					$this->UserForm->input('points', array(
						'type' => 'points',
						'placeholder' => $user['User']['points'],
						'class' => 'uk-input',
						'symbol' => 'uk-invisible',
					))
					?>
			</div>
			<div class="uk-text-center">
				<button type="submit" class="uk-button uk-button-primary"><?=__('Exchange')?></button>
			</div>
			<?=$this->UserForm->end()?>
		</div>
	</div>
</div>
