/**
 * Copyright (c) 2018 FuturumClix
 */
function setAllCheckboxes(className, state) {
	$('.' + className).each(function(i, box) {
		box.checked = state;
	});
}

function calculateFee(value, percentFee, amountFee) {
	var val = Big(value);
	var res = Big(0);
	var amountFee = Big(amountFee);
	var percentFee = Big(percentFee);

	if(val.eq(0)) {
		return res.toFixed(8);
	}

	if(!percentFee.eq(0)) {
		var p = percentFee.div(100);
		res = val.times(p);
	}

	if(!amountFee.eq(0)) {
		res = res.plus(amountFee);
	}
	return res.toFixed(8);
}

function addFees(value, percentFee, amountFee) {
	return Big(value).add(Big(calculateFee(value, percentFee, amountFee))).toFixed(8);
}

function formatCurrency(value, realRound, zeroValue) {
	realRound = typeof realRound !== 'undefined' ? realRound : false;
	zeroValue = typeof zeroValue !== 'undefined' ? zeroValue : false;

	var places = realRound != true ? CurrencyHelperData['commaPlaces'] : CurrencyHelperData['realCommaPlaces'];
	var res = '';
	var v = Big(value);
	v = v.round(places, 1);

	if(zeroValue && v.eq(0)) {
		return zeroValue;
	}

	if(CurrencyHelperData['symbolPosition'] == 'left') {
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

	if(CurrencyHelperData['symbolPosition'] == 'right') {
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
	$('#ajax-modal-container').load($(this).data('ajaxsource'));
}

function showModal(id) {
	$(document).ready(function() {
		UIkit.modal('#' + id, {center:true})[0].show();
	});
}

function selectAll(selectId) {
	$('#' + selectId + ' option').prop('selected', true);
}
