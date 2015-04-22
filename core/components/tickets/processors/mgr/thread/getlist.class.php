<?php

class TicketThreadsGetListProcessor extends modObjectGetListProcessor {
	public $objectType = 'TicketThread';
	public $classKey = 'TicketThread';
	public $languageTopics = array('tickets:default');
	public $defaultSortField = 'createdon';
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
		$c->leftJoin('Ticket', 'Ticket');
		$c->select($this->modx->getSelectColumns('TicketThread', 'TicketThread'));
		$c->select(array(
			'Ticket.pagetitle'
		));
		if (!$this->getProperty('combo')) {
			$c->leftJoin('TicketComment', 'Comments');
			$c->select(array(
				'comments' => 'COUNT(Comments.id)'
			));
			$c->groupby('TicketThread.id');
		}
		if ($query = $this->getProperty('query', null)) {
			$query = trim($query);
			if (is_numeric($query)) {
				$c->where(array(
					'TicketThread.id:=' => $query,
					'OR:TicketThread.resource:=' => $query,
				));
			}
			else {
				$c->where(array(
					'Ticket.pagetitle:LIKE' => "%{$query}%",
					'OR:TicketThread.name:LIKE' => "%{$query}%",
				));
			}
		}

		return $c;
	}


	/**
	 * Prepare the row for iteration
	 *
	 * @param xPDOObject $object
	 *
	 * @return array
	 */
	public function prepareRow(xPDOObject $object) {
		if ($this->getProperty('combo')) {
			return $object->get(array(
				'id', 'name', 'pagetitle'
			));
		}
		$array = parent::prepareRow($object);
		$icon = $this->_modx23 ? 'icon' : 'fa';

		$array['actions'] = array();

		// View
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-comments-o",
			'title' => $this->modx->lexicon('tickets_action_view'),
			'action' => 'viewThread',
			'button' => empty($array['deleted']) || empty($array['closed']),
			'menu' => true,
		);

		// Publish
		if (!$array['closed']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-power-off action-gray",
				'title' => $this->modx->lexicon('tickets_action_close'),
				'multiple' => $this->modx->lexicon('tickets_action_close'),
				'action' => 'closeThread',
				'button' => empty($array['deleted']),
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-power-off action-green",
				'title' => $this->modx->lexicon('tickets_action_open'),
				'multiple' => $this->modx->lexicon('tickets_action_open'),
				'action' => 'openThread',
				'button' => true,
				'menu' => true,
			);
		}

		// Delete
		if (!$array['deleted']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-trash-o action-yellow",
				'title' => $this->modx->lexicon('tickets_action_delete'),
				'multiple' => $this->modx->lexicon('tickets_action_delete'),
				'action' => 'deleteThread',
				'button' => false,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-undo action-green",
				'title' => $this->modx->lexicon('tickets_action_undelete'),
				'multiple' => $this->modx->lexicon('tickets_action_undelete'),
				'action' => 'undeleteThread',
				'button' => true,
				'menu' => true,
			);
		}

		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-trash-o action-red",
			'title' => $this->modx->lexicon('tickets_action_remove'),
			'multiple' => $this->modx->lexicon('tickets_action_remove'),
			'action' => 'removeThread',
			'button' => false,
			'menu' => true,
		);

		// Menu
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-cog actions-menu",
			'menu' => false,
			'button' => true,
			'action' => 'showMenu',
			'type' => 'menu',
		);

		return $array;
	}

}

return 'TicketThreadsGetListProcessor';