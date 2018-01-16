<button class="uk-modal-close-default" type="button" uk-close></button>
<div class="uk-modal-header">
	<h2 class="uk-modal-title" id="SubmitOfferWindow"><?=h($offer['PaidOffer']['title'])?></h2>
</div>
<div id="applicationSuccess" uk-alert style="display:none"><?=__('Application sucessfully saved.')?></div>
<div id="applicationFail" uk-alert style="display:none"><?=__('Failed to save application, please try again later.')?></div>
<?=$this->UserForm->create('PaidOffersApplication', array('id' => 'applicationForm', 'url' => array('controller' => 'paid_offers', 'action' => 'applicationAdd', $id)))?>
<div class="uk-modal-body uk-form-stacked">
	<?=h($offer['PaidOffer']['description'])?>
	<div class="uk-margin">
		<label class="uk-form-label"><?=__('Please provide information that you have completed above offer:')?></label>
		<?=$this->UserForm->input('description', array('type' => 'textarea', 'class' => 'uk-textarea'))?>
	</div>
</div>
<div class="uk-modal-footer uk-text-center">
	<button class="uk-button uk-button-danger uk-modal-close"><?=__('Close')?></button>
	<button class="uk-button uk-button-primary"><?=__('Submit')?></button>
</div>
<?=$this->UserForm->end()?>
<script>
	$('#applicationForm').submit(function(event) {
		event.preventDefault();
		var dataStr = $(this).serialize();
		$.ajax({
			type: "POST",
			data: dataStr,
			url: '<?=Router::url(array('controller' => 'paid_offers', 'action' => 'applicationAdd', $id))?>',
			success: function(msg) {
				$('#applicationForm').hide();
				$('#applicationTitle').hide();
				$('#applicationSuccess').show();
			},
			error: function(msg) {
				$('#applicationForm').hide();
				$('#applicationTitle').hide();
				$('#applicationFail').show();
			}
		});
	});
</script>
