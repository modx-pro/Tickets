<?php
class TicketCommentPublishProcessor extends modObjectUpdateProcessor  {
	/** @var TicketComment $object */
	public $object;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	//public $permission = 'comment_save';
	public $permission = 'update_document';
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';

	public function beforeSave() {
		if ($this->object->get('published')) {
			$this->object->set('published', 0);
		}
		else {
			$this->object->set('published', 1);
		}

		return parent::beforeSave();
	}

	public function afterSave() {
		$this->object->clearTicketCache();

		return parent::afterSave();
	}


	public function logManagerAction($action = '') {
		$action = $this->object->get('published') ? 'publish' : 'unpublish';
		$this->modx->logManagerAction($this->objectType.'_'.$action, $this->classKey, $this->object->get($this->primaryKeyField));
	}
}

return 'TicketCommentPublishProcessor';