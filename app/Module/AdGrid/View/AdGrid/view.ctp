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
				<span><?=__d('ad_grid', 'Please wait until advertisement is loading.')?></span>
			</div>
			<div id="errorField" class="col-md-3 info" style="display: none;">
				<i class="fa fa-warning fa-3x pull-left"></i>
				<span>
					<?=__d('ad_grid', 'We are sorry but this advertisement')?><br />
					<?=__d('ad_grid', 'is no longer available.')?>
				</span>
			</div>
			<div class="col-md-6 text-xs-right">
				<?=$this->BannerAds->show()?>
			</div>
		</div>
			<div class="col-md-12 viewadbar text-left">
				<div class="col-md-12">
				<?=__d('ad_grid', 'You are watching:')?> <a href="<?=h($ad['AdGridAd']['url'])?>" target="_blank" data-toggle="tooltip" data-placement="top" title="<?=__d('ad_grid', 'Open in a new window')?>"><?=h($ad['AdGridAd']['url'])?></a>
				&emsp;<a onclick="self.close()"><i class="fa fa-times" data-toggle="tooltip" data-placement="top" title="<?=__d('ad_grid', 'Close this window')?>"></i></a>
				<?php if($this->Session->read('Auth.User')): ?>
					&emsp;<a data-toggle="modal" data-target="#reportDiv"><i class="fa fa-flag" data-toggle="tooltip" data-placement="top" title="<?=__d('ad_grid', 'Report this ad')?>"></i></a>
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
	<iframe class="viewadframe" src="<?=h($ad['AdGridAd']['url'])?>" sandbox="allow-scripts allow-same-origin allow-forms"></iframe>
</body>

<?=$this->Html->script('AdGrid.adclick')?>
<script type="text/javascript">

function done(pField, x, y, onError) {
	$.ajax({
		url: '<?=Router::url(array('action' => 'done', $x, $y))?>',
		cache: false,
		type: 'GET',
		dataType: 'HTML',
		success: function(data) {
			$('#' + pField).html(data);
		},
		complete: function(req, status) {
			if(status !== 'success') {
				onError();
			}
		},
	});
}

<?php if($this->Session->read('Auth.User')): ?>
$(document).ready(function() {
	$('[data-toggle=tooltip]').tooltip();
	$('#reportDivContent').load('<?=Router::url(array('action' => 'report', $ad['AdGridAd']['id']))?>');
});
<?php endif; ?>

var onError = function() {
	<?php if($this->Session->read('Auth.User')): ?>
		$('#progressField').hide();
		$('#errorField').fadeIn('slow');
	<?php endif; ?>
} 

<?php if($settings['timeMode'] == 'immediately'): ?>
getProgressBar('progressField', 'errorField', '<?=Router::url(array('action' => 'fetchProgressBar', $x, $y))?>', <?=$x?>, <?=$y?>, onError, <?=$adTime?>, <?=$settings['focus'] ? 'true' : 'false'?>, done);
<?php else: ?>
var progressBarFetch = false;
$(window).load(function() {
	if(progressBarFetch == false) {
		getProgressBar('progressField', 'errorField', '<?=Router::url(array('action' => 'fetchProgressBar', $x, $y))?>', <?=$x?>, <?=$y?>, onError, <?=$adTime?>, <?=$settings['focus'] ? 'true' : 'false'?>, done);
		progressBarFetch = true;
	}
});
<?php endif; ?>

<?php if($settings['timeMode'] == 'dual'): ?>
setTimeout(function() {
	if(progressBarFetch == false) {
		getProgressBar('progressField', 'errorField', '<?=Router::url(array('action' => 'fetchProgressBar', $x, $y))?>', <?=$x?>, <?=$y?>, onError, <?=$adTime?>, <?=$settings['focus'] ? 'true' : 'false'?>, done);
		progressBarFetch = true;
	}
}, <?=$settings['delay'] * 1000?>);
<?php endif; ?>

</script>
