<?php
if (empty($modx->ticketsLoaded)) {
    $modx->addPackage('tickets', MODX_CORE_PATH . 'components/tickets/model/');
    $modx->ticketsLoaded = true;
}

$thread = 'resource-'.$input;

$q = $modx->newQuery('TicketComment');
$q->leftJoin('TicketThread', 'TicketThread','TicketThread.id = TicketComment.thread');
$q->where(array('TicketThread.name' => $thread));
return $modx->getCount('TicketComment', $q);