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
							<h5><?=__('Payment Message')?></h5>
						</div>
						<div class="col-md-12 margin30-top">
							<div class="alert alert-success" role="alert">
								<?=$message?>
							</div>
						</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
