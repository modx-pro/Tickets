<?php
class TicketCommentCreateProcessor extends modObjectCreateProcessor {
	private $thread;
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $objectType = 'comment';
	public $permission = 'comment_save';
	public $beforeSaveEvent = '';
	public $afterSaveEvent = '';


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
			,'createdby' => $this->modx->user->id
			,'createdon' => date('Y-m-d H:i:s')
			,'name' => $this->modx->user->Profile->fullname
			,'email' => $this->modx->user->Profile->email
			,'ip' => $_SERVER['REMOTE_ADDR']
			,'resource' => $this->thread->resource
		));

		return parent::beforeSet();
	}

	public function afterSave() {
		$thread = $this->object->getOne('Thread');
		$thread->fromArray(array(
			'comment_last' => $this->object->get('id')
			,'comment_time' => $this->object->get('createdon')
		));
		$thread->save();

		return parent::afterSave();
	}
}

return 'TicketCommentCreateProcessor';