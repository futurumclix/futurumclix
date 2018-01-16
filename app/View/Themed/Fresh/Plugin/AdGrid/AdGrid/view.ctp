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
			<span><?=__d('ad_grid', 'Please wait until advertisement is loading.')?></span>
		</div>
		<div id="errorField" class="info uk-width-1-4@m" style="display: none;">
			<div class="uk-grid-collapse" uk-grid>
				<div class="uk-width-auto@m"><i class="mdi mdi-18px mdi-alert"></i></div>
				<div class="uk-width-expand@m">
					<span>
					<?=__d('ad_grid', 'We are sorry but this advertisement')?><br />
					<?=__d('ad_grid', 'is no longer available.')?>
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
			<?=__d('ad_grid', 'You are watching:')?> <a href="<?=h($ad['AdGridAd']['url'])?>" target="_blank" uk-tooltip title="<?=__d('ad_grid', 'Open in a new window')?>"><?=h($ad['AdGridAd']['url'])?></a>
			&emsp;<a onclick="self.close()"><i class="mdi mdi-close-circle" uk-tooltip title="<?=__d('ad_grid', 'Close this window')?>"></i></a>
			<?php if($this->Session->read('Auth.User')): ?>
			&emsp;<a uk-toggle="target: #reportDiv"><i class="mdi mdi-flag" uk-tooltip title="<?=__d('ad_grid', 'Report this ad')?>"></i></a>
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
