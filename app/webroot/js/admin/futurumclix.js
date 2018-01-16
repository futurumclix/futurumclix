/**
 * Copyright (c) 2018 FuturumClix
 */
function setAllCheckboxes(className, state) {
	$('.' + className).each(function(i, box) {
		box.checked = state;
	});
}

function enablePriceInput(id, enable) {
	var obj = $('#' + id);
	obj.prop('readonly', !enable);
	if(enable) {
		obj.removeClass('disabled');
		if(obj.val() == '') {
			obj.val('0.0');
		}
	} else {
		obj.addClass('disabled');
	}
}

function setNavToggles(collapseButtonId, collapseId) {
	var collapse = $('#' + collapseId);
	var button = $('#' + collapseButtonId);

	collapse.on('hidden.bs.collapse', function() {
		button.removeClass('fa-minus-circle');
		button.addClass('fa-plus-circle');
	})
	collapse.on('show.bs.collapse', function() {
		button.removeClass('fa-plus-circle');
		button.addClass('fa-minus-circle');
	})
	if(collapse.hasClass('in')) {
		button.removeClass('fa-plus-circle');
		button.addClass('fa-minus-circle');
	} else {
		button.removeClass('fa-minus-circle');
		button.addClass('fa-plus-circle');
	}
}

function selectAll(selectId) {
	$('#' + selectId + ' option').prop('selected', true);
}

function jumpToTabByAnchor() {
	if(location.hash) {
		var activeTab = $('[href=' + location.hash + ']');
		activeTab && activeTab.tab('show');
	}
}

function checkboxesAsRadio(def) {
	def = typeof def !== 'undefined' ? def : false;

	$('input:checkbox.radioCheckbox').on('click', function() {
		var box = $(this);
		var group = 'input:checkbox[name^=\"' + box.attr('name')  + '\"]';

		if(box.is(':checked')) {
			$(group).prop('checked', false);
			box.prop('checked', true);
		} else {
			box.prop('checked', false);

			if(def !== false && $(group + ':checked').length == 0) {
				$(group + ':eq(' + def + ')').prop('checked', true);
			}
		}
	});

	if(def !== false) {
		$('input:checkbox.radioCheckbox').each(function() {
			var box = $(this);
			var group = 'input:checkbox[name=\"' + box.attr('name')  + '\"]';
			if($(group + ':checked').length == 0) {
				$(group + ':eq(' + def + ')').prop('checked', true);
			}
		});
	}
}

function addRowCopy(row, cleanupFunc) {
	var newrow = $(row).clone();
	var table = $(row).parent();

	cleanupFunc(row, newrow);

	table.append(newrow);
}

function formatCurrency(value, realRound, zeroValue, symbol) {
	realRound = typeof realRound !== 'undefined' ? realRound : false;
	zeroValue = typeof zeroValue !== 'undefined' ? zeroValue : false;
	symbol = typeof symbol !== 'undefined' ? symbol : true;

	var places = realRound != true ? CurrencyHelperData['commaPlaces'] : CurrencyHelperData['realCommaPlaces'];
	var res = '';
	var v = Big(value);
	v = v.round(places, 1);

	if(zeroValue && v.eq(0)) {
		return zeroValue;
	}

	if(CurrencyHelperData['symbolPosition'] == 'left' && symbol) {
		res = res.concat(CurrencyHelperData['formattedSymbol']);
	}

	v = v.toFixed(places);
	v = v.split('.');

	res = res.concat(v[0]);

	if(CurrencyHelperData['cutTrailing']) {
		v[1] = v[1].replace(/0+$/, '');
	}

	if(v[1].length > 0) {
		res = res.concat(CurrencyHelperData['decimalPoint']);
		res = res.concat(v[1]);
	}

	if(CurrencyHelperData['symbolPosition'] == 'right' && symbol) {
		res = res.concat(CurrencyHelperData['formattedSymbol']);
	}

	return res;
}

function charCounter() {
	var len = $(this).val().length;
	var limit = $(this).data('limit');
	if (len >= limit) {
		$(this).val($(this).val().substring(0, limit));
		$('#' + $(this).data('counter')).text(limit + ' / ' + limit);
	} else {
		$('#' + $(this).data('counter')).text(len + ' / ' + limit);
	}
};

function ajaxModal(event) {
	$('#ajax-modal').load($(this).data('ajaxsource'));
}
