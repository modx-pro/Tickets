<?php
/** @var array $scriptProperties */
if (empty($class)) {
    $class = 'Ticket';
}
/** @var integer $user */
if (empty($user)) {
    $user = $modx->user->get('id');
}
unset($scriptProperties['user']);

$ids = array();
$q = $modx->newQuery('TicketStar', array('class' => $class, 'createdby' => $user));
$q->select('id');
$tstart = microtime(true);
if ($q->prepare() && $q->stmt->execute()) {
    $modx->queryTime = microtime(true) - $tstart;
    $modx->executedQueries++;

    $ids = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
}

if (empty($ids)) {
    return false;
}

$where = array($class . '.id:IN' => $ids);
if (!empty($scriptProperties['where'])) {
    $tmp = json_decode($scriptProperties['where'], true);
    if (is_array($tmp)) {
        $where = array_merge($where, $tmp);
    }
}
$scriptProperties['where'] = json_encode($where);
if (empty($parents)) {
    $scriptProperties['parents'] = 0;
}
if (empty($tpl)) {
    unset($scriptProperties['tpl']);
}

return $class == 'Ticket'
    ? $modx->runSnippet('getTickets', $scriptProperties)
    : $modx->runSnippet('getComments', $scriptProperties);