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

return $chunks;