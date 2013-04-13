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


	/*
	 * Try to get parent ticket and clear its cache
	 * */
	public function clearTicketCache() {
		$clear = $this->xpdo->getOption('tickets.clear_cache_on_comment_save');
		if (!empty($clear) && $clear != 'false') {
			/* @var TicketThread $thread */
			/* @var Ticket $ticket */
			$thread = $this->getOne('Thread');
			if ($ticket = $thread->getOne('Ticket')) {
				if (method_exists($ticket, 'clearCache')) {
					$ticket->clearCache();
				}
			}
		}
	}

}