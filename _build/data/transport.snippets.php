<?php
/**
 * Add snippets to build
 * 
 * @package tickets
 * @subpackage build
 */
$snippets = array();

$tmp = array(
	'TicketForm' => 'ticket_form'
	,'TicketComments' => 'comments'
	,'TicketLatest' => 'ticket_latest'
	,'getTickets' => 'get_tickets'
	,'getTicketSections' => 'get_sections'
);

foreach ($tmp as $k => $v) {
	/* @avr modSnippet $snippet */
	$snippet = $modx->newObject('modSnippet');
	$snippet->fromArray(array(
		'id' => 0
		,'name' => $k
		,'description' => ''
		,'snippet' => getSnippetContent($sources['source_core'].'/elements/snippets/'.$v.'.php')
		,'static' => 1
		,'static_file' => 'core/components/tickets/elements/snippets/'.$v.'.php'
	),'',true,true);

	$properties = include $sources['build'].'properties/'.$v.'.php';
	$snippet->setProperties($properties);

	$snippets[] = $snippet;
}

unset($properties);
return $snippets;