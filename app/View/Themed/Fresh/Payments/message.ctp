<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Payment Message')?></h2>
			<h6 class="uk-text-center">
				<?=$message?>
			</h6>
		</div>
	</div>
</div>
