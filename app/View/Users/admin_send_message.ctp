<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Send E-mail To Users');?></h2>
	</div>
	<?=
		$this->AdminForm->create('PendingEmail', array(
			'class' => 'form-horizontal',
		))
	?>
		<div class="title2">
			<h2><?=__d('admin', 'Quantity')?></h2>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'To how many users do you want to send an email')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('mode')?>
			</div>
		</div>
		<div class="title2">
			<h2><?=__d('admin', 'E-mail Type')?></h2>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'What format of email do you want to send')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('format')?>
			</div>
		</div>
		<div class="title2">
			<h2><?=__d('admin', 'E-mail Details')?></h2>
		</div>
		<div class="form-group" id="singleUser" style="display: none">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Recipient Username')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('recipient')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Sender Name')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('sender_name')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Reply To')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('reply_to')?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-4 control-label"><?=__d('admin', 'Subject')?></label>
			<div class="col-sm-4">
				<?=$this->AdminForm->input('subject')?>
			</div>
		</div>
		<div class="title2">
			<h2><?=__d('admin', 'E-mail Content')?></h2>
		</div>
		<div class="text-center paddingten"><label class="label label-primary"><?=__d('admin', 'Insert Variables')?></label></div>
		<div class="btn-toolbar text-center paddingten" role="toolbar">
			<div class="btn-group" style="float: inherit;" role="group">
				<?php $i = 0; foreach($variables as $variable => $name): if($i++ == 6) break;?>
					<button data-variable="<?=h($variable)?>" type="button" class="btn btn-info"><?=h($name)?></button>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<?=
					$this->AdminForm->input('content', array(
						'type' => 'textarea',
					))
				?>
			</div>
		</div>
		<div class="text-center">
			<button class="btn btn-primary"><?=__d('admin', 'Send E-mail')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
<?php $this->TinyMCE->editor(array('mode' => 'none')) ?>
<?=$this->Js->buffer("
	$('#PendingEmailMode').on('change', function() {
		if($(this).val() == 'single') {
			$('#singleUser').show();
		} else {
			$('#singleUser').hide();
		}
	});
	$('button[data-variable]').click(function() {
		var editor = tinymce.get('PendingEmailContent');
		if(!editor) {
			var id = '#PendingEmailContent'
			var cursorPos = $(id).prop('selectionStart');
			var v = $(id).val();
			var textBefore = v.substring(0,  cursorPos);
			var textAfter  = v.substring(cursorPos, v.length);

			$(id).val(textBefore + $(this).data('variable') + textAfter);
			$(id).prop('selectionStart', cursorPos + $(this).data('variable').length);
		} else {
			editor.execCommand('mceInsertContent', false, $(this).data('variable'));
		}
	});
	$('#PendingEmailFormat').on('change', function() {
		if($(this).val() == 'html') {
			tinymce.EditorManager.execCommand('mceAddEditor', true, 'PendingEmailContent');
		} else {
			tinymce.EditorManager.execCommand('mceRemoveEditor', true, 'PendingEmailContent');
		}
	});
	if($('#PendingEmailMode').val() == 'single') {
		$('#singleUser').show();
	}
")?>
