<?php

class TicketCommentUpdateProcessor extends modObjectUpdateProcessor {
	/** @var TicketComment $object */
	public $object;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';
	public $permission = 'comment_save';
	protected $old_thread = 0;


	/** {@inheritDoc} */
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

		$this->old_thread = $this->object->get('thread');
		$parent = $this->getProperty('parent');
		// New parent is in other thread
		if ($parent != 0 && $parent != $this->object->get('parent')) {
			if ($parent = $this->modx->getObject('TicketComment', $this->getProperty('parent'))) {
				$this->setProperty('thread', $parent->get('thread'));
			}
		}

		return parent::beforeSet();
	}


	/** {@inheritDoc} */
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


	/** {@inheritDoc} */
	public function afterSave() {
		$new_thread = $this->object->get('thread');
		if ($this->old_thread != $new_thread) {
			$this->object->changeThread($this->old_thread, $new_thread);
		}
		else {
			$this->object->clearTicketCache();
		}

		return parent::afterSave();
	}

}

return 'TicketCommentUpdateProcessor';