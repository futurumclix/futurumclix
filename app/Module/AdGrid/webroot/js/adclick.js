/**
 * Copyright (c) 2018 FuturumClix
 */
function startTimer(pField, time, x, y, onError, adTime, focus, callback) {
	var progressBar = $('#progressBar');
	var value = 0;
	var done = false;

	function progressFunc() {
		if(document.hasFocus() || !focus) {
			if(done == false) {
				var percent = value / adTime * 100;
				progressBar.css('width', percent + '%').attr('aria-valuenow', value);
				value += 100;
				if(percent >= 110) {
					done = true;
					callback(pField, x, y, onError);
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

function getProgressBar(pField, eField, url, x, y, onError, adTime, focus, callback) {
	$.ajax({
		url: url,
		cache: false,
		type: 'GET',
		dataType: 'HTML',
		success: function(data) {
			$('#' + pField).html(data);
			startTimer(pField, adTime, x, y, onError, adTime, focus, callback);
		},
		complete: function(req, status) {
			if(status !== 'success') {
				onError();
			}
		},
	});
}
