<body style="height: 100%; overflow: hidden;">
	<div class="viewadheader uk-child-width-expand@s" uk-grid>
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
				<div id="progressIcon" class="uk-width-auto@m"><i class="mdi mdi-18px mdi-timer-sand-empty"></i></div>
				<div class="uk-width-expand@m"><progress id="progressBar" class="uk-progress" max="100"></progress></div>
			</div>
			<span id="progressText"><?=__d('ad_grid', 'Please watch your site below.')?></span>
			<div id="acceptForm" style="display: none">
				<?=$this->UserForm->create('AdGridAd')?>
				<?=$this->UserForm->input('checked', array('type' => 'hidden', 'value' => 1))?>
				<button class="uk-button uk-button-danger"><?=__d('ad_grid', 'Save')?></button>
				<?=$this->UserForm->end()?>
			</div>
		</div>
	</div>
	</div>
	</div>
	<div id="errorField" class="info uk-width-1-4@m" style="display: none;">
		<i class="uk-icon-warning uk-icon-large"></i>
		<span>
		<?=__d('ad_grid', 'We are sorry but an error occurred,')?><br />
		<?=__d('ad_grid', 'please try again later.')?>
		</span>
	</div>
	<div class="uk-width-expand@m uk-text-right">
		<?=$this->BannerAds->show()?>
	</div>
	</div>
	<div class="removestack" uk-grid>
		<div class="uk-width-expand@m viewadbar uk-text-left">
			<?=__d('ad_grid', 'You are watching:')?> <a href="<?=h($url)?>" target="_blank" uk-tooltip title="<?=__d('ad_grid', 'Open in a new window')?>"><?=h($url)?></a>
		</div>
	</div>
	<iframe class="viewadframe" src="<?=h($url)?>" sandbox="allow-scripts allow-same-origin allow-forms"></iframe>
</body>
<?=$this->Html->script('adclick')?>
<script type="text/javascript">
	function atSuccess() {
		$('#progressBar').parent().hide();
		$('#progressText').hide();
		$('#progressIcon').hide();
		$('#acceptForm').show();
	}
	
	var onError = function() {
		$('#progressField').hide();
		$('#errorField').fadeIn('slow');
	}
	
	startTimer('progressField', <?=$adTime?>, '', onError, <?=$adTime?>, true, atSuccess);
</script>
