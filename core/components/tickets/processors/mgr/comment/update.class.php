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
		if (!$this->getProperty('text')) {
			return $this->modx->lexicon('ticket_err_empty_comment');
		}

		return parent::beforeSet();
	}

	public function beforeSave() {
		$text = $this->getProperty('text');

		$this->object->fromArray(array(
			'editedon' => time()
			,'editedby' => $this->modx->user->id
			,'text' => $this->modx->Tickets->Jevix($text, 'Comment')
			,'raw' => $text
		));

		return parent::beforeSave();
	}

}

return 'TicketCommentUpdateProcessor';