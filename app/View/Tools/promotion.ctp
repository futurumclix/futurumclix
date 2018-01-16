<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12">
							<h5><?=__('Banners And Referral Links')?></h5>
						</div>
						<div class="col-sm-4 margin30-top">
							<div class="form-group">
								<label for="username"><?=__('Show Your Username As:')?></label>
								<select class="form-control" id="username">
									<option value="plain"><?=h($this->RefLink->getUsername())?></option>
									<option value="encrypted"><?=h($this->RefLink->getUsername(true))?></option>
								<select>
							</div>
						</div>
						<div class="col-sm-5 margin30-top">
							<div class="form-group">
								<label for="reflink"><?=__('Your Referral Link:')?></label>
								<input id="reflink" data-clipboard-target="#reflink" class="form-control reflinkcopy" value="<?=h($this->RefLink->get('/', false))?>" onmouseover="this.select();" onfocus="this.select();" readonly data-toggle="tooltip" data-placement="top" title="<?=__('Click to copy into clipboard')?>">
							</div>
						</div>
						<div class="col-sm-3 margin30-top">
							<div class="form-group">
								<label for="username"><?=__('QR Code:')?></label>
								<button type="button" class="btn btn-default form-control" data-toggle="modal" data-target="#qrcode">
									<?=__('Show QR Code')?>
								</button>
							</div>
						</div>
					<div class="clearfix"></div>
					<?php foreach($banners as $banner): ?>
					<div class="col-md-12 margin30-top">
						<h5><?=__('Statistical Banner')?></h5>
						<div class="col-md-8 col-md-offset-2 text-xs-center">
							<div class="form-group">
								<?=$this->Html->image(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername())), array(
									'alt' => Configure::read('siteName'),
									'id' => 'banner_image-'.$banner['Banner']['id'],
								))?>
							</div>
							<div class="form-group">
								<label><?=__('Banner Link:')?></label>
								<input data-clipboard-target="#banner_link-<?=$banner['Banner']['id']?>" id="banner_link-<?=$banner['Banner']['id']?>" class="form-control reflinkcopy" value="<?=h(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true))?>" onmouseover="this.select();" onfocus="this.select();" readonly data-toggle="tooltip" data-placement="top" title="<?=__('Click to copy into clipboard')?>">
							</div>
							<div class="form-group">
								<label><?=__('HTML Code:')?></label>
								<textarea data-clipboard-target="#html_code-<?=$banner['Banner']['id']?>" id="html_code-<?=$banner['Banner']['id']?>" class="form-control reflinkcopy" onmouseover="this.select();" onfocus="this.select();" readonly data-toggle="tooltip" data-placement="top" title="<?=__('Click to copy into clipboard')?>"><?=
									$this->Html->image(Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true), array(
										'alt' => Configure::read('siteName'),
										'url' => $this->RefLink->get('/', false),
									))
								?></textarea>
							</div>
							<div class="form-group">
								<label><?=__('Forum Code:')?></label>
								<textarea data-clipboard-target="#forum_code-<?=$banner['Banner']['id']?>" id="forum_code-<?=$banner['Banner']['id']?>" class="form-control reflinkcopy" onmouseover="this.select();" onfocus="this.select();" readonly data-toggle="tooltip" data-placement="top" title="<?=__('Click to copy into clipboard')?>">[URL=<?=$this->RefLink->get('/', false)?>][IMG]<?=Router::url(array('controller' => 'banners', 'action' => 'image', $banner['Banner']['id'], $this->RefLink->getUsername()), true)?>[/IMG][/URL]</textarea>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
					<?php endforeach ?>
				</div>
			</div>
		</div>
	</div>
</div>
<div class="modal fade" id="qrcode" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-sm" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
        <h5 class="modal-title" id="myModalLabel"><?=__('QR Code:')?></h5>
      </div>
        <img id="QRCode" src="https://chart.googleapis.com/chart?chs=295x295&cht=qr&chl=<?=h(urlencode($this->RefLink->get('/', false)))?>&choe=UTF-8" />
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
