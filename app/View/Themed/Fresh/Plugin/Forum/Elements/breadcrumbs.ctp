<?php if ($crumbs = $this->Breadcrumb->get()) { ?>
<div class="breadcrumbs">
	<div class="uk-container">
		<div class="uk-grid">
			<div class="uk-width-1-1">
				<ul class="uk-breadcrumb">
					<?php foreach ($crumbs as $i => $crumb) { ?>
					<li>
						<a href="<?php echo $this->Html->url($crumb['url']); ?>">
						<?php echo h($crumb['title']); ?>
						</a>
					</li>
					<?php } ?>
				</ul>
			</div>
		</div>
	</div>
</div>
<?php }
