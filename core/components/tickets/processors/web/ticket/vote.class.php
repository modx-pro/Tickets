<?php
class TicketVoteProcessor extends modObjectCreateProcessor {
	/** @var TicketVote $object */
	public $object;
	/* @var Ticket $ticket */
	private $ticket;
	public $objectType = 'TicketVote';
	public $classKey = 'TicketVote';
	public $languageTopics = array('tickets:default');
	public $permission = 'ticket_vote';


	/** {@inheritDoc} */
	public function beforeSet() {
		$id = $this->getProperty('id');

		if (!$this->ticket = $this->modx->getObject('Ticket', $id)) {
			return $this->modx->lexicon('ticket_err_ticket');
		}
		elseif ($this->ticket->createdby == $this->modx->user->id) {
			return $this->modx->lexicon('ticket_err_vote_own');
		}
		/** @var TicketVote $vote */
		elseif ($this->modx->getCount($this->classKey, array('id' => $id, 'createdby' => $this->modx->user->id, 'class' => 'Ticket'))) {
			return $this->modx->lexicon('ticket_err_vote_already');
		}

		return true;
	}


	/** {@inheritDoc} */
	public function beforeSave() {
		$this->modx->getRequest();
		$ip = $this->modx->request->getClientIp();

		$value = $this->getProperty('value');
		if ($value > 0) {
			$value = 1;
		}
		elseif ($value < 0) {
			$value = -1;
		}
		else {
			$value = 0;
		}

		$this->object->set('id', $this->ticket->id);
		$this->object->set('class', 'Ticket');
		$this->object->set('value', $value);
		$this->object->set('ip', $ip['ip']);
		$this->object->set('createdon', date('Y-m-d H:i:s'));
		$this->object->set('createdby', $this->modx->user->id);

		return true;
	}


	/** {@inheritDoc} */
	public function cleanup() {
		$rating = $this->ticket->updateRating();

		return $this->success('', $rating);
	}

}

return 'TicketVoteProcessor';