<?php

class TicketThreadDeleteProcessor extends modObjectUpdateProcessor {
	/** @var TicketThread $object */
	public $object;
	public $classKey = 'TicketThread';
	public $objectType = 'TicketThread';
	public $languageTopics = array('tickets:default');
	public $beforeSaveEvent = 'OnBeforeTicketThreadDelete';
	public $afterSaveEvent = 'OnTicketThreadDelete';
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
			'deleted' => 1,
			'deletedon' => time(),
			'deletedby' => $this->modx->user->get('id'),
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
		$this->modx->logManagerAction($this->objectType . '_delete', $this->classKey, $this->object->get($this->primaryKeyField));
	}

}

return 'TicketThreadDeleteProcessor';