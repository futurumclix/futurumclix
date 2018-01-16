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
	var done = false;
	var percentPerSubpage = 100 / (subpages + 1);

	function progressFunc() {
		if(document.hasFocus() || !focus) {
			if(done == false) {
				var percent = ProgressBarValue / subpageTime * 100;

				progressBar.attr('value', percent);

				ProgressBarValue += 100;
				if(percent >= percentPerSubpage * Visited) {
					result = false;
					done = true;
					Visited++;

					if(Visited >= subpages + 2) {
						result = atSuccess();
					} else {
						result = atWait();
					}

					Running = !result;
				}
			}
		}
	}

	if(Visited < subpages + 2) {
		window.setInterval(progressFunc, 100);
	}
}