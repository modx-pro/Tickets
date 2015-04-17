<?php

class TicketGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'Ticket';
	public $classKey = 'Ticket';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($parents = $this->getProperty('parents')) {
			if (!is_array($parents)) {
				$parents = explode(',', $parents);
			}
			$c->where(array('parent:IN' => $parents));
		}

		$c->where(array(
			'class_key' => 'Ticket',
			'published' => 1,
			'deleted' => 0,
		));
		return $c;
	}

}

return 'TicketGetListProcessor';