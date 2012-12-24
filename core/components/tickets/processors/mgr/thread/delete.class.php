<?php
class TicketThreadDeleteProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'TicketThread';
	public $languageTopics = array('tickets');
	public $beforeRemoveEvent = 'OnBeforeTicketThreadRemove';
	public $afterRemoveEvent = 'OnTicketThreadRemove';

	public function process() {
		$canRemove = $this->beforeRemove();
		if ($canRemove !== true) {
			return $this->failure($canRemove);
		}
		$preventRemoval = $this->fireBeforeRemoveEvent();
		if (!empty($preventRemoval)) {
			return $this->failure($preventRemoval);
		}

		// Toggle deleted status
		if ($this->object->get('deleted')) {
			$this->object->fromArray(array(
				'deleted' => 0
				,'deletedon' => null
				,'deletedby' => 0
			));
		}
		else {
			$this->object->fromArray(array(
				'deleted' => 1
				,'deletedon' => time()
				,'deletedby' => $this->modx->user->id
			));
		}

		if (!$this->object->save()) {
			return $this->failure($this->modx->lexicon($this->objectType.'_err_remove'));
		}
		$this->afterRemove();
		$this->fireAfterRemoveEvent();
		$this->logManagerAction();
		$this->cleanup();
		return $this->success('',array($this->primaryKeyField => $this->object->get($this->primaryKeyField)));
	}
}

return 'TicketThreadDeleteProcessor';