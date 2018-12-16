<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/delete.class.php';

class TicketDeleteProcessor extends modResourceDeleteProcessor
{
    public $classKey = 'Ticket';
    /** @var modResource $resource */
    public $resource;
    public $permission = 'ticket_delete';

    public function checkPermissions() {
        $id = $this->getProperty('id',false);
        $this->resource = $this->modx->getObject('modResource', $id);
        if (empty($this->resource)) return $this->modx->lexicon('resource_err_nfs',array('id' => $id));
        /* resource owner is this user? */
        if ($this->resource->get('createdby') != $this->modx->user->id) {
            return false;
        }
        return true;
    }
}

return 'TicketDeleteProcessor';
