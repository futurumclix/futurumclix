<?php
	$validationText = __('Please wait for the site validation.');
	$waitText = __('Please enter any of subpages.');
	?>
<script type="text/javascript">
	function atSuccess() {
		$('#progressBar').parent().hide();
		$('#progressText').hide();
		$('#progressIcon').hide();
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
			<span id="progressText"><?=$validationText?></span>
			<div class="uk-width-expand@m" id="acceptForm" style="display: none">
				<?=$this->UserForm->create('ExplorerAd')?>
				<?=$this->UserForm->input('checked', array('type' => 'hidden', 'value' => 1))?>
				<button class="btn btn-danger"><?=__('Save')?></button>
				<?=$this->UserForm->end()?>
			</div>
		</div>
		<div id="errorField" class="info uk-width-1-4@m" style="display: none;">
			<i class="uk-icon-warning uk-icon-large"></i>
			<span>
			<?=__('We are sorry but an error occurred,')?><br />
			<?=__('please try again later.')?>
			</span>
		</div>
		<div class="uk-width-expand@m uk-text-right">
			<?=$this->BannerAds->show()?>
		</div>
	</div>
	<div class="removestack" uk-grid>
		<div class="uk-width-expand@m viewadbar uk-text-left">
			<?=__('You are watching:')?> <a href="<?=h($url)?>" target="_blank" uk-tooltip title="<?=__('Open in a new window')?>"><?=h($url)?></a>
		</div>
	</div>
	<?php if($hide_referer): ?>
	<span style="display:none;" id="hideReferer"></span>
	<iframe name="viewadframe" id="viewadframe" width="100%" height="100%" src="about:blank" onLoad="explorerAfterLoad(<?=$previewSubPages?>, <?=$adTime?>, true);"></iframe>
	<script type="text/javascript">
		$('#hideReferer').html(ReferrerKiller.linkHtml('<?=h($url)?>', '', {target: 'viewadframe'}, {verticalAlign: 'bottom'}));
	</script>
	<?php else: ?>
	<iframe class="viewadframe" id="viewadframe" src="<?=h($url)?>" sandbox="allow-scripts allow-same-origin allow-forms" onLoad="explorerAfterLoad(<?=$previewSubPages?>, <?=$adTime?>, true);"></iframe>
	<?php endif; ?>
</body>
