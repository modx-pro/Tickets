<?php
class TicketThreadDeleteProcessor extends modObjectRemoveProcessor  {
	public $checkClosePermission = true;
	public $classKey = 'TicketThread';
	public $objectType = 'TicketThread';
	public $languageTopics = array('tickets');
	public $beforeCloseEvent = 'OnBeforeTicketThreadClose';
	public $afterCloseEvent = 'OnTicketThreadClose';

	public function process() {
		$canClose = $this->beforeRemove();
		if ($canClose !== true) {
			return $this->failure($canClose);
		}
		$preventRemoval = $this->fireBeforeRemoveEvent();
		if (!empty($preventRemoval)) {
			return $this->failure($preventRemoval);
		}

		// Toggle closed status
		if ($this->object->get('closed')) {
			$this->object->set('closed', 0);
			$action = 'open';
		}
		else {
			$this->object->set('closed', 1);
			$action = 'close';
		}

		if (!$this->object->save()) {
			return $this->failure($this->modx->lexicon($this->objectType.'_err_'.$action));
		}
		$this->afterRemove();
		$this->fireAfterRemoveEvent();
		$this->logManagerAction($action);
		$this->cleanup();
		return $this->success('',array($this->primaryKeyField => $this->object->get($this->primaryKeyField)));
	}


	public function cleanup() {
		$this->modx->cacheManager->delete('tickets/latest.comments');
		$this->modx->cacheManager->delete('tickets/latest.tickets');
	}


	public function logManagerAction($action = '') {
		$this->modx->logManagerAction($this->objectType.'_'.$action, $this->classKey, $this->object->get($this->primaryKeyField));
	}
}

return 'TicketThreadDeleteProcessor';