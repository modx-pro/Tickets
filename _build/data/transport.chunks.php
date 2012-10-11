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
	'name' => 'tpl.Tickets.item',
	'description' => 'Chunk for Items.',
	'snippet' => file_get_contents($sources['source_core'].'/elements/chunks/item.chunk.tpl'),
),'',true,true);

return $chunks;