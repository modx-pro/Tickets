<?php
/**
 * Get a list of Tickets
 *
 * @package tickets
 * @subpackage processors
 */
class TicketGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'Ticket';
	public $defaultSortField = 'id';
	public $defaultSortDirection  = 'DESC';
	public $renderers = '';
	
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		return $array;
	}
	
}

return 'TicketGetListProcessor';