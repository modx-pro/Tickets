<?php
class TicketThread extends xPDOSimpleObject {

	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);

		$this->set('comments',0);
	}

	public function getCommentsCount() {
		$q = $this->xpdo->newQuery('TicketComment', array('thread' => $this->get('id')));

		return $this->xpdo->getCount('TicketComment', $q);
	}

	public function updateLastComment() {
		$q = $this->xpdo->newQuery('TicketComment', array('thread' => $this->get('id')));
		$q->sortby('createdon','DESC');
		$q->limit(1);
		$q->select('id as comment_last,createdon as comment_time');
		if ($q->prepare() && $q->stmt->execute()) {
			$comment = $q->stmt->fetch(PDO::FETCH_ASSOC);
			if (empty($comment)) {
				$comment = array('comment_last' => 0, 'comment_time' => 0);
			}
			$this->fromArray($comment);
			$this->save();
		}
	}

	public function get($k, $format = null, $formatTemplate= null) {
		if ($k == 'comments') {
			return $this->getCommentsCount();
		}
		else {
			return parent::get($k, $format, $formatTemplate);
		}
	}

	public function toArray($keyPrefix= '', $rawValues= false, $excludeLazy= false, $includeRelated= false) {
		$array = parent::toArray($keyPrefix, $rawValues, $excludeLazy, $includeRelated);
		$array['comments'] = $this->getCommentsCount();

		return $array;
	}

}