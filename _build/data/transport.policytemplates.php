<?php
/**
 * Default Tickets Access Policy Templates
 *
 * @package tickets
 * @subpackage build
 */
$templates = array();
$permissions = include dirname(__FILE__).'/transport.permissions.php';

$templates[0]= $modx->newObject('modAccessPolicyTemplate');
$templates[0]->fromArray(array(
	'id' => 0,
	'name' => 'TicketsUserPolicyTemplate',
	'description' => 'A policy for ticket users.',
	'lexicon' => 'tickets:permissions',
	'template_group' => 1,
));
if (is_array($permissions[0])) {
	$templates[0]->addMany($permissions[0]);
} else { $modx->log(modX::LOG_LEVEL_ERROR,'Could not load Tickets Policy Template.'); }


$templates[1]= $modx->newObject('modAccessPolicyTemplate');
$templates[1]->fromArray(array(
	'id' => 0,
	'name' => 'TicketsSectionPolicyTemplate',
	'description' => 'A policy for section access.',
	'lexicon' => 'tickets:permissions',
	'template_group' => 3,
));
if (is_array($permissions[1])) {
	$templates[1]->addMany($permissions[1]);
} else { $modx->log(modX::LOG_LEVEL_ERROR,'Could not load Tickets Policy Template.'); }


return $templates;
