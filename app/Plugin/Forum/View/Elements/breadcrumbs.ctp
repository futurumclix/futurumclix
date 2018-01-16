<?php if ($crumbs = $this->Breadcrumb->get()) { ?>
    <ol class="breadcrumb">
		<?php foreach ($crumbs as $i => $crumb) { ?>
			<li>
				<a href="<?php echo $this->Html->url($crumb['url']); ?>">
					<?php echo h($crumb['title']); ?>
				</a>
			</li>
		<?php } ?>
    </ol>
<?php }
