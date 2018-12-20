<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/undelete.class.php';

class TicketUnDeleteProcessor extends modResourceUnDeleteProcessor
{
	public $classKey = 'Ticket';
    /** @var modResource $resource */
    public $resource;
    public $permission = 'ticket_delete';

    public function checkPermissions() {
        $id = $this->getProperty('id',false);
        $this->resource = $this->modx->getObject('modResource', $id);
        if (empty($this->resource)) return false;
        /* resource was deleted by this user? */
        if ($this->resource->get('deletedby') != $this->modx->user->id) {
            return false;
        }
        return true;
    }
}

return 'TicketUnDeleteProcessor';