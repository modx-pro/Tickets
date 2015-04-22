<?php

class TicketCommentUnpublishProcessor extends modObjectUpdateProcessor {
	/** @var TicketComment $object */
	public $object;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $beforeSaveEvent = 'OnBeforeCommentUnpublish';
	public $afterSaveEvent = 'OnCommentUnpublish';
	public $permission = 'comment_publish';
	protected $_sendEmails = false;


	/**
	 *
	 */
	public function beforeSet() {
		$this->properties = array();

		return true;
	}


	/**
	 * @return bool
	 */
	public function beforeSave() {
		$this->object->set('published', 0);

		return parent::beforeSave();
	}


	/**
	 * @return bool
	 */
	public function afterSave() {
		$this->object->clearTicketCache();
		/* @var TicketThread $thread */
		if ($thread = $this->object->getOne('Thread')) {
			$thread->updateLastComment();
		}

		$this->modx->cacheManager->delete('tickets/latest.comments');
		$this->modx->cacheManager->delete('tickets/latest.tickets');

		if ($this->_sendEmails) {
			$this->sendCommentMails();
		}

		return parent::afterSave();
	}


	/**
	 *
	 */
	protected function sendCommentMails() {
		/** @var TicketThread $thread */
		if ($thread = $this->object->getOne('Thread')) {
			/** @var Tickets $Tickets */
			if ($Tickets = $this->modx->getService('Tickets')) {
				$Tickets->config = $thread->get('properties');
				$Tickets->sendCommentMails($this->object->toArray());
			}
		}
	}


	/**
	 * @param string $action
	 */
	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType . '_unpublish', $this->classKey, $this->object->get($this->primaryKeyField));
	}

}

return 'TicketCommentUnpublishProcessor';