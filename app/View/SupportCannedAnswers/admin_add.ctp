<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Add Canned Response')?></h2>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Response Content')?></h2>
	</div>
	<?=$this->AdminForm->create('SupportCannedAnswer', array('class' => 'form-horizontal'))?>
		<div class="form-group">
			<label class="col-sm-2 control-label"><?=__d('admin', 'Name')?></label>
			<div class="col-sm-10">
				<?=$this->AdminForm->input('name')?>
			</div>
		</div>
		<div class="text-center paddingten"><label class="label label-primary"><?=__d('admin', 'Insert Variables')?></label></div>
		<div class="btn-toolbar paddingten text-center" role="toolbar">
			<div class="btn-group" role="group" style="float: inherit;">
				<?php foreach($variables as $variable => $name): ?>
					<button data-variable="<?=h($variable)?>" data-textarea="SupportCannedAnswerMessage" type="button" class="btn btn-info"><?=h($name)?></button>
				<?php endforeach; ?>
			</div>
		</div>
		<div class="form-group">
			<div class="col-md-12">
				<?=$this->AdminForm->input('message')?>
			</div>
		</div>
		<div class="text-center">
			<button class="btn btn-primary"><?=__d('admin', 'Add')?></button>
		</div>
	<?=$this->AdminForm->end()?>
</div>
<?php $this->Js->buffer("
	$('button[data-textarea][data-variable]').click(function() {
		var id = '#' + $(this).data('textarea');
		var cursorPos = $(id).prop('selectionStart');
		var v = $(id).val();
		var textBefore = v.substring(0,  cursorPos);
		var textAfter  = v.substring(cursorPos, v.length);

		$(id).val(textBefore + $(this).data('variable') + textAfter);
		$(id).prop('selectionStart', cursorPos + $(this).data('variable').length);
	});
")?>
