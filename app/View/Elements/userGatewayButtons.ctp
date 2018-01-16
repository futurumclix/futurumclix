<?php foreach($gateways as $gateway) {
	$label = $gateway['gateway'];

	if(bccomp($gateway['price'], '0') != 0) {
		$label .= '&nbsp<span class="badge '.$gateway['gateway'].'Price">'.$this->Currency->format($gateway['price']).'</span>';
	}

	echo $this->UserForm->button($label, array(
		'value' => $gateway['gateway'],
		'name' => $gateway['name'],
		'class' => 'btn btn-primary',
		'id' => $gateway['id'],
	));
} ?>
