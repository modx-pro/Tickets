<?php
/* @var array $scriptProperties */
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
$Tickets->initialize($modx->context->key, $scriptProperties);

if ((empty($action) || $action == 'getTicketForm') && !empty($_REQUEST['action'])) {$action = $_REQUEST['action'];}
$tid = $modx->getOption('tid', $_REQUEST, 0);

if (!$modx->user->isAuthenticated()) {
	return $modx->lexicon('ticket_err_no_auth');
}

$output = null;
switch ($action) {
	case 'getTicketForm': $output = $Tickets->getTicketForm(array('tid' => $tid)); break;
	case 'previewTicket': $output = $Tickets->previewTicket($_POST); break;
	case 'saveTicket': $output = $Tickets->saveTicket($_POST); break;
	case 'updateTicket': $output = $Tickets->saveTicket($_POST); break;
	case 'previewComment': $output = null; break;
	case 'saveComment': $output = null; break;
	case 'updateComment': $output = null; break;
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