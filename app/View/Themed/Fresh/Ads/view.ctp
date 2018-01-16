<body style="height: 100%; overflow: hidden;">
	<div class="viewadheader" uk-grid>
		<div class="toplogo uk-width-auto@m">
			<?=
				$this->Html->image('logo.png', array(
				'class' => 'toplogo',
				'url' => array('plugin' => null, 'controller' => 'pages', 'action' => 'display', 'home'),
				))
				?>
		</div>
		<div id="progressField" class="info uk-width-1-4@m">
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-auto@m"><i class="mdi mdi-18px mdi-timer-sand-empty"></i></div>
				<div class="uk-width-expand@m"><progress class="uk-progress" max="100"></progress></div>
			</div>
			<span><?=__('Please wait until advertisement is loading.')?></span>
		</div>
		<div id="errorField" class="info uk-width-1-4@m" style="display: none;">
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-auto@m"><i class="mdi mdi-18px mdi-alert"></i></div>
				<div class="uk-width-expand@m">
					<span>
					<?=__('We are sorry but this advertisement')?><br />
					<?=__('is no longer available.')?>
					</span>
				</div>
			</div>
		</div>
		<div class="uk-width-expand@m uk-text-right">
			<?=$this->BannerAds->show()?>
		</div>
	</div>
	<div class="removestack" uk-grid>
		<div class="uk-width-expand@m viewadbar uk-text-left">
			<?=__('You are watching:')?> <a href="<?=h($ad['Ad']['url'])?>" target="_blank" uk-tooltip title="<?=__('Open in a new window')?>"><?=h($ad['Ad']['url'])?></a>
			&emsp;<a onclick="self.close()"><i class="mdi mdi-close-circle" uk-tooltip title="<?=__('Close this window')?>"></i></a>
			<?php if($this->Session->read('Auth.User')): ?>
			&emsp;<a uk-toggle="target: #reportDiv"><i class="mdi mdi-flag" uk-tooltip title="<?=__('Report this ad')?>"></i></a>
			<?php endif; ?>
		</div>
	</div>
	</div>
	<div id="reportDiv" uk-modal>
		<div class="uk-modal-dialog" role="document">
			<button class="uk-modal-close-default" type="button" uk-close></button>
			<div id="reportDivContent" class="modal-content">
			</div>
		</div>
	</div>
	<?php if($ad['Ad']['hide_referer']): ?>
	<span style="display:none;" id="hideReferer"></span>
	<iframe name="viewadframe" width="100%" height="100%" src="about:blank"></iframe>
	<script type="text/javascript">
		$('#hideReferer').html(ReferrerKiller.linkHtml('<?=$ad['Ad']['url']?>', '', {target: 'viewadframe'}, {verticalAlign: 'bottom'}));
	</script>
	<?php else: ?>
	<iframe class="viewadframe" src="<?=h($ad['Ad']['url'])?>" sandbox="allow-scripts allow-same-origin allow-forms"></iframe>
	<?php endif; ?>
</body>
<?=$this->Html->script('adclick')?>
<script type="text/javascript">
	function verifyCaptcha() {
		$.ajax({
			url: '/ads/verifyCaptcha/<?=h($ad['Ad']['id'])?>',
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
