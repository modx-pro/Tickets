<?php
class TicketCommentCreateProcessor extends modObjectCreateProcessor {
	/* @var TicketThread $thread */
	private $thread;
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $permission = 'comment_save';
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';


	public function beforeSet() {
		if (!$this->thread = $this->modx->getObject('TicketThread', array('name' => $this->getProperty('thread')))) {
			return $this->modx->lexicon('ticket_err_wrong_thread');
		}
		$text = trim($this->getProperty('text'));
		if (empty($text)) {
			return $this->modx->lexicon('ticket_err_empty_comment');
		}
		$this->setProperties(array(
			'thread' => $this->thread->id
			,'name' => $this->modx->user->Profile->fullname
			,'email' => $this->modx->user->Profile->email
			,'ip' => $_SERVER['REMOTE_ADDR']
			,'createdon' => date('Y-m-d H:i:s')
			,'createdby' => $this->modx->user->id
			,'editedon' => 0
			,'editedby' => 0
			,'deleted' => 0
			,'deletedon' => 0
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

		$this->object->set('resource', $this->thread->get('resource'));

		return parent::afterSave();
	}
}

return 'TicketCommentCreateProcessor';