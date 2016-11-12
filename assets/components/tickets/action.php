<?php

if (empty($_REQUEST['action'])) {
    die('Access denied');
} else {
    $action = $_REQUEST['action'];
}

define('MODX_API_MODE', true);
/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/index.php';

$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

// Get properties
$properties = array();
/** @var TicketThread $thread */
if (!empty($_REQUEST['thread']) && $thread = $modx->getObject('TicketThread', array('name' => $_REQUEST['thread']))) {
    $properties = $thread->get('properties');
} elseif (!empty($_REQUEST['form_key']) && isset($_SESSION['TicketForm'][$_REQUEST['form_key']])) {
    $properties = $_SESSION['TicketForm'][$_REQUEST['form_key']];
} elseif (!empty($_SESSION['TicketForm'])) {
    $properties = $_SESSION['TicketForm'];
}

// Switch context
$context = 'web';
if (!empty($thread) && $thread->get('resource') && $object = $thread->getOne('Resource')) {
    $context = $object->get('context_key');
} elseif (!empty($_REQUEST['parent']) && $object = $modx->getObject('modResource', (int)$_REQUEST['parent'])) {
    $context = $object->get('context_key');
} elseif (!empty($_REQUEST['ctx']) && $object = $modx->getObject('modContext', array('key' => $_REQUEST['ctx']))) {
    $context = $object->get('key');
}
if ($context != 'web') {
    $modx->switchContext($context);
}

/** @var Tickets $Tickets */
define('MODX_ACTION_MODE', true);
$Tickets = $modx->getService('tickets', 'Tickets', $modx->getOption('tickets.core_path', null,
        $modx->getOption('core_path') . 'components/tickets/') . 'model/tickets/', $properties
);
if ($modx->error->hasError() || !($Tickets instanceof Tickets)) {
    die('Error');
}

switch ($action) {
    case 'comment/preview':
        $response = $Tickets->previewComment($_POST);
        break;
    case 'comment/save':
        $response = $Tickets->saveComment($_POST);
        break;
    case 'comment/get':
        $response = $Tickets->getComment((int)$_POST['id']);
        break;
    case 'comment/getlist':
        $response = $Tickets->getNewComments($_POST['thread']);
        break;
    case 'comment/subscribe':
        $response = $Tickets->subscribeThread($_POST['thread']);
        break;
    case 'comment/vote':
        $response = $Tickets->voteComment((int)$_POST['id'], (int)$_POST['value']);
        break;
    case 'comment/star':
        $response = $Tickets->starComment((int)$_POST['id']);
        break;

    case 'ticket/draft':
    case 'ticket/publish':
    case 'ticket/update':
    case 'ticket/save':
        $response = $Tickets->saveTicket($_POST);
        break;
    case 'ticket/preview':
        $response = $Tickets->previewTicket($_POST);
        break;
    case 'ticket/vote':
        $response = $Tickets->voteTicket((int)$_POST['id'], (int)$_POST['value']);
        break;
    case 'ticket/star':
        $response = $Tickets->starTicket((int)$_POST['id']);
        break;

    case 'section/subscribe':
        $response = $Tickets->subscribeSection((int)$_POST['section']);
        break;

    case 'ticket/file/upload':
        $response = $Tickets->fileUpload($_POST, 'Ticket');
        break;
    case 'ticket/file/delete':
        $response = $Tickets->fileDelete((int)$_POST['id']);
        break;
    default:
        $message = $_REQUEST['action'] != $action
            ? 'tickets_err_register_globals'
            : 'tickets_err_unknown';
        $response = json_encode(array(
            'success' => false,
            'message' => $modx->lexicon($message),
        ));
}

if (is_array($response)) {
    $response = json_encode($response);
}

@session_write_close();
exit($response);