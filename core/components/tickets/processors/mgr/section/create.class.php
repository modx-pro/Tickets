<?php

require_once MODX_CORE_PATH . 'model/modx/modprocessor.class.php';
require_once MODX_CORE_PATH . 'model/modx/processors/resource/create.class.php';

class TicketsSectionCreateProcessor extends modResourceCreateProcessor
{
    /** @var TicketsSection $object */
    public $object;
    public $classKey = 'TicketsSection';


    /**
     * @return array|string
     */
    public function beforeSet()
    {
        $this->setProperties(array(
            'isfolder' => 1,
        ));

        return parent::beforeSet();
    }


    /**
     * @return string
     */
    public function prepareAlias()
    {
        if ($this->workingContext->getOption('tickets.section_id_as_alias')) {
            $alias = 'empty';
            $this->setProperty('alias', $alias);
        } else {
            $alias = parent::prepareAlias();
        }

        return $alias;
    }


    /**
     * @return mixed
     */
    public function afterSave()
    {
        if ($this->object->alias == 'empty') {
            $this->object->set('alias', $this->object->id);
            $this->object->save();
        }

        // Updating resourceMap before OnDocSaveForm event
        $results = $this->modx->cacheManager->generateContext($this->object->context_key);
        if (isset($results['resourceMap'])) {
            $this->modx->context->resourceMap = $results['resourceMap'];
        }
        if (isset($results['aliasMap'])) {
            $this->modx->context->aliasMap = $results['aliasMap'];
        }
        $this->handleProperties();

        return parent::afterSave();
    }


    /**
     * Handle boolean properties
     */
    public function handleProperties()
    {
        $properties = $this->getProperty('properties');
        if (!empty($properties['tickets'])) {
            foreach ($properties['tickets'] as &$property) {
                if ($property == 'true') {
                    $property = true;
                } elseif ($property == 'false') {
                    $property = false;
                }
            }
        }
        $this->setProperty('properties', $properties);
    }
}