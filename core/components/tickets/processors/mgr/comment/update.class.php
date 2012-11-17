<?php

class TicketCommentUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('modextra');
	public $permission = 'update_document';

	public function beforeSave() {
		$this->object->fromArray(array(
			'editedon' => time()
			,'editedby' => $this->modx->user->id
			,'text' => $this->modx->tickets->Jevix($this->getProperty('text'), 'Comment')
		));
		return parent::beforeSave();
	}
}

return 'TicketCommentUpdateProcessor';