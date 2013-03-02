<?php
$cacheKey = $modx->getOption('cacheKey', $scriptProperties);

if (empty($cacheKey) || !$output = $modx->cacheManager->get('tickets/latest.'.$cacheKey)) {
	$action = $modx->getOption('action', $scriptProperties, 'comments');
	if (empty($action)) {$action = 'comments';}

	$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
	if (!($Tickets instanceof Tickets)) return '';
	$Tickets->config = array_merge($Tickets->config, $scriptProperties);

	switch($action) {
		case 'comments': $output = $Tickets->getLatestComments($scriptProperties); break;
		case 'tickets': $output = $Tickets->getLatestTickets($scriptProperties); break;
		default: $output = '';
	}

	if (!empty($cacheKey)) {
		$modx->cacheManager->set('tickets/latest.'.$cacheKey, $output, 1800);
	}
}

if (!empty($toPlaceholder)) {
	$modx->setPlaceholder($toPlaceholder, $output);
}
else {
	return $output;
}