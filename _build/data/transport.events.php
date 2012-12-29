<?php
/**
 * Add Tickets events for plugins to build
 *
 * @package tickets
 * @subpackage build
 */
$events = array();

$events[0]= $modx->newObject('modEvent');
$events[0]->fromArray(array (
	'name' => 'OnBeforeCommentSave',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[1]= $modx->newObject('modEvent');
$events[1]->fromArray(array (
	'name' => 'OnCommentSave',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[2]= $modx->newObject('modEvent');
$events[2]->fromArray(array (
	'name' => 'OnBeforeCommentDelete',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[3]= $modx->newObject('modEvent');
$events[3]->fromArray(array (
	'name' => 'OnCommentDelete',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[4]= $modx->newObject('modEvent');
$events[4]->fromArray(array (
	'name' => 'OnBeforeCommentRemove',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[5]= $modx->newObject('modEvent');
$events[5]->fromArray(array (
	'name' => 'OnCommentRemove',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[6]= $modx->newObject('modEvent');
$events[6]->fromArray(array (
	'name' => 'OnBeforeTicketThreadDelete',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[7]= $modx->newObject('modEvent');
$events[7]->fromArray(array (
	'name' => 'OnTicketThreadDelete',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[8]= $modx->newObject('modEvent');
$events[8]->fromArray(array (
	'name' => 'OnBeforeTicketThreadRemove',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);

$events[9]= $modx->newObject('modEvent');
$events[9]->fromArray(array (
	'name' => 'OnTicketThreadRemove',
	'service' => 6,
	'groupname' => 'Tickets',
), '', true, true);


return $events;