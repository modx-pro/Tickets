<?php
class TicketCommentVoteProcessor extends modObjectCreateProcessor {
	/** @var TicketVote $object */
	public $object;
	/* @var TicketComment $comment */
	private $comment;
	public $objectType = 'TicketVote';
	public $classKey = 'TicketVote';
	public $languageTopics = array('tickets:default');
	public $permission = 'comment_vote';


	public function beforeSet() {
		$id = $this->getProperty('id');

		if (!$this->comment = $this->modx->getObject('TicketComment', $id)) {
			return $this->modx->lexicon('ticket_comment_err_comment');
		}
		elseif ($this->comment->createdby == $this->modx->user->id) {
			return $this->modx->lexicon('ticket_comment_err_vote_own');
		}
		elseif ($this->modx->getCount($this->classKey, array('id' => $id, 'createdby' => $this->modx->user->id, 'class' => 'TicketComment'))) {
			return $this->modx->lexicon('ticket_comment_err_vote_already');
		}

		return true;
	}


	public function beforeSave() {
		$this->modx->getRequest();
		$ip = $this->modx->request->getClientIp();

		$value = $this->getProperty('value');
		$value = $value > 0 ? 1 : -1;

		$this->object->set('value', $value);
		$this->object->set('class', 'TicketComment');
		$this->object->set('id', $this->comment->id);
		$this->object->set('ip', $ip['ip']);
		$this->object->set('createdon', date('Y-m-d H:i:s'));
		$this->object->set('createdby', $this->modx->user->id);

		return true;
	}


	public function cleanup() {
		$rating = $this->comment->updateRating();

		return $this->success('', $rating);
	}

}

return 'TicketCommentVoteProcessor';