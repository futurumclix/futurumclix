/**
 * Copyright (c) 2018 FuturumClix
 */
function getCaptcha(pField, adId, onError) {
	$.ajax({
		url: '/ads/fetchCaptcha/' + adId,
		cache: false,
		type: 'GET',
		dataType: 'HTML',
		success: function(data) {
			$('#' + pField).html(data);
		},
		complete: function(req, status) {
			if(status !== 'success') {
				onError();
			}
		},
	});
}

function startTimer(pField, time, adId, onError, adTime, focus, callback) {
	callback = typeof callback !== 'undefined' ? callback : getCaptcha;
	var progressBar = $('#progressBar');
	var value = 0;
	var done = false;

	function progressFunc() {
		if(document.hasFocus() || !focus) {
			if(done == false) {
				var percent = value / adTime * 100;
				progressBar.attr('value', percent);
				value += 100;
				if(percent >= 110) {
					done = true;
					callback(pField, adId, onError);
				}
			}
		}
	}

	if(progressBar === null) {
		onError();
	} else {
		window.setInterval(progressFunc, 100);
	}
}

function getProgressBar(pField, eField, adId, onError, adTime, focus) {
	$.ajax({
		url: '/ads/fetchProgressBar/' + adId,
		cache: false,
		type: 'GET',
		dataType: 'HTML',
		success: function(data) {
			$('#' + pField).html(data);
			startTimer(pField, adTime, adId, onError, adTime, focus);
		},
		complete: function(req, status) {
			if(status !== 'success') {
				onError();
			}
		},
	});
}
