<?php
class TicketThreadsGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'TicketThread';
	public $classKey = 'TicketThread';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->leftJoin('Ticket','Ticket','`Ticket`.`id` = `TicketThread`.`resource`');

		if (!$this->getProperty('combo')) {
			$c->leftJoin('TicketComment','TicketComment','`TicketComment`.`thread` = `TicketThread`.`id`');
			$c->select('COUNT(`TicketComment`.`id`) as `comments`');
			$c->groupby('TicketThread.id');
		}

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

		$c->select($this->modx->getSelectColumns('TicketThread','TicketThread'));
		$c->select('`Ticket`.`pagetitle` as `pagetitle`');

		return $c;
	}


	/**
	 * Prepare the row for iteration
	 * @param xPDOObject $object
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		if (!$this->getProperty('combo')) {
			$row = $object->toArray();
			$row['url'] = !empty($row['resource'])
				? $this->modx->makeUrl($row['resource'], '', '', 'full')
				: '';
		}
		else {
			$row = array(
				'id' => $object->id,
				'name' => $object->get('name'),
				'pagetitle' => $object->get('pagetitle'),
			);
		}

		return $row;
	}


}
return 'TicketThreadsGetListProcessor';