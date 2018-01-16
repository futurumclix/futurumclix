<div class="uk-modal-header">
	<h4 class="modal-title" id="LoginAdsLabel"><?=__('Report Ad')?></h4>
</div>
<div id="reportSuccess" uk-alert style="display:none"><?=__('Your report is received, thanks for your contribution.')?></div>
<div id="reportFail" uk-alert style="display:none"><?=__('We could not receive your report, please try again later.')?></div>
<?=$this->UserForm->create(false, array('id' => 'reportForm', 'url' => array('controller' => 'ads', 'action' => 'report', $adId)))?>
<div class="uk-modal-body uk-form-horizontal">
	<div class="uk-margin">
		<label class="uk-form-label"><?=__('Reason')?></label>
		<?=$this->UserForm->input('type', array('class' => 'uk-select', 'options' => $this->Utility->enum('ItemReport', 'type')))?>
	</div>
	<div class="uk-margin">
		<?=$this->UserForm->input('reason', array('type' => 'textarea', 'class' => 'uk-textarea', 'placeholder' => 'Please choose reason for reporting this item and type comment why do you report it.'))?>
	</div>
</div>
<div class="uk-modal-footer">
	<div class="uk-margin uk-text-right">
		<button id="reportButton" class="uk-button uk-button-danger"><?=__('Report Ad')?></button>
	</div>
</div>
<?=$this->UserForm->end()?>
<script>
	$('#reportForm').submit(function(event) {
		event.preventDefault();
		var dataStr = $(this).serialize();
		$.ajax({
			type: "POST",
			data: dataStr,
			url: '<?=Router::url(array('controller' => 'ads', 'action' => 'report', $adId))?>',
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
