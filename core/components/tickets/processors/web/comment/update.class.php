<?php

class TicketCommentUpdateProcessor extends modObjectUpdateProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $permission = 'comment_save';
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';

	public function beforeSet() {
		$time = time() - strtotime($this->object->get('createdon'));

		if ($this->object->get('createdby') != $this->modx->user->id) {
			return $this->modx->lexicon('ticket_comment_err_wrong_user');
		}
		else if ($this->modx->getCount('TicketComment', array('parent' => $this->object->get('id')))) {
			return $this->modx->lexicon('ticket_comment_err_has_replies');
		}
		else if ($time >= $this->modx->getOption('tickets.comment_edit_time', null, 180)) {
			return $this->modx->lexicon('ticket_comment_err_no_time');
		}
		else if (!$text = $this->getProperty('text')) {
			return $this->modx->lexicon('ticket_err_empty_comment');
		}
		else {
			$this->properties = array(
				'text' => $text
				,'raw' => $this->getProperty('raw')
			);
		}

		return parent::beforeSet();
	}

	public function beforeSave() {
		$this->object->fromArray(array(
			'editedon' => time()
			,'editedby' => $this->modx->user->id
		));

		return parent::beforeSave();
	}

	public function afterSave() {
		/* @var TicketThread $thread */
		$thread = $this->object->getOne('Thread');
		$this->object->set('resource', $thread->get('resource'));

		return parent::afterSave();
	}

}

return 'TicketCommentUpdateProcessor';