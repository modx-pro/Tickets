<?php
/**
 * Overrides the modResourceUpdateProcessor to provide custom processor functionality for the Ticket type
 *
 * @package tickets
 */

require_once MODX_CORE_PATH.'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH.'model/modx/processors/resource/update.class.php';

class TicketUpdateProcessor extends modResourceUpdateProcessor {
	/** @var Ticket $object */
	public $object;
	public $classKey = 'Ticket';
	public $permission = 'ticket_save';
	public $languageTopics = array('resource','tickets:default');
	private $_published = 0;


	/** {inheritDoc} */
	public function initialize() {
		$primaryKey = $this->getProperty($this->primaryKeyField,false);
		if (empty($primaryKey)) return $this->modx->lexicon($this->objectType.'_err_ns');

		if (!$this->modx->getCount($this->classKey, array('id' => $primaryKey, 'class_key' => $this->classKey)) && $res = $this->modx->getObject('modResource', $primaryKey)) {
			$res->set('class_key', $this->classKey);
			$res->save();
		}

		return parent::initialize();
	}


	/** {@inheritDoc} */
	public function beforeSet() {
		$this->_published = $this->getProperty('published');

		if ($this->object->createdby != $this->modx->user->id && !$this->modx->hasPermission('edit_document')) {
			return $this->modx->lexicon('ticket_err_wrong_user');
		}

		// Required fields
		$requiredFields = $this->getProperty('requiredFields', array('parent','pagetitle','content'));
		foreach ($requiredFields as $field) {
			$value = trim($this->getProperty($field));
			if (empty($value) && $this->modx->context->key != 'mgr') {
				$this->addFieldError($field, $this->modx->lexicon('field_required'));
			}
			else {
				$this->setProperty($field, $value);
			}
		}
		if (!$this->getProperty('content') && $this->modx->context->key != 'mgr') {
			return $this->modx->lexicon('ticket_err_empty');
		}

		$set = parent::beforeSet();
		if ($this->hasErrors()) {
			return $this->modx->lexicon('ticket_err_form');
		}
		$this->setFieldDefault();

		return $set;
	}


	/** {@inheritDoc} */
	public function setFieldDefault() {
		// Ticket properties
		$properties = $this->modx->context->key == 'mgr'
			? $this->getProperty('properties')
			: $this->object->getProperties();
		$this->unsetProperty('properties');

		// Define introtext
		$introtext = $this->getProperty('introtext');
		if (empty($introtext)) {
			$introtext = $this->object->getIntroText($this->getProperty('content'), false);
		}
		if (empty($properties['disable_jevix'])) {
			$introtext = $this->object->Jevix($introtext);
		}

		// Set properties
		if ($this->modx->context->key != 'mgr') {
			$this->unsetProperty('properties');
		}
		$this->setProperties(array(
			'class_key' => 'Ticket',
			'published' => $this->modx->context->key == 'mgr'
				? $this->getProperty('published')
				: $this->_published,
			'syncsite' => 0,
			'introtext' => $introtext,
		));
		if ($this->modx->context->key == 'mgr') {
			$properties['disable_jevix'] = empty($properties['disable_jevix']);
			$properties['process_tags'] = empty($properties['process_tags']);
			$this->object->setProperties($properties, 'tickets', true);
		}
		return true;
	}


	/** {@inheritDoc} */
	public function checkFriendlyAlias() {
		$alias = parent::checkFriendlyAlias();

		if ($this->modx->context->key != 'mgr') {
			foreach ($this->modx->error->errors as $k => $v) {
				if ($v['id'] == 'alias' || $v['id'] == 'uri') {
					unset($this->modx->error->errors[$k]);
				}
			}
		}

		return $alias;
	}


	/** {@inheritDoc} */
	public function checkPublishingPermissions() {
		if ($this->modx->context->key == 'mgr') {
			return parent::checkPublishingPermissions();
		}
		return true;
	}


	/** {@inheritDoc} */
	public function clearCache() {
		$this->object->clearCache();
		/** @var TicketsSection $section */
		if ($section = $this->object->getOne('Section')) {
			$section->clearCache();
		}
	}


	/** {@inheritDoc} */
	public function saveTemplateVariables() {
		if ($this->modx->context->key != 'mgr') {
			$values = array();
			$tvs = $this->object->getMany('TemplateVars');

			/** @var modTemplateVarResource $tv */
			foreach ($tvs as $tv) {
				$values['tv' . $tv->id] = $this->getProperty($tv->name, $tv->get('value'));
			}

			if (!empty($values)) {
				$this->setProperties($values);
				$this->setProperty('tvs', 1);
			}
		}

		return parent::saveTemplateVariables();
	}

}