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

		$pid = 0;
		if (!empty($_REQUEST['tid']) && $current = $this->modx->getObject('Ticket', (integer) $_REQUEST['tid'])) {
			$pid = $current->get('parent');
		}

		$this->currentIndex = 0;
		/** @var xPDOObject|modAccessibleObject $object */
		foreach ($data['results'] as $object) {
			if ($object instanceof modAccessibleObject && !$object->checkPolicy('section_add_children') && $object->id != $pid) {
				continue;
			}
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