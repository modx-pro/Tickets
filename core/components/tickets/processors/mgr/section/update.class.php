<?php

/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class TicketsSectionUpdateProcessor extends modResourceUpdateProcessor {
	/** @var TicketsSection $object */
	public $object;
	public $classKey = 'TicketsSection';

	public function beforeSet() {
		$this->setProperties(array(
			'hide_children_in_tree' => 1
			,'isfolder' => 1
		));
		return parent::beforeSet();
	}

}
