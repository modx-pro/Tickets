<?php

class TicketThreadUndeleteProcessor extends modObjectUpdateProcessor {
	/** @var TicketThread $object */
	public $object;
	public $classKey = 'TicketThread';
	public $objectType = 'TicketThread';
	public $languageTopics = array('tickets:default');
	public $beforeSaveEvent = 'OnBeforeTicketThreadUndelete';
	public $afterSaveEvent = 'OnTicketThreadUndelete';
	public $permission = 'thread_delete';


	/**
	 * @return bool
	 */
	public function beforeSet() {
		$this->properties = array();

		return true;
	}


	/**
	 * @return bool
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
	 *
	 */
	public function afterSave() {
		$this->modx->cacheManager->delete('tickets/latest.comments');
		$this->modx->cacheManager->delete('tickets/latest.tickets');

		return true;
	}


	/**
	 * @param string $action
	 */
	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType . '_undelete', $this->classKey, $this->object->get($this->primaryKeyField));
	}

}

return 'TicketThreadUndeleteProcessor';