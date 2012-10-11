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
	'name' => 'Tickets',
	'description' => 'Displays Items.',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/snippet.tickets.php'),
),'',true,true);
$properties = include $sources['build'].'properties/properties.tickets.php';
$snippets[0]->setProperties($properties);
unset($properties);

return $snippets;