<?php
/**
 * Tickets Connector
 *
 * @package tickets
 */
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';
require_once MODX_CORE_PATH.'config/'.MODX_CONFIG_KEY.'.inc.php';
require_once MODX_CONNECTORS_PATH.'index.php';

$corePath = $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/');
require_once $corePath.'model/tickets/tickets.class.php';
$modx->Tickets = new Tickets($modx);

$modx->lexicon->load('tickets:default');

/* handle request */
$path = $modx->getOption('processorsPath',$modx->Tickets->config,$corePath.'processors/');
$modx->request->handleRequest(array(
    'processors_path' => $path,
    'location' => '',
));