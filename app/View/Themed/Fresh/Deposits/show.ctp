<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Manual Payment')?></h2>
			<h6><?=__('You have a pending deposit of')?> <?=h($this->Currency->format($deposit['Deposit']['amount']))?></h6>
			<h6><?=__('You have submitted your payment at:')?> <?=h($deposit['Deposit']['date'])?></h6>
			<h6><?=__('Please wait 24 hours for deposit to be credited on your account.')?></h6>
		</div>
	</div>
</div>
