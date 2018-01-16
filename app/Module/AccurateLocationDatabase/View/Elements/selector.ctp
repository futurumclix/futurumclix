<div class="col-md-8 col-md-offset-2 margin30-top" id="SelectorContainer">
	<span class="AccurateLocationSelectorRow" data-row="0">
		<fieldset class="form-group">
			<div class="input-group">
				<div class="input-group-addon"><?=__d('accurate_location_database', 'Choose country')?></div>
				<?=
					$this->UserForm->input('AccurateTargettedLocations.0.country', array(
						'class' => 'CountrySelector form-control',
						'type' => 'select',
						'options' => array('*' => __d('accurate_location_database', 'All')) + $countries,
					))
				?>
			</div>
		</fieldset>
		<fieldset class="form-group">
			<div class="input-group">
				<div class="input-group-addon"><?=__d('accurate_location_database', 'Choose region')?></div>
				<?=
					$this->UserForm->input('AccurateTargettedLocations.0.region', array(
						'class' => 'RegionSelector form-control',
						'type' => 'select',
						'options' => array('*' => __d('accurate_location_database', 'All')) + (isset($this->request->data['AccurateTargettedLocations'][0]['country_regions']) ? $this->request->data['AccurateTargettedLocations'][0]['country_regions'] : array()),
					))
				?>
			</div>
		</fieldset>
		<fieldset class="form-group">
			<div class="input-group">
				<div class="input-group-addon"><?=__d('accurate_location_database', 'Choose city')?></div>
				<?=
					$this->UserForm->input('AccurateTargettedLocations.0.city', array(
						'class' => 'CitySelector form-control',
						'type' => 'select',
						'options' => array('*' => __d('accurate_location_database', 'All')) + (isset($this->request->data['AccurateTargettedLocations'][0]['region_cities']) ? $this->request->data['AccurateTargettedLocations'][0]['region_cities'] : array()),
					))
				?>
			</div>
		</fieldset>
	</span>
	<?php 
		$locations = &$this->request->data['AccurateTargettedLocations']; $max = count($locations); 
		for($i = 1; $i < $max, $l = &$locations[$i]; ++$i): 
	?>
		<span class="AccurateLocationSelectorRow" data-row="<?=$i?>">
			<fieldset class="form-group">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('accurate_location_database', 'Remove targeting')?></div>
					<div class="input-group-btn">
						<a class="RemoveButton btn btn-primary" data-row="<?=$i?>"><?=__d('accurate_location_database', 'Remove')?></a>
					</div>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('accurate_location_database', 'Choose country')?></div>
					<?=
						$this->UserForm->input("AccurateTargettedLocations.$i.country", array(
							'class' => 'CountrySelector form-control',
							'type' => 'select',
							'options' => array('*' => __d('accurate_location_database', 'All')) + $countries,
						))
					?>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('accurate_location_database', 'Choose region')?></div>
					<?=
						$this->UserForm->input("AccurateTargettedLocations.$i.region", array(
							'class' => 'RegionSelector form-control',
							'type' => 'select',
							'options' => array('*' => __d('accurate_location_database', 'All')) + $this->request->data['AccurateTargettedLocations'][$i]['country_regions'],
						))
					?>
				</div>
			</fieldset>
			<fieldset class="form-group">
				<div class="input-group">
					<div class="input-group-addon"><?=__d('accurate_location_database', 'Choose city')?></div>
					<?=
						$this->UserForm->input("AccurateTargettedLocations.$i.city", array(
							'class' => 'CitySelector form-control',
							'type' => 'select',
							'options' => array('*' => __d('accurate_location_database', 'All')) + $this->request->data['AccurateTargettedLocations'][$i]['region_cities'],
						))
					?>
				</div>
			</fieldset>
		</span>
	<?php endfor; ?>
</div>
<div class="col-md-8 col-md-offset-2 margin30-top">
	<div class="row">
		<div class="col-sm-12 text-xs-right">
			<a data-toggle="tooltip" id="AccurateLocationDatabaseSelectorAdd" title="<?=__d('accurate_location_database', 'Add another location')?>"><i class="fa fa-plus"></i></a>
		</div>
	</div>
</div>
<?php
$dataURL = Router::url(array('plugin' => 'accurate_location_database', 'controller' => 'accurate_location_database_locations', 'action' => 'getData'));
$delete = '<fieldset class="form-group">
			<div class="input-group">
				<div class="input-group-addon">'.__d('accurate_location_database', 'Remove targeting').'</div>
				<div class="input-group-btn">
					<a class="RemoveButton btn btn-primary" data-row="0">'.__d('accurate_location_database', 'Remove').'</a>
				</div>
		</fieldset>';
$delete = str_replace("\n", '', $delete);
if($max == 0) $max = 1;
$this->Js->buffer("
	var alds_rows = $max;

	function regionChange() {
		row = $(this).closest('span');
		city = row.find('#AccurateTargettedLocations' + row.data('row') + 'City');
		all = $('<option>').text('All').attr('value', '*');

		$.getJSON('$dataURL/' + $(this).val(), function(json) {
			city.empty();
			city.append(all);
			$.each(json, function(i, obj) {
				city.append($('<option>').text(obj.AccurateLocationDatabaseLocation.name).attr('value', obj.AccurateLocationDatabaseLocation.name));
			});
		});
	}

	function countryChange() {
		row = $(this).closest('span');
		region = row.find('#AccurateTargettedLocations' + row.data('row') + 'Region');
		all = $('<option>').text('All').attr('value', '*');

		$.getJSON('$dataURL/' + $(this).val(), function(json) {
			region.empty();
			region.append(all);
			$.each(json, function(i, obj) {
				region.append($('<option>').text(obj.AccurateLocationDatabaseLocation.name).attr('value', obj.AccurateLocationDatabaseLocation.name));
			});
			city = row.find('#AccurateTargettedLocations' + row.data('row') + 'City');
			city.empty();
			city.append(all.clone());
		});
		
	}

	$('#AccurateLocationDatabaseSelectorAdd').on('click', function(e) {
		e.preventDefault();

		toAdd = $('.AccurateLocationSelectorRow[data-row=0]').clone().html().replace(/0/g, '' + alds_rows);
		toAdd = '$delete'.replace(/0/g, '' + alds_rows) + toAdd;

		$('#SelectorContainer').append('<span class=\"AccurateLocationSelectorRow\" data-row=\"' + alds_rows + '\">' + toAdd + '</span>');
		$('#SelectorContainer').find('.RemoveButton[data-row=' + alds_rows  + ']').on('click', function(e) {
			e.preventDefault();
			$('.AccurateLocationSelectorRow[data-row=' + $(this).data('row') + ']').remove();
		});

		countrySelect = $('#SelectorContainer').children().last().find('#AccurateTargettedLocations' + alds_rows + 'Country');
		countrySelect.on('change', countryChange);
		countrySelect.val('*');

		regionSelect = $('#SelectorContainer').children().last().find('#AccurateTargettedLocations' + alds_rows + 'Region');
		regionSelect.on('change', regionChange);
		regionSelect.empty();
		regionSelect.append($('<option>').text('All').attr('value', '*'));

		$('#SelectorContainer').children().last().find('#AccurateTargettedLocations' + alds_rows + 'City').empty().append($('<option>').text('All').attr('value', '*'));

		alds_rows++;
	});

	$('.RemoveButton[data-row]').on('click', function(e) {
		e.preventDefault();
		$('.AccurateLocationSelectorRow[data-row=' + $(this).data('row') + ']').remove();
	});

	$('.CountrySelector').on('change', countryChange);
	$('.RegionSelector').on('change', regionChange);
");
