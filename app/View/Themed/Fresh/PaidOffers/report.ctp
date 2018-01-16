<button class="uk-modal-close-default" type="button" uk-close></button>
<div class="uk-modal-header">
	<h2 class="uk-modal-title" id="LoginAdsLabel"><?=__('Report Offer')?></h2>
</div>
<div id="reportSuccess" uk-alert style="display:none"><?=__('Your report is received, thanks for your contribution.')?></div>
<div id="reportFail" uk-alert style="display:none"><?=__('We could not receive your report, please try again later.')?></div>
<?=$this->UserForm->create(false, array('id' => 'reportForm', 'url' => array('controller' => 'paid_offers', 'action' => 'report', $id)))?>
<div class="uk-modal-body uk-form-stacked">
	<div class="uk-margin">
		<label class="uk-form-label"><?=__('Reason')?></label>
		<?=$this->UserForm->input('type', array('class' => 'uk-select', 'options' => $this->Utility->enum('ItemReport', 'type')))?>
	</div>
	<div class="uk-margin">
		<?=$this->UserForm->input('reason', array('type' => 'textarea', 'class' => 'uk-textarea', 'placeholder' => 'Please choose reason for reporting this offer and type comment why do you report it.'))?>
	</div>
</div>
<div class="uk-modal-footer uk-text-center">
	<button id="reportButton" class="uk-button uk-button-danger"><?=__('Report Offer')?></button>
</div>
<?=$this->UserForm->end()?>
<script>
	$('#reportForm').submit(function(event) {
		event.preventDefault();
		var dataStr = $(this).serialize();
		$.ajax({
			type: "POST",
			data: dataStr,
			url: '<?=Router::url(array('controller' => 'paid_offers', 'action' => 'report', $id))?>',
			success: function(msg) {
				$('#reportForm').hide();
				$('#reportTitle').hide();
				$('#reportSuccess').show();
			},
			error: function(msg) {
				$('#reportForm').hide();
				$('#reportTitle').hide();
				$('#reportFail').show();
			}
		});
	});
</script>
