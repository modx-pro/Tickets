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

$snippets[1]= $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
	'id' => 0,
	'name' => 'TicketComments',
	'description' => 'Native comments for tickets',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/comments.php'),
),'',true,true);
$properties = include $sources['build'].'properties/comments.php';
$snippets[1]->setProperties($properties);
unset($properties);

$snippets[2]= $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
	'id' => 0,
	'name' => 'TicketLatest',
	'description' => 'Snippet for retrieving last tickets and comments',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/ticket_latest.php'),
),'',true,true);
$properties = include $sources['build'].'properties/ticket_latest.php';
$snippets[2]->setProperties($properties);
unset($properties);

$snippets[3]= $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
	'id' => 0,
	'name' => 'tagCut',
	'description' => 'Output filter for displaying content of ticket with various snippets, like getResources',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/tag_cut.php'),
),'',true,true);
//$properties = include $sources['build'].'properties/tag_cut.php';
//$snippets[3]->setProperties($properties);

$snippets[4]= $modx->newObject('modSnippet');
$snippets[4]->fromArray(array(
	'id' => 0,
	'name' => 'getCommentsCount',
	'description' => 'Simple snippet for retrieving number of ticket comments',
	'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/comment_count.php'),
),'',true,true);
//$properties = include $sources['build'].'properties/comment_count.php';
//$snippets[4]->setProperties($properties);

unset($properties);
return $snippets;