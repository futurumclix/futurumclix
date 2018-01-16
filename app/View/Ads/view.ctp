<body style="height: 100%; overflow: hidden;">
	<div class="row">
		<div class="col-md-12 viewad">
			<div class="col-md-3 logo">
				<?=$this->Html->image('logo_dashboard_bw.png', array('alt' => 'Logo')); ?>
			</div>
			<div id="progressField" class="col-xs-3 info">
			<i class="fa fa-spinner fa-spin fa-3x pull-left"></i>
				<div class="progressplacement">
					<progress class="progress progress-striped"  aria-valuenow="45" aria-valuemin="0" aria-valuemax="100" style="width: 0%"></progress>
				</div>
				<span><?=__('Please wait until advertisement is loading.')?></span>
			</div>
			<div id="errorField" class="col-md-3 info" style="display: none;">
				<i class="fa fa-warning fa-3x pull-left"></i>
				<span>
					<?=__('We are sorry but this advertisement')?><br />
					<?=__('is no longer available.')?>
				</span>
			</div>
			<div class="col-md-6 text-xs-right">
				<?=$this->BannerAds->show()?>
			</div>
		</div>
		<div class="col-md-12 viewadbar text-left">
			<div class="col-md-12">
			<?=__('You are watching:')?> <a href="<?=h($ad['Ad']['url'])?>" target="_blank" data-toggle="tooltip" data-placement="top" title="<?=__('Open in a new window')?>"><?=h($ad['Ad']['url'])?></a>
			&emsp;<a onclick="self.close()"><i class="fa fa-times" data-toggle="tooltip" data-placement="top" title="<?=__('Close this window')?>"></i></a>
			<?php if($this->Session->read('Auth.User')): ?>
				&emsp;<a data-toggle="modal" data-target="#reportDiv"><i class="fa fa-flag" data-toggle="tooltip" data-placement="top" title="<?=__('Report this ad')?>"></i></a>
			<?php endif; ?>
			</div>
		</div>
	</div>
	<div class="modal fade" id="reportDiv" tabindex="-1" role="dialog" aria-labelledby="reportDivLabel">
		<div class="modal-dialog" role="document">
			<div id="reportDivContent" class="modal-content">
			</div>
		</div>
	</div>
	<?php if($ad['Ad']['hide_referer']): ?>
		<span id="hideReferer"></span>
		<iframe name="viewadframe" width="100%" height="100%" src="about:blank"></iframe>
		<script type="text/javascript">
			$('#hideReferer').html(ReferrerKiller.linkHtml('<?=$ad['Ad']['url']?>', '', {target: 'viewadframe'}, {verticalAlign: 'bottom', onLoad: "$('#hideReferer').hide();"}));
		</script>
	<?php else: ?>
		<iframe class="viewadframe" src="<?=h($ad['Ad']['url'])?>" sandbox="allow-scripts allow-same-origin allow-forms"></iframe>
	<?php endif; ?>
</body>

<?=$this->Html->script('adclick', array('inline' => false))?>
<script type="text/javascript">

function verifyCaptcha() {
	$.ajax({
		url: '<?=Router::url(array('action' => 'verifyCaptcha', $ad['Ad']['id']), true)?>',
		cache: false,
		type: 'POST',
		dataType: 'HTML',
		data: $('#captchaForm').serialize(),
		success: function(data) {
			$('#progressField').html(data);
		},
		complete: function(req, status) {
			if(status !== 'success') {
				$('#progressField').hide();
				$('#errorField').fadeIn('slow');
			}
		}
	});
}

<?php if($this->Session->read('Auth.User')): ?>
$(document).ready(function() {
	$('[data-toggle=tooltip]').tooltip();
	$('#reportDivContent').load('<?=Router::url(array('action' => 'report', $ad['Ad']['id']))?>');
});
<?php endif; ?>

var onError = function() {
	<?php if($this->Session->read('Auth.User')): ?>
		$('#progressField').hide();
		$('#errorField').fadeIn('slow');
	<?php endif; ?>
} 

<?php if($settings['Settings']['typeTimeAdView'] == 'immediately'): ?>
getProgressBar('progressField', 'errorField', '<?=h($ad['Ad']['id'])?>', onError, <?=$adTime?>, <?=$settings['Settings']['focusAdView'] ? 'true' : 'false'?>);
<?php else: ?>
var progressBarFetch = false;
$(window).load(function() {
	if(progressBarFetch == false) {
		getProgressBar('progressField', 'errorField', '<?=h($ad['Ad']['id'])?>', onError, <?=$adTime?>, <?=$settings['Settings']['focusAdView'] ? 'true' : 'false'?>);
		progressBarFetch = true;
	}
});
<?php endif; ?>

<?php if($settings['Settings']['typeTimeAdView'] == 'dual'): ?>
setTimeout(function() {
	if(progressBarFetch == false) {
		getProgressBar('progressField', 'errorField', '<?=h($ad['Ad']['id'])?>', onError, <?=$adTime?>, <?=$settings['Settings']['focusAdView'] ? 'true' : 'false'?>);
		progressBarFetch = true;
	}
}, <?=$settings['Settings']['loadTimeAdView'] * 1000?>);
<?php endif; ?>

</script>
