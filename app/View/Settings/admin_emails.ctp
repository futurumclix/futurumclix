<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'System E-mails Settings')?></h2>
	</div>
	<ul class="nav nav-pills nav-stats">
		<?php $first = true;foreach($emails as $email): ?>
			<li <?php if($first): $first = false;?>class="active"<?php endif; ?>><a data-toggle="tab" href="#<?=Inflector::classify($email['Email']['name'])?>"><?=__d('admin', $email['Email']['name'])?></a></li>
		<?php endforeach; ?>
	</ul>
	<div class="tab-content">
		<?php $first = true; foreach($emails as $email): $this->request->data = $email;?>
			<div id="<?=Inflector::classify($email['Email']['name'])?>" class="tab-pane fade in<?php if($first): $first = false;?> active<?php endif; ?>">
				<div class="title2">
					<h2><?=__d('admin', 'Email Format')?></h2>
				</div>
				<?=$this->AdminForm->create('Email', array('class' => 'form-horizontal'))?>
					<?=$this->AdminForm->input('id')?>
					<div class="form-group">
						<label class="col-sm-6 control-label"><?=__d('admin', 'What format of email do you want to send?')?></label>
						<div class="col-sm-6">
							<?=$this->AdminForm->input('format', array('data-textarea' => 'EmailContent'.$email['Email']['id']))?>
						</div>
					</div>
					<div class="title2">
						<h2><?=__d('admin', 'Email Title')?></h2>
					</div>
					<div class="form-group">
						<div class="col-sm-12">
							<?=$this->AdminForm->input('subject')?>
						</div>
					</div>
					<div class="title2">
						<h2><?=__d('admin', 'Email Content')?></h2>
					</div>
					<div class="text-center paddingten">
						<label class="label label-primary"><?=__d('admin', 'Insert Variables')?></label>
					</div>
					<div class="btn-toolbar text-center paddingten" role="toolbar">
						<div class="btn-group" style="float: inherit;" role="group">
							<?php foreach($variables as $variable => $name): ?>
								<button data-variable="<?=h($variable)?>" data-textarea="<?='EmailContent'.$email['Email']['id']?>" type="button" class="btn btn-info"><?=h($name)?></button>
							<?php endforeach; ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-12">
							<?=
								$this->AdminForm->input('content', array(
									'type' => 'textarea',
									'id' => 'EmailContent'.$email['Email']['id'],
								))
							?>
						</div>
					</div>
					<div class="text-center paddingten">
						<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button></td>
					</div>
				<?=$this->AdminForm->end()?>
			</div>
		<?php endforeach; ?>
	</div>
</div>
<?php $this->TinyMCE->editor(array('mode' => 'none')) ?>
<?php $this->Js->buffer("
	$('select[data-textarea]').on('change', function() {
		if($(this).val() == 'html') {
			tinymce.EditorManager.execCommand('mceAddEditor', true, $(this).data('textarea'));
		} else {
			tinymce.EditorManager.execCommand('mceRemoveEditor', true, $(this).data('textarea'));
		}
	});
	$('button[data-textarea][data-variable]').click(function() {
		var editor = tinymce.get($(this).data('textarea'));
		if(!editor) {
			var id = '#' + $(this).data('textarea');
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
	$('select[data-textarea]').each(function(index, obj) {
		if($(obj).val() == 'html') {
			tinymce.EditorManager.execCommand('mceAddEditor', true, $(obj).data('textarea'));
		} else {
			tinymce.EditorManager.execCommand('mceRemoveEditor', true, $(obj).data('textarea'));
		}
	});
")?>
