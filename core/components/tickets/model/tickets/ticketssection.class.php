<?php
class TicketsSection extends modResource {
	public $showInContextMenu = true;

	function __construct(xPDO & $xpdo) {
		parent :: __construct($xpdo);
		$this->set('class_key','TicketsSection');
	}

	public static function getControllerPath(xPDO &$modx) {
		return $modx->getOption('tickets.core_path',null,$modx->getOption('core_path').'components/tickets/').'controllers/section/';
	}

	public function getContextMenuText() {
		 $this->xpdo->lexicon->load('tickets:default');
		 return array(
			 'text_create' => $this->xpdo->lexicon('tickets_section'),
			 'text_create_here' => $this->xpdo->lexicon('tickets_section_create_here'),
		 );
	 }

	public function getResourceTypeName() {
		$this->xpdo->lexicon->load('tickets:default');
		return $this->xpdo->lexicon('tickets_section');
	}

	public function getContent(array $options = array()) {
		$content = parent::getContent($options);
		$year = date('Y');
		$content .= '<div class="copyright">&copy; '.$year.'. All Rights Reserved.</div>';
		return $content;
	}
}