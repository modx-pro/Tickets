<?php

class TicketThreadGetProcessor extends modObjectGetProcessor
{
    public $objectType = 'TicketThread';
    public $classKey = 'TicketThread';
    public $languageTopics = array('tickets:default');
    public $object;

    public function initialize()
    {
        $res = (int)$this->getProperty('id');
        if (!$this->object = $this->modx->getObject($this->classKey, array('name' => 'resource-'.$res))) {
            $this->object = $this->modx->newObject($this->classKey);
            $this->object->fromArray(array(
                'name' => 'resource-'.$res,
                'createdby' => $this->modx->user->get('id'),
                'createdon' => date('Y-m-d H:i:s'),
                'resource' => $res,
            ));
            $this->object->save();
        } elseif ($this->object->get('deleted')) {
            $this->modx->error->message = $this->modx->lexicon('ticket_thread_err_deleted').$res;
            return false;
        }

        return true;
    }

    /**
     * @return array|string
     */
    public function cleanup()
    {
        $thread = $this->object->toArray();

        return $this->success('', $thread);
    }
}

return 'TicketThreadGetProcessor';