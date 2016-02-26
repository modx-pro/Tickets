<?php

class TicketCommentCreateProcessor extends modObjectCreateProcessor {
	/** @var TicketComment $object */
	public $object;
	public $objectType = 'TicketComment';
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $permission = 'comment_save';
	public $beforeSaveEvent = 'OnBeforeCommentSave';
	public $afterSaveEvent = 'OnCommentSave';
	/* @var TicketThread $thread */
	private $thread;
	private $guest = false;


	/**
	 * @return bool
	 */
	public function checkPermissions() {
		$this->guest = (bool)$this->getProperty('allowGuest', false);
		$this->unsetProperty('allowGuest');
		$this->unsetProperty('allowGuestEdit');
		$this->unsetProperty('captcha');

		return !empty($this->permission) && !$this->guest
			? $this->modx->hasPermission($this->permission)
			: true;
	}


	/**
	 * @return bool|null|string
	 */
	public function beforeSet() {
		$tid = $this->getProperty('thread');
		if (!$this->thread = $this->modx->getObject('TicketThread', array('name' => $tid, 'deleted' => 0, 'closed' => 0))) {
			return $this->modx->lexicon('ticket_err_wrong_thread');
		}
		elseif ($pid = $this->getProperty('parent')) {
			if (!$parent = $this->modx->getObject('TicketComment', array('id' => $pid, 'published' => 1, 'deleted' => 0))) {
				return $this->modx->lexicon('ticket_comment_err_parent');
			}
		}

		// Required fields
		$requiredFields = array_map('trim', explode(',', $this->getProperty('requiredFields', 'name,email')));
		foreach ($requiredFields as $field) {
			$value = $this->modx->stripTags(trim($this->getProperty($field)));
			if (empty($value)) {
				$this->addFieldError($field, $this->modx->lexicon('field_required'));
			}
			elseif ($field == 'email' && !preg_match('/.+@.+\..+/i', $value)) {
				$this->setProperty('email', '');
				$this->addFieldError($field, $this->modx->lexicon('ticket_comment_err_email'));
			}
			else {
				if ($field == 'email') {
					$value = strtolower($value);
				}
				$this->setProperty($field, $value);
			}
		}
		if (!$text = trim($this->getProperty('text'))) {
			return $this->modx->lexicon('ticket_comment_err_empty');
		}
		if (!$this->getProperty('email') && $this->modx->user->isAuthenticated($this->modx->context->key)) {
			return $this->modx->lexicon('ticket_comment_err_no_email');
		}

		// Additional properties
		$properties = $this->getProperties();
		$add = array();
		$meta = $this->modx->getFieldMeta('TicketComment');
		foreach ($properties as $k => $v) {
			if (!isset($meta[$k])) {
				$add[$k] = $this->modx->stripTags($v);
			}
		}
		if (!$this->getProperty('published')) {
			$add['was_published'] = false;
		}
		unset($properties['requiredFields']);

		// Comment values
		$ip = $this->modx->request->getClientIp();
		$this->setProperties(array(
			'text' => $text,
			'thread' => $this->thread->id,
			'ip' => $ip['ip'],
			'createdon' => date('Y-m-d H:i:s'),
			'createdby' => $this->modx->user->isAuthenticated($this->modx->context->key)
				? $this->modx->user->id
				: 0,
			'editedon' => '',
			'editedby' => 0,
			'deleted' => 0,
			'deletedon' => '',
			'deletedby' => 0,
			'properties' => $add,
		));
		$this->unsetProperty('action');

		return parent::beforeSet();
	}


	/**
	 * @return bool
	 */
	public function afterSave() {
		if ($this->object->get('published')) {
			$this->thread->fromArray(array(
				'comment_last' => $this->object->get('id'),
				'comment_time' => $this->object->get('createdon'),
			));
			$this->thread->save();
		}

		if ($this->guest) {
			if (!isset($_SESSION['TicketComments'])) {
				$_SESSION['TicketComments'] = array('ids' => array());
			}
			$_SESSION['TicketComments']['name'] = $this->object->get('name');
			$_SESSION['TicketComments']['email'] = $this->object->get('email');
			$_SESSION['TicketComments']['ids'][$this->object->get('id')] = 1;
		}

		$this->thread->updateCommentsCount();
		$this->object->clearTicketCache();

		return parent::afterSave();
	}

}

return 'TicketCommentCreateProcessor';
