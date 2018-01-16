<div class="modal-header">
	<button type="button" class="close" data-dismiss="modal" aria-label="<?=__d('admin', 'Close')?>"><span aria-hidden="true">&times;</span></button>
	<h4 class="modal-title"><?=__d('admin', 'Application details')?></h4>
</div>
<div id="applicationSuccess" class="alert alert-success" style="display:none"><?=__d('admin', 'Application sucessfully saved.')?></div>
<div id="applicationFail" class="alert alert-danger" style="display:none"><?=__d('admin', 'Failed to save application, please try again later.')?></div>
<?=$this->AdminForm->create(false, array('id' => 'applicationForm', 'url' => array('controller' => 'paid_offers', 'action' => 'applicationDetails', $id)))?>
<div class="modal-body">
	<p><?=h($description)?></p>
	<div class="input-group">
		<label class="input-group-addon"><?=__d('admin', 'Status')?></label>
		<?=$this->AdminForm->input('status', array('class' => 'form-control', 'options' => $this->Utility->enum('PaidOffersApplication', 'status'), 'default' => PaidOffersApplication::ACCEPTED))?>
	</div>
	<?=$this->AdminForm->input('reason', array('type' => 'textarea', 'class' => 'form-control', 'style' => 'display:none;', 'placeholder' => 'Please enter the reason for rejecting this application'))?>
</div>
<div class="modal-footer" style="text-align: center;">
	<button id="reportButton" class="btn btn-danger"><?=__d('admin', 'Save')?></button>
</div>
<?=$this->AdminForm->end()?>
<script>
	$('#applicationForm').submit(function(event) {
		event.preventDefault();
		var dataStr = $(this).serialize();
		$.ajax({
			type: "POST",
			data: dataStr,
			url: '<?=Router::url(array('controller' => 'paid_offers', 'action' => 'applicationDetails', $id))?>',
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
	$('#status').change(function() {
		if($('#status').val() == <?=PaidOffersApplication::REJECTED?>) {
			$('#reason').show();
		} else {
			$('#reason').hide();
		}
	});
</script>

