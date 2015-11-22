<?php

class TicketGetListProcessor extends modObjectGetListProcessor {
	public $classKey = 'Ticket';
	public $defaultSortField = 'id';
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
		$c->leftJoin('modUser', 'CreatedBy');
		$c->leftJoin('modUserProfile', 'UserProfile', 'UserProfile.internalKey = Ticket.createdby');
		$c->leftJoin('TicketThread', 'Thread', 'Thread.resource = Ticket.id');
		$c->select($this->modx->getSelectColumns('Ticket', 'Ticket'));
		$c->select(array(
			'username' => 'CreatedBy.username',
			'author' => 'UserProfile.fullname',
			'comments' => 'Thread.comments'
		));
		$c->where(array(
			'class_key' => 'Ticket',
		));
		if ($parent = $this->getProperty('parent', 0)) {
			$c->where(array(
				'parent' => $this->getProperty('parent')
			));
		}
		else {
			$c->leftJoin('modResource', 'Parent');
			$c->select(array(
				'section_id' => 'Parent.id',
				'section' => 'Parent.pagetitle',
			));
		}
		if ($query = $this->getProperty('query', null)) {
			$c->where(array(
				'pagetitle:LIKE' => "%{$query}%",
				'OR:description:LIKE' => "%{$query}%",
				'OR:introtext:LIKE' => "%{$query}%",
				'OR:CreatedBy.username:LIKE' =>  "%{$query}%",
				'OR:UserProfile.fullname:LIKE' =>  "%{$query}%",
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

		if (empty($array['author'])) {
			$array['author'] = $array['username'];
		}
		$this->modx->getContext($array['context_key']);
		$array['preview_url'] = $this->modx->makeUrl($array['id'], $array['context_key']);

		$icon = $this->_modx23 ? 'icon' : 'fa';

		$array['actions'] = array();
		// View
		if (!empty($array['preview_url'])) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-eye",
				'title' => $this->modx->lexicon('tickets_action_view'),
				'action' => 'viewTicket',
				'button' => true,
				'menu' => true,
			);
		}

		// Edit
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-edit",
			'title' => $this->modx->lexicon('tickets_action_edit'),
			'action' => 'editTicket',
			'button' => false,
			'menu' => true,
		);

		// Duplicate
		$array['actions'][] = array(
			'cls' => '',
			'icon' => "$icon $icon-files-o",
			'title' => $this->modx->lexicon('tickets_action_duplicate'),
			'action' => 'duplicateTicket',
			'button' => false,
			'menu' => true,
		);

		// Publish
		if (!$array['published']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-power-off action-green",
				'title' => $this->modx->lexicon('tickets_action_publish'),
				'multiple' => $this->modx->lexicon('tickets_action_publish'),
				'action' => 'publishTicket',
				'button' => true,
				'menu' => true,
			);
		}
		else {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-power-off action-gray",
				'title' => $this->modx->lexicon('tickets_action_unpublish'),
				'multiple' => $this->modx->lexicon('tickets_action_unpublish'),
				'action' => 'unpublishTicket',
				'button' => true,
				'menu' => true,
			);
		}

		// Delete
		if (!$array['deleted']) {
			$array['actions'][] = array(
				'cls' => '',
				'icon' => "$icon $icon-trash-o action-red",
				'title' => $this->modx->lexicon('tickets_action_delete'),
				'multiple' => $this->modx->lexicon('tickets_action_delete'),
				'action' => 'deleteTicket',
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
				'action' => 'undeleteTicket',
				'button' => true,
				'menu' => true,
			);
		}

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

return 'TicketGetListProcessor';