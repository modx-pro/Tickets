<?php

class TicketCommentGetProcessor extends modObjectGetProcessor {
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets:default');
	public $objectType = 'comment';

	public function cleanup() {
		$comment = $this->object->toArray();
		$comment['text'] = html_entity_decode($comment['text']);
		$comment['raw'] = html_entity_decode($comment['raw']);

		return $this->success('',$comment);
	}
}

return 'TicketCommentGetProcessor';