<?php
class TicketThreadRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'TicketThread';
	public $languageTopics = array('tickets');
	public $beforeRemoveEvent = 'OnBeforeTicketThreadRemove';
	public $afterRemoveEvent = 'OnTicketThreadRemove';

	public function beforeRemove() {
		$this->modx->removeCollection('TicketComment', array('thread' => $this->object->get('id')));
		return true;
	}

}
return 'TicketThreadRemoveProcessor';