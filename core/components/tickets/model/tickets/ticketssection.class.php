<?php
/**
 * The TicketsSection CRC for Tickets.
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

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

		return $content;
	}
}



/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */
class TicketsSectionCreateProcessor extends modResourceCreateProcessor {

	public function beforeSave() {
		return true;
	}

}



/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */
class TicketsSectionUpdateProcessor extends modResourceUpdateProcessor {

	public function beforeSave() {
		return true;
	}

}
