<?php

class TicketThreadOpenProcessor extends modObjectUpdateProcessor {
	/** @var TicketThread $object */
	public $object;
	public $classKey = 'TicketThread';
	public $objectType = 'TicketThread';
	public $languageTopics = array('tickets:default');
	public $beforeSaveEvent = 'OnBeforeTicketThreadOpen';
	public $afterSaveEvent = 'OnTicketThreadOpen';
	public $permission = 'thread_close';


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
			'closed' => 0,
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
		$this->modx->logManagerAction($this->objectType . '_open', $this->classKey, $this->object->get($this->primaryKeyField));
	}

}

return 'TicketThreadOpenProcessor';