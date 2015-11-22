<?php

class CommentStarProcessor extends modObjectProcessor {
	public $classKey = 'TicketStar';
	public $permission = 'comment_star';


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

		/** @var TicketComment $object */
		if (!$object = $this->modx->getObject('TicketComment', $id)) {
			return $this->failure($this->modx->lexicon('ticket_comment_err_id', array('id' => $id)));
		}

		$data = array(
			'id' => $id,
			'class' => 'TicketComment',
			'createdby' => $this->modx->user->id
		);

		/** @var TicketStar $star */
		if ($star = $this->modx->getObject($this->classKey, $data)) {
			$star->remove();

			$this->modx->invokeEvent('OnCommentUnStar', array(
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

			$this->modx->invokeEvent('OnCommentStar', array(
				$this->objectType => &$star,
				'object' => &$star,
			));
		}
		$stars = $this->modx->getCount('TicketStar', array('id' => $id, 'class' => 'TicketComment'));

		return $this->success('', array('stars' => $stars));
	}

}

return 'CommentStarProcessor';