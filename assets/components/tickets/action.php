<?php

if (empty($_REQUEST['action'])) {
	die('Access denied');
}
else {
	$action = $_REQUEST['action'];
}

define('MODX_API_MODE', true);
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/index.php';

$modx->getService('error','error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$ctx = !empty($_REQUEST['ctx']) ? $_REQUEST['ctx'] : 'web';
//if ($ctx != 'web') {$modx->switchContext($ctx);}
$properties = array();

/* @var TicketThread $thread */
if (!empty($_REQUEST['thread']) && $thread = $modx->getObject('TicketThread', array('name' => $_REQUEST['thread']))) {
	$properties = $thread->get('properties');
}
/* @var Tickets $Tickets */
$Tickets = $modx->getService('tickets','Tickets',$modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'model/tickets/', $properties);
if ($modx->error->hasError() || !($Tickets instanceof Tickets)) {die('Error');}
//$Tickets->initialize($ctx, array('json_response' => true));

switch ($action) {
	case 'comment/preview': $response = $Tickets->previewComment($_POST); break;
	case 'comment/save': $response = $Tickets->saveComment($_POST); break;
	case 'comment/get': $response = $Tickets->getComment(@$_POST['id']); break;
	case 'comment/getlist': $response = $Tickets->getNewComments(@$_POST['thread']); break;
	case 'comment/subscribe': $response = $Tickets->Subscribe(@$_POST['thread']); break;
	case 'ticket/preview': $response = $Tickets->previewTicket($_POST); break;
	case 'ticket/save': $response = $Tickets->saveTicket($_POST); break;
	case 'ticket/update': $response = $Tickets->saveTicket($_POST); break;
	default:
		$message = $_REQUEST['action'] != $action ? 'tickets_err_register_globals' : 'tickets_err_unknown';
		$response = $modx->toJSON(array('success' => false, 'message' => $modx->lexicon($message)));
}

if (is_array($response)) {
	$response = $modx->toJSON($response);
}

@session_write_close();
exit($response);