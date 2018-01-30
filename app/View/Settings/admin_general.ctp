<div class="col-md-12">
	<div class="title">
		<h2><?=__d('admin', 'General Settings')?></h2>
	</div>
	<?=$this->AdminForm->create('Settings', array('class' => 'form-horizontal'))?>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Site Name')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('siteName', array(
					'type' => 'text',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'Put name of your site which is going to be displayed on every page'),
					'default' => $settings['siteName'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Site Title')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('siteTitle', array(
					'type' => 'text',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'Put title of your site which is going to be displayed on browser\'s window'),
					'default' => $settings['siteTitle'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Site URL')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('siteURL', array(
					'type' => 'text',
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'Put full URL of your site, including http:// or https://'),
					'default' => $settings['siteURL'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Site E-mail')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('siteEmail', array(
				'type' => 'text',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Put email which is going to be used for every e-mail send from your site (like registration confirmation or support email'),
				'default' => $settings['siteEmail'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Site E-mail Sender')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('siteEmailSender', array(
				'type' => 'text',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Put sender name for site e-mail'),
				'default' => $settings['siteEmailSender'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Site Currency')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('siteCurrency', array(
				'type' => 'select',
				'options' => $currencies,
				'id' => 'siteCurrency',
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Select currency for site.'),
				'default' => $settings['siteCurrency'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Currency Symbol Display Mode')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('currencySymbol', array(
				'type' => 'select',
				'options' => array('ls' => __d('admin', 'On left, with space'), 'l' => __d('admin', 'On left, without space'), 'rs' => __d('admin', 'On right, with space'), 'r' => __d('admin', 'On right, without space')),
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Select where to display currency symbol: on left ($1.23) or right (1.23€) with ($ 1.23 or 1.23 €) or without space.'),
				'default' => $settings['currencySymbol'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Display Places After Comma')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('commaPlaces', array(
				'type' => 'number',
				'min' => 0,
				'max' => 8,
				'step' => 1,
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Select how many places after comma should be displayed in all of monetary values.'),
				'default' => $settings['commaPlaces'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Cut Trailing Zeros')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('cutTrailing', array(
				'type' => 'checkbox',
				'data-toggle' => 'popover',
				'data-trigger' => 'hover',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Check if you want to cut trailing zeros.'),
				'default' => $settings['cutTrailing'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Remind User To Update His Profile')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('remindProfile', array(
				'type' => 'checkbox',
				'data-toggle' => 'popover',
				'data-trigger' => 'hover',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Keep showing message to update user profile after signup'),
				'default' => $settings['remindProfile'],
			));
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Theme')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('siteTheme', array(
				'type' => 'select',
				'empty' => __d('admin', 'Default'),
				'options' => $themes,
				'data-toggle' => 'popover',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Select site theme'),
				'default' => $settings['siteTheme'],
			));
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Show online counter')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('onlineActive', array(
					'type' => 'checkbox',
					'data-toggle' => 'popover',
					'data-trigger' => 'hover',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'Check if you want to show how many users are currently online.'),
					'default' => $settings['onlineActive'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Clear cache')?></label>
		<div class="col-sm-6">
			<?=$this->AdminForm->postLink('Clear Cache', array('action' => 'clear_cache'), array('class' => 'btn btn-warning btn-sm'), __d('admin', 'Are you sure you want to clear cache?'))?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Double IP settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Block Same Signup IPs')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('blockSameSignupIP', array(
				'type' => 'checkbox',
				'data-toggle' => 'popover',
				'data-trigger' => 'hover',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Disallow signing up multiple users from one IP'),
				'default' => $settings['blockSameSignupIP'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Block Same Login IPs')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('blockSameLoginIP', array(
				'type' => 'checkbox',
				'data-toggle' => 'popover',
				'data-trigger' => 'hover',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Disallow logging in multiple users from one IP'),
				'default' => $settings['blockSameLoginIP'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Allow to log in with the same IP if other user (with the same IP) logged in more than')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('checkLoginIpDays', array(
				'type' => 'number',
				'step' => 1,
				'min' => 0,
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Put 0 to disable, to check double IP all the time'),
				'default' => $settings['checkLoginIpDays'],
				'style' => 'width: 15%; float: left; margin-right: 10px;',
			));
		?><label class="control-label"><?=__d('admin', ' days ago.')?></label>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Allow to signup with the same IP if other user (with the same IP) signup more than')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('checkSignupIpDays', array(
				'type' => 'number',
				'step' => 1,
				'min' => 0,
				'data-toggle' => 'popover',
				'data-trigger' => 'focus',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Put 0 to disable, to check double IP all the time'),
				'default' => $settings['checkSignupIpDays'],
				'style' => 'width: 15%; float: left; margin-right: 10px;',
			));
		?><label class="control-label"><?=__d('admin', ' days ago.')?></label>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Advertisement watching settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Clear Visited Ads')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->radio('clearVisitedAds', array(
				'accurate' => '<div class="radio">'.__d('admin', 'Accurate (24h per ad)').'</div>',
				'daily' => '<div class="radio">'.__d('admin', 'Daily (reset at 00:00 of server time)').'</div>',
				'first' => '<div class="radio">'.__d('admin', 'First (constant reset time taken from first clicked advertisement yesterday)').'</div>',
				'last' => '<div class="radio">'.__d('admin', 'Last (constant reset time taken from last clicked advertisement yesterday)').'</div>',
				'constPerUser' => '<div class="radio">'.__d('admin', 'Constant (constant reset time per user taken from very first clicked advertisement)').'</div>',
			), array(
				'separator' => '<br/>',
				'legend' => false,
				'value' => $settings['clearVisitedAds'],
				'label' => false,
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Focus Setting')?></label>
		<div class="col-sm-6">
		<?=
			$this->AdminForm->input('focusAdView', array(
				'type' => 'checkbox',
				'data-toggle' => 'popover',
				'data-trigger' => 'hover',
				'data-placement' => 'top',
				'data-content' => __d('admin', 'Turn on / turn off forcing focus while watching advertisements.'),
				'default' => $settings['focusAdView'],
			))
		?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Start Ad Timer Settings')?></label>
		<div class="col-sm-6">
		<?php
			$timeInput = $this->AdminForm->input('loadTimeAdView', array(
									'type' => 'number',
									'data-toggle' => 'popover',
									'data-trigger' => 'hover',
									'data-placement' => 'top',
									'data-content' => __d('admin', 'If advertised page does not load or load slow you can set after how many seconds timer has to start if site is not loaded fully yet. Set 0 to start timer immediately.'),
									'default' => $settings['loadTimeAdView'],
									'style' => 'display: inherit; width: 70px; margin: 0 5px;',
			));
		?>
		<?=
			$this->AdminForm->radio('typeTimeAdView', array(
				'dual' => '<div class="radio">'.__d('admin', 'Start ad timer after') . $timeInput . _('seconds or after page fully loaded').'</div>',
				'immediately' => '<div class="radio">'.__d('admin', 'Start timer immediately').'</div>',
				'afterLoad' => '<div class="radio">'.__d('admin', 'Start timer only if site is fully loaded').'</div>',
			), array(
				'separator' => '<br/>',
				'legend' => false,
				'value' => $settings['typeTimeAdView'],
				'label' => false,
			))
		?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Maintenance mode')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Put The Site Into Maintenance Mode')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('maintenanceMode', array(
					'type' => 'checkbox',
					'default' => $settings['maintenanceMode'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'My IP')?></label>
		<div class="col-sm-6">
			<span><?=$this->request->clientIp()?></span>
			<i class="fa fa-external-link" id="myIPButton" data-toggle="tooltip" data-original-title="<?=__d('admin', 'Click to enter into Bypass IP field')?>"></i>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Maintenance Mode Info')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('maintenanceInfo', array(
					'type' => 'text',
					'default' => $settings['maintenanceInfo'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Bypass IP')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('maintenanceIPs', array(
					'type' => 'text',
					'default' => $settings['maintenanceIPs'],
					'data-toggle' => 'popover',
					'data-trigger' => 'focus',
					'data-placement' => 'top',
					'data-content' => __d('admin', 'Enter IP or IPs which can access the site during maintenance mode (if more than one, separate them with comma)'),
				))
			?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'Google Analytics')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Enable Google Analytics Stats')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('googleAnalEnable', array(
					'type' => 'checkbox',
					'default' => $settings['googleAnalEnable'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Google Analytics ID')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('googleAnalID', array(
					'type' => 'text',
					'default' => $settings['googleAnalID'],
				))
			?>
		</div>
	</div>
	<div class="title2">
		<h2><?=__d('admin', 'SMTP settings')?></h2>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Enable sending emails with SMTP')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.SMTP.enable', array(
					'type' => 'checkbox',
					'default' => $settings['SMTP']['enable'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'SMTP Username')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.SMTP.username', array(
					'type' => 'text',
					'default' => $settings['SMTP']['username'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'SMTP Password')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.SMTP.password', array(
					'type' => 'text',
					'default' => $settings['SMTP']['password'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'SMTP Host')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.SMTP.host', array(
					'type' => 'text',
					'default' => $settings['SMTP']['host'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'SMTP Port')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.SMTP.port', array(
					'type' => 'text',
					'default' => $settings['SMTP']['port'],
				))
			?>
		</div>
	</div>
	<div class="form-group">
		<label class="col-sm-6 control-label"><?=__d('admin', 'Enable TLS')?></label>
		<div class="col-sm-6">
			<?=
				$this->AdminForm->input('Settings.SMTP.tls', array(
					'type' => 'checkbox',
					'default' => $settings['SMTP']['tls'],
				))
			?>
		</div>
	</div>
	<div class="col-md-12 text-center paddingten">
		<button class="btn btn-primary"><?=__d('admin', 'Save Changes')?></button>
	</div>
	<?=$this->AdminForm->end()?>
</div>
<?php
$confirmMsg = __d('admin', 'Change in site currency will deactivate any payment gateway which does not support selected currency. Are you sure?');
$this->Js->buffer("
	$('#saveChanges').click(function(event) {
		if($('#siteCurrency').val() != '{$settings['siteCurrency']}') {
			if(!confirm('$confirmMsg')) {
				event.preventDefault();
			}
		}
	});
	$('#myIPButton').click(function() {
		var newIp = $(this).parent().children().first().html();
		var ips = $('#SettingsMaintenanceIPs').val();

		if(ips.length == 0 || ips.substr(ips.length - 1) == ',') {
			$('#SettingsMaintenanceIPs').val(ips + newIp);
		} else {
			$('#SettingsMaintenanceIPs').val(ips + ',' + newIp);
		}
	});
");
?>
