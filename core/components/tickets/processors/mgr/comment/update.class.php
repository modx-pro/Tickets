<?php

class TicketCommentUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('modextra');
	public $permission = 'update_document';

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