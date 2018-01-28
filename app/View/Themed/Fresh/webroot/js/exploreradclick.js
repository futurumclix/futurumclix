/**
 * Copyright (c) 2018 FuturumClix
 */
var Visited = 1;
var ProgressBarValue = 0;
var Running = false;

function explorerAfterLoad(subpages, subpageTime, focus) {
	if(Running) return;

	Running = true;

	atStart();

	var progressBar = $('#progressBar');
	var percentPerSubpage = 100 / (subpages + 1);
	var intervalHandle;
	var nextCheck = Date.now() + subpageTime;

	function progressFunc() {
		if(document.hasFocus() || !focus) {
			var percent = ProgressBarValue / subpageTime * 100;

			progressBar.css('width', percent + '%').attr('aria-valuenow', ProgressBarValue);

			if(percentPerSubpage * Visited > percent) {
				ProgressBarValue += 100;
			}

			if(Date.now() >= nextCheck) {
				Visited++;

				if(Visited >= subpages + 2) {
					Running = !atSuccess();
				} else {
					Running = !atWait();
				}

				window.clearInterval(intervalHandle);
			}
		}
	}

	if(Visited < subpages + 2) {
		intervalHandle = window.setInterval(progressFunc, 100);
	}
}