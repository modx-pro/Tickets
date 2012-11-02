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
	public $checkListPermission = true;

	/**
	 * {@inheritDoc}
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->where(array(
			'class_key' => 'TicketsSection'
			,'published' => 1
			,'deleted' => 0
		));
		return $c;
	}

	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public function iterate(array $data) {
		$list = array();
		$list = $this->beforeIteration($list);
		$this->currentIndex = 0;
		/** @var xPDOObject|modAccessibleObject $object */
		foreach ($data['results'] as $object) {
			if ($this->checkListPermission && $object instanceof modAccessibleObject && !$object->checkPolicy('ticketsection_add_children')) continue;
			$objectArray = $this->prepareRow($object);
			if (!empty($objectArray) && is_array($objectArray)) {
				$list[] = $objectArray;
				$this->currentIndex++;
			}
		}
		$list = $this->afterIteration($list);
		return $list;
	}

	/**
	 * {@inheritDoc}
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = $object->toArray();
		return $array;
	}

}

return 'TicketsSectionGetListProcessor';