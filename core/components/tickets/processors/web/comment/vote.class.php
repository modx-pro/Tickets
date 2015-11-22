<?php

class TicketCommentVoteProcessor extends modObjectCreateProcessor {
	/** @var TicketVote $object */
	public $object;
	public $objectType = 'TicketVote';
	public $classKey = 'TicketVote';
	public $languageTopics = array('tickets:default');
	public $afterSaveEvent = 'OnCommentVote';
	public $permission = 'comment_vote';
	/* @var TicketComment $comment */
	private $comment;


	/**
	 * @return bool|null|string
	 */
	public function beforeSet() {
		$id = $this->getProperty('id');

		if (!$this->modx->user->isAuthenticated($this->modx->context->key)) {
			return $this->modx->lexicon('permission_denied');
		}
		elseif (!$this->comment = $this->modx->getObject('TicketComment', $id)) {
			return $this->modx->lexicon('ticket_comment_err_comment');
		}
		elseif ($this->comment->createdby == $this->modx->user->id) {
			return $this->modx->lexicon('ticket_comment_err_vote_own');
		}
		elseif ($this->modx->getCount($this->classKey, array('id' => $id, 'createdby' => $this->modx->user->get('id'), 'class' => 'TicketComment'))) {
			return $this->modx->lexicon('ticket_comment_err_vote_already');
		}

		return true;
	}


	/**
	 * @return bool
	 */
	public function beforeSave() {
		$this->modx->getRequest();
		$ip = $this->modx->request->getClientIp();

		$value = $this->getProperty('value');
		$value = $value > 0
			? 1
			: -1;

		$this->object->set('id', $this->comment->get('id'));
		$this->object->set('owner', $this->comment->get('createdby'));
		$this->object->set('class', 'TicketComment');
		$this->object->set('value', $value);
		$this->object->set('ip', $ip['ip']);
		$this->object->set('createdon', date('Y-m-d H:i:s'));
		$this->object->set('createdby', $this->modx->user->get('id'));

		return true;
	}


	/**
	 * @return array|string
	 */
	public function cleanup() {
		$rating = $this->comment->updateRating();

		return $this->success('', $rating);
	}

}

return 'TicketCommentVoteProcessor';