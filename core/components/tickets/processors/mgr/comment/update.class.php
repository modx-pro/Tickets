<?php

class TicketCommentUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	//public $permission = 'comment_save';
	public $permission = 'update_document';
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';

	public function beforeSet() {
		if (!$this->getProperty('name')) {
			$this->unsetProperty('name');
		}
		if (!$this->getProperty('email')) {
			$this->unsetProperty('email');
		}
		$this->object->fromArray(array(
			'editedon' => time()
			,'editedby' => $this->modx->user->id
			,'text' => $this->modx->tickets->Jevix($this->getProperty('text'), 'Comment')
		));
		return parent::beforeSet();
	}
}

return 'TicketCommentUpdateProcessor';