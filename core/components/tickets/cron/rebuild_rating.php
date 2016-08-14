<?php

define('MODX_API_MODE', true);

/** @noinspection PhpIncludeInspection */
require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/index.php';
/** @var modX $modx */
$modx->getService('error', 'error.modError');
$modx->getRequest();
$modx->setLogLevel(modX::LOG_LEVEL_ERROR);
$modx->setLogTarget('FILE');
$modx->error->message = null;

$time = time();

$modx->removeCollection('TicketAuthorAction', array());
$modx->removeCollection('TicketTotal', array());

$c = $modx->newQuery('modUser');
$c->sortby('id', 'asc');
$users = $modx->getIterator('modUser', $c);
/** @var modUser $user */
foreach ($users as $user) {
    /** @var TicketAuthor $profile */
    if (!$profile = $user->getOne('AuthorProfile')) {
        $profile = $modx->newObject('TicketAuthor');
        $user->addOne($profile);
    }
    $profile->refreshActions(true, true);
    $profile->save();
}

echo "Done in " . (time() - $time) . " sec.\n\n";