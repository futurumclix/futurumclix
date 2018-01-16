<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="<?=__('Close')?>"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title" id="LoginAdsLabel"><?=__('Report Offer')?></h4>
</div>
<div id="reportSuccess" class="alert alert-success" style="display:none"><?=__('Your report is received, thanks for your contribution.')?></div>
<div id="reportFail" class="alert alert-danger" style="display:none"><?=__('We could not receive your report, please try again later.')?></div>
<?=$this->UserForm->create(false, array('id' => 'reportForm', 'url' => array('controller' => 'paid_offers', 'action' => 'report', $id)))?>

<div class="modal-body">
	<div class="row">
		<div class="col-md-12">
			<fieldset class="form-group">
				<div class="input-group">
					<label class="input-group-addon"><?=__('Reason')?></label>
					<?=$this->UserForm->input('type', array('class' => 'form-control', 'options' => $this->Utility->enum('ItemReport', 'type')))?>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<?=$this->UserForm->input('reason', array('type' => 'textarea', 'class' => 'form-control', 'placeholder' => 'Please choose reason for reporting this offer and type comment why do you report it.'))?>
			</fieldset>
		</div>
	</div>
</div>
<div class="modal-footer">
	<div class="text-xs-right" style="padding: 10px;">
		<button id="reportButton" class="btn btn-danger"><?=__('Report Offer')?></button>
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

