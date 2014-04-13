<?php
class TicketCommentPublishProcessor extends modObjectUpdateProcessor  {
	/** @var TicketComment $object */
	public $object;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	//public $permission = 'comment_save';
	public $permission = 'update_document';
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';
	protected $_sendEmails = false;


	public function beforeSave() {
		if ($this->object->get('published')) {
			$this->object->set('published', 0);
		}
		else {
			$this->object->set('published', 1);
			$properties = $this->object->get('properties');
			if (array_key_exists('was_published', $properties)) {
				unset($properties['was_published']);
				$this->object->set('properties', $properties);
				$this->_sendEmails = true;
			}
		}

		return parent::beforeSave();
	}


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


	public function logManagerAction($action = '') {
		$action = $this->object->get('published') ? 'publish' : 'unpublish';
		$this->modx->logManagerAction($this->objectType.'_'.$action, $this->classKey, $this->object->get($this->primaryKeyField));
	}


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

}

return 'TicketCommentPublishProcessor';