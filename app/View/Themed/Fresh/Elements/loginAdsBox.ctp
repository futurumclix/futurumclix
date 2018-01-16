<?php foreach($ads as $ad): ?>
<div class="form-group">
	<?php 
		$image = $this->Html->image($ad['LoginAd']['image_url'], array_merge(array('title' => $ad['LoginAd']['title'], 'alt' => $ad['LoginAd']['title']), $imageOptions));
		echo $this->Html->link($image, array('plugin' => '', 'controller' => 'login_ads', 'action' => 'view', $ad['LoginAd']['id']), $linkOptions);
		?>
	<br /><br />
</div>
<?php endforeach; ?>
