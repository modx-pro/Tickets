<?php
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
if (!($Tickets instanceof Tickets)) return '';

if ((empty($action) || $action == 'getTicketForm') && !empty($_REQUEST['action'])) {$action = $_REQUEST['action'];}
if (!empty($_REQUEST['tid'])) {$tid = $_REQUEST['tid'];} else {$tid = 0;}

$output = null;
switch ($action) {
	case 'getTicketForm': $output = $Tickets->getTicketForm($tid); break;
	case 'saveTicket': $output = $Tickets->saveTicket($_REQUEST); break;
	case 'updateTicket': $output = $Tickets->saveTicket($_REQUEST); break;
	case 'previewTicket': $output = $Tickets->previewTicket($_REQUEST['data']); break;
}

if (is_array($output)) {
	$output = json_encode($output);
}

// Support for ajax requests
if (!empty($output) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	//$output = preg_replace('/\[\[(.*?)\]\]/', '', $output);
	$maxIterations= (integer) $modx->getOption('parser_max_iterations', null, 10);
	$modx->getParser()->processElementTags('', $output, false, false, '[[', ']]', array(), $maxIterations);
	$modx->getParser()->processElementTags('', $output, true, true, '[[', ']]', array(), $maxIterations);

	echo $output;
	exit;
}

return $output;