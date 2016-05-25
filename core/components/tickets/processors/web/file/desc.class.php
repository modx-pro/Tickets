<?php

class TicketFileDescProcessor extends modObjectProcessor {
	public $classKey = 'TicketFile';
	public $permission = 'ticket_file_upload';


	/**
	 * @return bool|null|string
	 */
	public function initialize() {
		if (!$this->modx->hasPermission($this->permission)) {
			return $this->modx->lexicon('access_denied');
		}
		return true;
	}


	/**
	 * @return array|string
	 */
	public function process() {
		$id = $this->getProperty('id');
		$description = $this->getProperty('description');
		/** @var TicketFile $file */
		if (!$file = $this->modx->getObject($this->classKey, $id)) {
			return $this->failure($this->modx->lexicon('ticket_err_file_ns'));
		}
		elseif ($file->createdby != $this->modx->user->id && !$this->modx->user->isMember('Administrator')) {
			return $this->failure($this->modx->lexicon('ticket_err_file_owner'));
		}
		$file->set('description', $description);
		$file->save();

		return $this->success();
	}

}

return 'TicketFileDescProcessor';
