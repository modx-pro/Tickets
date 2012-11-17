<?php
class TicketCommentRemoveProcessor extends modObjectRemoveProcessor  {
	public $checkRemovePermission = true;
	public $classKey = 'TicketComment';
	public $languageTopics = array('tickets');
	public $beforeRemoveEvent = '';
	public $afterRemoveEvent = '';
	private $children;

	public function beforeRemove() {
		$this->getChildren($this->object);
		$this->modx->removeCollection('TicketComment', array('id:IN' => $this->children));
		return true;
	}

	protected function getChildren(TicketComment $parent) {
		$children = $parent->getMany('Children');
		if (count($children) > 0) {
			/** @var TicketComment $child */
			foreach ($children as $child) {
				$this->children[] = $child->get('id');
				$this->getChildren($child);
			}
		}
	}

}
return 'TicketCommentRemoveProcessor';