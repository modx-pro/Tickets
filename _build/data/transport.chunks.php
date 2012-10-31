<?php
/**
 * Add chunks to build
 * 
 * @package tickets
 * @subpackage build
 */
$chunks = array();

$chunks[0]= $modx->newObject('modChunk');
$chunks[0]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.form.create',
	'description' => 'Chunk for creation of new ticket',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/form_create.chunk.tpl'),
),'',true,true);

$chunks[1]= $modx->newObject('modChunk');
$chunks[1]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.form.update',
	'description' => 'Chunk for update existing ticket',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/form_update.chunk.tpl'),
),'',true,true);

$chunks[2]= $modx->newObject('modChunk');
$chunks[2]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.form.section.row',
	'description' => 'Chunk for template one section of form select',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/form_section_row.chunk.tpl'),
),'',true,true);

$chunks[3]= $modx->newObject('modChunk');
$chunks[3]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.form.preview',
	'description' => 'Chunk for preview ticket before publish',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/form_preview.chunk.tpl'),
),'',true,true);

$chunks[4]= $modx->newObject('modChunk');
$chunks[4]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.comment.form',
	'description' => 'The add comment form. Can either be a chunk name or value. If set to a value, will override the chunk.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/comment_form.chunk.tpl'),
),'',true,true);

$chunks[5]= $modx->newObject('modChunk');
$chunks[5]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.comment.one',
	'description' => 'A Chunk for the comment itself.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/comment_one.chunk.tpl'),
),'',true,true);

$chunks[6]= $modx->newObject('modChunk');
$chunks[6]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.comment.wrapper',
	'description' => 'A Chunk for the outer wrapper for comments.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/comment_wrapper.chunk.tpl'),
),'',true,true);

$chunks[7]= $modx->newObject('modChunk');
$chunks[7]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.comment.login',
	'description' => 'The portion to show when the user is not logged in. Can either be a chunk name or value. If set to a value, will override the chunk.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/comment_login.chunk.tpl'),
),'',true,true);

$chunks[8]= $modx->newObject('modChunk');
$chunks[8]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.ticket.latest',
	'description' => 'The chunk for templating latest tickets',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/ticket_latest.chunk.tpl'),
),'',true,true);

$chunks[9]= $modx->newObject('modChunk');
$chunks[9]->fromArray(array(
	'id' => 0,
	'name' => 'tpl.Tickets.comment.latest',
	'description' => 'The chunk for templating latest comments',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/comment_latest.chunk.tpl'),
),'',true,true);


return $chunks;