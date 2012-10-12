<?php
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/',$scriptProperties);
if (!($Tickets instanceof Tickets)) return '';

if (empty($action)) {$action = 'getTicketForm';}
if (!empty($_REQUEST['action']) && $_REQUEST['action'] == 'previewTicket') {$action = 'previewTicket';}

$output = null;
switch ($action) {
	case 'getTicketForm': $output = $Tickets->getTicketForm(); break;
	case 'saveTicket': $output = $Tickets->saveTicket(); break;
	case 'previewTicket': $output = $Tickets->previewTicket(); break;
}

// Support for ajax requests
if (!empty($output) && !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] == 'XMLHttpRequest') {
	echo $output;
	exit;
}

return $output;