<?php
/**
 * Get a list of Sections
 *
 * @package tickets
 * @subpackage processors
 */
class TicketsSectionGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'TicketsSection';
	public $defaultSortField = 'pagetitle';
	public $defaultSortDirection  = 'ASC';
	public $renderers = '';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array(
			'class_key' => 'TicketsSection'
			,'published' => 1
			,'deleted' => 0
		));
		return $c;
	}

	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		return $array;
	}

}

return 'TicketsSectionGetListProcessor';