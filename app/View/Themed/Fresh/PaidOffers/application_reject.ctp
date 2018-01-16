<button class="uk-modal-close-default" type="button" uk-close></button>
<div class="uk-modal-header">
	<h2 class="uk-modal-title"><?=__('Reject application')?></h2>
</div>
<div id="applicationSuccess" uk-alert style="display:none"><?=__('Application sucessfully saved.')?></div>
<div id="applicationFail" uk-alert style="display:none"><?=__('Failed to save application, please try again later.')?></div>
<?=$this->UserForm->create(false, array('id' => 'applicationForm', 'url' => array('controller' => 'paid_offers', 'action' => 'applicationReject', $id)))?>
<div class="uk-modal-body uk-form-stacked">
	<div class="uk-margin">
		<label class="uk-form-label"><?=__('Please provide information why you reject that application:')?></label>
		<?=$this->UserForm->input('reason', array('type' => 'textarea', 'class' => 'uk-textarea'))?>
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
			url: '<?=Router::url(array('controller' => 'paid_offers', 'action' => 'applicationReject', $id))?>',
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
