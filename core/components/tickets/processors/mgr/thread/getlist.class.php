<?php
class TicketThreadsGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'TicketThread';
	public $classKey = 'TicketThread';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin('TicketComment','TicketComment','`TicketComment`.`thread` = `TicketThread`.`id`');
		$c->leftJoin('Ticket','Ticket','`Ticket`.`id` = `TicketThread`.`resource`');
		$c->select($this->modx->getSelectColumns('TicketThread','TicketThread'));
		$c->select('COUNT(`TicketComment`.`id`) as `comments`');
		$c->select('`Ticket`.`pagetitle` as `pagetitle`');
		$c->groupby('`TicketThread`.`name`');

		if ($query = $this->getProperty('query',null)) {
			$query = trim($query);
			if (is_numeric($query)) {
				$c->where(array(
					'TicketThread.id:=' => $query
					,'OR:TicketThread.resource:=' => $query
				));

			}
			else {
				$c->where(array(
					'Ticket.pagetitle:LIKE' => '%'.$query.'%'
					,'OR:TicketThread.name:LIKE' => '%'.$query.'%'
				));
			}
		}

		return $c;
	}

	/**
	 * Prepare the row for iteration
	 * @param xPDOObject $object
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$row = $object->toArray();
		$row['url'] = !empty($row['resource']) ? $this->modx->makeUrl($row['resource'], '', '', 'full') : '';

		return $row;
	}


}
return 'TicketThreadsGetListProcessor';