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
		));

		if ($query = $this->getProperty('query')) {
			$c->where(array('pagetitle:LIKE' => "%$query%"));
		}

		return $c;
	}

}

return 'TicketsSectionGetListProcessor';