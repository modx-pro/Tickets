<?php

class TicketThreadRemoveProcessor extends modObjectRemoveProcessor {
	/** @var TicketThread $object */
	public $object;
	public $checkRemovePermission = true;
	public $classKey = 'TicketThread';
	public $objectType = 'TicketThread';
	public $languageTopics = array('tickets');
	public $beforeRemoveEvent = 'OnBeforeTicketThreadRemove';
	public $afterRemoveEvent = 'OnTicketThreadRemove';
	public $permission = 'thread_remove';


	/**
	 * @return bool
	 */
	public function beforeRemove() {
		$comments = $this->modx->getIterator('TicketComment', array('thread' => $this->object->get('id')));
		/** @var TicketComment $comment */
		foreach ($comments as $comment) {
			$comment->remove();
		}

		return true;
	}


	/**
	 * @param string $action
	 */
	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType . '_remove', $this->classKey, $this->object->get($this->primaryKeyField));
	}


	/**
	 *
	 */
	public function afterRemove() {
		$this->modx->cacheManager->delete('tickets/latest.comments');
		$this->modx->cacheManager->delete('tickets/latest.tickets');

		return true;
	}

}

return 'TicketThreadRemoveProcessor';