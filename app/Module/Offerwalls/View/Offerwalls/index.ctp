<?=$this->element('userSidebar')?>
<div class="page">
	<div class="container-fluid">
		<div class="row">
			<div class="col-md-12 padding30-sides">
				<?=$this->element('userBreadcrumbs')?>
				<?=$this->Notice->show()?>
				<div class="panel">
					<div class="padding30-col">
						<div class="col-md-12 paidoffers">
							<h5><?=__d('offerwalls', 'Offerwalls')?></h5>
							<div class="margin30-top">
								<ul class="nav nav-tabs">
									<?php $active = true; foreach($offers as $wall => $html): ?>
									<li class="nav-item"> <a <?php if($active): ?>class="nav-link active"<?php else: ?>class="nav-link"<?php endif; ?> href="#<?=h($wall)?>" aria-controls="home" role="tab" data-toggle="tab"><?=h($wall)?></a></li>
									<?php $active = false; endforeach; ?>
								</ul>
								<div class="tab-content margin30-top">
									<?php  $active = true; foreach($offers as $wall => $html): ?>
									<div id="<?=h($wall)?>" class="tab-pane fade in <?php if($active): ?>active<?php endif; ?>">
										<?=$html?>
									</div>
									<?php $active = false; endforeach; ?>
								</div>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
