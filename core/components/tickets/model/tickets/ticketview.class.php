<?php

/**
 * Class TicketView
 *
 * @property int $id
 */
class TicketView extends xPDOObject {

	/**
	 * @param null $cacheFlag
	 *
	 * @return bool
	 */
	public function save($cacheFlag = null) {
		$new = $this->isNew();
		$parent = parent::save($cacheFlag);

		if ($new && $uid = $this->get('uid')) {
			/** @var TicketAuthor $profile */
			if ($profile = $this->xpdo->getObject('TicketAuthor', $uid)) {
				$profile->addAction('view', $this->get('parent'), $this->get('parent'));
			}
		}

		return $parent;
	}


	/**
	 * @param array $ancestors
	 *
	 * @return bool
	 */
	public function remove(array $ancestors = array()) {
		/** @var TicketAuthor $profile */
		if ($profile = $this->xpdo->getObject('TicketAuthor', $this->get('uid'))) {
			$profile->removeAction('view', $this->get('parent'), $this->get('uid'));
		}

		return parent::remove($ancestors);
	}

}