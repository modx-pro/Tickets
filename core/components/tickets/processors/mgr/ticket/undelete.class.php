<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/undelete.class.php';

class TicketUnDeleteProcessor extends modResourceUnDeleteProcessor {
	public $permission = 'ticket_delete';

}

return 'TicketUnDeleteProcessor';