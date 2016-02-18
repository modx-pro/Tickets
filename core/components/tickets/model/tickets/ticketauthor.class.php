<?php

/**
 * Class TicketAuthor
 *
 * @property int $id
 */
class TicketAuthor extends xPDOObject {
	protected $_ratings = array();

	/**
	 * @param $type
	 * @param $id
	 * @param $ticket_id
	 * @param int $multiplier
	 *
	 * @return bool
	 */
	public function addAction($type, $id, $ticket_id, $multiplier = 1) {
		/** @var Ticket $ticket */
		$ticket = $this->xpdo->getObject('modResource', $ticket_id);
		if (!$ticket || !($ticket instanceof Ticket) || empty($type)) {
			return false;
		}
		/** @var TicketsSection $section */
		$section = $ticket->getOne('Section');
		if (!$section || !($section instanceof TicketsSection)) {
			return false;
		}

		$ratings = $section->getProperties('ratings');
		if (isset($ratings[$type])) {
			$rating = $ratings[$type] * $multiplier;
			/** @noinspection PhpUndefinedFieldInspection */
			$key = array(
				'id' => $id,
				'action' => $type,
				'owner' => $this->get('id'),
				'createdby' => $this->xpdo->user->get('id'),
			);
			/** @var TicketAuthorAction $action */
			if (!$action = $this->xpdo->getObject('TicketAuthorAction', $key)) {
				$action = $this->xpdo->newObject('TicketAuthorAction');
				$action->fromArray($key, '', true, true);
				$action->fromArray(array(
					'rating' => $rating,
					'multiplier' => $multiplier,
					'ticket' => $ticket->get('id'),
					'section' => $section->get('id'),
				));
				if ($action->save()) {
					if (!empty($rating)) {
						$this->set('rating', $this->get('rating') + $rating);
					}
					if ($field = $this->_getTotalField($type)) {
						if (strpos($type, 'vote_') === 0) {
							$this->set($field, $this->get($field) + $rating);
							$field .= $rating > 0
								? '_up'
								: '_down';
						}
						$this->set($field, $this->get($field) + 1);
					}
					$this->save();
				}
				else {
					return false;
				}
			}
		}

		return true;
	}


	/**
	 * @param $type
	 * @param int $id
	 * @param int $createdby
	 *
	 * @return bool
	 */
	public function removeAction($type, $id = 0, $createdby = 0) {
		$key = array(
			'id' => $id,
			'action' => $type,
			'owner' => $this->get('id'),
			'createdby' => $createdby,
		);
		/** @var TicketAuthorAction $action */
		if ($action = $this->xpdo->getObject('TicketAuthorAction', $key)) {
			$rating = $action->get('rating');
			if ($action->remove()) {
				if (!empty($rating)) {
					$this->set('rating', $this->get('rating') - $rating);
				}
				if (!empty($rating) && $field = $this->_getTotalField($type)) {
					if (strpos($type, 'vote_') === 0) {
						$this->set($field, $this->get($field) - $rating);
						$field .= $rating > 0
							? '_up'
							: '_down';
					}
					$this->set($field, $this->get($field) - 1);
				}
				$this->save();
			}
			else {
				return false;
			}
		}

		return true;
	}


	/**
	 * @param bool $clearActions
	 * @param bool $updateTotals
	 *
	 * @return $this
	 */
	public function refreshActions($clearActions = true, $updateTotals = true) {
		$this->updateTickets($clearActions);
		$this->updateComments($clearActions);
		$this->updateViews($clearActions);
		$this->updateStars($clearActions);
		$this->updateVotes($clearActions);
		if ($updateTotals) {
			$this->updateTotals();
		}

		return $this;
	}


	/**
	 * @param bool $clearActions
	 */
	public function updateTickets($clearActions = true) {
		$action = 'ticket';
		if ($clearActions) {
			$this->xpdo->removeCollection('TicketAuthorAction', array('owner' => $this->id, 'action' => $action));
		}
		// Make new
		$c = $this->xpdo->newQuery('Ticket', array(
			'createdby' => $this->id,
			'class_key' => 'Ticket',
			'published' => 1,
			'deleted' => 0,
		));

		$c->select('id, parent, createdby, createdon');
		if ($c->prepare() && $c->stmt->execute()) {
			while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
				$ratings = $this->_getRatings($row['parent']);
				if (isset($ratings[$action])) {
					$rating = $ratings[$action];
					$record = array(
						'id' => $row['id'],
						'action' => $action,
						'rating' => $rating,
						'ticket' => $row['id'],
						'section' => $row['parent'],
						'createdby' => $row['createdby'],
						'createdon' => date('Y-m-d H:i:s', $row['createdon']),
						'owner' => $row['createdby'],
						'year' => date('Y', $row['createdon']),
						'month' => date('m', $row['createdon']),
						'day' => date('d', $row['createdon']),
					);
					$keys = array_keys($record);
					$fields = '`' . implode('`,`', $keys) . '`';
					$placeholders = substr(str_repeat('?,', count($keys)), 0, -1);
					$sql = "INSERT INTO {$this->xpdo->getTableName('TicketAuthorAction')} ({$fields}) VALUES ({$placeholders});";
					$this->xpdo->prepare($sql)->execute(array_values($record));
				}
			}
		}
	}


	/**
	 * @param bool $clearActions
	 */
	public function updateComments($clearActions = true) {
		$action = 'comment';
		if ($clearActions) {
			$this->xpdo->removeCollection('TicketAuthorAction', array('owner' => $this->id, 'action' => $action));
		}
		// Make new
		$c = $this->xpdo->newQuery('TicketComment', array(
			'createdby' => $this->id,
			'published' => 1,
			'deleted' => 0,
		));
		$c->innerJoin('TicketThread', 'Thread');
		$c->innerJoin('Ticket', 'Ticket', 'Ticket.id = Thread.resource AND Ticket.class_key = "Ticket"');
		$c->select('
			TicketComment.id, TicketComment.createdby, TicketComment.createdon,
			Ticket.id as ticket, Ticket.parent as section
		');
		if ($c->prepare() && $c->stmt->execute()) {
			while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
				$ratings = $this->_getRatings($row['section']);
				if (isset($ratings[$action])) {
					$rating = $ratings[$action];
					$record = array(
						'id' => $row['id'],
						'action' => $action,
						'rating' => $rating,
						'ticket' => $row['ticket'],
						'section' => $row['section'],
						'createdby' => $row['createdby'],
						'createdon' => $row['createdon'],
						'owner' => $row['createdby'],
						'year' => date('Y', strtotime($row['createdon'])),
						'month' => date('m', strtotime($row['createdon'])),
						'day' => date('d', strtotime($row['createdon'])),
					);
					$keys = array_keys($record);
					$fields = '`' . implode('`,`', $keys) . '`';
					$placeholders = substr(str_repeat('?,', count($keys)), 0, -1);
					$sql = "INSERT INTO {$this->xpdo->getTableName('TicketAuthorAction')} ({$fields}) VALUES ({$placeholders});";
					$this->xpdo->prepare($sql)->execute(array_values($record));
				}
			}
		}
	}


	/**
	 * @param bool $clearActions
	 */
	public function updateViews($clearActions = true) {
		$action = 'view';
		if ($clearActions) {
			$this->xpdo->removeCollection('TicketAuthorAction', array('owner' => $this->id, 'action' => $action));
		}
		// Make new
		$c = $this->xpdo->newQuery('TicketView', array(
			'uid' => $this->id,
			'Ticket.published' => 1,
			'Ticket.deleted' => 0,
		));
		$c->innerJoin('Ticket', 'Ticket', 'Ticket.id = TicketView.parent AND Ticket.class_key = "Ticket"');
		$c->select('uid, timestamp, Ticket.id, Ticket.parent as section');
		if ($c->prepare() && $c->stmt->execute()) {
			while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
				$ratings = $this->_getRatings($row['section']);
				if (isset($ratings[$action])) {
					$rating = $ratings[$action];
					$record = array(
						'id' => $row['id'],
						'action' => $action,
						'rating' => $rating,
						'ticket' => $row['id'],
						'section' => $row['section'],
						'createdby' => $row['uid'],
						'createdon' => $row['timestamp'],
						'owner' => $row['uid'],
						'year' => date('Y', strtotime($row['timestamp'])),
						'month' => date('m', strtotime($row['timestamp'])),
						'day' => date('d', strtotime($row['timestamp'])),
					);
					$keys = array_keys($record);
					$fields = '`' . implode('`,`', $keys) . '`';
					$placeholders = substr(str_repeat('?,', count($keys)), 0, -1);
					$sql = "INSERT INTO {$this->xpdo->getTableName('TicketAuthorAction')} ({$fields}) VALUES ({$placeholders});";
					$this->xpdo->prepare($sql)->execute(array_values($record));
				}
			}
		}
	}


	/**
	 * @param bool $clearActions
	 */
	public function updateStars($clearActions = true) {
		$actions = array('star_ticket', 'star_comment');
		if ($clearActions) {
			$this->xpdo->removeCollection('TicketAuthorAction', array('owner' => $this->id, 'action:IN' => $actions));
		}
		foreach ($actions as $action) {
			$c = $this->xpdo->newQuery('TicketStar', array('owner' => $this->id));
			if ($action == 'star_ticket') {
				$c->where(array('class' => 'Ticket'));
				$c->innerJoin('Ticket', 'Ticket', 'Ticket.id = TicketStar.id AND Ticket.class_key = "Ticket"');
				$c->select('
					TicketStar.id, TicketStar.createdon, TicketStar.createdby,
					Ticket.id as ticket, Ticket.parent as section
				');
				$c->where(array(
					'Ticket.published' => 1,
					'Ticket.deleted' => 0,
				));
			}
			else {
				$c->where(array('class' => 'TicketComment'));
				$c->innerJoin('TicketComment', 'Comment', 'Comment.id = TicketStar.id');
				$c->innerJoin('TicketThread', 'Thread', 'Thread.id = Comment.thread');
				$c->innerJoin('Ticket', 'Ticket', 'Thread.resource = Ticket.id AND Ticket.class_key = "Ticket"');
				$c->select('
					TicketStar.id, TicketStar.createdon, TicketStar.createdby,
					Ticket.id as ticket, Ticket.parent as section
				');
				$c->where(array(
					'Comment.published' => 1,
					'Comment.deleted' => 0,
				));
			}

			if ($c->prepare() && $c->stmt->execute()) {
				while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
					$ratings = $this->_getRatings($row['section']);
					if (isset($ratings[$action])) {
						$rating = $ratings[$action];
						$record = array(
							'id' => $row['id'],
							'action' => $action,
							'rating' => $rating,
							'ticket' => $row['ticket'],
							'section' => $row['section'],
							'createdby' => $row['createdby'],
							'createdon' => $row['createdon'],
							'owner' => $this->id,
							'year' => date('Y', strtotime($row['createdon'])),
							'month' => date('m', strtotime($row['createdon'])),
							'day' => date('d', strtotime($row['createdon'])),
						);
						$keys = array_keys($record);
						$fields = '`' . implode('`,`', $keys) . '`';
						$placeholders = substr(str_repeat('?,', count($keys)), 0, -1);
						$sql = "INSERT INTO {$this->xpdo->getTableName('TicketAuthorAction')} ({$fields}) VALUES ({$placeholders});";
						$this->xpdo->prepare($sql)->execute(array_values($record));
					}
				}
			}
		}
	}


	/**
	 * @param bool $clearActions
	 */
	public function updateVotes($clearActions = true) {
		$actions = array('vote_ticket', 'vote_comment');
		if ($clearActions) {
			$this->xpdo->removeCollection('TicketAuthorAction', array('owner' => $this->id, 'action:IN' => $actions));
		}
		foreach ($actions as $action) {
			$c = $this->xpdo->newQuery('TicketVote', array('owner' => $this->id));
			if ($action == 'vote_ticket') {
				$c->where(array('class' => 'Ticket'));
				$c->innerJoin('Ticket', 'Ticket', 'Ticket.id = TicketVote.id AND Ticket.class_key = "Ticket"');
				$c->select('
					TicketVote.id, TicketVote.createdon, TicketVote.createdby, TicketVote.value,
					Ticket.id as ticket, Ticket.parent as section
				');
				$c->where(array(
					'Ticket.published' => 1,
					'Ticket.deleted' => 0,
				));
			}
			else {
				$c->where(array('class' => 'TicketComment'));
				$c->innerJoin('TicketComment', 'Comment', 'Comment.id = TicketVote.id');
				$c->innerJoin('TicketThread', 'Thread', 'Thread.id = Comment.thread');
				$c->innerJoin('Ticket', 'Ticket', 'Thread.resource = Ticket.id AND Ticket.class_key = "Ticket"');
				$c->select('
					TicketVote.id, TicketVote.createdon, TicketVote.createdby, TicketVote.value, TicketVote.owner,
					Ticket.id as ticket, Ticket.parent as section
				');
				$c->where(array(
					'Comment.published' => 1,
					'Comment.deleted' => 0,
				));
			}
			if ($c->prepare() && $c->stmt->execute()) {
				while ($row = $c->stmt->fetch(PDO::FETCH_ASSOC)) {
					$ratings = $this->_getRatings($row['section']);
					if (isset($ratings[$action])) {
						$rating = $ratings[$action] * $row['value'];
						$record = array(
							'id' => $row['id'],
							'action' => $action,
							'rating' => $rating,
							'multiplier' => $row['value'],
							'ticket' => $row['ticket'],
							'section' => $row['section'],
							'createdby' => $row['createdby'],
							'createdon' => $row['createdon'],
							'owner' => $this->id,
							'year' => date('Y', strtotime($row['createdon'])),
							'month' => date('m', strtotime($row['createdon'])),
							'day' => date('d', strtotime($row['createdon'])),
						);

						$keys = array_keys($record);
						$fields = '`' . implode('`,`', $keys) . '`';
						$placeholders = substr(str_repeat('?,', count($keys)), 0, -1);
						$sql = "INSERT INTO {$this->xpdo->getTableName('TicketAuthorAction')} ({$fields}) VALUES ({$placeholders});";
						$this->xpdo->prepare($sql)->execute(array_values($record));
					}
				}
			}
		}
	}


	/**
	 * @return bool
	 */
	public function updateTotals() {
		$fields = array(
			'tickets' => 'ticket',
			'comments' => 'comment',
			'views' => 'view',
			'stars_tickets' => 'star_ticket',
			'stars_comments' => 'star_comment',
		);
		// Simple totals
		foreach ($fields as $field => $action) {
			$c = $this->xpdo->newQuery('TicketAuthorAction', array(
				'owner' => $this->id,
				'action' => $action,
			));
			$c->select('id');
			$count = $this->xpdo->getCount('TicketAuthorAction', $c);
			$this->set($field, $count);
		}
		// Votes
		foreach (array('ticket', 'comment') as $field) {
			foreach (array('up', 'down') as $type) {
				$count = $this->xpdo->getCount('TicketAuthorAction', array(
					'owner' => $this->id,
					'rating:' . ($type == 'up' ? '>' : '<') => 0,
					'action' => "vote_{$field}",
				));
				$this->set("votes_{$field}s_{$type}", $count);
			}
		}
		// Votes rating
		foreach (array('ticket', 'comment') as $field) {
			$c = $this->xpdo->newQuery('TicketAuthorAction', array(
				'owner' => $this->id,
				'action' => "vote_{$field}",
			));
			$c->select('SUM(rating)');
			if ($c->prepare() && $c->stmt->execute()) {
				$this->set("votes_{$field}s", floatval($c->stmt->fetchColumn()));
			}
		}
		// Total rating
		$c = $this->xpdo->newQuery('TicketAuthorAction', array('owner' => $this->id));
		$c->select('SUM(rating)');
		if ($c->prepare() && $c->stmt->execute()) {
			$this->set('rating', floatval($c->stmt->fetchColumn()));
		}

		return $this->save();
	}


	/**
	 * @param $type
	 *
	 * @return string
	 */
	protected function _getTotalField($type) {
		switch ($type) {
			case 'ticket':
				$field = 'tickets';
				break;
			case 'comment':
				$field = 'comments';
				break;
			case 'view':
				$field = 'views';
				break;
			case 'vote_ticket':
				$field = 'votes_tickets';
				break;
			case 'vote_comment':
				$field = 'votes_comments';
				break;
			case 'star_ticket':
				$field = 'stars_tickets';
				break;
			case 'star_comment':
				$field = 'stars_comments';
				break;
			default:
				$field = '';
		}

		return $field;
	}


	/**
	 * @param $section_id
	 *
	 * @return array
	 */
	protected function _getRatings($section_id) {
		if (!isset($this->_ratings[$section_id])) {
			/** @var TicketsSection $section */
			if (!$section = $this->xpdo->getObject('TicketsSection', $section_id)) {
				$section = $this->xpdo->newObject('TicketsSection');
			}

			$this->_ratings[$section_id] = $section->getProperties('ratings');
		}

		return $this->_ratings[$section_id];
	}


	/**
	 * @param null $cacheFlag
	 *
	 * @return bool
	 */
	public function save($cacheFlag = null) {
		if ($this->isNew()) {
			$this->set('createdon', time());
		}

		return parent::save($cacheFlag);
	}

}