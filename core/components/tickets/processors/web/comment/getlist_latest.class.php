<?php
class LatestCommentsGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
	public $defaultSortDirection = 'DESC';
	public $objectType = 'comment';

	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$q = $this->modx->newQuery('TicketComment');
		$q->leftJoin('TicketThread', 'TicketThread','`TicketThread`.`id` = `TicketComment`.`thread`');
		$q->select('DISTINCT(`TicketThread`.`id`)');
		$q->sortby('TicketComment.createdon', 'DESC');
		$parents = $this->getProperty('parents');
		$q->where(array('TicketThread.resource:!=' => 0));
		if ($parents && is_array($parents)) {
			$q->where(array('TicketThread.resource:IN' => $parents));
		}
		$q->limit($this->getProperty('limit',10),$this->getProperty('start',0));
		if ($q->prepare() && $q->stmt->execute()) {
			$ids = array();
			$tmp = $q->stmt->fetchAll(PDO::FETCH_COLUMN);
			foreach ($tmp as $v) {
				// Getting one last comment for each topic
				$q = $this->modx->newQuery('TicketComment', array('thread' => $v, 'deleted' => 0));
				$q->select('`id`');
				$q->sortby('createdon','DESC');
				$q->limit(1);
				if ($q->prepare() && $q->stmt->execute()) {
					$ids[] = $q->stmt->fetch(PDO::FETCH_COLUMN);
				}
			}
			$c->where(array('id:IN' => $ids));
		}

		$c->select($this->modx->getSelectColumns('TicketComment','TicketComment'));
		$c->select('`TicketThread`.`resource`, `modResource`.`pagetitle`,`modResource`.`parent` AS `section`');
		$c->leftJoin('TicketThread','TicketThread','`TicketThread`.`id` = `TicketComment`.`thread`');
		$c->leftJoin('modResource','modResource', '`TicketThread`.`resource` = `modResource`.`id`');
		return $c;
	}

}
return 'LatestCommentsGetListProcessor';