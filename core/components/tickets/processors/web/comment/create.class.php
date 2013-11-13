<?php
class TicketCommentCreateProcessor extends modObjectCreateProcessor {
	/** @var TicketComment $object */
	public $object;
	/* @var TicketThread $thread */
	private $thread;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $permission = 'comment_save';
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';


	public function beforeSet() {
		$tid = $this->getProperty('thread');
		if (!$this->thread = $this->modx->getObject('TicketThread', array('name' => $tid, 'deleted' => 0, 'closed' => 0))) {
			return $this->modx->lexicon('ticket_err_wrong_thread');
		}
		$text = trim($this->getProperty('text'));
		if (empty($text)) {
			return $this->modx->lexicon('ticket_comment_err_empty');
		}

		if ($pid = $this->getProperty('parent')) {
			if (!$parent = $this->modx->getObject('TicketComment', array('id' => $pid, 'published' => 1, 'deleted' => 0))) {
				return $this->modx->lexicon('ticket_comment_err_parent');
			}
		}

		$this->setProperties(array(
			'thread' => $this->thread->id
			,'name' => $this->modx->user->Profile->fullname
			,'email' => $this->modx->user->Profile->email
			,'ip' => $_SERVER['REMOTE_ADDR']
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->modx->user->id
			,'editedon' => ''
			,'editedby' => 0
			,'deleted' => 0
			,'deletedon' => ''
			,'deletedby' => 0
		));

		return parent::beforeSet();
	}

	public function afterSave() {
		$this->thread->fromArray(array(
			'comment_last' => $this->object->get('id')
			,'comment_time' => $this->object->get('createdon')
		));
		$this->thread->save();

		$this->thread->updateCommentsCount();
		$this->object->clearTicketCache();

		return parent::afterSave();
	}
}

return 'TicketCommentCreateProcessor';