<?php
require_once dirname(dirname(dirname(dirname(__FILE__)))).'/config.core.php';

define('MODX_API_MODE', true);
require dirname(MODX_CORE_PATH).'/index.php';

$modx->getService('error','error.modError');
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

return $modx->runSnippet('TicketComments', $_POST);
