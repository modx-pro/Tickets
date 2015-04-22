<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/delete.class.php';

class TicketDeleteProcessor extends modResourceDeleteProcessor {
	public $permission = 'ticket_delete';

}

return 'TicketDeleteProcessor';