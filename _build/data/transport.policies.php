<?php
/**
 * The default Policy scheme for the Tickets.
 *
 * @package tickets
 * @subpackage build
 */
$policies = array();

$policies[0]= $modx->newObject('modAccessPolicy');
$policies[0]->fromArray(array (
	'id' => 0,
	'name' => 'TicketUserPolicy',
	'description' => 'A policy for create and update Tickets.',
	'parent' => 0,
	'class' => '',
	'lexicon' => 'tickets:permissions',
	'data' => '{"ticket_delete":true,"ticket_publish":true,"ticket_save":true,"comment_save":true,"load":true,"list":true,"view":true}',
), '', true, true);

$policies[1]= $modx->newObject('modAccessPolicy');
$policies[1]->fromArray(array (
	'id' => 0,
	'name' => 'TicketSectionPolicy',
	'description' => 'A policy for add tickets in section.',
	'parent' => 0,
	'class' => '',
	'lexicon' => 'tickets:permissions',
	'data' => '{"section_add_children":true,"load":true,"list":true,"view":true}',
), '', true, true);

$policies[2]= $modx->newObject('modAccessPolicy');
$policies[2]->fromArray(array (
	'id' => 0,
	'name' => 'TicketVipPolicy',
	'description' => 'A policy for create and update private Tickets.',
	'parent' => 0,
	'class' => '',
	'lexicon' => 'tickets:permissions',
	'data' => '{"ticket_delete":true,"ticket_publish":true,"ticket_save":true,"comment_save":true,"load":true,"list":true,"view":true,"ticket_view_private":true}',
), '', true, true);

return $policies;