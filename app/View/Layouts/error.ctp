<?php
	echo $this->element('Layout/HeaderMain');
?>
<div class="container">
	<div class="row">
		<div class="col-md-12 front_text">
			<h2 class="text-xs-center"><?=__('Ooops, something went wrong...')?></h2>
			<?=$this->Session->flash()?>
			<?=$this->fetch('content')?>
		</div>	
	</div>
</div>
<?php
	echo $this->element('Layout/FooterMain');
?>
