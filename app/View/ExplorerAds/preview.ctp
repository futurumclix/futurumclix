<?php
 $validationText = __('Please wait for the site validation.');
 $waitText = __('Please enter any of subpages.');
?>
<script type="text/javascript">
function atSuccess() {
	$('#progressBar').parent().hide();
	$('#progressText').hide();
	$('#acceptForm').show();
	return true;
}

function onError() {
	$('#progressField').hide();
	$('#errorField').fadeIn('slow');
	return true;
}

function atWait() {
	$('#progressText').html('<?=$waitText?>');
	return true;
}

function atStart() {
	$('#progressText').html('<?=$validationText?>');
	return true;
}

</script>
<?=$this->Html->script('exploreradclick')?>
<body style="height: 100%; overflow: hidden;">
	<div class="row">
		<div class="col-md-12 viewad">
			<div class="col-md-3 logo">
				<?=$this->Html->image('logo_dashboard_bw.png', array('alt' => 'Logo')); ?>
			</div>
			<div id="progressField" class="col-xs-3 info">
			<i class="fa fa-spinner fa-spin fa-3x pull-left"></i>
				<div class="progressplacement">
					<progress id="progressBar" class="progress progress-striped progress-info progress-animated" max="100" value="<?=$adTime?>" style="width: 0%"></progress>
				</div>
				<span id="progressText"><?=$validationText?></span>
				<div id="acceptForm" style="display: none">
					<?=$this->UserForm->create('ExplorerAd')?>
						<?=$this->UserForm->input('checked', array('type' => 'hidden', 'value' => 1))?>
						<button class="btn btn-danger"><?=__('Save')?></button>
					<?=$this->UserForm->end()?>
				</div>
			</div>
			<div id="errorField" class="col-md-3 info" style="display: none;">
				<i class="fa fa-warning fa-3x pull-left"></i>
				<span>
					<?=__('We are sorry but an error occurred,')?><br />
					<?=__('please try again later.')?>
				</span>
			</div>
			<div class="col-md-6 text-xs-right">
				<?=$this->BannerAds->show()?>
			</div>
		</div>
			<div class="col-md-12 viewadbar text-left">
				<div class="col-md-12">
					<?=__('You are watching:')?> <a href="<?=h($url)?>" target="_blank" data-toggle="tooltip" data-placement="top" title="<?=__('Open in a new window')?>"><?=h($url)?></a>
				</div>
			</div>
	</div>
	<div class="modal fade" id="reportDiv" tabindex="-1" role="dialog" aria-labelledby="reportDivLabel">
		<div class="modal-dialog" role="document">
			<div id="reportDivContent" class="modal-content">
			</div>
		</div>
	</div>
	<?php if($hide_referer): ?>
		<span id="hideReferer"></span>
		<iframe name="viewadframe" id="viewadframe" width="100%" height="100%" src="about:blank" onLoad="explorerAfterLoad(<?=$previewSubPages?>, <?=$adTime?>, true);"></iframe>
		<script type="text/javascript">
			$('#hideReferer').html(ReferrerKiller.linkHtml('<?=h($url)?>', '', {target: 'viewadframe'}, {verticalAlign: 'bottom', onLoad: "$('#hideReferer').hide();"}));
		</script>
	<?php else: ?>
		<iframe class="viewadframe" id="viewadframe" src="<?=h($url)?>" sandbox="allow-scripts allow-same-origin allow-forms" onLoad="explorerAfterLoad(<?=$previewSubPages?>, <?=$adTime?>, true);"></iframe>
	<?php endif; ?>
</body>
