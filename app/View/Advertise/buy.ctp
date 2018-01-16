<div class="container advertise">
	<div class="row">
		<div class="col">
			<h2 class="text-xs-center"><?=__('You are buying')?> PTC Advertisement</h2>
		</div>
	</div>
	<div class="row margin30-top padding30-bottom">
		<div class="col-md-12">
			<div class="panel">
				<div class="padding30-col">
					<h5>
					Paid To Click Ads</h5>
					<p>Please fill in the form below and choose your payment method. After you will process your payment, we will review your advertisement and once it's revied, it will go live.</p>
					<div class="col-md-8 col-md-offset-2 margin30-top">
						<fieldset class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><?=__('Title')?></div>
								<?=
									$this->UserForm->input('', array(
										'placeholder' => __('Enter your advertisement title'),
										'class' => 'form-control',
										'data-limit' => 30,
										'data-counter' => 'titleCounter',
									))
									?>
								<div id="titleCounter" class="input-group-addon">30</div>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><?=__('Description')?></div>
								<?=
									$this->UserForm->input('', array(
										'placeholder' => __('Enter your advertisement description'),
										'class' => 'form-control',
										'data-limit' => 30,
										'data-counter' => 'descCounter'
									))
									?>
								<div id="descCounter" class="input-group-addon">30</div>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><?=__('URL')?></div>
								<?=
									$this->UserForm->input('', array(
										'placeholder' => 'http://',
										'class' => 'form-control',
									))
									?>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="input-group">
								<span class="input-group-addon">
								<input type="checkbox">
								</span>
								<input type="text" class="form-control" data-toggle="tooltip" data-placement="top" readonly="" value="<?=__('White label my traffic')?>" title="<?=__('Hide source of the traffic.')?>">
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><?=__('Choose category')?></div>
								<select class="form-control">
									<option>Mini Ads</option>
									<option>Mini Ads</option>
								</select>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><?=__('Choose geo targetting')?></div>
								<select class="form-control">
								</select>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><?=__('Click package')?></div>
								<select class="form-control">
									<option>1000 clicks</option>
									<option>1 day</option>
								</select>
							</div>
						</fieldset>
						<fieldset class="form-group">
							<div class="input-group">
								<div class="input-group-addon"><?=__('Payment method')?></div>
								<select class="form-control">
									<option>PayPal</option>
									<option>Payza</option>
								</select>
							</div>
						</fieldset>
						<div class="row">
							<div class="col-md-12 text-xs-right">
								<button class="btn btn-primary"><?=__('Buy $12')?></button>
							</div>
						</div>
					</div>
					<div class="clearfix"></div>
				</div>
			</div>
		</div>
	</div>
</div>
