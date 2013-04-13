<?php
class TicketThreadRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'TicketThread';
	public $objectType = 'TicketThread';
	public $languageTopics = array('tickets');
	public $beforeRemoveEvent = 'OnBeforeTicketThreadRemove';
	public $afterRemoveEvent = 'OnTicketThreadRemove';

	public function beforeRemove() {
		$this->modx->removeCollection('TicketComment', array('thread' => $this->object->get('id')));
		return true;
	}


	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType.'_remove', $this->classKey, $this->object->get($this->primaryKeyField));
	}

}

return 'TicketThreadRemoveProcessor';