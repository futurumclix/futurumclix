<div id="SelectorContainer">
	<span class="AccurateLocationSelectorRow" data-row="0">
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=__d('accurate_location_database', 'Choose country')?></label>
			<div class="col-sm-6">
				<?=
					$this->AdminForm->input('AccurateTargettedLocations.0.country', array(
						'class' => 'CountrySelector form-control',
						'type' => 'select',
						'options' => array('*' => __d('accurate_location_database', 'All')) + $countries,
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=__d('accurate_location_database', 'Choose region')?></label>
			<div class="col-sm-6">
				<?=
					$this->AdminForm->input('AccurateTargettedLocations.0.region', array(
						'class' => 'RegionSelector form-control',
						'type' => 'select',
						'options' => array('*' => __d('accurate_location_database', 'All')) + (isset($this->request->data['AccurateTargettedLocations'][0]['country_regions']) ? $this->request->data['AccurateTargettedLocations'][0]['country_regions'] : array()),
					))
				?>
			</div>
		</div>
		<div class="form-group">
			<label class="col-sm-3 control-label"><?=__d('accurate_location_database', 'Choose city')?></label>
			<div class="col-sm-6">
				<?=
					$this->AdminForm->input('AccurateTargettedLocations.0.city', array(
						'class' => 'CitySelector form-control',
						'type' => 'select',
						'options' => array('*' => __d('accurate_location_database', 'All')) + (isset($this->request->data['AccurateTargettedLocations'][0]['region_cities']) ? $this->request->data['AccurateTargettedLocations'][0]['region_cities'] : array()),
					))
				?>
			</div>
		</div>
	</span>
	<?php 
		$locations = &$this->request->data['AccurateTargettedLocations']; $max = count($locations); 
		for($i = 1; $i < $max, $l = &$locations[$i]; ++$i): 
	?>
		<span class="AccurateLocationSelectorRow" data-row="<?=$i?>">
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=__d('admin', 'Remove targeting')?></label>
				<div class="col-sm-6">
					<a class="RemoveButton btn btn-primary" data-row="<?=$i?>"><?=__d('admin', 'Remove')?></a>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=__d('accurate_location_database', 'Choose country')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input("AccurateTargettedLocations.$i.country", array(
							'class' => 'CountrySelector form-control',
							'type' => 'select',
							'options' => array('*' => __d('accurate_location_database', 'All')) + $countries,
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=__d('accurate_location_database', 'Choose region')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input("AccurateTargettedLocations.$i.region", array(
							'class' => 'RegionSelector form-control',
							'type' => 'select',
							'options' => array('*' => __d('accurate_location_database', 'All')) + $this->request->data['AccurateTargettedLocations'][$i]['country_regions'],
						))
					?>
				</div>
			</div>
			<div class="form-group">
				<label class="col-sm-3 control-label"><?=__d('accurate_location_database', 'Choose city')?></label>
				<div class="col-sm-6">
					<?=
						$this->AdminForm->input("AccurateTargettedLocations.$i.city", array(
							'class' => 'CitySelector form-control',
							'type' => 'select',
							'options' => array('*' => __d('accurate_location_database', 'All')) + $this->request->data['AccurateTargettedLocations'][$i]['region_cities'],
						))
					?>
				</div>
			</div>
		</span>
	<?php endfor; ?>
</div>
<div class="form-group">
	<div class="col-sm-10 text-right">
		<a data-toggle="tooltip" id="AccurateLocationDatabaseSelectorAdd" title="<?=__('Add another country')?>"><i class="fa fa-plus"></i></a>
	</div>
</div>
<?php
$dataURL = Router::url(array('plugin' => 'accurate_location_database', 'controller' => 'accurate_location_database_locations', 'action' => 'getData'));
$delete = '<div class="form-group">
				<label class="col-sm-3 control-label">'.__d('admin', 'Remove targeting').'</label>
				<div class="col-sm-6">
					<a class="RemoveButton btn btn-primary" data-row="0">'.__d('admin', 'Remove').'</a>
				</div>
			</div>';
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
