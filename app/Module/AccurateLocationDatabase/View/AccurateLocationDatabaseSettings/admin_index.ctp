<script>
window.onbeforeunload = function (e) {
    e = e || window.event;
    if (e) {
        e.returnValue = 'Are you sure to close this window? If you have an installation working it will fail then.';
    }
    return 'Are you sure to close this window? If you have an installation working it will fail then.';
};
</script>
<div class="col-md-12">
	<div class="title">
		<h2><?=__d('accurate_location_database_admin', 'Accurate Location Database module settings')?></h2>
	</div>
	<div class="form-group">
		<div id="StateField" class="col-sm-12">
			<?php if($installed): ?>
				<?=__d('accurate_location_database_admin', 'Accurate Location Database is currently installed. If you want to reinstall it, please truncate following tables: %s.', implode(', ', $tables))?>
			<?php elseif(!$cvsExists): ?>
				<?=__d('accurate_location_database_admin', 'Please upload your database file to: "%s" and set it readable for PHP.', $cvsPath)?>
			<?php else: ?>
				<?=__d('accurate_location_database_admin', 'You can start installation. It will take at least 20 minutes to install. Please keep this window open all the time during installation. If you will close this window during installation it will fail.')?>
				<div class="col-md-12 text-center paddingten">
					<button id="StartInstallation" class="btn btn-primary"><?=__d('accurate_location_database_admin', 'Start installation')?></button>
				</div>
			<?php endif;?>
		</div>
	</div>
</div>

<?php
$states = array(
	'start' => __d('accurate_location_database_admin', 'Starting installation...'),
	'installing' => __d('accurate_location_database_admin', 'Installing...'),
	'countries' => __d('accurate_location_database_admin', 'Extracting locations (step 1/3)...'),
	'regions' => __d('accurate_location_database_admin', 'Extracting locations (step 2/3)...'),
	'cities' => __d('accurate_location_database_admin', 'Extracting locations (step 3/3)...'),
	'done' => __d('accurate_location_database_admin', 'Installation done.'),
	'error' => __d('accurate_location_database_admin', 'Error: '),
);
$states = json_encode($states);
$URL = Router::url(array('action' => 'install'), true);
$countriesURL = Router::url(array('action' => 'extract_countries'), true);
$regionsURL = Router::url(array('action' => 'extract_regions'), true);
$citiesURL = Router::url(array('action' => 'extract_cities'), true);
$leave = __d('accurate_location_database_admin', 'Are you sure you want to close? Quitting when installation is not completed may leave database in inconsistent state.');
$this->Js->buffer("
	var States = $states;
	function setState(msg, err) {
		$('#StateField').empty();
		$('#StateField').append(msg);
		if(err) {
			$('#StateField').append(err);
		}
	}

	function extractCities(seek) {
		$.getJSON('$citiesURL/' + seek, function(json) {
			if(json.state == 'DONE') {
				setState(States.done);
			} else if(json.state == 'CONTINUE') {
				setState(States.cities);
				$('#StateField').append(json.percent + ' %');
				extractCities(json.seek);
			} else if(json.state == 'ERROR') {
				setState(States.error, json.msg);
			}
		});
	}

	function extractRegions(seek) {
		$.getJSON('$regionsURL/' + seek, function(json) {
			if(json.state == 'DONE') {
				setState(States.cities);
				extractCities(0);
			} else if(json.state == 'CONTINUE') {
				setState(States.regions);
				$('#StateField').append(json.percent + ' %');
				extractRegions(json.seek);
			} else if(json.state == 'ERROR') {
				setState(States.error, json.msg);
			}
		});
	}

	function extractCountries() {
		$.getJSON('$countriesURL/', function(json) {
			if(json.state == 'DONE') {
				setState(States.regions);
				extractRegions(0);
			} else if(json.state == 'ERROR') {
				setState(States.error, json.msg);
			}
		});
	}

	function moveInstallation(seek) {
		$.getJSON('$URL/' + seek, function(json) {
			if(json.state == 'DONE') {
				setState(States.countries);
				extractCountries();
			} else if(json.state == 'CONTINUE') {
				setState(States.installing);
				$('#StateField').append(json.percent + ' %');
				moveInstallation(json.seek);
			} else if(json.state == 'ERROR') {
				setState(States.error, json.msg);
			}
		});
	}

	$('#StartInstallation').on('click', function(e) {
		e.preventDefault();

		setState(States.start);

		moveInstallation(0);
	});

	$(window).bind('beforeunload', function() { 
		return confirm('$leave'); 
	});
");
