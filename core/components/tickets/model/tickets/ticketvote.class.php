<?php

/**
 * Class TicketVote
 *
 * @property int $id
 */
class TicketVote extends xPDOObject {

	/**
	 * @param null $cacheFlag
	 *
	 * @return bool
	 */
	public function save($cacheFlag = null) {
		$new = $this->isNew();
		$class = $this->get('class');
		$save = parent::save($cacheFlag);
		if ($new) {
			$type = '';
			$ticket_id = 0;
			if ($class == 'TicketComment') {
				$type = 'vote_comment';
				/** @var TicketComment $comment */
				if ($comment = $this->xpdo->getObject('TicketComment', $this->id)) {
					/** @var TicketThread $comment */
					if ($thread = $comment->getOne('Thread')) {
						$ticket_id = $thread->get('resource');
					}
				}
			}
			elseif ($class == 'Ticket') {
				$type = 'vote_ticket';
				$ticket_id = $this->id;
			}
			if (!empty($type) && !empty($ticket_id)) {
				$multiplier = $this->get('value');
				/** @var TicketAuthor $profile */
				if ($profile = $this->xpdo->getObject('TicketAuthor', $this->get('owner'))) {
					$profile->addAction($type, $this->id, $ticket_id, $multiplier);
				}
			}
		}

		return $save;
	}


	/**
	 * @param array $ancestors
	 *
	 * @return bool
	 */
	public function remove(array $ancestors = array()) {
		$type = '';
		$class = $this->get('class');
		if ($class == 'TicketComment') {
			$type = 'vote_comment';
		}
		elseif ($class == 'Ticket') {
			$type = 'vote_ticket';
		}
		if (!empty($type)) {
			/** @var TicketAuthor $profile */
			if ($profile = $this->xpdo->getObject('TicketAuthor', $this->get('owner'))) {
				$profile->removeAction($type, $this->id, $this->get('createdby'));
			}
		}

		return parent::remove($ancestors);
	}
}