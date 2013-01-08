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