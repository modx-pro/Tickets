<?php

class TicketAuthorGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'TicketAuthor';
	public $defaultSortField = 'rating';
	public $defaultSortDirection = 'DESC';
	protected $_modx23;


	/**
	 * @return bool
	 */
	public function initialize() {
		$parent = parent::initialize();

		/** @var Tickets $Tickets */
		$Tickets = $this->modx->getService('Tickets');
		$this->_modx23 = $Tickets->systemVersion();

		return $parent;
	}


	/**
	 * @param xPDOQuery $c
	 *
	 * @return xPDOQuery
	 */
	public function prepareQueryBeforeCount(xPDOQuery $c) {
		$c->innerJoin('modUser', 'User');
		$c->innerJoin('modUserProfile', 'UserProfile');
		$c->select($this->modx->getSelectColumns($this->classKey, $this->classKey));
		$c->select(array(
			'username' => 'User.username',
			'author' => 'UserProfile.fullname',
			'active' => 'User.active',
			'blocked' => 'UserProfile.blocked',
		));

		if ($query = $this->getProperty('query', null)) {
			$c->where(array(
				'User.username:LIKE' => "%{$query}%",
				'OR:UserProfile.fullname:LIKE' => "%{$query}%",
			));
		}

		return $c;
	}

	/**
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		$array = parent::prepareRow($object);

		if (empty($array['fullname'])) {
			$array['fullname'] = $array['username'];
		}

		return $array;
	}

}

return 'TicketAuthorGetListProcessor';