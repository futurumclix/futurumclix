<?=$this->element('userBreadcrumbs')?>
<div class="uk-container dashpage">
	<div class="uk-grid">
		<?=$this->element('userSidebar')?>
		<div class="uk-width-3-4@m">
			<?=$this->Notice->show()?>
			<h2 class="uk-margin-top"><?=__('Banners And Referral Links')?></h2>
			<div class="uk-margin uk-text-center">
				<label for="username"><?=__('Show Your Username As:')?></label>
				<select class="uk-select" id="username">
					<option value="plain"><?=h($this->RefLink->getUsername())?></option>
					<option value="encrypted"><?=h($this->RefLink->getUsername(true))?></option>
				</select>
			</div>
			<div class="uk-margin uk-text-center">
				<label for="reflink"><?=__('Your Referral Link:')?></label>
				<input id="reflink" data-clipboard-target="#reflink" class="uk-input reflinkcopy" value="<?=h($this->RefLink->get('/', false))?>" onmouseover="this.select();" onfocus="this.select();" readonly uk-tooltip title="<?=__('Click to copy into clipboard')?>">
			</div>
			<div class="uk-margin uk-text-center">
				<label for="username"><?=__('QR Code:')?></label>
				<button type="button" class="uk-button uk-button-primary" uk-toggle="target: #qrcode">
				<?=__('Show QR Code')?>
				</button>
			</div>
			<?php foreach($banners as $banner): ?>
			<div class="uk-text-center uk-form-stacked">
				<div class="uk-margin">
					<h2 class="uk-margin-top"><?=__('Statistical Banner')?></h2>
					<?=$this->Html->image(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername())), array(
						'alt' => Configure::read('siteName'),
						'id' => 'banner_image-'.$banner['Banner']['id'],
						))?>
				</div>
				<div class="uk-margin">
					<label class="uk-form-label"><?=__('Banner Link:')?></label>
				</div>
				<div class="uk-margin">
					<input data-clipboard-target="#banner_link-<?=$banner['Banner']['id']?>" id="banner_link-<?=$banner['Banner']['id']?>" class="uk-input reflinkcopy" value="<?=h(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true))?>" onmouseover="this.select();" onfocus="this.select();" readonly uk-tooltip title="<?=__('Click to copy into clipboard')?>">
				</div>
				<div class="uk-margin">
					<label><?=__('HTML Code:')?></label>
				</div>
				<div class="uk-margin">
					<textarea data-clipboard-target="#html_code-<?=$banner['Banner']['id']?>" id="html_code-<?=$banner['Banner']['id']?>" class="uk-textarea reflinkcopy" onmouseover="this.select();" onfocus="this.select();" readonly uk-tooltip title="<?=__('Click to copy into clipboard')?>"><?=
						$this->Html->image(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true), array(
						'alt' => Configure::read('siteName'),
						'url' => $this->RefLink->get('/', false),
						))
						?></textarea>
				</div>
				<div class="uk-margin">
					<label><?=__('Forum Code:')?></label>
				</div>
				<div class="uk-margin">
					<textarea data-clipboard-target="#forum_code-<?=$banner['Banner']['id']?>" id="forum_code-<?=$banner['Banner']['id']?>" class="uk-textarea reflinkcopy" onmouseover="this.select();" onfocus="this.select();" readonly uk-tooltip title="<?=__('Click to copy into clipboard')?>">[URL=<?=$this->RefLink->get('/', false)?>][IMG]<?=Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true)?>[/IMG][/URL]</textarea>
				</div>
			</div>
			<?php endforeach ?>
		</div>
	</div>
</div>
</div>
<div id="qrcode" uk-modal="center: true">
	<div class="uk-modal-dialog uk-modal-body" role="document">
		<h5 id="LoginAdsLabel" class="uk-modal-title">
			<?=__('QR Code:')?>
		</h5>
		<p>
		<div class="uk-text-center">
			<img id="QRCode" src="https://chart.googleapis.com/chart?chs=295x295&cht=qr&chl=<?=h(urlencode($this->RefLink->get('/', false)))?>&choe=UTF-8" />
		</div>
		</p>
		<div class="uk-text-center">
			<button type="button" class="uk-button medium black uk-modal-close" data-dismiss="modal"><?=__('Close')?></button>
		</div>
	</div>
</div>
<?php
	$this->Html->script('clipboard.min', array('inline' => false));
	$links = json_encode(array('encrypted' => $this->RefLink->get('/', true), 'plain' => $this->RefLink->get('/', false)));
	$bannersData = array();
	foreach($banners as $banner) {
	$bannersData[$banner['Banner']['id']] = array(
	'banner_link' => array(
	'plain' => Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true),
	'encrypted' => Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername(true), 'true'), true),
	),
	'html_code' => array(
	'plain' => $this->Html->image(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true), array(
	'alt' => Configure::read('siteName'),
	'url' => $this->RefLink->get('/', false),
	)),
	'encrypted' => $this->Html->image(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername(true), 'true'), true), array(
	'alt' => Configure::read('siteName'),
	'url' => $this->RefLink->get('/', true),
	)),
	),
	'forum_code' => array(
	'plain' => '[URL='.$this->RefLink->get('/', false).'][IMG]'.Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true).'[/IMG][/URL]',
	'encrypted' => '[URL='.$this->RefLink->get('/', true).'][IMG]'.Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername(true), 'true'), true).'[/IMG][/URL]',
	),
	);
	}
	$bannersData = json_encode($bannersData);
	
	$this->Js->buffer("
	$('#username').change(function() {
	var links = $links;
	var bannersData = $bannersData;
	var type = $(this).val();
	
	$('#reflink').val(links[type]);
	$('#QRCode').attr('src', 'https://chart.googleapis.com/chart?chs=300x300&cht=qr&chl=' + encodeURIComponent(links[type]) + '&choe=UTF-8');
	
	$(document).find('img[id^=banner_image-]').each(function() {
	var number = parseInt($(this).attr('id').slice('banner_image-'.length));
	$(this).attr('src', bannersData[number]['banner_link'][type]);
	});
	
	$(document).find('input[id^=banner_link-]').each(function() {
	var number = parseInt($(this).attr('id').slice('banner_link-'.length));
	$(this).val(bannersData[number]['banner_link'][type]);
	});
	
	$(document).find('textarea[id^=html_code-]').each(function() {
	var number = parseInt($(this).attr('id').slice('html_code-'.length));
	$(this).html(bannersData[number]['html_code'][type]);
	});
	
	$(document).find('textarea[id^=forum_code-]').each(function() {
	var number = parseInt($(this).attr('id').slice('forum_code-'.length));
	$(this).html(bannersData[number]['forum_code'][type]);
	});
	});
	new Clipboard('.reflinkcopy');
	
	");
	?>
