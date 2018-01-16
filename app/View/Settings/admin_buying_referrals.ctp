<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'Buying Referrals')?></h2>
	</div>
	<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Enable Selling Direct Referrals')?></label>
		<div class="col-sm-2">
			<?=
				$this->AdminForm->input('enableBuyingReferrals', array(
					'type' => 'checkbox',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-content' => __d('admin', 'Check to enable / disable referral renting.'),
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-4 control-label"><?=__d('admin', 'Who Can Be Sold (Filter)')?></label>
		<div class="col-sm-8">
			<?php
				$daysInput = $this->AdminForm->input('directMinClickDays', array(
									'type' => 'number',
									'data-toggle' => 'popover',
									'data-trigger' => 'focus',
									'style' => 'display: inherit; width: 70px; margin: 0 5px;',
				));
				echo	$this->AdminForm->radio('directFilter', array(
							'clickDays' => '<div class="radio">'.__d('admin', 'Active user which clicked at least last ').$daysInput.__d('admin', ' days').'</div>',
							'onlyActive' => '<div class="radio">'.__d('admin', 'All active users with no upline').'</div>',
							'all' => '<div class="radio">'.__d('admin', 'All members with no upline (active and inactive).').'</div>',
						), array(
							'separator' => '<br />',
							'legend' => false,
							'label' => false,
						));
			?>
		</div>
	</div>
	<div class="clearfix"></div>
	<div class="title2">
		<h2><?=__d('admin', 'Referral packs')?></h2>
	</div>
	<div id="pricesTable">
		<?php for($max = count($prices), $i = 0; $i < $max; $i++): ?>
			<?=
				$this->AdminForm->input("DirectReferralsPrice.$i.id", array(
					'type' => 'hidden',
					'value' => $prices[$i]['DirectReferralsPrice']['id'],
				));
			?>
			<div class="form-group">
				<div class="col-sm-5">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('admin', 'Referrals Amount')?></div>
						<?=
							$this->AdminForm->input("DirectReferralsPrice.$i.amount", array(
								'type' => 'number',
								'data-toggle' => 'popover',
								'data-trigger' => 'focus',
								'data-content'=> __d('admin', 'Please put number of referrals in this pack'),
								'value' => $prices[$i]['DirectReferralsPrice']['amount'],
							));
						?>
					</div>
				</div>
				<div class="col-sm-5">
					<div class="input-group">
						<div class="input-group-addon"><?=__d('admin', 'Price')?></div>
						<?=
							$this->AdminForm->input("DirectReferralsPrice.$i.price", array(
								'type' => 'money',
								'value' => $prices[$i]['DirectReferralsPrice']['price'],
							));
						?>
					</div>
				</div>
				<?php if($i): ?>
					<div class="col-sm-2 text-center">
						<?=
							$this->AdminForm->postLink('<i class="fa fa-minus-circle fa-lg" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click to delete this package').'"></i>',
								array('controller' => 'DirectReferralsPrices', 'action' => 'delete', $prices[$i]['DirectReferralsPrice']['id']),
								array('escape' => false),
								__d('admin', 'Are you sure you want to delete # %s?', $prices[$i]['DirectReferralsPrice']['id'])
							)
						?>
					</div>
				<?php endif; ?>
			</div>
			<?php if($i == $max - 1): ?>
				<div id="addRowButton" class="col-sm-12 text-right"><i id="addRowButton" title="<?=__d('admin', 'Click to add another pack')?>" data-toggle="tooltip" data-placement="left" class="fa fa-plus-circle fa-lg"></i></div>
			<?php endif;?>
		<?php endfor; ?>
	</div>
	<div class="text-center col-sm-12 paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
<?php
	$amount = __d('admin', 'Referrals amount');
	$price = __d('admin', 'Price');
	$daysInput = $this->AdminForm->input('DirectReferralsPrice.NUM.amount', array(
							'type' => 'number',
							'data-toggle' => 'popover',
							'data-trigger' => 'focus',
							'data-content'=> __d('admin', 'Please put number of referrals in this pack'),
					 ));
	$discountInput = $this->AdminForm->input('DirectReferralsPrice.NUM.price', array(
							'type' => 'money',
						  ));
	$remButton = '<a><i class="fa fa-minus-circle fa-lg remButton" data-toggle="tooltip" data-placement="top" title="'.__d('admin', 'Click to delete this range').'"></i></a>';
	$this->Js->buffer("
		var no = $max;
		$('#addRowButton').click(function clickFunc() {
			var row = '<div class=\"form-group\"><div class=\"col-sm-5\"><div class=\"input-group\"><div class=\"input-group-addon\">$amount</div>$daysInput</div></div><div class=\"col-sm-5\"><div class=\"input-group\"><div class=\"input-group-addon\">$price</div>$discountInput</div></div><div class=\"col-sm-2 text-center\">$remButton</div></div>';
			row = $(row.replace(/NUM/g, '' + no++));

			var button = $('#addRowButton');
			button.find('i').mouseleave();

			row.find('.remButton').click(function() {
				$('#pricesTable').append(button);
				row.remove();
			});

			$('#pricesTable').append(row.append(button));
		});
	");
?>
