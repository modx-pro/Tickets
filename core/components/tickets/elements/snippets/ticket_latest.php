<?php
$cacheKey = $modx->getOption('cacheKey', $scriptProperties);

if (!empty($cacheKey) && $output = $modx->cacheManager->get('tickets/latest.'.$cacheKey)) {
	return $output;
}

$action = $modx->getOption('action', $scriptProperties, 'comments');
switch($action) {
	case 'comments':
		if (!isset($scriptProperties['type'])) {$scriptProperties['type'] = 'last';}
		$Comments = $modx->getService('comments','Comments',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
		if (!($Comments instanceof Comments)) return '';
		$controller = $Comments->loadController('LatestComments');
		$output = $controller->run($scriptProperties);
	break;

	case 'tickets':
		$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
		if (!($Tickets instanceof Tickets)) return '';
		$output = $Tickets->getLatestTickets($scriptProperties);
	break;

	default: $output = '';
}

if (!empty($cacheKey)) {
	$modx->cacheManager->set('tickets/latest.'.$cacheKey, $output, 1800);
}
return $output;