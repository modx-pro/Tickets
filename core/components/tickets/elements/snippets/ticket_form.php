<?php
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
if (!($Tickets instanceof Tickets)) return '';

if ((empty($action) || $action == 'getTicketForm') && !empty($_REQUEST['action'])) {$action = $_REQUEST['action'];}
$tid = $modx->getOption('tid', $_REQUEST, 0);

//echo '<pre>';print_r($modx->user->getAttributes());echo'</pre>';

if (!$modx->user->isAuthenticated()) {
	//return $modx->lexicon('ticket_err_no_auth');
}

$output = null;
switch ($action) {
	case 'getTicketForm': $output = $Tickets->getTicketForm(array('tid' => $tid)); break;
	case 'saveTicket': $output = $Tickets->saveTicket($_POST); break;
	case 'updateTicket': $output = $Tickets->saveTicket($_POST); break;
	case 'previewTicket': $output = $Tickets->previewTicket($_POST['data']); break;
	case 'previewComment': $output = null; break;
	case 'sendComment': $output = null; break;
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