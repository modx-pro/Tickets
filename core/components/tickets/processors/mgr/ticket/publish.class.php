<?php

require_once MODX_CORE_PATH . 'model/modx/processors/resource/publish.class.php';

class TicketPublishProcessor extends modResourcePublishProcessor {
	public $permission = 'ticket_publish';

}

return 'TicketPublishProcessor';