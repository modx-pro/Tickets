<?php

/**
 * Overrides the modResourceCreateProcessor to provide custom processor functionality for the TicketsSection type
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/create.class.php';

class TicketsSectionCreateProcessor extends modResourceCreateProcessor {
	/** @var TicketsSection $object */
	public $object;
	public $classKey = 'TicketsSection';


	/** {@inheritDoc} */
	public function beforeSet() {
		$this->setProperties(array(
			'isfolder' => 1
		));
		return parent::beforeSet();
	}


	/** {@inheritDoc} */
	public function prepareAlias() {
		if ($this->workingContext->getOption('tickets.section_id_as_alias')) {
			$alias = 'empty-resource-alias';
			$this->setProperty('alias', $alias);
		}
		else {
			$alias = parent::prepareAlias();
		}

		return $alias;
	}


	/** {@inheritDoc} */
	public function afterSave() {
		if ($this->object->alias == 'empty-resource-alias') {
			$this->object->set('alias', $this->object->id);
			$this->object->save();
		}

		// Updating resourceMap before OnDocSaveForm event
		$results = $this->modx->cacheManager->generateContext($this->object->context_key);
		if (isset($results['resourceMap'])) {$this->modx->context->resourceMap = $results['resourceMap'];}
		if (isset($results['aliasMap'])) {$this->modx->context->aliasMap = $results['aliasMap'];}

		return parent::afterSave();
	}

}