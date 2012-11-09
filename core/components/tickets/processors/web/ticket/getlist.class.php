<?php
class TicketGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'Ticket';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';
	public $objectType = 'ticket';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		if ($parents = $this->getProperty('parents')) {
			if (is_array($parents)) {
				$c->where(array('parent:IN' => $parents));
			}
			else {
				$c->where(array('parent' => $parents));
			}
		}

		$c->where(array(
			'class_key' => 'Ticket'
			,'published' => 1
			,'deleted' => 0
		));
		return $c;
	}

}
return 'TicketGetListProcessor';