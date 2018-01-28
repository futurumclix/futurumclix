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
		result = false;
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
		result = false;
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
			<?php if($loggedIn):?>
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-auto@m"><i class="mdi mdi-18px mdi-timer-sand-empty"></i></div>
				<div class="uk-width-expand@m"><progress id="progressBar" class="uk-progress" max="100"></progress></div>
			</div>
			<span id="progressText"><?=$validationText?></span>
			<?php else: ?>
			<i class="mdi mdi-18px mdi-lock-plus"></i>
			<span>
			<?=__('You need to log in order to earn')?><br />
			<?=__('for watching this advertisement.')?>
			</span>
			<?php endif; ?>
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
			<?=__('You are watching:')?> <a href="<?=h($ad['ExplorerAd']['url'])?>" target="_blank" uk-tooltip title="<?=__('Open in a new window')?>"><?=h($ad['ExplorerAd']['url'])?></a>
			&emsp;<a onclick="self.close()"><i class="mdi mdi-close-circle" uk-tooltip title="<?=__('Close this window')?>"></i></a>
			<?php if($this->Session->read('Auth.User')): ?>
			&emsp;<a uk-toggle="target: #reportDiv"><i class="mdi mdi-flag" uk-tooltip title="<?=__('Report this ad')?>"></i></a>
			<?php endif; ?>
		</div>
	</div>
	<div id="reportDiv" uk-modal>
		<div class="uk-modal-dialog" role="document">
			<button class="uk-modal-close-default" type="button" uk-close></button>
			<div id="reportDivContent" class="modal-content">
			</div>
		</div>
	</div>
	<?php if($ad['ExplorerAd']['hide_referer']): ?>
	<span style="display:none;" id="hideReferer"></span>
	<iframe name="viewadframe" id="viewadframe" width="100%" height="100%" src="about:blank" <?php if($loggedIn): ?>onLoad="explorerAfterLoad(<?=$ad['ExplorerAd']['subpages']?>, <?=$adTime?>, <?=$settings['focusAdView'] ? 'true' : 'false'?>);"<?php endif; ?>></iframe>
	<script type="text/javascript">
		$('#hideReferer').html(ReferrerKiller.linkHtml('<?=$ad['ExplorerAd']['url']?>', '', {target: 'viewadframe'}, {verticalAlign: 'bottom'}));
	</script>
	<?php else: ?>
	<iframe class="viewadframe" id="viewadframe" src="<?=h($ad['ExplorerAd']['url'])?>" sandbox="allow-scripts allow-same-origin allow-forms" <?php if($loggedIn): ?>onLoad="explorerAfterLoad(<?=$ad['ExplorerAd']['subpages']?>, <?=$adTime?>, <?=$settings['focusAdView'] ? 'true' : 'false'?>);"<?php endif; ?>></iframe>
	<?php endif; ?>
</body>
