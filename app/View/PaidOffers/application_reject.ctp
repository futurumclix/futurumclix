<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="<?=__('Close')?>"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title"><?=__('Reject application')?></h4>
</div>
<div id="applicationSuccess" class="alert alert-success" style="display:none"><?=__('Application sucessfully saved.')?></div>
<div id="applicationFail" class="alert alert-danger" style="display:none"><?=__('Failed to save application, please try again later.')?></div>
<?=$this->UserForm->create(false, array('id' => 'applicationForm', 'url' => array('controller' => 'paid_offers', 'action' => 'applicationReject', $id)))?>
<div class="modal-body">
	<div class="submitfield">
		<div class="row">
			<div class="col-md-12">
				<fieldset class="form-group">
					<h6 class="text-xs-center"><?=__('Please provide information why you reject that application:')?></h6>
					<?=$this->UserForm->input('reason', array('type' => 'textarea', 'class' => 'form-control'))?>
				</fieldset>
			</div>
		</div>
	</div>
</div>
<div class="modal-footer text-xs-center" >
	<a class="btn btn-danger" data-dismiss="modal"><?=__('Close')?></a>
	<button class="btn btn-primary"><?=__('Submit')?></button>
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
