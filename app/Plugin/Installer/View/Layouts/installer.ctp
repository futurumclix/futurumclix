<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta http-equiv="x-ua-compatible" content="ie=edge">
    <title>FuturumClix - Installation</title>
    <?php
	    echo $this->Html->css('bootstrap', array('media' => 'screen'));
	    echo $this->Html->css('installer', array('media' => 'screen'));
	    echo $this->Html->script('jquery');
	    echo $this->Html->script('tether.min');
		echo $this->Html->script('bootstrap.min');
	?>
    <?=$this->fetch('script')?>
  </head>
	<body>
		<div class="container">
			<div class="row">
				<div class="col-sm-12 text-xs-center">
					<?=
						$this->Html->image('logo_dark.png', array(
							'alt' => __d('installer', 'logo'),
							'class' => 'logo',
						))
					?>
				</div>
				<div class="col-sm-12">
					<?=$this->Notice->show()?>
					<?=$this->fetch('content')?>
					<?=$this->Js->writeBuffer()?>
					<?=$this->fetch('postLink')?>
					<script>
					$(document).ready(function(){
						$("[data-toggle=tooltip]").tooltip({placement : 'top'});
					})
					</script>
				</div>
			</div>
		</div>
	</body>
</html>
