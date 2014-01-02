<?php
/**
 * @package tickets
 */
class TicketComment extends xPDOSimpleObject {
	public $class_key = 'TicketComment';

	/**
	 * {@inheritDoc}
	 */
	public function & getMany($alias, $criteria= null, $cacheFlag= true) {
		if ($alias == 'Attachments' || $alias == 'Votes') {
			$criteria = array('class' => $this->class_key);
		}
		return parent::getMany($alias, $criteria, $cacheFlag);
	}

	/**
	 * {@inheritDoc}
	 */
	public function addMany(& $obj, $alias= '') {
		$added= false;
		if (is_array($obj)) {
			foreach ($obj as $o) {
				if (is_object($o)) {
					$o->set('class', $this->class_key);
					$added = parent::addMany($obj, $alias);
				}
			}
			return $added;
		}
		else {
			return parent::addMany($obj, $alias);
		}
	}


	/**
	 * Try to clear cache of ticket
	 *
	 * @return bool
	 */
	public function clearTicketCache() {
		$clear = $this->xpdo->getOption('tickets.clear_cache_on_comment_save');
		if (!empty($clear) && $clear != 'false') {
			/** @var TicketThread $thread */
			$thread = $this->getOne('Thread');
			/** @var modResource|Ticket $ticket */
			if ($ticket = $this->xpdo->getObject('modResource', $thread->get('resource'))) {
				if (method_exists($ticket, 'clearCache')) {
					$ticket->clearCache();
					return true;
				}
			}
		}
		return false;
	}


	/**
	 * Move comment from one thread to another and clear cache of its tickets
	 *
	 * @param int $from
	 * @param int $to
	 *
	 * @return bool
	 */
	public function changeThread($from, $to) {
		/** @var TicketThread $old_thread */
		$old_thread = $this->xpdo->getObject('TicketThread', $from);
		/** @var TicketThread $new_thread */
		$new_thread = $this->xpdo->getObject('TicketThread', $to);

		if ($new_thread && $old_thread) {
			$this->set('thread', $to);
			$this->save();

			$children = $this->getMany('Children');
			/** @var TicketComment $child */
			foreach ($children as $child) {
				$child->set('parent', $to);
				$child->save();
			}

			$old_thread->updateLastComment();
			/** @var modResource|Ticket $ticket */
			if ($ticket = $this->xpdo->getObject('modResource', $old_thread->get('resource'))) {
				if (method_exists($ticket, 'clearCache')) {
					$ticket->clearCache();
				}
			}

			$new_thread->updateLastComment();
			/** @var modResource|Ticket $ticket */
			if ($ticket = $this->xpdo->getObject('modResource', $new_thread->get('resource'))) {
				if (method_exists($ticket, 'clearCache')) {
					$ticket->clearCache();
				}
			}

			return true;
		}
		return false;
	}

}