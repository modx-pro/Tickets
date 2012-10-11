<?php

class Ticket extends modResource {
	public $showInContextMenu = false;

	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);
		$this->set('class_key','Ticket');
	}

	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'controllers/ticket/';
	}

	public function getContextMenuText() {
		$this->xpdo->lexicon->load('tickets:default');
		return array(
			'text_create' => $this->xpdo->lexicon('tickets'),
			'text_create_here' => $this->xpdo->lexicon('ticket_create_here'),
		);
	}

	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('tickets:default');
		return $this->xpdo->lexicon('ticket');
	}
}