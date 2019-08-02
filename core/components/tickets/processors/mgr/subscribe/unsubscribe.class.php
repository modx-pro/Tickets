<?php

class TicketUnsubscribeRemoveProcessor extends modProcessor
{
    public $object;
    public $classKey = 'TicketsSection';
    public $languageTopics = array('tickets');
    public $permission = 'section_unsubscribe';

    /**
     * @return bool
     */
    public function checkPermissions()
    {
        if (!$this->modx->hasPermission($this->permission)) {
            return false;
        }

        return true;
    }


    /**
     * @return bool
     */
    public function process()
    {
        $parents = $this->getProperty('parents');
        if ($section = $this->modx->getObject('TicketsSection', $parents, false)) {
            $properties = $section->get('properties');
        }

        $arrUnsubscribe = $this->getProperty('ids');
        if (!empty($properties['subscribers'])) {
            $properties['subscribers'] =
            array_filter($properties['subscribers'], function($k) use($arrUnsubscribe,$parents){
                if ($unsub = in_array($k, $arrUnsubscribe))
                    $this->logManagerAction($k,$parents);
                return !$unsub;
            });
        }

        $section->set('properties', $properties);
        $section->save();

        return true;
    }

    public function logManagerAction($k,$parents) {
        $this->modx->logManagerAction('unsubscribe',$this->classKey,"{$parents} user {$k}");
    }

}

return 'TicketUnsubscribeRemoveProcessor';