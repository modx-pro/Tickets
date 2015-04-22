<?php

class TicketCommentUndeleteProcessor extends modObjectUpdateProcessor {
	/** @var TicketComment $object */
	public $object;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $beforeSaveEvent = 'OnBeforeCommentUndelete';
	public $afterSaveEvent = 'OnCommentUndelete';
	public $permission = 'comment_delete';


	/**
	 *
	 */
	public function beforeSet() {
		$this->properties = array();

		return true;
	}


	/**
	 * @return bool|null|string
	 */
	public function beforeSave() {
		$this->object->fromArray(array(
			'deleted' => 0,
			'deletedon' => null,
			'deletedby' => 0,
		));

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

		return parent::afterSave();
	}


	/**
	 *
	 */
	public function logManagerAction() {
		$this->modx->logManagerAction($this->objectType . '_undelete', $this->classKey, $this->object->get($this->primaryKeyField));
	}

}

return 'TicketCommentUndeleteProcessor';