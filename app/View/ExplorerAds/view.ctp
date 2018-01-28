<?php
	$validationText = __('Please wait for the site validation.');
?>
<script type="text/javascript">
function onError() {
	$('#progressField').hide();
	$('#errorField').fadeIn('slow');
}

function verifyCaptcha() {
	$.ajax({
		url: '<?=Router::url(array('action' => 'verify_captcha', $ad['ExplorerAd']['id']))?>',
		cache: false,
		type: 'POST',
		dataType: 'HTML',
		data: $('#captchaForm').serialize(),
		success: function(data) {
			$('#progressField').html(data);
		},
		complete: function(req, status) {
			if(status !== 'success') {
				onError();
			}
		}
	});
}

function atSuccess() {
	var result = false;
	$.ajax({
		url: '<?=Router::url(array('action' => 'next_subpage', $ad['ExplorerAd']['id']), true)?>',
		cache: false,
		async: false,
		dataType: 'HTML',
		success: function(data) {
			$('#progressField').html(data);
			result = true;
		},
		complete: function(req, status) {
			if(status !== 'success') {
				onError();
			}
		}
	});
	return result;
}

function atWait() {
	var result = false;
	$.ajax({
		url: '<?=Router::url(array('action' => 'next_subpage', $ad['ExplorerAd']['id']), true)?>',
		cache: false,
		async: false,
		dataType: 'HTML',
		success: function(data) {
			$('#progressText').html(data);
			result = true;
		},
		complete: function(req, status) {
			if(status !== 'success') {
				onError();
			}
		}
	});
	return result;
}

function atStart() {
	$('#progressText').html('<?=$validationText?>');
}

<?php if($this->Session->read('Auth.User')): ?>
$(document).ready(function() {
	$('#reportDivContent').load('<?=Router::url(array('action' => 'report', $ad['ExplorerAd']['id']))?>');
});
<?php endif; ?>
</script>
<?=$this->Html->script('exploreradclick')?>
<body style="height: 100%; overflow: hidden;">
	<div class="row">
		<div class="col-md-12 viewad">
			<div class="col-md-3 logo">
				<?=$this->Html->image('logo_dashboard_bw.png', array('alt' => 'Logo')); ?>
			</div>
			<div id="progressField" class="col-xs-3 info">
				<?php if($loggedIn):?>
					<i class="fa fa-spinner fa-spin fa-3x pull-left"></i>
					<div class="progressplacement">
						<progress id="progressBar" class="progress progress-striped progress-info progress-animated" max="100" value="<?=$adTime?>" style="width: 0%"></progress>
					</div>
					<span id="progressText"><?=$validationText?></span>
				<?php else: ?>
					<i class="fa fa-lock fa-3x pull-left"></i>
					<span>
						<?=__('You need to log in order to earn')?><br />
						<?=__('for watching this advertisement.')?>
					</span>
				<?php endif; ?>
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
			<?=__('You are watching:')?> <a href="<?=h($ad['ExplorerAd']['url'])?>" target="_blank" data-toggle="tooltip" data-placement="top" title="<?=__('Open in a new window')?>"><?=h($ad['ExplorerAd']['url'])?></a>
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
	<?php if($ad['ExplorerAd']['hide_referer']): ?>
		<span id="hideReferer"></span>
		<iframe name="viewadframe" id="viewadframe" width="100%" height="100%" src="about:blank" <?php if($loggedIn): ?>onLoad="explorerAfterLoad(<?=$ad['ExplorerAd']['subpages']?>, <?=$adTime?>, <?=$settings['focusAdView'] ? 'true' : 'false'?>);"<?php endif; ?>></iframe>
		<script type="text/javascript">
			$('#hideReferer').html(ReferrerKiller.linkHtml('<?=$ad['ExplorerAd']['url']?>', '', {target: 'viewadframe'}, {verticalAlign: 'bottom', onLoad: "$('#hideReferer').hide();"}));
		</script>
	<?php else: ?>
		<iframe class="viewadframe" id="viewadframe" src="<?=h($ad['ExplorerAd']['url'])?>" sandbox="allow-scripts allow-same-origin allow-forms" <?php if($loggedIn): ?>onLoad="explorerAfterLoad(<?=$ad['ExplorerAd']['subpages']?>, <?=$adTime?>, <?=$settings['focusAdView'] ? 'true' : 'false'?>);"<?php endif; ?>></iframe>
	<?php endif; ?>
</body>
