<?php
/**
 * Add snippets to build
 * 
 * @package tickets
 * @subpackage build
 */
$snippets = array();

$snippets[0]= $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
	'id' => 0,
	'name' => 'TicketForm',
	'description' => 'Generates edit form for create new or update existing ticket. Verify and save changes.',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/ticket_form.php'),
),'',true,true);
$properties = include $sources['build'].'properties/ticket_form.php';
$snippets[0]->setProperties($properties);
unset($properties);

return $snippets;