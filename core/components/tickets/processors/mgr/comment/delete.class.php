<?php
class TicketCommentDeleteProcessor extends modObjectRemoveProcessor  {
	/** @var TicketComment $object */
	public $object;
	public $checkRemovePermission = true;
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets');
	public $beforeRemoveEvent = 'OnBeforeCommentRemove';
	public $afterRemoveEvent = 'OnCommentRemove';
	public $permission = 'comment_save';

	public function initialize() {
		$parent = parent::initialize();
		if ($this->checkRemovePermission && !$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return $parent;
	}

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

	public function afterRemove() {
		$this->object->clearTicketCache();

		return parent::afterRemove();
	}
}

return 'TicketCommentDeleteProcessor';