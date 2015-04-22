<?php

class TicketCommentRemoveProcessor extends modObjectRemoveProcessor {
	/** @var TicketComment $object */
	public $object;
	public $checkRemovePermission = true;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets');
	public $beforeRemoveEvent = 'OnBeforeCommentRemove';
	public $afterRemoveEvent = 'OnCommentRemove';
	public $permission = 'comment_remove';
	private $children = array();


	/**
	 * @return bool|null|string
	 */
	public function initialize() {
		$parent = parent::initialize();
		if ($this->checkRemovePermission && !$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return $parent;
	}


	/**
	 * @return bool
	 */
	public function beforeRemove() {
		$this->getChildren($this->object);
		$children = $this->modx->getIterator('TicketComment', array('id:IN' => $this->children));
		/** @var TicketComment $child */
		foreach ($children as $child) {
			$child->remove();
		}
		return true;
	}


	/**
	 * @param TicketComment $parent
	 */
	protected function getChildren(TicketComment $parent) {
		$children = $parent->getMany('Children');
		if (count($children) > 0) {
			/** @var TicketComment $child */
			foreach ($children as $child) {
				$this->children[] = $child->get('id');
				$this->getChildren($child);
			}
		}
	}


	/**
	 * @return bool
	 */
	public function afterRemove() {
		$this->object->clearTicketCache();
		/* @var TicketThread $thread */
		if ($thread = $this->object->getOne('Thread')) {
			$thread->updateLastComment();
		}

		$this->modx->cacheManager->delete('tickets/latest.comments');
		$this->modx->cacheManager->delete('tickets/latest.tickets');

		return parent::afterRemove();
	}


	/**
	 *
	 */
	public function logManagerAction() {
		$this->modx->logManagerAction($this->objectType . '_remove', $this->classKey, $this->object->get($this->primaryKeyField));
	}

}

return 'TicketCommentRemoveProcessor';