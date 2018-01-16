<?php
	if($this->request->here == '/'): // serve on the main page
		echo $this->element('Layout/HeaderMain'); 
	elseif($this->Session->read('Auth.User')): // if user is logged in
		if($this->params['action'] == 'content' || $this->params['controller'] == 'news'): // serve main header for certain pages, like tos, privacy etc
			echo $this->element('Layout/HeaderMain');
		else:
			echo $this->element('Layout/HeaderDashboard'); // show logged in user dashboard header
		endif;
	else:
		echo $this->element('Layout/HeaderMain');
	endif;
	echo $this->fetch('content'); // serve the content
	if($this->Session->read('Auth.User')): // if user is logged in
		if(($this->request->here == '/') || ($this->params['action'] == 'content' || $this->params['controller'] == 'news')): // if user is logged in but back on the main page
			echo $this->element('Layout/FooterMain');
		else:
			echo $this->element('Layout/FooterDashboard');
		endif;
	else:
		echo $this->element('Layout/FooterMain');
	endif; 
?>