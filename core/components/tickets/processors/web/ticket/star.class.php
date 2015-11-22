<?php

class TicketStarProcessor extends modObjectProcessor {
	public $classKey = 'TicketStar';
	public $permission = 'ticket_star';


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

		/** @var Ticket $object */
		if (!$object = $this->modx->getObject('modResource', $id)) {
			return $this->failure($this->modx->lexicon('ticket_err_id', array('id' => $id)));
		}

		$data = array(
			'id' => $id,
			'class' => 'Ticket',
			'createdby' => $this->modx->user->id
		);

		/** @var TicketStar $star */
		if ($star = $this->modx->getObject($this->classKey, $data)) {
			$star->remove();

			$this->modx->invokeEvent('OnTicketUnStar', array(
				$this->objectType => &$star,
				'object' => &$star,
			));
		}
		else {
			$star = $this->modx->newObject($this->classKey);
			$data['owner'] = $object->get('createdby');
			$data['createdon'] = date('Y-m-d H:i:s');

			$star->fromArray($data, '', true, true);
			$star->save();

			$this->modx->invokeEvent('OnTicketStar', array(
				$this->objectType => &$star,
				'object' => &$star,
			));
		}

		$stars = $this->modx->getCount('TicketStar', array('id' => $id, 'class' => 'Ticket'));
		return $this->success('', array('stars' => $stars));
	}

}

return 'TicketStarProcessor';